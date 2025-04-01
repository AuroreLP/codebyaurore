<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Article;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommentController extends AbstractController
{
    #[Route('/blog/{slug}/comment/new', name: 'comment.new', methods: ['POST'])]
    public function new(Request $request, string $slug,
    ArticleRepository $articleRepository, EntityManagerInterface $em): Response
    {
        // récupérer l'article à partir du slug
        $article = $articleRepository->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        // création d'un nouveau formulaire
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // lier le commentaire à l'article
            $comment->setArticle($article);
            $comment->setPublishedAt(new \DateTimeImmutable());

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Commentaire ajouté avec succès !');

            // Rediriger vers la page de l'article
            return $this->redirectToRoute('article.show', ['slug' => $slug]);
        }

        // Rendu du formulaire
        return $this->render('comment/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/comment/{id}/edit', name: 'comment.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $em): Response
    {
        // Vérifier si le commentaire est bien lié à un article
        if (!$comment->getArticle()) {
            throw $this->createNotFoundException('Le commentaire n\'est pas associé à un article.');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Commentaire modifié avec succès !');
            return $this->redirectToRoute('article.show', ['slug' => $comment->getArticle()->getSlug()]);
        }

        return $this->render('comment/edit.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }

    #[Route('/comment/{id}/delete', name: 'comment.delete', methods: ['POST'])]
    public function delete(Comment $comment, EntityManagerInterface $em): Response
    {
        // Vérifier si l'article existe avant de supprimer le commentaire
        if (!$comment->getArticle()) {
            throw $this->createNotFoundException('Le commentaire n\'est pas associé à un article.');
        }
        
        $articleSlug = $comment->getArticle()->getSlug();
        $em->remove($comment);
        $em->flush();

        $this->addFlash('success', 'Commentaire supprimé avec succès !');
        return $this->redirectToRoute('article.show', ['slug' => $articleSlug]);
    }
}
