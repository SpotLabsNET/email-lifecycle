<?php

namespace Emails\Lifecycle;

class BouncedEmailsMigration extends \Db\Migration {

  /**
   * Apply only the current migration.
   * @return true on success or false on failure
   */
  function apply(\Db\Connection $db) {
    $q = $db->prepare("CREATE TABLE bounced_emails (
      id int not null auto_increment primary key,
      created_at timestamp not null default current_timestamp,

      email varchar(255) not null,
      message varchar(255) not null,
      uid varchar(255) not null,

      INDEX(email),
      INDEX(uid)
    );");
    return $q->execute();
  }

}
