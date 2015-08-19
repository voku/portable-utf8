<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8LtrimTest
 */
class Utf8LtrimTest extends PHPUnit_Framework_TestCase
{
  public function test_trim()
  {
    $str = 'ñtërnâtiônàlizætiøn';
    $trimmed = 'tërnâtiônàlizætiøn';
    self::assertEquals($trimmed, u::ltrim($str, 'ñ'));
  }

  public function test_no_trim()
  {
    $str = ' Iñtërnâtiônàlizætiøn';
    $trimmed = ' Iñtërnâtiônàlizætiøn';
    self::assertEquals($trimmed, u::ltrim($str, 'ñ'));
  }

  public function test_empty_string()
  {
    $str = '';
    $trimmed = '';
    self::assertEquals($trimmed, u::ltrim($str));
  }

  public function test_forward_slash()
  {
    $str = '/Iñtërnâtiônàlizætiøn';
    $trimmed = 'Iñtërnâtiônàlizætiøn';
    self::assertEquals($trimmed, u::ltrim($str, '/'));
  }

  public function test_negate_char_class()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $trimmed = 'Iñtërnâtiônàlizætiøn';
    self::assertEquals($trimmed, u::ltrim($str, '^s'));
  }

  public function test_linefeed()
  {
    $str = "ñ\nñtërnâtiônàlizætiøn";
    $trimmed = "\nñtërnâtiônàlizætiøn";
    self::assertEquals($trimmed, u::ltrim($str, 'ñ'));
  }

  public function test_linefeed_mask()
  {
    $str = "ñ\nñtërnâtiônàlizætiøn";
    $trimmed = 'tërnâtiônàlizætiøn';
    self::assertEquals($trimmed, u::ltrim($str, "ñ\n"));
  }
}
