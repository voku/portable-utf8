<?php

declare(strict_types=1);

use voku\helper\UTF8;

/**
 * Class Utf8AsciiTest
 *
 * @internal
 */
final class Utf8AsciiTest extends \PHPUnit\Framework\TestCase
{
    public function testUtf8()
    {
        $str = 'testiñg';
        static::assertFalse(UTF8::is_ascii($str));
    }

    public function testAscii()
    {
        $str = 'testing';
        static::assertTrue(UTF8::is_ascii($str));
    }

    public function testInvalidChar()
    {
        $str = "tes\xe9ting";
        static::assertFalse(UTF8::is_ascii($str));
    }

    public function testEmptyStr()
    {
        $str = '';
        static::assertTrue(UTF8::is_ascii($str));
    }

    public function testNewLine()
    {
        $str = "a\nb\nc";
        static::assertTrue(UTF8::is_ascii($str));
    }

    public function testTab()
    {
        $str = "a\tb\tc";
        static::assertTrue(UTF8::is_ascii($str));
    }

    public function testUtf8ToAscii()
    {
        $str = 'testiñg';
        static::assertSame('testing', UTF8::to_ascii($str));
    }

    public function testAsciiToAscii()
    {
        $str = 'testing';
        static::assertSame('testing', UTF8::to_ascii($str));
    }

    public function testInvalidCharToAscii()
    {
        $str = "tes\xe9ting";
        static::assertSame('testing', UTF8::to_ascii($str));
    }

    public function testEmptyStrToAscii()
    {
        $str = '';
        static::assertSame('', UTF8::to_ascii($str));
    }

    public function testNulAndNon7Bit()
    {
        $str = "a\x00ñ\x00c";
        static::assertSame('anc', UTF8::to_ascii($str));
    }

    public function testNul()
    {
        $str = "a\x00b\x00c";
        static::assertSame('abc', UTF8::to_ascii($str));
    }

    public function testNewLineToAscii()
    {
        $str = "a\nb\nc";
        static::assertSame("a\nb\nc", UTF8::to_ascii($str));
    }

    public function testTabToAscii()
    {
        $str = "a\tb\tc";
        static::assertSame("a\tb\tc", UTF8::to_ascii($str));
    }
}
