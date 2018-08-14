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

Router::get('/', function () {
    return 'hello!';
});

Router::route();