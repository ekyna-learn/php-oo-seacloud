<?php

declare(strict_types=1);

namespace Service;

use Entity\Server;

use function mail;
use function sprintf;

class Notifier
{
    private string $notifyEmail;

    public function __construct(string $notifyEmail)
    {
        $this->notifyEmail = $notifyEmail;
    }

    public function notifyReady(Server $server): void
    {

        $subject = sprintf('Server %s is ready', $server->getName());

        $message = <<<HTML
    <!doctype html>
     <html lang="en">
      <head>
       <title>$subject</title>
      </head>
      <body>
       <p>You server named '{$server->getName()} is ready.</p>
       <p>Get all details about our servers in your dashboard !</p>
      </body>
     </html>
    HTML;

        $headers = [
            'MIME-Version' => '1.0',
            'Content-type' => 'text/html; charset=iso-8859-1'
        ];

        // Voir documentation PHP pour configurer l'envoi de mails.
        // Ou mieux : utiliser une librairie (SwiftMailer, PHPMailer, etc)
        mail(
            $this->notifyEmail,
            $subject,
            $message,
            $headers
        );
    }
}
