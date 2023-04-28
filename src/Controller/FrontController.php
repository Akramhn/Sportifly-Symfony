<?php

namespace App\Controller;

use App\Entity\Actualite;
use App\Entity\User;
use App\Form\ActualiteType;
use App\Services\BadWordsFilterService;
use App\Entity\CommentaireAct;
use App\Form\CommentaireActType;
use App\Repository\ActualiteRepository;
use App\Repository\CommentaireActRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Knp\Component\Pager\PaginatorInterface;








class FrontController extends AbstractController
{
    private $badWordFilter;

    public function __construct(BadWordsFilterService $badWordFilter)
    {
        $this->badWordFilter = $badWordFilter;
    }

    #[Route('/front', name: 'app_front')]
    public function affichage(ActualiteRepository $actualiteRepository, PaginatorInterface $paginator,Request $request): Response
    {

        $actualites = $actualiteRepository->findAll();
        $actualites =$paginator->paginate(
            $actualites,$request->query->getInt('page',1),4
        );

        return $this->render('front/newspage.html.twig', [
            'actualites' => $actualites

        ]);
    }


    #[Route('/allActualite', name: 'list')]
    public function affichageJson(ActualiteRepository $actualiteRepository, NormalizerInterface $normalizer)
    {

        $actualites = $actualiteRepository->findAll();
        $actualiteNormalises = $normalizer->normalize($actualites,'json',['groups'=>"actualite"]);
        $json = json_encode($actualiteNormalises);

        return new Response($json);
    }


    #[Route('/ajouterCom/{id}', name: 'app_ajouterCom')]
    public function ajoutercommentaire(ActualiteRepository $repository, $id, Request $request, EntityManagerInterface $em,Security $security,BadWordsFilterService $badWordFilter): Response
    {$user=new User();
        $user=$security->getUser();
        $comment = new CommentaireAct();
        $form = $this->createForm(CommentaireActType::class, $comment);

        $actualite = $repository->find($id);
        $comment->setIdActualite($actualite);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $commentContent = $comment->getContenu();
            $filteredCommentContent = $badWordFilter->filter($commentContent);
            $comment->setContenu($filteredCommentContent);
            $comment->setIdUser($user);

            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute("app_ajouterCom",array('id'=>$id));
        }

        return $this->render('homee/single.html.twig', array(
            "form" => $form->createView(),
            "actualite" => $actualite
        ));
    }
/*
    #[Route('/qrCode/{id}', name: 'app_qrCode',methods: ['GET'])]
    public function qrCodeAction($id): Response
    {
        // Récupérer l'actualité à partir de l'ID
        $actualite = $this->getDoctrine()->getRepository(Actualite::class)->find($id);

        // Générer le code QR
        $qrCodeGenerator = $this->get('endroid.qrcode.generator');
        $qrCode = $qrCodeGenerator->generate($actualite->getTitre());

        // Personnaliser le code QR
        $qrCode->setSize(300);
        $qrCode->setMargin(10);

        // Générer la réponse HTTP contenant le code QR
        $qrCodeResponse = new QrCodeResponse($qrCode);

        return $this->render('front/qrCode.html.twig', [
            'qrCodeUrl' => $qrCodeResponse,
            'debug' => 'La variable qrCodeUrl est bien définie',
        ]);
    }
*/


    #[Route('/updateCom/{id}/{id2}', name: 'app_updateCom')]
    public function updateCommentaire(CommentaireActRepository $repository, $id,$id2, ManagerRegistry $doctrine, Request $request)
    {
        $comment = $repository->find($id);
        $form = $this->createForm(CommentaireActType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute("app_ajouterCom",array('id'=>$id2));
        }
        return $this->renderForm('front/updateCom.html.twig', array("form" => $form));
    }


         #[Route('/updateComAct/{id}', name: 'app_updateComAct')]
    public function updateCommentaireJSON( $id, Request $request,NormalizerInterface $normalizer)
    {
        $em = Doctrine()->getManager();
        $comment = $em ->getRepository(CommentaireAct::class)->find($id);
        $comment -> setContenu($request->get('contenu'));
        $em->flush();
        $jsonContent = $normalizer ->normalize($comment,'json',['groups'=>'comment']);
        return new Response("commentaire ajouté ".json_encode($jsonContent));
    }


    #[Route('/supprimerCom/{id}/{id2}', name: 'app_supprimerCom')]
    public function supprimerComment(CommentaireActRepository $repository,$id2, $id, ManagerRegistry $doctrine)
    {
        $comment = $repository->find($id);
        $form = $this->createForm(CommentaireActType::class, $comment);
        $em = $doctrine->getManager();
        $em->remove($comment);
        $em->flush();
        return $this->redirectToRoute("app_ajouterCom",array('id'=>$id2));

    }


         #[Route('/supprimerComAct/{id}', name: 'app_supprimerComAct')]
    public function supprimerCommentJSON( $id, Request $request, ManagerRegistry $doctrine,NormalizerInterface $normalizer)
    {

        $em = $doctrine->getManager();
        $comment = $em-> getRepository(CommentaireAct::class)->find($id);
        $em->remove($comment);
        $em->flush();
        $jsonContent = $normalizer ->normalize($comment,'json',['groups'=>'comment']);
        return new Response("commenatire supprimé ".json_encode($jsonContent));

    }


    #[Route('/recherche', name: 'app_recherche')]
    public function recherche(ActualiteRepository $repository, Request $request)
    {
        $query = $request->query->get('q'); // Get the search query from the request

        if (!$query) {
            return $this->redirectToRoute('app_front'); // Redirect to the main page if no query is provided
        }

        $actualites = $repository->search($query); // Use a custom method in your repository to search for actualités based on the query

        return $this->render('front/recherche.html.twig', [
            'query' => $query,
            'actualites' => $actualites,
        ]);
    }

}
