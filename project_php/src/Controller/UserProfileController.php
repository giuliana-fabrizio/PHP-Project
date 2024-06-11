<?php

namespace App\Controller;

use App\Form\UserProfileType;
use App\Form\ChangePasswordType;
use App\Security\Voter\UserProfileVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

class UserProfileController extends AbstractController
{
    private $UserProfileVoter;

    public function __construct(UserProfileVoter $UserProfileVoter)
    {
        $this->UserProfileVoter = $UserProfileVoter;
    }

    #[Route('/profile', name: 'app_profile')]
    public function viewProfile(Request $request): Response
    {
        $user = $this->getUser();

        // $this->denyAccessUnlessGranted('view_profile', $user);
        if (!$this->isGranted(UserProfileVoter::VIEW, $user)) {
            $this->addFlash('danger', "Vous n'avez pas la permission de voir ce profil.");
            throw $this->createAccessDeniedException("Vous n'avez pas la permission de voir ce profil.");
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function editProfile(Request $request, PersistenceManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        // $this->denyAccessUnlessGranted('edit_profile', $user);
        if (!$this->isGranted(UserProfileVoter::EDIT, $user)) {
            $this->addFlash('danger', "Vous n'avez pas la permission de modifier ce profil.");
            throw $this->createAccessDeniedException("Vous n'avez pas la permission de modifier ce profil.");
        }

        $profileForm = $this->createForm(UserProfileType::class, $user);
        $profileForm->handleRequest($request);
        
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $doctrine->getManager()->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('user/edit.html.twig', [
            'profileForm' => $profileForm->createView(),
        ]);
    }

    #[Route('/profile/edit/password', name: 'app_profile_edit_password')]
    public function editPassword(Request $request, PersistenceManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        // $this->denyAccessUnlessGranted('edit_profile', $user);

        $passwordForm = $this->createForm(ChangePasswordType::class);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $plainPassword = $passwordForm->get('plainPassword')->getData();
            $encodedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($encodedPassword);
            $doctrine->getManager()->flush();
            $this->addFlash('success', 'Mot de passe mis à jour avec succès');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('user/edit_password.html.twig', [
            'passwordForm' => $passwordForm->createView(),
        ]);
    }
}
