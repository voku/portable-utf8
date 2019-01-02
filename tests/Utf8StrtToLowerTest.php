<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;

/**
 * Class Utf8StrtToLowerTest
 *
 * @internal
 */
final class Utf8StrtToLowerTest extends \PHPUnit\Framework\TestCase
{
    public function testLower()
    {
        $str = 'IÑTËRNÂTIÔNÀLIZÆTIØN';
        $lower = 'iñtërnâtiônàlizætiøn';
        static::assertSame(UTF8::strtolower($str), $lower);
    }

    public function testEmptyString()
    {
        $str = '';
        $lower = '';
        static::assertSame(UTF8::strtolower($str), $lower);
    }
}
