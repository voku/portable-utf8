<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;
use voku\helper\UTF8 as u;

/**
 * Class Utf8StrcspnTest
 *
 * @internal
 */
final class Utf8StrcspnTest extends \PHPUnit\Framework\TestCase
{
    public function testNoCharlist()
    {
        $str = 'iñtërnâtiônàlizætiøn';
        static::assertSame(20, u::strcspn($str, ''));
    }

    public function testEmptyInput()
    {
        $str = '';
        static::assertSame(0, u::strcspn($str, "\n"));
    }

    public function testNoMatchSingleByteSearch()
    {
        $str = 'iñtërnâtiônàlizætiøn';
        static::assertSame(2, u::strcspn($str, 't'));
    }

    public function testNoMatchSingleByteSearchAndOffset()
    {
        $str = 'iñtërnâtiônàlizætiøn';
        static::assertSame(6, u::strcspn($str, 't', 10));
    }

    public function testNoMatchSingleByteSearchAndOffsetAndLength()
    {
        $str = 'iñtërnâtiônàlizætiøn';
        static::assertSame(1, u::strcspn($str, 'ñ', 0, 5));

        $str = 'iñtërnâtiônàlizætiøn';
        static::assertSame(5, u::strcspn($str, 'ø', 1, 5));
    }

    public function testCompareStrcspn()
    {
        $str = 'aeioustr';
        static::assertSame(\strcspn($str, 'tr'), u::strcspn($str, 'tr'));
    }

    public function testMatchAscii()
    {
        $str = 'internationalization';
        static::assertSame(\strcspn($str, 'a'), u::strcspn($str, 'a'));
    }

    public function testCompatibleWithPhpNativeFunction()
    {
        $str = '';
        static::assertSame(\strcspn($str, 'a'), u::strcspn($str, 'a'));

        // ---

        $str = 'internationalization';
        static::assertSame(\strcspn($str, ''), u::strcspn($str, ''));

        // ---

        $str = 'internationalization';
        static::assertSame(\strcspn($str, 't', 19), u::strcspn($str, 't', 19));
    }

    public function testLinefeed()
    {
        $str = "i\nñtërnâtiônàlizætiøn";
        static::assertSame(3, u::strcspn($str, 't'));
    }

    public function testLinefeedMask()
    {
        $str = "i\nñtërnâtiônàlizætiøn";
        static::assertSame(1, u::strcspn($str, "\n"));
    }

    public function testNoMatchMultiByteSearch()
    {
        $str = 'iñtërnâtiônàlizætiøn';
        static::assertSame(6, u::strcspn($str, 'â'));
    }

    public function testCompareStrspn()
    {
        $str = 'aeioustr';
        static::assertSame(UTF8::strcspn($str, 'tr'), \strcspn($str, 'tr'));
    }
}
