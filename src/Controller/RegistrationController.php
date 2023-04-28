<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\RegisterCoachFormType;
use App\Form\RegistrationFormType;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        $filesystem = new Filesystem();
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['Role_USER']);
            $entityManager->persist($user);
            $entityManager->flush();
            $uploadedFile = $form->get('image')->getData();
            $formData =  $uploadedFile->getPathname();
            $sourcePath = strval($formData);
            $destinationPath = 'userphoto/photo'.strval($user->getId()).'.png';
            $user->setImage($destinationPath);
            $filesystem->copy($sourcePath, $destinationPath);
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }



    #[Route('/registerjson', name: 'app_registerjson')]
    public function registerJson(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator, EntityManagerInterface $entityManager,NormalizerInterface $normalizer): Response
    {
        $user = new User();

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
            // do anything else you need here, like send an email
        if($request->get('email') == "" || $request->get('lastname') == ""||$request->get('plainPassword') == ""||$request->get('image') == ""   )
            return new JsonResponse('manque');

        $jsonContent=$normalizer->normalize($user,'json',['groups'=>"users"]);
        return new Response(json_encode($jsonContent));
        }




}
