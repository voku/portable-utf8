<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrrevTest
 *
 * @internal
 */
final class Utf8StrrevTest extends \PHPUnit\Framework\TestCase
{
    public function testReverse()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $rev = 'nøitæzilànôitânrëtñI';
        static::assertSame($rev, u::strrev($str));
    }

    public function testEmptyStr()
    {
        $str = '';
        $rev = '';
        static::assertSame($rev, u::strrev($str));
    }

    public function testLinefeed()
    {
        $str = "Iñtërnâtiôn\nàlizætiøn";
        $rev = "nøitæzilà\nnôitânrëtñI";
        static::assertSame($rev, u::strrev($str));
    }
}
