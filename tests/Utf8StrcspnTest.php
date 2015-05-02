<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrcspnTest
 */
class Utf8StrcspnTest extends PHPUnit_Framework_TestCase
{
  public function test_no_match_single_byte_search()
  {
    $str = 'iñtërnâtiônàlizætiøn';
    self::assertEquals(2, u::strcspn($str, 't'));
  }

  public function test_compare_strspn()
  {
    $str = 'aeioustr';
    self::assertEquals(strcspn($str, 'tr'), u::strcspn($str, 'tr'));
  }

  public function test_match_ascii()
  {
    $str = 'internationalization';
    self::assertEquals(strcspn($str, 'a'), u::strcspn($str, 'a'));
  }

  public function test_linefeed()
  {
    $str = "i\nñtërnâtiônàlizætiøn";
    self::assertEquals(3, u::strcspn($str, 't'));
  }

  public function test_linefeed_mask()
  {
    $str = "i\nñtërnâtiônàlizætiøn";
    self::assertEquals(1, u::strcspn($str, "\n"));
  }

  protected function tes_no_match_multi_byte_search()
  {
    $str = 'iñtërnâtiônàlizætiøn';
    self::assertEquals(6, u::strcspn($str, 'â'));
  }
}