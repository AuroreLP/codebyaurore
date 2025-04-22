<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Tag;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ArticleRepository $articleRepository, CategoryRepository $categoryRepository, TagRepository $tagRepository): Response
    {
        $articles   = $articleRepository->findFiltered();
        $categories = $categoryRepository->findAll();
        $tags = $tagRepository->findAll();

        return $this->render('main/home/index.html.twig', [
            'articles' => $articles,
            'categories' => $categories,
            'tags' => $tags,
        ]);
        }

        #[Route('/api/articles', name: 'api_articles', methods: ['GET'])]
        public function apiArticles(Request $request, ArticleRepository $articleRepository, CategoryRepository $categoryRepository, TagRepository $tagRepository): JsonResponse
        {
            $categorySlug = $request->query->get('category');
            $tagSlug      = $request->query->get('tag');
    
            $category = $categorySlug ? $categoryRepository->findOneBy(['slug' => $categorySlug]) : null;
            $tag      = $tagSlug      ? $tagRepository->findOneBy(['slug' => $tagSlug])         : null;
    
            $articles = $articleRepository->findFiltered($category, $tag);
    
            $data = array_map(function($article) {
                return [
                    'title'       => $article->getTitle(),
                    'slug'        => $article->getSlug(),
                    'meta'        => $article->getMetaDescription(),
                    'publishedAt' => $article->getPublishedAt()->format('d.m.y'),
                    'category'    => $article->getCategory()->getName(),
                    'tags'        => array_map(fn($t) => $t->getName(), $article->getTags()->toArray()),
                ];
            }, $articles);
    
            return $this->json(['articles' => $data]);
        }
    

}


