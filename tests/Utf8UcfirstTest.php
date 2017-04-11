<?php

use voku\helper\UTF8 as u;
use voku\helper\UTF8;

/**
 * Class Utf8UcfirstTest
 */
class Utf8UcfirstTest extends PHPUnit_Framework_TestCase
{
  public function test_ucfirst()
  {
    $str = 'ñtërnâtiônàlizætiøn';
    $ucfirst = 'Ñtërnâtiônàlizætiøn';
    self::assertSame($ucfirst, u::ucfirst($str));
  }

  public function test_ucfirst_space()
  {
    $str = ' iñtërnâtiônàlizætiøn';
    $ucfirst = ' iñtërnâtiônàlizætiøn';
    self::assertSame($ucfirst, u::ucfirst($str));
  }

  public function test_ucfirst_upper()
  {
    $str = 'Ñtërnâtiônàlizætiøn';
    $ucfirst = 'Ñtërnâtiônàlizætiøn';
    self::assertSame($ucfirst, u::ucfirst($str));
  }

  public function test_empty_string()
  {
    $str = '';
    self::assertSame('', u::ucfirst($str));
  }

  public function test_one_char()
  {
    $str = 'ñ';
    $ucfirst = 'Ñ';
    self::assertSame($ucfirst, u::ucfirst($str));
  }

  public function test_linefeed()
  {
    $str = "ñtërn\nâtiônàlizætiøn";
    $ucfirst = "Ñtërn\nâtiônàlizætiøn";
    self::assertSame($ucfirst, u::ucfirst($str));
  }

  public function testUcfirst()
  {
    self::assertSame('', UTF8::ucfirst(''));
    self::assertSame('Ä', UTF8::ucfirst('ä'));
    self::assertSame('Öäü', UTF8::ucfirst('Öäü'));
    self::assertSame('Öäü', UTF8::ucfirst('öäü'));
    self::assertSame('Κόσμε', UTF8::ucfirst('κόσμε'));
    self::assertSame('ABC-ÖÄÜ-中文空白', UTF8::ucfirst('aBC-ÖÄÜ-中文空白'));
    self::assertSame('Iñtërnâtiônàlizætiøn', UTF8::ucfirst('iñtërnâtiônàlizætiøn'));
    self::assertSame('Ñtërnâtiônàlizætiøn', UTF8::ucfirst('ñtërnâtiônàlizætiøn'));
    self::assertSame(' iñtërnâtiônàlizætiøn', UTF8::ucfirst(' iñtërnâtiônàlizætiøn'));
    self::assertSame('Ñtërnâtiônàlizætiøn', UTF8::ucfirst('Ñtërnâtiônàlizætiøn'));
    self::assertSame('ÑtërnâtiônàlizætIøN', UTF8::ucfirst('ñtërnâtiônàlizætIøN'));
    self::assertSame('ÑtërnâtiônàlizætIøN test câse', UTF8::ucfirst('ñtërnâtiônàlizætIøN test câse'));
    self::assertSame('', UTF8::ucfirst(''));
    self::assertSame('Ñ', UTF8::ucfirst('ñ'));
    self::assertSame("Ñtërn\nâtiônàlizætiøn", UTF8::ucfirst("ñtërn\nâtiônàlizætiøn"));
    self::assertSame('Deja', UTF8::ucfirst('deja'));
    self::assertSame('Σσς', UTF8::ucfirst('σσς'));
    self::assertSame('DEJa', UTF8::ucfirst('dEJa'));
    self::assertSame('ΣσΣ', UTF8::ucfirst('σσΣ'));

    // alias
    self::assertSame('Öäü', UTF8::ucword('öäü'));
  }
}
