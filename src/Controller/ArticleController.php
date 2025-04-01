<?php

namespace App\Controller;

use App\Form\ArticleType;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
    #[Route('/blog', name: 'article.index')]
    public function index(Request $request, ArticleRepository $repository): Response
    {
        $articles = $repository->findAll();

        return $this->render('article/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/blog/{slug}', name: 'article.show', requirements: ['slug' => '[a-z0-9-]+'])]
    public function show(Request $request, string $slug, ArticleRepository $repository, EntityManagerInterface $em): Response
    {
        $article = $repository->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

            // Création d'un nouveau commentaire
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);
            $comment->setPublishedAt(new \DateTimeImmutable());

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Votre commentaire a été ajouté !');
            return $this->redirectToRoute('article.show', ['slug' => $slug]);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'form' => $form->createView() // Passer le formulaire à Twig
        ]);
    }


    #[Route('/new', name: 'article.new', methods: ['GET', 'POST'])]

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

            $article->setStatus('DRAFT');

            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Article créé avec succès !');

            return $this->redirectToRoute('article.show', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/edit/{id}', name: 'article.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Article modifié avec succès !');
            return $this->redirectToRoute('article.index');
        }

        return $this->render('article/edit.html.twig', [
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
        return $this->redirectToRoute('article.index');
    }
}
