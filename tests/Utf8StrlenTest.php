<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrlenTest
 */
class Utf8StrlenTest extends PHPUnit_Framework_TestCase
{
  public function test_utf8()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertEquals(20, u::strlen($str));
  }

  public function test_utf8_invalid()
  {
    $str = "Iñtërnâtiôn\xE9àlizætiøn";
    self::assertEquals(20, u::strlen($str));
  }

  public function test_ascii()
  {
    $str = 'ABC 123';
    self::assertEquals(7, u::strlen($str));
  }

  public function test_empty_str()
  {
    $str = '';
    self::assertEquals(0, u::strlen($str));
  }
}
