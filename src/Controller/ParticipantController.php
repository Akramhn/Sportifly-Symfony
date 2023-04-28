<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Security\Core\Security;

class ParticipantController extends AbstractController
{
    #[Route('/participant', name: 'app_participant')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }

    #[Route('/listparticipants', name: 'app_listParticipants')]
    public function listParticipants(ParticipantRepository $repository)
    {
        $participants= $repository->findAll();
        return $this->render("participant/listeparticipants.html.twig",array("tabParticipants"=>$participants));

    }
    #[Route('/participer/{id}', name: 'app_participer')]
    public function participer(Security  $security,EventRepository $repository,$id,ManagerRegistry $doctrine,UserRepository $userRepo,ParticipantRepository $rep)
    {
        $user = new User();
        $user=$security->getUser();






        $event = $repository->find($id);

        $participant = new Participant();

        $participant->setEvent($event);
        $participant->setIdUser($user);
        $participant->setDateParticipation(new \DateTime());

            $em = $doctrine->getManager();
            $em->persist($participant);
            $em->flush();



         
        // $reclamation->setEtat(1 );
        $em = $doctrine->getManager();
        $rep->sms();
        $em->flush();
       
        $this->addFlash('danger', 'reponse envoyée avec succées');
       



            return  $this->redirectToRoute("app_homee");


        

    }

    
    #[Route('/updatePar/{id}', name: 'app_updatePar')]
    public function updateParticipant(ParticipantRepository $repository,$id,ManagerRegistry $doctrine,Request $request)
    {


        $participant= $repository->find($id);

        $form=$this->createForm(ParticipantType::class,$participant);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $em =$doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute("app_listParticipants");
        }
        return $this->renderForm("participant/add.html.twig",
            array("formParticipant"=>$form));
    }

    #[Route('/removePar/{id}', name: 'app_removePar')]
    public function deleteParticipant(ManagerRegistry $doctrine,$id,ParticipantRepository $repository)
    {
        $participant= $repository->find($id);
        $em= $doctrine->getManager();
        $em->remove($participant);
        $em->flush();
        return $this->redirectToRoute("app_listParticipants");

    }
    #[Route('/pdf', name: 'pdf')]
    public function pdf(ParticipantRepository $ParticipantRepository)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('participant/pdf.html.twig', [
            'participant' => $ParticipantRepository->findAll(),
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }

}
