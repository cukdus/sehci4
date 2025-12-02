<?php

namespace App\Libraries;

class WahaClient
{
    private string $baseUrl;
    private string $token;
    private ?string $session;
    private \CodeIgniter\HTTP\CURLRequest $client;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) (env('WAHA_BASE_URL') ?? ''), '/');
        $this->token = (string) (env('WAHA_TOKEN') ?? '');
        $this->session = env('WAHA_SESSION') ?? null;
        $this->client = \Config\Services::curlrequest();
    }

    public function sendText(string $phone, string $text): array
    {
        $url = $this->baseUrl . '/api/sendText';
        $headers = [
            'Content-Type' => 'application/json',
        ];
        if ($this->token !== '') {
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }
        $payload = [
            'phone' => $phone,
            'text' => $text,
        ];
        if ($this->session) {
            $payload['session'] = $this->session;
        }
        try {
            $response = $this->client->post($url, [
                'headers' => $headers,
                'json' => $payload,
                'http_errors' => false,
                'timeout' => 10,
            ]);
            return [
                'ok' => $response->getStatusCode() >= 200 && $response->getStatusCode() < 300,
                'status' => $response->getStatusCode(),
                'body' => (string) $response->getBody(),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'status' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }
}

