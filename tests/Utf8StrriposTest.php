<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;
use voku\helper\UTF8 as u;

/**
 * Class Utf8StrriposTest
 *
 * @internal
 */
final class Utf8StrriposTest extends \PHPUnit\Framework\TestCase
{
    public function testUtf8()
    {
        static::assertFalse(\strripos('', ''));
        static::assertFalse(\strripos(' ', ''));
        static::assertFalse(\strripos('', ' '));
        static::assertFalse(\strripos('DJ', ''));
        static::assertFalse(\strripos('', 'J'));

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
