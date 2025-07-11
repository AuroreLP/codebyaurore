<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CommentController extends AbstractController
{    
    #[Route('/blog/{slug}/comment/new', name: 'comment.new', methods: ['POST'])]
    public function new(Request $request, string $slug, 
    ArticleRepository $articleRepository, EntityManagerInterface $em, EmailService $emailService): Response
    {
    
        // récupérer l'article à partir du slug
        $article = $articleRepository->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }


        // création d'un nouveau commentaire
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);
            $comment->setPublishedAt(new \DateTimeImmutable());
            $comment->setStatus('en attente');

            // Générer un token unique pour ce commentaire
            $comment->setToken(bin2hex(random_bytes(32)));

            // Persist et flush
            $em->persist($comment);
            $em->flush();


            $this->addFlash('success', 'Commentaire envoyé pour validation. Un email de validation vous sera envoyé.');
            return $this->redirectToRoute('blog.show', ['slug' => $slug]);
        }

        // En cas d’erreur, on redirige vers la page article avec le formulaire rempli
        $comments = $article->getComments()->toArray();
        usort($comments, fn($a, $b) => $b->getPublishedAt() <=> $a->getPublishedAt());

        $deleteForms = [];
        foreach ($article->getComments() as $existingComment) {
            $deleteForms[$existingComment->getId()] = $this->createFormBuilder()
                ->setAction($this->generateUrl('comment.delete', ['id' => $existingComment->getId()]))
                ->setMethod('DELETE')
                ->getForm()
                ->createView();
        }

        // Rendu du formulaire
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'form' => $form->createView(), 
            'comments' => $article->getComments(),
            'deleteForms' => $deleteForms
        ]);
    }

    #[Route('/comment/validate/{token}', name: 'comment.validate', methods: ['GET'])]
    public function validateComment(string $token, EntityManagerInterface $em): Response
    {
        // Rechercher le commentaire par son token
        $comment = $em->getRepository(Comment::class)->findOneBy(['token' => $token]);

        if (!$comment) {
            throw $this->createNotFoundException('Commentaire non trouvé ou déjà validé.');
        }

        // Valider le commentaire
        $comment->setStatus('validé');  // Exemple de validation
        $comment->setToken(null); // Supprimer le token après validation pour éviter qu'il soit réutilisé

        // Persister et flusher les changements
        $em->persist($comment);
        $em->flush();

        // Ajouter un message flash
        $this->addFlash('success', 'Votre commentaire a été validé avec succès.');

        // Rediriger vers l'article concerné
        return $this->redirectToRoute('article.show', ['slug' => $comment->getArticle()->getSlug()]);
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
            'comment' => $comment,
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
