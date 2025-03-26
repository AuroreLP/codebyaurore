<?php

namespace App\Controller;

use App\Form\ArticleType;
use App\Entity\Article;
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
    public function show(Request $request, string $slug, ArticleRepository $repository): Response
    {
        $article = $repository->findOneBy(['slug' => $slug]);
        return $this->render('article/show.html.twig', [
            'article' => $article
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

/*

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $article->setStatus('DRAFT');
            
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('articles_list');
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form
        ]);
    }

}


#[Route('/articles', name: 'articles_')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(): Response
    {
        return $this->render('article/list.html.twig');
    }




}

*/