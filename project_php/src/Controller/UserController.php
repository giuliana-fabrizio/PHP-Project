<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    #[Route('/create_user', name: 'create_user')]
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

            return $this->redirectToRoute('app_index');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
