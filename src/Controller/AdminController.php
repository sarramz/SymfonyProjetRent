<?php

namespace App\Controller;

use App\Entity\Immobilier;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Vérifie si l'utilisateur a le rôle d'admin

        // Statistiques pour afficher sur le tableau de bord (exemples)
        $userCount = $entityManager->getRepository(User::class)->count([]);
        $immobilierCount = $entityManager->getRepository(Immobilier::class)->count([]);
        $reservationCount = $entityManager->getRepository(Reservation::class)->count([]);

        return $this->render('admin/dashboard.html.twig', [
            'userCount' => $userCount,
            'immobilierCount' => $immobilierCount,
            'reservationCount' => $reservationCount,
        ]);
    }

    #[Route('/users', name: 'admin_users')]
    public function listUsers(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }
    #[Route('/immobilier', name: 'admin_immobilier')]
    public function listImmobilier(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $immobilierList = $entityManager->getRepository(Immobilier::class)->findAll();

        return $this->render('admin/immobilier.html.twig', [
            'immobilierList' => $immobilierList,
        ]);
    }
    #[Route('/reservations', name: 'admin_reservations')]
    public function listReservations(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $reservations = $entityManager->getRepository(Reservation::class)->findAll();

        return $this->render('admin/reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }
}
