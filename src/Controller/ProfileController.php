<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\ReclamationRepository;
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        ValidatorInterface $validator,
        ReclamationRepository $reclamationRepository
    ): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        $errors = [];

        if ($request->isMethod('POST')) {
            // Gestion des données du formulaire
            $firstName = $request->request->get('firstName');
            $lastName = $request->request->get('lastName');
            $profilePicture = $request->files->get('profilePicture');

            // Mise à jour des données utilisateur
            $user->setFirstName($firstName ?: $user->getFirstName());
            $user->setLastName($lastName ?: $user->getLastName());

            // Upload de la photo de profil
            if ($profilePicture) {
                try {
                    $safeFilename = $slugger->slug(pathinfo($profilePicture->getClientOriginalName(), PATHINFO_FILENAME));
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $profilePicture->guessExtension();

                    $profilePicture->move($this->getParameter('images_directory'), $newFilename);
                    $user->setProfilePicture($newFilename);
                } catch (\Exception $e) {
                    $errors['profilePicture'] = 'Erreur lors du téléchargement de la photo.';
                }
            }

            // Validation des données
            $validationErrors = $validator->validate($user);
            if (count($validationErrors) > 0) {
                foreach ($validationErrors as $error) {
                    $errors[$error->getPropertyPath()] = $error->getMessage();
                }
            } else {
                // Enregistrement
                $entityManager->flush();
                $this->addFlash('success', 'Profil mis à jour avec succès.');
                return $this->redirectToRoute('app_profile');
            }
        }
        $reclamations = $reclamationRepository->findBy(['user' => $user]);
        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'reclamations' => $reclamations,
            'errors' => $errors,
        ]);
    }
}
        // #[Route('/notifications', name: 'app_user_notifications', methods: ['GET'])]
// public function getUserNotifications(NotificationRepository $notificationRepository): JsonResponse
// {
//     $user = $this->getUser();
//     if (!$user) {
//         return new JsonResponse([], 401);
//     }

//     $notifications = $notificationRepository->findBy(['User' => $user]);

//     return new JsonResponse(array_map(function ($notification) {
//         return [
//             'id' => $notification->getId(),
//             'contenu' => $notification->getContenu(),
//             'date' => $notification->getDate()->format('Y-m-d H:i:s'),
//         ];
//     }, $notifications));

