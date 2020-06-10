<?php

require_once './autoload.php';

use App\Database\Connection;

function app() {
    $app = [];
    $app['config'] = include './config.php';
    $app['connection'] = (new Connection)->start($app['config']['database']);
    return $app;
}
