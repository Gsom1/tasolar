<?php

namespace App\PaymentProcessor;

use App\Dto\NewPaymentDto;
use App\Entity\CreditCardTransactionParameters;
use App\Entity\PaymentTransaction;
use App\PaymentProcessor\Exceptions\DeclinedException;
use App\PaymentTransaction\PaymentTransactionStatus;
use App\Services\ApproveService\ApproveService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class PaymentProcessor
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ApproveService         $approveService,
    ) {
    }

    public function process(NewPaymentDto $dto): void
    {
        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction->setId(Uuid::v4());
        $paymentTransaction->setMerchantId($dto->merchantId);
        $paymentTransaction->setCost($dto->amount);

        $this->approveService->approve($dto, $paymentTransaction);
        if ($paymentTransaction->getStatus() === PaymentTransactionStatus::DECLINED) {
            throw new DeclinedException();
        }

        $ccParams = new CreditCardTransactionParameters();
        $ccParams->setCardNumber($dto->cardNumber);
        $ccParams->setName('noname');
        $ccParams->setExpiry($dto->expiryDate);
        $ccParams->setTransaction($paymentTransaction);
        $ccParams->setType($this->getCardType($dto->cardNumber));

        $this->em->persist($paymentTransaction);
        $this->em->persist($ccParams);
        $this->em->flush();
    }

    public function getCardType(string $cardNumber): ?string
    {
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $cardNumber)) {
            return 'VISA';
        }

        if (preg_match('/^5[1-5][0-9]{14}$/', $cardNumber) || preg_match('/^2[2-7][0-9]{14}$/', $cardNumber)) {
            return 'MasterCard';
        }

        return 'Unknown';
    }
}
