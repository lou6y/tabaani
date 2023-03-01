<?php

namespace App\Entity;

use App\Repository\RestoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Table(name="Resto")
 * @ORM\Entity(repositoryClass=RestoRepository::class)
 * @Vich\Uploadable
 */
class Resto
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $idResto;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive
     */

    private $nbplace;

    /**
     * @return mixed
     */
    public function getNbplace()
    {
        return $this->nbplace;
    }

    /**
     * @param mixed $nbplace
     */
    public function setNbplace($nbplace): void
    {
        $this->nbplace = $nbplace;
    }

    /**
     * @ORM\Column(type="float")
     * @Assert\Positive
     */
    private $budget;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     */

    private $nomresto;

    /**
     * @return mixed
     */
    public function getNomresto()
    {
        return $this->nomresto;
    }

    /**
     * @param mixed $nomresto
     */
    public function setNomresto($nomresto): void
    {
        $this->nomresto = $nomresto;
    }
    /**
     * @var \Categorie
     *
     * @ORM\ManyToOne(targetEntity="Categorie")
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="idCategorie", referencedColumnName="id_categorie")
     * })
     */
    private $specialite;
    /**
     * @var string
     *
     * @ORM\Column(name="restopic", type="string", length=255, nullable=true)
     */
    private $restopic;
    /**
     * @return mixed
     */
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="hotel", fileNameProperty="restopic")
     *
     * @var File|null
     */
    private $imageFile;

    public function getIdResto(): ?int
    {
        return $this->idResto;
    }





    public function setrestopic(?string $restopic): void
    {
        $this->restopic = $restopic;
    }

    public function getrestopic(): ?string
    {
        return $this->restopic;
    }
    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(float $budget): self
    {
        $this->budget = $budget;

        return $this;
    }



    public function getSpecialite(): ?Categorie
    {
        return $this->specialite;
    }

    public function setSpecialite(?Categorie $specialite): self
    {
        $this->specialite = $specialite;

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
