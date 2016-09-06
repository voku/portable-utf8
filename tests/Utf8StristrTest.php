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

    self::assertSame('nâtiônàlizætiøn', u::stristr($str, $search));
    self::assertSame('iñtër', u::stristr($str, $search, true));

    // --- alias

    self::assertSame('nâtiônàlizætiøn', u::strichr($str, $search));
    self::assertSame('iñtër', u::strichr($str, $search, true));
  }

  public function test_substr_no_match()
  {
    $str = 'iñtërnâtiônàlizætiøn';
    $search = 'foo';
    self::assertFalse(u::stristr($str, $search));

    // ---

    self::assertFalse(u::strstr($str, $search));
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
    self::assertSame('nâtiônàlizætiøn', u::stristr($str, $search));
  }

  public function test_linefeed_both()
  {
    $str = "iñtërn\nâtiônàlizætiøn";
    $search = "N\nÂT";
    self::assertSame("n\nâtiônàlizætiøn", u::stristr($str, $search));
  }

  public function test_case()
  {
    $str = "iñtërn\nâtiônàlizætiøn";
    $search = "n\nÂT";
    self::assertSame("n\nâtiônàlizætiøn", u::stristr($str, $search));

    // ---

    self::assertSame("n\nâtiônàlizætiøn", u::stristr($str, $search));
  }
}
