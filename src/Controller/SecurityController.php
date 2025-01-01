<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home'); // Redirect authenticated users
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        return $this->render('security/login.html.twig', [
            'registrationForm' => $form,
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {

        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');

    }

}
