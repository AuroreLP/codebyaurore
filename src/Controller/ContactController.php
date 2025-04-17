<?php

// src/Controller/ContactController.php

namespace App\Controller;

use App\Document\Message;
use App\Form\MessageType;
use App\Service\MongoDBMessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    private MongoDBMessageService $mongoDBMessageService;
    private MailerInterface $mailer;

    public function __construct(MongoDBMessageService $mongoDBMessageService, MailerInterface $mailer)
    {
        $this->mongoDBMessageService = $mongoDBMessageService;
        $this->mailer = $mailer;
    }

    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function contact(Request $request): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 1. Enregistrement en base MongoDB
            $this->mongoDBMessageService->saveMessage(
                $message->getLastname(),
                $message->getFirstname(),
                $message->getPhone(),
                $message->getEmail(),
                $message->getSubject(),
                $message->getContent()
            );

            // 2. Envoi de l'e-mail via Mailer

            $email = (new Email())
                ->from($message->getEmail())
                ->to('aleperff@gmail.com') // adresse de réceptio
                ->subject('Nouveau message de contact')
                ->text(
                    "Nom : {$message->getLastname()} {$message->getFirstname()}\n" .
                    "Email : {$message->getEmail()}\n" .
                    "Téléphone : {$message->getPhone()}\n" .
                    "Sujet : {$message->getSubject()}\n\n" .
                    "Message : {$message->getContent()}"
                );


            $this->mailer->send($email);

            // Afficher un message de confirmation (alt: utiliser des flash messages)
            $this->addFlash('success', 'Votre message a été envoyé avec succès.');

            // Rediriger ou afficher la page de contact
            return $this->redirectToRoute('contact');
        }

        return $this->render('main/contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/test-email', name: 'test_email')]
    public function testEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('no-reply@mailtrap.club')
            ->to('aleperff@yopmail.com') // remplace par ton adresse
            ->subject('Test Mailtrap')
            ->text('Ceci est un test simple');

        try {
            $mailer->send($email);
            return new Response('Email envoyé avec succès');
        } catch (\Throwable $e) {
            return new Response('Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
        }
    }


}
