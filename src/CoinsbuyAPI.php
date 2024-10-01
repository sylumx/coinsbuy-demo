<?php

class CoinsbuyAPI
{
    private $baseUrl;
    private $apiKey;
    private $apiSecret;
    private $accessToken;

    public function __construct($isSandbox = false)
    {
        $config = require __DIR__ . '/../config.php';
        $this->baseUrl = $isSandbox ? $config['sandbox_url'] : $config['production_url'];
        $this->apiKey = $isSandbox ? $config['sandbox_api_key'] : $config['production_api_key'];
        $this->apiSecret = $isSandbox ? $config['sandbox_api_secret'] : $config['production_api_secret'];
        $this->accessToken = null;
    }

    public function obtainToken()
    {
        $endpoint = $this->baseUrl . 'oauth/token';
        $data = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->apiKey,
            'client_secret' => $this->apiSecret
        ];

        $response = $this->makeRequest('POST', $endpoint, $data, false);
        if (isset($response['access_token'])) {
            $this->accessToken = $response['access_token'];
            return true;
        }
        return false;
    }

    public function createDeposit($data)
    {
        $endpoint = $this->baseUrl . 'deposit/';
        return $this->makeRequest('POST', $endpoint, $data);
    }

    public function createPayout($data)
    {
        $endpoint = $this->baseUrl . 'payout/';
        $idempotencyKey = $this->generateUUID4();
        return $this->makeRequest('POST', $endpoint, $data, true, $idempotencyKey);
    }

    public function getPayout($id)
    {
        $endpoint = $this->baseUrl . "payout/$id";
        return $this->makeRequest('GET', $endpoint);
    }

    public function precalculateFee($data)
    {
        $endpoint = $this->baseUrl . 'payout/calculate/';
        return $this->makeRequest('POST', $endpoint, $data);
    }

    public function handleCallback($rawData)
    {
        $callbackData = json_decode($rawData, true);
        if ($this->verifyCallbackSignature($rawData)) {
            return $callbackData;
        } else {
            return ['error' => 'Invalid signature'];
        }
    }

    private function verifyCallbackSignature($callbackData)
    {
        $signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
        $computedSignature = hash_hmac('sha256', $callbackData, $this->apiSecret);
        return hash_equals($signature, $computedSignature);
    }

    private function makeRequest($method, $url, $data = [], $authorized = true, $idempotencyKey = null)
    {
        $headers = [
            'Content-Type: application/vnd.api+json',
            'Accept: application/vnd.api+json'
        ];

        if ($authorized) {
            if (!$this->accessToken) {
                $this->obtainToken();
            }
            $headers[] = 'Authorization: Bearer ' . $this->accessToken;
        }

        if ($idempotencyKey) {
            $headers[] = 'idempotency-key: ' . $idempotencyKey;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    private function generateUUID4()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}