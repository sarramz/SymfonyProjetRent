<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImmobilierController extends AbstractController
{
    #[Route('/immobilier', name: 'app_immobilier')]
    public function index(): Response
    {
        return $this->render('immobilier/index.html.twig', [
            'controller_name' => 'ImmobilierController',
        ]);
    }

    
}
