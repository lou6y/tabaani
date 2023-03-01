<?php

namespace App\Controller;


use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Entity\Reservationhotel;
use App\Entity\User;
use App\Form\ReclamationType;
use App\Form\ReponseType;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ReponseController extends AbstractController
{
    /**
     * @Route("/reponse/add_reponse/{idRec}", name="add_reponse")
     */
    public function addReponse(Request $request, AuthenticationUtils $util,$idRec): Response
    {
        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);


        $notifier = NotifierFactory::create();
        $notification =
            (new Notification())
                ->setTitle('Reponse added ')
                ->setBody('Reponse added ');
        $notifier->send($notification);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $recla = $entityManager->getRepository(Reclamation::class)->find($idRec);
            $yo=$entityManager->getRepository(User::class)
                ->findOneBy(['email' => $util->getLastUsername()]);

            $user= $entityManager->getRepository(User::class)->find($yo);
            $reponse->setIdRec($recla);
            $reponse->setIdUser($user);
            $recla->setStatut(1);
            $reponse->setDateRep(new \DateTime('now'));


            $entityManager->persist($reponse);
            $entityManager->persist($recla);
            $entityManager->flush();

            return $this->redirectToRoute('list_reclamationback');

        }

        return $this->render("reponse/addreponse.html.twig", [
            "form_title" => "Ajouter une reponse",
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route("/reponse/listreponse", name="list_reponse")
     */
    public function listReponse(Request $request, PaginatorInterface $paginator)
    {
        $rec = $this->getDoctrine()->getRepository(Reponse::class)->findAll();

        $pagination = $paginator->paginate(
            $rec,
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
        return $this->render('reponse/listreponse.html.twig', [
            "reponses" => $pagination,
        ]);
    }

    /**
     * @Route("/reponse/delete_reponse/{idRep}", name="delete_reponse")
     */
    public function deleteReponse(int $idRep): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $rec = $entityManager->getRepository(Reponse::class)->find($idRep);
        $entityManager->remove($rec);
        $entityManager->flush();
        return $this->redirectToRoute('list_reponse');
    }

    /**
     * @Route("/reponse/show_reponse/{idrec}", name="show_reponse")
     */
    public function showreponse(int $idrec): Response
    {
        $reponse = $this->getDoctrine()->getRepository(Reponse::class)->findOneBy(['idRec' => $idrec]);

        return $this->render("reponse/showreponse.html.twig", [
            "rep" => $reponse,
        ]);
    }

    /**
     * @Route("/reponse/show_reponsefront/{idrec}", name="show_reponsefront")
     */
    public function showreponsefront(int $idrec): Response
    {

        $reponse = $this->getDoctrine()->getRepository(Reponse::class)->findOneBy(['idRec' => $idrec]);
        return $this->render("reponse/showreponsefront.html.twig", [
            "rep" => $reponse,
        ]);
    }

    /**
     * @Route("/reponse/pdf/{idrec}", name="pdf")
     */
    public function pdf(int $idrec): Response
    {

        $reponse = $this->getDoctrine()->getRepository(Reponse::class)->findOneBy(['idRec' => $idrec]);


        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = "<style>
.clearfix:after {
  content: \"\";
  display: table;
  clear: both;
}
a {
  color: #001028;
  text-decoration: none;
}
body {
  font-family: Junge;
  position: relative;
  width: 21cm;  
  height: 29.7cm; 
  margin: 0 auto; 
  color: #001028;
  background: #FFFFFF; 
  font-size: 14px; 
}
.arrow {
  margin-bottom: 4px;
}
.arrow.back {
  text-align: right;
}
.inner-arrow {
  padding-right: 10px;
  height: 30px;
  display: inline-block;
  background-color: rgb(233, 125, 49);
  text-align: center;
  line-height: 30px;
  vertical-align: middle;
}
.arrow.back .inner-arrow {
  background-color: rgb(233, 217, 49);
  padding-right: 0;
  padding-left: 10px;
}
.arrow:before,
.arrow:after {
  content:'';
  display: inline-block;
  width: 0; height: 0;
  border: 15px solid transparent;
  vertical-align: middle;
}
.arrow:before {
  border-top-color: rgb(233, 125, 49);
  border-bottom-color: rgb(233, 125, 49);
  border-right-color: rgb(233, 125, 49);
}
.arrow.back:before {
  border-top-color: transparent;
  border-bottom-color: transparent;
  border-right-color: rgb(233, 217, 49);
  border-left-color: transparent;
}
.arrow:after {
  border-left-color: rgb(233, 125, 49);
}
.arrow.back:after {
  border-left-color: rgb(233, 217, 49);
  border-top-color: rgb(233, 217, 49);
  border-bottom-color: rgb(233, 217, 49);
  border-right-color: transparent;
}
.arrow span { 
  display: inline-block;
  width: 80px; 
  margin-right: 20px;
  text-align: right; 
}
.arrow.back span { 
  margin-right: 0;
  margin-left: 20px;
  text-align: left; 
}
h1 {
  color: #5D6975;
  font-family: Junge;
  font-size: 2.4em;
  line-height: 1.4em;
  font-weight: normal;
  text-align: center;
  border-top: 1px solid #5D6975;
  border-bottom: 1px solid #5D6975;
  margin: 0 0 2em 0;
}
h1 small { 
  font-size: 0.45em;
  line-height: 1.5em;
  float: left;
} 
h1 small:last-child { 
  float: right;
} 
#project { 
  float: left; 
}
#company { 
  float: right; 
}
table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
  margin-bottom: 30px;
}
table th,
table td {
  text-align: center;
}
table th {
  padding: 5px 20px;
  color: #5D6975;
  border-bottom: 1px solid #C1CED9;
  white-space: nowrap;        
  font-weight: normal;
}
table .service,
table .desc {
  text-align: left;
}
table td {
  padding: 20px;
  text-align: right;
}
table td.service,
table td.desc {
  vertical-align: top;
}
table td.unit,
table td.qty,
table td.total {
  font-size: 1.2em;
}
table td.sub {
  border-top: 1px solid #C1CED9;
}
table td.grand {
  border-top: 1px solid #5D6975;
}
table tr:nth-child(2n-1) td {
  background: #EEEEEE;
}
table tr:last-child td {
  background: #DDDDDD;
}
#details {
  margin-bottom: 30px;
}
footer {
  color: #5D6975;
  width: 100%;
  height: 30px;
  position: absolute;
  bottom: 0;
  border-top: 1px solid #C1CED9;
  padding: 8px 0;
  text-align: center;
}</style>
<h1>Reponse Reclamation</h1>
 <!DOCTYPE html>
<html lang=\"en\">
  <head>
    <meta charset=\"utf-8\">
    <title>Example 3</title>
    <link rel=\"stylesheet\" href=\"style.css\" media=\"all\" />
  </head>
  <body>
    <main>
      <h1  class=\"clearfix\"><small><span>DATE</span><br />" . $reponse->getDateRep()->format("d/M/yy") . "
      <table>
        <thead>
          <tr>
            <th class=\"desc\">DESCRIPTION</th>
          </tr>
        </thead><tbody>
        <tr><td  class=\"service\">" . $reponse->getDescription() . "</td></tr>
 </tbody>

      </table>
       </br></br></br></br></br>
      <div id=\"details\" class=\"clearfix\">
        <div id=\"project\">
          <div class=\"arrow\"><div class=\"inner-arrow\"><span>Company</span>Tabaani</div></div>
          <div class=\"arrow\"><div class=\"inner-arrow\"><span>(216)50443853</span>Phone</div></div>
          </div>
        <div id=\"company\">
          <div class=\"arrow back\"><div class=\"inner-arrow\">Adress<span>ArianaSoghra</span></div></div>
          <div class=\"arrow back\"><div class=\"inner-arrow\">Email<span>tabaani@esprit.com</span></div></div>
           </div>
      </div>
  
     
    </main>
    <footer>
    tabaani
    </footer>
  </body>
</html>";

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("reponseReclaamtion.pdf", [
            "Attachment" => true
        ]);


        return $this->render("reponse/showreponsefront.html.twig", [
            "rep" => $reponse,
        ]);
    }

  }
