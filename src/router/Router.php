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
     * @return boolean  Returns false if $method is not recognized.
     */
    public static function register($method, $uri, $callback)
    {
        switch ($method) {
            case HTTP\Method::GET:
                self::$get[$uri] = $callback;
                return true;

            case HTTP\Method::POST:
                self::$post[$uri] = $callback;
                return true;

            default:
                return false;
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

            default:
                return false;
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

        /*
        It may not be prudent to use false as an indication of error
        here. Some routes may opt to return false to indicate error
        themselves, and the ambiguity between the route not being
        found and the route returning false could lead to issues down
        the line.

        What may be more ideal is returning "Bad Request" or "Not Found"
        instead.

        2018-12-13 Joseph Miles
         */

        switch ($method) {
            case HTTP\Method::GET:
                return self::$get[$uri]() ?? false;

            case HTTP\Method::POST:
                return self::$post[$uri]() ?? false;

            default:
                return false;
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
