<?php

namespace App\Model;

class Disbursement extends Model
{
    protected $table = 'disbursement';

    public function findByTransactionId($transaction_id)
    {
        return $this->find($transaction_id, 'transaction_id');
    }
}
