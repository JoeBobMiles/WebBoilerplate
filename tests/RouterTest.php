<?php

// Include PHPUnit TestCase class.
use PHPUnit\Framework\TestCase;

// Include the Router class.
use Router\Router;

class RouterTest extends TestCase
{
    /**
     * Tests that the Router class is a singleton.
     */
    public function testIsSingleton()
    {
        /*
        1. Cannot call constructor.
        2. Cannot use clone.
        3. Instances retreived from getInstance() are the same.
         */
        return;
    }

    /**
     * Tests to see if we can add GET routes to the Router and use them.
     */
    public function testCanRegisterGetRoutes()
    {
        $router = new Router;

        $callback = function () { echo "Hello World!" };

        $router->get('/', $callback);

        $this->assertTrue(array_key_exists('/', Router::$get));

        // IDK if 'assertEquals' is the appropriate check for comparing
        // Closures. We _could_ test the output of both Closures, which
        // should be the same.
        $this->assertEquals(Router::$get['/'], $callback);
    }

    public function testCanRegisterPostRoutes()
    {
        return;
    }

    public function testCanRouteGet()
    {
        return;
    }

    public function testCanRoutePost()
    {
        return;
    }
}