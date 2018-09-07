<?php

declare(strict_types=1);

use voku\helper\UTF8 as u;

/**
 * Class Utf8LcfirstTest
 */
class Utf8LcfirstTest extends \PHPUnit\Framework\TestCase
{
  public function test_lcfirst()
  {
    $str = 'ÑTËRNÂTIÔNÀLIZÆTIØN';
    $lcfirst = 'ñTËRNÂTIÔNÀLIZÆTIØN';
    self::assertSame($lcfirst, u::lcfirst($str));
  }

  public function test_lcfirst_upper()
  {
    $str = 'ñTËRNÂTIÔNÀLIZÆTIØN';
    $lcfirst = 'ñTËRNÂTIÔNÀLIZÆTIØN';
    self::assertSame($lcfirst, u::lcfirst($str));
  }

  public function test_empty_string()
  {
    $str = '';
    self::assertSame('', u::lcfirst($str));
  }

  public function test_one_char()
  {
    $str = 'Ñ';
    $lcfirst = 'ñ';
    self::assertSame($lcfirst, u::lcfirst($str));
  }

  public function test_linefeed()
  {
    $str = "ÑTËRN\nâtiônàlizætiøn";
    $lcfirst = "ñTËRN\nâtiônàlizætiøn";
    self::assertSame($lcfirst, u::lcfirst($str));
  }
}
