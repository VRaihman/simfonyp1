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
    public $dateAdd;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    public $dateUpdate;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    public $idDay;

    /**
     * @ORM\Column(type="string", length=10)
     */
    public $idProd;

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
        return $this->dateAdd;
    }

    public function setDateadd(?string $dateadd): self
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    public function getdateUpdate(): ?string
    {
        return $this->dateUpdate;
    }

    public function setdateUpdate(?string $dateUpdate): self
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    public function getidDay(): ?string
    {
        return $this->idDay;
    }

    public function setidDay(?string $idDay): self
    {
        $this->idDay = $idDay;

        return $this;
    }

    public function getidProd(): ?string
    {
        return $this->idProd;
    }

    public function setidProd(string $idProd): self
    {
        $this->idProd = $idProd;

        return $this;
    }
}
