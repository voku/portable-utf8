<?php

//use Normalizer as n;
use Symfony\Polyfill\Intl\Normalizer\Normalizer as n;

/**
 * Class ZNormalizationTest
 */
class ZNormalizationTest extends \PHPUnit\Framework\TestCase
{
  public $unicodeVersion = 70;

  public function testNormalize()
  {
    $t = \file(__DIR__ . '/fixtures/ZNormalizationTest.' . $this->unicodeVersion . '.txt');
    $c = [];

    foreach ($t as $s) {
      $t = \explode('#', $s);
      $t = \explode(';', $t[0]);

      if (6 === \count($t)) {
        foreach ($t as $k => $s) {
          $t = \explode(' ', $s);
          $t = \array_map('hexdec', $t);
          $t = \array_map('voku\helper\UTF8::chr', $t);
          $c[$k] = \implode('', $t);
        }

        self::assertSame($c[1], n::normalize($c[0], n::NFC));
        self::assertSame($c[1], n::normalize($c[1], n::NFC));
        self::assertSame($c[1], n::normalize($c[2], n::NFC));
        self::assertSame($c[3], n::normalize($c[3], n::NFC));
        self::assertSame($c[3], n::normalize($c[4], n::NFC));

        self::assertSame($c[2], n::normalize($c[0], n::NFD));
        self::assertSame($c[2], n::normalize($c[1], n::NFD));
        self::assertSame($c[2], n::normalize($c[2], n::NFD));
        self::assertSame($c[4], n::normalize($c[3], n::NFD));
        self::assertSame($c[4], n::normalize($c[4], n::NFD));

        self::assertSame($c[3], n::normalize($c[0], n::NFKC));
        self::assertSame($c[3], n::normalize($c[1], n::NFKC));
        self::assertSame($c[3], n::normalize($c[2], n::NFKC));
        self::assertSame($c[3], n::normalize($c[3], n::NFKC));
        self::assertSame($c[3], n::normalize($c[4], n::NFKC));

        self::assertSame($c[4], n::normalize($c[0], n::NFKD));
        self::assertSame($c[4], n::normalize($c[1], n::NFKD));
        self::assertSame($c[4], n::normalize($c[2], n::NFKD));
        self::assertSame($c[4], n::normalize($c[3], n::NFKD));
        self::assertSame($c[4], n::normalize($c[4], n::NFKD));

      }
    }
  }
}
