<?php

declare(strict_types=1);

namespace voku\tests;

use Symfony\Polyfill\Iconv\Iconv as p;
use voku\helper\UTF8;

/**
 * Class ShimIconvTest
 *
 * @internal
 */
final class ShimIconvTest extends \PHPUnit\Framework\TestCase
{
    public function testIconv()
    {
        // Native iconv() behavior varies between versions and OS for these two tests
        // See e.g. https://bugs.php.net/52211
        if (
        !\defined('HHVM_VERSION')
        &&
        (
            \PHP_VERSION_ID >= 50610
            ||
            (\PHP_VERSION_ID >= 50526 && \PHP_VERSION_ID < 50600)
            ||
            '\\' === \DIRECTORY_SEPARATOR
        )
    ) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertSame(\PHP_VERSION_ID >= 50400 ? false : 'n', @\iconv('UTF-8', 'ISO-8859-1', 'nœud'));
            static::assertSame('nud', \iconv('UTF-8', 'ISO-8859-1//IGNORE', 'nœud'));
        } elseif (\PHP_VERSION_ID >= 50400) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertFalse(@\iconv('UTF-8', 'ISO-8859-1', 'nœud'));

            // need testing
            if (\PHP_VERSION_ID < 70000) {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                static::assertFalse(@\iconv('UTF-8', 'ISO-8859-1//IGNORE', 'nœud'));
            } else {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                static::assertSame('nud', @\iconv('UTF-8', 'ISO-8859-1//IGNORE', 'nœud'));
            }
        } else {

      // See e.g. https://bugs.php.net/52211
            /** @noinspection PhpUndefinedConstantInspection */
            if (\defined('HHVM_VERSION') && HHVM_VERSION_ID >= 30901) {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                static::assertFalse(@\iconv('UTF-8', 'ISO-8859-1', 'nœud'));
                static::assertSame('nud', \iconv('UTF-8', 'ISO-8859-1//IGNORE', 'nœud'));
            } else {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                static::assertSame('n', @\iconv('UTF-8', 'ISO-8859-1', 'nœud'));
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                static::assertSame('nud', @\iconv('UTF-8', 'ISO-8859-1//IGNORE', 'nœud'));
            }
        }

        // The recent Windows behavior is the most useful
        static::assertFalse(p::iconv('UTF-8', 'ISO-8859-1', 'nœud'));

        if (UTF8::getSupportInfo('mbstring_func_overload') !== true) {
            static::assertSame('nud', p::iconv('UTF-8', 'ISO-8859-1//IGNORE', 'nœud'));
            static::assertSame(\utf8_decode('déjà'), p::iconv('CP1252', 'ISO-8859-1', \utf8_decode('déjà')));
            static::assertSame('déjà', p::iconv('UTF-8', 'utf8', 'déjà'));
            static::assertSame('deja noeud', p::iconv('UTF-8', 'US-ASCII//TRANSLIT', 'déjà nœud'));
        }

        static::assertSame('4', p::iconv('UTF-8', 'UTF-8', 4));
    }

    public function testIconvGetEncoding()
    {
        $a = [
            'input_encoding'    => 'UTF-8',
            'output_encoding'   => 'UTF-8',
            'internal_encoding' => 'UTF-8',
        ];

        foreach ($a as $t => $e) {
            static::assertTrue(p::iconv_set_encoding($t, $e));
            static::assertSame($e, p::iconv_get_encoding($t));
        }

        static::assertSame($a, p::iconv_get_encoding('all'));

        static::assertFalse(p::iconv_set_encoding('foo', 'UTF-8'));
    }

    public function testIconvMimeDecode()
    {
        $this->expectException(\PHPUnit\Framework\ExpectationFailedException::class);

        static::assertSame('Legal encoded-word: * .', p::iconv_mime_decode('Legal encoded-word: =?utf-8?B?Kg==?= .'));
        static::assertSame('Legal encoded-word: * .', p::iconv_mime_decode('Legal encoded-word: =?utf-8?Q?*?= .'));
        static::assertSame(
            'Illegal encoded-word:  .',
            p::iconv_mime_decode(
                'Illegal encoded-word: =?utf-8?Q?' . \chr(0xA1) . '?= .',
                \ICONV_MIME_DECODE_CONTINUE_ON_ERROR
        )
    );

        p::iconv_mime_decode('Illegal encoded-word: =?utf-8?Q?' . \chr(0xA1) . '?= .');
        static::assertFalse(true, 'An illegal encoded-word should trigger a notice');
    }

    public function testIconvMimeDecodeHeaders()
    {
        $headers = <<<HEADERS
From: =?UTF-8?B?PGZvb0BleGFtcGxlLmNvbT4=?=
Subject: =?ks_c_5601-1987?B?UkU6odk=?= Foo
X-Bar: =?cp949?B?UkU6odk=?= Foo
X-Bar: =?cp949?B?UkU6odk=?= =?UTF-8?Q?Bar?=
To: <test@example.com>
HEADERS;

        $result = [
            'From'    => '<foo@example.com>',
            'Subject' => '=?ks_c_5601-1987?B?UkU6odk=?= Foo',
            'X-Bar'   => [
                'RE:☆ Foo',
                'RE:☆Bar',
            ],
            'To' => '<test@example.com>',
        ];

        static::assertSame($result, p::iconv_mime_decode_headers($headers, \ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8'));
    }

    public function testIconvMimeEncode()
    {
        $text = "\xE3\x83\x86\xE3\x82\xB9\xE3\x83\x88\xE3\x83\x86\xE3\x82\xB9\xE3\x83\x88";
        $options = [
            'scheme'         => 'Q',
            'input-charset'  => 'UTF-8',
            'output-charset' => 'UTF-8',
            'line-length'    => 30,
        ];

        static::assertSame(
            "Subject: =?UTF-8?Q?=E3=83=86?=\r\n =?UTF-8?Q?=E3=82=B9?=\r\n =?UTF-8?Q?=E3=83=88?=\r\n =?UTF-8?Q?=E3=83=86?=\r\n =?UTF-8?Q?=E3=82=B9?=\r\n =?UTF-8?Q?=E3=83=88?=",
            p::iconv_mime_encode('Subject', $text, $options)
    );
    }

    public function testIconvStrPos()
    {
        static::assertSame(1, p::iconv_strpos('11--', '1-', 0, 'UTF-8'));
        static::assertSame(2, p::iconv_strpos('-11--', '1-', 0, 'UTF-8'));
        static::assertFalse(p::iconv_strrpos('한국어', '', 'UTF-8'));
        static::assertSame(1, p::iconv_strrpos('한국어', '국', 'UTF-8'));

        if (UTF8::getSupportInfo('mbstring_func_overload') !== true) {
            static::assertFalse(p::iconv_strrpos('한국어', ''));
            static::assertSame(9, p::iconv_strrpos('中文空白-ÖÄÜ-中文空白', '中'));
        }

        static::assertSame(1, p::iconv_strrpos('한국어', '국'));
        static::assertSame(6, p::iconv_strrpos('κόσμε-κόσμε', 'κ'));
        static::assertSame(13, p::iconv_strrpos('test κόσμε κόσμε test', 'σ'));
    }

    public function testIconvStrlen()
    {
        if (UTF8::getSupportInfo('mbstring_func_overload') !== true) {
            static::assertSame(4, p::iconv_strlen('déjà'));
        }
        static::assertSame(3, p::iconv_strlen('한국어'));

        if (UTF8::getSupportInfo('mbstring_func_overload') !== true) {
            static::assertSame(4, p::strlen1('déjà'));
            static::assertSame(3, p::strlen2('한국어'));

            static::assertSame(4, p::strlen1('déjà'));
            static::assertSame(3, p::strlen2('한국어'));
        }
    }

    public function testIconvSubstr()
    {
        static::assertSame('x', p::iconv_substr('x', 0, 1, 'UTF-8'));
    }
}
