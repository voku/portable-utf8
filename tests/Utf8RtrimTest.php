<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8 as u;

/**
 * Class Utf8RtrimTest
 *
 * @internal
 */
final class Utf8RtrimTest extends \PHPUnit\Framework\TestCase
{
    public function testTrim()
    {
        $str = '　中文空白　 ';
        $trimmed = '　中文空白';
        static::assertSame($trimmed, u::rtrim($str)); // rtrim() failed here

        $str = 'Iñtërnâtiônàlizætiø';
        $trimmed = 'Iñtërnâtiônàlizæti';
        static::assertSame($trimmed, u::rtrim($str, 'ø'));
        static::assertSame($trimmed, \rtrim($str, 'ø'));

        $str = '//Iñtërnâtiônàlizætiø//';
        $trimmed = '//Iñtërnâtiônàlizætiø';
        static::assertSame($trimmed, u::rtrim($str, '/'));
        static::assertSame($trimmed, \rtrim($str, '/'));
    }

    public function testNoTrim()
    {
        $str = 'Iñtërnâtiônàlizætiøn ';
        $trimmed = 'Iñtërnâtiônàlizætiøn ';
        static::assertSame($trimmed, u::rtrim($str, 'ø'));
        static::assertSame($trimmed, \rtrim($str, 'ø'));
    }

    public function testEmptyString()
    {
        $str = '';
        $trimmed = '';
        static::assertSame($trimmed, u::rtrim($str));
        static::assertSame($trimmed, \rtrim($str));
    }

    public function testLinefeed()
    {
        $str = "Iñtërnâtiônàlizætiø\nø";
        $trimmed = "Iñtërnâtiônàlizætiø\n";
        static::assertSame($trimmed, u::rtrim($str, 'ø'));
        static::assertSame($trimmed, \rtrim($str, 'ø'));
    }

    public function testLinefeedMask()
    {
        $str = "Iñtërnâtiônàlizætiø\nø";
        $trimmed = 'Iñtërnâtiônàlizæti';
        static::assertSame($trimmed, u::rtrim($str, "ø\n"));
        static::assertSame($trimmed, \rtrim($str, "ø\n"));
    }

    public function testRtrimWithCharacter0()
    {
        $str = "00700";
        $trimmed = '007';
        static::assertSame($trimmed, u::rtrim($str, "0"));
        static::assertSame($trimmed, \rtrim($str, "0"));
    }
}
