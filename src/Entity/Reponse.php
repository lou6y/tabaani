<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReponseRepository::class)
 */
class Reponse
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
    private $DateRep;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Description;

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
     * @var \Reclamation
     *
     * @ORM\OneToOne(targetEntity="Reclamation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idRec", referencedColumnName="id")
     * })
     */
    private $idRec;

    /**
     * @return \Reclamation
     */
    public function getIdRec(): ?Reclamation
    {
        return $this->idRec;
    }

    /**
     * @param \Reclamation $idRec
     */
    public function setIdRec(?Reclamation $idRec): void
    {
        $this->idRec = $idRec;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateRep(): ?\DateTimeInterface
    {
        return $this->DateRep;
    }

    public function setDateRep(\DateTimeInterface $DateRep): self
    {
        $this->DateRep = $DateRep;

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
}
