<?php

declare(strict_types=1);

use voku\helper\UTF8 as u;

/**
 * Class Utf8OrdTest
 *
 * @internal
 */
final class Utf8OrdTest extends \PHPUnit\Framework\TestCase
{
    public function testEmptyStr()
    {
        $str = '';
        static::assertSame(0, u::ord($str));
    }

    public function testAsciiChar()
    {
        $str = 'a';
        static::assertSame(97, u::ord($str));
    }

    public function test2ByteChar()
    {
        $str = 'ñ';
        static::assertSame(241, u::ord($str));
    }

    public function test3ByteChar()
    {
        $str = '₧';
        static::assertSame(8359, u::ord($str));
    }

    public function test4ByteChar()
    {
        $str = "\xF0\x90\x8C\xBC";
        static::assertSame(66364, u::ord($str));
    }
}
