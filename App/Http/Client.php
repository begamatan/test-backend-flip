<?php

namespace App\Http;

class Client
{
    /**
     * Request GET with CURL.
     *
     * @param  string  $url  Url to request
     * @param  array  $payload  Payload for request
     * @param  array  $headers  Header for request
     * @return string
     */
    public function get(string $url, array $payload = [], array $headers = []): string
    {
        return $this->curl('GET', $url, $payload, $headers);
    }

    /**
     * Request POST with CURL.
     *
     * @param  string  $url  Url to request
     * @param  array  $payload  Payload for request
     * @param  array  $headers  Header for request
     * @return string
     */
    public function post(string $url, array $payload = [], array $headers = []): string
    {
        return $this->curl('POST', $url, $payload, $headers);
    }

    /**
     * Request with CURL.
     *
     * @param  string  $method  Request method
     * @param  string  $url  Url to request
     * @param  array  $payload  Payload for request
     * @param  array  $headers  Header for request
     * @return string
     */
    protected function curl(string $method, string $url, array $payload, array $headers): string
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

    /**
     * Get header for request.
     *
     * @param  string  $method  Request method
     * @param  array  $headers  Header for request
     * @return array
     */
    protected function getHeader(string $method, array $headers): array
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
