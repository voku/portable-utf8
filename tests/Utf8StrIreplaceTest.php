<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;
use voku\helper\UTF8 as u;

/**
 * Class Utf8StrIreplaceTest
 *
 * @internal
 */
final class Utf8StrIreplaceTest extends \PHPUnit\Framework\TestCase
{
    public function testReplace()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'Iñtërnâtiônàlisetiøn';
        static::assertSame($replaced, u::str_ireplace('lIzÆ', 'lise', $str));

        $str = ['Iñtërnâtiônàlizætiøn', 'Iñtërnâtiônàlisetiøn', 'foobar', '', "\0", ' '];
        $replaced = ['Iñtërnâtiônàlisetiøn', 'Iñtërnâtiônàlisetiøn', 'foobar', '', "\0", ' '];
        static::assertSame($replaced, u::str_ireplace('lIzÆ', 'lise', $str));

        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'Iñtërnâtiônàlisetiøn';
        static::assertSame($replaced, UTF8::str_ireplace('lIzÆ', 'lise', $str));
    }

    public function testReplaceNoMatch()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'Iñtërnâtiônàlizætiøn';
        static::assertSame($replaced, u::str_ireplace('foo', 'bar', $str));
    }

    public function testEmptyString()
    {
        $str = '';
        $replaced = '';
        static::assertSame($replaced, u::str_ireplace('foo', 'bar', $str));
    }

    public function testEmptySearch()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'Iñtërnâtiônàlizætiøn';
        static::assertSame($replaced, u::str_ireplace('', 'x', $str));
    }

    public function testReplaceCount()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'IñtërXâtiôXàlizætiøX';
        static::assertSame($replaced, u::str_ireplace('n', 'X', $str, $count));
        static::assertSame(3, $count);
    }

    public function testReplaceDifferentSearchReplaceLength()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'IñtërXXXâtiôXXXàlizætiøXXX';
        static::assertSame($replaced, u::str_ireplace('n', 'XXX', $str));
    }

    public function testReplaceArrayAsciiSearch()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'Iñyërxâyiôxàlizæyiøx';
        static::assertSame(
        $replaced,
        u::str_ireplace(
            [
                'n',
                't',
            ],
            [
                'x',
                'y',
            ],
            $str
        )
    );
    }

    public function testReplaceArrayUtf8Search()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'I?tërnâti??nàliz????ti???n';
        static::assertSame(
        u::str_ireplace(
            [
                'Ñ',
                'ô',
                'ø',
                'Æ',
            ],
            [
                '?',
                '??',
                '???',
                '????',
            ],
            $str
        ),
        $replaced
    );
    }

    public function testReplaceArrayStringReplace()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'I?tërnâti?nàliz?ti?n';
        static::assertSame(
        $replaced,
        u::str_ireplace(
            [
                'Ñ',
                'ô',
                'ø',
                'Æ',
            ],
            '?',
            $str
        )
    );
    }

    public function testReplaceArraySingleArrayReplace()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'I?tërnâtinàliztin';
        static::assertSame(
        u::str_ireplace(
            [
                'Ñ',
                'ô',
                'ø',
                'Æ',
            ],
            ['?'],
            $str
        ),
        $replaced
    );
    }

    public function testReplaceLinefeed()
    {
        $str = "Iñtërnâti\nônàlizætiøn";
        $replaced = "Iñtërnâti\nônàlisetiøn";
        static::assertSame($replaced, u::str_ireplace('lIzÆ', 'lise', $str));
    }

    public function testReplaceLinefeedArray()
    {
        $str = "Iñtërnâti\nônàlizætiøn";
        $replaced = "Iñtërnâti\n\nônàlisetiøn";
        static::assertSame($replaced, u::str_ireplace(['lIzÆ', "\n"], ['lise', "\n\n"], $str));
    }

    public function testReplaceLinefeedSearch()
    {
        $str = "Iñtërnâtiônàli\nzætiøn";
        $replaced = 'Iñtërnâtiônàlisetiøn';
        static::assertSame($replaced, u::str_ireplace("lI\nzÆ", 'lise', $str));
    }
}
