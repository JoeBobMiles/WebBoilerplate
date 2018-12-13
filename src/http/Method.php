<?php
/**
 * @author Joseph Miles <josephmiles2015@gmail.com>
 *
 * This file defines HTTP request method names as enum values for code
 * readability.
 */

namespace HTTP;

class Method
{
    const GET  = 1;
    const POST = 2;

    /**
     * Converts an HTTP request method string to it's corresponding enum value.
     *
     * @param  string   $method_string
     *
     * @return integer|boolean
     */
    public static function convert($method_string)
    {
        switch (strtoupper($method_string)) {
            case "GET":
                return self::GET;

            case "POST":
                return self::POST;

            default:
                return false;
        }
    }
}
