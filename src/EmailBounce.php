<?php

namespace Emails\Lifecycle;

class EmailBounce extends \Jobs\JobType {

  /**
   * Get a list of all job instances that should be run soon.
   * @return a list of job parameters
   */
  function getPending(\Db\Connection $db) {
    // always runs
    return array(
      array(
        "job_type" => $this->getName(),
        "arg" => null,
      ),
    );
  }

  /**
   * Prepare a {@link JobInstance} that can be executed from
   * the given parameters.
   */
  function createInstance($params) {
    return new EmailBounceJob($params);
  }

  /**
   * Do any post-job-queue behaviour e.g. marking the job queue
   * as checked.
   */
  function finishedQueue(\Db\Connection $db, $jobs) {
    // empty
  }

  function getName() {
    return "email_lifecycle_bounces";
  }

}
