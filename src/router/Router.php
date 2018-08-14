<?php

namespace Router;

class Router
{
    /**
     * Stores the GET routes.
     *
     * @var array
     */
    public static $get = [];

    /**
     * Stores the POST routes.
     *
     * @var array
     */
    public static $post = [];

    /**
     * Executes the route specified by the $uri and the $request_type, echoing
     * the returned result.
     */
    public static function route()
    {
        $uri = $_SERVER['REQUEST_URI'];

        $request_name = strtolower($_SERVER['REQUEST_METHOD']);

        echo self::${$request_name}[$uri]($uri);
    }

    /**
     * Takes the given uri and operation and adds them to the $get array, with
     * the uri as the key and the operation as the value.
     *
     * @NOTE This and post() could probably be wrappers for a single function
     * so that we don't have to keep rewriting the same logic twice (or more
     * if we have other types of request functions).
     *
     * @param string   $uri
     * @param callable $operation
     */
    public static function get($uri, $operation)
    {
        $route = new Route($uri, $operation);

        self::$get[$uri] = $route;

        return $route;
    }

    /**
     * Takes the given uri and operation and adds them to the $post array,
     * with the uri as the key and the operation as the value.
     *
     * @param string   $uri
     * @param callable $operation
     */
    public static function post($uri, $operation)
    {
        $route = new Route($uri, $operation);

        self::$post[$uri] = $route;

        return $route;
    }
}