<?php

namespace App\Controller\Admin;

use App\Document\Message;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    #[Route('/admin/messages', name: 'admin.messages.index')]
    public function index()
    {
        // Récupérer tous les messages
        $messages = $this->documentManager->getRepository(Message::class)->findAll();

        return $this->render('admin/messages/index.html.twig', [
            'messages' => $messages,
        ]);
    }

    #[Route('/admin/message/{id}', name: 'admin.message.show')]
    public function show(string $id): Response
    {
        $message = $this->documentManager->getRepository(Message::class)->find($id);

        if (!$message) {
            throw $this->createNotFoundException('Message introuvable.');
        }

        return $this->render('admin/messages/show.html.twig', [
            'message' => $message,
        ]);
    }
}
