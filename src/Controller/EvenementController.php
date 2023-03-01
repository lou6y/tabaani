<?php

namespace App\Controller;

use App\Entity\Evenement;

use App\Entity\Reclamation;
use App\Form\EvenementType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EvenementController extends AbstractController
{
    /**
     * @Route("/evenement/listevenementback", name="list_evenementback")
     */
    public function listEvenementback(Request $request, PaginatorInterface $paginator)
    {
        $rec = $this->getDoctrine()->getRepository(Evenement::class)->findAll();

        $pagination = $paginator->paginate(
            $rec,
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
        return $this->render('evenement/index.html.twig', [
            "recs" => $pagination,
        ]);
    }
    /**
     * @Route("/evenement/listevenementFront", name="list_evenementtFront")
     */
    public function listEvenementFront(Request $request, PaginatorInterface $paginator)
    {
        $rec = $this->getDoctrine()->getRepository(Evenement::class)->findAll();

        $pagination = $paginator->paginate(
            $rec,
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
        return $this->render('evenement/indexFront.html.twig', [
            "event" => $pagination,
        ]);
    }
    /**
     * @Route("/evenement/new", name="evenement_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('list_evenementback');
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/evenement/{id}", name="evenement_show", methods={"GET"})
     */
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    /**
     * @Route("/evenement/{id}/edit", name="evenement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Evenement $evenement): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('list_evenementback');
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/evenement/{id}", name="evenement_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Evenement $evenement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('list_evenementback');
    }

    /**
     * @Route("/evenement/listeventFront", name="list_Eventfront")
     */
    public function listEventfront(Request $request, PaginatorInterface $paginator)
    {
        $rec = $this->getDoctrine()->getRepository(Evenement::class)->findAll();

        $pagination = $paginator->paginate(
            $rec,
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );

        return $this->render('evenement/frontshowevent.html.twig', [
            'event' => $pagination,
        ]);
    }
}
