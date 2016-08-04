<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrcasecmpTest
 */
class Utf8StrcasecmpTest extends PHPUnit_Framework_TestCase
{
  public function test_compare_equal()
  {
    $str_x = 'iñtërnâtiônàlizætiøn';
    $str_y = 'IÑTËRNÂTIÔNÀLIZÆTIØN';
    self::assertSame(0, u::strcasecmp($str_x, $str_y));
    self::assertSame(true, u::strcmp($str_x, $str_y) >= 1);

    $str_x = 'IÑTËRNÂTIÔNÀLIZÆTIØN';
    $str_y = 'IÑTËRNÂTIÔNÀLIZÆTIØN';
    self::assertSame(0, u::strcasecmp($str_x, $str_y));
    self::assertSame(0, u::strcmp($str_x, $str_y));
  }

  public function test_less()
  {
    $str_x = 'iñtërnâtiônàlizætiøn';
    $str_y = 'IÑTËRNÂTIÔÀLIZÆTIØN';
    self::assertTrue(u::strcasecmp($str_x, $str_y) > 0);
    self::assertTrue(u::strcmp($str_x, $str_y) > 0);
  }

  public function test_greater()
  {
    $str_x = 'iñtërnâtiôàlizætiøn';
    $str_y = 'IÑTËRNÂTIÔNÀLIZÆTIØN';
    self::assertTrue(u::strcasecmp($str_x, $str_y) < 0);
    self::assertTrue(u::strcmp($str_x, $str_y) > 0);
  }

  public function test_empty_x()
  {
    $str_x = '';
    $str_y = 'IÑTËRNÂTIÔNÀLIZÆTIØN';
    self::assertTrue(u::strcasecmp($str_x, $str_y) < 0);
    self::assertTrue(u::strcmp($str_x, $str_y) < 0);
  }

  public function test_empty_y()
  {
    $str_x = 'iñtërnâtiôàlizætiøn';
    $str_y = '';
    self::assertTrue(u::strcasecmp($str_x, $str_y) > 0);
    self::assertTrue(u::strcmp($str_x, $str_y) > 0);
  }

  public function test_empty_both()
  {
    $str_x = '';
    $str_y = '';
    self::assertTrue(u::strcasecmp($str_x, $str_y) === 0);
    self::assertTrue(u::strcmp($str_x, $str_y) === 0);
  }

  public function test_linefeed()
  {
    $str_x = "iñtërnâtiôn\nàlizætiøn";
    $str_y = "IÑTËRNÂTIÔN\nÀLIZÆTIØN";
    self::assertTrue(u::strcasecmp($str_x, $str_y) === 0);
    self::assertTrue(u::strcmp($str_x, $str_y) >= 1);
  }

}
