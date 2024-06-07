<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('accueil.html.twig');
    }

    #[Route('/event', name: 'app_createevent')]
    public function createEvent(): Response
    {
        return $this->render('create.html.twig');
    }
}