<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;

/**
 * Class Utf8StrriposTest
 *
 * @internal
 */
final class Utf8StrriposTest extends \PHPUnit\Framework\TestCase
{
    public function testUtf8()
    {
        if (!\voku\helper\Bootup::is_php('8.0')) {
            static::assertFalse(\strripos('', ''));
            static::assertFalse(\strripos(' ', ''));
        } else {
            static::assertSame(0, \strripos('', ''));
            static::assertSame(1, \strripos(' ', ''));
        }
        static::assertFalse(\strripos('', ' '));
        if (!\voku\helper\Bootup::is_php('8.0')) {
            static::assertFalse(\strripos('DJ', ''));
        } else {
            static::assertSame(2, \strripos('DJ', ''));
        }
        static::assertFalse(\strripos('', 'J'));

        static::assertSame(\strripos('', ''), UTF8::strripos('', ''));
        static::assertSame(\strripos(' ', ''), UTF8::strripos(' ', ''));
        static::assertSame(\strripos('DJ', ''), UTF8::strripos('DJ', ''));

        static::assertSame(1, UTF8::strripos('aσσb', 'ΣΣ'));
        static::assertSame(1, UTF8::strripos('aςσb', 'ΣΣ'));

        static::assertSame(1, \strripos('DJ', 'J'));
        static::assertSame(1, UTF8::strripos('DJ', 'J'));
        static::assertSame(3, UTF8::strripos('DÉJÀ', 'à'));
        static::assertSame(4, UTF8::strripos('ÀDÉJÀ', 'à'));
        static::assertSame(6, UTF8::strripos('κόσμε-κόσμε', 'Κ'));
        static::assertSame(7, UTF8::strripos('中文空白-ÖÄÜ-中文空白', 'ü'));
        static::assertSame(11, UTF8::strripos('test κόσμε κόσμε test', 'Κ'));
        static::assertSame(13, UTF8::strripos('ABC-ÖÄÜ-中文空白-中文空白', '中'));

        static::assertSame(6, UTF8::strripos('κόσμε-κόσμε' . "\xa0\xa1", 'Κ', -2, 'UTF8', false));
        static::assertSame(6, UTF8::strripos('κόσμε-κόσμε' . "\xa0\xa1", 'Κ', 2, 'UTF8', false));

        static::assertSame(6, UTF8::strripos('κόσμε-κόσμε' . "\xa0\xa1", 'Κ', -2, 'UTF8', true));
        static::assertSame(6, UTF8::strripos('κόσμε-κόσμε' . "\xa0\xa1", 'Κ', 2, 'UTF8', true));
    }
}
