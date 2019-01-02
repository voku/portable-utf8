<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;
use voku\helper\UTF8 as u;

/**
 * Class Utf8StristrTest
 *
 * @internal
 */
final class Utf8StristrTest extends \PHPUnit\Framework\TestCase
{
    public function testSubstr()
    {
        $str = 'iñtërnâtiônàlizætiøn';
        $search = 'NÂT';

        static::assertSame('nâtiônàlizætiøn', u::stristr($str, $search));
        static::assertSame('iñtër', u::stristr($str, $search, true));

        // --- alias

        static::assertSame('nâtiônàlizætiøn', u::strichr($str, $search));
        static::assertSame('iñtër', u::strichr($str, $search, true));
    }

    public function testSubstrNoMatch()
    {
        $str = 'iñtërnâtiônàlizætiøn';
        $search = 'foo';
        static::assertFalse(u::stristr($str, $search));

        // ---

        static::assertFalse(u::strstr($str, $search));
    }

    public function testEmptySearch()
    {
        $str = 'iñtërnâtiônàlizætiøn';
        $search = '';
        static::assertFalse(u::stristr($str, $search));

        // ---

        $str = 'iñtërnâtiônàlizætiøn';
        $search = '';
        static::assertFalse(UTF8::stristr($str, $search));

        $str = 'iñtërnâtiônàlizætiøn';
        $search = '';
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        static::assertFalse(@\stristr($str, $search));

        // ---

        $str = 'int';
        $search = null;
        static::assertFalse(UTF8::stristr($str, (string) $search));

        $str = 'int';
        $search = null;
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        static::assertFalse(@\stristr($str, (string) $search));
    }

    public function testEmptyStr()
    {
        $str = '';
        $search = 'NÂT';
        static::assertFalse(u::stristr($str, $search));
    }

    public function testEmptyBoth()
    {
        $str = '';
        $search = '';
        static::assertEmpty(u::stristr($str, $search));
    }

    public function testLinefeedStr()
    {
        $str = "iñt\nërnâtiônàlizætiøn";
        $search = 'NÂT';
        static::assertSame('nâtiônàlizætiøn', u::stristr($str, $search));
    }

    public function testLinefeedBoth()
    {
        $str = "iñtërn\nâtiônàlizætiøn";
        $search = "N\nÂT";
        static::assertSame("n\nâtiônàlizætiøn", u::stristr($str, $search));
    }

    public function testCase()
    {
        $str = "iñtërn\nâtiônàlizætiøn";
        $search = "n\nÂT";
        static::assertSame("n\nâtiônàlizætiøn", u::stristr($str, $search));
    }

    public function testEncoding()
    {
        $str = "iñtërn\nâtiônàlizætiøn";
        $search = "n\nÂT";

        // UTF-8
        static::assertSame("n\nâtiônàlizætiøn", u::stristr($str, $search, false, 'UTF-8', false));

        if (u::getSupportInfo('mbstring') === true) { // only with "mbstring"
            // UTF-7
            static::assertSame("n\n??ti??n??liz??ti??n", u::stristr($str, $search, false, 'UTF-7', false));
        }
    }

    public function testCleanUtf8()
    {
        $str = "iñtërn\nâtiônàl\x00izætiøn";
        $search = "n\nÂT";

        // UTF-8
        static::assertSame("n\nâtiônàlizætiøn", u::stristr($str, $search, false, 'UTF-8', true));
    }
}
