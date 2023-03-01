<?php

namespace App\Controller;

use App\Entity\Hotel;
use App\Entity\Service;
use App\Entity\User;
use App\Form\HotelType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ServiceRepository;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HotelController extends AbstractController
{
    /**
     * @Route("/hotel/add_hotel", name="add_hotel")
     */
    public function addHotel(Request $request): Response
    {
        $Hotel = new Hotel();
        $form = $this->createForm(HotelType::class,$Hotel);
        $form->handleRequest($request);


        if($form->isSubmitted()&& $form->isValid())
        {

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($Hotel);

            $entityManager->flush();

            return $this->redirectToRoute('list_hotel');

        }

        return $this->render("hotel/addhotel.html.twig", [
            "form_title" => "Ajouter un hotel",
            "form" => $form->createView(),
        ]);
    }
    /**
     * @Route("/hotel/listhotel", name="list_hotel")
     */
    public function listHotel(Request $request, PaginatorInterface $paginator)
    {
        $hotel = $this->getDoctrine()->getRepository(Hotel::class)->findAll();

        $pagination = $paginator->paginate(
            $hotel,
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
        return $this->render('hotel/showhotel.html.twig', [
            "hotel" => $pagination,
        ]);
    }
    /**
     * @Route("/front/hotel/listhotel", name="list_hotelfront")
     */
    public function listHotelfront(Request $request, PaginatorInterface $paginator)
    {
        $hotel = $this->getDoctrine()->getRepository(Hotel::class)->findAll();


        $pagination = $paginator->paginate(
            $hotel,
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
        return $this->render('hotel/frontshowhotels.html.twig', [
            "hotel" => $pagination,
        ]);
    }
    /**
     * @Route("/hotel/delete_hotel/{idHotel}", name="delete_hotel")
     */
    public function deleteHotel(int $idHotel): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $hotel = $entityManager->getRepository(Hotel::class)->find($idHotel);
        $entityManager->remove($hotel);
        $entityManager->flush();
        return $this->redirectToRoute('list_hotel');
    }
    /**
     * @Route("/hotel/edit_hotel/{idHotel}", name="edit_hotel")
     */
    public function editHotel(Request $request, int $idHotel): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        $hotel = $entityManager->getRepository(Hotel::class)->find($idHotel);
        $form = $this->createForm(HotelType::class, $hotel);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            return $this->redirectToRoute('list_hotel');
        }

        return $this->render("hotel/edithotel.html.twig", [
            "form_title" => "Modifier un hotel",
            "form" => $form->createView(),
        ]);
    }
    /**
     * @Route("/hotel/{idHotel}", name="detail_hotel")
     */
    public function detailHotel(int $idHotel): Response
    {
        $hotel = $this->getDoctrine()->getRepository(Hotel::class)->find($idHotel);
        $Service=$this->getDoctrine()->getRepository(Service::class)->findServiceByHotel($idHotel);

        return $this->render("hotel/detailhotel.html.twig", [
            "h" => $hotel,
            "s"=>$Service
        ]);
    }
    /**
     * @Route("/hotelfront/{idHotel}", name="detailfront_hotel")
     */
    public function detailFrontHotel(int $idHotel): Response
    {
        $hotel = $this->getDoctrine()->getRepository(Hotel::class)->find($idHotel);
        $Service=$this->getDoctrine()->getRepository(Service::class)->findServiceByHotel($idHotel);

        return $this->render("hotel/detailfronthotel.html.twig", [
            "h" => $hotel,
            "s"=>$Service
        ]);
    }

    /**
     *@Route("/searchajax", name="ajaxsearch")
     */
    public function searchAction(Request $request,AuthenticationUtils $util)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $yo=$entityManager->getRepository(User::class)
            ->findOneBy(['email' => $util->getLastUsername()]);


        $em=$this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $hotel = $em->getRepository(Hotel::class)->findEntitiesByString($requestString);
        if(!$hotel)
        {
            $result['hotel']['error']="hotel introuvable :( ";

        }else{
            $result['hotel']=$this->getRealEntities($hotel);
        }
        return new Response(json_encode($result));

    }
    public function getRealEntities($hotel){
        foreach ($hotel as $hotel){
            $realEntities[$hotel->getIdHotel()] = [$hotel->getNom()];
        }
        return $realEntities;
    }
}
