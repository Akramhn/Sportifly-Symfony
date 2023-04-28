<?php

namespace App\Controller;

use App\Entity\Actualite;
use App\Form\ActualiteType;
use App\Entity\CommentaireAct;
use App\Form\CommentaireActType;
use App\Repository\ActualiteRepository;
use App\Repository\CommentaireActRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireActController extends AbstractController
{
    #[Route('/commentaireAct', name: 'app_commentaireAct')]
    public function affichercommentaireAct(CommentaireActRepository $repository)
    {   $comment=$repository->findAll();
        return $this->render('commentaire_act/index.html.twig', array("form"=>$comment));
    }
    #[Route('/ajoutercommentaireAct/{id}', name: 'app_ajoutercommentaireAct')]
    public function ajoutercommentaire(ActualiteRepository $repository,$id,Request $request,ManagerRegistry $doctrine): Response
    {
        $comment = new CommentaireAct();
        $form = $this->createForm(CommentaireActType::class, $comment);
        $actualite=$repository->find($id);
        $comment->setIdActualite($actualite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $em = $doctrine->getManager();
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute("app_commentaireAct");
        }
        return $this->renderForm('commentaire_act/ajouter.html.twig', array("form" => $form));
    }
    #[Route('/updatecommentAct/{id}', name: 'app_updatecommentAct')]
    public function updateCommentAct(CommentaireActRepository $repository,$id,ManagerRegistry $doctrine,Request $request)
    {
        $comment = $repository->find($id);
        $form = $this->createForm(CommentaireActType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute("app_commentaireAct");
        }
        return $this->renderForm('commentaire_act/update.html.twig', array("form"=>$form));
    }
    #[Route('/supprimercommentaireAct/{id}', name: 'app_supprimercommentaireAct')]
    public function supprimerActualite(CommentaireActRepository $repository,$id,ManagerRegistry $doctrine)
    {    $comment=$repository->find($id);
        $form=$this->createForm(CommentaireActType::class,$comment);
        $em =$doctrine->getManager() ;
        $em->remove($comment);
        $em->flush();
        return $this->redirectToRoute("app_commentaireAct");

    }

}
