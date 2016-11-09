<?php

use voku\helper\UTF8 as u;
use voku\helper\UTF8;

/**
 * Class Utf8OrdTest
 */
class Utf8OrdTest extends PHPUnit_Framework_TestCase
{
  public function test_empty_str()
  {
    $str = '';
    self::assertSame(0, u::ord($str));
  }

  public function test_ascii_char()
  {
    $str = 'a';
    self::assertSame(97, u::ord($str));
  }

  public function test_2_byte_char()
  {
    $str = 'ñ';
    self::assertSame(241, u::ord($str));
  }

  public function test_3_byte_char()
  {
    $str = '₧';
    self::assertSame(8359, u::ord($str));
  }

  public function test_4_byte_char()
  {
    $str = "\xF0\x90\x8C\xBC";
    self::assertSame(66364, u::ord($str));
  }

  public function testEmptyStr()
  {
    $str = '';
    self::assertSame(0, UTF8::ord($str));
  }

  public function testAsciiChar()
  {
    $str = 'a';
    self::assertSame(97, UTF8::ord($str));
  }

  public function test2ByteChar()
  {
    $str = 'ñ';
    self::assertSame(241, UTF8::ord($str));
  }

  public function test3ByteChar()
  {
    $str = '₧';
    self::assertSame(8359, UTF8::ord($str));
  }

  public function test4ByteChar()
  {
    $str = "\xf0\x90\x8c\xbc";
    self::assertSame(66364, UTF8::ord($str));
  }
}
