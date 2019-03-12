<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DbCartRepository")
 */
class DbCart
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    public $name;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    public $price;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    public $date;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    public $dateadd;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    public $dateupdate;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    public $idday;

    /**
     * @ORM\Column(type="string", length=10)
     */
    public $idprod;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDateadd(): ?string
    {
        return $this->dateadd;
    }

    public function setDateadd(?string $dateadd): self
    {
        $this->dateadd = $dateadd;

        return $this;
    }

    public function getDateupdate(): ?string
    {
        return $this->dateupdate;
    }

    public function setDateupdate(?string $dateupdate): self
    {
        $this->dateupdate = $dateupdate;

        return $this;
    }

    public function getIdday(): ?string
    {
        return $this->idday;
    }

    public function setIdday(?string $idday): self
    {
        $this->idday = $idday;

        return $this;
    }

    public function getIdprod(): ?string
    {
        return $this->idprod;
    }

    public function setIdprod(string $idprod): self
    {
        $this->idprod = $idprod;

        return $this;
    }
}
