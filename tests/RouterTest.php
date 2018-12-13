<?php
/**
 * @author Joseph Miles <josephmiles2015@gmail.com>
 */

use PHPUnit\Framework\TestCase;
use HTTP;
use Router\Router;

class RouterTest extends TestCase
{
    /**
     * Empties all routes from the Router to ensure that the results of
     * the previous tests don't interfere with our current tests.
     *
     * @return void
     *
     * @before
     */
    public function clearRoutes()
    {
        Router::unregisterAll();
    }

    /**
     * Check to see if we can register a GET route to the Router.
     *
     * @return void
     */
    public function testCanRegisterGetRoute()
    {
        Router::register(HTTP\Method::GET, '/', function () {
            return "Hello World!";
        });

        $this->assertTrue(Router::has(HTTP\Method::GET, '/'));
    }

    /**
     * Check to see if we can invoke a GET route after registering it
     * with the Router.
     *
     * @return void
     */
    public function testCanInvokeGetRoute()
    {
        $message = "Hello World!";

        Router::register(HTTP\Method::GET, '/', function () use ($message) {
            return $message;
        });

        $this->assertEquals($message, Router::route('GET', '/'));
    }

    /**
     * Check to see if we can register a POST route to the Router.
     *
     * @return void
     */
    public function testCanRegisterPostRoute()
    {
        Router::register(HTTP\Method::POST, '/', function () {
            return "Hello World!";
        });

        $this->assertTrue(Router::has(HTTP\Method::POST, '/'));
    }

    /**
     * Check to see if we can invoke a POST route we have registered with the
     * Router.
     *
     * @return void
     */
    public function testCanInvokePostRoute()
    {
        $message = "Hello World!";

        Router::register(HTTP\Method::POST, '/', function () use ($message) {
            return $message;
        });

        $this->assertEquals($message, Router::route('POST', '/'));
    }

    /**
     * Checks that when Router::register() is given a nonsense value for the
     * request method that it returns false.
     *
     * @return void
     */
    public function testRegisterReturnsFalseOnBadRequestMethod()
    {
        $result = Router::register("chicken", '/', function () {});

        $this->assertFalse($result);
    }

    /**
     * Checks that when Router::has() is given a nonsense value for the
     * request method that it returns false.
     *
     * @return void
     */
    public function testHasReturnsFalseOnBadRequestMethod()
    {
        $this->assertFalse(Router::has("chicken", '/'));
    }

    /**
     * Checks that Router::route() returns false when given a nonsense value
     * for the request method.
     *
     * As I suggested in @see Router\Router::route(), we will want to change
     * the failure value to something other than false.
     * 2018-12-13 Joseph Miles
     *
     * @return void
     */
    public function testRouteReturnsFalseOnBadRequestMethod()
    {
        $this->assertFalse(Router::route("chicken", '/'));
    }

    /**
     * Checks that Router::route() returns false when given a valid request
     * method and URI that doesn't exist in the Router.
     *
     * As noted above, we will want to change the failure value to something
     * other than false for the reasons cited in @see Router\Router::route().
     * 2018-12-13 Joseph Miles
     *
     * @return void
     */
    public function testRouteReturnsFalseOnUndefinedUri()
    {
        $this->assertFalse(Router::route(HTTP\Method::GET, '/'));
    }
}
