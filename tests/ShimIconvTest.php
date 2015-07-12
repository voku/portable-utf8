<?php

use voku\helper\shim\Iconv as p;

/**
 * Class ShimIconvTest
 */
class ShimIconvTest extends PHPUnit_Framework_TestCase
{
  function testIconv()
  {
    // Native iconv() behavior varies between versions and OS for these two tests
    // See e.g. https://bugs.php.net/52211
    if (!defined('HHVM_VERSION') && (PHP_VERSION_ID >= 50610 || (PHP_VERSION_ID >= 50526 && PHP_VERSION_ID < 50600) || '\\' === DIRECTORY_SEPARATOR)) {
      /** @noinspection PhpUsageOfSilenceOperatorInspection */
      self::assertSame(PHP_VERSION_ID >= 50400 ? false : 'n', @iconv('UTF-8', 'ISO-8859-1', 'nœud'));
      self::assertSame('nud', iconv('UTF-8', 'ISO-8859-1//IGNORE', 'nœud'));
    } else if (PHP_VERSION_ID >= 50400) {
      /** @noinspection PhpUsageOfSilenceOperatorInspection */
      self::assertSame(false, @iconv('UTF-8', 'ISO-8859-1', 'nœud'));

      // need testing
      if (PHP_VERSION_ID < 70000) {
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        self::assertSame(false, @iconv('UTF-8', 'ISO-8859-1//IGNORE', 'nœud'));
      } else {
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        self::assertSame('nud', @iconv('UTF-8', 'ISO-8859-1//IGNORE', 'nœud'));
      }

    } else {
      /** @noinspection PhpUsageOfSilenceOperatorInspection */
      self::assertSame('n', @iconv('UTF-8', 'ISO-8859-1', 'nœud'));
      /** @noinspection PhpUsageOfSilenceOperatorInspection */
      self::assertSame('nud', @iconv('UTF-8', 'ISO-8859-1//IGNORE', 'nœud'));
    }

    // The recent Windows behavior is the most useful
    self::assertFalse(p::iconv('UTF-8', 'ISO-8859-1', 'nœud'));
    self::assertSame('nud', p::iconv('UTF-8', 'ISO-8859-1//IGNORE', 'nœud'));

    self::assertSame(utf8_decode('déjà'), p::iconv('CP1252', 'ISO-8859-1', utf8_decode('déjà')));
    self::assertSame('déjà', p::iconv('UTF-8', 'utf8', 'déjà'));
    self::assertSame('deja noeud', p::iconv('UTF-8', 'US-ASCII//TRANSLIT', 'déjà nœud'));

    self::assertSame('4', p::iconv('UTF-8', 'UTF-8', 4));
  }

  function testIconvStrlen()
  {
    self::assertSame(4, p::iconv_strlen('déjà'));
    self::assertSame(3, p::iconv_strlen('한국어'));

    self::assertSame(4, p::strlen1('déjà'));
    self::assertSame(3, p::strlen2('한국어'));

    self::assertSame(4, p::strlen1('déjà'));
    self::assertSame(3, p::strlen2('한국어'));
  }

  function testIconvStrPos()
  {
    self::assertSame(1, p::iconv_strpos('11--', '1-', 0, 'UTF-8'));
    self::assertSame(2, p::iconv_strpos('-11--', '1-', 0, 'UTF-8'));
    self::assertSame(false, p::iconv_strrpos('한국어', '', 'UTF-8'));
    self::assertSame(1, p::iconv_strrpos('한국어', '국', 'UTF-8'));
  }

  function testIconvSubstr()
  {
    self::assertSame('x', p::iconv_substr('x', 0, 1, 'UTF-8'));
  }

  function testIconvMimeEncode()
  {
    $text = "\xE3\x83\x86\xE3\x82\xB9\xE3\x83\x88\xE3\x83\x86\xE3\x82\xB9\xE3\x83\x88";
    $options = array(
        'scheme'         => 'Q',
        'input-charset'  => 'UTF-8',
        'output-charset' => 'UTF-8',
        'line-length'    => 30,
    );

    self::assertSame(
        "Subject: =?UTF-8?Q?=E3=83=86?=\r\n =?UTF-8?Q?=E3=82=B9?=\r\n =?UTF-8?Q?=E3=83=88?=\r\n =?UTF-8?Q?=E3=83=86?=\r\n =?UTF-8?Q?=E3=82=B9?=\r\n =?UTF-8?Q?=E3=83=88?=",
        p::iconv_mime_encode('Subject', $text, $options)
    );
  }

  /**
   * @expectedException PHPUnit_Framework_Error_Notice
   */
  function testIconvMimeDecode()
  {
    self::assertSame('Legal encoded-word: * .', p::iconv_mime_decode("Legal encoded-word: =?utf-8?B?Kg==?= ."));
    self::assertSame('Legal encoded-word: * .', p::iconv_mime_decode("Legal encoded-word: =?utf-8?Q?*?= ."));
    self::assertSame(
        'Illegal encoded-word:  .',
        p::iconv_mime_decode(
            "Illegal encoded-word: =?utf-8?Q?" . chr(0xA1) . "?= .",
            ICONV_MIME_DECODE_CONTINUE_ON_ERROR
        )
    );

    p::iconv_mime_decode("Illegal encoded-word: =?utf-8?Q?" . chr(0xA1) . "?= .");
    self::assertFalse(true, "An illegal encoded-word should trigger a notice");
  }

  function testIconvMimeDecodeHeaders()
  {
    $headers = <<<HEADERS
From: =?UTF-8?B?PGZvb0BleGFtcGxlLmNvbT4=?=
Subject: =?ks_c_5601-1987?B?UkU6odk=?= Foo
X-Bar: =?cp949?B?UkU6odk=?= Foo
X-Bar: =?cp949?B?UkU6odk=?= =?UTF-8?Q?Bar?=
To: <test@example.com>
HEADERS;

    $result = array(
        'From'    => '<foo@example.com>',
        'Subject' => '=?ks_c_5601-1987?B?UkU6odk=?= Foo',
        'X-Bar'   => array(
            'RE:☆ Foo',
            'RE:☆Bar',
        ),
        'To'      => '<test@example.com>',
    );

    self::assertSame($result, p::iconv_mime_decode_headers($headers, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8'));
  }

  function testIconvGetEncoding()
  {
    $a = array(
        'input_encoding'    => 'UTF-8',
        'output_encoding'   => 'UTF-8',
        'internal_encoding' => 'UTF-8',
    );

    foreach ($a as $t => $e) {
      self::assertTrue(p::iconv_set_encoding($t, $e));
      self::assertSame($e, p::iconv_get_encoding($t));
    }

    self::assertSame($a, p::iconv_get_encoding('all'));

    self::assertFalse(p::iconv_set_encoding('foo', 'UTF-8'));
  }
}
