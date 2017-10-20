<?php

namespace razorbacks\blackboard\rest;

use Zttp\Zttp;
use Exception;

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
            throw new Exception('No access token:'.PHP_EOL.$response->body());
        }

        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function send(string $method, string $endpoint, array $data = [])
    {
        $response = Zttp::withHeaders([
            'Authorization' => "Bearer {$this->getToken()}",
        ])
        ->$method($this->baseUrl.$endpoint, $data);

        if (!$response->isOk()) {
            throw new BadResponse('Response not OK:'.$response->status().PHP_EOL.$response->body());
        }

        return $response->json();
    }

    public function get(string $endpoint, array $data = [])
    {
        return $this->send('GET', $endpoint, $data);
    }

    public function post(string $endpoint, array $data)
    {
        return $this->send('POST', $endpoint, $data);
    }

    public function put(string $endpoint, array $data)
    {
        return $this->send('PUT', $endpoint, $data);
    }

    public function patch(string $endpoint, array $data)
    {
        return $this->send('PATCH', $endpoint, $data);
    }

    public function delete(string $endpoint, array $data = [])
    {
        return $this->send('DELETE', $endpoint, $data);
    }
}

class BadResponse extends Exception {}
