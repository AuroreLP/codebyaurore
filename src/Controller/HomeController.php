<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Tag;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
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

}


