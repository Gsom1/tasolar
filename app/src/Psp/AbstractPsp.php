<?php

namespace App\Psp;

use App\Entity\PaymentTransaction;
use App\Money\MoneyFormatter;
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

        $params = $transaction->getCreditCardTransactionParameters();

        $response = $this->client->request('POST', static::BASE_URL . '/payments', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body'    => json_encode([
                "cardNumber" => $params->getCardNumber(),
                "amount"     => (float)MoneyFormatter::getAmount($transaction->getCost()),
                "currency"   => $transaction->getCost()->getCurrency()->getCode(),
                "merchantId" => $transaction->getMerchantId(),
            ]),
        ]);

        $approved = false;
        $data = json_decode($response->getContent());
        if ($data === 'Approved') {
            $approved = true;
        }

        return new PspResponse($approved);
    }
}
