<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;

/**
 * @internal
 */
final class Utf8PhpDocExamplesTest extends \PHPUnit\Framework\TestCase
{
    private function fixture(string $filename): string
    {
        return __DIR__ . '/fixtures/' . $filename;
    }

    public function testCleanupPhpDocExampleOutput(): void
    {
        static::assertSame(
            '„Abcdef  …” — 😃 - Düsseldorf',
            UTF8::cleanup("\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃 - DÃ¼sseldorf")
        );
    }

    public function testChrToDecimalPhpDocExampleOutput(): void
    {
        static::assertSame(167, UTF8::chr_to_decimal('§'));
    }

    public function testFilterPhpDocExampleOutput(): void
    {
        static::assertSame(
            ['é', 'à', 'a'],
            UTF8::filter(["\xE9", 'à', 'a'])
        );
    }

    public function testIsBinaryAndIsBinaryFilePhpDocExampleOutputs(): void
    {
        static::assertTrue(UTF8::is_binary('01'));
        static::assertTrue(UTF8::is_binary_file($this->fixture('utf-16-le.txt')));
    }

    public function testIsUtf16AndIsUtf32PhpDocExampleOutputs(): void
    {
        static::assertSame(1, UTF8::is_utf16(\file_get_contents($this->fixture('utf-16-le.txt'))));
        static::assertSame(2, UTF8::is_utf16(\file_get_contents($this->fixture('utf-16-be.txt'))));
        static::assertFalse(UTF8::is_utf16(\file_get_contents($this->fixture('utf-8.txt'))));

        static::assertSame(1, UTF8::is_utf32(\file_get_contents($this->fixture('sample-utf-32-le-bom.txt'))));
        static::assertSame(2, UTF8::is_utf32(\file_get_contents($this->fixture('sample-utf-32-be-bom.txt'))));
        static::assertFalse(UTF8::is_utf32(\file_get_contents($this->fixture('utf-8.txt'))));
    }

    public function testJsonEncodeAndStringPhpDocExampleOutputs(): void
    {
        static::assertSame('[1,"\u00a5","\u00e4"]', UTF8::json_encode([1, '¥', 'ä']));
        static::assertSame('öäü', UTF8::string([246, 228, 252]));
    }

    public function testStrlenStrwidthAndWordwrapPhpDocExampleOutputs(): void
    {
        static::assertSame(20, UTF8::strlen('Iñtërnâtiônàlizætiøn'));
        static::assertSame(20, UTF8::strwidth('Iñtërnâtiônàlizætiøn'));
        static::assertSame(
            'Iñ<br>të<br>rn<br>ât<br>iô<br>nà<br>li<br>zæ<br>ti<br>øn',
            UTF8::wordwrap('Iñtërnâtiônàlizætiøn', 2, '<br>', true)
        );
    }

    public function testUtf8RoundtripPhpDocExampleOutputs(): void
    {
        static::assertSame(
            '  -ABC-????-  ',
            UTF8::to_utf8(UTF8::to_iso8859('  -ABC-中文空白-  '))
        );
        static::assertSame(
            '-ABC-????-',
            UTF8::encode('UTF-8', UTF8::utf8_decode('-ABC-中文空白-'))
        );
        static::assertSame(
            '-ABC-中文空白-',
            UTF8::utf8_decode(UTF8::utf8_encode('-ABC-中文空白-'))
        );
    }
}
