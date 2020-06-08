<?php

namespace App\Http;

class Client
{
    public function get($url, $payload = [], $headers = [])
    {
        return $this->curl('GET', $url, $payload, $headers);
    }

    public function post($url, $payload = [], $headers = [])
    {
        return $this->curl('POST', $url, $payload, $headers);
    }

    protected function curl($method, $url, $payload, $headers)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $this->getHeader($method, $headers),
        ]);

        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payload));
        }

        if ($method === 'GET' && !empty($payload)) {
            $url .= '?' . http_build_query($payload);
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    protected function getHeader($method, $headers)
    {
        $curl_headers = [];
        foreach ($headers as $header => $value) {
            $curl_headers[] = "{$header}: {$value}";
        }
        if ($method === 'POST') {
            $curl_headers[] = 'Content-Type: application/x-www-form-urlencoded';
        }
        return $curl_headers;
    }
}
