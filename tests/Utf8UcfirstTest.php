<?php

declare(strict_types=1);

use voku\helper\UTF8;
use voku\helper\UTF8 as u;

/**
 * Class Utf8UcfirstTest
 *
 * @internal
 */
final class Utf8UcfirstTest extends \PHPUnit\Framework\TestCase
{
    public function testUcfirstSpace()
    {
        $str = ' iñtërnâtiônàlizætiøn';
        $ucfirst = ' iñtërnâtiônàlizætiøn';
        static::assertSame($ucfirst, u::ucfirst($str));
    }

    public function testUcfirstUpper()
    {
        $str = 'Ñtërnâtiônàlizætiøn';
        $ucfirst = 'Ñtërnâtiônàlizætiøn';
        static::assertSame($ucfirst, u::ucfirst($str));
    }

    public function testEmptyString()
    {
        $str = '';
        static::assertSame('', u::ucfirst($str));
    }

    public function testOneChar()
    {
        $str = 'ñ';
        $ucfirst = 'Ñ';
        static::assertSame($ucfirst, u::ucfirst($str));
    }

    public function testLinefeed()
    {
        $str = "ñtërn\nâtiônàlizætiøn";
        $ucfirst = "Ñtërn\nâtiônàlizætiøn";
        static::assertSame($ucfirst, u::ucfirst($str));
    }

    public function testUcfirst()
    {
        $str = 'ñtërnâtiônàlizætiøn';
        $ucfirst = 'Ñtërnâtiônàlizætiøn';
        static::assertSame($ucfirst, u::ucfirst($str));

        // ---

        static::assertSame('', UTF8::ucfirst(''));
        static::assertSame('Ä', UTF8::ucfirst('ä'));
        static::assertSame('Öäü', UTF8::ucfirst('Öäü'));
        static::assertSame('Öäü', UTF8::ucfirst('öäü'));
        static::assertSame('Κόσμε', UTF8::ucfirst('κόσμε'));
        static::assertSame('ABC-ÖÄÜ-中文空白', UTF8::ucfirst('aBC-ÖÄÜ-中文空白'));
        static::assertSame('Iñtërnâtiônàlizætiøn', UTF8::ucfirst('iñtërnâtiônàlizætiøn'));
        static::assertSame('Ñtërnâtiônàlizætiøn', UTF8::ucfirst('ñtërnâtiônàlizætiøn'));
        static::assertSame(' iñtërnâtiônàlizætiøn', UTF8::ucfirst(' iñtërnâtiônàlizætiøn'));
        static::assertSame('Ñtërnâtiônàlizætiøn', UTF8::ucfirst('Ñtërnâtiônàlizætiøn'));
        static::assertSame('ÑtërnâtiônàlizætIøN', UTF8::ucfirst('ñtërnâtiônàlizætIøN'));
        static::assertSame('ÑtërnâtiônàlizætIøN test câse', UTF8::ucfirst('ñtërnâtiônàlizætIøN test câse'));
        static::assertSame('', UTF8::ucfirst(''));
        static::assertSame('Ñ', UTF8::ucfirst('ñ'));
        static::assertSame("Ñtërn\nâtiônàlizætiøn", UTF8::ucfirst("ñtërn\nâtiônàlizætiøn"));
        static::assertSame('Deja', UTF8::ucfirst('deja'));
        static::assertSame('Σσς', UTF8::ucfirst('σσς'));
        static::assertSame('DEJa', UTF8::ucfirst('dEJa'));
        static::assertSame('ΣσΣ', UTF8::ucfirst('σσΣ'));
        static::assertSame('ΣσΣ', UTF8::ucfirst('σσΣ' . "\x01\x02", 'UTF8', true));

        // alias
        static::assertSame('Öäü', UTF8::ucword('öäü'));
    }
}
