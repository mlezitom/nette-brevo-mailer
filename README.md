Brevo mailer for Nette framework (formerly Sendinblue)
=================

Implemented by [Tomáš Mleziva | mlezivat@gmail.com](mailto:mlezivat@gmail.com)

Installation
------------

The best way to install Brevo mailer is using  [Composer](http://getcomposer.org/):

```sh
$ composer require mlezitom/nette-brevo-mailer
```

Configuration
-------------

Put the following configuration in your config.neon file:

```yaml
parameters:
    brevo: 
        apikey: xkeysib-xxxxxxxxxxxxxxxxxxxx

services:
    mail.mailer: Mlezitom\NetteBrevoMailer\BrevoMailer(%brevo.apikey%)  
```

Usage
-----

Just inject the Nette framework's mailer service wherever you want to use it - same as with any other mailer service:

```php
use Mlezitom\NetteBrevoMailer\BrevoMailer;
use Nette\Mail\Message;
use IMailer;

class ExamplePresenter extends Nette\Application\UI\Presenter
{
    /**
	 * @var Mailer
	 * @inject
	 */
    public Mailer $mailer;

    public function sendMessage(Message $message): void
    {    
        $this->mailer->send($message);
    }
}
```