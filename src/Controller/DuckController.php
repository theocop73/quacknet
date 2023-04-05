<?php

namespace App\Controller;

use App\Entity\Duck;
use App\Entity\Avatar;
use App\Form\DuckType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/duck')]
class DuckController extends AbstractController
{
    #[Route('/profile', name: 'app_duck_profile', methods: ['GET', 'POST'])]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur actuel
        $avatar = $user->getAvatar() ?: new Avatar();


       $form = $this->createForm(DuckType::class, $user);

        $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $avatarFile = $form->get('avatar')->getData();

        if ($avatarFile) {
            // Générer un nom de fichier unique pour l'avatar
            $newFilename = uniqid().'.'.$avatarFile->guessExtension();

            try {
                // Déplacer le fichier téléchargé dans le répertoire de stockage de l'avatar
                $avatarFile->move(
                    $this->getParameter('avatar_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // Gérer les erreurs de téléchargement de fichier
                // ...
            }

            // Mettre à jour les informations d'avatar pour l'utilisateur
            $avatar
                ->setFilename($newFilename)
                ->setMimeType($avatarFile->getClientMimeType())
                ->setPath($this->getParameter('avatar_directory'))
            ;
            $user->setAvatar($avatar);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_duck_profile');
    }

    return $this->render('duck/profile.html.twig', [
        'user' => $user,
        'form' => $form->createView(),
        'avatarUrl' => $avatar ? $this->getParameter('avatar_url').'/'.$avatar->getFilename() : null,
    ]);
    }

    #[Route('/profile/{id}', name: 'app_duck_profile_user', methods: ['GET'])]
    public function userProfile(Request $request, EntityManagerInterface $entityManager, Duck $duck): Response
    {

        $avatar = $duck->getAvatar();
        return $this->render('duck/profileUser.html.twig', [
            'duck' => $duck,
       
            
        ]);
        
    }

    

}
