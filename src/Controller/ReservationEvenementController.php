<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Hotel;
use App\Entity\Reponse;
use App\Entity\Reservation;
use App\Entity\ReservationEvenement;
use App\Entity\User;
use App\Form\ReservationEvenementType;
use App\Form\ReservationType;
use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/reservationEvenement")
 */
class ReservationEvenementController extends AbstractController
{
    /**
     * @Route("/reservationevenement/index", name="reservationevenement_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $rec = $this->getDoctrine()->getRepository(ReservationEvenement::class)->findAll();

        $pagination = $paginator->paginate(
            $rec,
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
        return $this->render('reservation_evenement/index.html.twig', [
            "reservations" => $pagination,
        ]);

    }

    /**
     * @Route("/reservationevenement/new/{idEvent}", name="reservationevenement_new", methods={"GET","POST"})
     */
    public function new(AuthenticationUtils $util,Request $request,$idEvent): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $reservation = new ReservationEvenement();
        $form = $this->createForm(ReservationEvenementType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notifier = NotifierFactory::create();
            $notification =
                (new Notification())
                    ->setTitle('reservation added ')
                    ->setBody('number of places is down by one');
            $event=$entityManager->getRepository(Evenement::class)
                ->findOneBy(['id_event' => $idEvent]);
            $notifier->send($notification);

            if($event->getNbplace()==0)
            { return $this->render('reservation_evenement/new.html.twig', [
                'reservation' => $reservation,
                'form' => $form->createView(),
            ]);}

            else{
            $event->setNbplace( $event->getNbplace()-1);
            $entityManager->persist($event);
            $yo=$entityManager->getRepository(User::class)
                ->findOneBy(['email' => $util->getLastUsername()]);
            $reservation->setIdUser($yo);
            $event = $entityManager->getRepository(Evenement::class)->find($idEvent);

            $reservation->setEvenement($event);
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('list_reservEventFront');
        }}

        return $this->render('reservation_evenement/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reservation_evenement/MesReservation", name="list_reservEventUser", methods={"GET","POST","DELETE"})
     */
    public function listReservationUser(Request $request,AuthenticationUtils $util, PaginatorInterface $paginator)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $yo=$entityManager->getRepository(User::class)
            ->findOneBy(['email' => $util->getLastUsername()]);

        $rec = $this->getDoctrine()->getRepository(ReservationEvenement::class)->findBy(['idUser' => $yo]);
        $pagination = $paginator->paginate(
            $rec,
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
        return $this->render('reservation_evenement/MesReservation.html.twig', [
            "reservations" => $pagination,
        ]);
    }
    /**
     * @Route("/reservation_evenement/listreservationEvent/{idEvent}", name="list_reservEventEvent")
     */
    public function listReservationEvent(Request $request,int $idEvent, PaginatorInterface $paginator)
    {

        $reserv = $this->getDoctrine()->getRepository(ReservationEvenement::class)->findBy(['Evenement' => $idEvent]);
        $pagination = $paginator->paginate(
            $reserv,
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
        return $this->render('reservation_evenement/index.html.twig', [
            "reservations" => $pagination,
        ]);
    }

    /**
     * @Route("/reservationevenement/{id}/edit", name="reservationevenement_edit", methods={"GET","POST"})
     */
    public function edit(AuthenticationUtils $util,Request $request, Reservation $reservation): Response
    {        $entityManager = $this->getDoctrine()->getManager();

        $form = $this->createForm(ReservationEvenementType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('list_eventFront');
        }

        return $this->render('reservationevenement/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reservationevenement/delete/{id}", name="reservationevenement_delete")
     */
    public function delete(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reser = $entityManager->getRepository(ReservationEvenement::class)->findOneById($id);
            $entityManager->remove($reser);
            $entityManager->flush();


        return $this->redirectToRoute('list_reservEventFront');
    }
    /**
     * @Route("/reservation_evenement/deleteF/{id}", name="reservationevenement_deleteF")
     */
    public function deleteF($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reser = $entityManager->getRepository(ReservationEvenement::class)->findOneById($id);
        $entityManager->remove($reser);
        $entityManager->flush();

        return $this->redirectToRoute('list_reservEventUser');
    }
    /**
     * @Route("/reservation_evenement/pdf/{idres}", name="eventpdf")
     */
    public function pdf(AuthenticationUtils $util,int $idres,Request $request, PaginatorInterface $paginator): Response
    {

        $reservation = $this->getDoctrine()->getRepository(ReservationEvenement::class)->findOneBy(['id' => $idres]);


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
      <h3>Nom:   " . $reservation->getNom() . "</h3>
     
        <h3>Prenom:   " . $reservation->getPrenom() . "</h3>
        <h3>Mail:   " . $reservation->getMail() . "</h3>
        <h3>Telephone:   " . $reservation->getTelephone() . "</h3>
          <h3>Nom event:   " . $reservation->getEvenement()->getNomevent(). "</h3>
          <h3>Lieu:   " . $reservation->getEvenement()->getLieu(). "</h3>
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

        $rec = $this->getDoctrine()->getRepository(ReservationEvenement::class)->findBy(['idUser' => $yo]);
        $pagination = $paginator->paginate(
            $rec,
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );
        return $this->render('reservation_evenement/MesReservation.html.twig', [
            "reservations" => $pagination,
        ]);
    }
}
