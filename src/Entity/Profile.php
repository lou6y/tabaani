<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfileRepository::class)
 */
class Profile
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
    private $imag;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImag(): ?string
    {
        return $this->imag;
    }

    public function setImag(string $imag): self
    {
        $this->imag = $imag;

        return $this;
    }
}
