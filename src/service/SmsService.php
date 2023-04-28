<?php

namespace App\service;

use Vonage\Client\Credentials\Basic;
use Vonage\Client;
use Vonage\SMS\Message\SMS;

class SmsService
{
    private $client;
    private $from;

    public function __construct(string $apiKey, string $apiSecret, string $from)
    {
        $this->client = new Client(new Basic($apiKey, $apiSecret));
        $this->from = $from;
    }

    public function sendSms(string $to, string $text)
    {
        $response = $this->client->sms()->send(
            new SMS($to, $this->from, $text)
        );

        return $response;
    }
}