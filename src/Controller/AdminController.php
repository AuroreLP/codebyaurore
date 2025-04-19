<?php

namespace App\Controller;

use App\Document\Message;
use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin.dashboard')]
    public function index(EntityManagerInterface $em, DocumentManager $dm): Response
    {
        // Récupérer les derniers articles publiés
        $articles = $em->getRepository(Article::class)->findBy([], ['publishedAt' => 'DESC'], 5);

        // Récupérer les derniers messages reçus
        $messages = $dm->getRepository(Message::class)->findBy([], ['createdAt' => 'DESC'], 5);

        // Récupérer les commentaires en attente
        $comments = $em->getRepository(Comment::class)->findBy(['status' => 'en attente'], ['publishedAt' => 'DESC'], 5);
        
        return $this->render('admin/dashboard/index.html.twig', [
            'articles' => $articles,
            'messages' => $messages,
            'comments' => $comments,
        ]);
    }
}
