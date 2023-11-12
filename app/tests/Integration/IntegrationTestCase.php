<?php

namespace App\Tests\Integration;

use App\Money\MoneyFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class IntegrationTestCase extends KernelTestCase
{
    protected ContainerInterface     $container;
    protected EntityManagerInterface $em;
    protected MoneyFactory           $moneyFactory;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->container = static::getContainer();
        $this->em = $this->container->get(EntityManagerInterface::class);
        $this->moneyFactory = $this->container->get(MoneyFactory::class);
    }
}
