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
        $defaultCharset = \ini_get('default_charset');
        \ini_set('default_charset', 'ISO-8859-1');

        try {
            Bootup::initAll();

            static::assertSame('UTF-8', \ini_get('default_charset'));
        } finally {
            \ini_set('default_charset', (string) $defaultCharset);
        }
    }

    public function testCheckForSupportSetsMbInternalEncodingByDefault()
    {
        if (!\function_exists('mb_internal_encoding')) {
            static::markTestSkipped('mb_internal_encoding() is not available.');
        }

        $mbInternalEncoding = \mb_internal_encoding();
        $testEncoding = $this->getNonUtf8InternalEncoding();

        if ($testEncoding === null) {
            static::markTestSkipped('No non-UTF-8 mb_internal_encoding() value is available.');
        }

        \mb_internal_encoding($testEncoding);

        $refProperty = (new \ReflectionClass(UTF8::class))->getProperty('SUPPORT');
        if (\PHP_VERSION_ID < 80100) {
            $refProperty->setAccessible(true);
        }
        $support = $refProperty->getValue(null);
        $refProperty->setValue(null, []);

        try {
            UTF8::checkForSupport();

            static::assertNotSame([], $refProperty->getValue(null));
            static::assertSame('UTF-8', \mb_internal_encoding());
            static::assertSame('UTF-8', UTF8::getSupportInfo('mbstring_internal_encoding'));
        } finally {
            $refProperty->setValue(null, $support);
            \mb_internal_encoding((string) $mbInternalEncoding);
        }
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testInitAllCanSkipDefaultCharsetChangeViaConstant()
    {
        \define('PORTABLE_UTF8__DISABLE_AUTO_ENCODING', 1);

        $defaultCharset = \ini_get('default_charset');
        \ini_set('default_charset', 'ISO-8859-1');

        try {
            Bootup::initAll();

            static::assertSame('ISO-8859-1', \ini_get('default_charset'));
        } finally {
            \ini_set('default_charset', (string) $defaultCharset);
        }
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCheckForSupportCanPreserveMbInternalEncodingViaConstant()
    {
        \define('PORTABLE_UTF8__DISABLE_AUTO_ENCODING', 1);

        if (!\function_exists('mb_internal_encoding')) {
            static::markTestSkipped('mb_internal_encoding() is not available.');
        }

        $mbInternalEncoding = \mb_internal_encoding();
        $testEncoding = $this->getNonUtf8InternalEncoding();

        if ($testEncoding === null) {
            static::markTestSkipped('No non-UTF-8 mb_internal_encoding() value is available.');
        }

        \mb_internal_encoding($testEncoding);

        $refProperty = (new \ReflectionClass(UTF8::class))->getProperty('SUPPORT');
        if (\PHP_VERSION_ID < 80100) {
            $refProperty->setAccessible(true);
        }
        $support = $refProperty->getValue(null);
        $refProperty->setValue(null, []);

        try {
            UTF8::checkForSupport();

            static::assertNotSame([], $refProperty->getValue(null));
            static::assertSame($testEncoding, \mb_internal_encoding());
            static::assertSame($testEncoding, UTF8::getSupportInfo('mbstring_internal_encoding'));
        } finally {
            $refProperty->setValue(null, $support);
            \mb_internal_encoding((string) $mbInternalEncoding);
        }
    }

    private function getNonUtf8InternalEncoding(): ?string
    {
        $mbInternalEncoding = \mb_internal_encoding();

        foreach (['CP1252', 'Windows-1252', 'ISO-8859-1'] as $encoding) {
            if (\mb_internal_encoding($encoding) === true) {
                $testEncoding = (string) \mb_internal_encoding();
                \mb_internal_encoding((string) $mbInternalEncoding);

                return $testEncoding;
            }
        }

        \mb_internal_encoding((string) $mbInternalEncoding);

        return null;
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
