<?php

use \Emails\Lifecycle\EmailLifecycle;

class EmailLifecycleTest extends \PHPUnit_Framework_TestCase {

  function testGenerateMessageId() {
    $instance = new EmailLifecycle();
    $m1 = $instance->generateMessageId();
    $m2 = $instance->generateMessageId();

    $instance2 = new EmailLifecycle();
    $m3 = $instance->generateMessageId();

    $this->assertNotEquals($m1, $m2);
    $this->assertNotEquals($m2, $m3);
    $this->assertNotEquals($m3, $m1);

    // all strings need to be less than 255 chars
    $this->assertLessThan(255, strlen($m1), "$m1 should be less than 255 characters long");
    $this->assertLessThan(255, strlen($m2), "$m2 should be less than 255 characters long");
    $this->assertLessThan(255, strlen($m3), "$m3 should be less than 255 characters long");
  }

}
