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
      template_id=:template_id");
    $q->execute(array(
      "to_name" => $email['to_name'],
      "to_email" => $email['to_email'],
      "subject" => $email['subject'],
      "template_id" => $email['template_id'],
    ));
  }

}
