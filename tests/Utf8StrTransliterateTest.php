<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrTransliterateTest
 */
class Utf8StrTransliterateTest extends PHPUnit_Framework_TestCase
{
  public function test_utf8()
  {
    $str = 'testiñg';
    self::assertSame('testing', u::str_transliterate($str));

    $str = '  -ABC-中文空白-  ';
    self::assertSame('  -ABC-Zhong Wen Kong Bai -  ', u::str_transliterate($str));
  }

  public function test_ascii()
  {
    $str = 'testing';
    self::assertSame('testing', u::str_transliterate($str));
  }

  public function test_invalid_char()
  {
    $str = "tes\xE9ting";
    self::assertSame('testing', u::str_transliterate($str));
  }

  public function test_empty_str()
  {
    $str = '';
    self::assertEmpty(u::str_transliterate($str));
  }

  public function test_nul_and_non_7_bit()
  {
    $str = "a\x00ñ\x00c";
    self::assertSame('anc', u::str_transliterate($str));
  }

  public function test_nul()
  {
    $str = "a\x00b\x00c";
    self::assertSame('abc', u::str_transliterate($str));
  }
}
