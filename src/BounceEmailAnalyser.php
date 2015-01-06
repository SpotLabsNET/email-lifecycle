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

  }

  static function cleanMessage($message) {
    $message = preg_replace("#\(.+?\).+$#im", "", $message);
    return trim($message);
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
