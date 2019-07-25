<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $regularPrice;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $reducePrice;

    /**
     * @ORM\Column(type="float")
     */
    private $quantity;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $quantityPromotion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRegularPrice(): ?int
    {
        return $this->regularPrice;
    }

    public function setRegularPrice(int $regularPrice): self
    {
        $this->regularPrice = $regularPrice;

        return $this;
    }

    public function getReducePrice(): ?int
    {
        return $this->reducePrice;
    }

    public function setReducePrice(?int $reducePrice): self
    {
        $this->reducePrice = $reducePrice;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuantityPromotion(): ?int
    {
        return $this->quantityPromotion;
    }

    public function setQuantityPromotion(?int $quantityPromotion): self
    {
        $this->quantityPromotion = $quantityPromotion;

        return $this;
    }
}
