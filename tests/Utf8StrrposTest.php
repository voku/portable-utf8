<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrrposTest
 */
class Utf8StrrposTest extends PHPUnit_Framework_TestCase
{
  public function test_utf8()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertEquals(17, u::strrpos($str, 'i'));
  }

  public function test_utf8_offset()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertEquals(19, u::strrpos($str, 'n', 11));
  }

  public function test_utf8_invalid()
  {
    $str = "Iñtërnâtiôn\xE9àlizætiøn";
    self::assertEquals(15, u::strrpos($str, 'æ', 0, true));
  }

  public function test_utf8_with_code_point()
  {
    $str = "I*ñtërnâtiôn\xE9àlizætiøn";
    self::assertEquals(1, u::strrpos($str, 42, 0, true));
  }

  public function test_ascii()
  {
    $str = 'ABC ABC';
    self::assertEquals(5, u::strrpos($str, 'B'));
  }

  public function test_vs_strpos()
  {
    $str = 'ABC 123 ABC';
    self::assertEquals(strrpos($str, 'B'), u::strrpos($str, 'B'));
    self::assertEquals(strrpos($str, 1), u::strrpos($str, 1));

    $str = 'ABC * ABC';
    self::assertEquals(strrpos($str, 'B'), u::strrpos($str, 'B'));
    self::assertEquals(strrpos($str, 42), u::strrpos($str, 42));
  }

  public function test_empty_str()
  {
    $str = '';
    self::assertFalse(u::strrpos($str, 'x'));
  }

  public function test_linefeed()
  {
    $str = "Iñtërnâtiônàlizætiø\nn";
    self::assertEquals(17, u::strrpos($str, 'i'));
  }

  public function test_linefeed_search()
  {
    $str = "Iñtërnâtiônàlizætiø\nn";
    self::assertEquals(19, u::strrpos($str, "\n"));
  }
}
