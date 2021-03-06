<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\UserRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class SecurityController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        return new Response($this->twig->render(
            'security/login.html.twig',
            [
                'last_username' => $authenticationUtils->getLastUsername(),
                'error' => $authenticationUtils->getLastAuthenticationError()
            ]
        ));
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/confirm/{token}", name="security_confirm")
     */
    public function confirm(string $token, UserRepository $userRepository, EntityManagerInterface $entityManager) {

        $user = $userRepository->findOneBy(
            [
                'confirmationToken' => $token
            ]
        );

        if (null !== $user) {
            $user->setEnabled(true);
            $user->setConfirmationToken('');

            $entityManager->flush();
        }

        return new Response(
            $this->twig->render(
                'security/confirmation.html.twig',
                [
                    'user' => $user,
                ]
            )
        );
    }
}