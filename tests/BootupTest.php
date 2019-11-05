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
        Bootup::initAll();

        static::assertSame('UTF-8', \ini_get('default_charset'));
    }

    public function testFilterRequestInputs()
    {
        UTF8::checkForSupport();

        $c = 'à';
        $d = \Normalizer::normalize($c, \Normalizer::NFD);

        $bak = [
            $_GET,
            $_POST,
            $_COOKIE,
            $_REQUEST,
            $_ENV,
            $_FILES,
        ];

        $_GET = [
            'n' => 4,
            'a' => "\xE9",
            'b' => \substr($d, 1),
            'c' => $c,
            'd' => $d,
            'e' => "\n\r\n\r",
        ];

        $_GET['f'] = $_GET;

        $_FILES = [
            'a' => [
                'name'     => '',
                'type'     => '',
                'tmp_name' => '',
                'error'    => 4,
                'size'     => 0,
            ],
            'b' => [
                'name' => [
                    '',
                    '',
                ],
                'type' => [
                    '',
                    '',
                ],
                'tmp_name' => [
                    '',
                    '',
                ],
                'error' => [
                    4,
                    4,
                ],
                'size' => [
                    0,
                    0,
                ],
            ],
        ];

        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        @Bootup::filterRequestInputs();

        $expect = [
            'n' => 4,
            'a' => 'é',
            'b' => '◌' . \substr($d, 1),
            'c' => $c,
            'd' => $c,
            'e' => "\n\n\n",
        ];

        $expect['f'] = $expect;

        static::assertSame($expect, $_GET);

        list($_GET, $_POST, $_COOKIE, $_REQUEST, $_ENV, $_FILES) = $bak;
    }

    public function testFilterRequestUri()
    {
        $uriA = '/' . \urlencode('bàr');
        $uriB = '/' . \urlencode(\utf8_decode('bàr'));
        $uriC = '/' . \utf8_decode('bàr');
        $uriD = '/' . 'bàr';
        $uriE = '/' . \rawurlencode('bàr');
        $uriF = '/' . \rawurlencode(\utf8_decode('bàr'));
        $uriG = '/' . 'bar';
        $uriH = '/' . \urldecode('bàr');
        $uriI = '/' . \urldecode(\utf8_decode('bàr'));
        $uriJ = '/' . \rawurldecode('bàr');
        $uriK = '/' . \rawurldecode(\utf8_decode('bàr'));

        // --

        $u = Bootup::filterRequestUri(null, false);
        static::assertFalse($u);

        $_SERVER['REQUEST_URI'] = $uriA;

        $u = Bootup::filterRequestUri(null, false);
        static::assertSame('/b%C3%A0r', $u);

        // ---

        $u = Bootup::filterRequestUri($uriA, false);
        static::assertSame($uriA, $u);

        $u = Bootup::filterRequestUri($uriB, false);
        static::assertSame($uriA, $u);

        $u = Bootup::filterRequestUri($uriC, false);
        static::assertSame($uriA, $u);

        $u = Bootup::filterRequestUri($uriD, false);
        static::assertSame($uriD, $u);

        $u = Bootup::filterRequestUri($uriE, false);
        static::assertSame($uriE, $u);

        $u = Bootup::filterRequestUri($uriF, false);
        static::assertSame($uriA, $u);

        $u = Bootup::filterRequestUri($uriG, false);
        static::assertSame($uriG, $u);

        $u = Bootup::filterRequestUri($uriH, false);
        static::assertSame($uriH, $u);

        $u = Bootup::filterRequestUri($uriI, false);
        static::assertSame($uriA, $u);

        $u = Bootup::filterRequestUri($uriJ, false);
        static::assertSame($uriJ, $u);

        $u = Bootup::filterRequestUri($uriK, false);
        static::assertSame($uriA, $u);

        // ---

        $_SERVER['REQUEST_URI'] = '//google.com/%c0%af';

        $u = Bootup::filterRequestUri(null, false);
        static::assertSame('/google.com/%C0%AF', $u);

        // ---

        $_SERVER['REQUEST_URI'] = '////google.com/%c0%af';

        $u = Bootup::filterRequestUri(null, false);
        static::assertSame('/google.com/%C0%AF', $u);

        // ---

        $_SERVER['REQUEST_URI'] = '/%c0%af/google.com/%c0%af';

        $u = Bootup::filterRequestUri(null, false);
        static::assertSame('/%C0%AF/google.com/%C0%AF', $u);

        // ---

        $_SERVER['REQUEST_URI'] = '%22http%3a%2f%2f
www.badplace.com%2fnasty.js%22%3e%3c%2fscript%3e&%C0%AF';

        $u = Bootup::filterRequestUri(null, false);
        static::assertSame('%22http%3a%2f%2f
www.badplace.com%2fnasty.js%22%3e%3c%2fscript%3e&%C0%AF', $u);

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

        static::assertTrue(
            \strlen(\bin2hex($bytes[0])) === 24
        );

        static::assertFalse(
            $bytes[1] === $bytes[2]
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
