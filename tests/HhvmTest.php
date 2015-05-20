<?php

use voku\helper\UTF8 as u;

/**
 * Class HhvmTest
 */
class HhvmTest extends \PHPUnit_Framework_TestCase
{
  function test1()
  {
    /** @noinspection PhpUsageOfSilenceOperatorInspection */
    self::assertFalse( @grapheme_extract(array(), 0) );
  }

  function test2()
  {
    // Negative offset are not allowed but native PHP silently casts them to zero
    self::assertSame( 0, grapheme_strpos('abc', 'a', -1) );
  }

  function test3()
  {
    self::assertSame( 'ÉJÀ', grapheme_stristr('DÉJÀ', 'é') );
  }

  function test4()
  {
    if (PHP_VERSION_ID >= 50400) {
      self::assertSame( '1×234¡56', number_format(1234.557, 2, '¡', '×') );
    }
  }

  function test5()
  {
    self::assertEquals( 'nàlizæti', grapheme_substr('Iñtërnâtiônàlizætiøn', 10, -2) );
  }

  function test6()
  {
    self::assertNull( grapheme_strlen("\xE9 invalid UTF-8") );
  }

  function test7()
  {
    self::assertFalse( \Normalizer::normalize("\xE9 invalid UTF-8") );
  }

  function test8()
  {
    /** @noinspection PhpUsageOfSilenceOperatorInspection */
    self::assertSame( '', @(substr(array(), 0).'') );
  }
}