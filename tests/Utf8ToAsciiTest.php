<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8ToAsciiTest
 */
class Utf8ToAsciiTest extends PHPUnit_Framework_TestCase
{
  public function test_utf8()
  {
    $str = 'testiñg';
    self::assertEquals('testing', u::toAscii($str));
  }

  public function test_ascii()
  {
    $str = 'testing';
    self::assertEquals('testing', u::toAscii($str));
  }

  public function test_invalid_char()
  {
    $str = "tes\xE9ting";
    self::assertEquals('testing', u::toAscii($str));
  }

  public function test_empty_str()
  {
    $str = '';
    self::assertEmpty(u::toAscii($str));
  }

  public function test_nul_and_non_7_bit()
  {
    $str = "a\x00ñ\x00c";
    self::assertEquals('anc', u::toAscii($str));
  }

  public function test_nul()
  {
    $str = "a\x00b\x00c";
    self::assertEquals('abc', u::toAscii($str));
  }
}
