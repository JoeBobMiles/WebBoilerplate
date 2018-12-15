<?php

require_once '../bootstrap/bootstrap.php';

/**
 * @todo Need to create a way to generate views more elegantly. Probably do
 * something with the PSRs HTTP message specifications.
 */

echo '<pre>';

use Router\Router;
use Mustache\Mustache;

/*
Let's render our example Mustache template with some example Mustache data!
 */
Router::register(HTTP\Method::GET, '/', function () {
    return Mustache::render(
        file_get_contents($_SERVER['DOCUMENT_ROOT']."/../templates/example.tpl"),
        [
            'name' => 'John Doe',
            'value' => 1000000,
            'taxed_value' => 1000000 - (1000000 * 0.4),
            'in_ca' => true
        ]
    );
});

echo Router::route($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
