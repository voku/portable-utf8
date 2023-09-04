<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8 as u;

/**
 * Class Utf8StrPadTest
 *
 * @internal
 */
final class Utf8StrPadTest extends \PHPUnit\Framework\TestCase
{
    public function testStrPad()
    {
        $toPad = '<IñtërnëT>'; // 10 characters
        $padding = 'ø__'; // 4 characters

        static::assertSame($toPad . '          ', u::str_pad($toPad, 20));
        static::assertSame('          ' . $toPad, u::str_pad($toPad, 20, ' ', \STR_PAD_LEFT));
        static::assertSame('     ' . $toPad . '     ', u::str_pad($toPad, 20, ' ', \STR_PAD_BOTH));

        static::assertSame($toPad, u::str_pad($toPad, 10));
        static::assertSame('5char', \str_pad('5char', 4)); // str_pos won't truncate input string
        static::assertSame($toPad, u::str_pad($toPad, 8));

        static::assertSame('ø__ø__ø__ø__ø__ø__ø_', u::str_pad('', 20, $padding, \STR_PAD_RIGHT));
        static::assertSame($toPad . 'ø__ø__ø__ø', u::str_pad($toPad, 20, $padding, \STR_PAD_RIGHT));
        static::assertSame('ø__ø__ø__ø' . $toPad, u::str_pad($toPad, 20, $padding, \STR_PAD_LEFT));
        static::assertSame('ø__ø_' . $toPad . 'ø__ø_', u::str_pad($toPad, 20, $padding, \STR_PAD_BOTH));
    }
}
