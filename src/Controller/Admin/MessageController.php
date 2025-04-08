<?php

namespace App\Controller\Admin;

use App\Document\Message;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * @Route("/admin/messages", name="admin_messages")
     */
    public function index()
    {
        // Récupérer tous les messages
        $messages = $this->documentManager->getRepository(Message::class)->findAll();

        return $this->render('admin/messages/index.html.twig', [
            'messages' => $messages,
        ]);
    }
}
