<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    /**
     * @Route("/Categorie/add_categorie", name="add_categorie")
  */
    public function addCategorie(Request $request): Response
    {
        $Categorie=new Categorie();
        $form = $this->createForm(CategorieType::class,$Categorie);
        $form->handleRequest($request);


        if($form->isSubmitted()&& $form->isValid())
        {

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($Categorie);

            $entityManager->flush();

            return $this->redirectToRoute('list_categorie');

        }
        return $this->render('Categorie/addcategorie.html.twig', [
            'controller_name' => 'CategorieController',
            "form_title" => "Ajouter une Categorie",
            "form" => $form->createView(),
        ]);
    }
    /**
     * @Route("/Categorie/list_categorie", name="list_categorie")
     */
    public function listCategorie(Request $request)
    {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->findAll();

        return $this->render('Categorie/showcategorie.html.twig', [
            'categorie'=>$categorie,
        ]);
    }

    /**
     * @Route("/Categorie/delete_categorie/{idCategorie}", name="delete_categorie")
     */
    public function deleteCategorie(int $idCategorie): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categorie = $entityManager->getRepository(Categorie::class)->find($idCategorie);
        $entityManager->remove($categorie);
        $entityManager->flush();
        return $this->redirectToRoute('list_categorie');
    }
    /**
     * @Route("/Categorie/edit_categorie/{idCategorie}", name="edit_categorie")
     */
    public function editCategorie(Request $request, int $idCategorie): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        $categorie = $entityManager->getRepository(Categorie::class)->find($idCategorie);
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            return $this->redirectToRoute('list_categorie');
        }

        return $this->render("Categorie/editcategorie.html.twig", [
            "form_title" => "Modifier une categorie",
            "form" => $form->createView(),
        ]);
    }
}
