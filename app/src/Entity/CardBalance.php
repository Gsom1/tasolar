<?php

namespace App\Entity;

use App\Repository\CardBalanceRepository;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

#[ORM\Entity(repositoryClass: CardBalanceRepository::class)]
class CardBalance
{
    public const FIELD_CARD_NUMBER = 'card_number';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 16, unique: true)]
    private ?string $card_number = null;

    #[ORM\Column]
    private ?int $balance = null;

    #[ORM\Column(length: 8)]
    private ?string $currency = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCardNumber(): ?string
    {
        return $this->card_number;
    }

    public function setCardNumber(string $card_number): static
    {
        $this->card_number = $card_number;

        return $this;
    }

    public function setBalance(Money $balance): static
    {
        $this->balance = $balance->getAmount();
        $this->currency = $balance->getCurrency()->getCode();

        return $this;
    }

    public function getBalance(): Money
    {
        return new Money($this->balance, new Currency($this->currency));
    }
}
