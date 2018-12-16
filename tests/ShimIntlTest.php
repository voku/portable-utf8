<?php declare(strict_types=0);

use Normalizer as n;
use Symfony\Polyfill\Intl\Grapheme\Grapheme as p;
use voku\helper\UTF8;

/**
 * Class ShimIntlTest
 *
 * @internal
 */
final class ShimIntlTest extends \PHPUnit\Framework\TestCase
{
    public function testGraphemeExtractArrayError()
    {
        try {
            p::grapheme_extract([], 0);
            static::fail('Warning or notice expected');
        } catch (\PHPUnit\Framework\Error\Warning $e) {
            static::assertTrue(true, 'Regular PHP throws a warning');
        } catch (\PHPUnit\Framework\Error\Notice $e) {
            static::assertTrue(true, 'HHVM throws a notice');
        }
    }

    public function testGraphemeExtract()
    {
        static::assertFalse(p::grapheme_extract('abc', 1, -1));

        static::assertSame(\grapheme_extract('', 0), p::grapheme_extract('', 0));
        static::assertSame(\grapheme_extract('abc', 0), p::grapheme_extract('abc', 0));

        if (UTF8::getSupportInfo('mbstring_func_overload') !== true) {
            static::assertSame('국어', p::grapheme_extract('한국어', 2, \GRAPHEME_EXTR_COUNT, 3, $next));
            static::assertSame(9, $next);
        }

        static::assertSame('국어', \grapheme_extract('한국어', 2, \GRAPHEME_EXTR_COUNT, 3, $next));
        static::assertSame(9, $next);

        $next = 0;
        static::assertSame('한', p::grapheme_extract('한국어', 1, \GRAPHEME_EXTR_COUNT, $next, $next));
        static::assertSame('국', p::grapheme_extract('한국어', 1, \GRAPHEME_EXTR_COUNT, $next, $next));
        static::assertSame('어', p::grapheme_extract('한국어', 1, \GRAPHEME_EXTR_COUNT, $next, $next));
        static::assertFalse(p::grapheme_extract('한국어', 1, \GRAPHEME_EXTR_COUNT, $next, $next));

        static::assertSame(\str_repeat('-', 69000), p::grapheme_extract(\str_repeat('-', 70000), 69000, \GRAPHEME_EXTR_COUNT));

        if (UTF8::getSupportInfo('mbstring_func_overload') !== true) {
            static::assertSame('d', p::grapheme_extract('déjà', 2, \GRAPHEME_EXTR_MAXBYTES));
        }

        static::assertSame('dé', p::grapheme_extract('déjà', 2, \GRAPHEME_EXTR_MAXCHARS));

        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        static::assertFalse(@p::grapheme_extract([], 0));
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        static::assertFalse(@\grapheme_extract([], 0));
    }

    public function testGraphemeStrlen()
    {
        static::assertSame(3, \grapheme_strlen('한국어'));
        static::assertSame(3, \grapheme_strlen(n::normalize('한국어', n::NFD)));

        static::assertSame(3, p::grapheme_strlen('한국어'));
        static::assertSame(3, p::grapheme_strlen(n::normalize('한국어', n::NFD)));

        static::assertNull(p::grapheme_strlen("\xE9"));
    }

    public function testGraphemeSubstr()
    {
        $c = 'déjà';

        static::assertSame('jà', \grapheme_substr($c, 2));
        static::assertSame('jà', \grapheme_substr($c, -2));
        // The next 3 tests are disabled due to http://bugs.php.net/62759 and 55562
        //self::assertSame( "jà", grapheme_substr($c, -2,  3) );
        //self::assertSame( "", grapheme_substr($c, -1,  0) );
        //self::assertSame( false, grapheme_substr($c,  1, -4) );
        static::assertSame('j', \grapheme_substr($c, -2, -1));
        static::assertSame('', \grapheme_substr($c, -2, -2));
        static::assertFalse(\grapheme_substr($c, 5, 0));
        static::assertFalse(\grapheme_substr($c, -5, 0));

        static::assertSame('jà', p::grapheme_substr($c, 2));
        static::assertSame('jà', p::grapheme_substr($c, -2));
        static::assertSame('jà', p::grapheme_substr($c, -2, 3));
        static::assertSame('', p::grapheme_substr($c, -1, 0));
        static::assertFalse(p::grapheme_substr($c, 1, -4));
        static::assertSame('j', p::grapheme_substr($c, -2, -1));
        static::assertSame('', p::grapheme_substr($c, -2, -2));
        static::assertFalse(p::grapheme_substr($c, 5, 0));
        static::assertFalse(p::grapheme_substr($c, -5, 0));

        static::assertSame('jà', p::grapheme_substr($c, 2, 2147483647));
        static::assertSame('jà', p::grapheme_substr($c, -2, 2147483647));
        static::assertSame('jà', p::grapheme_substr($c, -2, 3));
        static::assertSame('', p::grapheme_substr($c, -1, 0));
        static::assertFalse(p::grapheme_substr($c, 1, -4));
        static::assertSame('j', p::grapheme_substr($c, -2, -1));
        static::assertSame('', p::grapheme_substr($c, -2, -2));
        static::assertFalse(p::grapheme_substr($c, 5, 0));
        static::assertFalse(p::grapheme_substr($c, -5, 0));
    }

    public function testGraphemeStrpos()
    {
        static::assertFalse(\grapheme_strpos('abc', ''));
        static::assertFalse(\grapheme_strpos('abc', 'd'));
        static::assertFalse(\grapheme_strpos('abc', 'a', 3));
        if (\defined('HHVM_VERSION_ID') || \PHP_VERSION_ID < 50535 || (50600 <= \PHP_VERSION_ID && \PHP_VERSION_ID < 50621) || (70000 <= \PHP_VERSION_ID && \PHP_VERSION_ID < 70006)) {
            static::assertSame(0, \grapheme_strpos('abc', 'a', -1));
        } else {
            $tmp = \grapheme_strpos('abc', 'a', -1);
            if ($tmp !== false && $tmp !== 0) { // polyfill will fail in some versions ...
                static::assertFalse($tmp);
            } else {
                static::assertTrue(true);
            }
        }
        static::assertSame(1, \grapheme_strpos('한국어', '국'));
        static::assertSame(3, \grapheme_stripos('DÉJÀ', 'à'));
        static::assertFalse(\grapheme_strrpos('한국어', ''));
        static::assertSame(1, \grapheme_strrpos('한국어', '국'));
        static::assertSame(3, \grapheme_strripos('DÉJÀ', 'à'));

        static::assertFalse(p::grapheme_strpos('abc', ''));
        static::assertFalse(p::grapheme_strpos('abc', 'd'));
        static::assertFalse(p::grapheme_strpos('abc', 'a', 3));
        if (\defined('HHVM_VERSION_ID') || \PHP_VERSION_ID < 50535 || (50600 <= \PHP_VERSION_ID && \PHP_VERSION_ID < 50621) || (70000 <= \PHP_VERSION_ID && \PHP_VERSION_ID < 70006)) {
            static::assertSame(0, p::grapheme_strpos('abc', 'a', -1));
        } else {
            $tmp = p::grapheme_strpos('abc', 'a', -1);
            if ($tmp !== false && $tmp !== 0) { // polyfill will fail in some versions ...
                static::assertFalse($tmp);
            } else {
                static::assertTrue(true);
            }
        }
        static::assertSame(1, p::grapheme_strpos('한국어', '국'));
        static::assertSame(3, p::grapheme_stripos('DÉJÀ', 'à'));
        static::assertFalse(p::grapheme_strrpos('한국어', ''));
        static::assertSame(1, p::grapheme_strrpos('한국어', '국'));
        static::assertSame(3, p::grapheme_strripos('DÉJÀ', 'à'));
        static::assertSame(16, p::grapheme_stripos('der Straße nach Paris', 'Paris'));
    }

    public function testGraphemeStrstr()
    {
        static::assertSame('국어', \grapheme_strstr('한국어', '국'));
        static::assertSame('ÉJÀ', \grapheme_stristr('DÉJÀ', 'é'));

        static::assertSame('국어', p::grapheme_strstr('한국어', '국'));
        static::assertSame('ÉJÀ', p::grapheme_stristr('DÉJÀ', 'é'));
        static::assertSame('Paris', p::grapheme_stristr('der Straße nach Paris', 'Paris'));
    }

    public function testGraphemeBugs()
    {
        if (\extension_loaded('intl') && (50418 > \PHP_VERSION_ID || 50500 === \PHP_VERSION_ID)) {
            // Buggy behavior see https://bugs.php.net/61860
            static::assertSame(17, \grapheme_stripos('der Straße nach Paris', 'Paris'));
            static::assertSame('aris', \grapheme_stristr('der Straße nach Paris', 'Paris'));
        } else {
            static::assertSame(16, \grapheme_stripos('der Straße nach Paris', 'Paris'));
            static::assertSame('Paris', \grapheme_stristr('der Straße nach Paris', 'Paris'));
        }
    }
}
