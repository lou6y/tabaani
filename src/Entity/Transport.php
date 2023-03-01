<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Transport
 *
 * @ORM\Table(name="transport")
 * @ORM\Entity
 */
class Transport
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(name="type_vehicule", type="string", length=50, nullable=false)
     */
    private $typeVehicule;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $image;
    /**
     * @ORM\Column(type="float", length=50, nullable=false)
     */
    private $prix24h;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="idtransport", orphanRemoval=true)
     */
    private $reservations;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $model;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeVehicule(): ?string
    {
        return $this->typeVehicule;
    }

    public function setTypeVehicule(string $typeVehicule): self
    {
        $this->typeVehicule = $typeVehicule;

        return $this;
    }




    /**
     * @return Collection|Reservation[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setIdtransport($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getIdtransport() === $this) {
                $reservation->setIdtransport(null);
            }
        }

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }
    public function getPrix24h(): ?float
    {
        return $this->prix24h;
    }

    public function setPrix24h(?float $prix24h): self
    {
        $this->prix24h = $prix24h;
        return $this;
    }

    public function __toString():string
    {  if($this->getImage()){
        return $this->getTypeVehicule()." ".$this->getModel()."</br> prix /24h =  ".$this->getPrix24h().'</br> <img src="/vehicule/'. $this->getImage() .'" style="width: 200px; height: 150px;"  />';
    }
        return $this->getTypeVehicule()." ".$this->getModel()."</br> prix /24h =  ".$this->getPrix24h();
    }

}
