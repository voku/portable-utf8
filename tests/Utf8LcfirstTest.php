<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8 as u;

/**
 * Class Utf8LcfirstTest
 *
 * @internal
 */
final class Utf8LcfirstTest extends \PHPUnit\Framework\TestCase
{
    public function testLcfirst()
    {
        $str = 'ÑTËRNÂTIÔNÀLIZÆTIØN';
        $lcfirst = 'ñTËRNÂTIÔNÀLIZÆTIØN';
        static::assertSame($lcfirst, u::lcfirst($str));
    }

    public function testLcfirstUpper()
    {
        $str = 'ñTËRNÂTIÔNÀLIZÆTIØN';
        $lcfirst = 'ñTËRNÂTIÔNÀLIZÆTIØN';
        static::assertSame($lcfirst, u::lcfirst($str));
    }

    public function testEmptyString()
    {
        $str = '';
        static::assertSame('', u::lcfirst($str));
    }

    public function testOneChar()
    {
        $str = 'Ñ';
        $lcfirst = 'ñ';
        static::assertSame($lcfirst, u::lcfirst($str));
    }

    public function testLinefeed()
    {
        $str = "ÑTËRN\nâtiônàlizætiøn";
        $lcfirst = "ñTËRN\nâtiônàlizætiøn";
        static::assertSame($lcfirst, u::lcfirst($str));
    }
}
