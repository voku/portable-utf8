<?php

use voku\helper\UTF8;
use voku\helper\UTF8 as u;

/**
 * Class Utf8StrIreplaceTest
 */
class Utf8StrIreplaceTest extends \PHPUnit\Framework\TestCase
{
  public function test_replace()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'Iñtërnâtiônàlisetiøn';
    self::assertSame($replaced, u::str_ireplace('lIzÆ', 'lise', $str));

    $str = ['Iñtërnâtiônàlizætiøn', 'Iñtërnâtiônàlisetiøn', 'foobar', '', "\0", ' '];
    $replaced = ['Iñtërnâtiônàlisetiøn', 'Iñtërnâtiônàlisetiøn', 'foobar', '', "\0", ' '];
    self::assertSame($replaced, u::str_ireplace('lIzÆ', 'lise', $str));
  }

  public function test_replace_no_match()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'Iñtërnâtiônàlizætiøn';
    self::assertSame($replaced, u::str_ireplace('foo', 'bar', $str));
  }

  public function test_empty_string()
  {
    $str = '';
    $replaced = '';
    self::assertSame($replaced, u::str_ireplace('foo', 'bar', $str));
  }

  public function test_empty_search()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'Iñtërnâtiônàlizætiøn';
    self::assertSame($replaced, u::str_ireplace('', 'x', $str));
  }

  public function test_replace_count()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'IñtërXâtiôXàlizætiøX';
    self::assertSame($replaced, u::str_ireplace('n', 'X', $str, $count));
    self::assertSame(3, $count);
  }

  public function test_replace_different_search_replace_length()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'IñtërXXXâtiôXXXàlizætiøXXX';
    self::assertSame($replaced, u::str_ireplace('n', 'XXX', $str));
  }

  public function test_replace_array_ascii_search()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'Iñyërxâyiôxàlizæyiøx';
    self::assertSame(
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

  public function test_replace_array_utf8_search()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'I?tërnâti??nàliz????ti???n';
    self::assertSame(
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

  public function test_replace_array_string_replace()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'I?tërnâti?nàliz?ti?n';
    self::assertSame(
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

  public function test_replace_array_single_array_replace()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'I?tërnâtinàliztin';
    self::assertSame(
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

  public function test_replace_linefeed()
  {
    $str = "Iñtërnâti\nônàlizætiøn";
    $replaced = "Iñtërnâti\nônàlisetiøn";
    self::assertSame($replaced, u::str_ireplace('lIzÆ', 'lise', $str));
  }

  public function test_replace_linefeed_array()
  {
    $str = "Iñtërnâti\nônàlizætiøn";
    $replaced = "Iñtërnâti\n\nônàlisetiøn";
    self::assertSame($replaced, u::str_ireplace(['lIzÆ', "\n"], ['lise', "\n\n"], $str));
  }

  public function test_replace_linefeed_search()
  {
    $str = "Iñtërnâtiônàli\nzætiøn";
    $replaced = 'Iñtërnâtiônàlisetiøn';
    self::assertSame($replaced, u::str_ireplace("lI\nzÆ", 'lise', $str));
  }

  public function testReplace()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'Iñtërnâtiônàlisetiøn';
    self::assertSame($replaced, UTF8::str_ireplace('lIzÆ', 'lise', $str));
  }

  public function testReplaceNoMatch()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'Iñtërnâtiônàlizætiøn';
    self::assertSame($replaced, UTF8::str_ireplace('foo', 'bar', $str));
  }

  public function testEmptyString()
  {
    $str = '';
    $replaced = '';
    self::assertSame($replaced, UTF8::str_ireplace('foo', 'bar', $str));
  }

  public function testEmptySearch()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'Iñtërnâtiônàlizætiøn';
    self::assertSame($replaced, UTF8::str_ireplace('', 'x', $str));
  }

  public function testReplaceCount()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'IñtërXâtiôXàlizætiøX';
    self::assertSame($replaced, UTF8::str_ireplace('n', 'X', $str, $count));
    self::assertSame(3, $count);
  }

  public function testReplaceDifferentSearchReplaceLength()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'IñtërXXXâtiôXXXàlizætiøXXX';
    self::assertSame($replaced, UTF8::str_ireplace('n', 'XXX', $str));
  }

  public function testReplaceArrayAsciiSearch()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'Iñyërxâyiôxàlizæyiøx';
    self::assertSame(
        $replaced,
        UTF8::str_ireplace(
            ['n', 't'],
            ['x', 'y'],
            $str
        )
    );
  }

  public function testReplaceArrayUTF8Search()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'I?tërnâti??nàliz????ti???n';
    self::assertSame(
        $replaced,
        UTF8::str_ireplace(
            ['Ñ', 'ô', 'ø', 'Æ'],
            ['?', '??', '???', '????'],
            $str
        )
    );
  }

  public function testReplaceArrayStringReplace()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'I?tërnâti?nàliz?ti?n';
    self::assertSame(
        $replaced,
        UTF8::str_ireplace(
            ['Ñ', 'ô', 'ø', 'Æ'],
            '?',
            $str
        )
    );
  }

  public function testReplaceArraySingleArrayReplace()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'I?tërnâtinàliztin';
    self::assertSame(
        $replaced,
        UTF8::str_ireplace(
            ['Ñ', 'ô', 'ø', 'Æ'],
            ['?'],
            $str
        )
    );
  }

  public function testReplaceLinefeed()
  {
    $str = "Iñtërnâti\nônàlizætiøn";
    $replaced = "Iñtërnâti\nônàlisetiøn";
    self::assertSame($replaced, UTF8::str_ireplace('lIzÆ', 'lise', $str));
  }

  public function testReplaceLinefeedSearch()
  {
    $str = "Iñtërnâtiônàli\nzætiøn";
    $replaced = 'Iñtërnâtiônàlisetiøn';
    self::assertSame($replaced, UTF8::str_ireplace("lI\nzÆ", 'lise', $str));
  }
}
