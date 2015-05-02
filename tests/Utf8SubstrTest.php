<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8SubstrTest
 */
class Utf8SubstrTest extends PHPUnit_Framework_TestCase
{
  public function test_utf8()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertEquals('Iñ', u::substr($str, 0, 2));
  }

  public function test_utf8_two()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertEquals('të', u::substr($str, 2, 2));
  }

  public function test_utf8_zero()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertEquals('Iñtërnâtiônàlizætiøn', u::substr($str, 0));
  }

  public function test_utf8_zero_zero()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertEquals('', u::substr($str, 0, 0));
  }

  public function test_start_great_than_length()
  {
    $str = 'Iñt';
    self::assertEmpty(u::substr($str, 4));
  }

  public function test_compare_start_great_than_length()
  {
    $str = 'abc';
    self::assertEquals(substr($str, 4), u::substr($str, 4));
  }

  public function test_length_beyond_string()
  {
    $str = 'Iñt';
    self::assertEquals('ñt', u::substr($str, 1, 5));
  }

  public function test_compare_length_beyond_string()
  {
    $str = 'abc';
    self::assertEquals(substr($str, 1, 5), u::substr($str, 1, 5));
  }

  public function test_start_negative()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertEquals('tiøn', u::substr($str, -4));
  }

  public function test_length_negative()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertEquals('nàlizæti', u::substr($str, 10, -2));
  }

  public function test_start_length_negative()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertEquals('ti', u::substr($str, -4, -2));
  }

  public function test_linefeed()
  {
    $str = "Iñ\ntërnâtiônàlizætiøn";
    self::assertEquals("ñ\ntër", u::substr($str, 1, 5));
  }

  public function test_long_length()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertEquals('Iñtërnâtiônàlizætiøn', u::substr($str, 0, 15536));
  }
}
