<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=EvenementRepository::class)
 * @Vich\Uploadable
 */
class Evenement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_event",type="integer")
     */
    private $id_event;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbplace;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomevent;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lieu;

    /**
     * @ORM\Column(type="integer")
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;
    /**
     * @ORM\Column(type="string")
     *
     * @var string|null
     */
    private $eventpic;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="hotel", fileNameProperty="eventpic")
     *
     * @var File|null
     */
    private $imageFile;

    /**
     * @return mixed
     */
    public function getIdEvent()
    {
        return $this->id_event;
    }

    /**
     * @param mixed $id_event
     */
    public function setIdEvent($id_event): void
    {
        $this->id_event = $id_event;
    }


    /**
     * @return mixed
     */
    public function getNomevent()
    {
        return $this->nomevent;
    }

    /**
     * @param mixed $nomevent
     */
    public function setNomevent($nomevent): void
    {
        $this->nomevent = $nomevent;
    }




    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id_event;
    }

    public function getNbplace(): ?int
    {
        return $this->nbplace;
    }

    public function setNbplace(int $nbplace): self
    {
        $this->nbplace = $nbplace;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    /**
     * @return string
     */
    public function getEventpic(): ?string
    {
        return $this->eventpic;
    }

    /**
     * @param string $eventpic
     */
    public function setEventpic(?string $eventpic): void
    {
        $this->eventpic = $eventpic;
    }



    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }


    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            //  $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

}
