<?php

namespace Emails\Lifecycle;

class EmailLifecycle {

  /**
   * Captures the {@code email_sent} event.
   */
  function onEmailSent($email) {
    // insert in database keys
    $q = db()->prepare("INSERT INTO sent_emails SET
      to_name=:to_name,
      to_email=:to_email,
      subject=:subject,
      template_id=:template_id,
      message_id=:message_id,
      arguments=:arguments");
    $q->execute(array(
      "to_name" => $email['to_name'],
      "to_email" => $email['to_email'],
      "subject" => $email['subject'],
      "template_id" => $email['template_id'],
      "message_id" => $email['arguments']['message_id'],
      "arguments" => serialize($email['arguments']),
    ));
  }

  static $message_id_counter = 0;

  /**
   * Generate a unique message ID.
   * This message ID needs to be unique across multiple requests, multiple IPs,
   * multiple instances in the same script, and multiple times.
   */
  function generateMessageId($arguments = array()) {
    return uniqid() . "_" . self::$message_id_counter++ . "_" . md5(date('r') . implode(",", $arguments) . (isset($_ENV['REMOTE_ADDR']) ? $_ENV['REMOTE_ADDR'] : ''));
  }

}
