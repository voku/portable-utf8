<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrWordwrapTest
 */
class Utf8StrWordwrapTest extends PHPUnit_Framework_TestCase
{
  public function test_no_args()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $wrapped = 'Iñtërnâtiônàlizætiøn';
    self::assertEquals($wrapped, u::wordwrap($str));
  }

  public function test_break_at_ten()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $wrapped = "Iñtërnâtiô\nnàlizætiøn";
    self::assertEquals($wrapped, u::wordwrap($str, 10, "\n", true));
  }

  public function test_break_at_ten_br()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $wrapped = "Iñtërnâtiô<br>nàlizætiøn";
    self::assertEquals($wrapped, u::wordwrap($str, 10, '<br>', true));
  }

  public function test_break_at_ten_int()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $wrapped = "Iñtërnâtiô 우리をあöä nàlizætiøn";
    self::assertEquals($wrapped, u::wordwrap($str, 10, ' 우리をあöä ', true));
  }
}
