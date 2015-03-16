<?php

use voku\helper\UTF8 as u;

class Utf8StrTransliterateTest extends PHPUnit_Framework_TestCase
{
  public function test_utf8()
  {
    $str = 'testiñg';
    $this->assertEquals('testing', u::str_transliterate($str));

    $str = '  -ABC-中文空白-  ';
    $this->assertEquals('  -ABC-Zhong Wen Kong Bai -  ', u::str_transliterate($str));
  }

  public function test_ascii()
  {
    $str = 'testing';
    $this->assertEquals('testing', u::str_transliterate($str));
  }

  public function test_invalid_char()
  {
    $str = "tes\xE9ting";
    $this->assertEquals('testing', u::str_transliterate($str));
  }

  public function test_empty_str()
  {
    $str = '';
    $this->assertEmpty(u::str_transliterate($str));
  }

  public function test_nul_and_non_7_bit()
  {
    $str = "a\x00ñ\x00c";
    $this->assertEquals("anc", u::str_transliterate($str));
  }

  public function test_nul()
  {
    $str = "a\x00b\x00c";
    $this->assertEquals('abc', u::str_transliterate($str));
  }
}