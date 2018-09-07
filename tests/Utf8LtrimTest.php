<?php

declare(strict_types=1);

use voku\helper\UTF8 as u;

/**
 * Class Utf8LtrimTest
 */
class Utf8LtrimTest extends \PHPUnit\Framework\TestCase
{
  public function test_trim()
  {
    $str = '　中文空白　 ';
    $trimmed = '中文空白　 ';
    self::assertSame($trimmed, u::ltrim($str)); // ltrim() failed here

    $str = ' 𩸽 exotic test ホ 𩸽 ';
    $trimmed = '𩸽 exotic test ホ 𩸽 ';
    self::assertSame($trimmed, u::ltrim($str));
    self::assertSame($trimmed, ltrim($str));

    $str = ' 𩸽 exotic test ホ 𩸽 ';
    $trimmed = 'exotic test ホ 𩸽 ';
    self::assertSame($trimmed, u::ltrim($str, '𩸽 '));
    self::assertSame($trimmed, ltrim($str, '𩸽 '));

    $str = 'ñtërnâtiônàlizætiøn';
    $trimmed = 'tërnâtiônàlizætiøn';
    self::assertSame($trimmed, u::ltrim($str, 'ñ'));
    self::assertSame($trimmed, ltrim($str, 'ñ'));

    $str = '//ñtërnâtiônàlizætiøn//';
    $trimmed = 'ñtërnâtiônàlizætiøn//';
    self::assertSame($trimmed, u::ltrim($str, '/'));
    self::assertSame($trimmed, ltrim($str, '/'));
  }

  public function test_no_trim()
  {
    $str = ' Iñtërnâtiônàlizætiøn';
    $trimmed = ' Iñtërnâtiônàlizætiøn';
    self::assertSame($trimmed, u::ltrim($str, 'ñ'));
    self::assertSame($trimmed, ltrim($str, 'ñ'));
  }

  public function test_empty_string()
  {
    $str = '';
    $trimmed = '';
    self::assertSame($trimmed, u::ltrim($str));
    self::assertSame($trimmed, ltrim($str));
  }

  public function test_forward_slash()
  {
    $str = '/Iñtërnâtiônàlizætiøn';
    $trimmed = 'Iñtërnâtiônàlizætiøn';
    self::assertSame($trimmed, u::ltrim($str, '/'));
    self::assertSame($trimmed, ltrim($str, '/'));
  }

  public function test_negate_char_class()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $trimmed = 'Iñtërnâtiônàlizætiøn';
    self::assertSame($trimmed, u::ltrim($str, '^s'));
    self::assertSame($trimmed, ltrim($str, '^s'));
  }

  public function test_linefeed()
  {
    $str = "ñ\nñtërnâtiônàlizætiøn";
    $trimmed = "\nñtërnâtiônàlizætiøn";
    self::assertSame($trimmed, u::ltrim($str, 'ñ'));
    self::assertSame($trimmed, ltrim($str, 'ñ'));
  }

  public function test_linefeed_mask()
  {
    $str = "ñ\nñtërnâtiônàlizætiøn";
    $trimmed = 'tërnâtiônàlizætiøn';
    self::assertSame($trimmed, u::ltrim($str, "ñ\n"));
    self::assertSame($trimmed, ltrim($str, "ñ\n"));
  }
}
