<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8UcwordsTest
 */
class Utf8UcwordsTest extends PHPUnit_Framework_TestCase
{
  public function test_ucword()
  {
    $str = 'iñtërnâtiônàlizætiøn';
    $ucwords = 'Iñtërnâtiônàlizætiøn';
    self::assertSame($ucwords, u::ucwords($str));
  }

  public function test_ucwords()
  {
    $str = 'iñt ërn âti ônà liz æti øn';
    $ucwords = 'Iñt Ërn Âti Ônà Liz Æti Øn';
    self::assertSame($ucwords, u::ucwords($str));
  }

  public function test_ucwords_newline()
  {
    $str = "iñt ërn âti\n ônà liz æti  øn";
    $ucwords = "Iñt Ërn Âti\n Ônà Liz Æti  Øn";
    self::assertSame($ucwords, u::ucwords($str));
  }

  public function test_empty_string()
  {
    $str = '';
    $ucwords = '';
    self::assertSame($ucwords, u::ucwords($str));
  }

  public function test_one_char()
  {
    $str = 'ñ';
    $ucwords = 'Ñ';
    self::assertSame($ucwords, u::ucwords($str));
  }

  public function test_linefeed()
  {
    $str = "iñt ërn âti\n ônà liz æti øn";
    $ucwords = "Iñt Ërn Âti\n Ônà Liz Æti Øn";
    self::assertSame($ucwords, u::ucwords($str));
  }
}
