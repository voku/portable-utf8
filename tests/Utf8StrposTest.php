<?php

declare(strict_types=1);

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrposTest
 *
 * @internal
 */
final class Utf8StrposTest extends \PHPUnit\Framework\TestCase
{
    public function testUtf8()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        static::assertSame(6, u::strpos($str, 'â'));
        static::assertSame(6, u::stripos($str, 'Â'));
    }

    public function testUtf8Offset()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        static::assertSame(19, u::strpos($str, 'n', 11));
        static::assertSame(19, u::stripos($str, 'N', 11));
    }

    public function testUtf8Invalid()
    {
        $str = "Iñtërnâtiôn\xE9àlizætiøn";
        static::assertSame(15, u::strpos($str, 'æ', 0, 'UTF-8', true));
        static::assertSame(15, u::stripos($str, 'æ', 0, 'UTF-8', true));
        static::assertSame(15, u::strpos($str, 'æ', 0, true, true));
        static::assertSame(15, u::stripos($str, 'æ', 0, false, true));
    }

    public function testAscii()
    {
        $str = 'ABC 123';
        static::assertSame(1, u::strpos($str, 'B'));
        static::assertSame(1, u::stripos($str, 'b'));
    }

    public function testVsStrpos()
    {
        $str = 'ABC 123 ABC';
        static::assertSame(\strpos($str, 'B', 3), u::strpos($str, 'B', 3));
        static::assertSame(\stripos($str, 'b', 3), u::stripos($str, 'b', 3));
    }

    public function testEmptyStr()
    {
        $str = '';
        static::assertFalse(u::strpos($str, 'x'));
        static::assertFalse(u::stripos($str, 'x'));
    }
}
