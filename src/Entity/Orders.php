<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 */
class Orders
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
    private $paymentMethod;

    /**
     * @ORM\Column(type="integer")
     */
    private $ammount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addressOrder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addressComplementOrder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cityOrder;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     */
    private $zipCodeOrder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $countryOrder;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $statut;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Client", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $orderApiId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getAmmount(): ?int
    {
        return $this->ammount;
    }

    public function setAmmount(int $ammount): self
    {
        $this->ammount = $ammount;

        return $this;
    }

    public function getAddressOrder(): ?string
    {
        return $this->addressOrder;
    }

    public function setAddressOrder(?string $addressOrder): self
    {
        $this->addressOrder = $addressOrder;

        return $this;
    }

    public function getAddressComplementOrder(): ?string
    {
        return $this->addressComplementOrder;
    }

    public function setAddressComplementOrder(?string $addressComplementOrder): self
    {
        $this->addressComplementOrder = $addressComplementOrder;

        return $this;
    }

    public function getCityOrder(): ?string
    {
        return $this->cityOrder;
    }

    public function setCityOrder(?string $cityOrder): self
    {
        $this->cityOrder = $cityOrder;

        return $this;
    }

    public function getZipCodeOrder(): ?int
    {
        return $this->zipCodeOrder;
    }

    public function setZipCodeOrder(?int $zipCodeOrder): self
    {
        $this->zipCodeOrder = $zipCodeOrder;

        return $this;
    }

    public function getCountryOrder(): ?string
    {
        return $this->countryOrder;
    }

    public function setCountryOrder(?string $countryOrder): self
    {
        $this->countryOrder = $countryOrder;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getOrderApiId(): ?int
    {
        return $this->orderApiId;
    }

    public function setOrderApiId(?int $orderApiId): self
    {
        $this->orderApiId = $orderApiId;

        return $this;
    }
}
