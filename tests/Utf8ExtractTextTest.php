<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;

final class Utf8ExtractTextTest extends \PHPUnit\Framework\TestCase
{
    public function testExtractTextTrimsLeadingWhitespaceFromUtf8Excerpt(): void
    {
        static::assertSame(
            '…a Fork of UTF8',
            UTF8::extract_text('this is only a Fork of UTF8', 'Fork', 5)
        );

        static::assertSame(
            '…Fork of UTF8, take a look at the new features.',
            UTF8::extract_text('This is only a Fork of UTF8, take a look at the new features.', 'Fork', 0)
        );
    }

    public function testExtractTextTrimsLeadingWhitespaceFromIsoExcerpt(): void
    {
        $string = 'This is only a Fork of UTF8, take a look at the new features.';

        static::assertSame(
            '…a Fork…',
            UTF8::extract_text($string, 'Fork', 5, '…', 'ISO-8859-1')
        );

        static::assertSame(
            '…Fork of UTF8, take a look at the new features.',
            UTF8::extract_text($string, 'Fork', 0, '…', 'ISO-8859-1')
        );
    }
}
