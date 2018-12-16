<?php declare(strict_types=0);

use Symfony\Polyfill\Php72\Php72 as p;
use voku\helper\UTF8;

/**
 * Class ShimXmlTest
 *
 * @internal
 */
final class ShimXmlTest extends \PHPUnit\Framework\TestCase
{
    public function testUtf8Encode()
    {
        $s = \array_map('chr', \range(0, 255));
        $s = \implode('', $s);
        $e = p::utf8_encode($s);

        if (UTF8::getSupportInfo('mbstring_func_overload') !== true) {
            static::assertSame(\utf8_encode($s), p::utf8_encode($s));
            static::assertSame(\utf8_decode($e), p::utf8_decode($e));

            static::assertSame('??', p::utf8_decode('Σ어'));
        }

        // ---

        $s = 444;
        static::assertSame(\utf8_encode($s), p::utf8_encode($s));
        static::assertSame(\utf8_decode($s), p::utf8_decode($s));
    }
}
