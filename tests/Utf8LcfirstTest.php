<?php

use voku\helper\UTF8 as u;

class Utf8LcfirstTest extends PHPUnit_Framework_TestCase
{
  public function test_lcfirst()
  {
    $str = 'ÑTËRNÂTIÔNÀLIZÆTIØN';
    $lcfirst = 'ñTËRNÂTIÔNÀLIZÆTIØN';
    $this->assertEquals($lcfirst, u::lcfirst($str));
  }

  public function test_lcfirst_upper()
  {
    $str = 'ñTËRNÂTIÔNÀLIZÆTIØN';
    $lcfirst = 'ñTËRNÂTIÔNÀLIZÆTIØN';
    $this->assertEquals($lcfirst, u::lcfirst($str));
  }

  public function test_empty_string()
  {
    $str = '';
    $this->assertEquals('', u::lcfirst($str));
  }

  public function test_one_char()
  {
    $str = 'Ñ';
    $lcfirst = "ñ";
    $this->assertEquals($lcfirst, u::lcfirst($str));
  }

  public function test_linefeed()
  {
    $str = "ÑTËRN\nâtiônàlizætiøn";
    $lcfirst = "ñTËRN\nâtiônàlizætiøn";
    $this->assertEquals($lcfirst, u::lcfirst($str));
  }
}