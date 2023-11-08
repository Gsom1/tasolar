<?php

namespace App\Psp;

use App\Entity\PaymentTransaction;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AbstractPsp implements PaymentProviderInterface
{
    protected const BASE_URL = '';

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly LoggerInterface     $logger
    ) {
    }

    public function payment(PaymentTransaction $transaction): PspResponse
    {
        $this->logger->debug(__METHOD__, [$transaction]);

        $response = $this->client->request('POST', static::BASE_URL . '/payments', [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'body'    => '{
                "cardNumber": "4355068868972142",
                "amount": 123,
                "currency": "RUB",
                "merchantId": "1"
            }',
        ]);

        $approved = false;
        $data = json_decode($response->getContent());
        if ($data === 'Approved') {
            $approved = true;
        }

        return new PspResponse($approved);
    }
}
