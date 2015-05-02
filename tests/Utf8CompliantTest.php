<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8CompliantTest
 */
class Utf8CompliantTest extends PHPUnit_Framework_TestCase
{
  public function test_valid_utf8()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertTrue(u::is_utf8($str));
  }

  public function test_valid_utf8_ascii()
  {
    $str = 'ABC 123';
    self::assertTrue(u::is_utf8($str));
  }

  public function test_invalid_utf8()
  {
    $str = "Iñtërnâtiôn\xE9àlizætiøn";
    self::assertFalse(u::is_utf8($str));
  }

  public function test_invalid_utf8_ascii()
  {
    $str = "this is an invalid char '\xE9' here";
    self::assertFalse(u::is_utf8($str));
  }

  public function test_empty_string()
  {
    $str = '';
    self::assertTrue(u::is_utf8($str));
  }

  public function test_valid_two_octet_id()
  {
    $str = "\xC3\xB1";
    self::assertTrue(u::is_utf8($str));
  }

  public function test_invalid_two_octet_sequence()
  {
    $str = "Iñtërnâtiônàlizætiøn \xC3\x28 Iñtërnâtiônàlizætiøn";
    self::assertFalse(u::is_utf8($str));
  }

  public function test_invalid_id_between_twoAnd_three()
  {
    $str = "Iñtërnâtiônàlizætiøn\xA0\xA1Iñtërnâtiônàlizætiøn";
    self::assertFalse(u::is_utf8($str));
  }

  public function test_valid_three_octet_id()
  {
    $str = "Iñtërnâtiônàlizætiøn\xE2\x82\xA1Iñtërnâtiônàlizætiøn";
    self::assertTrue(u::is_utf8($str));
  }

  public function test_invalid_three_octet_sequence_second()
  {
    $str = "Iñtërnâtiônàlizætiøn\xE2\x28\xA1Iñtërnâtiônàlizætiøn";
    self::assertFalse(u::is_utf8($str));
  }

  public function test_invalid_three_octet_sequence_third()
  {
    $str = "Iñtërnâtiônàlizætiøn\xE2\x82\x28Iñtërnâtiônàlizætiøn";
    self::assertFalse(u::is_utf8($str));
  }

  public function test_valid_four_octet_id()
  {
    $str = "Iñtërnâtiônàlizætiøn\xF0\x90\x8C\xBCIñtërnâtiônàlizætiøn";
    self::assertTrue(u::is_utf8($str));
  }

  public function test_invalid_four_octet_sequence()
  {
    $str = "Iñtërnâtiônàlizætiøn\xF0\x28\x8C\xBCIñtërnâtiônàlizætiøn";
    self::assertFalse(u::is_utf8($str));
  }

  public function test_invalid_five_octet_sequence()
  {
    $str = "Iñtërnâtiônàlizætiøn\xF8\xA1\xA1\xA1\xA1Iñtërnâtiônàlizætiøn";
    self::assertFalse(u::is_utf8($str));
  }

  public function test_invalid_six_octet_sequence()
  {
    $str = "Iñtërnâtiônàlizætiøn\xFC\xA1\xA1\xA1\xA1\xA1Iñtërnâtiônàlizætiøn";
    self::assertFalse(u::is_utf8($str));
  }
}