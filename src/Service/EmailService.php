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
        Environment $twig
        
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendContactEmail(string $fromEmail, string $lastname, string $firstname, ?string $phone,
    string $subject, string $message): void
    {
        if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("L'email n'est pas valide.");
        }

        $emailBody = $this->twig->render('emails/contact.html.twig', [
            'lastname' => $lastname,
            'firstname' => $firstname,
            'mail' => $fromEmail,
            'phone' => $phone,
            'subject' => $subject,
            'messageContent' => $message, 
        ]);

        $email = (new TemplatedEmail())
            ->from('contact@codebyaurore.dev')
            ->replyTo($fromEmail)   
            ->to('aleperff@gmail.com')
            ->subject("Nouveau message de $firstname $lastname")
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
