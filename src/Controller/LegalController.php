<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class LegalController extends AbstractController
{

     #[Route("/mentions-legales", name:"mentions_legales")]
    public function mentionsLegales(): Response
    {
        return $this->render('legal/mentions_legales.html.twig');
    }

     #[Route("/politique-de-confidentialite", name:"politique_confidentialite")]
    public function politiqueConfidentialite(): Response
    {
        return $this->render('legal/politique_confidentialite.html.twig');
    }
}
