<?php

namespace App\Controller\Api\V1;

use App\Dto\NewPaymentDto;
use App\Money\MoneyFactory;
use App\UserRequests\NewPaymentRequest;
use App\PaymentProcessor\PaymentProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1', name: 'api')]
class PaymentController extends AbstractController
{
    #[Route('/payments', name: 'new_payment', methods: ['POST'])]
    public function index(
        Request             $request,
        SerializerInterface $serializer,
        ValidatorInterface  $validator,
        PaymentProcessor    $processor,
        MoneyFactory        $moneyFactory,
    ): JsonResponse {
        $data = $request->getContent();
        /** @var NewPaymentRequest $paymentRequest */
        $paymentRequest = $serializer->deserialize($data, NewPaymentRequest::class, 'json');
        $errors = $validator->validate($paymentRequest);

        if (count($errors) > 0) {
            $errorResponses = [];
            foreach ($errors as $error) {
                $errorResponses[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(
                $errorResponses,
                Response::HTTP_BAD_REQUEST,
            );
        }

        $processor->process(
            new NewPaymentDto(
                merchantId: $paymentRequest->merchantId,
                cardNumber: $paymentRequest->cardNumber,
                expiryDate: $paymentRequest->expiryDate,
                cvv       : $paymentRequest->cvv,
                amount    : $moneyFactory->create($paymentRequest->amount, $paymentRequest->currency),
            )
        );

        return new JsonResponse('ok', Response::HTTP_OK);
    }
}
