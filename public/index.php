<?php

require_once '../bootstrap/bootstrap.php';

/*
TODO:
    1. Routing
    2. Views (External library?)
 */

echo '<pre>';
// var_dump($_SERVER);

use Router\Router;
use Mustache\Mustache;

Router::get('/', function () {
    return (new Mustache)
    ->render(
        'example', 
        [
            'name' => 'John Doe',
            'value' => 1000000,
            'taxed_value' => 1000000 - (1000000 * 0.4),
            'in_ca' => true
        ]
    );
});

Router::route();