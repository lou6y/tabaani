<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Reservationhotel
 *
 * @ORM\Table(name="reservationhotel")
 * @ORM\Entity
 */
class Reservationhotel
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_reserv", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idReserv;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_nuit", type="integer", nullable=false)
     */
    private $nbNuit;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_chambre", type="integer", nullable=false)
     */
    private $nbChambre;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_personne", type="integer", nullable=false)
     */
    private $nbPersonne;

    /**
     * @Assert\GreaterThan("today")
     * @ORM\Column(type="date")
     */
    private $dateReservation;

    /**
     * Reservationhotel constructor.
     */
    public function __construct()
    {
        $this->dateReservation= new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getDateReservation()
    {

        return $this->dateReservation;
    }

    /**
     * @param mixed $dateReservation
     */
    public function setDateReservation($dateReservation): void
    {
        $this->dateReservation = $dateReservation;
        $day   = $dateReservation->format('d'); // Format the current date, take the current day (01 to 31)
        $month = $dateReservation->format('m'); // Same with the month
        $year  = $dateReservation->format('Y'); // Same with the year

        $date = $day.'-'.$month.'-'.$year; // Return a string and not an object

    }


    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     * })
     */
    private $idUser;

    /**
     * @var \Hotel
     *
     * @ORM\ManyToOne(targetEntity="Hotel")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_hotel", referencedColumnName="id_hotel")
     * })
     */
    private $idHotel;

    public function getIdReserv(): ?int
    {
        return $this->idReserv;
    }

    public function getNbNuit(): ?int
    {
        return $this->nbNuit;
    }

    public function setNbNuit(int $nbNuit): self
    {
        $this->nbNuit = $nbNuit;

        return $this;
    }

    public function getNbChambre(): ?int
    {
        return $this->nbChambre;
    }

    public function setNbChambre(int $nbChambre): self
    {
        $this->nbChambre = $nbChambre;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getNbPersonne(): ?int
    {
        return $this->nbPersonne;
    }

    public function setNbPersonne(int $nbPersonne): self
    {
        $this->nbPersonne = $nbPersonne;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getIdHotel(): ?Hotel
    {
        return $this->idHotel;
    }

    public function setIdHotel(?Hotel $idHotel): self
    {
        $this->idHotel = $idHotel;

        return $this;
    }


}
