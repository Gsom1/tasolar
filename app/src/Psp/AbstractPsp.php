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
            'card_number' => $transaction->getCreditCardTransactionParameters()->getCardNumber(),
        ]);

        $approved = false;
        if ($response->getContent() === 'Approved') {
            $approved = true;
        }

        return new PspResponse($approved);
    }
}
