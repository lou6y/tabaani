<?php

namespace App\Controller;

use App\Entity\ReservationResto;
use App\Entity\Resto;
use App\Entity\User;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;

class MobileController extends AbstractController
{
    /**
     * @Route("/list_RestoM", name="list_RestoM")
     */
    public function listResto(Request $request)
    {
        $resto = $this->getDoctrine()->getManager()->getRepository(Resto::class)->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($resto);
        return new JsonResponse($formatted);



    }
    /**
     * @Route("/add_RestoM", name="add_RestoM")

     */
    public function ajouterResto(Request $request)
    {
        $resto = new resto();

        $nbplace=$request->get('nbplace');
        $budget=$request->get('budget');
        $nomresto=$request->get('nomresto');
        $restopic=$request->get('restopic');
        $em = $this->getDoctrine()->getManager();

        $resto->setNbplace($nbplace);
        $resto->setBudget($budget);
        $resto->setNomresto($nomresto);
        $resto->setrestopic($restopic);

        $em->persist($resto);
        $em->flush();

        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($resto);
        return new JsonResponse($formatted);
    }

    /**
     * @Route("/delete_RestoM", name="delete_RestoM")

     */
    public function deleteRestoM(Request $request)
    { $id_resto =$request->get("id_resto");
    $em = $this->getDoctrine()->getManager();
    $resto = $em->getRepository(resto::class)->find($id_resto);
    if($resto!=null) {
        $em->remove($resto);
        $em->flush();

        $serialize = new Serializer([new ObjectNormalizer()]);
        $formatted = $serialize->normalize("Resto a ete supprime avec success");
        return new JsonResponse($formatted);
    }
    return new JsonResponse("id_resto invalide .");


    }

    /**
     * @Route("/update_RestoM", name="update_RestoM")

     */
    public function updateRestoM(Request $request){

        $em = $this->getDoctrine()->getManager();

        $resto = $this->getDoctrine()->getManager()->getRepository(resto::class)->find($request->get("id_resto"));
        $resto->setNbplace($request->get("nbplace"));
        $resto->getBudget($request->get("budget"));
        $resto->getNomresto($request->get("nomresto"));
        $resto->setrestopic($request->get("restopic"));

        $em->persist($resto);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($resto);
        return new JsonResponse("resto a ete modifie avec success");


    }

//KKkk//

  /*  public function ajouterreservation( Request $request)
    {
        $Reservationresto = new ReservationResto();

        $nbpersonne=$request->get('nbpersonne');
        $mail=$request->get('mail');
        $numero=$request->get('numero');
        $em = $this->getDoctrine()->getManager();

        $Reservationresto->setNbpersonne($nbpersonne);
        $Reservationresto->setMail($mail);
        $Reservationresto->setn($numero);

        $em->persist($Reservationresto);
        $em->flush();

        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($Reservationresto);
        return new JsonResponse($formatted);
    }*/
    /**
     * @Route("/list_RestorM", name="list_RestorM")
     */
    public function listResrvation(Request $request)
    {
        $Reservationresto = $this->getDoctrine()->getManager()->getRepository(ReservationResto::class)->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($Reservationresto);
        return new JsonResponse($formatted);



    }

    /**
     * @Route("/ajouterreservation", name="ajouterreservation")

     */
    public function ajouterreservation( Request $request)    {

        $Reservationresto = new ReservationResto();

        $entitymanager = $this->getDoctrine()->getManager();
        $nbpersonne=$request->get('nbpersonne');
        $mail=$request->get('mail');
        $numero=$request->get('numero');
       // $id_resto=$request->get("idResto");
        $id_user=$request->get('id_user');
        $ev=$entitymanager->getRepository(User::class)->findOneBy(array("id"=>$id_user));
        $em = $this->getDoctrine()->getManager();






        $Reservationresto = new ReservationResto();
        $Reservationresto->setNbpersonne($nbpersonne);
        $Reservationresto->setMail($mail);
        $Reservationresto->setNumero($numero);
        $Reservationresto->setIdUser($ev);
        $entitymanager->persist($Reservationresto);
        $entitymanager->flush();


        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($Reservationresto);
        return new JsonResponse($formatted);

    }

    /**
     * @Route("/delete_ResM", name="delete_ResM")

     */
    public function deleteResM(Request $request)
    { $id_res_resto =$request->get("id_res_resto");
        $em = $this->getDoctrine()->getManager();
        $Reservationresto = $em->getRepository(ReservationResto::class)->find($id_res_resto);
        if($Reservationresto!=null) {
            $em->remove($Reservationresto);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("Reservation resto a ete supprime avec success");
            return new JsonResponse($formatted);
        }
        return new JsonResponse("$id_res_resto invalide .");


    }

}
