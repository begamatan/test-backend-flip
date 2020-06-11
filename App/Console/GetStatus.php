<?php

namespace App\Console;

use App\Model\Disbursement;
use App\Service\Flip;

class GetStatus extends Command
{
    const SUCCESS = 'SUCCESS';

    /**
     * List of question to be prompted.
     *
     * @var array
     */
    protected array $question = [
        'transaction_id' => 'Please input your transaction id'
    ];

    /**
     * Get transaction status.
     *
     * @return void
     */
    public function process(): void
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

    /**
     * Get transaction status from API.
     *
     * @return void
     */
    protected function getStatus(): void
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

    /**
     * Display receive and success message.
     *
     * @param  string  $receipt  Link to download receipt
     * @return void
     */
    protected function success(string $receipt): void
    {
        $this->prompt(
            'Your disbursement request is successfully processed. Here\'s the receipt for transaction.'
        );
        $this->prompt('%s', [
            $receipt
        ]);
    }
}
