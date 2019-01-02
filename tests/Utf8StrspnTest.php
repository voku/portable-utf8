<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrspnTest
 *
 * @internal
 */
final class Utf8StrspnTest extends \PHPUnit\Framework\TestCase
{
    public function testMatch()
    {
        $str = 'iñtërnâtiônàlizætiøn';

        static::assertSame(1, u::strspn($str, 'i'));
        static::assertSame(0, u::strspn($str, 'â'));
        static::assertSame(0, u::strspn($str, 'âë'));
        static::assertSame(3, u::strspn($str, 'itñ'));
        static::assertSame(3, u::strspn($str, 'iñt'));
        static::assertSame(11, u::strspn($str, 'âëiônñrt'));
    }

    public function testNoCharlist()
    {
        $str = 'iñtërnâtiônàlizætiøn';
        static::assertSame(0, u::strspn($str, ''));
    }

    public function testEmptyInput()
    {
        $str = '';
        static::assertSame(0, u::strspn($str, "\n"));
    }

    public function testMatchTwo()
    {
        $str = 'iñtërnâtiônàlizætiøn';
        static::assertSame(4, u::strspn($str, 'iñtë'));
    }

    public function testCompareStrspn()
    {
        $str = 'aeioustr';
        static::assertSame(\strspn($str, 'saeiou'), u::strspn($str, 'saeiou'));
    }

    public function testMatchAscii()
    {
        $str = 'internationalization';
        static::assertSame(\strspn($str, 'aeionrt'), u::strspn($str, 'aeionrt'));
    }

    public function testMaxLength()
    {
        $str = "iñtërnât\niônàlizætiøn";
        static::assertSame(5, u::strspn($str, 'âëiônñrt', 0, 5));
    }

    public function testOffset()
    {
        $str = "iñtërnât\niônàlizætiøn";
        static::assertSame(5, u::strspn($str, 'âëiônñrt', 1, 5));
    }

    public function testLinefeed()
    {
        $str = "iñtërnât\niônàlizætiøn";
        static::assertSame(8, u::strspn($str, 'âëiônñrt'));
    }

    public function testLinefeedMask()
    {
        $str = "iñtërnât\niônàlizætiøn";
        static::assertSame(12, u::strspn($str, "âëiônñrt\n"));
    }
}
