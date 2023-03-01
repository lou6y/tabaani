<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reservation
 *
 * @ORM\Table(name="reservation")
 * @ORM\Entity
 */
class Reservation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_reservation", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idReservation;


    /**
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(name="prise", type="string", length=50, nullable=false)
     */
    private $prise;

    /**
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(name="remise", type="string", length=50, nullable=false)
     */
    private $remise;

    /**
     * @var \DateTime
     * @Assert\NotBlank
     * @ORM\Column(name="date_debut", type="date", nullable=false)
     */
    private $dateDebut;

    /**
     * @var \DateTime
     * @Assert\NotBlank
     * @ORM\Column(name="date_fin", type="date", nullable=false)
     */
    private $dateFin;
    /**
     * @var float
     * @ORM\Column(name="prix_total", type="float", nullable=false)
     */
    private $prix_total;
    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     * })
     */
    private $iduser;

    /**
     * @ORM\ManyToOne(targetEntity=Transport::class, inversedBy="reservations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idtransport;

    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="boolean", nullable=false)
     */
    private $etat;


    public function getIdReservation(): ?int
    {
        return $this->idReservation;
    }


    public function getPrise(): ?string
    {
        return $this->prise;
    }

    public function setPrise(string $prise): self
    {
        $this->prise = $prise;

        return $this;
    }

    public function getRemise(): ?string
    {
        return $this->remise;
    }

    public function setRemise(string $remise): self
    {
        $this->remise = $remise;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getIduser(): ?User
    {
        return $this->iduser;
    }

    public function setIduser(?User $iduser): self
    {
        $this->iduser = $iduser;

        return $this;
    }

    public function getIdtransport(): ?Transport
    {
        return $this->idtransport;
    }

    public function setIdtransport(?Transport $idtransport): self
    {
        $this->idtransport = $idtransport;

        return $this;
    }
    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }


    public function getPrix_total(): ?float
    {
        return $this->prix_total;
    }

    public function setPrix_total(float $prix_total): self
    {
        $this->prix_total = $prix_total;

        return $this;
    }

}
