<?php

namespace App\Controller\Api\V1;

use App\Dto\NewPaymentDto;
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
    public function index(Request $request, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        $data = $request->getContent();
        $paymentDetails = $serializer->deserialize($data, NewPaymentDto::class, 'json');
        $errors = $validator->validate($paymentDetails);

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

        return new JsonResponse('ok', Response::HTTP_OK);
    }
}
