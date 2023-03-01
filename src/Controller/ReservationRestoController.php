<?php

namespace App\Controller;

use App\Entity\ReservationResto;
use App\Entity\Resto;
use App\Entity\User;
use App\Form\ReservationRestoType;
use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReservationRestoController extends AbstractController

{
    /**
     * @Route("/reservationresto/add_reservationResto/{idResto}", name="add_reservationResto")
     */
    public function addReservationResto(Request $request,$idResto ,AuthenticationUtils $util): Response
    {
        $reservationr = new ReservationResto();
        $form = $this->createForm(ReservationRestoType::class, $reservationr);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        $yo=$entityManager->getRepository(User::class)
            ->findOneBy(['email' => $util->getLastUsername()]);

        if ($form->isSubmitted() && $form->isValid()) {
            $notifier = NotifierFactory::create();
            $notification =
                (new Notification())
                    ->setTitle('reservation added ')
                    ->setBody('number of places is down by one');
            $entityManager = $this->getDoctrine()->getManager();
            $resto = $entityManager->getRepository(Resto::class)->find($idResto);
            $notifier->send($notification);
            $reservationr->setIdResto($resto);
            $reservationr->setIdUser($yo);
            $entityManager->persist($reservationr);

           $basic  = new \Nexmo\Client\Credentials\Basic('6c102b9a', 'g5p8lT47PVP8oq7o');
            $client = new \Nexmo\Client($basic);

            $message = $client->message()->send([
                'to' => '21650055953',
                'from' => 'travel',
                'text' => 'Reservation ajoutée'
            ]);

            $entityManager->flush();

            return $this->redirectToRoute('list_reservationresto' );

        }

        return $this->render("reservation_resto/reserverresto.html.twig", [

            "form_title" => "Ajouter une Reservation",
            "form" => $form->createView(),

        ]);
    }
    /**
     * @Route("/reservationresto/listReservationresto", name="list_reservationresto")
     */
    public function listResto(Request $request,AuthenticationUtils $util)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $yo=$entityManager->getRepository(User::class)
            ->findOneBy(['email' => $util->getLastUsername()]);

        $Reservationresto = $this->getDoctrine()->getRepository(ReservationResto::class)
            ->findBy(array('idUser'=> $yo->getId()));



        return $this->render("reservation_resto/detailreservationr.html.twig", [
            "reservationresto" => $Reservationresto,
        ]);
    }
    /**
     * @Route("/reservationresto/delete_ReservationResto/{idResResto}", name="delete_Reservationresto")
     */
    public function deleteReservationResto(int $idResResto): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $resto = $entityManager->getRepository(ReservationResto::class)->find($idResResto);
        $entityManager->remove($resto);
        $entityManager->flush();
        return $this->redirectToRoute('list_reservationresto');
    }
    /**
     * @Route("/reservationresto/edit_ReservationResto/{idResResto}", name="edit_Reservationresto")
     */
    public function editResto(Request $request, int $idResResto): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        $resto = $entityManager->getRepository(ReservationResto::class)->find($idResResto);
        $form = $this->createForm(ReservationRestoType::class, $resto);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            return $this->redirectToRoute('list_reservationresto');
        }

        return $this->render("reservation_resto/editReservationResto.html.twig", [
            "form_title" => "Modifier une Reservation",
            "form" => $form->createView(),
        ]);
    }
    /**
     * @Route("/reservationhback/listReservationrestorb", name="list_reservationrestoback")
     */
    public function reservationlistback(Request $request,AuthenticationUtils $util)
    {
        $Reservationresto = $this->getDoctrine()->getRepository(ReservationResto::class)->findAll();





        return $this->render('reservation_resto/listereservationback.html.twig', [
            "Reservationresto" => $Reservationresto,
        ]);
    }
    /**
     * @Route("/front/resto/pdf/{idResResto}", name="restopdf")
     */
    public function pdf(Request $request,AuthenticationUtils $util,int $idResResto, PaginatorInterface $paginator): Response
    {

        $reservation = $this->getDoctrine()->getRepository(ReservationResto::class)->findOneBy(['idResResto' => $idResResto]);


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
<h1>Reservation</h1>
 <!DOCTYPE html>
<html lang=\"en\">
  <head>
    <meta charset=\"utf-8\">
    <title>Example 3</title>
    <link rel=\"stylesheet\" href=\"style.css\" media=\"all\" />
  </head>
  <body>
    <main>
      <h3>Nom:   " . $reservation->getIdResto()->getNomresto() . "</h3>
     
        <h3>Mail:   " . $reservation->getIdUser()->getEmail() . "</h3>
    
          <h3>Nbre de place:   " . $reservation->getNbpersonne(). "</h3>
          
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
        $dompdf->stream("reservation.pdf", [
            "Attachment" => true
        ]);


        $entityManager = $this->getDoctrine()->getManager();
        $yo=$entityManager->getRepository(User::class)
            ->findOneBy(['email' => $util->getLastUsername()]);

        $rec = $this->getDoctrine()->getRepository(ReservationResto::class)->findBy(['idUser' => $yo]);
        $pagination = $paginator->paginate(
            $rec,
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
        return $this->render('reservation_resto/listereservationback.html.twig', [
            "rec" => $pagination,
        ]);
    }



}
