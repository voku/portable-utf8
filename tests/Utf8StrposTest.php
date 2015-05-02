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
    self::assertEquals(6, u::strpos($str, 'â'));
    self::assertEquals(6, u::stripos($str, 'Â'));
  }

  public function test_utf8_offset()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertEquals(19, u::strpos($str, 'n', 11));
    self::assertEquals(19, u::stripos($str, 'N', 11));
  }

  public function test_utf8_invalid()
  {
    $str = "Iñtërnâtiôn\xE9àlizætiøn";
    self::assertEquals(15, u::strpos($str, 'æ', 0, true));
    self::assertEquals(15, u::stripos($str, 'æ', 0, true));
  }

  public function test_ascii()
  {
    $str = 'ABC 123';
    self::assertEquals(1, u::strpos($str, 'B'));
    self::assertEquals(1, u::stripos($str, 'b'));
  }

  public function test_vs_strpos()
  {
    $str = 'ABC 123 ABC';
    self::assertEquals(strpos($str, 'B', 3), u::strpos($str, 'B', 3));
    self::assertEquals(stripos($str, 'b', 3), u::stripos($str, 'b', 3));
  }

  public function test_empty_str()
  {
    $str = '';
    self::assertFalse(u::strpos($str, 'x'));
    self::assertFalse(u::stripos($str, 'x'));
  }
}
