<?php declare(strict_types=1);

declare(strict_types=0);

use Normalizer as in;
use Symfony\Polyfill\Intl\Normalizer\Normalizer as pn;

/**
 * Class ShimNormalizerTest
 *
 * @internal
 */
final class ShimNormalizerTest extends \PHPUnit\Framework\TestCase
{
    public function testConstants()
    {
        $rpn = new \ReflectionClass('Symfony\Polyfill\Intl\Normalizer\Normalizer');
        $rin = new \ReflectionClass('Normalizer');

        $rpn = $rpn->getConstants();
        $rin = $rin->getConstants();

        \ksort($rpn);
        \ksort($rin);

        static::assertSame($rin, $rpn);
    }

    public function testIsNormalized()
    {
        $c = 'déjà';
        $d = in::normalize($c, pn::NFD);

        static::assertTrue(pn::isNormalized(''));
        static::assertTrue(pn::isNormalized('abc'));
        static::assertTrue(pn::isNormalized($c));
        static::assertTrue(pn::isNormalized($c, pn::NFC));
        static::assertFalse(pn::isNormalized($d, pn::NFD)); // The current implementation defensively says false
        static::assertFalse(pn::isNormalized($c, pn::NFD));
        static::assertFalse(pn::isNormalized($d, pn::NFC));
        static::assertFalse(pn::isNormalized("\xFF"));
    }

    public function testNormalize()
    {
        $c = in::normalize('déjà', pn::NFC) . in::normalize('훈쇼™', pn::NFD);
        static::assertSame($c, pn::normalize($c, pn::NONE));
        static::assertSame($c, in::normalize($c, pn::NONE));

        $c = 'déjà 훈쇼™';
        $d = in::normalize($c, pn::NFD);
        $kc = in::normalize($c, pn::NFKC);
        $kd = in::normalize($c, pn::NFKD);

        static::assertSame('', pn::normalize(''));
        static::assertSame($c, pn::normalize($d));
        static::assertSame($c, pn::normalize($d, pn::NFC));
        static::assertSame($d, pn::normalize($c, pn::NFD));
        static::assertSame($kc, pn::normalize($d, pn::NFKC));
        static::assertSame($kd, pn::normalize($c, pn::NFKD));

        static::assertFalse(pn::normalize($c, -1));
        static::assertFalse(pn::normalize("\xFF"));
    }
}
