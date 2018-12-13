<?php

require_once '../bootstrap/bootstrap.php';

/*
TODO:
    1. Views
 */

echo '<pre>';
// var_dump($_SERVER);

use HTTP;
use Router\Router;
use Mustache\Mustache;

/*
Let's render our example Mustache template with some example Mustache data!
 */
Router::register(HTTP\Method::GET, '/', function () {
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

echo Router::route($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
