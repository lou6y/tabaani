<?php
/**
 * Created by PhpStorm.
 * User: giorgiopagnoni
 * Date: 17/01/18
 * Time: 10:14
 */

namespace App\Controller;


use App\Repository\UserRepository;

//use Laminas\Diactoros\Request\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\User\EditType;
use App\Form\User\RegistrationType;
use App\Form\User\RequestResetPasswordType;
use App\Form\User\ResetPasswordType;
use App\Form\Security\LoginType;
use App\Security\LoginFormAuthenticator;
use App\Service\CaptchaValidator;
use App\Service\Mailer;
use App\Service\TokenGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\Exception\ValidatorException;
use Dompdf\Dompdf;
use Dompdf\Options;

use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    const DOUBLE_OPT_IN = false;

    /**
     * @Route("/register", name="register" , )
     * @param Request $request
     * @param TokenGenerator $tokenGenerator
     * @param UserPasswordEncoderInterface $encoder
     * @param Mailer $mailer
     * @param CaptchaValidator $captchaValidator
     * @param TranslatorInterface $translator
     * @return Response
     * @throws \Throwable
     */


    public function register(Request $request, TokenGenerator $tokenGenerator, UserPasswordEncoderInterface $encoder,
                             Mailer $mailer, CaptchaValidator $captchaValidator, TranslatorInterface $translator)
    {
        $form = $this->createForm(RegistrationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            try {
                // if (!$captchaValidator->validateCaptcha($request->get('g-recaptcha-response'))) {
                //    $form->addError(new FormError($translator->trans('captcha.wrong')));
                //    throw new ValidatorException('captcha.wrong');
                // }

                $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
                $token = $tokenGenerator->generateToken();
                $user->setToken($token);
                $user->setIsActive(false);
                $user->setRole('Client');

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                if (self::DOUBLE_OPT_IN) {
                    $mailer->sendActivationEmailMessage($user);
                    $this->addFlash('success', 'user.activation-link');
                    return $this->redirect($this->generateUrl('frontacceuil'));
                }

                return $this->redirect($this->generateUrl('user_activate', ['token' => $token]));

            } catch (ValidatorException $exception) {

            }
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
            'captchakey' => $captchaValidator->getKey()
        ]);
    }

    /**
     * @Route("/activate/{token}", name="activate")
     * @param $request Request
     * @param $user User
     * @param GuardAuthenticatorHandler $authenticatorHandler
     * @param LoginFormAuthenticator $loginFormAuthenticator
     * @return Response
     */
    public function activate(Request $request, User $user, GuardAuthenticatorHandler $authenticatorHandler, LoginFormAuthenticator $loginFormAuthenticator)
    {
        $user->setIsActive(true);
        $user->setToken(null);
        $user->setActivatedAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'user.welcome');

        // automatic login
        return $authenticatorHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $loginFloginFormAuthenticator,
            'main'
        );
    }




    /**
     * @Route("/request-password-reset", name="request_password_reset")
     * @param Request $request
     * @param TokenGenerator $tokenGenerator
     * @param Mailer $mailer
     * @param CaptchaValidator $captchaValidator
     * @param TranslatorInterface $translator
     * @throws \Throwable
     * @return Response
     */
    public function requestPasswordReset(Request $request, TokenGenerator $tokenGenerator, Mailer $mailer,
                                         CaptchaValidator $captchaValidator, TranslatorInterface $translator)
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('frontacceuil'));
        }

        $form = $this->createForm(RequestResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                // if (!$captchaValidator->validateCaptcha($request->get('g-recaptcha-response'))) {
                //    $form->addError(new FormError($translator->trans('captcha.wrong')));
                //    throw new ValidatorException('captcha.wrong');
                // }
                $repository = $this->getDoctrine()->getRepository(User::class);

                /** @var User $user */
                $user = $repository->findOneBy(['email' => $form->get('_username')->getData()]);
                if (!$user) {
                    $this->addFlash('warning', 'user.not-found');
                    return $this->render('user/request-password-reset.html.twig', [
                        'form' => $form->createView(),
                        'captchakey' => $captchaValidator->getKey()
                    ]);
                }

                $token = $tokenGenerator->generateToken();
                $user->setToken($token);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $mailer->sendResetPasswordEmailMessage($user);

                $this->addFlash('success', 'user.request-password-link');
                return $this->redirect($this->generateUrl('frontacceuil'));
            } catch (ValidatorException $exception) {

            }
        }

        return $this->render('user/request-password-reset.html.twig', [
            'form' => $form->createView(),
            'captchakey' => $captchaValidator->getKey()
        ]);
    }

    /**
     * @Route("/reset-password/{token}", name="reset_password")
     * @param $request Request
     * @param $user User
     * @param $authenticatorHandler GuardAuthenticatorHandler
     * @param $loginFormAuthenticator LoginFormAuthenticator
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function resetPassword(Request $request, User $user, GuardAuthenticatorHandler $authenticatorHandler,
                                  LoginFormAuthenticator $loginFormAuthenticator, UserPasswordEncoderInterface $encoder)
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('frontacceuil'));
        }

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            $user->setToken(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'user.update.success');

            // automatic login
            return $authenticatorHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $loginFormAuthenticator,
                'main'
            );
        }

        return $this->render('user/password-reset.html.twig', ['form' => $form->createView()]);
    }




    /*


    /**
         * @Route("/edit", name="edit")
         * @Security("has_role('ROLE_USER')")
         * @param $request Request
         * @param UserPasswordEncoderInterface $encoder
         * @return Response
         */
    /* public function edit(Request $request, UserPasswordEncoderInterface $encoder)
      {
          $origPwd = $this->getUser()->getPassword();
          $form = $this->createForm(EditType::class, $this->getUser());
          $form->handleRequest($request);

          if ($form->isSubmitted()) {
              /** @var User $user */
    /*     $user = $form->getData();
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
*/

///////////////////////////////////////////////////Mobile//////////////////////////////////////////////////////////////////
//////////////////////////////////// Login/////////////////////////////////////////////
    /**
     * @Route("/signinm", name="app_loginnnn")
     */

    public function signinAction(Request $request):JsonResponse{
        $email = $request->query->get("email");
        $password = $request->query->get("password");

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email'=>$email]);//bch nlawj ala user b username ta3o fi base s'il existe njibo
        //ken l9ito f base


            //lazm n9arn password zeda madamo crypté nesta3mlo password_verify
            if(password_verify($password,$user->getPassword())) {
                $serializer = new Serializer([new ObjectNormalizer()]);
                $formatted = $serializer->normalize($user);
                return new JsonResponse($user->getRole());//
            }
            else
                return new JsonResponse(null);
    }
//////////////////////////////////////////////Inscription//////////////////////////////////////////////////
    /**
     * @Route("/signupm", name="app_registerr")
     */
    public function  signupAction(Request  $request, UserPasswordEncoderInterface $passwordEncoder):JsonResponse {

        $email = $request->query->get("email");
        $username = $request->query->get("username");
        $password = $request->query->get("password");
        $role= $request->query->get("role");
        //$dateNassiance = $request->query->get("dateNaissance");

        //control al email lazm @
       /* if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse("email invalid.");
        }*/
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRole("Client");
        $pass = $passwordEncoder->encodePassword(
            $user,
            $password
        );
        $user->setPassword($pass);
        $user->setIsActive(true);//par défaut user lazm ykoun enabled.
        //$user->setDateNaissance(new \DateTime($dateNassiance));
        //$user->setRole(array($role));//aleh array khater roles par defaut fi security  type ta3ha array

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = $serializer->normalize($user);
            return new JsonResponse($formatted);
           // return new JsonResponse("success",200);//200 ya3ni http result ta3 server OK
        }catch (\Exception $ex) {
            return new Response("execption ".$ex->getMessage());
        }
    }


////Gestion_Profile (Edit User)////////////////////////////////////////////////////////////////////////////////////////
    /**
     * @Route("/editUser", name="app_gestion_profile")
     */

   /* public function editUser(Request $request, UserPasswordEncoderInterface $passwordEncoder) {
        $id = $request->get("id");//kima query->get wala get directement c la meme chose
        $username = $request->query->get("username");
        $password = $request->query->get("password");
        $email = $request->query->get("email");
        $em=$this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        //bon l modification bch na3mlouha bel image ya3ni kif tbadl profile ta3ik tzid image
        if($request->files->get("photo")!= null) {

            $file = $request->files->get("photo");//njib image fi url
            $fileName = $file->getClientOriginalName();//nom ta3ha

            //taw na5ouha w n7otaha fi dossier upload ely tet7t fih les images en principe te7t public folder
            $file->move(
                $fileName
            );
            $user->setPhoto($fileName);
        }


        $user->setUsername($username);
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $password
            )
        );

        $user->setEmail($email);
        $user->setIsActive(true);//par défaut user lazm ykoun enabled.



        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse("success",200);//200 ya3ni http result ta3 server OK
        }catch (\Exception $ex) {
            return new Response("fail ".$ex->getMessage());
        }

    }
*/
/////////////////////////////////////////////Supprimer///////////////////////////////////////////////////
    /**
     * @Route("/deleteU", name="delete_reclamation")
     */

    public function deleteU(Request $request) {
        $id = $request->get("id");

        $em = $this->getDoctrine()->getManager();
        $User = $em->getRepository(User::class)->find($id);
        if($User!=null ) {
            $em->remove($User);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("User a ete supprimee avec success.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id User invalide.");


    }



////////////////////////////////////////Afficher users///////////////////////////////////////////////
    /**
     * @Route("/AfficherUserMobile", name="AfficherUserMobile" )
     */
    public function AfficherUserMobile (): JsonResponse

    {
        //$email=$request->get('email');

        $user= $this->getDoctrine()->getManager()->getRepository(User::class)->findAll() ;


        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($user);
        return new JsonResponse($formatted);

    }

////////////////////////////////////////mdp oublié///////////////////////////////////////////////////////
    /**
     * @Route("/getPasswordByEmail", name="app_password")
     */

    public function getPassswordByEmail(Request $request):JsonResponse{

        $email = $request->get('email');
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findBy(['email'=>$email]);
        if($user) {
           // $password = $user->getPassword();
            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = $serializer->normalize($user);
            return new JsonResponse($formatted);
        }
        return new JsonResponse("user not found");




    }
/////////////////////////////////////////////////////////Edit/////////////////////////////////////////////////////////////

    /**
     * @Route("/ediUserr", name="app_gestion_profile")
     */

    public function EditUserr(Request $request)
    {
        $id = $request->get("id");//kima query->get wala get directement c la meme chose
        $username = $request->query->get("username");
        $password = $request->query->get("password");
        $email = $request->query->get("email");
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        //bon l modification bch na3mlouha bel image ya3ni kif tbadl profile ta3ik tzid image
        if ($request->files->get("photo") != null) {

            $file = $request->files->get("photo");//njib image fi url
            $fileName = $file->getClientOriginalName();//nom ta3ha

            //taw na5ouha w n7otaha fi dossier upload ely tet7t fih les images en principe te7t public folder
            $file->move(
                $fileName
            );
            $user->setPhoto($fileName);
        }


        $user->setUsername($username);
        $user->setPassword($password

        );

        $user->setEmail($email);
        $user->setIsActive(true);//par défaut user lazm ykoun enabled.


        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse("success", 200);//200 ya3ni http result ta3 server OK
        } catch (\Exception $ex) {
            return new Response("fail " . $ex->getMessage());
        }

    }











}