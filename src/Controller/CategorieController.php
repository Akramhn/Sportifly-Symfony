<?php

namespace App\Controller;

use App\Entity\CategorieActivite;
use App\Form\CategorieActiviteType;
use App\Repository\CategorieActiviteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    public function affiche_cate(CategorieActiviteRepository $repository): Response
    {
        $categorie= $repository->findAll();
        return $this->render("categorie/listecateg_back.html.twig",array("tabCategorie"=>$categorie));
    }


    #[Route('/add_categ', name: 'app_addcateg')]
    public function addcateg(Request $request,ManagerRegistry $doctrine): Response
    {



        $categ=new CategorieActivite();
        $form= $this->createForm(CategorieActiviteType::class,$categ);

        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){


            $em =$doctrine->getManager() ;
            $em->persist($categ);
            $em->flush();
            return $this->redirectToRoute("app_categorie");
        }
        return $this->renderForm("categorie/addcateg.html.twig",
            array("form"=>$form));


    }





    #[Route('/updatecategorie/{id}',name:   'app_updatecategorie') ]
    Public function updateCategorie(CategorieActiviteRepository $repository,$id,ManagerRegistry $doctrine,Request $request)
    {
        $categorie=$repository->find($id);
        $form=$this->createForm(CategorieActiviteType::class,$categorie);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {$em=$doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute("app_categorie");
        }
        return $this->renderForm("categorie/updateCategorie.html.twig",
            array("form"=>$form));

    }




    #[Route('/removecategorie/{id}',name:   'app_removecategorie') ]
    Public function removeCategorie(CategorieActiviteRepository $repository,$id,ManagerRegistry $doctrine)
    {
        $categorie=$repository->find($id);
        $em=$doctrine->getManager();
        $em->remove($categorie);
        $em->flush();
        return $this->redirectToRoute("app_categorie");
    }








}
