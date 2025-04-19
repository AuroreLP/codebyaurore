<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CommentController extends AbstractController
{
    #[Route('/admin/comments', name: 'admin.comments.index')]
    public function index(EntityManagerInterface $em): Response
    {
        $comments = $em->getRepository(Comment::class)->findBy([], ['publishedAt' => 'DESC']);
    
        return $this->render('admin/comments/index.html.twig', [
            'comments' => $comments
        ]);
    }

    #[Route('/admin/comment/{id}/validate', name: 'admin.comment.validate')]
    public function validate(Comment $comment, EntityManagerInterface $em): RedirectResponse
    {
        if ($comment->getStatus() !== 'validé') {
            $comment->setStatus('validé');
            $comment->setToken(null);
            $em->flush();
            $this->addFlash('success', 'Commentaire validé.');
        }

        return $this->redirectToRoute('admin.comments.index');
    }

    #[Route('/admin/comment/{id}/refuse', name: 'admin.comment.refuse')]
    public function refuse(Comment $comment, EntityManagerInterface $em): RedirectResponse
    {
        if ($comment->getStatus() !== 'refusé') {
            $comment->setStatus('refusé');
            $comment->setToken(null); // tu peux garder ça si tu utilises un token de modération
            $em->flush();
            $this->addFlash('success', 'Commentaire refusé.');
        }

        return $this->redirectToRoute('admin.comments.index');
    }

    #[Route('/admin/comment/{id}/delete', name: 'admin.comment.delete', methods: ['POST'])]
    public function delete(Comment $comment, EntityManagerInterface $em): RedirectResponse
    {
        $em->remove($comment);
        $em->flush();

        $this->addFlash('success', 'Commentaire supprimé.');

        return $this->redirectToRoute('admin.comments');
    }


}