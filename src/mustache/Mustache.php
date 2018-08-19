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
        $path = "/var/www/templates/{$template}.tpl";

        $contents = file_get_contents($path);
        
        $tokens = $this->tokenize($contents);

        $this->parse($tokens);

        return $contents;
    }

    /**
     * This splits the given template contents by the accepted Mustache tags
     * and creates an array of tokens from the split segments.
     * 
     * @param  string $contents
     * @return array
     */
    private function tokenize($contents)
    {
        $segments = preg_split(
            '/({{.+?}?}})/',
            $contents,
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );

        $tokens = [];

        foreach ($segments as $key => $segment) {

            /*
            Match the partial tag ({{>...}}).
             */
            if (preg_match('/{{>(.+?)}}/', $segment, $match)) {
                $tokens[$key] = [
                    'type' => 'tag_partial',
                    /*
                    We are going to need to perform some extra parsing in
                    order to figure out which template is being referred to.
                    Could be done here, or elsewhere.
                    */
                    'name' => $match[1],
                    'segment' => $segment
                ];
            }

            /*
            Match the section begin tag ({{#...}}).
             */
            else if (preg_match('/{{#(.+?)}}/', $segment, $match)) {
                $tokens[$key] = [
                    'type' => 'tag_section_begin',
                    'name' => $match[1],
                    'segment' => $segment
                ];
            }

            /*
            Match the inverted section tag ({{^...}})
             */
            else if (preg_match('/{{\^(.+?)}}/', $segment, $match)) {
                $tokens[$key] = [
                    'type' => 'tag_inverted_begin',
                    'name' => $match[1],
                    'segement' => $segment
                ];
            }

            /*
            Match the end section tag ({{/...}})
             */
            else if (preg_match('/{{\/(.+?)}}/', $segment, $match)) {
                $tokens[$key] = [
                    'type' => 'tag_section_end',
                    'name' => $match[1],
                    'segment' => $segment
                ];
            }

            /*
            Match the comment tag ({{!...}})
             */
            else if (preg_match('/{{!.+?}}/', $segment)) {
                $tokens[$key] = [
                    'type' => 'tag_comment',
                    'segment' => $segment
                ];
            }

            /*
            Match the unescaped tags ({{{...}}} and {{&...}}).
            Incidentally, this also treats the 'malformed' {{&...}}} as a valid
            unescaped tag.
             */
            else if (preg_match('/{{[{&](.+?)}?}}/', $segment, $match)) {
                $tokens[$key] = [
                    'type' => 'tag_unescaped',
                    'name' => $match[1],
                    'segment' => $segment
                ];
            }

            /*
            Match the escaped tag.
             */
            else if (preg_match('/{{(.+?)}}/', $segment, $match)) {
                $tokens[$key] = [
                    'type' => 'tag_escaped', 
                    'name' => $match[1],
                    'segment' => $segment
                ];
            }

            /*
            Anything else that doesn't match the above patterns will be treated
            as text for the sake of simplicity.
             */
            else {
                $tokens[$key] = [
                    'type' => 'text',
                    'segment' => $segment
                ];
            }
        }

        return $tokens;
    }

    /**
     * Parses the given tokens into contexts and readies them to be processed.
     *
     * @NOTE We may just process them here and return the compiled template.
     * 
     * @param  array  $tokens
     * @return string
     */
    private function parse($tokens)
    {
        dd($tokens);
    }
}