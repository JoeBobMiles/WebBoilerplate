<?php
/**
 * @author Joseph Miles <josephmiles2015@gmail.com>
 *
 * This file declares the Router object, through which we create routes that
 * allow us to declare custom URIs and actions for them.
 */

namespace Router;

use HTTP;

class Router
{
    /**
     * An array in which we store all of our routes that use the GET request
     * method.
     *
     * @var array
     */
    private static $get = [];

    /**
     * An array in which we store all of our routes that use the POST request
     * method.
     *
     * @var array
     */
    private static $post = [];

    /**
     * Register a new route.
     *
     * @param  integer  $method   The request method type
     * @param  string   $uri      The URI this route is mapped to.
     * @param  Closure  $callback What to do when this route is requested.
     *
     * @return void
     */
    public static function register($method, $uri, $callback)
    {
        switch ($method) {
            case HTTP\Method::GET:
                self::$get[$uri] = $callback;
                break;

            case HTTP\Method::POST:
                self::$post[$uri] = $callback;
                break;
        }
    }

    /**
     * Checks that the Router has a route whose request method and URI match
     * the given $method and $uri, respectively.
     *
     * @param  integer  $method The request method of the route.
     * @param  string   $uri    The URI of the route.
     *
     * @return boolean  True if the route exists, false otherwise.
     */
    public static function has($method, $uri)
    {
        switch ($method) {
            case HTTP\Method::GET:
                return isset(self::$get[$uri]);

            case HTTP\Method::POST:
                return isset(self::$post[$uri]);
        }
    }

    /**
     * Call the route (if it is registered) that corresponds to the given
     * request method string and URI.
     *
     * @param  string   $method_string The request method string.
     * @param  string   $uri           The URI.
     *
     * @return mixed    What is returned by the route.
     */
    public static function route($method_string, $uri)
    {
        $method = HTTP\Method::convert($method_string);

        switch ($method) {
            case HTTP\Method::GET:
                return self::$get[$uri]();

            case HTTP\Method::POST:
                return self::$post[$uri]();
        }
    }

    /**
     * Removes all routes registered to the Router.
     *
     * @return void
     */
    public static function unregisterAll()
    {
        self::$get  = [];
        self::$post = [];
    }
}
