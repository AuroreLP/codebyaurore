<?php

namespace App\Service;

use App\Entity\Comment;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class EmailService
{
    private MailerInterface $mailer;
    private Environment $twig;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        MailerInterface $mailer,
        Environment $twig,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
    }

    public function sendValidationEmail(Comment $comment): void
    {
        $validationUrl = $this->urlGenerator->generate(
            'comment.validate',
            ['token' => $comment->getToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $emailBody = $this->twig->render('emails/comment_validation.html.twig', [
            'comment' => $comment,
            'validationUrl' => $validationUrl,
        ]);

        $email = (new Email())
            ->from('noreply@tonsite.fr')
            ->to($comment->getEmail())
            ->subject('Validez votre commentaire')
            ->html($emailBody);

        $this->mailer->send($email);
    }

    public function sendContactEmail(string $fromEmail, string $name, string $message): void
    {
        $emailBody = $this->twig->render('emails/contact.html.twig', [
            'name' => $name,
            'email' => $fromEmail,
            'message' => $message,
        ]);

        $email = (new Email())
            ->from($fromEmail)
            ->to('contact@tonsite.fr') // remplace par ton adresse pro
            ->subject("Nouveau message de $name")
            ->html($emailBody);

        $this->mailer->send($email);
    }
}
