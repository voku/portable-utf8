<?php

declare(strict_types=1);

use voku\helper\UTF8;
use voku\helper\UTF8 as u;

/**
 * Class Utf8StrrposTest
 */
class Utf8StrrposTest extends \PHPUnit\Framework\TestCase
{
  public function test_utf8()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertSame(17, u::strrpos($str, 'i'));
  }

  public function test_utf8_offset()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertSame(19, u::strrpos($str, 'n', 11));
  }

  public function test_utf8_invalid()
  {
    $str = "Iñtërnâtiôn\xE9àlizætiøn";
    self::assertSame(15, u::strrpos($str, 'æ', 0, 'UTF-8', true));
  }

  public function test_utf8_with_code_point()
  {
    $str = "I*ñtërnâtiôn\xE9àlizætiøn";
    self::assertSame(1, u::strrpos($str, 42, 0, 'UTF-8', true));
  }

  public function test_ascii()
  {
    $str = 'ABC ABC';
    self::assertSame(5, u::strrpos($str, 'B'));
  }

  public function test_vs_strpos()
  {
    $str = 'ABC 123 ABC';
    self::assertSame(strrpos($str, 'B'), u::strrpos($str, 'B'));
    if (UTF8::getSupportInfo('mbstring_func_overload') !== true) {
      // strrpos() is not working as expected with overload ...
      self::assertSame(strrpos($str, 1), u::strrpos($str, 1));
    }

    $str = 'ABC * ABC';
    self::assertSame(strrpos($str, 'B'), u::strrpos($str, 'B'));
    if (UTF8::getSupportInfo('mbstring_func_overload') !== true) {
      // strrpos() is not working as expected with overload ...
      self::assertSame(strrpos($str, 42), u::strrpos($str, 42));
    }
  }

  public function test_empty_str()
  {
    $str = '';
    self::assertFalse(u::strrpos($str, 'x'));
  }

  public function test_linefeed()
  {
    $str = "Iñtërnâtiônàlizætiø\nn";
    self::assertSame(17, u::strrpos($str, 'i'));
  }

  public function test_linefeed_search()
  {
    $str = "Iñtërnâtiônàlizætiø\nn";
    self::assertSame(19, u::strrpos($str, "\n"));
  }
}
