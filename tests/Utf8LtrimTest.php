<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8 as u;

/**
 * Class Utf8LtrimTest
 *
 * @internal
 */
final class Utf8LtrimTest extends \PHPUnit\Framework\TestCase
{
    public function testTrim()
    {
        $str = '　中文空白　 ';
        $trimmed = '中文空白　 ';
        static::assertSame($trimmed, u::ltrim($str)); // ltrim() failed here

        $str = ' 𩸽 exotic test ホ 𩸽 ';
        $trimmed = '𩸽 exotic test ホ 𩸽 ';
        static::assertSame($trimmed, u::ltrim($str));
        static::assertSame($trimmed, \ltrim($str));

        $str = ' 𩸽 exotic test ホ 𩸽 ';
        $trimmed = 'exotic test ホ 𩸽 ';
        static::assertSame($trimmed, u::ltrim($str, '𩸽 '));
        static::assertSame($trimmed, \ltrim($str, '𩸽 '));

        $str = 'ñtërnâtiônàlizætiøn';
        $trimmed = 'tërnâtiônàlizætiøn';
        static::assertSame($trimmed, u::ltrim($str, 'ñ'));
        static::assertSame($trimmed, \ltrim($str, 'ñ'));

        $str = '//ñtërnâtiônàlizætiøn//';
        $trimmed = 'ñtërnâtiônàlizætiøn//';
        static::assertSame($trimmed, u::ltrim($str, '/'));
        static::assertSame($trimmed, \ltrim($str, '/'));
    }

    public function testNoTrim()
    {
        $str = ' Iñtërnâtiônàlizætiøn';
        $trimmed = ' Iñtërnâtiônàlizætiøn';
        static::assertSame($trimmed, u::ltrim($str, 'ñ'));
        static::assertSame($trimmed, \ltrim($str, 'ñ'));
    }

    public function testEmptyString()
    {
        $str = '';
        $trimmed = '';
        static::assertSame($trimmed, u::ltrim($str));
        static::assertSame($trimmed, \ltrim($str));
    }

    public function testForwardSlash()
    {
        $str = '/Iñtërnâtiônàlizætiøn';
        $trimmed = 'Iñtërnâtiônàlizætiøn';
        static::assertSame($trimmed, u::ltrim($str, '/'));
        static::assertSame($trimmed, \ltrim($str, '/'));
    }

    public function testNegateCharClass()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $trimmed = 'Iñtërnâtiônàlizætiøn';
        static::assertSame($trimmed, u::ltrim($str, '^s'));
        static::assertSame($trimmed, \ltrim($str, '^s'));
    }

    public function testLinefeed()
    {
        $str = "ñ\nñtërnâtiônàlizætiøn";
        $trimmed = "\nñtërnâtiônàlizætiøn";
        static::assertSame($trimmed, u::ltrim($str, 'ñ'));
        static::assertSame($trimmed, \ltrim($str, 'ñ'));
    }

    public function testLinefeedMask()
    {
        $str = "ñ\nñtërnâtiônàlizætiøn";
        $trimmed = 'tërnâtiônàlizætiøn';
        static::assertSame($trimmed, u::ltrim($str, "ñ\n"));
        static::assertSame($trimmed, \ltrim($str, "ñ\n"));
    }

    public function testLtrimWithCharacter0()
    {
        $str = "007";
        $trimmed = '7';
        static::assertSame($trimmed, u::ltrim($str, "0"));
        static::assertSame($trimmed, \ltrim($str, "0"));
    }
}
