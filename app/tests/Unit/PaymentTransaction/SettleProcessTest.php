<?php

namespace App\Tests\Unit\PaymentTransaction;

use App\Entity\CardBalance;
use App\Entity\CreditCardTransactionParameters;
use App\Entity\PaymentTransaction;
use App\Money\MoneyFactory;
use App\PaymentTransaction\PaymentTransactionStatus;
use App\PaymentTransaction\SettleProcess;
use App\Repository\CardBalanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Money\Currency;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

class SettleProcessTest extends KernelTestCase
{
    private const CARD_BALANCE = 100;

    private SettleProcess $settleProcess;
    private PaymentTransaction $transaction;
    private MoneyFactory $moneyFactory;
    private LockInterface $lock;
    private CardBalanceRepository $cardBalanceRepository;
    private CardBalance $cardBalance;

    public function __construct()
    {
        parent::__construct();
    }

    protected function setUp(): void
    {
        $this->cardBalanceRepository = $this->createMock(CardBalanceRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->lockFactory = $this->createMock(LockFactory::class);
        $this->lock = $this->createMock(LockInterface::class);
        $this->lockFactory->method('createLock')->willReturn($this->lock);
        self::bootKernel();
        $container = static::getContainer();
        $this->moneyFactory = $container->get(MoneyFactory::class);

        $this->cardBalance = new CardBalance();
        $this->cardBalance->setBalance($this->moneyFactory->create(self::CARD_BALANCE, new Currency('USD')));
        $this->cardBalanceRepository->method('findOneBy')->willReturn($this->cardBalance);

        $this->settleProcess = new SettleProcess(
            $this->cardBalanceRepository,
            new MoneyFactory(),
            new NullLogger(),
            $this->entityManager,
            $this->lockFactory
        );

        $params = new CreditCardTransactionParameters();
        $params->setCardNumber('4245208063498857');
        $this->transaction = new PaymentTransaction();
        $this->transaction->setCreditCardTransactionParameters($params);
        $params->setTransaction($this->transaction);
    }

    public function testSettle(): void
    {
        $this->lock->method('acquire')->willReturn(true);
        $this->transaction->setCost($this->moneyFactory->create(self::CARD_BALANCE, new Currency('USD')));
        $this->settleProcess->settle($this->transaction);

        self::assertEquals(PaymentTransactionStatus::SETTLED, $this->transaction->getStatus());
        self::assertEquals(0, $this->cardBalance->getBalance()->getAmount());
    }

    /**
     * Test that transaction fails if card has not enough balance
     */
    public function testSettleNotEnoughBalance(): void
    {
        $initialCardBalance = clone $this->cardBalance->getBalance();
        $this->lock->method('acquire')->willReturn(true);
        $this->transaction->setCost($this->moneyFactory->create(self::CARD_BALANCE + 1, new Currency('USD')));
        $this->settleProcess->settle($this->transaction);

        self::assertEquals(PaymentTransactionStatus::FAILED, $this->transaction->getStatus());
        self::assertTrue($initialCardBalance->equals($this->cardBalance->getBalance()));
    }
}
