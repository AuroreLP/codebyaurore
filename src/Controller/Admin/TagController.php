<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/tags', name: 'admin.tags.')]
class TagController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(TagRepository $tagRepository): Response
    {
        return $this->render('admin/tag/index.html.twig', [
            'tags' => $tagRepository->findAll(),
            'title' => 'Liste des tags'
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tag);
            $em->flush();
            $this->addFlash('success', 'Tag créé avec succès.');

            return $this->redirectToRoute('admin.tags.index');
        }

        return $this->render('admin/tag/new.html.twig', [
            'form' => $form->createView(),
            'title' => 'Créer un nouveau tag'
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Tag $tag, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TagType::class, $tag);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Tag mis à jour.');

            return $this->redirectToRoute('admin.tags.index');
        }

        return $this->render('admin/tag/edit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier le tag'
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Tag $tag, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_tag_' . $tag->getId(), $request->request->get('_token'))) {
            $em->remove($tag);
            $em->flush();
            $this->addFlash('success', 'Tag supprimé.');
        }

        return $this->redirectToRoute('admin.tags.index');
    }
}
