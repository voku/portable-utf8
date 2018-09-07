<?php

declare(strict_types=1);

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrrevTest
 */
class Utf8StrrevTest extends \PHPUnit\Framework\TestCase
{
  public function test_reverse()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $rev = 'nøitæzilànôitânrëtñI';
    self::assertSame($rev, u::strrev($str));
  }

  public function test_empty_str()
  {
    $str = '';
    $rev = '';
    self::assertSame($rev, u::strrev($str));
  }

  public function test_linefeed()
  {
    $str = "Iñtërnâtiôn\nàlizætiøn";
    $rev = "nøitæzilà\nnôitânrëtñI";
    self::assertSame($rev, u::strrev($str));
  }
}
