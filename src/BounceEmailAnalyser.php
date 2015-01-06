<?php

namespace Emails\Lifecycle;

/**
 * This is a messy class that takes the reply messages and bounce messages from
 * various email servers, tries to find the original message IDs, and tries to
 * find the error message that caused the message to bounce.
 *
 * This class needs to be thoroughly tested with the response of common email servers.
 */
class BounceEmailAnalyser {

  var $text;

  /**
   * @param $html the HTML text of the email
   * @param $text the text of the email
   */
  function __construct($text) {
    $this->text = str_replace("\r", "\n", str_replace("\r\n", "\n", $text)) . "\n\n";
  }

  var $emails = array();
  var $messages = array();
  var $searched = false;

  function doSearch() {
    if ($this->searched) {
      return;
    }

    $this->searched = true;

    // captures citadel
    if (preg_match_all("/(Giving up on the following addresses|The following addresses were undeliverable):?\\s*\n\n\\s*(.+?)\\s*\n\n/ims", $this->text, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        $bits = explode(": ", $match[2], 2);
        if (count($bits) == 2) {
          $this->emails[] = $bits[0];
          $this->messages[] = self::cleanMessage($bits[1]);
        } else {
          $this->messages[] = self::cleanMessage($bits[0]);
        }
      }
    }

    // captures qmail
    if (preg_match_all("/\n<(.+?@.+?)>:\n(.+?)\n\n/ims", $this->text, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        $this->emails[] = $match[1];
        $this->messages[] = self::cleanMessage($match[2]);
      }
    }

    // captures jeyo
    if (preg_match_all("/\n<(.+?@.+?)>,\\s+(.+?)\n\n/ims", $this->text, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        $this->emails[] = $match[1];
        $this->messages[] = self::cleanMessage($match[2]);
      }
    }

    // captures chello
    if (preg_match_all("/\\s*Recipient:\\s+<(.+?@.+?)>\n\\s*Reason:\\s+(.+?)\n\n/ims", $this->text, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        $this->emails[] = $match[1];
        $this->messages[] = self::cleanMessage($match[2]);
      }
    }

    // captures campbx
    if (preg_match_all("/(Delivery has failed to these recipients or groups):?\n\n(.+?@[^<]+).*?\n(.+)\n\n/im", $this->text, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        $this->emails[] = $match[2];
        $this->messages[] = self::cleanMessage($match[3]);
      }
    }

    // captures cranenz
    if (preg_match_all("/This is an automatically generated Delivery Status Notification.\n\n(.+)\n\n\\s*(.+@.+)\n\n/im", $this->text, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        $this->emails[] = $match[2];
        $this->messages[] = self::cleanMessage($match[1]);
      }
    }

    // captures massey
    if (!$this->emails && !$this->messages && preg_match_all("/<(.+?@.+?)>(.+):\\s*((.|\n\\s+)+?)\n\n/im", $this->text, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        $this->emails[] = self::cleanEmail($match[1]);
        $this->emails[] = self::cleanEmail($match[2]);
        $this->messages[] = self::cleanMessage($match[3]);
      }
    }

  }

  static function cleanMessage($message) {
    // join up connected lines for massey
    $message = preg_replace("#\n   +#", " ", $message);

    $message = preg_replace("#.+Remote host said: ([^\n]+).*#ims", "\\1", $message);
    $message = preg_replace("#\\(.+?@.+?\\).+$#im", "", $message);
    $message = preg_replace("#\\(\\#[0-9\\.]+\\)$#im", "", $message);
    $message = preg_replace("#^[0-9\\. \\#]+\\s+#im", "", $message);
    $message = preg_replace("#<[^>]+>:?\\s*#im", "", $message);
    $message = preg_replace("#([^\n]+)\n.+$#ims", "\\1", $message);   // first line only
    return trim($message);
  }

  static function cleanEmail($email) {
    $email = preg_replace("#^.+?<(.+@[^>]+)>.+$#im", "\\1", $email);
    return $email;
  }

  /**
   * Return all message IDs that can be found in the given email.
   * Can return an empty array.
   */
  function findEmails() {
    $this->doSearch();
    return $this->emails;
  }

  /**
   * Return all error messages that can be found in the given email.
   * Can return an empty array.
   */
  function findErrorMessages() {
    $this->doSearch();
    return $this->messages;
  }

}
