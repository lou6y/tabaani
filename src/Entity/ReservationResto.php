<?php

namespace App\Entity;

use App\Repository\ReservationRestoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=ReservationRestoRepository::class)
 */
class ReservationResto
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $idResResto;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive(
     * message="le nb de personnes doit etre positif"
     * )
     */
    private $Nbpersonne;

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
     * @var \Resto
     *
     * @ORM\ManyToOne(targetEntity="Resto")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="idResto", referencedColumnName="id_resto")
     * })
     */
    private $idResto;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(
     *  message = "The email '{{ value }}' is not a valid email."
     * )
     */
    private $mail;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive
     * @Assert\NotBlank
     * @Assert\Length(min=8)































     * message = "le numero '{{ value }}' doit contenir au maximum 8 chiffres."
     * 
     * )
     */
    private $numero;


    public function getIdResResto(): ?int
    {
        return $this->idResResto;
    }



    public function getNbpersonne(): ?int
    {
        return $this->Nbpersonne;
    }

    public function setNbpersonne(int $Nbpersonne): self
    {
        $this->Nbpersonne = $Nbpersonne;

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


    public function getIdResto(): ?Resto
    {
        return $this->idResto;
    }


    public function setIdResto(?Resto $idResto): self
    {
        $this->idResto = $idResto;
        return $this;
    }

   /* public function getIdResto(): ?int
    {
        return $this->idResto;
    }

    public function setIdResto(int $idResto): self
    {
        $this->idResto = $idResto;

    }*/

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

}
