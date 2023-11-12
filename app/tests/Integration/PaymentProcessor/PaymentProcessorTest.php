<?php

namespace App\Tests\Integration\PaymentProcessor;

use App\Dto\NewPaymentDto;
use App\Entity\PaymentTransaction;
use App\PaymentProcessor\PaymentProcessor;
use App\Repository\PaymentTransactionRepository;
use App\Tests\Integration\IntegrationTestCase;

class PaymentProcessorTest extends IntegrationTestCase
{
    private const CARD = '5481832318764704';

    private PaymentProcessor $processor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->processor = $this->container->get(PaymentProcessor::class);
    }

    public function testProcess(): void
    {
        $this->processor->process(
            new NewPaymentDto(
                merchantId: '1',
                cardNumber: self::CARD,
                expiryDate: '11/25',
                cvv       : 123,
                amount    : $this->moneyFactory->create(100, 'USD')
            )
        );

        /** @var PaymentTransactionRepository $transactionRepository */
        $transactionRepository = $this->em->getRepository(PaymentTransaction::class);
        $transactions = $transactionRepository->findAll();
        $transaction = reset($transactions);

        self::assertEquals(1, count($transactions));
        self::assertEquals(self::CARD, $transaction->getCreditCardTransactionParameters()->getCardNumber());
        self::assertTrue($transaction->getCost()->equals($this->moneyFactory->create(100, 'USD')));
    }
}
