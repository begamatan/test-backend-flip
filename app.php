<?php
require_once './bootstrap.php';

use App\Console\Disburse;
use App\Console\GetStatus;

echo 'Hello! What do you want me to do?' . PHP_EOL;
echo '1) Send disbursement' . PHP_EOL;
echo '2) Get disbursement status' . PHP_EOL;
echo 'Please choose the task by typing the number (1/2)' . PHP_EOL;
switch (trim(readline())) {
    case '1':
        (new Disburse)->start();
        break;

    case '2':
        (new GetStatus)->start();
        break;

    default:
        echo 'Task not found. Please try again and choose correct task' . PHP_EOL;
        break;
}
