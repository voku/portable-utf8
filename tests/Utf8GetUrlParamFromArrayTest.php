<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8 as u;

/**
 * @internal
 */
final class Utf8GetUrlParamFromArrayTest extends \PHPUnit\Framework\TestCase
{

    public function testInvalidSixOctetSequence()
    {
        // init
        $array = [];

        // ------

        static::assertSame(
            null,
            u::getUrlParamFromArray('fooobar_noop', $array)
        );

        // ------

        $array['fooobar'] = 'lall0';

        static::assertSame(
            'lall0',
            u::getUrlParamFromArray('fooobar', $array)
        );

        // ------

        $array['fooo'][3432] = 'lall1';

        static::assertSame(
            'lall1',
            u::getUrlParamFromArray('fooo[3432]', $array)
        );

        // ------


        $array['fooo'][3433]['lall'] = 'lall2';

        static::assertSame(
            'lall2',
            u::getUrlParamFromArray('fooo[3433][lall]', $array)
        );

        // ------

        $array['fooo'][3434]['lall'] = ['lall3', 'lall4'];

        static::assertSame(
            ['lall3', 'lall4'],
            u::getUrlParamFromArray('fooo[3434][lall]', $array)
        );
    }
}
