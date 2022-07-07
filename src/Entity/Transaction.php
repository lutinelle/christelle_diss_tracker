<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'float')]
    private $qty;

    #[ORM\Column(type: 'float')]
    private $price;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $comment;

    #[ORM\ManyToOne(targetEntity: CryptoCurrency::class, inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    private $currency;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQty(): ?float
    {
        return $this->qty;
    }

    public function setQty(float $qty): self
    {
        $this->qty = $qty;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCurrency(): ?CryptoCurrency
    {
        return $this->currency;
    }

    public function setCurrency(?CryptoCurrency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

}
