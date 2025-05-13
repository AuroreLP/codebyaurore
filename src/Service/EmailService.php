<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Twig\Environment;

class EmailService
{
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(
        MailerInterface $mailer,
        Environment $twig,
        
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendContactEmail(string $fromEmail, string $lastname, string $firstname, ?string $phone,
    string $subject, string $message): void
    {
        $emailBody = $this->twig->render('emails/contact.html.twig', [
            'lastname' => $lastname,
            'firstname' => $firstname,
            'mail' => $fromEmail,
            'phone' => $phone,
            'subject' => $subject,
            'messageContent' => $message, 
        ]);

        $email = (new TemplatedEmail())
            ->from($fromEmail)
            ->to('aleperff@gmail.com') // remplace par ton adresse pro
            ->subject("Nouveau message de $lastname . $firstname")
            ->htmlTemplate('emails/contact.html.twig')
            ->context([
                'lastname' => $lastname,
                'firstname' => $firstname,
                'mail' => $fromEmail,
                'phone' => $phone,
                'subject' => $subject,
                'messageContent' => $message,
            ]);
        
        // Envoi du message
        $this->mailer->send($email);
    }
}
