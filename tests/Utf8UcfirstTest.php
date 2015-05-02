<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8UcfirstTest
 */
class Utf8UcfirstTest extends PHPUnit_Framework_TestCase
{
  public function test_ucfirst()
  {
    $str = 'ñtërnâtiônàlizætiøn';
    $ucfirst = 'Ñtërnâtiônàlizætiøn';
    self::assertEquals($ucfirst, u::ucfirst($str));
  }

  public function test_ucfirst_space()
  {
    $str = ' iñtërnâtiônàlizætiøn';
    $ucfirst = ' iñtërnâtiônàlizætiøn';
    self::assertEquals($ucfirst, u::ucfirst($str));
  }

  public function test_ucfirst_upper()
  {
    $str = 'Ñtërnâtiônàlizætiøn';
    $ucfirst = 'Ñtërnâtiônàlizætiøn';
    self::assertEquals($ucfirst, u::ucfirst($str));
  }

  public function test_empty_string()
  {
    $str = '';
    self::assertEquals('', u::ucfirst($str));
  }

  public function test_one_char()
  {
    $str = 'ñ';
    $ucfirst = "Ñ";
    self::assertEquals($ucfirst, u::ucfirst($str));
  }

  public function test_linefeed()
  {
    $str = "ñtërn\nâtiônàlizætiøn";
    $ucfirst = "Ñtërn\nâtiônàlizætiøn";
    self::assertEquals($ucfirst, u::ucfirst($str));
  }
}