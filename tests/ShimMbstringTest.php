<?php

use Normalizer as n;
use voku\helper\shim\Mbstring as p;

/**
 * Class ShimMbstringTest
 */
class ShimMbstringTest extends PHPUnit_Framework_TestCase
{
  /**
   * @expectedException PHPUnit_Framework_Error_Warning
   */
  function testmb_stubs()
  {
    self::assertFalse(p::mb_substitute_character('?'));
    self::assertSame('none', p::mb_substitute_character());

    self::assertContains('UTF-8', p::mb_list_encodings());

    self::assertTrue(p::mb_internal_encoding('utf8'));
    self::assertFalse(p::mb_internal_encoding('no-no'));
    self::assertSame('UTF-8', p::mb_internal_encoding());

    p::mb_encode_mimeheader('');
    self::assertFalse(true, 'mb_encode_mimeheader() is bugged. Please use iconv_mime_encode() instead');
  }

  function testmb_convert_encoding()
  {
    self::assertSame(utf8_decode('déjà'), p::mb_convert_encoding('déjà', 'Windows-1252'));
    self::assertSame(base64_encode('déjà'), p::mb_convert_encoding('déjà', 'Base64'));
    self::assertSame('&#23455;<&>d&eacute;j&agrave;', p::mb_convert_encoding('実<&>déjà', 'Html-entities'));
    self::assertSame('déjà', p::mb_convert_encoding(base64_encode('déjà'), 'Utf-8', 'Base64'));
    self::assertSame('déjà', p::mb_convert_encoding('d&eacute;j&#224;', 'Utf-8', 'Html-entities'));
  }

  function testStrCase()
  {
    self::assertSame('déjà σσς iiıi', p::mb_strtolower('DÉJÀ Σσς İIıi'));
    self::assertSame('DÉJÀ ΣΣΣ İIII', p::mb_strtoupper('Déjà Σσς İIıi'));
    self::assertSame('Déjà Σσσ Iı Ii İi', p::mb_convert_case('DÉJÀ ΣΣΣ ıı iI İİ', MB_CASE_TITLE));
  }

  function testmb_strlen()
  {
    self::assertSame(3, mb_strlen('한국어'));
    self::assertSame(8, mb_strlen(n::normalize('한국어', n::NFD)));

    self::assertSame(3, p::mb_strlen('한국어'));
    self::assertSame(8, p::mb_strlen(n::normalize('한국어', n::NFD)));
  }

  function testmb_substr()
  {
    $c = "déjà";

    if (PHP_VERSION_ID >= 50408) {
      self::assertSame("jà", mb_substr($c, 2, null));
    }

    self::assertSame("jà", mb_substr($c, 2));
    self::assertSame("jà", mb_substr($c, -2));
    self::assertSame("jà", mb_substr($c, -2, 3));
    self::assertSame("", mb_substr($c, -1, 0));
    self::assertSame("", mb_substr($c, 1, -4));
    self::assertSame("j", mb_substr($c, -2, -1));
    self::assertSame("", mb_substr($c, -2, -2));
    self::assertSame("", mb_substr($c, 5, 0));
    self::assertSame("", mb_substr($c, -5, 0));

    self::assertSame("jà", p::mb_substr($c, 2, null));
    self::assertSame("jà", p::mb_substr($c, 2));
    self::assertSame("jà", p::mb_substr($c, -2));
    self::assertSame("jà", p::mb_substr($c, -2, 3));
    self::assertSame("", p::mb_substr($c, -1, 0));
    self::assertSame("", p::mb_substr($c, 1, -4));
    self::assertSame("j", p::mb_substr($c, -2, -1));
    self::assertSame("", p::mb_substr($c, -2, -2));
    self::assertSame("", p::mb_substr($c, 5, 0));
    self::assertSame("", p::mb_substr($c, -5, 0));
  }

  function testmb_strpos()
  {
    /** @noinspection PhpUsageOfSilenceOperatorInspection */
    self::assertSame(false, @mb_strpos('abc', ''));
    /** @noinspection PhpUsageOfSilenceOperatorInspection */
    self::assertSame(false, @mb_strpos('abc', 'a', -1));
    self::assertSame(false, mb_strpos('abc', 'd'));
    self::assertSame(false, mb_strpos('abc', 'a', 3));
    self::assertSame(1, mb_strpos('한국어', '국'));
    self::assertSame(3, mb_stripos('DÉJÀ', 'à'));
    self::assertSame(false, mb_strrpos('한국어', ''));
    self::assertSame(1, mb_strrpos('한국어', '국'));
    self::assertSame(3, mb_strripos('DÉJÀ', 'à'));
    self::assertSame(1, mb_stripos('aςσb', 'ΣΣ'));
    self::assertSame(1, mb_strripos('aςσb', 'ΣΣ'));
    self::assertSame(3, mb_strrpos('ababab', 'b', -2));

    /** @noinspection PhpUsageOfSilenceOperatorInspection */
    self::assertSame(false, @p::mb_strpos('abc', ''));
    /** @noinspection PhpUsageOfSilenceOperatorInspection */
    self::assertSame(false, @p::mb_strpos('abc', 'a', -1));
    self::assertSame(false, p::mb_strpos('abc', 'd'));
    self::assertSame(false, p::mb_strpos('abc', 'a', 3));
    self::assertSame(1, p::mb_strpos('한국어', '국'));
    self::assertSame(3, p::mb_stripos('DÉJÀ', 'à'));
    self::assertSame(false, p::mb_strrpos('한국어', ''));
    self::assertSame(1, p::mb_strrpos('한국어', '국'));
    self::assertSame(3, p::mb_strripos('DÉJÀ', 'à'));
    self::assertSame(1, p::mb_stripos('aςσb', 'ΣΣ'));
    self::assertSame(1, p::mb_strripos('aςσb', 'ΣΣ'));
    self::assertSame(3, p::mb_strrpos('ababab', 'b', -2));
  }

  /**
   * @expectedException PHPUnit_Framework_Error_Warning
   */
  function testmb_strpos_empty_delimiter()
  {
    try {
      mb_strpos('abc', '');
      self::assertFalse(true, "The previous line should trigger a warning (Empty delimiter)");
    }
    catch (\PHPUnit_Framework_Error_Warning $e) {
      p::mb_strpos('abc', '');
      self::assertFalse(true, "The previous line should trigger a warning (Empty delimiter)");
    }
  }

  /**
   * @expectedException PHPUnit_Framework_Error_Warning
   */
  function testmb_strpos_negative_offset()
  {
    try {
      mb_strpos('abc', 'a', -1);
      self::assertFalse(true, "The previous line should trigger a warning (Offset not contained in string)");
    }
    catch (\PHPUnit_Framework_Error_Warning $e) {
      p::mb_strpos('abc', 'a', -1);
      self::assertFalse(true, "The previous line should trigger a warning (Offset not contained in string)");
    }
  }

  function testmb_strstr()
  {
    self::assertSame('국어', mb_strstr('한국어', '국'));
    self::assertSame('ÉJÀ', mb_stristr('DÉJÀ', 'é'));

    self::assertSame('국어', p::mb_strstr('한국어', '국'));
    self::assertSame('ÉJÀ', p::mb_stristr('DÉJÀ', 'é'));

    self::assertSame('éjàdéjà', p::mb_strstr('déjàdéjà', 'é'));
    self::assertSame('ÉJÀDÉJÀ', p::mb_stristr('DÉJÀDÉJÀ', 'é'));
    self::assertSame('ςσb', p::mb_stristr('aςσb', 'ΣΣ'));
    self::assertSame('éjà', p::mb_strrchr('déjàdéjà', 'é'));
    self::assertSame('ÉJÀ', p::mb_strrichr('DÉJÀDÉJÀ', 'é'));

    self::assertSame('d', p::mb_strstr('déjàdéjà', 'é', true));
    self::assertSame('D', p::mb_stristr('DÉJÀDÉJÀ', 'é', true));
    self::assertSame('a', p::mb_stristr('aςσb', 'ΣΣ', true));
    self::assertSame('déjàd', p::mb_strrchr('déjàdéjà', 'é', true));
    self::assertSame('DÉJÀD', p::mb_strrichr('DÉJÀDÉJÀ', 'é', true));
    self::assertSame('Paris', p::mb_stristr('der Straße nach Paris', 'Paris'));
  }

  function testmb_check_encoding()
  {
    self::assertFalse(p::mb_check_encoding());
    self::assertTrue(p::mb_check_encoding('aςσb', 'UTF8'));
    self::assertTrue(p::mb_check_encoding('abc', 'ASCII'));
  }

  function testmb_detect_encoding()
  {
    self::assertSame('ASCII', p::mb_detect_encoding('abc'));
    self::assertSame('UTF8', p::mb_detect_encoding('abc', 'UTF8, ASCII'));
    self::assertSame(
        'ISO-8859-1',
        p::mb_detect_encoding(
            "\x9D",
            array(
                'UTF-8',
                'ASCII',
                'ISO-8859-1',
            )
        )
    );
  }

  function testmb_detect_order()
  {
    self::assertSame(
        array(
            'ASCII',
            'UTF-8',
        ),
        p::mb_detect_order()
    );
    self::assertTrue(p::mb_detect_order('UTF-8, ASCII'));
    self::assertSame(
        array(
            'UTF-8',
            'ASCII',
        ),
        p::mb_detect_order()
    );
  }

  function testmb_language()
  {
    self::assertSame('neutral', p::mb_language());
    self::assertTrue(p::mb_language('UNI'));
    self::assertFalse(p::mb_language('ABC'));
    self::assertSame('uni', p::mb_language());
  }

  function testmb_encoding_aliases()
  {
    self::assertSame(array('utf8'), p::mb_encoding_aliases('UTF-8'));
    self::assertFalse(p::mb_encoding_aliases('ASCII'));
  }

  function testmb_strwidth()
  {
    self::assertSame(2, p::mb_strwidth("\0実"));
    self::assertSame(4, p::mb_strwidth('déjà'));
    self::assertSame(4, p::mb_strwidth(utf8_decode('déjà'), 'CP1252'));
  }
}
