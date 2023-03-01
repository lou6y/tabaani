<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Reservationhotel;
use App\Entity\User;
use App\Form\ReservationType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/reservation")
 */
class ReservationController extends AbstractController
{
    /**
     * @Route("/", name="reservation", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator,AuthenticationUtils $util): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $yo=$entityManager->getRepository(User::class)
            ->findOneBy(['email' => $util->getLastUsername()]);


            $reservations = $this->getDoctrine()->getRepository(Reservation::class)->findAll();

            $reservations = $paginator->paginate(
                $reservations,
                $request->query->getInt('page', 1),
                3
            );
            return $this->render('reservation/backindex.html.twig', [
                'reservations' => $reservations,
                'page_title'=>"Reservations"
            ]);
        }



    /**
     * @Route("/List", name="listRF", methods={"GET"})
     */
    public function index1(Request $request, PaginatorInterface $paginator,AuthenticationUtils $util): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $yo=$entityManager->getRepository(User::class)
            ->findOneBy(['email' => $util->getLastUsername()]);

            $reservations = $this->getDoctrine()->getRepository(Reservation::class)->findBy(['idUser' => $yo]);
            return $this->render('reservation/index.html.twig', [
                'reservations' => $reservations,
                'page_title'=>"Reservations"
            ]);
    }







    /**
     * @Route("/payement/{idReservation}", name="payment")
     */
    public function paymentAction(Request $request , int  $idReservation )
    {

        $form=$em=$this->getDoctrine()->getManager()->getRepository(reservation::class)->find($idReservation);



        $amount=$form->getPrix_total();


        if ($request->isMethod("GET")) {

            \Stripe\Stripe::setApiKey("sk_test_51IZfKWCn5Kv58XkaE1GSmt7CG0VwdlMqfg3nmMwIjm0gt9AUjjqFABs3w5BpRoisf42JhrJWiHtrpqrahTbAkVfo00CquDZDoo");


            //dump($amount);
            // Token is created using Checkout or Elements!
            // Get the payment token ID submitted by the form:
            $charge = \Stripe\Charge::create([
                'amount' => $amount,
                'currency' => 'usd',
                'description' => 'Example charge',
                'source' => 'tok_visa',
            ]);


            return $this->render('reservation/payment.html.twig', array('id'=>$idReservation) );
        }
        return $this->redirectToRoute('listRF') ;


    }
    /**
     * @Route("/listpdf", name="reservation_list", methods={"GET"})
     */
    public function listpdf(Request $request,AuthenticationUtils $util): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $yo=$entityManager->getRepository(User::class)
            ->findOneBy(['email' => $util->getLastUsername()]);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $reservations = $this->getDoctrine()->getRepository(Reservation::class)->findBy(["idUser"=>$yo]);



        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('reservation/listpdf.html.twig', [
            'reservations' => $reservations,

        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]);


        $user = $this->getUser();
        if ($this->getUser()->getRoles() == ['Client']) {
            $reservations = $this->getDoctrine()->getRepository(Reservation::class)->findBy(["idUser" => $yo]);
            return $this->render('reservation/index.html.twig', [
                'reservations' => $reservations,
                'page_title' => "Reservations"
            ]);
        }
    }


    /**
     * @Route("/new", name="reservation_new", methods={"GET","POST"})
     */
    public function new(Request $request,AuthenticationUtils $util): Response
    {

        $reservation = new Reservation();
        $reservation->setDateDebut(new \DateTime('@'.strtotime('now')));
        $time= new \DateTime('@'.strtotime('now'));
        $interval=new \DateInterval('P'.'1'.'D');
        $reservation->setDateFin($time->add($interval));

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $reservation->setEtat(false);
            $days=$reservation->getDateFin()->diff($reservation->getDateDebut())->days;
            $reservation->setPrix_total($reservation->getIdtransport()->getPrix24h()*$days);

            $yo=$entityManager->getRepository(User::class)
                ->findOneBy(['email' => $util->getLastUsername()]);
            $reservation->setIdUser($yo);


            $entityManager->persist($reservation);
            $entityManager->flush();

            //$request->getSession()->getFlashBag()->add();
            $this->addFlash(
                'info',
                'reservation ajoutée avec succèe'
            );
            return $this->redirectToRoute('listRF');
        }


            return $this->render('reservation/new.html.twig', [
                'page_title'=>"new reservation",
                'form' => $form->createView()
            ]);


    }


    /**
     * @Route("/edit/{idReservation}", name="reservation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PaginatorInterface $paginator, Reservation $idReservation): Response
    {
        $reservation=$this->getDoctrine()->getRepository(Reservation::class)->find($idReservation);
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $days=$reservation->getDateFin()->diff($reservation->getDateDebut())->days;
            $reservation->setPrix_total($reservation->getIdtransport()->getPrix24h()*$days);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reservation');
        }
        $reservations = $this->getDoctrine()->getRepository(Reservation::class)->findAll();

        $reservations = $paginator->paginate(
            $reservations,
            $request->query->getInt('page', 1),
            3
        );
                return $this->render('reservation/backedit.html.twig', [
                    'reservation' => $reservation,
                    'form' => $form->createView(),
                    'page_title'=>"edit reservation "
                ]);
        }





    /**
     * @Route("/editF/{idReservation}", name="reservation_editF", methods={"GET","POST"})
     */
    public function editF(Request $request, Reservation $idReservation): Response
    {
        $reservation=$this->getDoctrine()->getRepository(Reservation::class)->find($idReservation);
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $days=$reservation->getDateFin()->diff($reservation->getDateDebut())->days;
            $reservation->setPrix_total($reservation->getIdtransport()->getPrix24h()*$days);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('listRF');
        }
        $reservations = $this->getDoctrine()->getRepository(Reservation::class)->findAll();

            return $this->render('reservation/edit.html.twig', [
                'reservations' => $reservations,
                'form' => $form->createView(),
                'page_title'=>"edit reservation ",
            ]);
        }




    /**
     * @Route("/del/{idReservation}", name="reservation_delete", methods={"GET"})
     */
    public function delete( int $idReservation): Response
    {
          $reservation=$this->getDoctrine()->getRepository(Reservation::class)->find($idReservation);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reservation);
            $entityManager->flush();

        $this->addFlash(
            'info',
            'reservation supprimée'
        );
        return $this->redirectToRoute('reservation');
    }




    /**
     * @Route("/delF/{idReservation}", name="reservation_deleteF", methods={"GET"})
     */
    public function deleteF( int $idReservation): Response
    {
        $reservation=$this->getDoctrine()->getRepository(Reservation::class)->find($idReservation);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($reservation);
        $entityManager->flush();

        $this->addFlash(
            'info',
            'reservation supprimée'
        );
        return $this->redirectToRoute('listRF');
    }













    /**
     * @Route("/confirmer/{idReservation}", name="reservation_confirmer", methods={"GET"})
     */
    public function confirmer( int $idReservation , MailerInterface $mailer): Response
    {

        $reservation= new Reservation();
        $reservation=$this->getDoctrine()->getRepository(Reservation::class)->find($idReservation);
        $reservation->setEtat(true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        $mailuser=$reservation->getIduser()->getEMail();
        $email=(new Email())
            ->from('tabaanii9@gmail.com')
            ->to($mailuser)
            ->subject('confirmation')
            ->text('votre réservation a été confirmée ');
        $mailer->send($email);
        return $this->redirectToRoute('reservation');

    }
}
