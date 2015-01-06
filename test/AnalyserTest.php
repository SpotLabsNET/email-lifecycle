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

  function testTimeout() {
    $this->doTest("timeout",
      array("foo.bar@gmaiil.com"),
      array("Connection broken during SMTP conversation"));
  }

  function testIcloud() {
    $this->doTest("icloud",
      array("123@icloud.com"),
      array("unknown or illegal alias: 123@icloud.com"));
  }

  function testCallout() {
    $this->doTest("callout",
      array("abc@ieh-mail.de"),
      array("Callout verification failed:"));
  }

  function testGmail() {
    $this->doTest("gmail",
      array("an_address@gmail.com"),
      array("The email account that you tried to reach does not exist. Please try"));
  }

  function testQuota() {
    $this->doTest("quota",
      array("a@gmail.com"),
      array("The email account that you tried to reach is over quota. Please direct"));
  }

  function testConnect() {
    $this->doTest("connect",
      array("sample@email.tst"),
      array("Unable to connect to email.tst : 25"));
  }

  function testNotFound() {
    $this->doTest("notfound",
      array("username@gmial.com"),
      array("Recipient not found."));
  }

  function testEee() {
    $this->doTest("eee",
      array("'eee@eee.ee"),
      array("No Such User Here"));
  }

  function testTotalScore() {
    $this->doTest("totalscore",
      array("max@toklan.com"),
      array("Mail (m-40152-02364) appears to be unsolicited - Totalscore(101) over MessageScoringUpperLimit - contact postmaster@toklan.com for resolution"));
  }

  function testStartmail() {
    $this->doTest("startmail",
      array("foo@startmail.com"),
      array("Recipient address rejected: undeliverable address: Unknown user"));
  }

  function testJsonRpcPhp() {
    $this->doTest("jsonrpcphp",
      array("info@jsonrpcphp.org"),
      array("No Such User Here"));
  }

  function testAlias() {
    $this->doTest("alias",
      array("t123456@gamil.com"),
      array("Recipient address rejected: User unknown in virtual alias table"));
  }

  function testHotmai() {
    $this->doTest("hotmai",
      array("user.name@hotmai.ch"),
      array("Temporary local problem - please try later"));
  }

  function testBlocklist() {
    $this->doTest("blocklist",
      array("foo@address.nl"),
      array("Service unavailable; Client host [123.45.67.89] blocked using Blocklist 1; To request removal from this list please forward this message to delist@messaging.microsoft.com"));
  }

  function testWeb() {
    $this->doTest("web",
      array("lala@web.com"),
      array("... User unknown"));
  }

  function testQmail() {
    $this->doTest("qmail",
      array("jevon@jevon.org"),
      array("You must log in to send mail from cryptfolio.com"));
  }

  function testEzmlm() {
    $this->doTest("ezmlm",
      array("tina@jevon.org"),
      array("ezmlm-reject: fatal: Sorry, I don't accept commands in the subject line. Please send a message to the -help address shown in the the ``Mailing-List:'' header for command info"));
  }

  function testParadise() {
    $this->doTest("paradise",
      array("foo@paradise.net.nz"),
      array("bad address foo@paradise.net.nz"));
  }

  function testDevNull() {
    $this->doTest("devnull",
      array("thedigitalvoid@dev.null"),
      array("Sorry, I couldn't find any host named dev.null."));
  }

  function testLetxt() {
    $this->doTest("letxt",
      array("123@sms.letxt.com.au"),
      array("This email address is not activated to accept SMS messages."));
  }

  function testJeyo() {
    $this->doTest("jeyo",
      array("supportnn@jeyo.com"),
      array("sorry, no mailbox here by that name."));
  }

  function testChello() {
    $this->doTest("chello",
      array("user@t-email.hu"),
      array("Recipient address rejected: Recipient address does not exist"));
  }

  function testCampbx() {
    $this->doTest("campbx",
      array("support@campbx.com"),
      array("Your message can't be delivered because delivery to this address is restricted."));
  }

  function testCranenz() {
    $this->doTest("cranenz",
      array("user@cranenz.co.nz"),
      array("Delivery to the following recipients failed."));
  }

  function testMassey() {
    $this->doTest("massey",
      array("jevon@jevon.org", "user@massey.ac.nz"),
      array("conversation with mail.jevon.org[203.194.209.183] timed out while sending message body"));
  }

  function testCleanMessage() {
    $this->assertEquals("hi",
      BounceEmailAnalyser::cleanMessage("hi"));

    $this->assertEquals("delivery error: dd This user doesn't have a yahoo.com account",
      BounceEmailAnalyser::cleanMessage("delivery error: dd This user doesn't have a yahoo.com account (thisemailaddressshouldnotexist12312312312312312312311@yahoo.com) [0] - mta1445.mail.gq1.yahoo.com"));
  }

}
