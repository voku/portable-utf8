<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8RtrimTest
 */
class Utf8RtrimTest extends PHPUnit_Framework_TestCase
{
  public function test_trim()
  {
    $str = 'Iñtërnâtiônàlizætiø';
    $trimmed = 'Iñtërnâtiônàlizæti';
    self::assertEquals($trimmed, u::rtrim($str, 'ø'));
  }

  public function test_no_trim()
  {
    $str = 'Iñtërnâtiônàlizætiøn ';
    $trimmed = 'Iñtërnâtiônàlizætiøn ';
    self::assertEquals($trimmed, u::rtrim($str, 'ø'));
  }

  public function test_empty_string()
  {
    $str = '';
    $trimmed = '';
    self::assertEquals($trimmed, u::rtrim($str));
  }

  public function test_linefeed()
  {
    $str = "Iñtërnâtiônàlizætiø\nø";
    $trimmed = "Iñtërnâtiônàlizætiø\n";
    self::assertEquals($trimmed, u::rtrim($str, 'ø'));
  }

  public function test_linefeed_mask()
  {
    $str = "Iñtërnâtiônàlizætiø\nø";
    $trimmed = 'Iñtërnâtiônàlizæti';
    self::assertEquals($trimmed, u::rtrim($str, "ø\n"));
  }
}
