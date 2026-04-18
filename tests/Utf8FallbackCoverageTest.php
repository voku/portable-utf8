<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;

final class Utf8FallbackCoverageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array|null
     */
    private $oldSupportArray;

    protected function tearDown(): void
    {
        $this->reactivateNativeUtf8Support();
    }

    public function testChrToDecimalFallbackHandlesMultipleByteWidths()
    {
        $this->disableNativeUtf8Support();

        static::assertSame(65, UTF8::chr_to_decimal('A'));
        static::assertSame(241, UTF8::chr_to_decimal('ñ'));
        static::assertSame(8364, UTF8::chr_to_decimal('€'));
        static::assertSame(128512, UTF8::chr_to_decimal('😀'));
    }

    public function testExtractTextSupportsIsoEncodingBranches()
    {
        $str = 'This is only a Fork of UTF8, take a look at the new features.';

        static::assertSame('This…', UTF8::extract_text($str, '', 4, '…', 'ISO-8859-1'));
        static::assertSame('…a Fork…', UTF8::extract_text($str, 'Fork', 5, '…', 'ISO-8859-1'));
        static::assertSame('…Fork of UTF8, take a look at the new features.', UTF8::extract_text($str, 'Fork', 0, '…', 'ISO-8859-1'));
        static::assertSame('…Fork of UTF8…', UTF8::extract_text($str, 'UTF8', 15, '…', 'ISO-8859-1'));
        static::assertSame('This is only a…', UTF8::extract_text($str, 'missing', 15, '…', 'ISO-8859-1'));
    }

    public function testFallbackSearchHelpersCoverAsciiAndUtf8Branches()
    {
        $this->disableNativeUtf8Support();

        static::assertSame(17, UTF8::strrpos('Iñtërnâtiônàlizætiøn', 'i'));
        static::assertSame(5, UTF8::strrpos('banana', 'a', 0, 'ASCII'));

        static::assertSame(6, UTF8::strripos('κόσμε-κόσμε', 'Κ'));
        static::assertSame(5, UTF8::strripos('Banana', 'A', 0, 'ASCII'));

        static::assertSame('κόσμε', UTF8::strrchr('κόσμεκόσμε-äöü', 'κόσμε', true));
        static::assertSame('a', UTF8::strrchr('banana', 'a', false, 'ASCII'));

        static::assertSame('nâtiônàlizætiøn', UTF8::stristr('iñtërnâtiônàlizætiøn', 'NÂT'));
        static::assertSame('D', UTF8::stristr('DÉJÀ', 'é', true));

        static::assertSame('中文空白.com', UTF8::strstr('ABC@中文空白.com', '中文空白'));
        static::assertSame('ba', UTF8::strstr('banana', 'na', true, 'ASCII'));

        static::assertSame(8, UTF8::strpos('ABC-ÖÄÜ-中文空白-中文空白', '中'));
        static::assertSame(1, UTF8::strpos('banana', 'a', 1, 'ASCII'));

        static::assertSame(1, UTF8::stripos('aσσb', 'ΣΣ'));
        static::assertSame(1, UTF8::stripos('Banana', 'A', 1, 'ASCII'));
    }

    public function testIsUtf8FallbackHandlesManualParsing()
    {
        $this->disableNativeUtf8Support();

        static::assertTrue(UTF8::is_utf8('A'));
        static::assertTrue(UTF8::is_utf8('ñ'));
        static::assertTrue(UTF8::is_utf8('😀'));

        static::assertFalse(UTF8::is_utf8("\x80"));
        static::assertFalse(UTF8::is_utf8("\xE2\x82"));
        static::assertFalse(UTF8::is_utf8("\xF8\x88\x80\x80\x80"));
        static::assertFalse(UTF8::is_utf8("\xFC\x84\x80\x80\x80\x80"));
    }

    public function testIconvFallbackBranches()
    {
        $this->disableNativeUtf8Support([
            'iconv' => true,
        ]);

        static::assertSame('κόσμε', UTF8::strrchr('κόσμεκόσμε-äöü', 'κόσμε', true));
        static::assertSame('κόσμε-äöü', UTF8::strrchr('κόσμεκόσμε-äöü', 'κόσμε'));
        static::assertSame(8, UTF8::strpos('ABC-ÖÄÜ-中文空白-中文空白', '中'));
        static::assertSame('ñt', UTF8::substr('Iñtërnâtiônàlizætiøn', 1, 2));
        static::assertSame(20, UTF8::strlen('Iñtërnâtiônàlizætiøn'));
    }

    public function testStrlenFallbackHandlesAsciiUtf8AndInvalidSequences()
    {
        $this->disableNativeUtf8Support();

        static::assertSame(6, UTF8::strlen('banana', 'ASCII'));
        static::assertSame(4, UTF8::strlen('déjà'));
        static::assertFalse(UTF8::strlen("\xF8\x88\x80\x80\x80"));
    }

    public function testStrrevAndSubstrFallbackBranches()
    {
        $this->disableNativeUtf8Support();

        static::assertSame('üäö-κ', UTF8::strrev('κ-öäü'));

        static::assertSame('ti', UTF8::substr('Iñtërnâtiônàlizætiøn', -4, -2));
        static::assertSame('sti', UTF8::substr('testing', 2, 3, 'ASCII'));
        static::assertSame('', UTF8::substr('abc', 3));

        $this->disableNativeUtf8Support([
            'mbstring' => true,
            'iconv' => true,
        ]);

        static::assertSame('cba', UTF8::strrev('abc', 'ISO-8859-1'));
    }

    public function testStrSplitCoversArrayLongStringAndVanillaFallback()
    {
        static::assertSame(
            [
                ['中文', '空白'],
                ['te', 'st'],
            ],
            UTF8::str_split(['中文空白', 'test'], 2)
        );

        static::assertCount(128, UTF8::str_split(str_repeat('é', 128)));

        $this->disableNativeUtf8Support();

        static::assertSame(['Añ', '€😀'], UTF8::str_split('Añ€😀', 2));
    }

    public function testStrTitleizeSupportsIsoAndSpecialLanguageBranches()
    {
        static::assertSame('', UTF8::str_titleize(''));
        static::assertSame('Foo Bar', UTF8::str_titleize("foo\x00 bar", null, 'UTF-8', true));
        static::assertSame('Foo bar Baz', UTF8::str_titleize('foo bar baz', ['bar'], 'ISO-8859-1'));

        if (UTF8::getSupportInfo('intl') === true) {
            static::assertSame('İstanbul İzmir', UTF8::str_titleize('istanbul izmir', null, 'UTF-8', false, 'tr', true, false, ' '));

            return;
        }

        $warnings = [];

        \set_error_handler(static function (int $severity, string $message) use (&$warnings): bool {
            if ($severity !== \E_USER_WARNING) {
                return false;
            }

            $warnings[] = $message;

            return true;
        });

        try {
            static::assertSame('Istanbul Izmir', UTF8::str_titleize('istanbul izmir', null, 'UTF-8', false, 'tr', true, false, ' '));
        } finally {
            \restore_error_handler();
        }

        static::assertCount(4, $warnings);
        foreach ($warnings as $warning) {
            static::assertStringContainsString('without intl cannot handle the "lang"', $warning);
            static::assertStringContainsString('tr', $warning);
        }
    }

    public function testSubstrReplaceFallbackHandlesAsciiAndUtf8()
    {
        $this->disableNativeUtf8Support();

        static::assertSame('teXng', UTF8::substr_replace('testing', 'X', 2, 3));
        static::assertSame('dXYZà', UTF8::substr_replace('déjà', 'XYZ', 1, 2));
    }

    public function testSubstrReplaceRejectsArrayLengthForScalarString()
    {
        $this->expectException(\InvalidArgumentException::class);

        UTF8::substr_replace('testing', 'X', 1, [1]);
    }

    public function testSubstrReplaceRejectsArrayOffsetForScalarString()
    {
        $this->expectException(\InvalidArgumentException::class);

        UTF8::substr_replace('testing', 'X', [1], 1);
    }

    private function reactivateNativeUtf8Support()
    {
        if ($this->oldSupportArray === null) {
            return;
        }

        $refProperty = (new \ReflectionObject(new UTF8()))->getProperty('SUPPORT');
        if (\PHP_VERSION_ID < 80100) {
            $refProperty->setAccessible(true);
        }

        $refProperty->setValue(null, $this->oldSupportArray);
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function disableNativeUtf8Support(array $overrides = [])
    {
        $refProperty = (new \ReflectionObject(new UTF8()))->getProperty('SUPPORT');
        if (\PHP_VERSION_ID < 80100) {
            $refProperty->setAccessible(true);
        }

        if ($this->oldSupportArray === null) {
            $this->oldSupportArray = $refProperty->getValue(null);
        }

        if ($this->oldSupportArray['mbstring_func_overload'] === true) {
            return;
        }

        $refProperty->setValue(null, \array_replace([
            'already_checked_via_portable_utf8' => true,
            'mbstring'                          => false,
            'mbstring_func_overload'            => false,
            'mbstring_regex'                    => false,
            'mbstring_internal_encoding'        => 'UTF-8',
            'iconv'                             => false,
            'intl'                              => false,
            'intl__transliterator_list_ids'     => [],
            'intlChar'                          => false,
            'pcre_utf8'                         => false,
            'ctype'                             => true,
            'finfo'                             => true,
            'json'                              => true,
            'symfony_polyfill_used'             => true,
        ], $overrides));
    }
}
