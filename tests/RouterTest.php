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
}
