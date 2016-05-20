<?php

use Normalizer as n;
use Symfony\Polyfill\Intl\Grapheme\Grapheme as p;

/**
 * Class ShimIntlTest
 */
class ShimIntlTest extends PHPUnit_Framework_TestCase
{
  public function testGrapheme_extract_arrayError()
  {
    try {
      p::grapheme_extract(array(), 0);
      self::fail('Warning or notice expected');
    }
    catch (\PHPUnit_Framework_Error_Warning $e) {
      self::assertTrue(true, 'Regular PHP throws a warning');
    }
    catch (\PHPUnit_Framework_Error_Notice $e) {
      self::assertTrue(true, 'HHVM throws a notice');
    }
  }

  public function testGrapheme_extract()
  {
    self::assertFalse(p::grapheme_extract('abc', 1, -1));

    self::assertSame(grapheme_extract('', 0), p::grapheme_extract('', 0));
    self::assertSame(grapheme_extract('abc', 0), p::grapheme_extract('abc', 0));

    self::assertSame('국어', p::grapheme_extract('한국어', 2, GRAPHEME_EXTR_COUNT, 3, $next));
    self::assertSame(9, $next);

    self::assertSame('국어', grapheme_extract('한국어', 2, GRAPHEME_EXTR_COUNT, 3, $next));
    self::assertSame(9, $next);

    $next = 0;
    self::assertSame('한', p::grapheme_extract('한국어', 1, GRAPHEME_EXTR_COUNT, $next, $next));
    self::assertSame('국', p::grapheme_extract('한국어', 1, GRAPHEME_EXTR_COUNT, $next, $next));
    self::assertSame('어', p::grapheme_extract('한국어', 1, GRAPHEME_EXTR_COUNT, $next, $next));
    self::assertFalse(p::grapheme_extract('한국어', 1, GRAPHEME_EXTR_COUNT, $next, $next));

    self::assertSame(str_repeat('-', 69000), p::grapheme_extract(str_repeat('-', 70000), 69000, GRAPHEME_EXTR_COUNT));

    self::assertSame('d', p::grapheme_extract('déjà', 2, GRAPHEME_EXTR_MAXBYTES));
    self::assertSame('dé', p::grapheme_extract('déjà', 2, GRAPHEME_EXTR_MAXCHARS));

    /** @noinspection PhpUsageOfSilenceOperatorInspection */
    self::assertFalse(@p::grapheme_extract(array(), 0));
    /** @noinspection PhpUsageOfSilenceOperatorInspection */
    self::assertFalse(@grapheme_extract(array(), 0));
  }

  public function testGrapheme_strlen()
  {
    self::assertSame(3, grapheme_strlen('한국어'));
    self::assertSame(3, grapheme_strlen(n::normalize('한국어', n::NFD)));

    self::assertSame(3, p::grapheme_strlen('한국어'));
    self::assertSame(3, p::grapheme_strlen(n::normalize('한국어', n::NFD)));

    self::assertNull(p::grapheme_strlen("\xE9"));
  }

  public function testGrapheme_substr()
  {
    $c = 'déjà';

    self::assertSame('jà', grapheme_substr($c, 2));
    self::assertSame('jà', grapheme_substr($c, -2));
    // The next 3 tests are disabled due to http://bugs.php.net/62759 and 55562
    //self::assertSame( "jà", grapheme_substr($c, -2,  3) );
    //self::assertSame( "", grapheme_substr($c, -1,  0) );
    //self::assertSame( false, grapheme_substr($c,  1, -4) );
    self::assertSame('j', grapheme_substr($c, -2, -1));
    self::assertSame('', grapheme_substr($c, -2, -2));
    self::assertSame(false, grapheme_substr($c, 5, 0));
    self::assertSame(false, grapheme_substr($c, -5, 0));

    self::assertSame('jà', p::grapheme_substr($c, 2));
    self::assertSame('jà', p::grapheme_substr($c, -2));
    self::assertSame('jà', p::grapheme_substr($c, -2, 3));
    self::assertSame('', p::grapheme_substr($c, -1, 0));
    self::assertSame(false, p::grapheme_substr($c, 1, -4));
    self::assertSame('j', p::grapheme_substr($c, -2, -1));
    self::assertSame('', p::grapheme_substr($c, -2, -2));
    self::assertSame(false, p::grapheme_substr($c, 5, 0));
    self::assertSame(false, p::grapheme_substr($c, -5, 0));

    self::assertSame('jà', p::grapheme_substr($c, 2, 2147483647));
    self::assertSame('jà', p::grapheme_substr($c, -2, 2147483647));
    self::assertSame('jà', p::grapheme_substr($c, -2, 3));
    self::assertSame('', p::grapheme_substr($c, -1, 0));
    self::assertSame(false, p::grapheme_substr($c, 1, -4));
    self::assertSame('j', p::grapheme_substr($c, -2, -1));
    self::assertSame('', p::grapheme_substr($c, -2, -2));
    self::assertSame(false, p::grapheme_substr($c, 5, 0));
    self::assertSame(false, p::grapheme_substr($c, -5, 0));
  }

  public function testGrapheme_strpos()
  {
    self::assertSame(false, grapheme_strpos('abc', ''));
    self::assertSame(false, grapheme_strpos('abc', 'd'));
    self::assertSame(false, grapheme_strpos('abc', 'a', 3));
    if (defined('HHVM_VERSION_ID') || PHP_VERSION_ID < 50535 || (50600 <= PHP_VERSION_ID && PHP_VERSION_ID < 50621) || (70000 <= PHP_VERSION_ID && PHP_VERSION_ID < 70006)) {
      $this->assertSame(0, grapheme_strpos('abc', 'a', -1));
    } else {
      $this->assertFalse(grapheme_strpos('abc', 'a', -1));
    }
    self::assertSame(1, grapheme_strpos('한국어', '국'));
    self::assertSame(3, grapheme_stripos('DÉJÀ', 'à'));
    self::assertSame(false, grapheme_strrpos('한국어', ''));
    self::assertSame(1, grapheme_strrpos('한국어', '국'));
    self::assertSame(3, grapheme_strripos('DÉJÀ', 'à'));

    self::assertSame(false, p::grapheme_strpos('abc', ''));
    self::assertSame(false, p::grapheme_strpos('abc', 'd'));
    self::assertSame(false, p::grapheme_strpos('abc', 'a', 3));
    if (defined('HHVM_VERSION_ID') || PHP_VERSION_ID < 50535 || (50600 <= PHP_VERSION_ID && PHP_VERSION_ID < 50621) || (70000 <= PHP_VERSION_ID && PHP_VERSION_ID < 70006)) {
      $this->assertSame(0, p::grapheme_strpos('abc', 'a', -1));
    } else {
      $this->assertFalse(p::grapheme_strpos('abc', 'a', -1));
    }
    self::assertSame(1, p::grapheme_strpos('한국어', '국'));
    self::assertSame(3, p::grapheme_stripos('DÉJÀ', 'à'));
    self::assertSame(false, p::grapheme_strrpos('한국어', ''));
    self::assertSame(1, p::grapheme_strrpos('한국어', '국'));
    self::assertSame(3, p::grapheme_strripos('DÉJÀ', 'à'));
    self::assertSame(16, p::grapheme_stripos('der Straße nach Paris', 'Paris'));
  }

  public function testGrapheme_strstr()
  {
    self::assertSame('국어', grapheme_strstr('한국어', '국'));
    self::assertSame('ÉJÀ', grapheme_stristr('DÉJÀ', 'é'));

    self::assertSame('국어', p::grapheme_strstr('한국어', '국'));
    self::assertSame('ÉJÀ', p::grapheme_stristr('DÉJÀ', 'é'));
    self::assertSame('Paris', p::grapheme_stristr('der Straße nach Paris', 'Paris'));
  }

  public function testGrapheme_bugs()
  {
    if (extension_loaded('intl') && (50418 > PHP_VERSION_ID || 50500 == PHP_VERSION_ID)) {
      // Buggy behavior see https://bugs.php.net/61860
      self::assertSame(17, grapheme_stripos('der Straße nach Paris', 'Paris'));
      self::assertSame('aris', grapheme_stristr('der Straße nach Paris', 'Paris'));
    } else {
      self::assertSame(16, grapheme_stripos('der Straße nach Paris', 'Paris'));
      self::assertSame('Paris', grapheme_stristr('der Straße nach Paris', 'Paris'));
    }
  }
}
