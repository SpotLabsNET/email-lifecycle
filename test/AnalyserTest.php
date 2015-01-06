<?php

use \Emails\Lifecycle\BounceEmailAnalyser;

class AnalyserTest extends \PHPUnit_Framework_TestCase {

  function doTest($resource_name, $expected_emails, $expected_messages) {
    $text_file = __DIR__ . "/resources/$resource_name.txt";
    $this->assertTrue(file_exists($text_file), "Could not find '$text_file");

    $text = file_get_contents($text_file);

    $analyser = new BounceEmailAnalyser($text);
    $emails = $analyser->findEmails();
    $messages = $analyser->findErrorMessages();

    asort($expected_emails);
    asort($emails);
    asort($expected_messages);
    asort($messages);

    $this->assertEquals($expected_emails, $emails);
    $this->assertEquals($expected_messages, $messages);
  }

  function testForwarded() {
    $this->doTest("forwarded",
      array("support@example.com"),
      array("Connection timed out"));
  }

  function testYahoo() {
    $this->doTest("yahoo",
      array("thisemailaddressshouldnotexist12312312312312312312311@yahoo.com"),
      array("delivery error: dd This user doesn't have a yahoo.com account"));
  }

  function testCleanMessage() {
    $this->assertEquals("hi",
      BounceEmailAnalyser::cleanMessage("hi"));

    $this->assertEquals("delivery error: dd This user doesn't have a yahoo.com account",
      BounceEmailAnalyser::cleanMessage("delivery error: dd This user doesn't have a yahoo.com account (thisemailaddressshouldnotexist12312312312312312312311@yahoo.com) [0] - mta1445.mail.gq1.yahoo.com"));
  }

}
