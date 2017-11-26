<?php

use voku\helper\UTF8;

/**
 * Class Utf8StrtToLowerTest
 */
class Utf8StrtToLowerTest extends \PHPUnit\Framework\TestCase
{
  public function testLower()
  {
    $str = 'IÑTËRNÂTIÔNÀLIZÆTIØN';
    $lower = 'iñtërnâtiônàlizætiøn';
    self::assertSame(UTF8::strtolower($str), $lower);
  }

  public function testEmptyString()
  {
    $str = '';
    $lower = '';
    self::assertSame(UTF8::strtolower($str), $lower);
  }
}
