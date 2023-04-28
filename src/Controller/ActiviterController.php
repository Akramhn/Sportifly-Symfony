<?php

namespace App\Controller;
use Illuminate\Support\Facades\Auth;
use App\Entity\Activiter;
use App\Entity\CategorieActivite;
use App\Entity\User;
use App\Form\ActiviterType;

use App\Repository\ActiviterRepository;
use App\Repository\CategorieActiviteRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ActiviterController extends AbstractController
{
    #[Route('/activiter', name: 'app_activiter')]
    public function affiche_act(ActiviterRepository $repository,Security $security): Response
    { $user=$security->getUser();

        $activiter= $repository->findBy(['id_user' => $user]);
        return $this->render("activiter/listefront.html.twig",array("tabActiviter"=>$activiter));
    }



    #[Route('/liste_activ', name: 'liste_activiter')]
    public function affiche2_act(ActiviterRepository $repository): Response
    {
        $activiter= $repository->findAll();
        return $this->render("activiter/liste_back.html.twig",array("tabActiviter"=>$activiter));
    }

    #[Route('/add_activiter', name: 'app_addactiviter')]
    public function addactiviter(Request $request,ManagerRegistry $doctrine,UserRepository $repository): Response
    {

        $user=new User();
        $user=$repository->find(1);
        $Activ=new Activiter();
        $form= $this->createForm(ActiviterType::class,$Activ);
        $Activ->setIdUser($user);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){


            $em =$doctrine->getManager() ;
            $em->persist($Activ);
            $em->flush();
            return $this->redirectToRoute("liste_activiter");
        }
        return $this->renderForm("activiter/addActivit.html.twig",
            array("form"=>$form));


    }
    #[Route('/add_activiter_front', name: 'app_addactiviter_front')]
    public function addactiviter2(Request $request,ManagerRegistry $doctrine,Security $security): Response
    {


        $user=$security->getUser();
        $Activ=new Activiter();
        $form= $this->createForm(ActiviterType::class,$Activ);
        $Activ->setIdUser($user);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){


            $em =$doctrine->getManager() ;
            $em->persist($Activ);
            $em->flush();
            return $this->redirectToRoute("app_activiter");
        }
        return $this->renderForm("activiter/addfront.html.twig",
            array("form"=>$form));


    }





    #[Route('/updateactiviter/{id}',name:   'app_updateactiviter') ]
    Public function updateActiviter(ActiviterRepository $repository,$id,ManagerRegistry $doctrine,Request $request)
    {
        $activiter=$repository->find($id);
        $form=$this->createForm(ActiviterType::class,$activiter);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {$em=$doctrine->getManager();
        $em->flush();
        return $this->redirectToRoute("liste_activiter");
        }
        return $this->renderForm("activiter/updateActivit.html.twig",
        array("form"=>$form));

    }


    #[Route('/updateactiviter_front/{id}',name:   'app_updateactiviter_front') ]
    Public function updateActiviter2(ActiviterRepository $repository,$id,ManagerRegistry $doctrine,Request $request)
    {
        $activiter=$repository->find($id);
        $form=$this->createForm(ActiviterType::class,$activiter);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {$em=$doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute("app_activiter");
        }
        return $this->renderForm("activiter/updateActfront.html.twig",
            array("form"=>$form));

    }




#[Route('/removeactiviter/{id}',name:   'app_removeactiviter') ]
Public function removeActiviter(ActiviterRepository $repository,$id,ManagerRegistry $doctrine)
{
    $activiter=$repository->find($id);
    $em=$doctrine->getManager();
    $em->remove($activiter);
    $em->flush();
    return $this->redirectToRoute("app_activiter");
}




    #[Route('/removeactiviter_back/{id}',name:   'app_removeactiviter_back') ]
    Public function removeActiviter2(ActiviterRepository $repository,$id,ManagerRegistry $doctrine)
    {
        $activiter=$repository->find($id);
        $em=$doctrine->getManager();
        $em->remove($activiter);
        $em->flush();
        return $this->redirectToRoute("liste_activiter");
    }









    //les services Json

    #[Route('/displayJson/{id}', name: 'json_display')]
    public function displayjson(NormalizerInterface $normalizer,$id,UserRepository $repository2,Request $request,ActiviterRepository $repository)
    {$user=new User();
        $user=$repository2->find($id);
        $Activite = $repository->findBy(['id_user'=>$user]);


        $ActiviteNormlise = $normalizer->normalize($Activite, 'json', ['groups' => "Activite"]);

        $json = json_encode($ActiviteNormlise);
        return new Response($json);
    }

    #[Route('/addJson', name: 'json_add')]
    public function addjson(NormalizerInterface $normalizer,UserRepository $repository2,Request $request,ManagerRegistry $doctrine,CategorieActiviteRepository $repository)
    { $user=new User();
        $user=$repository2->find($request->get('id_user') );
        $categorie=new CategorieActivite();
        $categorie=$repository->find($request->get('ref_categ') );
        $em=$doctrine->getManager();
        $activite=new Activiter();
        $activite->setTitre($request->get('titre'));
        $date=$request->get('date_debut');
        $date2=new \DateTime($date);
        $activite->setDateDebut($date2);
        $date=$request->get('date_fin');
        $date2=new \DateTime($date);
        $activite->setDateFin($date2);

        $activite->setIdUser($user);
        $activite->setRefCateg($categorie);


        $em->persist($activite);
        $em->flush();


        $jsonContent=$normalizer->normalize($activite,'json',['groups'=>"Activite"]);
        $json=json_encode($jsonContent);
        return new Response($json);
    }


    #[Route('/updateJson/{id}', name: 'json_update')]
    public function updatejson($id,NormalizerInterface $normalizer,Request $request,ManagerRegistry $doctrine,CategorieActiviteRepository $repository,ActiviterRepository $repository2)
    {
        $categorie=new CategorieActivite();
        $categorie=$repository->find($request->get('ref_categ') );
        $em=$doctrine->getManager();
        $activite=$repository2->find($id);
        $activite->setTitre($request->get('titre'));
        $date=$request->get('date_debut');
        $date2=new \DateTime($date);
        $activite->setDateDebut($date2);
        $date=$request->get('date_fin');
        $date2=new \DateTime($date);
        $activite->setDateFin($date2);


        $activite->setRefCateg($categorie);

        $em->flush();


        $jsonContent=$normalizer->normalize($activite,'json',['groups'=>"Activite"]);
        $json=json_encode($jsonContent);
        return new Response("Activite Updated Successfuly".$json);
    }


    #[Route('/deletJson/{id}', name: 'json_delet')]
    public function deletjson($id,NormalizerInterface $normalizer,Request $request,ManagerRegistry $doctrine,ActiviterRepository $repository2)
    {
        $em=$doctrine->getManager();
        $activite=$repository2->find($id);
        $em->remove($activite);
        $em->flush();


        $jsonContent=$normalizer->normalize($activite,'json',['groups'=>"Activite"]);
        $json=json_encode($jsonContent);
        return new Response("Activite Deleted Successfuly".$json);
    }





}
