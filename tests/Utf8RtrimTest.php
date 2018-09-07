<?php

declare(strict_types=1);

use voku\helper\UTF8 as u;

/**
 * Class Utf8RtrimTest
 */
class Utf8RtrimTest extends \PHPUnit\Framework\TestCase
{
  public function test_trim()
  {
    $str = '　中文空白　 ';
    $trimmed = '　中文空白';
    self::assertSame($trimmed, u::rtrim($str)); // rtrim() failed here

    $str = 'Iñtërnâtiônàlizætiø';
    $trimmed = 'Iñtërnâtiônàlizæti';
    self::assertSame($trimmed, u::rtrim($str, 'ø'));
    self::assertSame($trimmed, rtrim($str, 'ø'));

    $str = '//Iñtërnâtiônàlizætiø//';
    $trimmed = '//Iñtërnâtiônàlizætiø';
    self::assertSame($trimmed, u::rtrim($str, '/'));
    self::assertSame($trimmed, rtrim($str, '/'));
  }

  public function test_no_trim()
  {
    $str = 'Iñtërnâtiônàlizætiøn ';
    $trimmed = 'Iñtërnâtiônàlizætiøn ';
    self::assertSame($trimmed, u::rtrim($str, 'ø'));
    self::assertSame($trimmed, rtrim($str, 'ø'));
  }

  public function test_empty_string()
  {
    $str = '';
    $trimmed = '';
    self::assertSame($trimmed, u::rtrim($str));
    self::assertSame($trimmed, rtrim($str));
  }

  public function test_linefeed()
  {
    $str = "Iñtërnâtiônàlizætiø\nø";
    $trimmed = "Iñtërnâtiônàlizætiø\n";
    self::assertSame($trimmed, u::rtrim($str, 'ø'));
    self::assertSame($trimmed, rtrim($str, 'ø'));
  }

  public function test_linefeed_mask()
  {
    $str = "Iñtërnâtiônàlizætiø\nø";
    $trimmed = 'Iñtërnâtiônàlizæti';
    self::assertSame($trimmed, u::rtrim($str, "ø\n"));
    self::assertSame($trimmed, rtrim($str, "ø\n"));
  }
}
