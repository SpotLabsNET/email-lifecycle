<?php

namespace Emails\Lifecycle;

use \Monolog\Logger;

class EmailBounceJob extends \Jobs\JobInstance {

  function run(\Db\Connection $db, Logger $logger) {
    // TODO connect with IMAP
    $logger->warn("Does nothing yet");
  }

}
