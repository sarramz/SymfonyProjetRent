<?php

namespace App\Controller;

use App\Entity\Immobilier;
use App\Form\ImmobilierType;
use App\Repository\ImmobilierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/immobilier')]
final class ImmobilierController extends AbstractController
{
    #[Route(name: 'app_immobilier_index', methods: ['GET'])]
    public function index(ImmobilierRepository $immobilierRepository): Response
    {
        return $this->render('immobilier/index.html.twig', [
            'immobiliers' => $immobilierRepository->findAll(),
        ]);
    }
    #[Route('/search', name: 'app_immobilier_search', methods: ['GET', 'POST'])]
    public function search(Request $request, ImmobilierRepository $immobilierRepository): Response
    {
        // Récupération des critères de recherche
        $region = $request->query->get('region', '');
        $minPrix = (float) $request->query->get('min_prix', 0);
        $maxPrix = (float) $request->query->get('max_prix', PHP_INT_MAX);

        // Recherche avancée avec des critères
        $immobiliers = $immobilierRepository->findBySearchCriteria($region, $minPrix, $maxPrix);

        return $this->render('immobilier/search.html.twig', [
            'immobiliers' => $immobiliers,
            'searchParams' => [
                'region' => $region,
                'min_prix' => $minPrix,
                'max_prix' => $maxPrix,
            ],
        ]);
    }

    #[Route('/add', name: 'app_immobilierGet_new', methods: ['GET'])]
    public function newI(): Response
    {
        return $this->render('immobilier/new.html.twig');
    }


    #[Route('/new', name: 'app_immobilier_new', methods: [ 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager,Security $security): Response
    {
        $user = $security->getUser();
        $prix = (float) $request->request->get('prix');
        $description = (string) $request->request->get('description');
        $adresse= (string) $request->request->get('adresse');
        $ville= (string)$request->request->get('ville');
        $region= (string)$request->request->get('region');
        $nbr_chambres=(int) $request->request->get('nbr_chambres');
        $statut= (int)$request->request->get('statut');
        //$image = $request->request->get('image', '');
        $image = $request->files->get('image');
        $immobilier = new Immobilier();

        $immobilier->setPrix($prix);
        $immobilier->setDescription($description);
        $immobilier->setAdresse($adresse);
        $immobilier->setVille($ville);
        $immobilier->setRegion($region);
        $immobilier->setNbrChambres($nbr_chambres);
        $immobilier->setStatut($statut);
        if ($image) {
            // Génère un nom unique pour le fichier image
            $imageName = uniqid() . '.' . $image->guessExtension();

            // Déplace l'image téléchargée dans le répertoire public/images
            $image->move($this->getParameter('images_directory'), $imageName);

            // Met à jour l'objet avec le nom de l'image
            $immobilier->setImage($imageName);
        } else {
            $immobilier->setImage('');
        }
        $immobilier->setCreatedAt(new \DateTime());
        $immobilier->setUser($user);

      //  $form = $this->createForm(ImmobilierType::class, $immobilier);
       // $form->handleRequest($request);

        //if ($form->isSubmitted() && $form->isValid()) {

        $entityManager->persist($immobilier);
            $entityManager->flush();
            return $this->redirectToRoute('app_immobilier_index', [], Response::HTTP_SEE_OTHER);
      //  }

      //  return $this->render('immobilier/new.html.twig', ['immobilier' => $immobilier,'form' => $form,]);
    }

    #[Route('/{id}', name: 'app_immobilier_show', methods: ['GET'])]
    public function show(Immobilier $immobilier): Response
    {
        return $this->render('immobilier/show.html.twig', [
            'immobilier' => $immobilier,
        ]);
    }

   // #[Route('/{id}/edit', name: 'app_immobilier_edit', methods: ['GET', 'POST'])]
// public function edit(Request $request, Immobilier $immobilier, EntityManagerInterface $entityManager): Response
// {
//     $form = $this->createForm(ImmobilierType::class, $immobilier);
//     $form->handleRequest($request);

//     if ($form->isSubmitted() && $form->isValid()) {
//         $entityManager->flush();

//         return $this->redirectToRoute('app_immobilier_index', [], Response::HTTP_SEE_OTHER);
//     }

//     return $this->render('immobilier/edit.html.twig', [
//         'immobilier' => $immobilier,
//         'form' => $form,
//     ]);
// }
    #[Route('/{id}/edit', name: 'app_immobilier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Immobilier $immobilier, EntityManagerInterface $entityManager): Response
    {

        if ($request->isMethod('POST')) {
            // Récupération des données du formulaire
            $prix = (float) $request->request->get('prix');
            $description = (string) $request->request->get('description');
            $adresse = (string) $request->request->get('adresse');
            $ville = (string) $request->request->get('ville');
            $region = (string) $request->request->get('region');
            $nbr_chambres = (int) $request->request->get('nbr_chambres');
            $statut = (int) $request->request->get('statut');
            $image = $request->files->get('image');


            $immobilier->setPrix($prix);
            $immobilier->setDescription($description);
            $immobilier->setAdresse($adresse);
            $immobilier->setVille($ville);
            $immobilier->setRegion($region);
            $immobilier->setNbrChambres($nbr_chambres);
            $immobilier->setStatut($statut);


            if ($image) {
                $imageName = uniqid() . '.' . $image->guessExtension();

                // Déplace l'image téléchargée dans le répertoire public/images
                $image->move($this->getParameter('images_directory'), $imageName);

                // Met à jour l'entité avec le nouveau nom de fichier
                $immobilier->setImage($imageName);
            }


            $entityManager->flush();


            return $this->redirectToRoute('app_immobilier_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->render('immobilier/edit.html.twig', [
            'immobilier' => $immobilier,
        ]);
    }

    #[Route('/{id}', name: 'app_immobilier_delete', methods: ['POST'])]
    public function delete(Request $request, Immobilier $immobilier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $immobilier->getId(), $request->request->get('_token'))) {
            foreach ($immobilier->getAvis() as $avis) {
                $entityManager->remove($avis);
            }

            foreach ($immobilier->getReservation() as $reservation) {
                $entityManager->remove($reservation);
            }

            $entityManager->remove($immobilier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_immobilier_index', [], Response::HTTP_SEE_OTHER);
    }



}
