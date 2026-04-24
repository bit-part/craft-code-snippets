<?php

namespace bitpart\codesnippets\tests\unit;

use bitpart\codesnippets\CodeSnippets;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * Tests for the pure HTML injection helper used by CodeSnippets::injectSnippets().
 */
class HtmlInjectionTest extends TestCase
{
    private ReflectionMethod $injectAtTag;
    private CodeSnippets $instance;

    protected function setUp(): void
    {
        parent::setUp();

        // Avoid invoking the Plugin constructor (requires Craft runtime).
        $this->instance = (new ReflectionClass(CodeSnippets::class))
            ->newInstanceWithoutConstructor();

        $this->injectAtTag = new ReflectionMethod(CodeSnippets::class, 'injectAtTag');
    }

    private function inject(string $html, string $pattern, string $content, bool $before): string
    {
        return $this->injectAtTag->invoke($this->instance, $html, $pattern, $content, $before, 'test');
    }

    public function testInjectsBeforeClosingHead(): void
    {
        $html = '<html><head><title>x</title></head><body></body></html>';
        $out = $this->inject($html, '/<\/head>/i', '<script>GTM</script>', true);
        $this->assertStringContainsString("<script>GTM</script>\n</head>", $out);
    }

    public function testInjectsAfterOpeningHead(): void
    {
        $html = '<html><head><title>x</title></head></html>';
        $out = $this->inject($html, '/<head[^>]*>/i', '<meta name="x">', false);
        $this->assertStringContainsString("<head>\n<meta name=\"x\">", $out);
    }

    public function testInjectsAfterOpeningBodyWithAttributes(): void
    {
        $html = '<html><body class="x" data-y="z"><p>hi</p></body></html>';
        $out = $this->inject($html, '/<body[^>]*>/i', '<noscript>NS</noscript>', false);
        $this->assertStringContainsString("<body class=\"x\" data-y=\"z\">\n<noscript>NS</noscript>", $out);
    }

    public function testDollarSignsInContentAreNotInterpretedAsBackrefs(): void
    {
        // The whole point of switching from preg_replace to preg_replace_callback:
        // user-provided content with $1, $2, \1 must be inserted literally.
        $html = '<html><body></body></html>';
        $content = '<script>var x = "$1 $2 \\1 $0 ${name}";</script>';
        $out = $this->inject($html, '/<\/body>/i', $content, true);
        $this->assertStringContainsString('$1 $2', $out);
        $this->assertStringContainsString('$0', $out);
        $this->assertStringContainsString('${name}', $out);
    }

    public function testNoMatchReturnsOriginalHtml(): void
    {
        $html = '<html><body></body></html>'; // no </head>
        $out = $this->inject($html, '/<\/head>/i', '<x/>', true);
        $this->assertSame($html, $out);
    }

    public function testOnlyFirstMatchIsReplaced(): void
    {
        $html = '<body></body><body></body>';
        $out = $this->inject($html, '/<body[^>]*>/i', '<X/>', false);
        $this->assertSame(1, substr_count($out, '<X/>'));
    }

    public function testCaseInsensitiveTagMatch(): void
    {
        $html = '<HTML><HEAD></HEAD></HTML>';
        $out = $this->inject($html, '/<\/head>/i', '<script>x</script>', true);
        $this->assertStringContainsString("<script>x</script>\n</HEAD>", $out);
    }
}
