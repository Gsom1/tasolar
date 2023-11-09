<?php

namespace App\PaymentProcessor;

use App\Dto\NewPaymentDto;
use App\Entity\CreditCardTransactionParameters;
use App\Entity\PaymentTransaction;
use App\Message\NewPaymentTransactionMessage;
use App\PaymentTransaction\CardType;
use App\PaymentTransaction\PaymentTransactionStatus;
use App\Psp\PspResponse;
use App\PspRouter\PspResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

class PaymentProcessor
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PspResolver            $pspResolver,
        private readonly MessageBusInterface    $bus,
    ) {
    }

    public function process(NewPaymentDto $dto): PspResponse
    {
        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction->setId(Uuid::v4());
        $paymentTransaction->setStatus(PaymentTransactionStatus::CREATED);
        $paymentTransaction->setMerchantId($dto->merchantId);
        $paymentTransaction->setCost($dto->amount);

        $ccParams = new CreditCardTransactionParameters();
        $ccParams->setCardNumber($dto->cardNumber);
        $ccParams->setName('noname');
        $ccParams->setExpiry($dto->expiryDate);
        $ccParams->setTransaction($paymentTransaction);
        $ccParams->setType($this->getCardType($dto->cardNumber));
        $paymentTransaction->setCreditCardTransactionParameters($ccParams);

        $this->em->persist($paymentTransaction);
        $this->em->persist($ccParams);

        $router = $this->pspResolver->getRouter($paymentTransaction);
        $pspResponse = $router->route($paymentTransaction);
        if ($pspResponse->isApproved()) {
            $paymentTransaction->setStatus(PaymentTransactionStatus::APPROVED);
        } else {
            $paymentTransaction->setStatus(PaymentTransactionStatus::DECLINED);
        }

        $this->em->persist($paymentTransaction);
        $this->em->flush();

        $this->bus->dispatch(new NewPaymentTransactionMessage($paymentTransaction->getId()));

        return $pspResponse;
    }

    public function getCardType(string $cardNumber): CardType
    {
        $type = CardType::UNKNOWN;
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $cardNumber)) {
            $type = CardType::VISA;
        }

        if (preg_match('/^5[1-5][0-9]{14}$/', $cardNumber) || preg_match('/^2[2-7][0-9]{14}$/', $cardNumber)) {
            $type = CardType::MASTER_CARD;
        }

        return $type;
    }
}
