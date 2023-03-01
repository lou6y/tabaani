<?php

namespace App\Controller;

use App\Entity\Hotel;

use App\Entity\Reservationhotel;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;


class MobileServicesController extends AbstractController
{
    /**
     * @Route("/mobile/hotels", name="mobile_hotels")
     */
    public function listhotel(SerializerInterface $serializer)
    {
        $hotel = $this->getDoctrine()->getRepository(Hotel::class)->findHotelService();

        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($hotel);
        return new JsonResponse($formatted);
    }



    /**
     * @Route("/mobile/newreserv", name="reservation_newmobile")
     */
    public function new(Request $request )
    {
        $entityManager = $this->getDoctrine()->getManager();
        $res = new Reservationhotel();

        $idhotel=$request->get('idhotel');

        $nbn=$request->get('nbn');
        $nbc=$request->get('nbc');
        $type=$request->get('type');
        $nbp=$request->get('nbp');
        $date=$request->get('date');
        $idUser=$request->get('id_user');
        $ev=$entityManager->getRepository(User::class)->findOneBy(array("id"=>$idUser));





        $dated = new \DateTime($date);
        $hotel = $entityManager->getRepository(Hotel::class)->find($idhotel);
        $res->setIdHotel($hotel);
        $res->setNbNuit($nbn);
        $res->setNbChambre($nbc);
        $res->setType($type);
        $res->setNbPersonne($nbp);
        $res->setDateReservation($dated);
        $res->setIdUser($ev);



        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($res);
        $entityManager->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($res);
        return new JsonResponse($formatted);



    }
}

