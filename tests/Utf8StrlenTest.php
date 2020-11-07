<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrlenTest
 *
 * @internal
 */
final class Utf8StrlenTest extends \PHPUnit\Framework\TestCase
{
    public function testUtf8()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        static::assertSame(20, u::strlen($str));
    }

    public function testUtf8Invalid()
    {
        if (u::mbstring_loaded() === true) { // only with "mbstring"
            $str = "Iñtërnâtiôn\xE9àlizætiøn";
            static::assertSame(20, u::strlen($str, 'UTF-8', true));
        } else {
            static::markTestSkipped('only with "mbstring"');
        }
    }

    public function testAscii()
    {
        $str = 'ABC 123';
        static::assertSame(7, u::strlen($str));
    }

    public function testEmptyStr()
    {
        $str = '';
        static::assertSame(0, u::strlen($str));
    }
}
