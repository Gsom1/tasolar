<?php

namespace App\Entity;

use App\PaymentTransaction\PaymentTransactionStatus;
use App\PaymentTransaction\TransactionStatusType;
use App\Repository\PaymentTransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PaymentTransactionRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class PaymentTransaction
{
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id;

    #[ORM\Column(type: "bigint")]
    private ?int $amount = null;

    #[ORM\Column(length: 8)]
    private ?string $currency = null;

    #[ORM\Column(type: TransactionStatusType::NAME)]
    private ?PaymentTransactionStatus $status = null;

    #[ORM\Column(length: 255)]
    private ?string $merchant_id = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function setCost(Money $cost): static
    {
        $this->amount = $cost->getAmount();
        $this->currency = $cost->getCurrency()->getCode();

        return $this;
    }

    public function getCost(): Money
    {
        return new Money($this->amount, new Currency($this->currency));
    }

//    public function getAmount(): ?string
//    {
//        return $this->amount;
//    }
//
//    public function setAmount(string $amount): static
//    {
//        $this->amount = $amount;
//
//        return $this;
//    }
//
//    public function getCurrency(): ?string
//    {
//        return $this->currency;
//    }
//
//    public function setCurrency(string $currency): static
//    {
//        $this->currency = $currency;
//
//        return $this;
//    }

    public function getStatus(): ?PaymentTransactionStatus
    {
        return $this->status;
    }

    public function setStatus(PaymentTransactionStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getMerchantId(): ?string
    {
        return $this->merchant_id;
    }

    public function setMerchantId(string $merchant_id): static
    {
        $this->merchant_id = $merchant_id;

        return $this;
    }
}
