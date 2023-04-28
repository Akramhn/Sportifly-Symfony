<?php

namespace App\Controller;
use App\Entity\Event;
use App\Entity\User;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EventController extends AbstractController
{
    #[Route('/event', name: 'app_event')]
    public function index(): Response
    {
        return $this->render('event/index.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }
    
    
    #[Route('/events', name: 'app_events')]
    public function listEvent(EventRepository $repository, PaginatorInterface $paginator, Request $request)
    {
        $events = $repository->findAll();
        
        $query = $request->query->get('q');
        $events = $this->getDoctrine()
            ->getRepository(Event::class)
            ->searchEvents($query);
                                                 
        $pagination = $paginator->paginate(
            $events,
            $request->query->getInt('page', 1),
            10 // number of items per page
        );
    
        return $this->render("event/listevent.html.twig", [
            "tabEvents" => $pagination,
            'query' => $query,
        ]);
    }

    #[Route('/tri', name: 'tri')]
    public function tri(EventRepository $repository)
    {
        $events = $repository->findAll();
        $events=$repository->Trieparevent();                                        
       
    
        return $this->render("front/liste.html.twig",array("tabEvents"=>$events));
    }

    #[Route('/trie', name: 'trie')]
    public function trie(EventRepository $repositoryE)
    {
        $event = $repositoryE->findAll();
        $event=$repositoryE->Trieparevents();                                        
       
    
        return $this->render("event/listevent.html.twig",array("tabEvents"=>$event));
    }
 




    #[Route('/front1', name: 'app_front1')]

    public function listEventFront(EventRepository $repository,PaginatorInterface $paginator, Request $request)
    {
        $events= $repository->findAll();
        $pagination = $paginator->paginate(
            $events,
            $request->query->getInt('page', 1),
            10 // number of items per page
        );
    
        return $this->render("front/liste.html.twig",array("tabEvents"=>$events));
     }



    #[Route('/addevent', name: 'app_addevent')]
    public function addEvent(\Doctrine\Persistence\ManagerRegistry $doctrine,Request $request)
    {
        $em = $doctrine->getManager();
        $event= new Event();
        $form= $this->createForm(EventType::class,$event);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $file = $request->files->get('event')['img'];
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($uploads_directory, $filename);
            $event->setImg($filename);
            $em->persist($event);
            $em->flush();
            return  $this->redirectToRoute("app_events");
        }
        return $this->renderForm("event/add.html.twig",
            array("formEvent"=>$form));
    }



    #[Route('/addevent_front', name: 'app_addevent_front')]
    public function addEventfront(Security $security,\Doctrine\Persistence\ManagerRegistry $doctrine,Request $request)
    {$user=new User();
        $user=$security->getUser();
        $em = $doctrine->getManager();
        $event= new Event();
        $form= $this->createForm(EventType::class,$event);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $file = $request->files->get('event')['img'];
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($uploads_directory, $filename);
            $event->setImg($filename);
            $event->setIdUser($user);
            $em->persist($event);
            $em->flush();
            return  $this->redirectToRoute("app_front1");
        }
        return $this->renderForm("event/addfront.html.twig",
            array("formEvent"=>$form));
    }





    #[Route('/updateEvent/{id}', name: 'app_updateEvent')]
    public function updateEvent(EventRepository $repository,$id,ManagerRegistry $doctrine,Request $request)
    {
        $em =$doctrine->getManager();
        $event= $repository->find($id);
        $form=$this->createForm(EventType::class,$event);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $file = $request->files->get('event')['img'];
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($uploads_directory, $filename);
            $event->setImg($filename);
            $em->flush();
            return $this->redirectToRoute("app_events");
        }
        return $this->renderForm("event/add.html.twig",
            array("formEvent"=>$form));
    }

    #[Route('/removeEvent/{id}', name: 'app_removeEvent')]

    public function deleteEvent(ManagerRegistry $doctrine,$id,EventRepository $repository)
    {
        $event= $repository->find($id);
        $em= $doctrine->getManager();
        $em->remove($event);
        $em->flush();
        return $this->redirectToRoute("app_events");

    }
    
    /**
     * @Route("/search", name="event_search")
     */
    public function search(Request $request): Response
    {
        $query = $request->query->get('q');
        $events = $this->getDoctrine()
            ->getRepository(Event::class)
            ->searchEvents($query);

        return $this->render('event/search.html.twig', [
            'events' => $events,
            'query' => $query,
        ]);
    }
}
