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
    #[Route('/blog/{slug}', name: 'article.show', requirements: ['slug' => '[a-z0-9-]+'])]
    public function showArticle(Request $request, string $slug, ArticleRepository $articleRepository, EntityManagerInterface $em): Response
    {
        $article = $articleRepository->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        // Créer un nouveau commentaire
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);
            $comment->setPublishedAt(new \DateTimeImmutable());

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Votre commentaire a été ajouté !');

            // Rediriger vers la page de l'article pour afficher le nouveau commentaire
            return $this->redirectToRoute('article.show', ['slug' => $slug]);
        }

        // Créer un tableau de formulaires de suppression pour chaque commentaire
        $deleteForms = [];
        foreach ($article->getComments() as $existingComment) {
            $deleteForms[$existingComment->getId()] = $this->createDeleteForm($existingComment)->createView();
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
            'delete_forms' => $deleteForms,  // Passer les formulaires de suppression à la vue
    ]);
    }

    private function createDeleteForm(Comment $comment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comment.delete', ['id' => $comment->getId()]))
            ->setMethod('DELETE') // Utilisation de DELETE au lieu de POST
            ->getForm();
    }
    
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
            // lier le commentaire à l'article
            $comment->setArticle($article);
            $comment->setPublishedAt(new \DateTimeImmutable());
            $comment->setStatus('en attente');

            // Générer un token unique pour ce commentaire
            $comment->setToken(bin2hex(random_bytes(32)));

            // Persist et flush
            $em->persist($comment);
            $em->flush();

            // Envoyer un email avec le token pour valider le commentaire
            $emailService->sendValidationEmail($comment);

            $this->addFlash('success', 'Commentaire ajouté avec succès! Un email de validation vous a été envoyé.');
            return $this->redirectToRoute('article.show', ['slug' => $slug]);
        }

        // Rendu du formulaire
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
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
