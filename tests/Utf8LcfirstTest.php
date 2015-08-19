<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8LcfirstTest
 */
class Utf8LcfirstTest extends PHPUnit_Framework_TestCase
{
  public function test_lcfirst()
  {
    $str = 'ÑTËRNÂTIÔNÀLIZÆTIØN';
    $lcfirst = 'ñTËRNÂTIÔNÀLIZÆTIØN';
    self::assertEquals($lcfirst, u::lcfirst($str));
  }

  public function test_lcfirst_upper()
  {
    $str = 'ñTËRNÂTIÔNÀLIZÆTIØN';
    $lcfirst = 'ñTËRNÂTIÔNÀLIZÆTIØN';
    self::assertEquals($lcfirst, u::lcfirst($str));
  }

  public function test_empty_string()
  {
    $str = '';
    self::assertEquals('', u::lcfirst($str));
  }

  public function test_one_char()
  {
    $str = 'Ñ';
    $lcfirst = 'ñ';
    self::assertEquals($lcfirst, u::lcfirst($str));
  }

  public function test_linefeed()
  {
    $str = "ÑTËRN\nâtiônàlizætiøn";
    $lcfirst = "ñTËRN\nâtiônàlizætiøn";
    self::assertEquals($lcfirst, u::lcfirst($str));
  }
}
