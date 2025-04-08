<?php

namespace App\Controller;

use App\Document\Message;
use App\Form\MessageType;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }
    
    #[Route('/contact', name: 'contact')]
    public function index(
        Request $request,
        MailerInterface $mailer,
        DocumentManager $dm
    ): Response {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrement dans MongoDB
            $dm->persist($message);
            $dm->flush();

            // Envoi de l'e-mail
            $email = (new TemplatedEmail())
                ->from($message->getEmail())
                ->to('aleperff@gmail.com') // L'adresse à laquelle tu reçois le mail
                ->subject('Nouveau message de contact : ' . $message->getSubject())
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    'message' => $message,
                ]);

            $mailer->send($email);

            $this->addFlash('success', 'Votre message a été envoyé avec succès !');
            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}