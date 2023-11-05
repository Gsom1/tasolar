<?php

namespace App\Entity;

use App\PaymentTransaction\CardType;
use App\PaymentTransaction\CardTypeType;
use App\Repository\CreditCardTransactionParametersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CreditCardTransactionParametersRepository::class)]
class CreditCardTransactionParameters
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 16)]
    private ?string $card_number = null;

    #[ORM\Column(length: 8)]
    private ?string $expiry = null;

    #[ORM\Column(length: 64)]
    private ?string $name = null;

    #[ORM\Column(type: CardTypeType::NAME)]
    private ?CardType $type = null;

    #[ORM\OneToOne(inversedBy: 'creditCardTransactionParameters', cascade: ['persist', 'remove'])]
    private ?PaymentTransaction $transaction = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getExpiry(): ?string
    {
        return $this->expiry;
    }

    public function setExpiry(string $expiry): static
    {
        $this->expiry = $expiry;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?CardType
    {
        return $this->type;
    }

    public function setType(CardType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getTransaction(): ?PaymentTransaction
    {
        return $this->transaction;
    }

    public function setTransaction(?PaymentTransaction $transaction): static
    {
        $this->transaction = $transaction;

        return $this;
    }
}
