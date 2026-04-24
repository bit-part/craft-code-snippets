<?php

namespace bitpart\codesnippets\tests\unit\elements;

use bitpart\codesnippets\elements\Snippet;
use PHPUnit\Framework\TestCase;

/**
 * Verifies the consistency between the position constants, POSITIONS array,
 * POSITION_META map, and the labels returned by getPositionOptions().
 *
 * These tests catch regressions where someone adds a new position to one
 * constant without updating the others.
 */
class SnippetMetaTest extends TestCase
{
    public function testPositionsAndMetaKeysMatch(): void
    {
        $metaKeys = array_keys(Snippet::POSITION_META);
        sort($metaKeys);

        $positions = Snippet::POSITIONS;
        sort($positions);

        $this->assertSame($positions, $metaKeys, 'POSITIONS and POSITION_META keys must match');
    }

    public function testPositionMetaShapeIsValid(): void
    {
        foreach (Snippet::POSITION_META as $position => $meta) {
            $this->assertContains($position, Snippet::POSITIONS, "{$position} should be in POSITIONS");
            $this->assertArrayHasKey('tagPattern', $meta);
            $this->assertArrayHasKey('insertBefore', $meta);
            $this->assertIsString($meta['tagPattern']);
            $this->assertIsBool($meta['insertBefore']);
            // Pattern must be a valid PCRE
            $this->assertNotFalse(@preg_match($meta['tagPattern'], ''), "{$position} tagPattern must be a valid regex");
        }
    }

    public function testDefaultEnvironmentsContainsExpected(): void
    {
        $this->assertContains('dev', Snippet::DEFAULT_ENVIRONMENTS);
        $this->assertContains('staging', Snippet::DEFAULT_ENVIRONMENTS);
        $this->assertContains('production', Snippet::DEFAULT_ENVIRONMENTS);
        $this->assertCount(3, Snippet::DEFAULT_ENVIRONMENTS);
    }

    public function testPositionConstants(): void
    {
        $this->assertSame('headBegin', Snippet::POSITION_HEAD_BEGIN);
        $this->assertSame('headEnd', Snippet::POSITION_HEAD_END);
        $this->assertSame('bodyBegin', Snippet::POSITION_BODY_BEGIN);
        $this->assertSame('bodyEnd', Snippet::POSITION_BODY_END);
    }
}
