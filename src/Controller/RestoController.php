<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\ReservationResto;
use App\Entity\Resto;
use App\Form\RestoType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Appointments;

// Include paginator interface

class RestoController extends AbstractController
{
    /**
     * @Route("/Resto/add_resto", name="add_resto")
     */
    public function addResto(Request $request): Response
    {
        $Resto = new Resto();
        $form = $this->createForm(RestoType::class, $Resto);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($Resto);

            $entityManager->flush();

            return $this->redirectToRoute('list_Resto');

        }
        return $this->render('resto/addresto.html.twig', [
            'controller_name' => 'RestoController',
            "form_title" => "Ajouter un Resto",
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/Resto/list_Resto", name="list_Resto")
     */
    public function listResto(Request $request, PaginatorInterface $paginator)
    {
        $resto = $this->getDoctrine()->getRepository(Resto::class)->findAll();
        $pagination = $paginator->paginate(
            $resto,
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
        return $this->render('Resto/showresto.html.twig', [
            "resto" => $pagination,
        ]);


    }

    /**
     * @Route("/Resto/delete_Resto/{idResto}", name="delete_Resto")
     */
    public function deleteResto(int $idResto): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $resto = $entityManager->getRepository(Resto::class)->find($idResto);
        $entityManager->remove($resto);
        $entityManager->flush();
        return $this->redirectToRoute('list_Resto');
    }

    /**
     * @Route("/Resto/edit_Resto/{idResto}", name="edit_Resto")
     */
    public function editResto(Request $request, int $idResto): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        $resto = $entityManager->getRepository(Resto::class)->find($idResto);
        $form = $this->createForm(RestoType::class, $resto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('list_Resto');
        }

        return $this->render("resto/editResto.html.twig", [
            "form_title" => "Modifier un Resto",
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route("/Resto/Sort_Resto/{sort}", name="Sort_Resto")
     */
    public function sortAction($sort, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        if ($sort == 'ASC') {
            $query = $entityManager->createQuery(
                'SELECT r
    FROM App\Entity\Resto r
    ORDER BY r.budget ASC'
            );
        } else {
            $query = $entityManager->createQuery(
                'SELECT r
    FROM App\Entity\Resto r
    ORDER BY r.budget  DESC'
            );
        }
        $resto = $query->getResult();
        return $this->render('Resto/sort.html.twig', [
            'resto' => $resto,
        ]);
    }

    /**
     * @Route("/front/resto/listresto", name="list_Restofront")
     */
    public function listRestofront(Request $request, PaginatorInterface $paginator)
    {
        $resto = $this->getDoctrine()->getRepository(Resto::class)->findAll();
        $pagination = $paginator->paginate(
            $resto,
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );

        return $this->render('Resto/frontshowRestos.html.twig', [
            'resto' => $pagination,
        ]);
    }
}