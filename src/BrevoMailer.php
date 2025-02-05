<?php

namespace Mlezitom\NetteBrevoMailer;

use Nette\Mail\Mailer as NetteMailer;
use Nette\Mail\Message as NetteMessage;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use Brevo\Client\Model\SendSmtpEmailSender;
use Brevo\Client\Model\SendSmtpEmailTo;
use Brevo\Client\Model\SendSmtpEmailCc;
use Brevo\Client\Model\SendSmtpEmailBcc;
use Brevo\Client\Model\SendSmtpEmailReplyTo;
use Brevo\Client\Model\SendSmtpEmailAttachment;
use Nette\Mail\MimePart;

class BrevoMailer implements NetteMailer
{
    private array $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function send(NetteMessage $mail): void
    {
        $params = [
            'subject' => $mail->getSubject(),
            'htmlContent' => $mail->getHtmlBody(),
        ];

        foreach ($mail->getFrom() as $senderEmail => $senderName) {
            $params['sender'] = new SendSmtpEmailSender([
                'name' => $senderName,
                'email' => $senderEmail
            ]);
        }
        foreach ($mail->getHeader('To') ?: [] as $recipientEmail => $recipientName) {
            $params['to'][] = new SendSmtpEmailTo([
                'email' => $recipientEmail, 'name' => trim((string)$recipientName) ?: null,
            ]);
        }
        foreach ($mail->getHeader('Cc') ?: [] as $ccEmail => $ccName) {
            $params['cc'][] = new SendSmtpEmailCc([
                'email' => $ccEmail, 'name' => trim((string)$ccName) ?: null
            ]);
        }
        foreach ($mail->getHeader('Bcc') ?: [] as $bccEmail => $bccName) {
            $params['bcc'][] = new SendSmtpEmailBcc([
                'email' => $bccEmail, 'name' => trim((string)$bccName) ?: null
            ]);
        }
        foreach ($mail->getHeader('Reply-To') ?: [] as $rtEmail => $rtName) {
            $params['replyTo'] = new SendSmtpEmailReplyTo([
                'email' => $rtEmail,
            ]);
        }
        foreach ($mail->getAttachments() as $attachment) {
            /**
             * @var MimePart $attachment
             */
            $filename = null;
            if (preg_match('~filename=\"([^\"]+)~', $attachment->getHeader('Content-Disposition'), $matches)) {
                $filename = $matches[1];
            }
            $params['attachment'][] = new SendSmtpEmailAttachment([
                'content' => base64_encode(file_get_contents($attachment->getBody())),
                'name' => $filename,
            ]);
        }

        if ($mail->getHtmlBody()) {
            $params['html'] = $mail->getHtmlBody();
        } else {
            $params['text'] = $mail->getBody();
        }

        // sendinblue
        $brevoConfig = Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->config['apikey']);
        $apiInstance = new TransactionalEmailsApi(null, $brevoConfig);

        $email = new SendSmtpEmail($params);
        $apiInstance->sendTransacEmail($email);
    }
}
