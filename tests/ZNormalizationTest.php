<?php

declare(strict_types=1);

namespace voku\tests;

//use Normalizer as n;
use Symfony\Polyfill\Intl\Normalizer\Normalizer as n;
use voku\helper\UTF8;

/**
 * Class ZNormalizationTest
 *
 * @internal
 */
final class ZNormalizationTest extends \PHPUnit\Framework\TestCase
{
    public $unicodeVersion = 70;

    public function testNormalize()
    {
        $t = \file(__DIR__ . '/fixtures/ZNormalizationTest.' . $this->unicodeVersion . '.txt');
        $c = [];
        $utf8 = new UTF8();

        foreach ($t as $s) {
            $t = \explode('#', $s);
            $t = \explode(';', $t[0]);

            if (\count($t) === 6) {
                /** @noinspection PhpForeachNestedOuterKeyValueVariablesConflictInspection */
                /** @noinspection SuspiciousLoopInspection */
                foreach ($t as $k => $s) {
                    $t = \explode(' ', $s);
                    $t = \array_map('hexdec', $t);
                    $t = \array_map([$utf8, 'chr'], $t);
                    $c[$k] = \implode('', $t);
                }

                static::assertSame($c[1], n::normalize($c[0], n::NFC));
                static::assertSame($c[1], n::normalize($c[1], n::NFC));
                static::assertSame($c[1], n::normalize($c[2], n::NFC));
                static::assertSame($c[3], n::normalize($c[3], n::NFC));
                static::assertSame($c[3], n::normalize($c[4], n::NFC));

                static::assertSame($c[2], n::normalize($c[0], n::NFD));
                static::assertSame($c[2], n::normalize($c[1], n::NFD));
                static::assertSame($c[2], n::normalize($c[2], n::NFD));
                static::assertSame($c[4], n::normalize($c[3], n::NFD));
                static::assertSame($c[4], n::normalize($c[4], n::NFD));

                static::assertSame($c[3], n::normalize($c[0], n::NFKC));
                static::assertSame($c[3], n::normalize($c[1], n::NFKC));
                static::assertSame($c[3], n::normalize($c[2], n::NFKC));
                static::assertSame($c[3], n::normalize($c[3], n::NFKC));
                static::assertSame($c[3], n::normalize($c[4], n::NFKC));

                static::assertSame($c[4], n::normalize($c[0], n::NFKD));
                static::assertSame($c[4], n::normalize($c[1], n::NFKD));
                static::assertSame($c[4], n::normalize($c[2], n::NFKD));
                static::assertSame($c[4], n::normalize($c[3], n::NFKD));
                static::assertSame($c[4], n::normalize($c[4], n::NFKD));
            }
        }
    }
}
