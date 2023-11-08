<?php

namespace App\Controller\Api\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * This just emulates external payment provider
 * so no validation, just last card number digit check
 */
class PspController extends AbstractController
{
    #[Route('/psp1/payments', methods: ['POST'])]
    public function psp1Payments(Request $request)
    {
        return $this->payments($request);
    }

    #[Route('/psp2/payments', methods: ['POST'])]
    public function psp2Payments(Request $request)
    {
        return $this->payments($request);
    }

    #[Route('/psp3/payments', methods: ['POST'])]
    public function psp3Payments(Request $request)
    {
        return $this->payments($request);
    }

    private function payments(Request $request)
    {
        $data = $request->getContent();
        $data = json_decode($data, true);
        $lastDigit = substr($data['cardNumber'], -1);

        $message = $lastDigit % 2 === 0 ? 'Approved' : 'Declined';

        return new JsonResponse($message);
    }
}
