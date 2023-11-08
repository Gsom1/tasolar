<?php

namespace App\Controller\Api\V1;

use App\Dto\NewPaymentDto;
use App\Money\MoneyFactory;
use App\PaymentProcessor\PaymentProcessor;
use App\Psp\PspResponse;
use App\UserRequests\NewPaymentRequest;
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
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface  $validator,
    ) {
    }

    #[Route('/payments', name: 'new_payment', methods: ['POST'])]
    public function index(
        Request          $request,
        PaymentProcessor $processor,
        MoneyFactory     $moneyFactory,
    ): JsonResponse {
        $data = $request->getContent();

        /** @var NewPaymentRequest $paymentRequest */
        $paymentRequest = $this->validate($data, NewPaymentRequest::class);
        if ($paymentRequest instanceof JsonResponse) {
            return $paymentRequest;
        }

        $pspResponse = $processor->process(
            new NewPaymentDto(
                merchantId: $paymentRequest->merchantId,
                cardNumber: $paymentRequest->cardNumber,
                expiryDate: $paymentRequest->expiryDate,
                cvv       : $paymentRequest->cvv,
                amount    : $moneyFactory->create($paymentRequest->amount, $paymentRequest->currency),
            )
        );

        return $this->responseFactory($pspResponse);
    }

    private function validate($data, string $dtoClass)
    {
        $paymentRequest = $this->serializer->deserialize($data, $dtoClass, 'json');
        $errors = $this->validator->validate($paymentRequest);

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

        return $paymentRequest;
    }

    private function responseFactory(PspResponse $pspResponse): JsonResponse
    {
        if ($pspResponse->isApproved()) {
            return new JsonResponse('Approved', Response::HTTP_OK);
        }

        return new JsonResponse('Denied', Response::HTTP_BAD_REQUEST);
    }
}
