<?php

namespace App\Model;

class Disbursement extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected string $table = 'disbursement';

    /**
     * Find data by transaction id.
     *
     * @param  mixed  $transaction_id  Transaction id
     * @return array
     */
    public function findByTransactionId($transaction_id): array
    {
        return $this->find($transaction_id, 'transaction_id');
    }
}
