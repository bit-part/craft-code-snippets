<?php

namespace bitpart\codesnippets\tests\unit\services;

use bitpart\codesnippets\elements\Snippet;
use bitpart\codesnippets\services\SnippetService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class SnippetServiceTest extends TestCase
{
    private SnippetService $service;
    private ReflectionMethod $uriMatchesPattern;
    private ReflectionMethod $matchesEnvironment;
    private ReflectionMethod $matchesUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SnippetService();

        // Access private methods via reflection for unit testing
        $this->uriMatchesPattern = new ReflectionMethod(SnippetService::class, 'uriMatchesPattern');
        $this->matchesEnvironment = new ReflectionMethod(SnippetService::class, 'matchesEnvironment');
        $this->matchesUri = new ReflectionMethod(SnippetService::class, 'matchesUri');
    }

    // =========================================================================
    // uriMatchesPattern tests
    // =========================================================================

    /**
     * @dataProvider uriPatternProvider
     */
    public function testUriMatchesPattern(string $uri, string $pattern, bool $expected): void
    {
        $result = $this->uriMatchesPattern->invoke($this->service, $uri, $pattern);
        $this->assertSame($expected, $result, "URI '$uri' with pattern '$pattern'");
    }

    public static function uriPatternProvider(): array
    {
        return [
            // Exact match
            'exact match' => ['checkout', 'checkout', true],
            'exact match with leading slash' => ['checkout', '/checkout', true],
            'exact match both slashes' => ['/checkout', '/checkout', true],
            'exact no match' => ['checkout', 'contact', false],

            // Wildcard patterns
            'wildcard products' => ['products/shoes', 'products/*', true],
            'wildcard nested' => ['products/shoes/red', 'products/*', true],
            'wildcard no match' => ['blog/post-1', 'products/*', false],
            'wildcard root' => ['anything', '*', true],

            // Homepage
            'homepage empty uri' => ['', '/', true],
            'homepage slash uri' => ['/', '/', true],

            // Empty pattern should NOT match (fixed in review)
            'empty pattern' => ['some-page', '', false],

            // Case insensitive
            'case insensitive' => ['Products/Shoes', 'products/*', true],

            // Special characters in URI (should be safe due to preg_quote)
            'uri with dots' => ['file.html', 'file.html', true],
            'uri with query-like' => ['search', 'search', true],

            // Multi-level paths
            'deep path exact' => ['a/b/c/d', 'a/b/c/d', true],
            'deep path wildcard' => ['a/b/c/d', 'a/b/*', true],
            'deep path no match' => ['a/b/c/d', 'x/y/*', false],

            // Trailing slash
            'trailing slash uri' => ['products/', 'products/*', true],
            'pattern without slash vs uri with' => ['products/', 'products/', true],
        ];
    }

    // =========================================================================
    // matchesEnvironment tests (using mock object to avoid Element dependency)
    // =========================================================================

    /**
     * @dataProvider environmentProvider
     */
    public function testMatchesEnvironment(?string $environments, ?string $currentEnv, bool $expected): void
    {
        $snippet = $this->createSnippetStub($environments);

        $result = $this->matchesEnvironment->invoke($this->service, $snippet, $currentEnv);
        $this->assertSame($expected, $result);
    }

    public static function environmentProvider(): array
    {
        return [
            // null environments = all environments
            'null envs matches any' => [null, 'production', true],
            'null envs matches null' => [null, null, true],
            'empty string envs' => ['', 'dev', true],

            // Specific environments
            'production only - match' => ['["production"]', 'production', true],
            'production only - no match' => ['["production"]', 'dev', false],
            'production only - no match staging' => ['["production"]', 'staging', false],

            // Multiple environments
            'dev+staging - match dev' => ['["dev","staging"]', 'dev', true],
            'dev+staging - match staging' => ['["dev","staging"]', 'staging', true],
            'dev+staging - no match prod' => ['["dev","staging"]', 'production', false],

            // null/empty current env = match all (env not configured)
            'specific envs but null current' => ['["production"]', null, true],
            'specific envs but empty current' => ['["production"]', '', true],

            // Invalid JSON
            'invalid json' => ['not-json', 'dev', true],
        ];
    }

    // =========================================================================
    // matchesUri tests
    // =========================================================================

    public function testMatchesUriEmptyPatterns(): void
    {
        $snippet = $this->createSnippetStub(null, null);

        $result = $this->matchesUri->invoke($this->service, $snippet, 'any-page');
        $this->assertTrue($result, 'null uriPattern should match all pages');
    }

    public function testMatchesUriEmptyStringPattern(): void
    {
        $snippet = $this->createSnippetStub(null, '');

        $result = $this->matchesUri->invoke($this->service, $snippet, 'any-page');
        $this->assertTrue($result, 'empty uriPattern should match all pages');
    }

    public function testMatchesUriMultiplePatterns(): void
    {
        $snippet = $this->createSnippetStub(null, "checkout\ncontact\nproducts/*");

        $this->assertTrue($this->matchesUri->invoke($this->service, $snippet, 'checkout'));
        $this->assertTrue($this->matchesUri->invoke($this->service, $snippet, 'products/shoes'));
        $this->assertFalse($this->matchesUri->invoke($this->service, $snippet, 'blog'));
    }

    public function testMatchesUriBlankLinesIgnored(): void
    {
        $snippet = $this->createSnippetStub(null, "checkout\n\n\ncontact");

        $this->assertTrue($this->matchesUri->invoke($this->service, $snippet, 'checkout'));
        $this->assertTrue($this->matchesUri->invoke($this->service, $snippet, 'contact'));
        $this->assertFalse($this->matchesUri->invoke($this->service, $snippet, 'random-page'));
    }

    // =========================================================================
    // Additional edge case tests
    // =========================================================================

    public function testUriMatchesPatternWithLongInput(): void
    {
        $longUri = str_repeat('a/', 500) . 'page';
        $result = $this->uriMatchesPattern->invoke($this->service, $longUri, str_repeat('a/', 500) . '*');
        $this->assertTrue($result);
    }

    public function testMatchesUriWithCRLFLineEndings(): void
    {
        $snippet = $this->createSnippetStub(null, "checkout\r\nproducts/*\r\ncontact");

        $this->assertTrue($this->matchesUri->invoke($this->service, $snippet, 'checkout'));
        $this->assertTrue($this->matchesUri->invoke($this->service, $snippet, 'products/shoes'));
        $this->assertTrue($this->matchesUri->invoke($this->service, $snippet, 'contact'));
        $this->assertFalse($this->matchesUri->invoke($this->service, $snippet, 'blog'));
    }

    public function testUriMatchesPatternRegexMetaChars(): void
    {
        $result = $this->uriMatchesPattern->invoke($this->service, 'file.html', 'file.html');
        $this->assertTrue($result);

        // The dot should not match any char (it's escaped by preg_quote)
        $result = $this->uriMatchesPattern->invoke($this->service, 'fileXhtml', 'file.html');
        $this->assertFalse($result);
    }

    public function testMatchesEnvironmentWithMultipleSelected(): void
    {
        $snippet = $this->createSnippetStub('["dev","staging"]');

        $this->assertTrue($this->matchesEnvironment->invoke($this->service, $snippet, 'dev'));
        $this->assertTrue($this->matchesEnvironment->invoke($this->service, $snippet, 'staging'));
        $this->assertFalse($this->matchesEnvironment->invoke($this->service, $snippet, 'production'));
    }

    // =========================================================================
    // Snippet constant tests
    // =========================================================================

    public function testSnippetPositionConstants(): void
    {
        $this->assertSame('headBegin', Snippet::POSITION_HEAD_BEGIN);
        $this->assertSame('headEnd', Snippet::POSITION_HEAD_END);
        $this->assertSame('bodyBegin', Snippet::POSITION_BODY_BEGIN);
        $this->assertSame('bodyEnd', Snippet::POSITION_BODY_END);
        $this->assertCount(4, Snippet::POSITIONS);
    }

    public function testSnippetDefaultEnvironments(): void
    {
        $this->assertContains('dev', Snippet::DEFAULT_ENVIRONMENTS);
        $this->assertContains('staging', Snippet::DEFAULT_ENVIRONMENTS);
        $this->assertContains('production', Snippet::DEFAULT_ENVIRONMENTS);
    }

    // =========================================================================
    // Helper: create a lightweight snippet stub for testing
    // =========================================================================

    /**
     * Create a stub object with getEnvironments() and getUriPatterns() for testing
     * without requiring Craft runtime (Element base class).
     */
    private function createSnippetStub(?string $environments = null, ?string $uriPattern = null): object
    {
        return new class($environments, $uriPattern) {
            public ?string $environments;
            public ?string $uriPattern;
            public string $position = 'headEnd';
            public string $code = '';

            public function __construct(?string $environments, ?string $uriPattern = '__default__')
            {
                $this->environments = $environments;
                $this->uriPattern = $uriPattern === '__default__' ? null : $uriPattern;
            }

            public function getEnvironments(): array
            {
                if ($this->environments === null || $this->environments === '') {
                    return [];
                }
                $decoded = json_decode($this->environments, true);
                if (!is_array($decoded)) {
                    return [];
                }
                return array_values(array_filter($decoded, 'is_string'));
            }

            public function getUriPatterns(): array
            {
                if ($this->uriPattern === null || $this->uriPattern === '') {
                    return [];
                }
                return array_values(array_filter(array_map('trim', explode("\n", $this->uriPattern))));
            }
        };
    }
}
