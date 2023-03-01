<?php

namespace App\Controller;


use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Entity\Reservationhotel;
use App\Entity\User;
use App\Form\ReclamationType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
class ReclamationController extends AbstractController
{
    /**
     * @Route("/reclamation/add_reclamation/{idReserv}/{obj}", name="add_reclamation")
     */
    public function addReclamation(Request $request,$obj,$idReserv, AuthenticationUtils $util,\Swift_Mailer $mailer): Response
    {
        $rec= new Reclamation();
        $form = $this->createForm(ReclamationType::class,$rec);
        $form->handleRequest($request);


        if($form->isSubmitted()&& $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $yo=$entityManager->getRepository(User::class)
                ->findOneBy(['email' => $util->getLastUsername()]);

            $user= $entityManager->getRepository(User::class)->find($yo);
            $rec->setIdReserv($idReserv);
            $rec->setIdUser($user);
            $rec->setObjet($obj);
            $rec->setStatut(0);
            $rec->setDateRec( new \DateTime('now'));

            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('tabaani@esprit.tn')
                ->setTo('louay.fkhmd@gmail.com')
                ->setBody('Reclamation added');
            $mailer->send($message);



            $entityManager->persist($rec);

            $entityManager->flush();

            return $this->redirectToRoute('list_reclamation');

        }

        return $this->render("reclamation/addreclamation.html.twig", [
            "form_title" => "Ajouter une Reclamation",
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route("/reclamation/edit_reclamation/{idRec}", name="edit_reclamation")
     */
    public function editReclamation(Request $request, int $idRec): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        $rec = $entityManager->getRepository(Reclamation::class)->find($idRec);
        $form = $this->createForm(ReclamationType::class, $rec);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $rec->setDateRec( new \DateTime('now'));
            $entityManager->flush();
            return $this->redirectToRoute('list_reclamation');
        }

        return $this->render("reclamation/editreclamation.html.twig", [
            "form_title" => "Ajouter une Reclamation",
            "form" => $form->createView(),
        ]);
    }


    /**
     * @Route("/reclamation/listreclamation", name="list_reclamation")
     */
    public function listReclamation(Request $request,AuthenticationUtils $util, PaginatorInterface $paginator)
    {   $entityManager = $this->getDoctrine()->getManager();
        $yo=$entityManager->getRepository(User::class)
            ->findOneBy(['email' => $util->getLastUsername()]);


        $rec = $this->getDoctrine()->getRepository(Reclamation::class)
        ->findBy(array('idUser'=> $yo->getId()));
        $reponses = $this->getDoctrine()->getRepository(Reponse::class)
        ->findOneBy(array('idUser'=>$yo));
        $pagination = $paginator->paginate(
            $rec,
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
        return $this->render('reclamation/listreclamation.html.twig', [
            "recs" => $pagination, "reps"=>$reponses
        ]);
    }

    /**
     * @Route("/reclamation/delete_reclamation/{idRec}", name="delete_reclamation")
     */
    public function deleteReclamation(int $idRec): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $rec= $entityManager->getRepository(Reclamation::class)->find($idRec);
        $entityManager->remove($rec);
        $entityManager->flush();
        return $this->redirectToRoute('list_reclamation');
    }



    /**
     * @Route("/reclamation/listreclamationback", name="list_reclamationback")
     */
    public function listReclamationback(Request $request, PaginatorInterface $paginator)
    {
        $rec = $this->getDoctrine()->getRepository(Reclamation::class)->findAll();

        $pagination = $paginator->paginate(
            $rec,
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
        return $this->render('reclamation/listreclamationback.html.twig', [
            "recs" => $pagination,
        ]);
    }

    ///////////////////////////////////Mobile//////////////////////////////////////////////////
    /**
     * @Route("/displayReclamations", name="display_reclamation")
     */
    public function allRecAction()
    {
  
        $reclamation = $this->getDoctrine()->getManager()->getRepository(Reclamation::class)->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($reclamation);

        return new JsonResponse($formatted);

    }







}
