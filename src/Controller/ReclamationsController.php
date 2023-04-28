<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Reclamation;
use App\Entity\Reclamations;
use App\Form\ReclamationsType;
use App\Form\ReclamationType;
use App\Repository\ReclamationsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\Id;
use Doctrine\Persistence\ManagerRegistry;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/reclamations')]
class ReclamationsController extends AbstractController
{
    #[Route('/', name: 'app_reclamations_index', methods: ['GET'])]
    public function index(ReclamationsRepository $reclamationsRepository): Response
    {
        return $this->render('reclamations/index.html.twig', [
            'reclamation' => $reclamationsRepository->findAll(),
        ]);
    }
    #[Route('/stats',name:'app_reclamation_stat')]
    public function stats(ReclamationsRepository $repository,NormalizerInterface $Normalizer)
    {
        $reclamations=$repository->countByDate();
        $dates=[];
        $reclamationsCount=[];
        foreach($reclamations as $reclamation){
            $dates[] = $reclamation['dateReclamation'];
            $reclamationsCount[] = $reclamation['count'];
        }
        dump($reclamationsCount);
        return $this->render('reclamations/stats.html.twig',[
            'dates' => json_encode($dates),
            'reclamationsCount' => json_encode($reclamationsCount),

        ]);
    }




    #[Route('/addreclamationfront/{name}', name: 'app_reclamations_new')]
    public function new(Request $request, ManagerRegistry $doctrine, UserRepository $repository, $name)
    {
        $user = new User();
        $reclamation = new Reclamations();
        $form = $this->createForm(ReclamationsType::class, $reclamation);
        $form->handleRequest($request);

        $user = $repository->findOneBy(['email' => $name]);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $reclamation->setUser($user);
            $em->persist($reclamation);
            $em->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->renderForm("reclamations/addreclamationfront.html.twig", array("reclamation" => $form));
    }

    #[Route('/{id}', name: 'app_reclamations_show', methods: ['GET'])]
    public function show(Reclamations $reclamation): Response
    {
        return $this->render('reclamations/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reclamations_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamations $reclamation, ReclamationsRepository $reclamationsRepository): Response
    {
        $form = $this->createForm(ReclamationsType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamationsRepository->save($reclamation, true);

            return $this->redirectToRoute('app_reclamations_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("reclamations/addreclamationfront.html.twig", array("reclamation" => $form));

    }


    #[Route('/{id}', name: 'app_reclamations_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamations $reclamation, ReclamationsRepository $reclamationsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $request->request->get('_token'))) {
            $reclamationsRepository->remove($reclamation, true);
        }

        return $this->redirectToRoute('app_reclamations_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/traiter/{id}', name: 'reclamationtraite')]
    function Traiter(ReclamationsRepository $repository, $id, Request $request, ManagerRegistry $doctrine, UserRepository $repo)
    {
        $user = new User();
        $user->eraseCredentials();
        $reclamation = new Reclamations();
        $user = $repo->find($id);
        $em = $doctrine->getManager();
        $em->flush();
        $repository->sms();
        $reclamation->setEtat("yes");
        $em->flush();
        $this->addFlash('danger', 'reponse envoyée avec succées');
        return $this->redirectToRoute('app_reclamations_index');
    }
    #[Route('/allusers', name: 'listM')]
    public function getReclamation(ReclamationsRepository $reclamationsRepository,NormalizerInterface $serializer){

        $users=$reclamationsRepository->findAll();
        $json=$serializer->normalize($users,'json',['groups'=>'$users']);
        return new Response($json);
    }





}
