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

            preg_match_all('/{{(.+?[}]?)}}/', $line, $matches);

            /*
            TODO:
                1. Sections whose name match an index whose value is an array
                    or other iterable need to iterate over the contents of
                    said iterable, using what is between the tags as the new
                    template.
                2. Data elements that are callable need to be called and given
                    parameters that allow them to do interesting things like
                    add bold tags to the text they are given.
                3. Inverted sections (sections that are executed if an index
                    is _not_ in the array, or has a false value).
                4. Partials (injection of templates into templates).
             */

            foreach ($matches[1] as $match) {

                // Check for section tags
                if (preg_match('/^[#\/]/', $match)) {
                    $line = '';

                    $skip = !$skip && !($data[trim($match, '#/')] ?? 0);

                    if ($skip) continue;
                }

                if (!$skip) {
                    $scrubbed_match = addslashes(trim($match, '&{} '));

                    $replacement = $data[$scrubbed_match] ?? '';

                    if (!preg_match('/{.+?}|^&/', $match))
                        $replacement = htmlspecialchars($replacement);

                    $line = str_replace("{{".$match."}}", $replacement, $line);
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