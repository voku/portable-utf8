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
    self::assertSame('Iñ', u::substr($str, 0, 2));
  }

  public function test_utf8_two()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertSame('të', u::substr($str, 2, 2));
  }

  public function test_utf8_zero()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertSame('Iñtërnâtiônàlizætiøn', u::substr($str, 0));
  }

  public function test_utf8_zero_zero()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertSame('', u::substr($str, 0, 0));
  }

  public function test_start_great_than_length()
  {
    $str = 'Iñt';
    self::assertEmpty(u::substr($str, 4));
  }

  public function test_compare_start_great_than_length()
  {
    $str = 'abc';
    self::assertSame(substr($str, 4), u::substr($str, 4));
  }

  public function test_length_beyond_string()
  {
    $str = 'Iñt';
    self::assertSame('ñt', u::substr($str, 1, 5));
  }

  public function test_compare_length_beyond_string()
  {
    $str = 'abc';
    self::assertSame(substr($str, 1, 5), u::substr($str, 1, 5));
  }

  public function test_start_negative()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertSame('tiøn', u::substr($str, -4));
  }

  public function test_length_negative()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertSame('nàlizæti', u::substr($str, 10, -2));
  }

  public function test_start_length_negative()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertSame('ti', u::substr($str, -4, -2));
  }

  public function test_linefeed()
  {
    $str = "Iñ\ntërnâtiônàlizætiøn";
    self::assertSame("ñ\ntër", u::substr($str, 1, 5));
  }

  public function test_long_length()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertSame('Iñtërnâtiônàlizætiøn', u::substr($str, 0, 15536));
  }
}
