<?php

namespace App\Controller;

use ApiPlatform\Api\UrlGeneratorInterface;
use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\ResetPassType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');

    }


    #[Route(path: '/forgot', name: 'forgot')]
    public function forgotPassword(Request $request, UserRepository $userRepository, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator,ManagerRegistry $doctrine)
    {


        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $donnees = $form->getData();//


            $user = $userRepository->findOneBy(['email' => $donnees]);
            if (!$user) {
                $this->addFlash('danger', 'cette adresse n\'existe pas');
                return $this->redirectToRoute('forgot');

            }
            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetToken($token);
                $em = $doctrine->getManager();
                $em->persist($user);
                $em->flush();


            } catch (\Exception $exception) {
                $this->addFlash('warning', 'une erreur est survenue :' . $exception->getMessage());
                return $this->redirectToRoute('app_login');


            }

            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABS_URL);

            //BUNDLE MAILER
            $message = (new \Swift_Message('Mot de password oublié'))
                ->setFrom('amira.khalfi@esprit.tn')
                ->setTo($user->getEmail())
                ->setBody("<p> Bonjour</p> unde demande de réinitialisation de mot de passe a été effectuée. Veuillez cliquer sur le lien suivant :" . $url,
                    "text/html");

            //send mail
            $mailer->send($message);
            $this->addFlash('message', 'E-mail  de réinitialisation du mp envoyé :');
            //    return $this->redirectToRoute("app_login");


        }

        return $this->render('security/forgotPassword.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/resetpassword/{token}', name: 'app_reset_password')]

    public function resetpassword(Request $request, string $token,UserPasswordEncoderInterface $passwordEncoder,ManagerRegistry $doctrine)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token' => $token]);

        if ($user == null) {
            $this->addFlash('danger', 'TOKEN INCONNU');
            return $this->redirectToRoute('app_login');

        }

        if ($request->isMethod('POST')) {
            $user->setResetToken(null);

            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('message', 'Mot de passe mis à jour :');
            return $this->redirectToRoute('app_login');

        } else {
            return $this->render('security/reset_password.html.twig', ['token' => $token]);

        }
    }




    #[Route(path: '/loginjson', name: 'app_loginjson')]
    public function loginjson(AuthenticationUtils $authenticationUtils,NormalizerInterface $normalizer): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $jsonContent = $normalizer->normalize('json', ['groups' => "users"]);
        return new Response(json_encode($jsonContent));
    }
}


   /* #[Route(path: '/forgot', name: 'forgotten_password')]
    public function forgottenPass(Request $request, UserRepository $usersRepo, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator, ManagerRegistry $doctrine)
    {
        // On crée le formulaire
        $form = $this->createForm(ResetPassType::class);

        // On traite le formulaire
        $form->handleRequest($request);

        // Si le formulaire est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les données
            $donnees = $form->getData();

            // On cherche si un utilisateur a cet email
            $user = $usersRepo->findOneByEmail($donnees['email']);

            // Si l'utilisateur n'existe pas
            if (!$user) {
                // On envoie un message flash
                $this->addFlash('danger', 'Cette adresse n\'existe pas');

                return $this->redirectToRoute('app_login');
            }

            // On génère un token
            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetToken($token);
                $em = $doctrine->getManager();
                $em->persist($user);
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Une erreur est survenue : ' . $e->getMessage());
                return $this->redirectToRoute('app_login');
            }

            // On génère l'URL de réinitialisation de mot de passe
            $url = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABS_URL);

            // On envoie le message
            $message = (new \Swift_Message('Mot de passe oublié'))
                ->setFrom('amirakhalfy12@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    "<p>Bonjour,</p><p>Une demande de réinitialisation de mot de passe a été effectuée pour le site Travel Me. Veuillez cliquer sur le lien suivant : " . $url . '</p>',
                    'text/html'
                );

            // On envoie l'e-mail
            $mailer->send($message);

            // On crée le message flash
            $this->addFlash('message', 'Un e-mail de réinitialisation de mot de passe vous a été envoyé');

            return $this->redirectToRoute('app_login');
        }

        // On envoie vers la page de demande de l'e-mail
        return $this->render('security/forgotpassword.html.twig', ['emailForm' => $form->createView()]);
    }

}
  #[Route(path: '/reset_pass/{token}', name: 'app_reset_password')]
    public function resetPassword($token, Request $request, EntityManagerInterface $manager,ManagerRegistry $doctrine){
        // On cherche l'utilisateur avec le token fourni

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token' => $token]);

        if(!$user){
            $this->addFlash('danger', 'Token inconnu');
            return $this->redirectToRoute('app_login');
        }


        if($request->isMethod('POST')){

            $user->setResetToken(null);

            #$hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($user->getPassword());

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('message', 'Mot de passe modifié avec succès');

            return $this->redirectToRoute('app_login');
        }else{
            return $this->render('security/reset_password.html.twig', ['token' => $token]);
        }

    }

}

   */
