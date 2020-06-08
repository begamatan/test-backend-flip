<?php
require_once "./autoload.php";

use Model\Disbursement;

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

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://nextar.flip.id/disburse",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "bank_code={$bank_code}&account_number={$bank_account}&amount={$amount}&remark={$remark}",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic SHl6aW9ZN0xQNlpvTzduVFlLYkc4TzRJU2t5V25YMUp2QUVWQWh0V0tadW1vb0N6cXA0MTo=",
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $data = json_decode($response);
        echo "Disbursement request is sent. Your transaction id is {$data->id}.\n";
        echo "Saving response data to database...\n";
        $disbursement = new Disbursement;
        $disbursement->insert([
            'transaction_id' => $data->id,
            'amount' => $data->amount,
            'status' => $data->status,
            'timestamp' => $data->timestamp,
            'bank_code' => $data->bank_code,
            'account_number' => $data->account_number,
            'beneficiary_name' => $data->beneficiary_name,
            'remark' => $data->remark,
            'receipt' => $data->receipt,
            'time_served' => $data->time_served === '0000-00-00 00:00:00' ? null : $data->time_served,
            'fee' => $data->fee
        ]);
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
