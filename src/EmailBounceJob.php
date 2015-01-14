<?php

namespace Emails\Lifecycle;

use \Monolog\Logger;
use \Openclerk\Config;
use \Openclerk\Events;

class EmailBounceJob extends \Jobs\JobInstance {

  function run(\Db\Connection $db, Logger $logger) {

    $server = new \Fetch\Server(Config::get("bounce_server"), Config::get("bounce_port", 993));
    $server->setAuthentication(Config::get("bounce_username"), Config::get("bounce_password"));

    $messages = $server->getOrderedMessages(SORTARRIVAL, 1 /* reverse */, Config::get("bounce_message_limit", 30 /* limit */));
    $logger->info("Found " . number_format(count($messages)) . " messages");

    foreach ($messages as $message) {
      $uid = $message->getUid();
      $q = $db->prepare("SELECT * FROM bounced_emails WHERE uid=? LIMIT 1");
      $q->execute(array($uid));
      if (!$q->fetch()) {
        $logger->info("Found new bounce message '" . $message->getSubject() . "' (" . $uid . ")");

        // a new bounced message
        $text = $message->getMessageBody(false);
        $analyser = new BounceEmailAnalyser($text);
        $logger->info($text);

        $emails = $analyser->findEmails();
        $messages = $analyser->findErrorMessages();
        foreach ($emails as $email) {
          foreach ($messages as $m) {
            $logger->info(sprintf("'%s' failed with message '%s'", $email, $m));

            $q = $db->prepare("INSERT INTO bounced_emails SET
              uid=:uid,
              email=:email,
              message=:message");
            $q->execute(array(
              "uid" => substr($uid, 0, 255),
              "email" => substr($email, 0, 255),
              "message" => substr($m, 0, 255),
            ));
            $logger->info("Inserted bounced_email id=" . $db->lastInsertId());

            // trigger event
            Events::trigger('email_bounced', array(
              "uid" => $uid,
              "email" => $email,
              "message" => $m,
              "text" => $text,
              "subject" => $message->getSubject(),
              "date" => $message->getDate(),
            ));
          }
        }
      }
    }

  }

}
