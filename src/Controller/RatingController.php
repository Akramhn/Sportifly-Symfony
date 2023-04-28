<?php
// src/Controller/RatingController.php
namespace App\Controller;

use App\Entity\Stars;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\RatingType;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RatingController extends AbstractController
{

    #[Route('/rating', name: 'rating', methods: ['GET', 'POST'])]
    public function saveRating(UserRepository $userRepository,Request $request,EntityManagerInterface $entityManager): Response
    {
        // $avg = ('3');
        //  echo($uID);
        $uID= $userRepository->find(2);
        if ($request->request->get('save')) {
            $ratedIndex = $request->request->get('ratedIndex');
            if ($ratedIndex !== null && $ratedIndex >= 1 && $ratedIndex <= 5) {
                $rating = new Stars();
                $rating->setUID($uID);
                $rating->setRateIndex($ratedIndex);
                $entityManager->persist($rating);
                $entityManager->flush();
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(['id' => $uID]));
            return $response;
        }

        // Render the template with the average rating and star rating system
        return $this->render('offre/userspace.html.twig', [
            // 'avg' => $avg,
            'uID' => $uID
        ]);
    }



}