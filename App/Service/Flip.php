<?php

namespace App\Service;

use App\Http\Client;
use App\Model\Disbursement;

class Flip
{
    private $token;

    private $base_url;

    private $headers = [];

    public function __construct()
    {
        $config = $this->getConfig();
        $this->token = $this->getToken($config["api_secret_key"]);
        $this->headers["Authorization"] = "Basic {$this->token}";
        $this->base_url = $config['api_base_url'];
    }

    private function getConfig()
    {
        return include(__DIR__ . '/../../config.php');
    }

    private function getToken($secret_key)
    {
        return base64_encode("{$secret_key}:");
    }

    public function disburse($payload)
    {
        $client = new Client;
        $response = $client->post("{$this->base_url}/disburse", $payload, $this->headers);

        $this->saveRecord(json_decode($response));

        return $response;
    }

    private function saveRecord($response)
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
            'time_served' => $response->time_served === '0000-00-00 00:00:00' ? null : $response->time_served,
            'fee' => $response->fee
        ]);
    }

    public function getStatus($transaction_id)
    {
        $client = new Client;
        $response = $client->get("{$this->base_url}/disburse/{$transaction_id}", [], $this->headers);
        $this->setStatus(json_decode($response));
        return $response;
    }

    private function setStatus($response)
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

    private function triggerEvent($event_name)
    {
        if (php_sapi_name() === 'cli') {
            switch ($event_name) {
                case 'save_record':
                    echo "Saving response data to database...\n";
                    break;

                case 'set_status':
                    echo "Updating transaction status...\n";

                default:
                    break;
            }
        }
    }
}
