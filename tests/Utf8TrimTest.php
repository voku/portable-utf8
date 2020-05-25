<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8 as u;

/**
 * Class Utf8TrimTest
 *
 * @internal
 */
final class Utf8TrimTest extends \PHPUnit\Framework\TestCase
{
    public function testTrim()
    {
        $str = 'ñtërnâtiônàlizætiø';
        $trimmed = 'tërnâtiônàlizæti';
        static::assertSame($trimmed, u::trim($str, 'ñø'));
    }

    public function testNoTrim()
    {
        $str = ' Iñtërnâtiônàlizætiøn ';
        $trimmed = ' Iñtërnâtiônàlizætiøn ';
        static::assertSame($trimmed, u::trim($str, 'ñø'));
    }

    public function testEmptyString()
    {
        $str = '';
        $trimmed = '';
        static::assertSame($trimmed, u::trim($str));
    }

    public function testTrimWithCharacter0()
    {
        $str = "00700";
        $trimmed = '7';
        static::assertSame($trimmed, u::trim($str, "0"));
    }
}
