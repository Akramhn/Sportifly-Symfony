<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OupsAccountController extends AbstractController
{
    #[Route('/oups/account', name: 'app_oups_account')]
    public function index(): Response
    {
        return $this->render('oups_account/index.html.twig', [
            'controller_name' => 'OupsAccountController',
        ]);
    }
}
