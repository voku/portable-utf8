<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrPadTest
 */
class Utf8StrPadTest extends PHPUnit_Framework_TestCase
{
  public function test_str_pad()
  {
    $toPad = '<IñtërnëT>'; // 10 characters
    $padding = 'ø__'; // 4 characters

    self::assertEquals($toPad . '          ', u::str_pad($toPad, 20));
    self::assertEquals('          ' . $toPad, u::str_pad($toPad, 20, ' ', STR_PAD_LEFT));
    self::assertEquals('     ' . $toPad . '     ', u::str_pad($toPad, 20, ' ', STR_PAD_BOTH));

    self::assertEquals($toPad, u::str_pad($toPad, 10));
    self::assertEquals('5char', str_pad('5char', 4)); // str_pos won't truncate input string
    self::assertEquals($toPad, u::str_pad($toPad, 8));

    self::assertEquals($toPad . 'ø__ø__ø__ø', u::str_pad($toPad, 20, $padding, STR_PAD_RIGHT));
    self::assertEquals('ø__ø__ø__ø' . $toPad, u::str_pad($toPad, 20, $padding, STR_PAD_LEFT));
    self::assertEquals('ø__ø_' . $toPad . 'ø__ø_', u::str_pad($toPad, 20, $padding, STR_PAD_BOTH));
  }
}