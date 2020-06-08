<?php
require_once "./autoload.php";

use App\Service\Flip;

echo "Hello! What do you want me to do?\n";
echo "1) Send disbursement\n";
echo "2) Update disbursement status\n";
echo "3) Get current disbursement status\n";
echo "Please choose the task by typing the number (1/2/3)\n";
$handle = fopen("php://stdin", "r");
$task = fgets($handle);
switch (trim($task)) {
    case '1':
        echo "Please input bank code\n";
        $bank_code = trim(fgets($handle));
        echo "Please input bank account number\n";
        $bank_account = trim(fgets($handle));
        echo "Please input amount to disburse\n";
        $amount = intval(trim(fgets($handle)));
        echo "Please input remark\n";
        $remark = trim(fgets($handle));
        echo "Sending disbursement request ...\n";
        $curl = curl_init();

        $flip = new Flip;
        $response = $flip->disburse([
            'bank_code' => $bank_code,
            'account_number' => $bank_account,
            'remark' => $remark,
            'amount' => $amount
        ]);
        $data = json_decode($response);
        echo "Disbursement request is sent. Your transaction id is {$data->id}.\n";
        break;

    case '2':
        break;

    default:
        abort();
}

echo "Thank you for using our service\n";

function abort() {
    echo "Whoops, we have trouble.\n";
    echo "Aborting....\n";
    exit;
}
