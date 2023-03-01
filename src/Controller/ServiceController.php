<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    /**
     * @Route("/service/add_service", name="add_service")
     */
    public function addService(Request $request): Response
    {
        $service = new service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($service);

            $entityManager->flush();

            return $this->redirectToRoute('list_service');

        }

        return $this->render("service/addservice.html.twig", [
            "form_title" => "Ajouter un service",
            "form" => $form->createView(),
        ]);
    }
    /**
     * @Route("/service/listService", name="list_service")
     */
    public function listService(Request $request, PaginatorInterface $paginator)
    {
        $service = $this->getDoctrine()->getRepository(Service::class)->findAll();

        $pagination = $paginator->paginate(
            $service,
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
        return $this->render('service/showservice.html.twig', [
            "service" => $pagination,
        ]);
    }
    /**
     * @Route("/service/delete_service/{idService}", name="delete_service")
     */
    public function deleteservice(int $idService): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $service = $entityManager->getRepository(Service::class)->find($idService);
        $entityManager->remove($service);
        $entityManager->flush();
        return $this->redirectToRoute('list_service');
    }
    /**
     * @Route("/service/edit_service/{idService}", name="edit_service")
     */
    public function editservice(Request $request, $idService): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        $service = $entityManager->getRepository(Service::class)->find($idService);
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            return $this->redirectToRoute('list_service');
        }

        return $this->render("service/editservice.html.twig", [
            "form_title" => "Modifier service",
            "form" => $form->createView(),
        ]);
    }


}