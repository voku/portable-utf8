<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8TrimTest
 */
class Utf8TrimTest extends PHPUnit_Framework_TestCase
{
  public function test_trim()
  {
    $str = 'ñtërnâtiônàlizætiø';
    $trimmed = 'tërnâtiônàlizæti';
    self::assertSame($trimmed, u::trim($str, 'ñø'));
  }

  public function test_no_trim()
  {
    $str = ' Iñtërnâtiônàlizætiøn ';
    $trimmed = ' Iñtërnâtiônàlizætiøn ';
    self::assertSame($trimmed, u::trim($str, 'ñø'));
  }

  public function test_empty_string()
  {
    $str = '';
    $trimmed = '';
    self::assertSame($trimmed, u::trim($str));
  }
}
