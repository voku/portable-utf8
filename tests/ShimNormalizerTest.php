<?php

declare(strict_types=0);

use Normalizer as in;
use Symfony\Polyfill\Intl\Normalizer\Normalizer as pn;

/**
 * Class ShimNormalizerTest
 */
class ShimNormalizerTest extends \PHPUnit\Framework\TestCase
{
  public function testConstants()
  {
    $rpn = new \ReflectionClass('Symfony\Polyfill\Intl\Normalizer\Normalizer');
    $rin = new \ReflectionClass('Normalizer');

    $rpn = $rpn->getConstants();
    $rin = $rin->getConstants();

    ksort($rpn);
    ksort($rin);

    self::assertSame($rin, $rpn);
  }

  public function testIsNormalized()
  {
    $c = 'déjà';
    $d = in::normalize($c, pn::NFD);

    self::assertTrue(pn::isNormalized(''));
    self::assertTrue(pn::isNormalized('abc'));
    self::assertTrue(pn::isNormalized($c));
    self::assertTrue(pn::isNormalized($c, pn::NFC));
    self::assertFalse(pn::isNormalized($d, pn::NFD)); // The current implementation defensively says false
    self::assertFalse(pn::isNormalized($c, pn::NFD));
    self::assertFalse(pn::isNormalized($d, pn::NFC));
    self::assertFalse(pn::isNormalized("\xFF"));
  }

  public function testNormalize()
  {
    $c = in::normalize('déjà', pn::NFC) . in::normalize('훈쇼™', pn::NFD);
    self::assertSame($c, pn::normalize($c, pn::NONE));
    self::assertSame($c, in::normalize($c, pn::NONE));

    $c = 'déjà 훈쇼™';
    $d = in::normalize($c, pn::NFD);
    $kc = in::normalize($c, pn::NFKC);
    $kd = in::normalize($c, pn::NFKD);

    self::assertSame('', pn::normalize(''));
    self::assertSame($c, pn::normalize($d));
    self::assertSame($c, pn::normalize($d, pn::NFC));
    self::assertSame($d, pn::normalize($c, pn::NFD));
    self::assertSame($kc, pn::normalize($d, pn::NFKC));
    self::assertSame($kd, pn::normalize($c, pn::NFKD));

    self::assertFalse(pn::normalize($c, -1));
    self::assertFalse(pn::normalize("\xFF"));
  }
}
