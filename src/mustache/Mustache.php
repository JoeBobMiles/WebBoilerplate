<?php

namespace Mustache;

class Mustache
{
    /**
     * Renders the given $template_string with the given $data.
     *
     * @param  string $template_string
     * @param  array  $data
     *
     * @return string
     */
    public static function render($template, $data)
    {
        $tokens = self::tokenize($template);

        $syntax_tree = self::parse($tokens);

        return self::compile($syntax_tree, $data);
    }

    public static function getTemplate($template_name)
    {
        return file_get_contents(
            $_SERVER['DOCUMENT_ROOT']."/../templates/{$template_name}.tpl"
        );
    }

    /**
     * This splits the given template contents by the accepted Mustache tags
     * and creates an array of tokens from the split segments.
     *
     * @param  string $contents
     *
     * @return array
     */
    private static function tokenize($contents)
    {

        /*
        Cleans up any trailing new lines after section and partial tags.
        We do this so that new lines that occur after unrendered tags don't
        get rendered either.

        The regex matches the following unrendered tags:
         - The 'partial' tag,
         - The section tags (start, inverted, end), and
         - The comment tag
        */
        $contents = preg_replace(
            '/({{[!>#^\/].+?}})[\n\r]{1,2}/',
            '$1',
            $contents
        );

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
                    'name' => $match[1],
                    'segment' => $segment
                ];
            }

            /*
            Match the section begin tag ({{#...}}).
             */
            elseif (preg_match('/{{#(.+?)}}/', $segment, $match)) {
                $tokens[$key] = [
                    'type' => 'tag_section_begin',
                    'name' => $match[1],
                    'segment' => $segment
                ];
            }

            /*
            Match the inverted section tag ({{^...}})
             */
            elseif (preg_match('/{{\^(.+?)}}/', $segment, $match)) {
                $tokens[$key] = [
                    'type' => 'tag_inverted_begin',
                    'name' => $match[1],
                    'segement' => $segment
                ];
            }

            /*
            Match the end section tag ({{/...}})
             */
            elseif (preg_match('/{{\/(.+?)}}/', $segment, $match)) {
                $tokens[$key] = [
                    'type' => 'tag_section_end',
                    'name' => $match[1],
                    'segment' => $segment
                ];
            }

            /*
            Match the comment tag ({{!...}})
             */
            elseif (preg_match('/{{!.+?}}/', $segment)) {
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
            elseif (preg_match('/{{[{&](.+?)}?}}/', $segment, $match)) {
                $tokens[$key] = [
                    'type' => 'tag_unescaped',
                    'name' => $match[1],
                    'segment' => $segment
                ];
            }

            /*
            Match the escaped tag.
             */
            elseif (preg_match('/{{(.+?)}}/', $segment, $match)) {
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
     *
     * @return array
     */
    private static function parse($tokens)
    {
        $syntax_tree = [];
        $section = null;

        foreach ($tokens as $token) {
            if ($token['type'] === 'tag_section_begin' && !$section) {
                $section = $token['name'];
                $syntax_tree[$section]['inverted'] = false;
                $syntax_tree[$section]['nodes'] = [];

                continue;
            }

            elseif ($token['type'] === 'tag_inverted_begin' && !$section) {
                $section = $token['name'];
                $syntax_tree[$section]['inverted'] = true;
                $syntax_tree[$section]['nodes'] = [];

                continue;
            }

            elseif ($token['type'] === 'tag_section_end'
                    && $token['name'] === $section)
            {
                $syntax_tree[$section]['nodes'] = self::parse(
                                            $syntax_tree[$section]['nodes']
                                        );

                $section = null;

                continue;
            }


            if ($section)
                $syntax_tree[$section]['nodes'][] = $token;

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
     *
     * @return string
     */
    private static function compile($syntax_tree, $data)
    {
        $segments = [];

        foreach ($syntax_tree as $key => $node) {
            if (is_string($key)) {
                if ($node['inverted'] && !($data[$key] ?? false))
                    $segments[] = self::compile($node['nodes'], $data);

                elseif (!$node['inverted'] && ($data[$key] ?? false)) {
                    if (is_array($data[$key])) {
                        $subcontext_keys = array_keys($data[$key]);

                        if (is_numeric($subcontext_keys[0])) {
                            foreach ($data[$key] as $context)
                                $segments[] = self::compile($node['nodes'], $context);

                        } else
                            $segments[] = self::compile($node['nodes'], $data[$key]);
                    }

                    elseif (is_callable($data[$key]))
                        $segments[] = call_user_func($data[$key], self::reconstructTemplate($node['nodes']), $data);

                    else
                        $segments[] = self::compile($node['nodes'], $data[$key]);
                }
            } elseif (is_numeric($key)) {
                if ($node['type'] === 'text')
                    $segments[] = $node['segment'];

                elseif ($node['type'] === 'tag_partial') {
                    $contents = self::getTemplate($node['name']);

                    $segments[] = self::render($contents, $data);
                }

                elseif ($node['type'] === 'tag_unescaped')
                    $segments[] = $data[$node['name']] ?? '';

                elseif ($node['type'] !== 'tag_comment')
                    $segments[] = htmlspecialchars($data[$node['name']] ?? '');
            }
        }

        return implode($segments);
    }

    /**
     * Allows us to reconstruct the syntax tree into the text that it was
     * derived from self::parse().
     *
     * @param  mixed[]  $syntax_tree    A syntax tree returned from self::parse()
     *
     * @return string
     */
    private static function reconstructTemplate($syntax_tree)
    {
        $string = "";

        foreach ($syntax_tree as $node)
            $string .= $node['segment'];

        return $string;
    }
}
