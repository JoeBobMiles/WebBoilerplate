<?php
/*
This is used to bootstrap our top level functions and configurations.
 */

// Helper functions

/**
 * Dumps and dies the given parameters.
 *
 * @param  mixed $params
 */
function dd (...$params) {
    die(var_dump(...$params));
}

// Autoloader function
spl_autoload_register(function ($classname) {

    $bits = explode('/', $classname);

    $filename = end($bits).'.php';

    // May not be entirely necissary, but doing it anyways.
    reset($bits);

    $bits = array_map(function ($bit) { return strtolower($bit); }, $bits);

    $path = join(array_slice($bits, 0, count($bits) - 1), '/');


    if (count($bits) > 1)
        $full_path = "../src/{$path}/{$filename}";

    else $full_path = "../src/{$filename}";


    if (file_exists($full_path)) {
        require_once $full_path;
        return true;
    }

    else return false;
});