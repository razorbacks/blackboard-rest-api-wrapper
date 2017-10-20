<?php

namespace razorbacks\blackboard\rest;

use Zttp\Zttp;

class Api
{
    protected $baseUrl = '/learn/api/public/v1';
    protected $token;

    public function __construct(string $hostname, string $applicationId, string $secret)
    {
        $this->baseUrl = rtrim($hostname, '/').$this->baseUrl;

        $this->setToken($applicationId, $secret);
    }

    public function setToken(string $applicationId, string $secret)
    {
        $url = $this->baseUrl.'/oauth2/token?grant_type=client_credentials';

        $response = Zttp::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])
        ->withBasicAuth($applicationId, $secret)
        ->post($url);

        if (empty($token = $response->json()['access_token'] ?? null)) {
            throw new \Exception('No access token:'.PHP_EOL.$response->body());
        }

        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }
}
