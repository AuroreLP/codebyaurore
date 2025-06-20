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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupérer les derniers articles publiés
        $articles = $em->getRepository(Article::class)->findBy([], ['publishedAt' => 'DESC'], 5);

        // Compter le nombre total d'articles
        $articlesCount = $em->getRepository(Article::class)->count([]);

        // Récupérer les derniers messages reçus
        $messages = $dm->getRepository(Message::class)->findBy([], ['createdAt' => 'DESC'], 5);

        // Compter le nombre total de messages avec MongoDB
        $messagesCount = $dm->getRepository(Message::class)->createQueryBuilder()
            ->hydrate(false)  // éviter d'hydrater en objets
            ->count()         // compter les documents
            ->getQuery()
            ->execute();  // Utilise execute() pour obtenir le résultat compté


        // Récupérer les commentaires en attente
        $comments = $em->getRepository(Comment::class)->findBy(['status' => Comment::STATUS_PENDING], ['publishedAt' => 'DESC'], 5);

        // Compter le nombre total de commentaires
        $commentsCount = $em->getRepository(Comment::class)->count(['status' => Comment::STATUS_VALIDATED]);
        
        return $this->render('admin/dashboard/index.html.twig', [
            'articles' => $articles,
            'messages' => $messages,
            'comments' => $comments,
            'articlesCount' => $articlesCount,
            'messagesCount' => $messagesCount,
            'commentsCount' => $commentsCount,
        ]);
    }
}
