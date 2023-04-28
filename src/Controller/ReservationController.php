<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Entity\Reservation;
use App\Entity\User;

use App\Repository\OffreRepository;
use App\Repository\ReservationRepository;
use Swift_Mailer;
use Swift_Message;
use App\Repository\UserRepository;
use App\service\MailerService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Security;


class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(): Response
    {
        return $this->render('reservation/index.html.twig', [
            'controller_name' => 'ReservationController',
        ]);
    }

    #[Route('/reserveroffre/{id}', name: 'app_reserverOffre')]
    public function ReserverOffre(Security $security,$id, ManagerRegistry $doctrine , Request $request , OffreRepository $rep, UserRepository $rep2){
        $user = new User();
        $offre = new Offre();
        $offre = $rep->find($id);
        $user = $security->getUser();

        $reservation = new Reservation();
            $reservation->setIdUser($user);
            $reservation->setIdOffre($offre);
        $reservation->setStatus('En Cours');
        $reservation->setDate(new \DateTime('now') ) ;
            $em = $doctrine->getManager();
            $em->persist($reservation);
            $em->flush();
            return $this->redirectToRoute('app_listoffre');

    }






    #[Route('/reserveroffrefront/{id}', name: 'app_reserverOffrefront')]
    public function ReserverOffre1(Security $security,$id, ManagerRegistry $doctrine , Request $request , OffreRepository $rep, UserRepository $rep2,Swift_Mailer $mailer){
        $user = new User();
        $offre = new Offre();
        $offre = $rep->find($id);
        $coach = $offre->getIdUser();
        $coachemail = $coach->getEmail();
        $user = $security->getUser();
        $offre->setNbplace($offre->getNbplace()-1);

        $reservation = new Reservation();
        $reservation->setIdUser($user);
        $reservation->setIdOffre($offre);
        $reservation->setDate(new \DateTime('now') ) ;
        $reservation->setStatus('En Cours');
        $em = $doctrine->getManager();
        $em->persist($reservation);
        $em->flush();

        $apiKey = $_ENV['VONAGE_API_KEY'];

        $apiSecret = $_ENV['VONAGE_API_SECRET'];

        // Send SMS using Vonage API
        $client = new Client();
        $response = $client->request('POST', 'https://rest.nexmo.com/sms/json', [
            'form_params' => [
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
                'to' => '+21694240978',
                'from' => 'API Vonage',
                'text' => 'Reservation made by '.$user->getEmail().' for '.$offre->getDescription().' on '.$reservation->getDate()->format('Y-m-d H:i:s')
            ]
        ]);

        $content = '<p>Reservation made:</p>';
        $content .= '<ul>';
        $content .= '<li>User: '.$user->getEmail().'</li>';
        $content .= '<li>Offre: '.$offre->getDescription().'</li>';
        $content .= '<li>Date: '.$reservation->getDate()->format('Y-m-d H:i:s').'</li>';
        $content .= '</ul>';

        $message=(new  Swift_Message('Reservation accepté'))
            ->setFrom('wadhah.naggui@esprit.tn')
            ->setTo($coachemail)
            ->setBody($content,"text/html");

        $mailer->send($message);
//
//        $email = (new Email())
//            ->from('wadhah.naggui@esprit.tn')
//            ->to($user->getEmail())
//            ->subject('Test email')
//            ->text('This is a test email sent from Symfony Mailer');
//
//            $mailer->send($email);



        return $this->redirectToRoute('app_listreservationfront');

    }


    //BACKOFFICE
    #[Route('admin/reservation/listreservation', name: 'app_listreservation')]
    public function affRes( ReservationRepository $repository  ): Response
    {   $user=$repository->getuser();
        $offre = $repository->getOffre();
        $reservation =$repository->findAll();
        return $this->render('reservation/listres.html.twig', [
            'reservation' => $reservation ,
            'offre' => $offre,'user'=>$user
        ] );
    }



    #[Route('/reservation/listreservationfront', name: 'app_listreservationfront')]
    public function affResById( Security $security,ReservationRepository $repository , UserRepository $rep2  ): Response
    {
        $user = new User();
        $user=$security->getUser();



        $offre =$repository->getOffreById($user->getId());
        $reservation =$repository->findBy(['id_user'=>$user]);
        return $this->render('reservation/listresfront.html.twig', [
            'reservation' => $reservation ,
            'offre' => $offre
        ] );
    }

    #[Route('/reservation/listreservationcoach', name: 'app_listreservationcoach')]
    public function affResByIdCoach( Security $security,ReservationRepository $repository , UserRepository $rep2  ): Response
    {
        $user = new User();
        $user=$security->getUser();



        $offre =$repository->getOffreById($user->getId());
        $reservation =$repository->findBy(['id_user'=>$user]);
        return $this->render('reservation/listdemandes.html.twig', [
            'reservation' => $reservation ,
            'offre' => $offre
        ] );
    }


    #[Route('/removeFront/{id}', name: 'app_removeFront')]

    public function deleteRes(ManagerRegistry $doctrine,$id,ReservationRepository  $repository,OffreRepository $rep)
    {

        $res= $repository->find($id);
        $offre = $res->getIdOffre();
        $offre->setNbplace($offre->getNbplace()+1);

        $em= $doctrine->getManager();
        $em->remove($res);

        // Update the corresponding Offre entity
        $em->persist($offre);

        $em->flush();
        return $this->redirectToRoute("app_listreservationfront");

    }
    #[Route('/removeback/{id}', name: 'app_removeback')]

    public function deleteres1(ManagerRegistry $doctrine,$id,ReservationRepository  $repository)
    {
        $offre= $repository->find($id);
        $em= $doctrine->getManager();
        $em->remove($offre);
        $em->flush();
        return $this->redirectToRoute("app_listreservation");

    }


    //Accepter Reservation

    #[Route('/reservation/accept/{id}', name: 'app_accepterdemande')]
    public function AccepterReservation($id, ManagerRegistry $doctrine , Request $request , OffreRepository $rep, UserRepository $rep2, ReservationRepository $rep3,Swift_Mailer $mailer){

        $reservation = $rep3->find($id);
        $reservation->setStatus('Accepted');
        $em = $doctrine->getManager();
        $em->persist($reservation);
        $em->flush();

        $user = $reservation->getIdUser();
        $userEmail= $user->getEmail();

        $offre = $reservation->getIdOffre();
        $offreDescription = $offre->getDescription();

        // Compose the email message
        $content = '<p>Your reservation for the following offer has been accepted:</p>';
        $content .= '<ul>';
        $content .= '<li>Offer: '.$offreDescription.'</li>';
        $content .= '</ul>';

        $message = (new Swift_Message('Reservation accepted'))
            ->setFrom('wadhah.naggui@esprit.tn')
            ->setTo($userEmail)
            ->setBody($content, 'text/html');

        $mailer->send($message);

        $apiKey = $_ENV['VONAGE_API_KEY'];
//
        $apiSecret = $_ENV['VONAGE_API_SECRET'];
//
//        // Send SMS using Vonage API
        $client = new Client();
        $response = $client->request('POST', 'https://rest.nexmo.com/sms/json', [
            'form_params' => [
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
                'to' => '+21694240978',
                'from' => 'API Vonage',
                'text' => $content
            ]
        ]);
        return $this->redirectToRoute('app_listreservationcoach');

    }

    //Refuser Reservation

    #[Route('/reservation/decline/{id}', name: 'app_declinedemande')]

    public function RefuserReservation($id, ManagerRegistry $doctrine , Request $request , OffreRepository $rep, UserRepository $rep2, ReservationRepository $rep3,Swift_Mailer $mailer){

        $reservation = $rep3->find($id);
        if ($reservation->getStatus() == 'Accepted') {
            $message = 'Reservation cancellation not allowed.';
            $this->addFlash('warning', $message);
            return $this->redirectToRoute('app_listreservationfront');
        }else
        $reservation->setStatus('Refuser');
        $em = $doctrine->getManager();
        $em->persist($reservation);
        $em->flush();
        $user = $reservation->getIdUser();
        $userEmail= $user->getEmail();

        $offre = $reservation->getIdOffre();
        $offreDescription = $offre->getDescription();

        // Compose the email message
        $content = '<p>Your reservation for the following offer has been Refused:</p>';
        $content .= '<ul>';
        $content .= '<li>Offer: '.$offreDescription.'</li>';
        $content .= '</ul>';

        $message = (new Swift_Message('Reservation Refused'))
            ->setFrom('wadhah.naggui@esprit.tn')
            ->setTo($userEmail)
            ->setBody($content, 'text/html');

        $mailer->send($message);
       $apiKey = $_ENV['VONAGE_API_KEY'];
//
    $apiSecret = $_ENV['VONAGE_API_SECRET'];
//
//        // Send SMS using Vonage API
        $client = new Client();
        $response = $client->request('POST', 'https://rest.nexmo.com/sms/json', [
            'form_params' => [
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
                'to' => '+21694240978',
                'from' => 'API Vonage',
                'text' => $content
            ]
        ]);
        $message = 'Reservation cancelled.';
        $this->addFlash('success', $message);
        return $this->redirectToRoute('app_listreservationcoach');

    }
}
