<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\Bootup;
use voku\helper\UTF8;

/**
 * Class BootupTest
 *
 * @internal
 */
final class BootupTest extends \PHPUnit\Framework\TestCase
{
    public function testInitAll()
    {
        Bootup::initAll();

        static::assertSame('UTF-8', \ini_get('default_charset'));
    }

    public function testGetRandomBytes()
    {
        $rand_false = Bootup::get_random_bytes(0);
        static::assertFalse($rand_false);

        $rand_false = Bootup::get_random_bytes('test');
        static::assertFalse($rand_false);

        $rand = Bootup::get_random_bytes(32);

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            static::assertTrue(\strlen($rand) > 1); // :/
        } else {
            static::assertSame(32, \strlen($rand));
        }

        $rand = Bootup::get_random_bytes(0);
        static::assertFalse($rand);

        $bytes = [
            Bootup::get_random_bytes(12),
            Bootup::get_random_bytes(16),
            Bootup::get_random_bytes(16),
        ];

        static::assertSame(
            \strlen(\bin2hex($bytes[0])),
            24
        );

        static::assertNotSame(
            $bytes[1],
            $bytes[2]
        );
    }

    public function testIsPhp()
    {
        $isPHP = Bootup::is_php('0.1');
        static::assertTrue($isPHP);

        $isPHP = Bootup::is_php('999');
        static::assertFalse($isPHP);

        if (\defined('PHP_MAJOR_VERSION') && \PHP_MAJOR_VERSION <= 5) {
            $isPHP = Bootup::is_php('7');
            static::assertFalse($isPHP);
        }

        if (\defined('PHP_MAJOR_VERSION') && \PHP_MAJOR_VERSION >= 5) {
            $isPHP = Bootup::is_php('5.0');
            static::assertTrue($isPHP);
        }

        if (\defined('PHP_MAJOR_VERSION') && \PHP_MAJOR_VERSION >= 7) {
            $isPHP = Bootup::is_php('7');
            static::assertTrue($isPHP);
        }
    }
}
