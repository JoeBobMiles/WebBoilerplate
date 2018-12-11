<?php
/**
 * @author Joseph Miles <josephmiles2015@gmail.com>
 */

namespace Router;

class Router
{
    /**
     * Stores the GET routes.
     *
     * @var callable[]
     */
    public static $get = [];

    /**
     * Stores the POST routes.
     *
     * @var callable[]
     */
    public static $post = [];

    /**
     * Executes the route specified by the $uri and the $request_type, echoing
     * the returned result.
     *
     * If the route specified by the request uri does not exist, then the
     * Router returns 404.
     *
     * @return void
     */
    public static function route()
    {
        $uri = $_SERVER['REQUEST_URI'];

        $request_name = strtolower($_SERVER['REQUEST_METHOD']);

        if (isset(self::${$request_name}[$uri]))
            echo self::${$request_name}[$uri]($uri);

        else echo '404';
    }

    /**
     * Takes the given uri and operation and adds them to the $get array, with
     * the uri as the key and the operation as the value.
     *
     * @NOTE This and post() could probably be wrappers for a single function
     * so that we don't have to keep rewriting the same logic twice (or more
     * if we have other types of request functions).
     *
     * @param   string      $uri        The URI to respond to.
     * @param   callable    $operation  The operation to perform.
     *
     * @return  Route   A new Route object.
     */
    public static function get(string $uri, callable $operation)
    {
        $route = new Route($uri, $operation);

        self::$get[$uri] = $route;

        return $route;
    }

    /**
     * Takes the given uri and operation and adds them to the $post array,
     * with the uri as the key and the operation as the value.
     *
     * @param   string   $uri       The URI to respond to.
     * @param   callable $operation The operation to perform.
     *
     * @return  Route   A new Route object.
     */
    public static function post(string $uri, callable $operation)
    {
        $route = new Route($uri, $operation);

        self::$post[$uri] = $route;

        return $route;
    }
}
