<?php

namespace App\Console;

use App\Model\Disbursement;
use App\Service\Flip;

class GetStatus extends Command
{
    const SUCCESS = 'SUCCESS';

    protected $question = [
        'transaction_id' => 'Please input your transaction id'
    ];

    public function process()
    {
        $this->prompt('Please wait while we\'re getting your disbursement status...');

        $disbursement = (new Disbursement)->findByTransactionId($this->input['transaction_id']);

        if (!$disbursement) {
            die($this->prompt('Can\'t find transaction, please try again with correct transaction id'));
        }

        if ($disbursement['status'] === self::SUCCESS) {
            $this->success($disbursement['receipt']);
        } else {
            $this->getStatus();
        }
    }

    protected function getStatus()
    {
        $flip = new Flip;
        $response = json_decode(
            $flip->getStatus($this->input['transaction_id'])
        );
        if ($response->status === self::SUCCESS) {
            $this->success($response->receipt);
        } else {
            $this->prompt('Your disbursement status is %s', [
                $response->status
            ]);
        }
    }

    protected function success($receipt)
    {
        $this->prompt(
            'Your disbursement request is successfully processed. Here\'s the receipt for transaction.'
        );
        $this->prompt('%s', [
            $receipt
        ]);
    }
}
