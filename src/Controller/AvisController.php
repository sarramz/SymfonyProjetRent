<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Immobilier;
use App\Repository\AvisRepository;
use App\Repository\ImmobilierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/avis')]
final class AvisController extends AbstractController
{
    #[Route('/', name: 'app_avis_index', methods: ['GET'])]
    public function index(Request $request,AvisRepository $avisRepository, ImmobilierRepository $immobilierRepository): Response
    {
        $immobilierId = $request->query->get('immobilierId');
        $avis = $immobilierId
            ? $avisRepository->findBy(['immobilier' => $immobilierId])
            : $avisRepository->findAll();

        $immobilier = $immobilierId ? $immobilierRepository->find($immobilierId) : null;

        return $this->render('avis/index.html.twig', [
            'avis' => $avis,
            'immobilier' => $immobilier,
        ]);
    }

    #[Route('/add', name: 'app_avis_get_new', methods: ['GET'])]
    public function newForm(Request $request, ImmobilierRepository $immobilierRepository): Response
    {
        $immobilierId = $request->query->get('immobilierId');
        $immobilier = $immobilierId ? $immobilierRepository->find($immobilierId) : null;

        return $this->render('avis/new.html.twig', [
            'immobiliers' => $immobilierRepository->findAll(),
            'immobilier' => $immobilier,
        ]);
    }

    #[Route('/new', name: 'app_avis_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ImmobilierRepository $immobilierRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour ajouter un avis.');
            return $this->redirectToRoute('app_login');
        }

        $contenu = $request->request->get('contenu');
        $immobilierId = $request->request->get('immobilier_id');

        if (!$contenu) {
            $this->addFlash('error', 'Le contenu de l’avis est obligatoire.');
            return $this->redirectToRoute('app_avis_get_new');
        }

        $immobilier = $immobilierRepository->find($immobilierId);
        if (!$immobilier) {
            $this->addFlash('error', 'Immobilier introuvable.');
            return $this->redirectToRoute('app_avis_get_new');
        }

        $avis = (new Avis())
            ->setContenu($contenu)
            ->setDate(new \DateTime())
            ->setUser($user)
            ->setImmobilier($immobilier);

        $entityManager->persist($avis);
        $entityManager->flush();

        $this->addFlash('success', 'Avis ajouté avec succès.');
        return $this->redirectToRoute('app_immobilier_show', ['id' => $immobilier->getId()]);
    }

    #[Route('/{id}/edit', name: 'app_avis_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Avis $avis,
        EntityManagerInterface $entityManager,
        ImmobilierRepository $immobilierRepository
    ): Response {
        $immobiliers = $immobilierRepository->findAll();

        if ($request->isMethod('POST')) {
            $contenu = $request->request->get('contenu');
            $immobilierId = $request->request->get('immobilier_id');

            if (!empty($contenu)) {
                $avis->setContenu($contenu);
            }

            if (!empty($immobilierId)) {
                $immobilier = $immobilierRepository->find($immobilierId);
                if (!$immobilier) {
                    $this->addFlash('error', 'Immobilier introuvable.');
                    return $this->redirectToRoute('app_avis_edit', ['id' => $avis->getId()]);
                }
                $avis->setImmobilier($immobilier);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Avis mis à jour avec succès.');
            return $this->redirectToRoute('app_immobilier_show', ['id' => $avis->getImmobilier()->getId()]);
        }

        return $this->render('avis/edit.html.twig', [
            'avis' => $avis,
            'immobiliers' => $immobiliers,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_avis_delete', methods: ['POST'])]
    public function delete(Request $request, Avis $avis, EntityManagerInterface $entityManager): Response
    {
        $immobilier = $avis->getImmobilier(); // Récupérer le bien immobilier associé.

        if ($this->isCsrfTokenValid('delete' . $avis->getId(), $request->request->get('_token'))) {
            $entityManager->remove($avis);
            $entityManager->flush();
            $this->addFlash('success', 'Avis supprimé avec succès.');

            // Redirection vers la page du bien immobilier associé ou la liste générale.
            return $this->redirectToRoute('app_immobilier_show', ['id' => $immobilier->getId()]);
        }

        $this->addFlash('error', 'Token CSRF invalide.');
        return $this->redirectToRoute('app_avis_index');
    }

}
