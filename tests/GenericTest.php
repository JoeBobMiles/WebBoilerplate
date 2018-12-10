<?php

use Router\Router;

use PHPUnit\Framework\TestCase;

class GenericTest extends TestCase
{
    public function testItWorks()
    {
        $this->assertInstanceOf(Router::class, new Router);
    }
}