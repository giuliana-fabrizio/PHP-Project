<?php

namespace App\Controller;

use App\Controller\EventController;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UserProfileType;
use App\Form\UserType;
use App\Security\Voter\UserProfileVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

class UserController extends AbstractController
{
    public function __construct(
        private EventController $eventController,
        private UserProfileVoter $UserProfileVoter,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[Route('/user_profile', name: 'app_user_profile')]
    public function getUserProfile(Request $request): Response
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

    #[Route('/user_events', name: 'app_user_events')]
    public function getUserEvents(Request $request): Response
    {
        $user = $this->getUser();
        $events = $user->getEvents();

        return $this->eventController->renderEvents($events->toArray(), $request);
    }

    #[Route('/create_user', name: 'app_create_user')]
    public function createuser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(["ROLE_USER"]);
            $user->setPassword($this->passwordHasher->hashPassword($user, $form->getData()->getPassword()));
            $entityManager->persist($user);

            try {
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', "Email déjà utilisé");
                return $this->render('user/create.html.twig', [
                    'form' => $form->createView()
                ]);
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit_user', name: 'app_edit_user')]
    public function editUser(Request $request, PersistenceManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
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
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user/edit.html.twig', [
            'profileForm' => $profileForm->createView(),
        ]);
    }

    #[Route('/edit_user_password', name: 'app_edit_user_password')]
    public function editUserPassword(Request $request, PersistenceManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
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
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user/edit_password.html.twig', [
            'passwordForm' => $passwordForm->createView(),
        ]);
    }
}
