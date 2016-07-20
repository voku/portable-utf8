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
    self::assertSame($ucfirst, u::ucfirst($str));
  }

  public function test_ucfirst_space()
  {
    $str = ' iñtërnâtiônàlizætiøn';
    $ucfirst = ' iñtërnâtiônàlizætiøn';
    self::assertSame($ucfirst, u::ucfirst($str));
  }

  public function test_ucfirst_upper()
  {
    $str = 'Ñtërnâtiônàlizætiøn';
    $ucfirst = 'Ñtërnâtiônàlizætiøn';
    self::assertSame($ucfirst, u::ucfirst($str));
  }

  public function test_empty_string()
  {
    $str = '';
    self::assertSame('', u::ucfirst($str));
  }

  public function test_one_char()
  {
    $str = 'ñ';
    $ucfirst = 'Ñ';
    self::assertSame($ucfirst, u::ucfirst($str));
  }

  public function test_linefeed()
  {
    $str = "ñtërn\nâtiônàlizætiøn";
    $ucfirst = "Ñtërn\nâtiônàlizætiøn";
    self::assertSame($ucfirst, u::ucfirst($str));
  }
}
