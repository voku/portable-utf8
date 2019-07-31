<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrWordwrapTest
 *
 * @internal
 */
final class Utf8StrWordwrapTest extends \PHPUnit\Framework\TestCase
{
    public function testOrig()
    {
        $str = '';
        static::assertSame(\wordwrap($str), u::wordwrap($str));

        $str = 'test foo';
        static::assertSame(\wordwrap($str, 1, '<br>', true), u::wordwrap($str, 1, '<br>', true));
    }

    public function testNoArgsEmptyString()
    {
        $str = '';
        $wrapped = '';
        static::assertSame($wrapped, u::wordwrap($str));
    }

    public function testNoArgs()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $wrapped = 'Iñtërnâtiônàlizætiøn';
        static::assertSame($wrapped, u::wordwrap($str));
    }

    public function testBreakAtTen()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $wrapped = "Iñtërnâtiô\nnàlizætiøn";
        static::assertSame($wrapped, u::wordwrap($str, 10, "\n", true));
    }

    public function testBreakWithBreak()
    {
        $str = 'Iñtër' . "\n" . 'n' . "#\n#" . 'â' . "#\n#" . 't#i#ô#n#à#lizætiøn';
        $wrapped = 'Iñtër' . "\n" . 'n#' . "\n" . '#â#' . "\n" . '#t#i#ô#n#à' . "\n" . '#lizætiøn';
        static::assertSame($wrapped, u::wordwrap($str, 10, "\n", true));
    }

    public function testBreakAtOne()
    {
        $str = 'ñ';
        $wrapped = 'ñ';
        static::assertSame($wrapped, u::wordwrap($str, 1, "\n", true));
    }

    public function testEmptyBreak()
    {
        $str = 'ñ';
        $wrapped = '';
        static::assertSame($wrapped, u::wordwrap($str, 1, '', true));
    }

    public function testBreakSpecial()
    {
        $str = 'ñ-ñ';
        $wrapped = 'ñ-ñ';
        static::assertSame($wrapped, u::wordwrap($str, 1, '-', true));
    }

    public function testBreakAtOneWithEmptyString()
    {
        $str = 'ñ ñ';
        $wrapped = 'ñ' . "\n" . 'ñ';
        static::assertSame($wrapped, u::wordwrap($str, 1, "\n", true));
    }

    public function testBreakAtTwoBr()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $wrapped = 'Iñ<br>të<br>rn<br>ât<br>iô<br>nà<br>li<br>zæ<br>ti<br>øn';
        static::assertSame($wrapped, u::wordwrap($str, 2, '<br>', true));
    }

    public function testBreakAtTenInt()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $wrapped = 'Iñtërnâtiô 우리をあöä nàlizætiøn';
        static::assertSame($wrapped, u::wordwrap($str, 10, ' 우리をあöä ', true));
    }
}
