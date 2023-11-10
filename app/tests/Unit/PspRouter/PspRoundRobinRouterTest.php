<?php

namespace App\Tests\Unit\PspRouter;

use App\Entity\PaymentTransaction;
use App\Psp\Psp1;
use App\PspRouter\PspRoundRobinRouter;
use PHPUnit\Framework\TestCase;

class PspRoundRobinRouterTest extends TestCase
{
    private PspRoundRobinRouter $router;

    protected function setUp(): void
    {
        parent::setUp();

        $this->router = new PspRoundRobinRouter();
    }

    /**
     * Test that every provider gets called once in a loop
     */
    public function testRoute(): void
    {
        $psp1 = $this->createMock(Psp1::class);
        $psp1->expects($this->once())->method('payment');
        $psp2 = $this->createMock(Psp1::class);
        $psp2->expects($this->once())->method('payment');
        $psp3 = $this->createMock(Psp1::class);
        $psp3->expects($this->once())->method('payment');

        $this->router->setPsp([$psp1, $psp2, $psp3,]);

        $this->router->route(new PaymentTransaction());
        $this->router->route(new PaymentTransaction());
        $this->router->route(new PaymentTransaction());
    }
}
