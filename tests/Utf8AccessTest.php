<?php

declare(strict_types=1);

use voku\helper\UTF8;

/**
 * Class Utf8AccessTest
 */
class Utf8AccessTest extends \PHPUnit\Framework\TestCase
{
  // tests for utf8_locate_current_chr & utf8_locate_next_chr
  public function test_singlebyte()
  {
    $tests = [];

    // single byte, should return current index
    $tests[] = ['aaживπά우리をあöä', 8, '리'];
    $tests[] = ['aaживπά우리をあöä', 9, 'を'];

    foreach ($tests as $test) {
      self::assertSame($test[2], UTF8::access($test[0], $test[1]));
    }

    $tests = [];
    $tests[] = ['aaживπά우리をあöä', 7, '우'];

    foreach ($tests as $test) {
      self::assertSame($test[2], UTF8::access($test[0], $test[1]));
    }

  }
}
