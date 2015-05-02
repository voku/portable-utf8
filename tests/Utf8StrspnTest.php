<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrspnTest
 */
class Utf8StrspnTest extends PHPUnit_Framework_TestCase
{
  public function test_match()
  {
    $str = 'iñtërnâtiônàlizætiøn';
    self::assertEquals(11, u::strspn($str, 'âëiônñrt'));
  }

  public function test_match_two()
  {
    $str = 'iñtërnâtiônàlizætiøn';
    self::assertEquals(4, u::strspn($str, 'iñtë'));
  }

  public function test_compare_strspn()
  {
    $str = 'aeioustr';
    self::assertEquals(strspn($str, 'saeiou'), u::strspn($str, 'saeiou'));
  }

  public function test_match_ascii()
  {
    $str = 'internationalization';
    self::assertEquals(strspn($str, 'aeionrt'), u::strspn($str, 'aeionrt'));
  }

  public function test_linefeed()
  {
    $str = "iñtërnât\niônàlizætiøn";
    self::assertEquals(8, u::strspn($str, 'âëiônñrt'));
  }

  public function test_linefeed_mask()
  {
    $str = "iñtërnât\niônàlizætiøn";
    self::assertEquals(12, u::strspn($str, "âëiônñrt\n"));
  }
}