<?php

namespace Emails\Lifecycle;

class SentEmailsMigration extends \Db\Migration {

  /**
   * Apply only the current migration.
   * @return true on success or false on failure
   */
  function apply(\Db\Connection $db) {
    $q = $db->prepare("CREATE TABLE sent_emails (
      id int not null auto_increment primary key,
      created_at timestamp not null default current_timestamp,

      to_name varchar(255) null,
      to_email varchar(255) null,
      subject varchar(255) null,
      template_id varchar(255) null,

      is_failed tinyint not null default 0,

      INDEX(is_failed),
      INDEX(to_name),
      INDEX(to_email)
    );");
    return $q->execute();
  }

}
