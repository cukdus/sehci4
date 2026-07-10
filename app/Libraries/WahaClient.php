<?php

namespace App\Libraries;

class WahaClient
{
    private string $baseUrl;
    private string $sendUrl;
    private string $token;
    private string $session;
    private \CodeIgniter\HTTP\CURLRequest $client;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) (env('WAHA_BASE_URL') ?? ''), '/');
        $this->sendUrl = trim((string) (env('WAHA_SEND_URL') ?? ''));
        $this->token = (string) (env('WAHA_TOKEN') ?? '');
        $this->session = trim((string) (env('WAHA_SESSION') ?? 'default')) ?: 'default';
        $skip = (bool) (env('WAHA_SKIP_SSL_VERIFY') ?? false);
        $opts = [
            'timeout' => 10,
            'http_errors' => false,
            'allow_redirects' => ['max' => 5, 'strict' => false, 'referer' => false],
        ];
        if ($skip) {
            $opts['verify'] = false;
        }
        $this->client = \Config\Services::curlrequest($opts);
    }

    public function sendText(string $phone, string $text): array
    {
        $phone = preg_replace('/\D+/', '', $phone) ?? '';
        if ($phone === '') {
            return [
                'ok' => false,
                'status' => 0,
                'error' => 'Nomor telepon kosong atau tidak valid.',
            ];
        }

        $targets = $this->buildTargets();
        if ($targets === []) {
            return [
                'ok' => false,
                'status' => 0,
                'error' => 'WAHA_BASE_URL atau WAHA_SEND_URL belum dikonfigurasi.',
            ];
        }

        $headers = [
            'Content-Type' => 'application/json',
        ];
        if ($this->token !== '') {
            $headers['Authorization'] = 'Bearer ' . $this->token;
            $headers['x-api-key'] = $this->token;
        }

        $chatId = $phone . '@c.us';
        $attempts = [];

        try {
            foreach ($targets as $url) {
                $variants = [
                    ['headers' => $headers, 'json' => ['phone' => $phone, 'chatId' => $chatId, 'text' => $text, 'session' => $this->session]],
                    ['headers' => $headers, 'json' => ['to' => $phone, 'chatId' => $chatId, 'text' => $text, 'session' => $this->session]],
                    ['headers' => $headers, 'json' => ['phone' => $phone, 'chatId' => $chatId, 'message' => $text, 'session' => $this->session]],
                    ['headers' => ['Content-Type' => 'application/x-www-form-urlencoded'] + $headers, 'form_params' => ['phone' => $phone, 'chatId' => $chatId, 'message' => $text, 'session' => $this->session]],
                ];
                if (str_ends_with($url, '/messages')) {
                    $variants[] = ['headers' => $headers, 'json' => ['chatId' => $chatId, 'body' => $text, 'session' => $this->session]];
                }

                foreach ($variants as $variant) {
                    $response = $this->client->post($url, $variant);
                    $status = $response->getStatusCode();
                    $body = (string) $response->getBody();
                    if ($status >= 200 && $status < 300) {
                        log_message('info', 'WAHA send success: status={status} url={url}', [
                            'status' => $status,
                            'url' => $url,
                        ]);
                        return [
                            'ok' => true,
                            'status' => $status,
                            'body' => $body,
                        ];
                    }

                    $attempts[] = 'status=' . $status . ' url=' . $url . ' body=' . $body;
                }
            }

            return [
                'ok' => false,
                'status' => 0,
                'error' => $attempts !== [] ? implode(' || ', $attempts) : 'Gagal mengirim pesan ke WAHA.',
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'status' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function buildTargets(): array
    {
        $targets = [];
        if ($this->sendUrl !== '') {
            $targets[] = $this->sendUrl;
        }
        if ($this->baseUrl !== '') {
            $targets[] = $this->baseUrl . '/api/sendText';
            $targets[] = $this->baseUrl . '/messages';
        }

        return array_values(array_unique(array_filter($targets)));
    }
}
