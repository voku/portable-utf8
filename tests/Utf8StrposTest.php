<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrposTest
 */
class Utf8StrposTest extends PHPUnit_Framework_TestCase
{
  public function test_utf8()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertSame(6, u::strpos($str, 'â'));
    self::assertSame(6, u::stripos($str, 'Â'));
  }

  public function test_utf8_offset()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertSame(19, u::strpos($str, 'n', 11));
    self::assertSame(19, u::stripos($str, 'N', 11));
  }

  public function test_utf8_invalid()
  {
    $str = "Iñtërnâtiôn\xE9àlizætiøn";
    self::assertSame(15, u::strpos($str, 'æ', 0, 'UTF-8', true));
    self::assertSame(15, u::stripos($str, 'æ', 0, 'UTF-8', true));
    self::assertSame(15, u::strpos($str, 'æ', 0, true, true));
    self::assertSame(15, u::stripos($str, 'æ', 0, false, true));
  }

  public function test_ascii()
  {
    $str = 'ABC 123';
    self::assertSame(1, u::strpos($str, 'B'));
    self::assertSame(1, u::stripos($str, 'b'));
  }

  public function test_vs_strpos()
  {
    $str = 'ABC 123 ABC';
    self::assertSame(strpos($str, 'B', 3), u::strpos($str, 'B', 3));
    self::assertSame(stripos($str, 'b', 3), u::stripos($str, 'b', 3));
  }

  public function test_empty_str()
  {
    $str = '';
    self::assertFalse(u::strpos($str, 'x'));
    self::assertFalse(u::stripos($str, 'x'));
  }
}
