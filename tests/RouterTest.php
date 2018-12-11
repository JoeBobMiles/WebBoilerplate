<?php
/**
 * @author Joseph Miles <josephmiles2015@gmail.com>
 */

use PHPUnit\Framework\TestCase;
use Router\Route;
use Router\Router;

class RouterTest extends TestCase
{
    /**
     * Tests that an instance of the Router class cannot be created.
     *
     * @return  void
     */
    public function testIsSingleton()
    {
        return;
    }

    /**
     * Tests to see if we can add GET routes to the Router.
     *
     * @return  void
     */
    public function testCanRegisterGetRoutes()
    {
        Router::get('/', function () { echo "Hello World!" });

        $this->assertTrue(array_key_exists('/', Router::$get));
        $this->assertInstanceOf(Route::class, Router::$get['/']);
    }

    /**
     * Tests to see if we can add POST routes to the Router.\
     *
     * @return  void
     */
    public function testCanRegisterPostRoutes()
    {
        Router::post('/', function () { echo "Hello World!"; });

        $this->assertTrue(array_key_exists('/', Router::$post));
        $this->assertInstanceOf(Route::class, Router::$get['/'])
    }

    /**
     * Test that when we call Router::route() with the request method of GET,
     * we get the expected result.
     *
     * @return  void
     */
    public function testCanRouteGetRequest()
    {
        return;
    }

    /**
     * Test that when we call Router::route() with the request method of POST,
     * we get the expected result.
     *
     * @return  void
     */
    public function testCanRoutePostRequest()
    {
        return;
    }
}
