<?php

namespace Mustache;

class Mustache
{
    public function __construct()
    {
        //...
    }

    /**
     * Renders the given template with the given data.
     * 
     * @param  string $template
     * @param  array  $data
     * @return string
     */
    public function render($template, $data)
    {
        // @HACK This is has a hardcoded document root, which is problematic
        // for deployment later, but sufficient for our current purposes.
        $path = "/var/www/templates/{$template}.template";

        $contents = file_get_contents($path);

        $lines = explode("\n", $contents);

        foreach ($lines as &$line) {
            preg_match_all('/{{(.+?)}}/', $line, $matches);

            foreach ($matches[1] as $match) {
                $match = addslashes($match);
                $line = str_replace(
                            "{{{$match}}}",
                            $data[$match] ?? '',
                            $line
                        );
            }
        }

        // Free the reference so that we can use it later if need be.
        unset($line);

        return implode($lines);
    }
}