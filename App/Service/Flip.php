<?php

namespace App\Service;

use App\Http\Client;
use App\Model\Disbursement;

class Flip
{
    /**
     * API secret key.
     *
     * @var string
     */
    private string $token;

    /**
     * API base URL.
     *
     * @var string
     */
    private string $base_url;

    /**
     * HTTP request header.
     *
     * @var array
     */
    private array $headers = [];

    public function __construct()
    {
        $config = app()["config"];
        $this->token = $this->getToken($config["api_secret_key"]);
        $this->headers["Authorization"] = "Basic {$this->token}";
        $this->base_url = $config["api_base_url"];
    }

    /**
     * Get token for basic auth.
     *
     * @param  string  $secret_key
     * @return string
     */
    private function getToken(string $secret_key): string
    {
        return base64_encode("{$secret_key}:");
    }

    /**
     * Send disbursement request.
     *
     * @param  array  $payload Payload for request
     * @return string
     */
    public function disburse(array $payload): string
    {
        $client = new Client;
        $response = $client->post("{$this->base_url}/disburse", $payload, $this->headers);

        $this->saveRecord(json_decode($response));

        return $response;
    }

    /**
     * Save disbursement request to database.
     *
     * @param  object  $response
     * @return void
     */
    private function saveRecord(object $response): void
    {
        $this->triggerEvent('save_record');
        $disbursement = new Disbursement;
        $disbursement->insert([
            'transaction_id' => $response->id,
            'amount' => $response->amount,
            'status' => $response->status,
            'timestamp' => $response->timestamp,
            'bank_code' => $response->bank_code,
            'account_number' => $response->account_number,
            'beneficiary_name' => $response->beneficiary_name,
            'remark' => $response->remark,
            'receipt' => $response->receipt,
            'time_served' => (
                $response->time_served === '0000-00-00 00:00:00' ?
                null :
                $response->time_served
            ),
            'fee' => $response->fee
        ]);
    }

    /**
     * Get status of transaction id.
     *
     * @param  string  $transaction_id
     * @return string
     */
    public function getStatus(string $transaction_id): string
    {
        $client = new Client;
        $response = $client->get("{$this->base_url}/disburse/{$transaction_id}", [], $this->headers);
        $this->setStatus(json_decode($response));
        return $response;
    }

    /**
     * Save disbursement request to database.
     *
     * @param  object  $response
     * @return void
     */
    private function setStatus(object $response): void
    {
        $this->triggerEvent('set_status');
        $disbursement = new Disbursement;
        $disbursement->update([
            'status' => $response->status,
            'time_served' => $response->time_served,
            'receipt' => $response->receipt
        ], [
            'transaction_id' => $response->id
        ]);
    }

    /**
     * Echo message to shell.
     *
     * @param  string  $event_name
     * @return void
     */
    private function triggerEvent(string $event_name): void
    {
        if (php_sapi_name() === 'cli') {
            switch ($event_name) {
                case 'save_record':
                    echo 'Saving response data to database...' . PHP_EOL;
                    break;

                case 'set_status':
                    echo 'Updating transaction status...' . PHP_EOL;

                default:
                    break;
            }
        }
    }
}
