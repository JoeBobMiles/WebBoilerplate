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

            /*
            TODO: Figure out how to hand skipping sections. Once we've figured
            out how to do that, we can figure out how to fill out the inside of
            them.
             */

            // This causes us to skip the check that would set $skip to false.
            // if ($skip) continue;

            preg_match_all('/{{(.+?)}}/', $line, $matches);

            foreach ($matches[1] as $match) {

                // Check for section begin tag
                if (preg_match('/^#/', $match)) {
                    echo 'section begin.';
                    $skip = true;
                }

                // Check for section end tag
                else if (preg_match('/^\//', $match)) {
                    echo 'section end.';
                    $skip = false;
                }

                // This doesn't skip the check, and seems promising, besides
                // the fact that the end-section tag get's left behind as a
                // blank line.
                if ($skip) continue;

                // Check for escape tag

                // Normal tag
                else {
                    $match = addslashes($match);

                    $line = str_replace(
                                "{{{$match}}}",
                                $data[$match] ?? '',
                                $line
                            );
                }
            }
        }

        // Free the reference so that we can use it later if need be.
        unset($line);

        return implode($lines);
    }
}