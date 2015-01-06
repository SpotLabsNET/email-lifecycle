<?php

/**
 * Register the email lifecycle handlers.
 */

use Emails\Email;
use Openclerk\Events;

use Emails\Lifecycle\EmailLifecycle;

$instance = new EmailLifecycle();

Events::on('email_sent', array($instance, 'onEmailSent'));

Email::registerGlobalArgument('message_id', array($instance, 'generateMessageId'));
