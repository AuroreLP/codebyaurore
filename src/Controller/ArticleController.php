<?php

namespace App\Controller;

use App\Form\ArticleType;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Tag;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
    #[Route('/blog', name: 'article.index')]
    public function index(ArticleRepository $repository, EntityManagerInterface $em): Response
    {
        // $articles = $repository->findAll(); test:
        $articles = $repository->findBy([], ['published_at' => 'DESC']);

        // Récupérer toutes les catégories et tous les tags
        $categories = $em->getRepository(Category::class)->findAll();
        $tags = $em->getRepository(Tag::class)->findAll();

        return $this->render('home/index.html.twig', [
            'articles' => $articles,
            'categories' => $categories,
            'tags' => $tags
        ]);
    }

    #[Route('/blog/{slug}', name: 'article.show', requirements: ['slug' => '[a-z0-9-]+'])]
    public function show(Request $request, string $slug, ArticleRepository $repository): Response
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
    public function list(EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(Article::class)->findBy([], ['created_at' => 'DESC']);

        return $this->render('admin/article/list.html.twig', [
            'articles' => $articles,
        ]);
    }


    #[Route('/admin/new', name: 'article.new', methods: ['GET', 'POST'])]

    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
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

            $article->setStatus('DRAFT');

            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Article créé avec succès !');

            return $this->redirectToRoute('article.show', ['slug' => $article->getSlug()]);
        }

        return $this->render('admin/article/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/admin/edit/{id}', name: 'article.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $em): Response
    {
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
    public function remove(Article $article, EntityManagerInterface $em)
    {
        $em->remove($article);
        $em->flush();
        $this->addFlash('success', 'L\'article a bien été supprimé');
        return $this->redirectToRoute('admin.articles');
    }
}
