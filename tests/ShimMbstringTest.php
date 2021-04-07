<?php

declare(strict_types=1);

namespace voku\tests;

use Normalizer as n;
use Symfony\Polyfill\Mbstring\Mbstring as p;
use voku\helper\UTF8;

/**
 * Class ShimMbstringTest
 *
 * @internal
 */
final class ShimMbstringTest extends \PHPUnit\Framework\TestCase
{
    public function testTestmbStubs()
    {
        if (!\voku\helper\Bootup::is_php('8.0')) {
            static::assertFalse(p::mb_substitute_character('?'));
            static::assertSame('none', p::mb_substitute_character());
        }

        static::assertContains('UTF-8', p::mb_list_encodings());

        static::assertTrue(p::mb_internal_encoding('utf8'));

        if (!\voku\helper\Bootup::is_php('8.0')) {
            static::assertFalse(p::mb_internal_encoding('no-no'));
        }

        static::assertSame('UTF-8', p::mb_internal_encoding());

        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        @p::mb_encode_mimeheader('');
        static::assertTrue(true, 'mb_encode_mimeheader() is bugged. Please use iconv_mime_encode() instead');
    }

    public function testmbConvertEncoding()
    {
        static::assertSame(\utf8_decode('déjà'), p::mb_convert_encoding('déjà', 'Windows-1252'));
        static::assertSame(\base64_encode('déjà'), p::mb_convert_encoding('déjà', 'Base64'));
        static::assertSame('&#23455;<&>d&eacute;j&agrave;', p::mb_convert_encoding('実<&>déjà', 'Html-entities'));
        static::assertSame('déjà', p::mb_convert_encoding(\base64_encode('déjà'), 'Utf-8', 'Base64'));
        static::assertSame('déjà', p::mb_convert_encoding('d&eacute;j&#224;', 'Utf-8', 'Html-entities'));
        static::assertSame('déjà', p::mb_convert_encoding(\utf8_decode('déjà'), 'Utf-8', 'ASCII,ISO-2022-JP,UTF-8,ISO-8859-1'));
        static::assertSame(
            'déjà',
            p::mb_convert_encoding(
                \utf8_decode('déjà'),
                'Utf-8',
                [
                    'ASCII',
                    'ISO-2022-JP',
                    'UTF-8',
                    'ISO-8859-1',
                ]
            )
        );
    }

    public function testStrCase()
    {
        if (UTF8::getSupportInfo('mbstring_func_overload') !== true) {
            static::assertSame('déjà σσς iiıi', p::mb_strtolower('DÉJÀ Σσς İIıi'));
            static::assertSame('DÉJÀ ΣΣΣ İIII', p::mb_strtoupper('Déjà Σσς İIıi'));
            static::assertSame('Déjà Σσσ Iı Ii İi', p::mb_convert_case('DÉJÀ ΣΣΣ ıı iI İİ', \MB_CASE_TITLE));
        } else {
            static::markTestSkipped('mbstring_func_overload is used ... so skip this test ...');
        }
    }

    public function testmbStrlen()
    {
        static::assertSame(3, \mb_strlen('한국어'));
        static::assertSame(8, \mb_strlen(n::normalize('한국어', n::NFD)));

        static::assertSame(3, p::mb_strlen('한국어'));
        static::assertSame(8, p::mb_strlen(n::normalize('한국어', n::NFD)));
    }

    public function testmbSubstr()
    {
        $c = 'déjà';

        if (\PHP_VERSION_ID >= 50408) {
            static::assertSame('jà', \mb_substr($c, 2, null));
        }

        static::assertSame('jà', \mb_substr($c, 2));
        static::assertSame('jà', \mb_substr($c, -2));
        static::assertSame('jà', \mb_substr($c, -2, 3));
        static::assertSame('', \mb_substr($c, -1, 0));
        static::assertSame('', \mb_substr($c, 1, -4));
        static::assertSame('j', \mb_substr($c, -2, -1));
        static::assertSame('', \mb_substr($c, -2, -2));

        if (UTF8::getSupportInfo('mbstring_func_overload') !== true) {
            static::assertSame('', \mb_substr($c, 5, 0));
        }
        static::assertSame('', \mb_substr($c, -5, 0));

        static::assertSame('jà', p::mb_substr($c, 2, null));
        static::assertSame('jà', p::mb_substr($c, 2));
        static::assertSame('jà', p::mb_substr($c, -2));
        static::assertSame('jà', p::mb_substr($c, -2, 3));
        static::assertSame('', p::mb_substr($c, -1, 0));
        static::assertSame('', p::mb_substr($c, 1, -4));
        static::assertSame('j', p::mb_substr($c, -2, -1));
        static::assertSame('', p::mb_substr($c, -2, -2));
        static::assertSame('', p::mb_substr($c, 5, 0));
        static::assertSame('', p::mb_substr($c, -5, 0));
    }

    public function testmbStrpos()
    {
        if (\voku\helper\Bootup::is_php('8.0')) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertSame(0, @\mb_strpos('abc', ''));
        } else {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertFalse(@\mb_strpos('abc', ''));
        }
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        static::assertFalse(@\mb_strpos('abc', 'a', -1));
        static::assertFalse(\mb_strpos('abc', 'd'));
        static::assertFalse(\mb_strpos('abc', 'a', 3));
        static::assertSame(1, \mb_strpos('한국어', '국'));
        static::assertSame(3, \mb_stripos('DÉJÀ', 'à'));
        if (\voku\helper\Bootup::is_php('8.0')) {
            static::assertSame(3, \mb_strrpos('한국어', '')); // ?
        } else {
            static::assertFalse(\mb_strrpos('한국어', ''));
        }
        static::assertSame(1, \mb_strrpos('한국어', '국'));
        static::assertSame(3, \mb_strripos('DÉJÀ', 'à'));
        static::assertSame(1, \mb_stripos('aςσb', 'ΣΣ'));
        static::assertSame(1, \mb_strripos('aςσb', 'ΣΣ'));
        static::assertSame(3, \mb_strrpos('ababab', 'b', -2));


        if (!\voku\helper\Bootup::is_php('8.0')) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertFalse(@p::mb_strpos('abc', ''));
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertFalse(@p::mb_strpos('abc', 'a', -1));
        }

        static::assertFalse(p::mb_strpos('abc', 'd'));
        static::assertFalse(p::mb_strpos('abc', 'a', 3));
        static::assertSame(1, p::mb_strpos('한국어', '국'));

        if (\voku\helper\Bootup::is_php('8.0')) {
            static::assertSame(3, p::mb_strrpos('한국어', ''));
        } else {
            static::assertFalse(p::mb_strrpos('한국어', ''));
        }

        static::assertSame(1, p::mb_strrpos('한국어', '국'));

        if (UTF8::getSupportInfo('mbstring_func_overload') !== true) {
            static::assertSame(3, p::mb_stripos('DÉJÀ', 'à'));
            static::assertSame(3, p::mb_strripos('DÉJÀ', 'à'));
            static::assertSame(1, p::mb_stripos('aςσb', 'ΣΣ'));
            static::assertSame(1, p::mb_strripos('aςσb', 'ΣΣ'));
        }

        static::assertSame(3, p::mb_strrpos('ababab', 'b', -2));
    }

    public function testTestmbStrposEmptyDelimiter()
    {
        try {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            @\mb_strpos('abc', '');
            static::assertTrue(true, 'The previous line should trigger a warning (Empty delimiter)');
        } catch (\PHPUnit\Framework\Error\Warning $e) {
            p::mb_strpos('abc', '');
            static::assertTrue(true, 'The previous line should trigger a warning (Empty delimiter)');
        }
    }

    public function testTestmbStrstr()
    {
        static::assertSame('국어', \mb_strstr('한국어', '국'));

        if (UTF8::getSupportInfo('mbstring_func_overload') !== true) {
            static::assertSame('ÉJÀ', \mb_stristr('DÉJÀ', 'é'));
            static::assertSame('ÉJÀ', p::mb_stristr('DÉJÀ', 'é'));
            static::assertSame('ÉJÀDÉJÀ', p::mb_stristr('DÉJÀDÉJÀ', 'é'));
            static::assertSame('D', p::mb_stristr('DÉJÀDÉJÀ', 'é', true));
            static::assertSame('DÉJÀD', p::mb_strrichr('DÉJÀDÉJÀ', 'é', true));
            static::assertSame('ςσb', p::mb_stristr('aςσb', 'ΣΣ'));
            static::assertSame('a', p::mb_stristr('aςσb', 'ΣΣ', true));
            static::assertSame('Paris', p::mb_stristr('der Straße nach Paris', 'Paris'));

            static::assertSame('ÉJÀ', p::mb_strrichr('DÉJÀDÉJÀ', 'é'));
        }

        static::assertSame('국어', p::mb_strstr('한국어', '국'));

        static::assertSame('éjàdéjà', p::mb_strstr('déjàdéjà', 'é'));
        static::assertSame('éjà', p::mb_strrchr('déjàdéjà', 'é'));

        static::assertSame('d', p::mb_strstr('déjàdéjà', 'é', true));
        static::assertSame('déjàd', p::mb_strrchr('déjàdéjà', 'é', true));
    }

    public function testmbCheckEncoding()
    {
        static::assertFalse(p::mb_check_encoding());
        static::assertTrue(p::mb_check_encoding('aςσb', 'UTF8'));
        static::assertTrue(p::mb_check_encoding('abc', 'ASCII'));
    }

    public function testmbDetectEncoding()
    {
        static::assertSame('ASCII', p::mb_detect_encoding('abc', \mb_detect_order(), true));
        static::assertSame('UTF-8', p::mb_detect_encoding('abc', 'UTF8, ASCII', true));
        static::assertSame(
            'ISO-8859-1',
            p::mb_detect_encoding(
                "\x9D",
                [
                    'UTF-8',
                    'ASCII',
                    'ISO-8859-1',
                ],
                true
            )
        );
    }

    public function testmbDetectOrder()
    {
        static::assertSame(
            [
                'ASCII',
                'UTF-8',
            ],
            p::mb_detect_order()
        );
        static::assertTrue(p::mb_detect_order('UTF-8, ASCII'));
        static::assertSame(
            [
                'UTF-8',
                'ASCII',
            ],
            p::mb_detect_order()
        );
    }

    public function testmbLanguage()
    {
        static::assertSame('neutral', p::mb_language());
        static::assertTrue(p::mb_language('UNI'));

        if (!\voku\helper\Bootup::is_php('8.0')) {
            static::assertFalse(p::mb_language('ABC'));
        }

        static::assertSame('uni', p::mb_language());
    }

    public function testmbEncodingAliases()
    {
        static::assertSame(['utf8'], p::mb_encoding_aliases('UTF-8'));
        static::assertFalse(p::mb_encoding_aliases('ASCII'));
    }

    public function testmbStrwidth()
    {
        static::assertSame(3, p::mb_strwidth("\0実"));
        static::assertSame(4, p::mb_strwidth('déjà'));
        static::assertSame(4, p::mb_strwidth(\utf8_decode('déjà'), 'CP1252'));
    }
}
