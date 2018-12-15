<?php

require_once '../bootstrap/bootstrap.php';

use Router\Router;
use Mustache\Mustache;

/*
Let's render our example Mustache template with some example Mustache data!
 */
Router::register(HTTP\Method::GET, '/', function () {
    return Mustache::render(
        Mustache::getTemplate("index"),
        [
            'title' => "J. R. Miles"
        ]
    );
});

echo Router::route($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
