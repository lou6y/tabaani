<?php

namespace App\Controller;


use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\ReservationRepository;

use phpDocumentor\Reflection\DocBlock\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * @Route("/reservation")
 */

class resTMobileController extends AbstractController
{


    ///////////////////afficher reser//////////////////////////
    /**
     * @Route("/testaff", name="reservationAPI")
     */
    public function API(ReservationRepository $repository): JsonResponse
    {

        $Reservations=$repository->findAll();
        $datas = array();
        foreach ($Reservations as $key=>$reservation)
        {
            $datas[$key]['id'] = $reservation->getIdReservation();
            $datas[$key]['prise'] = $reservation->getPrise();
            $datas[$key]['remise'] = $reservation->getRemise();
            $datas[$key]['dateD'] = $reservation->getDateDebut();
            $datas[$key]['dateF'] = $reservation->getDateFin();
        }
        return new JsonResponse($datas);
    }


        /**
         * @Route("/res/liste", name="lister", methods={"GET"})
         */
    public function listeRes(ReservationRepository $repo)
    {
        // On récupère la liste des articles
        $articles = $repo->apiFindAll();

        // On spécifie qu'on utilise l'encodeur JSON
        $encoders = [new JsonEncoder()];

        // On instancie le "normaliseur" pour convertir la collection en tableau
        $normalizers = [new ObjectNormalizer()];

        // On instancie le convertisseur
        $serializer = new Serializer($normalizers, $encoders);

        // On convertit en json
        $jsonContent = $serializer->serialize($articles, 'json', [
            'groups' => function ($object) {
                return $object->getId();
            }
        ]);

        // On instancie la réponse
        $response = new Response($jsonContent);

        // On ajoute l'entête HTTP
        $response->headers->set('Content-Type', 'application/json');

        // On envoie la réponse
        return $response;
    }
    /**
     * @param Request $request
     * @param SerializerInterface $serializer
     * @route("/api_add_reservation",name="addentreprisesjson")
     * @return Response
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function addEntreprise(Request $request,NormalizerInterface $normalizer){
        $entitymanager= $this->getDoctrine()->getManager();
        $date_debut = $request->query->get("date_debut");
        $date_fin = $request->query->get("date_fin");
        $iduser = $request->query->get("id_user");

        $ev=$entitymanager->getRepository(User::class)->findOneBy(array("id"=>$iduser));
        $em=$this->getDoctrine()->getManager();
        $entreprises= new Reservation();
        $entreprises->setPrise($request->get('prise'));
        $entreprises->setRemise($request->get('remise'));
        $entreprises->setDateDebut(new \DateTime($date_debut));
        $entreprises->setDateFin(new \DateTime($date_fin));
        $entreprises->setIduser($ev);

        $em->persist($entreprises);
        $em->flush();
        $jsonContent =$normalizer->normalize($entreprises,'json',['groups'=>'reservations']);
        return new Response(json_encode($jsonContent));
    }

   /* /**
     * @Route("/deleteRes", name="delete_reservation")
     * @Method("DELETE")
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */

   /* public function deleteResAction(Request $request) {
        $idReservation = $request->get("idReservation");

        $em = $this->getDoctrine()->getManager();
        $reservation = $em->getRepository(Reservation::class)->find($idReservation);
        if($reservation!=null ) {
            $em->remove($reservation);
            $em->flush();

            $serialize = new Serializer([new ObjectNormalizer()]);
            $formatted = $serialize->normalize("reservation supprimée avec succés.");
            return new JsonResponse($formatted);

        }
        return new JsonResponse("id reservation invalide.");

    }*/
    ///////////////delet res///////////////////////
    
}
