<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrReplaceTest
 */
class Utf8StrReplaceTest extends \PHPUnit\Framework\TestCase
{
  public function test_replace()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'Iñtërnâtiônàlisetiøn';
    self::assertSame($replaced, u::str_replace('lizæ', 'lise', $str));

    $str = 'Привет мир';
    $replaced = 'Пока мир';
    self::assertSame($replaced, u::str_replace('Привет', 'Пока', $str));
  }

  public function test_replace_no_match()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'Iñtërnâtiônàlizætiøn';
    self::assertSame($replaced, u::str_replace('foo', 'bar', $str));
  }

  public function test_empty_string()
  {
    $str = '';
    $replaced = '';
    self::assertSame($replaced, u::str_replace('foo', 'bar', $str));
  }

  public function test_empty_search()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'Iñtërnâtiônàlizætiøn';
    self::assertSame($replaced, u::str_replace('', 'x', $str));
  }

  public function test_replace_count()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'IñtërXâtiôXàlizætiøX';
    self::assertSame($replaced, u::str_replace('n', 'X', $str, $count));
    self::assertSame(3, $count);
  }

  public function test_replace_different_search_replace_length()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'IñtërXXXâtiôXXXàlizætiøXXX';
    self::assertSame($replaced, u::str_replace('n', 'XXX', $str));
  }

  public function test_replace_array_ascii_search()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'Iñyërxâyiôxàlizæyiøx';
    self::assertSame(
        $replaced,
        u::str_replace(
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
        u::str_replace(
            [
                'ñ',
                'ô',
                'ø',
                'æ',
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
        u::str_replace(
            [
                'ñ',
                'ô',
                'ø',
                'æ',
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
        u::str_replace(
            [
                'ñ',
                'ô',
                'ø',
                'æ',
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
    self::assertSame($replaced, u::str_replace('lizæ', 'lise', $str));
  }

  public function test_replace_linefeed_search()
  {
    $str = "Iñtërnâtiônàli\nzætiøn";
    $replaced = 'Iñtërnâtiônàlisetiøn';
    self::assertSame($replaced, u::str_replace("li\nzæ", 'lise', $str));
  }
}
