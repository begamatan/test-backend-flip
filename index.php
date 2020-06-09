<?php
require_once "./autoload.php";

use App\Service\Flip;

echo "Hello! What do you want me to do?" . PHP_EOL;
echo "1) Send disbursement" . PHP_EOL;
echo "2) Get disbursement status" . PHP_EOL;
echo "Please choose the task by typing the number (1/2)" . PHP_EOL;
$handle = fopen("php://stdin", "r");
$task = fgets($handle);
switch (trim($task)) {
    case '1':
        echo "Please input bank code" . PHP_EOL;
        $bank_code = trim(fgets($handle));
        echo "Please input bank account number" . PHP_EOL;
        $bank_account = trim(fgets($handle));
        echo "Please input amount to disburse" . PHP_EOL;
        $amount = intval(trim(fgets($handle)));
        echo "Please input remark" . PHP_EOL;
        $remark = trim(fgets($handle));
        echo "Sending disbursement request ..." . PHP_EOL;
        $curl = curl_init();

        $flip = new Flip;
        $response = $flip->disburse([
            'bank_code' => $bank_code,
            'account_number' => $bank_account,
            'remark' => $remark,
            'amount' => $amount
        ]);
        $data = json_decode($response);
        echo "Disbursement request is sent. Your transaction id is {$data->id}." . PHP_EOL;
        break;

    case '2':
        echo "Please input your transaction id" . PHP_EOL;
        $transaction_id = readline();
        echo "Please wait while we're getting your disbursement status..." . PHP_EOL;
        $flip = new Flip;
        $response = json_decode($flip->getStatus($transaction_id));
        if ($response->status === 'SUCCESS') {
            echo "Your disbursement request is successfully processed. Here's the receipt for transaction." . PHP_EOL;
            echo "{$response->receipt}" . PHP_EOL;
        } else {
            echo "Your disbursement status is {$response->status}" . PHP_EOL;
        }
        break;

    default:
        abort();
}

echo "Thank you for using our service" . PHP_EOL;

function abort() {
    echo "Whoops, we have trouble." . PHP_EOL;
    echo "Aborting...." . PHP_EOL;
    exit;
}
