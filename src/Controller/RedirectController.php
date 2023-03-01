<?php

namespace App\Controller;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\User\EditType;
use App\Form\User\RegistrationType;
use App\Form\User\RequestResetPasswordType;
use App\Form\User\ResetPasswordType;
use App\Form\User\UserType;
use App\Security\LoginFormAuthenticator;
use App\Service\CaptchaValidator;
use App\Service\Mailer;
use App\Service\TokenGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Dompdf\Dompdf;
use Dompdf\Options;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\File\File;
//use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
//use Symfony\Component\Serializer\Serializer;



class RedirectController extends AbstractController
{
    /**
     * @Route("/backacceuil", name="backacceuil")
     */
    public function backend(): Response
    {
        return $this->render('backend/backbody.html.twig', [
            'controller_name' => 'RedirectController',
        ]);
    }
    /**
     * @Route("/frontacceuil", name="frontacceuil")
     */
    public function frontend(): Response
    {
        return $this->render('frontend/frontbody.html.twig', [
            'controller_name' => 'RedirectController',
        ]);
    }




  /**
     * @Route("/frontacceuil2", name="frontacceuil2")
     */
    public function frontLogin(): Response
    {
        return $this->render('user/front_apresLogin.html.twig' );
    }












/**
     * @Route("/{id}", name="user_delete1", methods={"DELETE"})
     */
  /*  public function deleteF(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }


    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
  /*  public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/frontacceuil2.html.twig', [
            'users' => $userRepository->findAll()
        ]);

    }

*/


 /**
     * @Route("/editF{id}/edit", name="user_edit", methods={"GET","POST"})
     */
  /*  public function editF(Request $request, User $user): Response
    {
        echo 'AAA';
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
       
    }*/




/**
     * @Route("/edit", name="edit")
    
     * @param $request Request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function edit(Request $request, UserPasswordEncoderInterface $encoder)
    {
        echo 'AAAAAAAAA';
        $origPwd = $this->getUser()->getPassword();
        $form = $this->createForm(EditType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            /** @var User $user */
           $user = $form->getData();
            $pwd = $user->getPassword() ? $encoder->encodePassword($user, $user->getPassword()) : $origPwd;
            $user->setPassword($pwd);
            
            $em = $this->getDoctrine()->getManager();

            if ($form->isValid()) {
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'user.update.success');

                return $this->redirect($this->generateUrl('frontacceuil2'));
            }

            // see http://stackoverflow.com/questions/9812510/symfony2-how-to-modify-the-current-users-entity-using-a-form
            $em->refresh($user);
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView()
        ]);



    }



/**
     * @Route("/back1", name="back1")
     */
    public function back(): Response
    {
        return $this->render('user/Back1.html.twig', [

        ]);
    }


      /**
     * @Route("/user_trieadmin", name="user_trieadmin" , methods={"GET","POST"})
     */
    
    public function trieradmin(UserRepository $userRepository , Request $request): Response
    {if($request->isMethod('POST'))
    {$role="admin";
        $email=$request->get('email');
        $e=$this->getDoctrine()->getManager()->getRepository(User::class)->rechercheNomEmailrole($email, $role);
        return $this->render('user/afficherusersback.html.twig', [
            'users' => $e  ]);

    }

        return $this->render('user/afficheruserroleadmin.html.twig', [
            'users' => $userRepository->trieradmin(),
        ]);
    }

  /**
     * @Route("/user_index1", name="user_index1" , methods={"GET","POST"})
     */
    public function index1(UserRepository $userRepository , Request  $request): Response
    {  if($request->isMethod('POST'))
    {
        $email=$request->get('email');
     
        return $this->render('user/afficherusersback.html.twig', [
            'users' => $userRepository ->findByOne($email)  ]);

    }
        return $this->render('user/afficherusersback.html.twig', [
            'users' => $userRepository->findAll(),
        ]);


    }





/**
     * @Route("/rechercheAll", name="rechercheAll" , methods={"GET","POST"})
     */
    public function rechercheAll(UserRepository $userRepository, Request $request): Response
    {
        if($request->isMethod('POST'))
        {
            $email=$request->get('email');
            $e=$this->getDoctrine()->getManager()->getRepository(User::class)->rechercheeclient($email);
            return $this->render('user/afficherusersback.html.twig', [
                'users' => $e  ]);


        }
        return $this->render('user/afficheruserroleclient.html.twig', [
            'users' => $userRepository->rechercheAll(),
        ]); 
    }


/**
     * @Route("/user_trieclient", name="user_trieclient" , methods={"GET","POST"})
     */
    public function trierclient(UserRepository $userRepository, Request $request): Response
    {
        if($request->isMethod('POST'))
        {$role="Client";
            $email=$request->get('email');
            $e=$this->getDoctrine()->getManager()->getRepository(User::class)->rechercheNomEmailrole($email,$role);
            return $this->render('user/afficherusersback.html.twig', [
                'users' => $e  ]);
        }
        return $this->render('user/afficheruserroleclient.html.twig', [
            'users' => $userRepository->trierclient(),
        ]);
    }




/**
     * @Route("/newBcack", name="user_newBack", methods={"GET","POST"})
     */
    public function newBack(Request $request,TranslatorInterface $translator, TokenGenerator $tokenGenerator, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
        $token = $tokenGenerator->generateToken();
        $user->setToken($token);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setRole('Admin');
            $user->setIsActive(1);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('user_index1');
        }

        return $this->render('user/Ajout_CompteBack.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }




    /**
     * @Route("/editBackend{id}/edit", name="user_edit1", methods={"GET","POST"})
     */
    public function editBckend(Request $request, User $user,TranslatorInterface $translator, TokenGenerator $tokenGenerator, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
        $token = $tokenGenerator->generateToken();
        $user->setToken($token);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index1');
        }

        return $this->render('user/editUserBackend.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }




    /**
     * @Route("/editProfile/edit", name="profile_edit", methods={"GET","POST"})
     */
    public function editProfile(Request $request, AuthenticationUtils $util,TranslatorInterface $translator, TokenGenerator $tokenGenerator, UserPasswordEncoderInterface $encoder): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user=$entityManager->getRepository(User::class)
            ->findOneBy(['email' => $util->getLastUsername()]);


        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);


        $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
        $token = $tokenGenerator->generateToken();
        $user->setToken($token);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index1');
        }

        return $this->render('user/editUserBackend.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editProfile/editFront", name="profile_editFront", methods={"GET","POST"})
     */
    public function editProfileFront(Request $request, AuthenticationUtils $util,TranslatorInterface $translator, TokenGenerator $tokenGenerator, UserPasswordEncoderInterface $encoder): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user=$entityManager->getRepository(User::class)
            ->findOneBy(['email' => $util->getLastUsername()]);


        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);


        $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
        $token = $tokenGenerator->generateToken();
        $user->setToken($token);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('frontacceuil2');
        }

        return $this->render('user/editUserFront.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $yo= $this->getDoctrine()->getRepository(User::class)->find($user);
            $entityManager->remove($yo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backacceuil');
    }
    /**
     * @Route("/F/{id}", name="user_deleteF", methods={"DELETE"})
     */
    public function deleteF(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $yo= $this->getDoctrine()->getRepository(User::class)->find($user);
            $entityManager->remove($yo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('frontacceuil');
    }



  /**
     * @Route("/user_recherche", name="user_recherche" , methods={"GET","POST"})
     */
    
  /* public function recherche (UserRepository $userRepository , Request $request): Response
    {if($request->isMethod('POST'))
        {;
            $email=$request->get('email');
            $e=$this->getDoctrine()->getManager()->getRepository(User::class)->rechercheEmail($email);
            return $this->render('user/afficherusersback.html.twig', [
                'users' => $e  ]);




        }
        return $this->render('user/afficheruserroleadmin.html.twig', [
            'users' => $userRepository->trieradmin(),
        ]);
    }*/


/////////////////////////////////////////////////////////////////Mobile/////////////////////////////////////////////////////
                            ///////////////////////////Back/////////////////////////














    /////////////////////////////////////////////////////Back////////////////////////////////////////////////////////

























}