<?php

declare(strict_types=1);

use voku\helper\UTF8 as u;

/**
 * Class Utf8SubstrTest
 *
 * @internal
 */
final class Utf8SubstrTest extends \PHPUnit\Framework\TestCase
{
    public function testUtf8()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        static::assertSame('Iñ', u::substr($str, 0, 2));
    }

    public function testUtf8Two()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        static::assertSame('të', u::substr($str, 2, 2));
    }

    public function testUtf8Zero()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        static::assertSame('Iñtërnâtiônàlizætiøn', u::substr($str, 0));
    }

    public function testUtf8ZeroZero()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        static::assertSame('', u::substr($str, 0, 0));
    }

    public function testStartGreatThanLength()
    {
        $str = 'Iñt';
        static::assertEmpty(u::substr($str, 4));
    }

    public function testCompareStartGreatThanLength()
    {
        $str = 'abc';
        static::assertSame((string) \substr($str, 4), (string) u::substr($str, 4));
    }

    public function testLengthBeyondString()
    {
        $str = 'Iñt';
        static::assertSame('ñt', u::substr($str, 1, 5));
    }

    public function testCompareLengthBeyondString()
    {
        $str = 'abc';
        static::assertSame(\substr($str, 1, 5), u::substr($str, 1, 5));
    }

    public function testStartNegative()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        static::assertSame('tiøn', u::substr($str, -4));
    }

    public function testLengthNegative()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        static::assertSame('nàlizæti', u::substr($str, 10, -2));
    }

    public function testStartLengthNegative()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        static::assertSame('ti', u::substr($str, -4, -2));
    }

    public function testLinefeed()
    {
        $str = "Iñ\ntërnâtiônàlizætiøn";
        static::assertSame("ñ\ntër", u::substr($str, 1, 5));
    }

    public function testLongLength()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        static::assertSame('Iñtërnâtiônàlizætiøn', u::substr($str, 0, 15536));
    }
}
