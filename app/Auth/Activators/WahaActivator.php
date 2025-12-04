<?php

namespace App\Auth\Activators;

use Myth\Auth\Authentication\Activators\ActivatorInterface;
use Myth\Auth\Authentication\Activators\BaseActivator;
use Myth\Auth\Entities\User;

class WahaActivator extends BaseActivator implements ActivatorInterface
{
    public function send(?User $user = null): bool
    {
        if (!$user || !isset($user->activate_hash)) {
            $this->error = 'User atau token aktivasi tidak valid';
            return false;
        }

        $phone = (string) ($user->username ?? '');
        $phone = preg_replace('/[^0-9\+]/', '', $phone);
        if ($phone === '') {
            $this->error = 'Nomor HP tidak tersedia';
            return false;
        }

        $link = url_to('activate-account') . '?token=' . $user->activate_hash;
        $message = 'Aktivasi akun Anda: ' . $link;

        $settings = $this->getActivatorSettings();
        $apiURL = $settings->apiURL ?? env('WAHA_BASE_URL');
        $apiToken = $settings->apiToken ?? env('WAHA_TOKEN');
        if (!$apiURL) {
            $this->error = 'WAHA API URL belum dikonfigurasi';
            return false;
        }

        try {
            $skip = (bool) (env('WAHA_SKIP_SSL_VERIFY') ?? false);
            $headers = [
                'Content-Type' => 'application/json',
            ];
            if ($apiToken) {
                $headers['Authorization'] = 'Bearer ' . $apiToken;
                $headers['x-api-key'] = $apiToken;
            }
            $opts = [
                'baseURI' => $apiURL,
                'headers' => $headers,
                'http_errors' => false,
            ];
            if ($skip) {
                $opts['curl'] = [CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => 0];
            }
            $client = service('curlrequest', $opts);
            $payload = [
                'to' => $phone,
                'message' => $message,
            ];
            $resp = $client->post('/messages', ['json' => $payload]);
            $code = $resp->getStatusCode();
            if ($code >= 200 && $code < 300) {
                return true;
            }
            $this->error = 'Gagal mengirim WA (' . $code . ')';
            return false;
        } catch (\Throwable $e) {
            $this->error = 'Error WA: ' . $e->getMessage();
            return false;
        }
    }
}
