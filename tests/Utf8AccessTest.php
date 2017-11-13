<?php
use voku\helper\UTF8;

/**
 * Class Utf8AccessTest
 */
class Utf8AccessTest extends \PHPUnit\Framework\TestCase
{
  // tests for utf8_locate_current_chr & utf8_locate_next_chr
  public function test_singlebyte()
  {
    $tests = array();

    // single byte, should return current index
    $tests[] = array('aaживπά우리をあöä', 8, '리');
    $tests[] = array('aaживπά우리をあöä', 9, 'を');

    foreach ($tests as $test) {
      self::assertSame($test[2], UTF8::access($test[0], $test[1]));
    }

    $tests = array();
    $tests[] = array('aaживπά우리をあöä', 7, '우');

    foreach ($tests as $test) {
      self::assertSame($test[2], UTF8::access($test[0], $test[1]));
    }

  }
}
