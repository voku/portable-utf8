<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrcasecmpTest
 *
 * @internal
 */
final class Utf8StrcasecmpTest extends \PHPUnit\Framework\TestCase
{
    public function testCompareEqual()
    {
        $str_x = 'iñtërnâtiônàlizætiøn';
        $str_y = 'IÑTËRNÂTIÔNÀLIZÆTIØN';
        static::assertSame(0, u::strcasecmp($str_x, $str_y));
        static::assertTrue(u::strcmp($str_x, $str_y) >= 1);

        $str_x = 'IÑTËRNÂTIÔNÀLIZÆTIØN';
        $str_y = 'IÑTËRNÂTIÔNÀLIZÆTIØN';
        static::assertSame(0, u::strcasecmp($str_x, $str_y));
        static::assertSame(0, u::strcmp($str_x, $str_y));
    }

    public function testLess()
    {
        $str_x = 'iñtërnâtiônàlizætiøn';
        $str_y = 'IÑTËRNÂTIÔÀLIZÆTIØN';
        static::assertTrue(u::strcasecmp($str_x, $str_y) > 0);
        static::assertTrue(u::strcmp($str_x, $str_y) > 0);
    }

    public function testGreater()
    {
        $str_x = 'iñtërnâtiôàlizætiøn';
        $str_y = 'IÑTËRNÂTIÔNÀLIZÆTIØN';
        static::assertTrue(u::strcasecmp($str_x, $str_y) < 0);
        static::assertTrue(u::strcmp($str_x, $str_y) > 0);
    }

    public function testEmptyX()
    {
        $str_x = '';
        $str_y = 'IÑTËRNÂTIÔNÀLIZÆTIØN';
        static::assertTrue(u::strcasecmp($str_x, $str_y) < 0);
        static::assertTrue(u::strcmp($str_x, $str_y) < 0);
    }

    public function testEmptyY()
    {
        $str_x = 'iñtërnâtiôàlizætiøn';
        $str_y = '';
        static::assertTrue(u::strcasecmp($str_x, $str_y) > 0);
        static::assertTrue(u::strcmp($str_x, $str_y) > 0);
    }

    public function testEmptyBoth()
    {
        $str_x = '';
        $str_y = '';
        static::assertTrue(u::strcasecmp($str_x, $str_y) === 0);
        static::assertTrue(u::strcmp($str_x, $str_y) === 0);
    }

    public function testLinefeed()
    {
        $str_x = "iñtërnâtiôn\nàlizætiøn";
        $str_y = "IÑTËRNÂTIÔN\nÀLIZÆTIØN";
        static::assertTrue(u::strcasecmp($str_x, $str_y) === 0);
        static::assertTrue(u::strcmp($str_x, $str_y) >= 1);
    }
}
