<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        // Création du formulaire
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        // Traitement du formulaire après soumission
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Création de l'email
            $email = (new Email())
                ->from($data['email']) 
                ->to('aleperff@gmail.com')
                ->subject('Nouveau message de ' . $data['firstname'] . ' ' . $data['lastname'])
                ->html("
                    <p><strong>Email :</strong> {$data['email']}</p>
                    <p><strong>Téléphone :</strong> " . ($data['phone'] ?? 'Non renseigné') . "</p>
                    <p><strong>Message :</strong><br>" . nl2br($data['message']) . "</p>
                ");

            // Envoi de l'email
            $mailer->send($email);

            // Flash message pour informer l'utilisateur
            $this->addFlash('success', 'Votre message a bien été envoyé !');

            return $this->redirectToRoute('contact');
        }

        // Passage du formulaire à la vue
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
