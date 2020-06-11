<?php

namespace App\Console;

use App\Service\Flip;

class Disburse extends Command
{
    /**
     * List of question to be prompted.
     *
     * @var array
     */
    protected array $question = [
        'bank_code' => 'Please input bank code',
        'account_number' => 'Please input bank account number',
        'amount' => 'Please input amount to disburse',
        'remark' => 'Please input remark'
    ];

    /**
     * Send disbursement request and save to database.
     *
     * @return void
     */
    public function process(): void
    {
        $this->prompt('Sending disbursement request ...');

        $flip = new Flip;
        $response = json_decode(
            $flip->disburse(
                $this->input
            )
        );
        $this->prompt(
            'Disbursement request is sent. Your transaction id is %s. You can use it to check your transaction status later.',
            [
                $response->id
            ]
        );
    }
}
