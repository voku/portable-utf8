<?php

declare(strict_types=1);

use voku\helper\UTF8;
use voku\helper\UTF8 as u;

/**
 * Class Utf8StrcspnTest
 */
class Utf8StrcspnTest extends \PHPUnit\Framework\TestCase
{
  public function test_no_charlist()
  {
    $str = 'iñtërnâtiônàlizætiøn';
    self::assertSame(null, u::strcspn($str, ''));
  }

  public function test_empty_input()
  {
    $str = '';
    self::assertSame(null, u::strcspn($str, "\n"));
  }

  public function test_no_match_single_byte_search()
  {
    $str = 'iñtërnâtiônàlizætiøn';
    self::assertSame(2, u::strcspn($str, 't'));
  }

  public function test_no_match_single_byte_search_and_offset()
  {
    $str = 'iñtërnâtiônàlizætiøn';
    self::assertSame(6, u::strcspn($str, 't', 10));
  }

  public function test_no_match_single_byte_search_and_offset_and_length()
  {
    $str = 'iñtërnâtiônàlizætiøn';
    self::assertSame(1, u::strcspn($str, 'ñ', 0, 5));

    $str = 'iñtërnâtiônàlizætiøn';
    self::assertSame(5, u::strcspn($str, 'ø', 1, 5));
  }

  public function test_compare_strcspn()
  {
    $str = 'aeioustr';
    self::assertSame(strcspn($str, 'tr'), u::strcspn($str, 'tr'));
  }

  public function test_match_ascii()
  {
    $str = 'internationalization';
    self::assertSame(strcspn($str, 'a'), u::strcspn($str, 'a'));
  }

  public function test_linefeed()
  {
    $str = "i\nñtërnâtiônàlizætiøn";
    self::assertSame(3, u::strcspn($str, 't'));
  }

  public function test_linefeed_mask()
  {
    $str = "i\nñtërnâtiônàlizætiøn";
    self::assertSame(1, u::strcspn($str, "\n"));
  }

  protected function tes_no_match_multi_byte_search()
  {
    $str = 'iñtërnâtiônàlizætiøn';
    self::assertSame(6, u::strcspn($str, 'â'));
  }

  public function testNoMatchSingleByteSearch()
  {
    $str = 'iñtërnâtiônàlizætiøn';
    self::assertSame(UTF8::strcspn($str, 't'), 2);
  }

  public function testNoMatchMultiByteSearch()
  {
    $str = 'iñtërnâtiônàlizætiøn';
    self::assertSame(UTF8::strcspn($str, 'â'), 6);
  }

  public function testCompareStrspn()
  {
    $str = 'aeioustr';
    self::assertSame(UTF8::strcspn($str, 'tr'), strcspn($str, 'tr'));
  }

  public function testMatchAscii()
  {
    $str = 'internationalization';
    self::assertSame(UTF8::strcspn($str, 'a'), strcspn($str, 'a'));
  }

  public function testLinefeed()
  {
    $str = "i\nñtërnâtiônàlizætiøn";
    self::assertSame(UTF8::strcspn($str, 't'), 3);
  }

  public function testLinefeedMask()
  {
    $str = "i\nñtërnâtiônàlizætiøn";
    self::assertSame(UTF8::strcspn($str, "\n"), 1);
  }
}
