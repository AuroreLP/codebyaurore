<?php
namespace App\Controller;

use App\Document\Message;
use App\Form\MessageType;
use App\Service\MongoDBMessageService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ContactController extends AbstractController
{
    private MongoDBMessageService $mongoDBMessageService;
    private Environment $twig;

    public function __construct(MongoDBMessageService $mongoDBMessageService, Environment $twig)
    {
        $this->mongoDBMessageService = $mongoDBMessageService;
        $this->twig = $twig;
    }

    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function contact(Request $request, MailerInterface $mailer): Response
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
            try {
                $emailBody = $this->twig->render('emails/contact.html.twig', [
                    'lastname' => $message->getLastname(),
                    'firstname' => $message->getFirstname(),
                    'mail' => $message->getEmail(),
                    'phone' => $message->getPhone(),
                    'subject' => $message->getSubject(),
                    'messageContent' => $message->getContent(),
                ]);

                $email = (new TemplatedEmail())
                    ->from($message->getEmail())
                    ->to('aleperff@gmail.com')
                    ->subject("Nouveau message de {$message->getLastname()} {$message->getFirstname()}")
                    ->htmlTemplate('emails/contact.html.twig')
                    ->context([
                        'lastname' => $message->getLastname(),
                        'firstname' => $message->getFirstname(),
                        'mail' => $message->getEmail(),
                        'phone' => $message->getPhone(),
                        'subject' => $message->getSubject(),
                        'messageContent' => $message->getContent(),
                    ]);

                // Envoi du message
                $mailer->send($email);
                $this->addFlash('success', 'Votre message a été envoyé avec succès.');
            } catch (\Throwable $e) {
                $this->addFlash('danger', 'Erreur lors de l’envoi du mail : ' . $e->getMessage());
            }

            // Rediriger ou afficher la page de contact
            return $this->redirectToRoute('contact');
        }

        return $this->render('main/contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
