<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactContollerController extends AbstractController
{
    /**
     * @Route("/", name="frontacceuil")
     */
    public function index(Request $request, \Swift_Mailer $mailer): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $form =$this->createForm(ContactType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $contact=$form->getData();
            $message=(new \Swift_Message('Nouveau contact'))
                ->setFrom('fourat.chaachoui@esprit.tn')
                ->setTo($contact['Email'])
                ->setBody(
                    $this->renderView(
                        'emails/contact.html.twig', compact('contact')
                    ),
                    'text/html'
                )
                ;

            $mailer->send($message);
            $this->addFlash('message','message sent');
        }
        if($this->getUser()->getRoles()==['ROLE_USER']){
            return $this->redirectToRoute('reservation');
        }
        return $this->render('frontend/frontbody.html.twig', [
            'controller_name' => 'RedirectController',
            'page_title' => 'Acceuil',
            'form'=>$form->createView()
        ]);
    }
}
