<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/articles', name: 'articles_')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(): Response
    {
        return $this->render('article/list.html.twig');
    }

    #[Route('/edit/{id}', name: 'edit')]
    #[Route('/create', name: 'create')]
    public function edit(): Response
    {
        return $this->render('article/edit.html.twig');
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(): RedirectResponse
    {
        $this->addFlash('success', 'L\'article a été supprimé');
        return $this->redirectToRoute('articles_list');
    }
}