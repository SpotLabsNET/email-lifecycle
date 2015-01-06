<?php

namespace Emails\Lifecycle;

use \Monolog\Logger;
use \Openclerk\Config;

class EmailBounceJob extends \Jobs\JobInstance {

  function run(\Db\Connection $db, Logger $logger) {

    $server = new \Fetch\Server(Config::get("bounce_server"), Config::get("bounce_port", 993));
    $server->setAuthentication(Config::get("bounce_username"), Config::get("bounce_password"));

    $messages = $server->getMessages(30 /* limit */);
    $logger->info("Found " . number_format(count($messages)) . " messages");

    foreach ($messages as $message) {
      $logger->info(print_r($message, true));
    }

  }

}
