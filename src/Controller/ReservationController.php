<?php

namespace App\Controller;

use App\Entity\Immobilier;
use App\Entity\Reservation;
use App\Entity\Facture;
use App\Repository\ReservationRepository;
use App\Repository\ImmobilierRepository;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reservation')]
final class ReservationController extends AbstractController
{

    #[Route(name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }


    #[Route('/add', name: 'app_reservation_get_new', methods: ['GET'])]
    public function newForm(ImmobilierRepository $immobilierRepository): Response
    {
        $immobiliers = $immobilierRepository->findAll();

        return $this->render('reservation/new.html.twig', [
            'immobiliers' => $immobiliers,
        ]);
    }


    #[Route('/new', name: 'app_reservation_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $dateDebut = new \DateTime($request->request->get('date_debut'));
        $dateFin = new \DateTime($request->request->get('date_fin'));
        $statut = 'En attente';  // Statut par défaut
        $dateRes = new \DateTime();  // Date de réservation par défaut (aujourd'hui)
        $immobilierId = $request->request->get('immobilier_id');


        $reservation = new Reservation();
        $reservation->setDateDebut($dateDebut);
        $reservation->setDateFin($dateFin);
        $reservation->setStatut($statut);
        $reservation->setDateRes($dateRes);


        $immobilier = $entityManager->getRepository(Immobilier::class)->find($immobilierId);
        if (!$immobilier) {
            $this->addFlash('error', 'Immobilier introuvable.');
            return $this->redirectToRoute('app_reservation_index');
        }
        $reservation->setImmobilier($immobilier);


        $facture = new Facture();
        $facture->setContenu("Réservation pour l'immobilier : " . $immobilier->getDescription());
        $montantHT = 1000;  // Montant HT fictif à adapter selon votre logique
        $TVA = $montantHT * 0.2;  // Calcul de la TVA
        $facture->setMontantHT($montantHT);
        $facture->setTVA($TVA);
        $facture->setDate(new \DateTime());


        $entityManager->persist($facture);


        $reservation->setFacture($facture);


        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Utilisateur non connecté.');
            return $this->redirectToRoute('app_login');
        }
        $reservation->setUser($user);


        $entityManager->persist($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'Réservation créée avec succès.');
        return $this->redirectToRoute('app_reservation_index');
    }


    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Reservation $reservation,
        EntityManagerInterface $entityManager,
        ImmobilierRepository $immobilierRepository,
        FactureRepository $factureRepository
    ): Response {
        $immobiliers = $immobilierRepository->findAll();
        $factures = $factureRepository->findAll();

        if ($request->isMethod('POST')) {
            $dateDebut = new \DateTime($request->request->get('date_debut'));
            $dateFin = new \DateTime($request->request->get('date_fin'));
            $statut = $request->request->get('statut');
            $dateRes = new \DateTime($request->request->get('date_res'));
            $factureId = $request->request->get('facture_id');
            $immobilierId = $request->request->get('immobilier_id');

            $reservation->setDateDebut($dateDebut);
            $reservation->setDateFin($dateFin);
            $reservation->setStatut($statut);
            $reservation->setDateRes($dateRes);


            if ($factureId) {
                $facture = $factureRepository->find($factureId);
                if ($facture) {
                    $reservation->setFacture($facture);
                }
            }


            if ($immobilierId) {
                $immobilier = $immobilierRepository->find($immobilierId);
                if ($immobilier) {
                    $reservation->setImmobilier($immobilier);
                }
            }


            $entityManager->flush();

            $this->addFlash('success', 'Réservation mise à jour avec succès.');
            return $this->redirectToRoute('app_reservation_index');
        }


        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'immobiliers' => $immobiliers,
            'factures' => $factures,
        ]);
    }


    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        // Vérifier la validité du token CSRF pour la suppression
        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'Réservation supprimée avec succès.');
        }

        return $this->redirectToRoute('app_reservation_index');
    }
}
