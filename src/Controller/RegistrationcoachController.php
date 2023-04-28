<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterCoachFormType;
use App\Form\RegistrationFormType;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RegistrationcoachController extends AbstractController
{
    #[Route('/registerr', name: 'app_registercoach')]
    public function registercoach(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $coach = new User();
        $form = $this->createForm(RegisterCoachFormType::class, $coach);
        $form->handleRequest($request);
        $filesystem = new Filesystem();
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $coach->setPassword(
                $userPasswordHasher->hashPassword(
                    $coach,
                    $form->get('plainPassword')->getData()
                )
            );
            $coach->setRoles(['Role_Coach']);
            $entityManager->persist($coach);
            $entityManager->flush();
            $uploadedFile = $form->get('image')->getData();
            $formData = $uploadedFile->getPathname();
            $sourcePath = strval($formData);
            $destinationPath = 'userphoto/photo' . strval($coach->getId()) . '.png';
            $coach->setImage($destinationPath);
            $filesystem->copy($sourcePath, $destinationPath);
            $entityManager->persist($coach);
            $entityManager->flush();
            // do anything else you need here, like send an email

            /*  return $userAuthenticator->authenticateUser(
                  $user,
                  $authenticator,
                  $request
              );*/
            return $this->redirectToRoute("app_login");
        }

        return $this->render('registration/registrationcoach.html.twig', [
            'RegisterCoachForm' => $form->createView(),
        ]);
    }


    #[Route('/registerrjson', name: 'app_registercoachjson')]
    public function registercoachjson(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator, EntityManagerInterface $entityManager, NormalizerInterface $normalizer): Response
    {
        $coach = new User();
        $form = $this->createForm(RegisterCoachFormType::class, $coach);
        $form->handleRequest($request);
        $filesystem = new Filesystem();
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $coach->setPassword(
                $userPasswordHasher->hashPassword(
                    $coach,
                    $form->get('plainPassword')->getData()
                )
            );
            $coach->setRoles(['Role_Coach']);
            $entityManager->persist($coach);
            $entityManager->flush();
            $uploadedFile = $form->get('image')->getData();
            $formData = $uploadedFile->getPathname();
            $sourcePath = strval($formData);
            $destinationPath = 'userphoto/photo' . strval($coach->getId()) . '.png';
            $coach->setImage($destinationPath);
            $filesystem->copy($sourcePath, $destinationPath);
            $entityManager->persist($coach);
            $entityManager->flush();

        }
        $jsonContent=$normalizer->normalize($coach,'json',['groups'=>"users"]);
        return new Response(json_encode($jsonContent));

    }

}

