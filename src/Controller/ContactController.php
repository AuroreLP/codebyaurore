<?php

// src/Controller/ContactController.php

namespace App\Controller;

use App\Document\Message;
use App\Form\MessageType;
use App\Service\EmailService;
use App\Service\MongoDBMessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    private MongoDBMessageService $mongoDBMessageService;
    private EmailService $emailService;

    public function __construct(MongoDBMessageService $mongoDBMessageService, EmailService $emailService)
    {
        $this->mongoDBMessageService = $mongoDBMessageService;
        $this->emailService = $emailService;
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

            // Envoi via EmailService
            $this->emailService->sendContactEmail(
                $message->getEmail(),
                $message->getLastname(),
                $message->getFirstname(),
                $message->getPhone(),
                $message->getSubject(),
                $message->getContent()
            );

            // Afficher un message de confirmation (alt: utiliser des flash messages)
            $this->addFlash('success', 'Votre message a été envoyé avec succès.');

            // Rediriger ou afficher la page de contact
            return $this->redirectToRoute('contact');
        }

        return $this->render('main/contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
