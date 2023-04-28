<?php

namespace App\Controller;

use App\Repository\ActualiteRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeeController extends AbstractController
{
    #[Route('/homee', name: 'app_homee')]
    public function index(EventRepository  $repository,ActualiteRepository $Act): Response
    {
        $events= $repository->findAll();
        $actualites = $Act->findAll();

        return $this->render('homee/index.html.twig',
            array("event"=>$events,"act"=>$actualites));
    }
}
