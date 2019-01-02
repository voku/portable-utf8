<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;
use voku\helper\UTF8 as u;

/**
 * Class Utf8StrrposTest
 *
 * @internal
 */
final class Utf8StrrposTest extends \PHPUnit\Framework\TestCase
{
    public function testUtf8()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        static::assertSame(17, u::strrpos($str, 'i'));
    }

    public function testUtf8Offset()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        static::assertSame(19, u::strrpos($str, 'n', 11));
    }

    public function testUtf8Invalid()
    {
        $str = "Iñtërnâtiôn\xE9àlizætiøn";
        static::assertSame(15, u::strrpos($str, 'æ', 0, 'UTF-8', true));
    }

    public function testUtf8WithCodePoint()
    {
        $str = "I*ñtërnâtiôn\xE9àlizætiøn";
        static::assertSame(1, u::strrpos($str, 42, 0, 'UTF-8', true));
    }

    public function testAscii()
    {
        $str = 'ABC ABC';
        static::assertSame(5, u::strrpos($str, 'B'));
    }

    public function testVsStrpos()
    {
        $str = 'ABC 123 ABC';
        static::assertSame(\strrpos($str, 'B'), u::strrpos($str, 'B'));
        if (UTF8::getSupportInfo('mbstring_func_overload') !== true) {
            // strrpos() is not working as expected with overload ...
            static::assertSame(\strrpos($str, 1), u::strrpos($str, 1));
        }

        $str = 'ABC * ABC';
        static::assertSame(\strrpos($str, 'B'), u::strrpos($str, 'B'));
        if (UTF8::getSupportInfo('mbstring_func_overload') !== true) {
            // strrpos() is not working as expected with overload ...
            static::assertSame(\strrpos($str, 42), u::strrpos($str, 42));
        }
    }

    public function testEmptyStr()
    {
        $str = '';
        static::assertFalse(u::strrpos($str, 'x'));
    }

    public function testLinefeed()
    {
        $str = "Iñtërnâtiônàlizætiø\nn";
        static::assertSame(17, u::strrpos($str, 'i'));
    }

    public function testLinefeedSearch()
    {
        $str = "Iñtërnâtiônàlizætiø\nn";
        static::assertSame(19, u::strrpos($str, "\n"));
    }
}
