<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrSplitTest
 *
 * @internal
 */
final class Utf8StrSplitTest extends \PHPUnit\Framework\TestCase
{
    public function testSplitOneChar()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $array = [
            'I',
            'ñ',
            't',
            'ë',
            'r',
            'n',
            'â',
            't',
            'i',
            'ô',
            'n',
            'à',
            'l',
            'i',
            'z',
            'æ',
            't',
            'i',
            'ø',
            'n',
        ];

        static::assertSame($array, u::str_split($str));
    }

    public function testSplitFiveChars()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $array = [
            'Iñtër',
            'nâtiô',
            'nàliz',
            'ætiøn',
        ];

        static::assertSame($array, u::str_split($str, 5));
    }

    public function testSplitSixChars()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $array = [
            'Iñtërn',
            'âtiônà',
            'lizæti',
            'øn',
        ];

        static::assertSame($array, u::str_split($str, 6));
    }

    public function testSplitLong()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $array = [
            'Iñtërnâtiônàlizætiøn',
        ];

        static::assertSame($array, u::str_split($str, 40));
    }

    public function testSplitNewline()
    {
        $str = "\nIñtërn\nâtiônàl\nizætiøn\n\n";
        $array = [
            "\n",
            'I',
            'ñ',
            't',
            'ë',
            'r',
            'n',
            "\n",
            'â',
            't',
            'i',
            'ô',
            'n',
            'à',
            'l',
            "\n",
            'i',
            'z',
            'æ',
            't',
            'i',
            'ø',
            'n',
            "\n",
            "\n",
        ];

        static::assertSame($array, u::str_split($str));
    }

    public function testSplitZeroLength()
    {
        $str = 'Iñtë';
        $array = [];

        static::assertSame($array, u::str_split($str, 0));
    }

    public function testSplitOneLength()
    {
        $str = 'Iñtë';
        $array = [
            'I',
            'ñ',
            't',
            'ë',
        ];

        static::assertSame($array, u::str_split($str, 1));
    }
}
