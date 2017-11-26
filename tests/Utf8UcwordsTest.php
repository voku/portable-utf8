<?php

use voku\helper\UTF8;
use voku\helper\UTF8 as u;

/**
 * Class Utf8UcwordsTest
 */
class Utf8UcwordsTest extends \PHPUnit\Framework\TestCase
{
  public function test_ucword()
  {
    $str = 'iñtërnâtiônàlizætiøn';
    $ucwords = 'Iñtërnâtiônàlizætiøn';
    self::assertSame($ucwords, u::ucwords($str));
  }

  public function test_ucwords()
  {
    $str = 'iñt ërn âti ônà liz æti øn';
    $ucwords = 'Iñt Ërn Âti Ônà Liz Æti Øn';
    self::assertSame($ucwords, u::ucwords($str));
  }

  public function test_ucwords_newline()
  {
    $str = "iñt ërn âti\n ônà liz æti  øn";
    $ucwords = "Iñt Ërn Âti\n Ônà Liz Æti  Øn";
    self::assertSame($ucwords, u::ucwords($str));
  }

  public function test_empty_string()
  {
    $str = '';
    $ucwords = '';
    self::assertSame($ucwords, u::ucwords($str));
  }

  public function test_one_char()
  {
    $str = 'ñ';
    $ucwords = 'Ñ';
    self::assertSame($ucwords, u::ucwords($str));
  }

  public function test_linefeed()
  {
    $str = "iñt ërn âti\n ônà liz æti øn";
    $ucwords = "Iñt Ërn Âti\n Ônà Liz Æti Øn";
    self::assertSame($ucwords, u::ucwords($str));
  }

  public function testUcWords()
  {
    self::assertSame('Iñt Ërn ÂTi Ônà Liz Æti Øn', UTF8::ucwords('iñt ërn âTi ônà liz æti øn'));
    self::assertSame("Iñt Ërn Âti\n Ônà Liz Æti  Øn", UTF8::ucwords("iñt ërn âti\n ônà liz æti  øn"));
    self::assertSame('中文空白 foo Oo Oöäü#s', UTF8::ucwords('中文空白 foo oo oöäü#s', ['foo'], '#'));
    self::assertSame('中文空白 foo Oo Oöäü#S', UTF8::ucwords('中文空白 foo oo oöäü#s', ['foo'], ''));
    self::assertSame('', UTF8::ucwords(''));
    self::assertSame('Ñ', UTF8::ucwords('ñ'));
    self::assertSame("Iñt ËrN Âti\n Ônà Liz Æti Øn", UTF8::ucwords("iñt ërN âti\n ônà liz æti øn"));
    self::assertSame('ÑtërnâtiônàlizætIøN', UTF8::ucwords('ñtërnâtiônàlizætIøN'));
    self::assertSame('ÑtërnâtiônàlizætIøN Test câse', UTF8::ucwords('ñtërnâtiônàlizætIøN test câse', ['câse']));
    self::assertSame('Deja Σσς DEJa ΣσΣ', UTF8::ucwords('deja σσς dEJa σσΣ'));

    self::assertSame('Deja Σσς DEJa ΣσΣ', UTF8::ucwords('deja σσς dEJa σσΣ', ['de']));
    self::assertSame('Deja Σσς DEJa ΣσΣ', UTF8::ucwords('deja σσς dEJa σσΣ', ['d', 'e']));

    self::assertSame('deja Σσς DEJa ΣσΣ', UTF8::ucwords('deja σσς dEJa σσΣ', ['deja']));
    self::assertSame('deja Σσς DEJa σσΣ', UTF8::ucwords('deja σσς dEJa σσΣ', ['deja', 'σσΣ']));
    self::assertSame('Deja σσς dEJa σσΣ', UTF8::ucwords('deja σσς dEJa σσΣ', ['deja', 'σσΣ'], ' '));
    self::assertSame(
        'deja Σσς DEJa σσΣ', UTF8::ucwords(
        'deja σσς dEJa σσΣ' . "\x01\x02", [
        'deja',
        'σσΣ',
    ], '', 'UTF-8', true
    )
    );
  }
}
