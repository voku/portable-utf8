<?php

declare(strict_types=1);

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrSplitTest
 */
class Utf8StrSplitTest extends \PHPUnit\Framework\TestCase
{
  public function test_split_one_char()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $array = [
        'I',
        'ñ',
        't',
        'ë',
        'r',
        'n',
        'â',
        't',
        'i',
        'ô',
        'n',
        'à',
        'l',
        'i',
        'z',
        'æ',
        't',
        'i',
        'ø',
        'n',
    ];

    self::assertSame($array, u::split($str));
  }

  public function test_split_five_chars()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $array = [
        'Iñtër',
        'nâtiô',
        'nàliz',
        'ætiøn',
    ];

    self::assertSame($array, u::split($str, 5));
  }

  public function test_split_six_chars()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $array = [
        'Iñtërn',
        'âtiônà',
        'lizæti',
        'øn',
    ];

    self::assertSame($array, u::split($str, 6));
  }

  public function test_split_long()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $array = [
        'Iñtërnâtiônàlizætiøn',
    ];

    self::assertSame($array, u::split($str, 40));
  }

  public function test_split_newline()
  {
    $str = "\nIñtërn\nâtiônàl\nizætiøn\n\n";
    $array = [
        "\n",
        'I',
        'ñ',
        't',
        'ë',
        'r',
        'n',
        "\n",
        'â',
        't',
        'i',
        'ô',
        'n',
        'à',
        'l',
        "\n",
        'i',
        'z',
        'æ',
        't',
        'i',
        'ø',
        'n',
        "\n",
        "\n",
    ];

    self::assertSame($array, u::split($str));
  }

  public function test_split_zero_length()
  {
    $str = 'Iñtë';
    $array = [];

    self::assertSame($array, u::split($str, 0));
  }

  public function test_split_one_length()
  {
    $str = 'Iñtë';
    $array = [
        'I',
        'ñ',
        't',
        'ë',
    ];

    self::assertSame($array, u::split($str, 1));
  }
}
