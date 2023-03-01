<?php

namespace App\Entity;

use App\Repository\ReservationEvenementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReservationEvenementRepository::class)
 */
class ReservationEvenement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail;
    /**
     * @ORM\Column(type="integer")
     */
    private $telephone;

    /**
     * ReservationEvenement constructor.
     * @param \Evenement $Evenement
     */

    public function __construct()
    {   }

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param mixed $telephone
     */


    public function setTelephone($telephone): void
    {
        $this->telephone = $telephone;
    }

    /**
     * @return \User
     */
    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    /**
     * @param \User $idUser
     */
    public function setIdUser(?User $idUser): void
    {
        $this->idUser = $idUser;
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
     * @return \Evenement
     */
    public function getEvenement(): ?Evenement
    {
        return $this->Evenement;
    }

    /**
     * @param \Evenement $Evenement
     */
    public function setEvenement(?Evenement $Evenement): void
    {
        $this->Evenement = $Evenement;
    }

    /**
     * @var \Evenement
     *
     * @ORM\ManyToOne(targetEntity="Evenement")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="idEvenement", referencedColumnName="id_event")
     * })
     */
    private $Evenement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }
}
