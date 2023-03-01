<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\User;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ReservationEvenement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\Json;



class EventMobileController extends AbstractController
{
    /**
     * @Route("/displayReserv", name="displayreservation")
     */
    public function allResAction()
    {

        $reservation = $this->getDoctrine()->getRepository(ReservationEvenement::class)->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($reservation);

        return new JsonResponse($formatted);

    }

    /**
     * @Route("/listevenement", name="list_evenement")
     */
    public function listEvenement()
    {
        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($evenement);

        return new JsonResponse($formatted);

    }
    /**
     * @Route("/addreservation", name="addreservation")
     */
    public function addAction(Request $request)
    {
        $entitymanager = $this->getDoctrine()->getManager();
        $nom = $request->get('nom');
        $prenom = $request->get('prenom');
        $evtid = $request->get('idEvenement');
        $mail = $request->get('mail');
        //$id_user = $request->get('id_user');
       // $ev=$entitymanager->getRepository(User::class)->findOneBy(array("id"=>$id_user));
        //$cat = new  Category();
        $ev=$entitymanager->getRepository(Evenement::class)->findOneBy(array("nomevent"=>$evtid));
        //int $idcat;
        //$idcat->setIdCategory($cat);




        $user = new ReservationEvenement();
        $user->setEvenement($ev);
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setMail($mail);
        //$user->setIdUser($ev);
        $entitymanager->persist($user);
        $entitymanager->flush();


        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($user);
        return new JsonResponse($formatted);

    }
}

