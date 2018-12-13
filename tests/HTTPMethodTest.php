<?php
/**
 * @author Joseph Miles <josephmiles2015@gmail.com>
 */

use PHPUnit\Framework\TestCase;
use HTTP\Method;

class HTTPMethodTest extends TestCase
{
    public function testConvertsGetStringToGetInteger()
    {
        $this->assertEquals(Method::GET, Method::convert("GET"));
    }

    public function testConvertsPostStringToPostInteger()
    {
        $this->assertEquals(Method::POST, Method::convert("POST"));
    }

    public function testReturnsFalseOnNonsenseMethodString()
    {
        $this->assertFalse(Method::convert("chicken"));
    }
}
