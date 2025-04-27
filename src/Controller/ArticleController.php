<?php

namespace App\Controller;

use App\Form\ArticleType;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Tag;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{

    #[Route('/blog/{slug}', name: 'article.show', requirements: ['slug' => '[a-z0-9-]+'])]
    public function show(string $slug, ArticleRepository $repository): Response
    {
        $article = $repository->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        // Afficher l'article sans gérer le formulaire de commentaire ici
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    // ADMIN PART
    // liste des articles par ordre chronologique DESC
    #[Route('/admin/articles', name: 'admin.articles')]
    public function list(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $articles = $em->getRepository(Article::class)->findAll();

        // Séparer les articles par statut
        $drafts = array_filter($articles, fn($a) => $a->getStatus() === 'draft');
        $ready = array_filter($articles, fn($a) => $a->getStatus() === 'ready');
        $published = array_filter($articles, fn($a) => $a->getStatus() === 'published');

        // Trier les articles publiés par date de publication (du plus récent au plus ancien)
        usort($published, fn($a, $b) => $b->getPublishedAt() <=> $a->getPublishedAt());

        // Fusionner les deux tableaux : drafts en haut, puis les published triés
        $sortedArticles = array_merge($drafts, $ready, $published);

        return $this->render('admin/article/list.html.twig', [
            'articles' => $sortedArticles,
        ]);
    }


    #[Route('/admin/new', name: 'article.new', methods: ['GET', 'POST'])]

    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $article = new Article(); // Création d'un nouvel article

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // Gestion automatique du slug s'il est vide
            if (!$article->getSlug()) {
                $article->setSlug(strtolower($slugger->slug($article->getTitle())));
            }

            if (!$article->getPublishedAt()) {
                $article->setPublishedAt(new \DateTimeImmutable());
            }

            $article->setStatus(Article::STATUS_DRAFT);

            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Article créé avec succès !');

            /*return $this->redirectToRoute('article.show', ['slug' => $article->getSlug()]);*/
            return $this->redirectToRoute('admin.articles');
        }

        return $this->render('admin/article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/edit/{id}', name: 'article.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Article modifié avec succès !');
            return $this->redirectToRoute('admin.articles');
        }

        return $this->render('admin/article/edit.html.twig', [
            'article' => $article,
            'form' => $form
        ]);
    }    

    #[Route('/delete/{id}', name: 'article.delete', methods: ['DELETE'])]
    public function remove(Article $article, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Supprimer les commentaires associés à l'article
        $comments = $em->getRepository(Comment::class)->findBy(['article' => $article]);
        foreach ($comments as $comment) {
            $em->remove($comment);
        }

        $em->remove($article);
        $em->flush();
        $this->addFlash('success', 'L\'article a bien été supprimé');
        return $this->redirectToRoute('admin.articles');
    }
}
