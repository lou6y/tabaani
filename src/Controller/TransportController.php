<?php

namespace App\Controller;

use App\Entity\Transport;
use App\Form\TransportType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @Route("/transport")
 */
class TransportController extends AbstractController
{
    /**
     * @Route("/", name="transport", methods={"GET"})
     */
    public function index(): Response
    {

        $transports = $this->getDoctrine()->getRepository(Transport::class)->findAll();

        return $this->render('transport/index.html.twig', [
            'transports' => $transports,
            'page_title'=>"Transport"
        ]);
    }

    /**
     * @Route("/new", name="transport_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {

        $transport = new Transport();
        $form = $this->createForm(TransportType::class, $transport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['image']->getData();
            if($uploadedFile){
            $destination = $this->getParameter('kernel.project_dir').'/public/vehicule';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();
             $uploadedFile->move(
                $destination,
                $newFilename
            );
                $transport->setImage($newFilename);

            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($transport);
            $entityManager->flush();
            return $this->redirectToRoute('transport');
        }

        return $this->render('transport/new.html.twig', [
            'transport' => $transport,
            'form' => $form->createView(),
            'page_title'=>"New Transport"
        ]);
    }

    /**
     * @Route("/{idTransport}", name="transport_show", methods={"GET"})
     */
    public function show(Transport $transport): Response
    {
        return $this->render('transport/show.html.twig', [
            'transport' => $transport,
        ]);
    }

    /**
     * @Route("/edit/{idTransport}", name="transport_edit", methods={"GET","POST"})
     */
    public function edit(Request $request,int $idTransport): Response
    {

        $transport=$this->getDoctrine()->getRepository(Transport::class)->find($idTransport);
        $image=$transport->getImage();
        $form = $this->createForm(TransportType::class, $transport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['image']->getData();
            if($uploadedFile){
                $destination = $this->getParameter('kernel.project_dir').'/public/vehicule';
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();
                $uploadedFile->move(
                    $destination,
                    $newFilename
                );
                $transport->setImage($newFilename);

            }else{
                $transport->setImage($image);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('transport');
        }

        return $this->render('transport/edit.html.twig', [
            'transport' => $transport,
            'form' => $form->createView(),
            'page_title'=>'edit transport'
        ]);
    }

    /**
     * @Route("/del/{idTransport}", name="transport_delete", methods={"GET"})
     */
    public function delete(Request $request, int $idTransport): Response
    {

       $transport=$this->getDoctrine()->getRepository(Transport::class)->find($idTransport);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($transport);
            $entityManager->flush();


        return $this->redirectToRoute('transport');
    }
}
