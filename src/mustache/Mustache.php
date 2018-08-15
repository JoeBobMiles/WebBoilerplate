<?php

namespace Mustache;

class Mustache
{
    public function __construct()
    {
        //...
    }

    /**
     * Renders the template with the given name with the given data.
     * 
     * @param  string $template
     * @param  array  $data
     * @return string
     */
    public function render($template, $data)
    {
        /*
        @HACK This is has a hardcoded document root, which is problematic for
        deployment later, but sufficient for our current purposes.

        Also, we should probably take into account that not every template is
        going to be located in the templates/ directory. Some are going to be
        located in sub-directories and we should figure out how to parse the
        template name in such a manner as to allow for indexing into sub-
        directories.
        */
        $path = "/var/www/templates/{$template}.template";

        $contents = file_get_contents($path);

        $lines = explode("\n", $contents);

        $skip = false;

        foreach ($lines as &$line) {

            preg_match_all('/{{(.+?)}}/', $line, $matches);

            foreach ($matches[1] as $match) {

                // Check for section tags
                if (preg_match('/^[#\/]/', $match)) {
                    $line = '';

                    // I know this looks like it can be reduced, but it can't.
                    // Reducing the below expression breaks it.
                    $skip = !$skip && !isset($data[trim($match,'#/')]);

                    if ($skip) continue;
                }

                if (!$skip) {
                    $match = addslashes($match);

                    $line = str_replace(
                                "{{{$match}}}",
                                $data[$match] ?? '',
                                $line
                            );
                }

                else $line = '';
            }
        }

        // Free the reference so that we can use $line else where without
        // incident.
        unset($line);

        return implode($lines);
    }
}