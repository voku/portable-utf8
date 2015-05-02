<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8StristrTest
 */
class Utf8StristrTest extends PHPUnit_Framework_TestCase
{
  public function test_substr()
  {
    $str = 'iñtërnâtiônàlizætiøn';
    $search = 'NÂT';
    self::assertEquals('nâtiônàlizætiøn', u::stristr($str, $search));
  }

  public function test_substr_no_match()
  {
    $str = 'iñtërnâtiônàlizætiøn';
    $search = 'foo';
    self::assertFalse(u::stristr($str, $search));
  }

  public function test_empty_search()
  {
    $str = 'iñtërnâtiônàlizætiøn';
    $search = '';
    self::assertFalse(u::stristr($str, $search));
  }

  public function test_empty_str()
  {
    $str = '';
    $search = 'NÂT';
    self::assertFalse(u::stristr($str, $search));
  }

  public function test_empty_both()
  {
    $str = '';
    $search = '';
    self::assertEmpty(u::stristr($str, $search));
  }

  public function test_linefeed_str()
  {
    $str = "iñt\nërnâtiônàlizætiøn";
    $search = 'NÂT';
    self::assertEquals('nâtiônàlizætiøn', u::stristr($str, $search));
  }

  public function test_linefeed_both()
  {
    $str = "iñtërn\nâtiônàlizætiøn";
    $search = "N\nÂT";
    self::assertEquals("n\nâtiônàlizætiøn", u::stristr($str, $search));
  }
}
