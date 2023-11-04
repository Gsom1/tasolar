<?php

namespace App\PaymentProcessor;

use App\Dto\NewPaymentDto;
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
    )
    {
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

        $this->em->persist($paymentTransaction);
        $this->em->flush();
    }
}
