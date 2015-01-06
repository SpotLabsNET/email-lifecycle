openclerk/email-lifecycle
=========================

A library to track bounced emails sent with [openclerk/emails](https://github.com/openclerk/emails).

This library means you can notify users on your web application when your mailer
fails to send an email and receives an Automated Delivery Notification; for example,
the address does not exist, the server times out, or the SMTP connection is refused.

Bounce emails are forwarded to an email account and polled regularly with IMAP
using the [openclerk/jobs](https://github.com/openclerk/jobs) framework.

## Installing

Include `openclerk/email-lifecycle` as a requirement in your project `composer.json`,
and run `composer update` to install it into your project:

```json
{
  "require": {
    "openclerk/email-lifecycle": "dev-master"
  }
}
```

## Using

Use [openclerk/db](https://github.com/openclerk/db) to insert in the `bounced_emails` table
(provided automatically by _BouncedEmailsMigration_).

Configure your bounce server IMAP connection details:

```php
Openclerk\Config::merge(array(
  "bounce_server" => "imap.gmail.com",
  "bounce_port" => 993,
  "bounce_username" => "bounces@mydomain.com",
  "bounce_password" => "password",
));
```

You can then run the _EmailBounceJob_ to poll this server for new bounce messages,
which are analysed for failing addresses and error messages.

Bounce messages are stored in the `bounced_emails` database table, and are also
triggered with the `email_bounced` event:

```php
Openclerk\Events::on('email_bounced', function($email) {
  echo "Email to " . $email['email'] . " bounced with message '" . $email['message'] . "'";
});
```

## Tests

Because all mail servers return Automated Delivery Notification messages differently, and
we can't rely on headers being preserved in a consistent way, we use regular expressions
to try and identify the failing addresses and relevant messages from bounce messages text.

A suite of example bounce messages are provided in `test/resources`, and can be run
using phpunit:

```
vendor/bin/phpunit
```

More example bounce messages from other mail servers are welcome.

## TODO

1. Add and process more example bounce messages
