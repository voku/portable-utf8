<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;

/**
 * Class Utf8StrToUpperTest
 *
 * @internal
 */
final class Utf8StrToUpperTest extends \PHPUnit\Framework\TestCase
{
    public function testUpper()
    {
        $str = 'iñtërnâtiônàlizætiøn--ἑλληνικὴ';
        $upper = 'IÑTËRNÂTIÔNÀLIZÆTIØN--ἙΛΛΗΝΙΚῊ';
        static::assertSame(UTF8::strtoupper($str), $upper);
    }

    public function testEmptyString()
    {
        $str = '';
        $upper = '';
        static::assertSame(UTF8::strtoupper($str), $upper);
    }
}
