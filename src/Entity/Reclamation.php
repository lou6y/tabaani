<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=ReclamationRepository::class)
 */
class Reclamation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $DateRec;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Objet;
    /**
     * @Assert\NotBlank
     * @Assert\Length(min=4)
     * @Assert\Length(max=16)
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Title;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=8)
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Statut;


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
     * @var int
     *
     * @ORM\Column(name="idReserv", type="integer", nullable=false)
     */
    private $idReserv;

    /**
     * @return int
     */
    public function getIdReserv(): int
    {
        return $this->idReserv;
    }

    /**
     * @param int $idReserv
     */
    public function setIdReserv(int $idReserv): void
    {
        $this->idReserv = $idReserv;
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



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateRec(): ?\DateTimeInterface
    {
        return $this->DateRec;
    }

    public function setDateRec(\DateTimeInterface $DateRec): self
    {
        $this->DateRec = $DateRec;

        return $this;
    }

    public function getObjet(): ?string
    {
        return $this->Objet;
    }

    public function setObjet(string $Objet): self
    {
        $this->Objet = $Objet;

        return $this;
    }
    public function getTitle(): ?string
    {
        return $this->Title;
    }
    public function setTitle(string $Title): self
    {
        $this->Title = $Title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getStatut()
    {
        return $this->Statut;
    }


    public function setStatut($Statut): self
    {
        $this->Statut = $Statut;

        return $this;
    }
}
