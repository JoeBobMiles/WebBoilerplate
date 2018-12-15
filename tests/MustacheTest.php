<?php
/**
 * @author Joseph Miles <josephmiles2015@gmail.com>
 */

use PHPUnit\Framework\TestCase;
use Mustache\Mustache;

class MustacheTest extends TestCase
{
    /**
     * Check that when the variable a variable tag references is in the
     * hash that is passed to Mustache, it renders with the value of that
     * variable.
     *
     * @return void
     */
    public function testVariableTagRendersWhenVariableIsPresentInHash()
    {
        $template = "{{hello}}";

        $message  = "Hello World!";

        $data = [ 'hello' => $message ];

        $this->assertEquals($message, Mustache::render($template, $data));
    }

    /**
     * Check that when the variable a variable tag references is not in the
     * hash that is passed to Mustache, it does not render at all.
     *
     * @return void
     */
    public function testVariableTagDoesNotRenderWhenVariableIsMissing()
    {
        $template = "{{hello}}";

        $this->assertEquals("", Mustache::render($template, []));
    }

    /**
     * Check to make sure that the variable tag properly escapes HTML.
     *
     * @return void
     */
    public function testVariableTagEscapesHtmlUponReplacement()
    {
        $template = "{{message}}";

        $message = "<p>Hello World!</p>";

        $data = [ 'message' => $message ];

        $this->assertEquals(
            htmlspecialchars($message),
            Mustache::render($template, $data)
        );
    }

    /**
     * Check that, when the variable an unescaped variable tag references is
     * present, that the unescaped variable is rendered with the value in the
     * hash.
     *
     * @return void
     */
    public function testUnescapedVariableTagsRenderWhenVariableIsPresentInHash()
    {
        $template1 = "{{{hello}}}";
        $template2 = "{{&hello}}";

        $message = "Hello World!";

        $data = [ 'hello' => $message ];

        $this->assertEquals($message, Mustache::render($template1, $data));
        $this->assertEquals($message, Mustache::render($template2, $data));
    }

    /**
     * Check that, when the variable an unescaped variable tag references is
     * missing, the unescaped variable is not rendered.
     *
     * @return void
     */
    public function testUnescapedVariableTagsDoNotRenderWhenVariableIsAbsent()
    {
        $template1 = "{{{hello}}}";
        $template2 = "{{&hello}}";

        $this->assertEquals("", Mustache::render($template1, []));
        $this->assertEquals("", Mustache::render($template2, []));
    }

    /**
     * Check that the unescaped variable tags do not escape HTML.
     *
     * @return void
     */
    public function testUnescapedVariableTagsDoNotEscapeHtml()
    {
        $template1 = "{{{message}}}";
        $template2 = "{{&message}}";

        $message = "<p>Hello World!</p>";

        $data = [ 'message' => $message ];

        $this->assertEquals($message, Mustache::render($template1, $data));
        $this->assertEquals($message, Mustache::render($template2, $data));
    }

    /**
     * Check that the section tags do not render when the variable is present
     * in the has we given the Mustache renderer.
     *
     * @return void
     */
    public function testSectionTagsDoNotRenderWhenVariablePresent()
    {
        $template = "{{#section}}{{/section}}";

        $data = [ 'section' => true ];

        $this->assertEquals("", Mustache::render($template, $data));
    }

    /**
     * Check that the section tags do not render when the variable is not
     * present in the has we give the Mustache renderer.
     *
     * @return void
     */
    public function testSectionTagsDoNotRenderWhenVariableAbsent()
    {
        $template = "{{#section}}{{/section}}";

        $this->assertEquals("", Mustache::render($template, []));
    }

    /**
     * Check that when the variable in the hash that the section references is
     * falsy (evaulates to false), that the section tags do not render.
     *
     * @return void
     */
    public function testSectionTagsDoNotRenderWhenVariableIsFalsy()
    {
        $template = "{{#section}}{{/section}}";

        $data = [ 'section' => false ];

        $this->assertEquals("", Mustache::render($template, $data));
    }

    /**
     * Check that the contents of the section tags render when the variable
     * they reference is present in the hash we give the Mustache renderer.
     *
     * @return void
     */
    public function testSectionContentsRenderWhenVariablePresent()
    {
        $message = "Hello World!";

        $template = "{{#section}}{$message}{{/section}}";

        $data = [ 'section' => true ];

        $this->assertEquals($message, Mustache::render($template, $data));
    }

    /**
     * Check that the contents of the section tags do not render when the
     * variable they reference is absent from the hash we give the Mustache
     * renderer.
     *
     * @return void
     */
    public function testSectionContentsDoNotRenderWhenVariableAbsent()
    {
        $message = "Hello World!";

        $template = "{{#section}}{$message}{{/section}}";

        $this->assertEquals("", Mustache::render($template, []));
    }

    /**
     * Check that the contents of the section tag do not render when the
     * variable they reference is falsy (evaluates to false).
     *
     * @return void
     */
    public function testSectionContentsDoNotRenderWhenVariableFalsy()
    {
        $message = "Hello World!";

        $template = "{{#section}}{$message}{{/section}}";

        $data = [ 'section' => false ];

        $this->assertEquals("", Mustache::render($template, $data));
    }

    /**
     * Check that the section only uses the values stored in the variable it
     * references and cannot access the context outside of it.
     *
     * @return void
     */
    public function testSectionOnlyUsesVariableValueAsContext()
    {
        $template = "{{#section}}{{incontext}}{{outofcontext}}{{/section}}";

        $message = "Hello World!";

        $data = [
            'outofcontext' => "Goodbye World!",
            'section' => [ 'incontext' => $message ]
        ];

        $this->assertEquals($message, Mustache::render($template, $data));
    }

    /**
     * Check that when the variable a section references is a non-empty list,
     * the section renders once for each item in the list.
     *
     * @return void
     */
    public function testSectionCanIterateOverNonEmptyLists()
    {
        $template = "{{#section}}{{name}}!{{/section}}";

        $data = [
            'section' => [
                [ 'name' => 'Adam' ],
                [ 'name' => 'Adam' ],
                [ 'name' => 'Adam' ]
            ]
        ];

        $this->assertEquals(
            "Adam!Adam!Adam!",
            Mustache::render($template, $data)
        );
    }

    /**
     * Check that when the variable is a Closure, the section passes its
     * contents to the Closure and then displays what is returned by the
     * Closure.
     *
     * @return void
     */
    public function testSectionPassesContentToClosureAndDisplaysReturnValue()
    {
        $message = "Hello World!";

        $template = "{{#section}}{{/section}}";

        $data = [
            'section' => function () use ($message) {
                return $message;
            }
        ];

        $this->assertEquals($message, Mustache::render($template, $data));
    }

    /**
     * Check that when the variable the section references is a Closure, the
     * section passes its parent context to the Closure.
     *
     * @return void
     */
    public function testSectionPassesParentContextToClosure()
    {
        $message = "Hello World!";

        $template = "{{#section}}{{message}}{{/section}}";

        $data = [
            'message' => $message,
            'section' => function ($content, $context) {
                return '<b>'.Mustache::render($content, $context).'</b>';
            }
        ];

        $this->assertEquals(
            "<b>{$message}</b>",
            Mustache::render($template, $data)
        );
    }

    /**
     * Check that when the variable that an inverted section refers to is
     * present in the hash that has been passed to our Mustache renderer, the
     * contents of the inverted section are not displayed.
     *
     * @return void
     */
    public function testInvertedSectionContentsDoNotRenderWhenVariablePresent()
    {
        $template = "{{^section}}Hello World!{{/section}}";

        $data = [ 'section' => true ];

        $this->assertEquals("", Mustache::render($template, $data));
    }

    /**
     * Check that when the variable an inverted section refers to is present
     * in the hash, the contents of the inverted section are displayed.
     *
     * @return void
     */
    public function testInvertedSectionContentsRenderWhenVariableIsAbsent()
    {
        $message = "Hello World!";

        $template = "{{^section}}{$message}{{/section}}";

        $this->assertEquals($message, Mustache::render($template, []));
    }

    /**
     * Check that when the variable an inverted section refers to is falsy
     * (evaluates to false), the contents of the inverted section are
     * displayed.
     *
     * @return void
     */
    public function testInvertedSectionContentsRenderWhenVariableIsFalsy()
    {
        $message = "Hello World!";

        $template = "{{^section}}{$message}{{/section}}";

        $data = [ 'section' => false ];

        $this->assertEquals($message, Mustache::render($template, $data));
    }

    /**
     * Check that the comment tag does not render.
     *
     * @return void
     */
    public function testCommentTagDoesNotRender()
    {
        $template = "{{! This is a comment}}";

        $this->assertEquals("", Mustache::render($template, []));
    }

    public function testPartialTagIncludesSpecifiedTemplateFile()
    {
        $message1 = "Hello World!";
        $message2 = "Goodbye World!";

        file_put_contents(
            $_SERVER['DOCUMENT_ROOT']."../templates/test.tpl",
            $message2
        );

        $template = "{$message1} {{>example_partial}}";

        $this->assertEquals(
            "{$message1} {$message2}",
            Mustache::render($template, []);
        );
    }
}
