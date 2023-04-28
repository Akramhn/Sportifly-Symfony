<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\TriUserType;
use App\Form\UserType;
use App\Repository\ReclamationsRepository;
use App\Repository\UserRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notifier;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Laracasts\Flash\Flash;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;



class UserController extends AbstractController
{
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    #[Route('/{_locale}/admin', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response

    {
        $user = $userRepository->findAll();
        $user = $paginator->paginate(
            $user, /* query NOT result */
            $request->query->getInt('page', 1),
            4
        );
        return $this->render("user/index.html.twig", array("users" => $user));

    }

    #[Route('/admin/Listuser', name: 'app_Listuser')]
    public function Listuser(UserRepository $repository, Request $request, PaginatorInterface $paginator)
    {
        $user = $repository->findAll();
        $user = $paginator->paginate(
            $user, /* query NOT result */
            $request->query->getInt('page', 1),
            4
        );
        return $this->render("user/index.html.twig", array("users" => $user));


    }


    #[Route('/admin/Listtri', name: 'app_tri')]
    public function Listrri(UserRepository $repository, Request $request, PaginatorInterface $paginator)
    {

        $user = $repository->orderByexperience();
        $user = $paginator->paginate(
            $user, /* query NOT result */
            $request->query->getInt('page', 1),
            4
        );

        return $this->render("user/index.html.twig", array("users" => $user));

    }

    #[Route('/adduser', name: 'app_add_user', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Ajout avec Success');
            if ($user->getPassword()) {
                $user->setPassword(
                    $this->userPasswordEncoder->encodePassword($user, $user->getPassword())
                );
                $user->eraseCredentials();
            }
            $roles[] = '';
            $user->setRoles($roles);
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/adduser.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{id}/edituser', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edituser(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_Listuser', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edituser.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{email}/edituserfront', name: 'app_edit_profile', methods: ['GET', 'POST'])]
    public function edituserfront(Request $request, UserRepository $userRepository, $email, \Doctrine\Persistence\ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $user = $userRepository->findOneBy(['email' => $email]);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPassword()) {
                $user->setPassword(
                    $this->userPasswordEncoder->encodePassword($user, $user->getPassword())
                );
                $user->eraseCredentials();
            }
            $em = $doctrine->getManager();
            $em->flush();

            return $this->redirectToRoute('app_logout', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/editprofil.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/user/updateuser/json', name: 'app_edit_profilejson')]
    public function edituserfrontjson(UserPasswordHasherInterface $userPasswordHasher,EntityManagerInterface $entityManager,Request $request, UserRepository $userRepository, NormalizerInterface $normalizer): Response
    {

        $user = $userRepository->find($request->get('id'));

        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $request->get('plainPassword')
            ));
        $user->setRoles(['Role_USER']);
        $user->setEmail($request->get('email'));
        $user->setLastname($request->get('lastname'));
        $user->setImage($request->get('image'));
        $entityManager->persist($user);
        $entityManager->flush();
        if($request->get('email') == "" || $request->get('lastname') == ""||$request->get('image') == ""   )
            return new JsonResponse('manque');
        $jsonContent = $normalizer->normalize($user, 'json', ['groups' => "users"]);
        return new Response(json_encode($jsonContent));

    }


    #[Route('/admin/deleteuser/{id}', name: 'app_deleteuser')]
    public function delete(UserRepository $repository, $id, \Doctrine\Persistence\ManagerRegistry $doctrine)
    {
        $user = $repository->find($id);
        $em = $doctrine->getManager();
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute("app_user_index");


    }


    #[Route('/admin/deleteuserjson/{id}', name: 'app_deleteuserjson')]
    public function deletejson(UserRepository $repository, $id, \Doctrine\Persistence\ManagerRegistry $doctrine, NormalizerInterface $normalizer)
    {
        $user = $repository->find($id);
        $em = $doctrine->getManager();
        $em->remove($user);
        $em->flush();
        $jsonContent = $normalizer->normalize($user, 'json', ['groups' => 'users']);
        return new Response("user deleted successfully" . json_encode($jsonContent));


    }
    #[Route('/admin/Allusers/json', name: 'listM')]
    public function getUsers(UserRepository $userRepository, SerializerInterface $serializer)
    {
        $users = $userRepository->findAll();
        $json = $serializer->serialize($users, 'json', ['groups' => "users"]);
        return new Response($json);
    }


    #[Route('/admin/adduserjson', name: 'app_add_userjson')]
    public function newjson(Request $request, UserRepository $userRepository, SerializerInterface $serializer): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Ajout avec Success');
            if ($user->getPassword()) {
                $user->setPassword(
                    $this->userPasswordEncoder->encodePassword($user, $user->getPassword())
                );
                $user->eraseCredentials();
            }
            $roles[] = '';
            $user->setRoles($roles);
            $userRepository->save($user, true);

        }
        $json = $serializer->serialize($user, 'json', ['groups' => "users"]);
        return new Response($json);
    }

    #[Route('/loginmo', name: 'app_loginmo')]
    public function loginmo(Request $request,UserRepository $repo): JsonResponse
    {
        // Get the username and password input from the user
        $username = $request->get('email');
        $password = $request->get('password');


        if($request->get('email') == "" || $request->get('password') == ""){
            return new JsonResponse('Invalid', Response::HTTP_UNAUTHORIZED);
        }
        else{
            // Authenticate the user
            $user = $repo->findOneBy(['email' => $username]);
            if (!$user || !$this->userPasswordEncoder ->isPasswordValid($user, $password)) {
                return new JsonResponse('Invalid', Response::HTTP_UNAUTHORIZED);
            }

            // Return the JWT in the response body
            return new JsonResponse('Valid');
        }

    }


    #[Route('/{id}/change-status', name: 'app_user_change_status')]
    public function changeStatusUser(User $user, UserRepository $userRepository, SendMailService $mail): Response
    {


        if ($user->getStatus() == 'Blocked') {
            $user->setStatus('Actif');
            $userRepository->save($user, true);
            $status = ' We are glad to inform you that your account has been activated again .
        You can now access our app and benefit from our services .';

            $context = compact('status');

            $mail->send(
                'amira.khalfi@esprit.tn',
                $user->getEmail(),
                'Account Re-activated',
                '/user/warning-status.html.twig',
                $context
            );

        } else {
            $user->setStatus('Blocked');
            $userRepository->save($user, true);


            $status = ' We are sorry to inform you that your account has been blocked .
        You will no longer be able to benefit from our services until later notice .';


            $context = compact('status');
            $mail->send(
                'amira.khalfi@esprit.tn',
                $user->getEmail(),
                'Account Blocked',
                '/user/warning-status.html.twig',
                $context,
            );

        }
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/pdf/{id}', name: 'PDF_user', methods: ['GET'])]
    public function pdf(UserRepository $Repository,$id)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Open Sans');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $user= $Repository->find($id);
        $html = $this->renderView('user/pdf.html.twig', [
            'users' => [$user],
        ]);

        // Add header HTML to $html variable
        $headerHtml = '<h1 style="text-align: center; color: #b0f2b6;">Bienvenue chez Sportifly </h1>';
        $html = $headerHtml . $html;

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        // Send the PDF to the browser
        $response = new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="ListeDesuser.pdf"',
        ]);

        return $response;
    }




#[Route('/recherche_ajax', name: 'recherche_ajax')]
    public function rechercheAjax(Request $request,UserRepository $sr):JsonResponse
    {
        $requestString = $request->query->get('searchValue');
        $resultats = $sr->findUserbyEmail($requestString);

        return $this->json($resultats);
    }


    #[Route('/Adm', name: 'app_adm')]
    public function affichage(): Response
    {
        return $this->render("user/index.html.twig");
    }
    #[Route('/userJson', name: 'app_userJson')]
    public function userJson(Request $request,SerializerInterface $serializer,UserRepository  $userRepository): Response
    {
        $email = $request->get('email');
        $users = $userRepository->findOneByEmail($email);
        $json = $serializer->serialize($users, 'json', ['groups' => "users"]);
        return new Response($json);
    }


    //block user by id
    #[Route('/block/{id}', name: 'app_user_block', methods: ['GET', 'POST'])]
    public function block(Request $request, User $user, UserRepository $userRepository): Response
    {
        $user->setIsBlocked(true);
        $user->setEtat("bloque");
        $userRepository->save($user, true);
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    //unblock user by id
    #[Route('/unblock/{id}', name: 'app_user_unblock', methods: ['GET', 'POST'])]
    public function unblock(Request $request, User $user, UserRepository $userRepository): Response
    {
        $user->setIsBlocked(false);
        $user->setEtat(" debloque");
        $userRepository->save($user, true);
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }





}