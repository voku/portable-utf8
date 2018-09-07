<?php

declare(strict_types=1);

use voku\helper\UTF8;

/**
 * Class Utf8StrToUpperTest
 */
class Utf8StrToUpperTest extends \PHPUnit\Framework\TestCase
{
  public function testUpper()
  {
    $str = 'iñtërnâtiônàlizætiøn';
    $upper = 'IÑTËRNÂTIÔNÀLIZÆTIØN';
    self::assertSame(UTF8::strtoupper($str), $upper);
  }

  public function testEmptyString()
  {
    $str = '';
    $upper = '';
    self::assertSame(UTF8::strtoupper($str), $upper);
  }
}
