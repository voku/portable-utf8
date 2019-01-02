<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;
use voku\helper\UTF8 as u;

/**
 * Class Utf8UcwordsTest
 *
 * @internal
 */
final class Utf8UcwordsTest extends \PHPUnit\Framework\TestCase
{
    public function testUcword()
    {
        $str = 'iñtërnâtiônàlizætiøn';
        $ucwords = 'Iñtërnâtiônàlizætiøn';
        static::assertSame($ucwords, u::ucwords($str));
    }

    public function testUcwordsNewline()
    {
        $str = "iñt ërn âti\n ônà liz æti  øn";
        $ucwords = "Iñt Ërn Âti\n Ônà Liz Æti  Øn";
        static::assertSame($ucwords, u::ucwords($str));
    }

    public function testEmptyString()
    {
        $str = '';
        $ucwords = '';
        static::assertSame($ucwords, u::ucwords($str));
    }

    public function testOneChar()
    {
        $str = 'ñ';
        $ucwords = 'Ñ';
        static::assertSame($ucwords, u::ucwords($str));
    }

    public function testLinefeed()
    {
        $str = "iñt ërn âti\n ônà liz æti øn";
        $ucwords = "Iñt Ërn Âti\n Ônà Liz Æti Øn";
        static::assertSame($ucwords, u::ucwords($str));
    }

    public function testUcWords()
    {
        $str = 'iñt ërn âti ônà liz æti øn';
        $ucwords = 'Iñt Ërn Âti Ônà Liz Æti Øn';
        static::assertSame($ucwords, u::ucwords($str));

        // ---

        static::assertSame('Iñt Ërn ÂTi Ônà Liz Æti Øn', UTF8::ucwords('iñt ërn âTi ônà liz æti øn'));
        static::assertSame("Iñt Ërn Âti\n Ônà Liz Æti  Øn", UTF8::ucwords("iñt ërn âti\n ônà liz æti  øn"));
        static::assertSame('中文空白 foo Oo Oöäü#s', UTF8::ucwords('中文空白 foo oo oöäü#s', ['foo'], '#'));
        static::assertSame('中文空白 foo Oo Oöäü#S', UTF8::ucwords('中文空白 foo oo oöäü#s', ['foo'], ''));
        static::assertSame('', UTF8::ucwords(''));
        static::assertSame('Ñ', UTF8::ucwords('ñ'));
        static::assertSame("Iñt ËrN Âti\n Ônà Liz Æti Øn", UTF8::ucwords("iñt ërN âti\n ônà liz æti øn"));
        static::assertSame('ÑtërnâtiônàlizætIøN', UTF8::ucwords('ñtërnâtiônàlizætIøN'));
        static::assertSame('ÑtërnâtiônàlizætIøN Test câse', UTF8::ucwords('ñtërnâtiônàlizætIøN test câse', ['câse']));
        static::assertSame('Deja Σσς DEJa ΣσΣ', UTF8::ucwords('deja σσς dEJa σσΣ'));

        static::assertSame('Deja Σσς DEJa ΣσΣ', UTF8::ucwords('deja σσς dEJa σσΣ', ['de']));
        static::assertSame('Deja Σσς DEJa ΣσΣ', UTF8::ucwords('deja σσς dEJa σσΣ', ['d', 'e']));

        static::assertSame('deja Σσς DEJa ΣσΣ', UTF8::ucwords('deja σσς dEJa σσΣ', ['deja']));
        static::assertSame('deja Σσς DEJa σσΣ', UTF8::ucwords('deja σσς dEJa σσΣ', ['deja', 'σσΣ']));
        static::assertSame('Deja σσς dEJa σσΣ', UTF8::ucwords('deja σσς dEJa σσΣ', ['deja', 'σσΣ'], ' '));
        static::assertSame(
        'deja Σσς DEJa σσΣ',
        UTF8::ucwords(
        'deja σσς dEJa σσΣ' . "\x01\x02",
            [
                'deja',
                'σσΣ',
            ],
            '',
            'UTF-8',
            true
    )
    );
    }
}
