<?php

namespace Mustache;

class Mustache
{
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

        $syntax_tree = $this->parse($tokens);

        return $this->compile($syntax_tree, $data);
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
     * Parses the given array of tokens into a syntax tree.
     *
     * @param  array  $tokens
     * @return array
     */
    private function parse($tokens)
    {
        $syntax_tree = [];
        $section = null;

        foreach ($tokens as $token) {

            if ($token['type'] === 'tag_section_begin' && !$section) {
                $section = $token['name'];
                $syntax_tree[$section] = [];

                continue;
            }

            else if ($token['type'] === 'tag_inverted_begin' && !$section) {
                $section = $token['name'];
                $syntax_tree[$section] = [];

                continue;
            }

            else if ($token['type'] === 'tag_section_end' && 
                     $token['name'] === $section)
            {
                $syntax_tree[$section] = $this->parse(
                                            $syntax_tree[$section]
                                        );

                $section = null;

                continue;
            }


            if ($section)
                $syntax_tree[$section][] = $token;

            else
                $syntax_tree[] = $token;
        }

        return $syntax_tree;
    }

    /**
     * Compiles the given syntax tree with the given data and returns the
     * resultant string.
     * 
     * @param  array  $syntax_tree
     * @param  array  $data        
     * @return string
     */
    private function compile($syntax_tree, $data)
    {
        $segments = [];

        foreach ($syntax_tree as $key => $node) {
            if ($data[$key] ?? false)
                $segments[] = $this->compile($node, $data);

            else if (is_numeric($key)) {
                if ($node['type'] === 'text') {
                    // The first text segment in a section will annoyingly
                    // have a leading newline due to how we tokenize. What this
                    // means is that we have to left-trim the first text segment
                    // to make sure there are no new lines that are upsetting
                    // our formating.
                    $segments[] = $key !== 0 
                                    ? $node['segment']
                                    : ltrim($node['segment']);
                }

                // It also seems that when we have a token immediately following
                // an end-of-section tag, we need to trim the leading new line,
                // but I have no idea how to do that.
                else if ($node['type'] === 'tag_partial')
                    $segments[] = $this->render($node['name'], $data);

                else if ($node['type'] !== 'tag_comment')
                    $segments[] = $data[$node['name']] ?? '';
            }
        }

        return implode($segments);
    }
}