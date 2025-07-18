<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\Exception\RateLimitExceededException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Psr\Log\LoggerInterface;

class SecurityController extends AbstractController
{
    private $loginRateLimiter;
    private $logger;

    public function __construct(RateLimiterFactory $loginRateLimiter, LoggerInterface $logger)
    {
        $this->loginRateLimiter = $loginRateLimiter;
        $this->logger = $logger;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        try {
        $limiter = $this->loginRateLimiter->create($request->getClientIp());
        $limiter->consume()->ensureAccepted();
    } catch (RateLimitExceededException $e) {
        $this->logger->warning('Too many login attempts from IP: ' . $request->getClientIp());
        $this->addFlash('error', 'Trop de tentatives de connexion. Merci de patienter quelques minutes avant de réessayer.');

        return $this->render('security/login.html.twig', [
            'last_username' => '',
            'error' => null,
        ], new Response(null, 429)); // HTTP 429 Too Many Requests
    }
        
        // Si l'utilisateur est déjà authentifié, rediriger vers la page d'accueil ou une autre page
        if ($this->getUser()) {
        return $this->redirectToRoute('admin.dashboard'); // Remplace 'home' par le nom de ta route d'accueil
    }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
