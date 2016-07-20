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

    self::assertSame($toPad . '          ', u::str_pad($toPad, 20));
    self::assertSame('          ' . $toPad, u::str_pad($toPad, 20, ' ', STR_PAD_LEFT));
    self::assertSame('     ' . $toPad . '     ', u::str_pad($toPad, 20, ' ', STR_PAD_BOTH));

    self::assertSame($toPad, u::str_pad($toPad, 10));
    self::assertSame('5char', str_pad('5char', 4)); // str_pos won't truncate input string
    self::assertSame($toPad, u::str_pad($toPad, 8));

    self::assertSame($toPad . 'ø__ø__ø__ø', u::str_pad($toPad, 20, $padding, STR_PAD_RIGHT));
    self::assertSame('ø__ø__ø__ø' . $toPad, u::str_pad($toPad, 20, $padding, STR_PAD_LEFT));
    self::assertSame('ø__ø_' . $toPad . 'ø__ø_', u::str_pad($toPad, 20, $padding, STR_PAD_BOTH));
  }
}
