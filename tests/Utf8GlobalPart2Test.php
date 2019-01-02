<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\Bootup;
use voku\helper\UTF8;

/**
 * Class Utf8GlobalPart2Test
 *
 * @internal
 */
final class Utf8GlobalPart2Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $oldSupportArray;

    protected function setUp()
    {
        \error_reporting(\E_STRICT);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on
     * @param string $methodName Method name to call
     * @param array  $parameters array of parameters to pass into method
     *
     * @return mixed method return
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(\get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function testStrlen()
    {
        // string with UTF-16 (LE) BOM + valid UTF-8 && invalid UTF-8
        $string = "\xFF\xFE" . 'string <strong>with utf-8 chars åèä</strong>' . "\xa0\xa1" . ' - doo-bee doo-bee dooh';

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            static::assertSame(71, \strlen($string));
        } else {
            static::assertSame(74, \strlen($string));
        }

        static::assertSame(74, UTF8::strlen($string, '8bit'));
        static::assertSame(67, UTF8::strlen($string, 'UTF-8', true));

        if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
            static::assertSame(71, UTF8::strlen($string));
            static::assertSame(71, UTF8::strlen($string, 'UTF-8', false));
        }

        $string_test1 = \strip_tags($string);
        $string_test2 = UTF8::strip_tags($string);

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            static::assertSame(54, \strlen($string_test1));
        } else {
            static::assertSame(57, \strlen($string_test1)); // not correct
        }

        // only "mbstring" can handle broken UTF-8 by default
        if (UTF8::mbstring_loaded() === true) {
            static::assertSame(54, UTF8::strlen($string_test2, 'UTF-8', false));
        } else {
            static::assertFalse(UTF8::strlen($string_test2, 'UTF-8', false));
        }

        static::assertSame(50, UTF8::strlen($string_test2, 'UTF-8', true));

        $testArray = [
            '⠊⠀⠉⠁⠝⠀⠑⠁⠞⠀⠛⠇⠁⠎⠎⠀⠁⠝⠙⠀⠊⠞'    => 22,
            "<a href='κόσμε'>κόσμε</a>" => 25,
            '<白>'                       => 3,
            'öäü'                       => 3,
            ' '                         => 1,
            // ''                          => 0,
            // 1                           => 1,
            // -1                           => 2,
        ];

        for ($i = 0; $i <= 2; ++$i) { // keep this loop for simple performance tests

            if ($i === 0) {
                $this->disableNativeUtf8Support();
            } elseif ($i > 0) {
                $this->reactivateNativeUtf8Support();
            }

            foreach ($testArray as $actual => $expected) {
                static::assertSame($expected, UTF8::strlen($actual), 'tested: ' . $actual);
            }
        }

        $testArray = [
            "<a href='test'>tester</a>" => 25,
            '<a>'                       => 3,
            'abc'                       => 3,
            ' '                         => 1,
            ''                          => 0,
            1                           => 1,
            -1                          => 2,
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, \strlen((string) $actual), 'tested: ' . $actual);
        }
    }

    public function testStrnatcasecmp()
    {
        static::assertSame(0, UTF8::strnatcasecmp('Hello world 中文空白!', 'Hello WORLD 中文空白!'));
        static::assertSame(1, UTF8::strnatcasecmp('Hello world 中文空白!', 'Hello WORLD 中文空白'));
        static::assertSame(-1, UTF8::strnatcasecmp('Hello world 中文空白', 'Hello WORLD 中文空白!'));
        static::assertSame(-1, UTF8::strnatcasecmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!'));
        static::assertSame(1, UTF8::strcasecmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!')); // strcasecmp
        static::assertSame(1, UTF8::strnatcasecmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'));
        static::assertSame(-1, UTF8::strcasecmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!')); // strcasecmp
        static::assertSame(0, UTF8::strnatcasecmp('10Hello world 中文空白!', '10Hello world 中文空白!'));
        static::assertSame(0, UTF8::strnatcasecmp('Hello world 中文空白!', 'Hello WORLD 中文空白!'));
    }

    public function testStrnatcmp()
    {
        static::assertSame(1, UTF8::strnatcmp('Hello world 中文空白!', 'Hello WORLD 中文空白!'));
        static::assertSame(1, UTF8::strnatcmp('Hello world 中文空白!', 'Hello WORLD 中文空白'));
        static::assertSame(1, UTF8::strnatcmp('Hello world 中文空白', 'Hello WORLD 中文空白!'));
        static::assertSame(-1, UTF8::strnatcmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!'));
        static::assertSame(1, UTF8::strcmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!')); // strcmp
        static::assertSame(1, UTF8::strnatcmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'));
        static::assertSame(-1, UTF8::strcmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!')); // strcmp
        static::assertSame(0, UTF8::strnatcmp('10Hello world 中文空白!', '10Hello world 中文空白!'));
        static::assertSame(1, UTF8::strnatcmp('Hello world 中文空白!', 'Hello WORLD 中文空白!'));
    }

    public function testStrncasecmp()
    {
        $tests = [
            ''                                                                                    => -3,
            ' '                                                                                   => -1,
            'a'                                                                                   => -1,
            'ü'                                                                                   => 0,
            'Ü'                                                                                   => 0,
            ' foo ' . "\xe2\x80\xa8" . ' öäü' . "\xe2\x80\xa9"                                    => -1,
            "«\xe2\x80\x80foobar\xe2\x80\x80»"                                                    => 1,
            '中文空白 ‟'                                                                              => 1,
            "<ㅡㅡ></ㅡㅡ><div>\xe2\x80\x85</div><input type='email' name='user[email]' /><a>wtf</a>" => -1,
            "–\xe2\x80\x8bDÃ¼sseldorf\xe2\x80\x8b—"                                               => 1,
            "„Abcdef\xe2\x81\x9f”"                                                                => 1,
            " foo\t foo "                                                                         => -1,
        ];

        foreach ($tests as $before => $after) {
            if ($after < 0) {
                static::assertTrue(UTF8::strncasecmp($before, 'ü', 10) < 0, 'tested: ' . $before);
            } elseif ($after > 0) {
                static::assertTrue(UTF8::strncasecmp($before, 'ü', 10) > 0, 'tested: ' . $before);
            } else {
                static::assertTrue(UTF8::strncasecmp($before, 'ü', 10) === 0, 'tested: ' . $before);
            }
        }
    }

    public function testStrncmp()
    {
        $tests = [
            ''                                                                                    => -3,
            ' '                                                                                   => -1,
            'a'                                                                                   => -1,
            'ü'                                                                                   => 0,
            'Ü'                                                                                   => -1,
            ' foo ' . "\xe2\x80\xa8" . ' öäü' . "\xe2\x80\xa9"                                    => -1,
            "«\xe2\x80\x80foobar\xe2\x80\x80»"                                                    => 1,
            '中文空白 ‟'                                                                              => 1,
            "<ㅡㅡ></ㅡㅡ><div>\xe2\x80\x85</div><input type='email' name='user[email]' /><a>wtf</a>" => -1,
            "–\xe2\x80\x8bDÃ¼sseldorf\xe2\x80\x8b—"                                               => 1,
            "„Abcdef\xe2\x81\x9f”"                                                                => 1,
            " foo\t foo "                                                                         => -1,
        ];

        foreach ($tests as $before => $after) {
            if ($after < 0) {
                static::assertTrue(UTF8::strncmp($before, 'ü', 10) < 0, 'tested: ' . $before);
            } elseif ($after > 0) {
                static::assertTrue(UTF8::strncmp($before, 'ü', 10) > 0, 'tested: ' . $before);
            } else {
                static::assertTrue(UTF8::strncmp($before, 'ü', 10) === 0, 'tested: ' . $before);
            }
        }
    }

    public function testStrpbrk()
    {
        // php compatible tests

        $text = 'This is a Simple text.';

        static::assertFalse(\strpbrk($text, ''));
        static::assertSame(\strpbrk($text, ''), UTF8::strpbrk($text, ''));

        static::assertFalse(\strpbrk('', 'mi'));
        static::assertSame(\strpbrk('', 'mi'), UTF8::strpbrk('', 'mi'));

        // this echoes "is is a Simple text." because 'i' is matched first
        static::assertSame('is is a Simple text.', \strpbrk($text, 'mi'));
        static::assertSame(\strpbrk($text, 'mi'), UTF8::strpbrk($text, 'mi'));

        // this echoes "Simple text." because chars are case sensitive
        static::assertSame('Simple text.', \strpbrk($text, 'S'));
        static::assertSame('Simple text.', UTF8::strpbrk($text, 'S'));

        // ---

        // UTF-8
        $text = 'Hello -中文空白-';
        static::assertSame('白-', UTF8::strpbrk($text, '白'));

        // empty input
        static::assertFalse(UTF8::strpbrk('', 'z'));

        // empty char-list
        static::assertFalse(UTF8::strpbrk($text, ''));

        // not matching char-list
        $text = 'Hello -中文空白-';
        static::assertFalse(UTF8::strpbrk($text, 'z'));
    }

    public function testStrpos()
    {
        for ($i = 0; $i <= 2; ++$i) { // keep this loop for simple performance tests

            if ($i === 0) {
                $this->disableNativeUtf8Support();
            } elseif ($i > 0) {
                $this->reactivateNativeUtf8Support();
            }

            // php compatible tests

            static::assertFalse(\strpos('abc', ''));
            static::assertFalse(UTF8::strpos('abc', ''));

            static::assertFalse(\strpos('abc', 'd'));
            static::assertFalse(UTF8::strpos('abc', 'd'));

            static::assertFalse(\strpos('abc', 'a', 3));
            static::assertFalse(UTF8::strpos('abc', 'a', 3));

            static::assertFalse(\strpos('abc', 'a', 1));
            static::assertFalse(UTF8::strpos('abc', 'a', 1));

            static::assertSame(1, \strpos('abc', 'b', 1));
            static::assertSame(1, UTF8::strpos('abc', 'b', 1));

            static::assertFalse(\strpos('abc', 'b', -1));
            static::assertFalse(UTF8::strpos('abc', 'b', -1));

            static::assertSame(1, \strpos('abc', 'b', 0));
            static::assertSame(1, UTF8::strpos('abc', 'b', 0));

            // UTF-8 tests

            if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
                static::assertSame(16, \strpos('der Straße nach Paris', 'Paris'));
            } else {
                static::assertSame(17, \strpos('der Straße nach Paris', 'Paris')); // not correct
            }

            static::assertSame(17, UTF8::strpos('der Straße nach Paris', 'Paris', 0, '8bit')); // not correct
            static::assertSame(16, UTF8::strpos('der Straße nach Paris', 'Paris'));

            if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
                static::assertSame(1, \strpos('한국어', '국'));
            } else {
                static::assertSame(3, \strpos('한국어', '국')); // not correct
            }

            static::assertSame(1, UTF8::strpos('한국어', '국'));

            static::assertSame(0, UTF8::strpos('κόσμε-κόσμε-κόσμε', 'κ'));
            static::assertSame(7, UTF8::strpos('test κόσμε test κόσμε', 'σ'));
            static::assertSame(8, UTF8::strpos('ABC-ÖÄÜ-中文空白-中文空白', '中'));

            // --- invalid UTF-8

            if (UTF8::getSupportInfo('mbstring') === true) { // only with "mbstring"

                static::assertSame(15, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白'));

                if (Bootup::is_php('7.1') === false) {
                    static::assertSame(3, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', -8));
                } else {
                    static::assertSame(20, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', -8));
                }

                static::assertFalse(UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', -4));
                static::assertFalse(UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', -1));
                static::assertSame(15, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', 0));
                static::assertSame(15, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', 4));
                static::assertSame(15, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', 8));
                static::assertSame(14, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', 0, 'UTF-8', true));
                static::assertSame(15, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', 0, 'UTF-8', false));
                static::assertSame(26, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', 0, 'ISO', true));
                static::assertSame(27, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', 0, 'ISO', false));

                // ISO

                if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
                    static::assertSame(16, \strpos('der Straße nach Paris', 'Paris', 0));
                } else {
                    static::assertSame(17, \strpos('der Straße nach Paris', 'Paris', 0)); // not correct
                }

                static::assertSame(17, UTF8::strpos('der Straße nach Paris', 'Paris', 0, 'ISO')); // not correct

                if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
                    static::assertSame(1, \strpos('한국어', '국', 0));
                } else {
                    static::assertSame(3, \strpos('한국어', '국', 0)); // not correct
                }

                static::assertSame(3, UTF8::strpos('한국어', '국', 0, 'ISO')); // not correct
            }
        }
    }

    public function testStrrchr()
    {
        $testArray = [
            'κόσμε'                                                                            => 'κόσμε',
            'Κόσμε'                                                                            => false,
            'öäü-κόσμεκόσμε-äöü'                                                               => 'κόσμε-äöü',
            'öäü-κόσμεκόσμε-äöüöäü-κόσμεκόσμε-äöü'                                             => 'κόσμε-äöü',
            'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε'                     => 'κόσμε',
            'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-Κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε' => 'κόσμε',
            '  '                                                                               => false,
            ''                                                                                 => false,
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::strrchr($actual, 'κόσμε'), 'error by ' . $actual);
        }

        // --- UTF-8

        static::assertSame('κόσμε-äöü', UTF8::strrchr('κόσμεκόσμε-äöü', 'κόσμε', false, 'UTF-8'));
        static::assertFalse(UTF8::strrchr('Aκόσμεκόσμε-äöü', 'aκόσμε', false, 'UTF-8'));

        static::assertSame('κόσμε', UTF8::strrchr('κόσμεκόσμε-äöü', 'κόσμε', true, 'UTF-8', false));
        static::assertFalse(UTF8::strrchr('Aκόσμεκόσμε-äöü', 'aκόσμε', true, 'UTF-8', false));

        static::assertSame('κόσμε', UTF8::strrchr('κόσμεκόσμε-äöü', 'κόσμε', true, 'UTF-8', true));
        static::assertFalse(UTF8::strrchr('Aκόσμεκόσμε-äöü', 'aκόσμε', true, 'UTF-8', true));

        // --- ISO

        if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
            static::assertSame('κόσμε-äöü', UTF8::strrchr('κόσμεκόσμε-äöü', 'κόσμε', false, 'ISO'));
            static::assertFalse(UTF8::strrchr('Aκόσμεκόσμε-äöü', 'aκόσμε', false, 'ISO'));

            static::assertSame('κόσμε', UTF8::strrchr('κόσμεκόσμε-äöü', 'κόσμε', true, 'ISO'));
            static::assertFalse(UTF8::strrchr('Aκόσμεκόσμε-äöü', 'aκόσμε', true, 'ISO'));
        }
    }

    public function testStrrev()
    {
        $testArray = [
            'κ-öäü'  => 'üäö-κ',
            'abc'    => 'cba',
            'abcöäü' => 'üäöcba',
            '-白-'    => '-白-',
            ''       => '',
            ' '      => ' ',
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::strrev($actual), 'error by ' . $actual);
        }
    }

    public function testStrrichr()
    {
        $testArray = [
            'κόσμε'                                                                            => 'κόσμε',
            'Κόσμε'                                                                            => 'Κόσμε',
            'öäü-κόσμεκόσμε-äöü'                                                               => 'κόσμε-äöü',
            'öäü-κόσμεκόσμε-äöüöäü-κόσμεκόσμε-äöü'                                             => 'κόσμε-äöü',
            'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε'                     => 'κόσμε',
            'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-Κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε' => 'κόσμε',
            '  '                                                                               => false,
            ''                                                                                 => false,
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::strrichr($actual, 'κόσμε'), 'error by ' . $actual);
        }

        // --- UTF-8

        static::assertSame('Aκόσμεκόσμε-äöü', UTF8::strrichr('Aκόσμεκόσμε-äöü', 'aκόσμε', false, 'UTF-8'));
        static::assertSame('ü-abc', UTF8::strrichr('äöü-abc', 'ü', false, 'UTF-8'));

        static::assertSame('', UTF8::strrichr('Aκόσμεκόσμε-äöü', 'aκόσμε', true, 'UTF-8', false));
        static::assertSame('äö', UTF8::strrichr('äöü-abc', 'ü', true, 'UTF-8', false));

        static::assertSame('', UTF8::strrichr('Aκόσμεκόσμε-äöü', 'aκόσμε', true, 'UTF-8', true));
        static::assertSame('äö', UTF8::strrichr('äöü-abc', 'ü', true, 'UTF-8', true));

        // --- ISO

        if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
            static::assertSame('Aκόσμεκόσμε-äöü', UTF8::strrichr('Aκόσμεκόσμε-äöü', 'aκόσμε', false, 'ISO'));
            static::assertSame('ü-abc', UTF8::strrichr('äöü-abc', 'ü', false, 'ISO'));

            static::assertSame('', UTF8::strrichr('Aκόσμεκόσμε-äöü', 'aκόσμε', true, 'ISO'));
            static::assertSame('äö', UTF8::strrichr('äöü-abc', 'ü', true, 'ISO'));
        }
    }

    public function testStrrpos()
    {
        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            static::assertSame(1, \strrpos('한국어', '국'));
        } else {
            static::assertSame(3, \strrpos('한국어', '국')); // not correct
        }

        // bug is reported: https://github.com/facebook/hhvm/issues/7318
        if (\defined('HHVM_VERSION') === true) {
            static::assertSame(1, UTF8::strrpos('한국어', '국', 0, '8bit', false));
            static::assertSame(1, UTF8::strrpos('한국어', '국', 0, 'ISO', false));
            static::assertSame(1, UTF8::strrpos('한국어', '국', 0, '', true));
        } else {
            if (UTF8::getSupportInfo('mbstring') === true) { // only with "mbstring"
                static::assertSame(3, UTF8::strrpos('한국어', '국', 0, '8bit', false));
                static::assertSame(3, UTF8::strrpos('한국어', '국', 0, 'ISO', false));
            }

            static::assertSame(1, UTF8::strrpos('한국어', '국', 0, '', true));
        }

        static::assertSame(1, UTF8::strrpos('한국어', '국', 0, 'UTF-8', false));

        // --- invalid UTF-8

        if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
            static::assertSame(11, UTF8::strrpos("Iñtërnâtiôn\xE9àlizætiøn", 'à', 0, 'UTF-8', true));
            static::assertSame(12, UTF8::strrpos("Iñtërnâtiôn\xE9àlizætiøn", 'à', 0, 'UTF-8', false));
        }

        // ---

        static::assertSame(1, UTF8::strrpos('11--', '1-', 0, 'UTF-8', false));
        static::assertSame(2, UTF8::strrpos('-11--', '1-', 0, 'UTF-8', false));
        static::assertFalse(UTF8::strrpos('한국어', '', 0, 'UTF-8', false));
        static::assertSame(1, UTF8::strrpos('한국어', '국', 0, 'UTF8', true));
        static::assertFalse(UTF8::strrpos('한국어', ''));
        static::assertSame(1, UTF8::strrpos('한국어', '국'));
        static::assertSame(6, UTF8::strrpos('κόσμε-κόσμε', 'κ'));
        static::assertSame(13, UTF8::strrpos('test κόσμε κόσμε test', 'σ'));
        static::assertSame(9, UTF8::strrpos('中文空白-ÖÄÜ-中文空白', '中'));
        static::assertSame(13, UTF8::strrpos('ABC-ÖÄÜ-中文空白-中文空白', '中'));
    }

    public function testStrtocasefold()
    {
        static::assertSame(UTF8::strtocasefold('J̌̌◌̱', true), UTF8::strtocasefold('ǰ◌̱', true)); // Original (NFC)
        static::assertSame('ǰ◌̱', UTF8::strtocasefold('ǰ◌̱', true)); // Original (NFC)
        static::assertSame('j◌̌◌', UTF8::strtocasefold('J◌̌◌')); // Uppercased
        static::assertSame('j◌̱◌̌', UTF8::strtocasefold('J◌̱◌̌')); // Uppercased NFC

        // valid utf-8
        static::assertSame('hello world 中文空白', UTF8::strtocasefold('Hello world 中文空白'));

        // invalid utf-8

        if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
            static::assertSame('iñtërnâtiôn?àlizætiøn', UTF8::strtocasefold("Iñtërnâtiôn\xE9àlizætiøn"));
            static::assertSame('iñtërnâtiôn?àlizætiøn', UTF8::strtocasefold("Iñtërnâtiôn\xE9àlizætiøn", true));
        }

        static::assertSame('iñtërnâtiônàlizætiøn', UTF8::strtocasefold("Iñtërnâtiôn\xE9àlizætiøn", true, true));
    }

    public function testStrtolower()
    {
        $tests = [
            1               => '1',
            -1              => '-1',
            'ABC-中文空白'      => 'abc-中文空白',
            'ÖÄÜ'           => 'öäü',
            'öäü'           => 'öäü',
            'κόσμε'         => 'κόσμε',
            'Κόσμε'         => 'κόσμε',
            'ㅋㅋ-Lol'        => 'ㅋㅋ-lol',
            'ㅎㄹ..-Daebak'   => 'ㅎㄹ..-daebak',
            'ㅈㅅ-Sorry'      => 'ㅈㅅ-sorry',
            'ㅡㅡ-WTF'        => 'ㅡㅡ-wtf',
            'ABC-ΣΣ'        => 'abc-σσ', // result for language === "tr" --> "abc-σς"
            'Å/å, Æ/æ, Ø/ø' => 'å/å, æ/æ, ø/ø',
            'ΣΣΣ'           => 'σσσ', // result for language === "tr" --> "σσς"
            'DINÇ'          => 'dinç', // result for language === "tr" --> "dınç"
            'TeSt-ẞ'        => 'test-ß',
        ];

        if (
            Bootup::is_php('7.3')
            &&
            UTF8::mbstring_loaded() === true
        ) {
            $tests += [
                'DÉJÀ Σσς Iıİi' => 'déjà σσς iıi̇i', // result for language === "tr" --> "déjà σσς ııii"
                'DİNÇ'          => 'di̇nç',
            ];
        } else {
            $tests += [
                'DÉJÀ Σσς Iıİi' => 'déjà σσς iıii', // result for language === "tr" --> "déjà σσς ııii"
                'DİNÇ'          => 'dinç',
            ];
        }

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::strtolower($before), 'tested: ' . $before);
        }

        // ---

        // ISO (non utf-8 encoding)
        static::assertNotSame('déjà σσς iıii', UTF8::strtolower('DÉJÀ Σσς Iıİi', 'ISO'));
        static::assertNotSame('öäü', UTF8::strtolower('ÖÄÜ', 'ISO'));

        // ---

        // invalid utf-8
        if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
            static::assertSame('iñtërnâtiôn?àlizætiøn', UTF8::strtolower("Iñtërnâtiôn\xE9àlizætiøn"));
            static::assertSame('iñtërnâtiôn?àlizætiøn', UTF8::strtolower("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', false));
        }

        static::assertSame('iñtërnâtiônàlizætiøn', UTF8::strtolower("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', true));

        // ---

        UTF8::checkForSupport();

        $supportNull = UTF8::getSupportInfo('foo');
        static::assertNull($supportNull);

        $support = UTF8::getSupportInfo();
        static::assertInternalType('array', $support);

        // language === "tr"
        if (
            UTF8::intl_loaded() === true
            &&
            \in_array('tr-Lower', $support['intl__transliterator_list_ids'], true)
        ) {
            $tests = [
                1               => '1',
                -1              => '-1',
                'ABC-中文空白'      => 'abc-中文空白',
                'ÖÄÜ'           => 'öäü',
                'öäü'           => 'öäü',
                'κόσμε'         => 'κόσμε',
                'Κόσμε'         => 'κόσμε',
                'ㅋㅋ-Lol'        => 'ㅋㅋ-lol',
                'ㅎㄹ..-Daebak'   => 'ㅎㄹ..-daebak',
                'ㅈㅅ-Sorry'      => 'ㅈㅅ-sorry',
                'ㅡㅡ-WTF'        => 'ㅡㅡ-wtf',
                'DÉJÀ Σσς Iıİi' => 'déjà σσς ııii',
                'ABC-ΣΣ'        => 'abc-σς',
                'Å/å, Æ/æ, Ø/ø' => 'å/å, æ/æ, ø/ø',
                'ΣΣΣ'           => 'σσς',
                'DİNÇ'          => 'dinç',
                'DINÇ'          => 'dınç',
            ];

            // DEBUG (for travis ci)
            /** @noinspection ForgottenDebugOutputInspection */
            //var_dump(transliterator_list_ids());

            foreach ($tests as $before => $after) {
                static::assertSame($after, UTF8::strtolower($before, 'UTF8', false, 'tr'), 'tested: ' . $before);
            }
        }
    }

    public function testStrtonatfold()
    {
        $utf8 = new UTF8();

        // valid utf-8
        $string = $this->invokeMethod($utf8, 'strtonatfold', ['Hello world 中文空白']);
        static::assertSame('Hello world 中文空白', $string);

        // invalid utf-8
        $string = $this->invokeMethod($utf8, 'strtonatfold', ["Iñtërnâtiôn\xE9àlizætiøn"]);
        static::assertSame('', $string);
    }

    public function testStrtoupper()
    {
        $tests = [
            1               => '1',
            -1              => '-1',
            'abc-中文空白'      => 'ABC-中文空白',
            'öäü'           => 'ÖÄÜ',
            'öäü test öäü'  => 'ÖÄÜ TEST ÖÄÜ',
            'ÖÄÜ'           => 'ÖÄÜ',
            '中文空白'          => '中文空白',
            'Déjà Σσς Iıİi' => 'DÉJÀ ΣΣΣ IIİI', // result for language === "tr" --> "DÉJÀ ΣΣΣ IIİİ"
            'DÉJÀ Σσς Iıİi' => 'DÉJÀ ΣΣΣ IIİI', // result for language === "tr" --> "DÉJÀ ΣΣΣ IIİİ"
            'abc-σς'        => 'ABC-ΣΣ',
            'abc-σσ'        => 'ABC-ΣΣ',
            'Å/å, Æ/æ, Ø/ø' => 'Å/Å, Æ/Æ, Ø/Ø',
            'σσς'           => 'ΣΣΣ',
            'σσσ'           => 'ΣΣΣ',
            'DİNÇ'          => 'DİNÇ',
            'DINÇ'          => 'DINÇ',
            'dinç'          => 'DINÇ', // result for language === "tr" --> "DİNÇ"
            'dınç'          => 'DINÇ',
        ];

        if (
            Bootup::is_php('7.3')
            &&
            UTF8::mbstring_loaded() === true
        ) {
            $tests += [
                'test-ß' => 'TEST-SS',
            ];
        }

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::strtoupper($before), 'tested: ' . $before);
        }

        // ---

        // keep string length ...
        static::assertSame('TEST-ẞ', UTF8::strtoupper('test-ß', 'UTF-8', false, null, true));

        // ---

        // ISO (non utf-8 encoding)
        static::assertNotSame('DÉJÀ ΣΣΣ IIİI', UTF8::strtoupper('Déjà Σσς Iıİi', 'ISO'));
        static::assertSame('ABC TEST', UTF8::strtoupper('abc test', 'ISO'));

        // ---

        // invalid utf-8

        if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
            static::assertSame('IÑTËRNÂTIÔN?ÀLIZÆTIØN', UTF8::strtoupper("Iñtërnâtiôn\xE9àlizætiøn"));
            static::assertSame('IÑTËRNÂTIÔN?ÀLIZÆTIØN', UTF8::strtoupper("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', false));
        }

        static::assertSame('IÑTËRNÂTIÔNÀLIZÆTIØN', UTF8::strtoupper("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', true));

        // ---

        UTF8::checkForSupport();
        $support = UTF8::getSupportInfo();

        // language === "tr"
        if (
            UTF8::intl_loaded() === true
            &&
            \in_array('tr-Upper', $support['intl__transliterator_list_ids'], true)
        ) {
            $tests = [
                1               => '1',
                -1              => '-1',
                'abc-中文空白'      => 'ABC-中文空白',
                'öäü'           => 'ÖÄÜ',
                'öäü test öäü'  => 'ÖÄÜ TEST ÖÄÜ',
                'ÖÄÜ'           => 'ÖÄÜ',
                '中文空白'          => '中文空白',
                'Déjà Σσς Iıİi' => 'DÉJÀ ΣΣΣ IIİİ',
                'DÉJÀ Σσς Iıİi' => 'DÉJÀ ΣΣΣ IIİİ',
                'abc-σς'        => 'ABC-ΣΣ',
                'abc-σσ'        => 'ABC-ΣΣ',
                'Å/å, Æ/æ, Ø/ø' => 'Å/Å, Æ/Æ, Ø/Ø',
                'σσς'           => 'ΣΣΣ',
                'σσσ'           => 'ΣΣΣ',
                'DİNÇ'          => 'DİNÇ',
                'DINÇ'          => 'DINÇ',
                'dinç'          => 'DİNÇ',
                'dınç'          => 'DINÇ',
            ];

            foreach ($tests as $before => $after) {
                static::assertSame($after, UTF8::strtoupper($before, 'UTF8', false, 'tr'), 'tested: ' . $before);
            }
        }
    }

    public function testStrtr()
    {
        // php compatible tests

        $arr = [
            'Hello' => 'Hi',
            'world' => 'earth',
        ];
        static::assertSame('Hi earth', \strtr('Hello world', $arr));
        static::assertSame('Hi earth', UTF8::strtr('Hello world', $arr));

        // UTF-8 tests

        $arr = [
            'Hello' => '○●◎',
            '中文空白'  => 'earth',
        ];
        static::assertSame('○●◎ earth', UTF8::strtr('Hello 中文空白', $arr));

        static::assertSame('○●◎◎o wor◎d', UTF8::strtr('Hello world', 'Hello', '○●◎'));
        static::assertSame(' world', UTF8::strtr('Hello world', 'Hello'));
        static::assertSame('test world', UTF8::strtr('Hello world', ['Hello' => 'test']));
        static::assertSame('Hello world H●◎', UTF8::strtr('Hello world ○●◎', '○', 'Hello'));
        static::assertSame('Hello world ○●◎', UTF8::strtr('Hello world ○●◎', ['○'], ['Hello']));
    }

    public function testStrwidth()
    {
        $testArray = [
            'testtest' => 8,
            'Ã'        => 1,
            ' '        => 1,
            ''         => 0,
            "\n"       => 1,
            'test'     => 4,
            "ひらがな\r"   => 9,
            "○●◎\r"    => 4,
        ];

        foreach ($testArray as $before => $after) {
            static::assertSame($after, UTF8::strwidth($before));
        }

        // test + Invalid Chars

        if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
            static::assertSame(21, UTF8::strwidth("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', false));
        }

        static::assertSame(20, UTF8::strwidth("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', true));

        if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
            static::assertSame(20, UTF8::strlen("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', false));
        }

        static::assertSame(20, UTF8::strlen("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', true));

        // ISO

        if (UTF8::getSupportInfo('mbstring') === true) { // only with "mbstring"
            static::assertSame(28, UTF8::strlen("Iñtërnâtiôn\xE9àlizætiøn", 'ISO', false));
            static::assertSame(27, UTF8::strlen("Iñtërnâtiôn\xE9àlizætiøn", 'ISO', true));
        }
    }

    public function testSubstr()
    {
        static::assertSame('23', \substr((string) 1234, 1, 2));
        static::assertSame('bc', \substr('abcde', 1, 2));
        static::assertSame('de', \substr('abcde', -2, 2));
        static::assertSame('bc', \substr('abcde', 1, 2));
        static::assertSame('bc', \substr('abcde', 1, 2));
        static::assertSame('bcd', \substr('abcde', 1, 3));
        static::assertSame('bc', \substr('abcde', 1, 2));

        static::assertSame('23', UTF8::substr((string) 1234, 1, 2));
        static::assertSame('bc', UTF8::substr('abcde', 1, 2));
        static::assertSame('de', UTF8::substr('abcde', -2, 2));
        static::assertSame('bc', UTF8::substr('abcde', 1, 2));
        static::assertSame('bc', UTF8::substr('abcde', 1, 2, 'UTF8'));
        static::assertSame('bc', UTF8::substr('abcde', 1, 2, 'UTF-8', true));
        static::assertSame('bcd', UTF8::substr('abcde', 1, 3));
        static::assertSame('bc', UTF8::substr('abcde', 1, 2));

        // UTF-8
        static::assertSame('文空', UTF8::substr('中文空白', 1, 2));
        static::assertSame('空白', UTF8::substr('中文空白', -2, 2));
        static::assertSame('空白', UTF8::substr('中文空白', -2));
        static::assertSame('Я можу', UTF8::substr('Я можу їсти скло', 0, 6));

        $this->disableNativeUtf8Support();

        // UTF-8
        static::assertSame('文空', UTF8::substr('中文空白', 1, 2));
        static::assertSame('空白', UTF8::substr('中文空白', -2, 2));
        static::assertSame('空白', UTF8::substr('中文空白', -2));
        static::assertSame('Я можу', UTF8::substr('Я можу їсти скло', 0, 6));

        $this->reactivateNativeUtf8Support();
    }

    public function testSubstrCompare()
    {
        // php compatible tests

        static::assertSame(0, \substr_compare((string) 12345, (string) 23, 1, 2));
        static::assertSame(0, UTF8::substr_compare((string) 12345, (string) 23, 1, 2));

        static::assertSame(0, \substr_compare('abcde', 'bc', 1, 2));
        static::assertSame(0, UTF8::substr_compare('abcde', 'bc', 1, 2));

        static::assertSame(0, \substr_compare('abcde', 'de', -2, 2));
        static::assertSame(0, UTF8::substr_compare('abcde', 'de', -2, 2));

        static::assertSame(0, \substr_compare('abcde', 'bcg', 1, 2));
        static::assertSame(0, UTF8::substr_compare('abcde', 'bcg', 1, 2));

        static::assertSame(0, \substr_compare('abcde', 'BC', 1, 2, true));
        static::assertSame(0, UTF8::substr_compare('abcde', 'BC', 1, 2, true));

        static::assertSame(1, \substr_compare('abcde', 'bc', 1, 3));
        static::assertSame(1, UTF8::substr_compare('abcde', 'bc', 1, 3));

        static::assertSame(-1, \substr_compare('abcde', 'cd', 1, 2));
        static::assertSame(-1, UTF8::substr_compare('abcde', 'cd', 1, 2));

        // UTF-8 tests

        static::assertTrue(UTF8::substr_compare("○●◎\r", '●◎') < 0);
        static::assertTrue(UTF8::substr_compare("○●◎\r", '●◎', -1) < 0);
        static::assertTrue(UTF8::substr_compare("○●◎\r", '●◎', -1, 2) < 0);
        static::assertTrue(UTF8::substr_compare("○●◎\r", '●◎', 0, 2) < 0);

        static::assertSame(1, UTF8::substr_compare("○●◎\r", '◎●', 1, 2));

        static::assertSame(0, UTF8::substr_compare("○●◎\r", '●◎', 1, 2, false));
        static::assertSame(0, UTF8::substr_compare("○●◎\r", '●◎', 1, 2));
        static::assertSame(0, UTF8::substr_compare('中文空白', '文空', 1, 2, true));
        static::assertSame(0, UTF8::substr_compare('中文空白', '文空', 1, 2));
    }

    public function testSubstrCount()
    {
        // php compatible tests

        static::assertFalse(\substr_count('', ''));
        static::assertFalse(UTF8::substr_count('', ''));

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            static::assertFalse(\substr_count('', '', '1')); // offset (int) is encoding (string) :/
        } else {
            static::assertFalse(\substr_count('', '', 1));
        }
        static::assertFalse(UTF8::substr_count('', '', 1));

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            static::assertFalse(\substr_count('', '', ''));  // offset (int) is encoding (string) :/
        } else {
            static::assertFalse(\substr_count('', '', 1, 1));
        }

        static::assertFalse(UTF8::substr_count('', '', 1, 1));

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            static::assertFalse(\substr_count('', 'test', '1')); // offset (int) is encoding (string) + last parameter is not available :/
        } else {
            static::assertFalse(\substr_count('', 'test', 1, 1));
        }

        static::assertFalse(UTF8::substr_count('', 'test', 1, 1));

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            static::assertFalse(\substr_count('test', '', '1')); // offset (int) is encoding (string) + last parameter is not available :/
        } else {
            static::assertFalse(\substr_count('test', '', 1, 1));
        }

        static::assertFalse(UTF8::substr_count('test', '', 1, 1));

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            static::assertFalse(\substr_count('test', 'test', '1')); // offset (int) is encoding (string) + last parameter is not available :/
        } else {
            static::assertSame(0, \substr_count('test', 'test', 1, 1));
        }

        static::assertSame(0, UTF8::substr_count('test', 'test', 1, 1));

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            static::assertFalse(\substr_count((string) 12345, (string) 23, (string) 1)); // offset (int) is encoding (string) + last parameter is not available :/
        } else {
            static::assertSame(1, \substr_count((string) 12345, (string) 23, 1, 2));
        }

        static::assertSame(1, UTF8::substr_count((string) 12345, (string) 23, 1, 2));

        static::assertSame(2, \substr_count('abcdebc', 'bc'));
        static::assertSame(2, UTF8::substr_count('abcdebc', 'bc'));

        if (Bootup::is_php('7.1') === false) {
            if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
                static::assertFalse(\substr_count('abcde', 'de', (string) -2)); // offset (int) is encoding (string) + last parameter is not available :/
            } else {
                static::assertFalse(\substr_count('abcde', 'de', -2, 2));
            }

            static::assertFalse(UTF8::substr_count('abcde', 'de', -2, 2));
        } else {
            if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
                static::assertFalse(\substr_count('abcde', 'de', (string) -2)); // offset (int) is encoding (string) + last parameter is not available :/
            } else {
                static::assertSame(1, \substr_count('abcde', 'de', -2, 2));
            }

            static::assertSame(1, UTF8::substr_count('abcde', 'de', -2, 2));
        }

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            static::assertFalse(\substr_count('abcde', 'bcg', (string) 1)); // offset (int) is encoding (string) + last parameter is not available :/
        } else {
            static::assertSame(0, \substr_count('abcde', 'bcg', 1, 2));
        }

        static::assertSame(0, UTF8::substr_count('abcde', 'bcg', 1, 2));

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            static::assertFalse(\substr_count('abcde', 'BC', (string) 1)); // offset (int) is encoding (string) + last parameter is not available :/
        } else {
            static::assertSame(0, \substr_count('abcde', 'BC', 1, 2));
        }

        static::assertSame(0, UTF8::substr_count('abcde', 'BC', 1, 2));

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            static::assertFalse(\substr_count('abcde', 'bc', (string) 1)); // offset (int) is encoding (string) + last parameter is not available :/
        } else {
            static::assertSame(1, \substr_count('abcde', 'bc', 1, 3));
        }

        static::assertSame(1, UTF8::substr_count('abcde', 'bc', 1, 3));

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            static::assertFalse(\substr_count('abcde', 'cd', (string) 1)); // offset (int) is encoding (string) + last parameter is not available :/
        } else {
            static::assertSame(0, \substr_count('abcde', 'cd', 1, 2));
        }

        static::assertSame(0, UTF8::substr_count('abcde', 'cd', 1, 2));

        // UTF-8 tests

        static::assertFalse(UTF8::substr_count('', '文空'));
        static::assertFalse(UTF8::substr_count('中文空白', ''));
        static::assertFalse(UTF8::substr_count('', ''));

        static::assertSame(0, UTF8::substr_count('中文空白', '文空', 0, 0));

        static::assertSame(0, UTF8::substr_count('中文空白', '文空', 0, 1));
        static::assertSame(1, UTF8::substr_count("○●◎\r", '●◎', 1, 2));
        static::assertSame(1, UTF8::substr_count('中文空白', '文空', 1, 2));
        static::assertSame(1, UTF8::substr_count('中文空白', '文空', 1));
        static::assertSame(2, UTF8::substr_count('Можам да јадам стакло, а не ме штета.', 'д'));
        static::assertSame(2, UTF8::substr_count("○●◎\r◎", '◎'));
        static::assertSame(2, UTF8::substr_count('中文空白 文空 文空', '文空', 0, 7));
        static::assertSame(3, UTF8::substr_count('中文空白 文空 文空', '文空', 1));

        // ISO

        if (UTF8::getSupportInfo('mbstring') === true) { // only with "mbstring"
            static::assertSame(0, UTF8::substr_count('中文空白', '文空', 1, 2, 'ISO'));
            static::assertSame(1, UTF8::substr_count('abcde', 'bc', 1, 2, 'ISO'));
        }
    }

    public function testSubstrILeft()
    {
        $str = 'ΚόσμεMiddleEnd';

        $tests = [
            'Κόσμε' => 'MiddleEnd',
            'κόσμε' => 'MiddleEnd',
            // ''      => 'ΚόσμεMiddleEnd',
            ' '     => 'ΚόσμεMiddleEnd',
            // false   => 'ΚόσμεMiddleEnd',
            'Κ'     => 'όσμεMiddleEnd',
            'End'   => 'ΚόσμεMiddleEnd',
            'end'   => 'ΚόσμεMiddleEnd',
        ];

        foreach ($tests as $test => $result) {
            static::assertSame($result, UTF8::substr_ileft($str, $test), 'tested: ' . $test);
        }

        // ---

        static::assertSame('MiddleEndΚόσμε', UTF8::substr_ileft('ΚόσμεMiddleEndΚόσμε', 'Κόσμε'));

        // ---

        static::assertSame('ΚόσμεMiddleEndΚόσμε', UTF8::substr_ileft('ΚόσμεMiddleEndΚόσμε', ''));

        // ---

        static::assertSame('', UTF8::substr_ileft('', 'Κόσμε'));
    }

    public function testSubstrIRight()
    {
        $str = 'BeginMiddleΚόσμε';

        $tests = [
            'Κόσμε' => 'BeginMiddle',
            'κόσμε' => 'BeginMiddle',
            // ''      => 'BeginMiddleΚόσμε',
            ' '     => 'BeginMiddleΚόσμε',
            // false   => 'BeginMiddleΚόσμε',
            'ε'     => 'BeginMiddleΚόσμ',
            'End'   => 'BeginMiddleΚόσμε',
            'end'   => 'BeginMiddleΚόσμε',
        ];

        foreach ($tests as $test => $result) {
            static::assertSame($result, UTF8::substr_iright($str, $test), 'tested: ' . $test);
        }

        // ---

        static::assertSame('ΚόσμεMiddleEnd', UTF8::substr_iright('ΚόσμεMiddleEndΚόσμε', 'Κόσμε'));

        // ---

        static::assertSame('ΚόσμεMiddleEndΚόσμε', UTF8::substr_iright('ΚόσμεMiddleEndΚόσμε', ''));

        // ---

        static::assertSame('', UTF8::substr_iright('', 'Κόσμε'));
    }

    public function testSubstrLeft()
    {
        $str = 'ΚόσμεMiddleEnd';

        $tests = [
            'Κόσμε' => 'MiddleEnd',
            'κόσμε' => 'ΚόσμεMiddleEnd',
            // ''      => 'ΚόσμεMiddleEnd',
            ' '     => 'ΚόσμεMiddleEnd',
            // false   => 'ΚόσμεMiddleEnd',
            'Κ'     => 'όσμεMiddleEnd',
            'End'   => 'ΚόσμεMiddleEnd',
            'end'   => 'ΚόσμεMiddleEnd',
        ];

        foreach ($tests as $test => $result) {
            static::assertSame($result, UTF8::substr_left($str, $test), 'tested: ' . $test);
        }

        // ---

        static::assertSame('MiddleEndΚόσμε', UTF8::substr_left('ΚόσμεMiddleEndΚόσμε', 'Κόσμε'));

        // ---

        static::assertSame('ΚόσμεMiddleEndΚόσμε', UTF8::substr_left('ΚόσμεMiddleEndΚόσμε', ''));

        // ---

        static::assertSame('', UTF8::substr_left('', 'Κόσμε'));
    }

    public function testSubstrRight()
    {
        $str = 'BeginMiddleΚόσμε';

        $tests = [
            'Κόσμε' => 'BeginMiddle',
            'κόσμε' => 'BeginMiddleΚόσμε',
            // ''      => 'BeginMiddleΚόσμε',
            ' '     => 'BeginMiddleΚόσμε',
            // false   => 'BeginMiddleΚόσμε',
            'ε'     => 'BeginMiddleΚόσμ',
            'End'   => 'BeginMiddleΚόσμε',
            'end'   => 'BeginMiddleΚόσμε',
        ];

        foreach ($tests as $test => $result) {
            static::assertSame($result, UTF8::substr_right($str, $test), 'tested: ' . $test);
        }

        // ---

        static::assertSame('ΚόσμεMiddleEnd', UTF8::substr_right('ΚόσμεMiddleEndΚόσμε', 'Κόσμε'));

        // ---

        static::assertSame('ΚόσμεMiddleEndΚόσμε', UTF8::substr_right('ΚόσμεMiddleEndΚόσμε', ''));

        // ---

        static::assertSame('', UTF8::substr_right('', 'Κόσμε'));
    }

    public function testSwapCase()
    {
        $tests = [
            // 1                                      => '1',
            // -1                                     => '-1',
            ' '                                    => ' ',
            // ''                                     => '',
            'أبز'                                  => 'أبز',
            "\xe2\x80\x99"                         => '’',
            'Ɓtest'                                => 'ɓTEST',
            '  -ABC-中文空白-  '                       => '  -abc-中文空白-  ',
            "      - abc- \xc2\x87"                => '      - ABC- ',
            'abc'                                  => 'ABC',
            'deja vu'                              => 'DEJA VU',
            'déjà vu'                              => 'DÉJÀ VU',
            'déJÀ σσς iıII'                        => 'DÉjà ΣΣΣ IIIi',
            "test\x80-\xBFöäü"                     => 'TEST-ÖÄÜ',
            'Internationalizaetion'                => 'iNTERNATIONALIZAETION',
            "中 - &#20013; - %&? - \xc2\x80"        => '中 - &#20013; - %&? - ',
            'BonJour'                              => 'bONjOUR',
            'BonJour & au revoir'                  => 'bONjOUR & AU REVOIR',
            'Déjà'                                 => 'dÉJÀ',
            'това е тестово заглавие'              => 'ТОВА Е ТЕСТОВО ЗАГЛАВИЕ',
            'це є тестовий заголовок з ґ, є, ї, і' => 'ЦЕ Є ТЕСТОВИЙ ЗАГОЛОВОК З Ґ, Є, Ї, І',
            'это тестовый заголовок'               => 'ЭТО ТЕСТОВЫЙ ЗАГОЛОВОК',
            'führen Aktivitäten Haglöfs'           => 'FÜHREN aKTIVITÄTEN hAGLÖFS',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::swapCase($before, 'UTF-8', true), $before);
        }

        // ---

        static::assertNotSame('это тестовый заголовок', UTF8::swapCase('ЭТО ТЕСТОВЫЙ ЗАГОЛОВОК', 'ISO'));
        static::assertSame('BonJour & au revoir', UTF8::swapCase('bONjOUR & AU REVOIR', 'ISO'));
    }

    public function testToLatin1Utf8()
    {
        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-????-  ',
            '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
            'öäü'            => 'öäü',
            ''               => '',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::to_utf8(UTF8::to_latin1($before)));
        }

        // alias
        static::assertSame($tests, UTF8::to_utf8(UTF8::toIso8859($tests)));
        static::assertSame($tests, UTF8::to_utf8(UTF8::to_latin1($tests)));
        static::assertSame($tests, UTF8::toUTF8(UTF8::toLatin1($tests)));
    }

    public function testToUtf8()
    {
        $examples = [
            // Valid UTF-8
            'κόσμε'                                                                => ['κόσμε' => 'κόσμε'],
            '中'                                                                    => ['中' => '中'],
            // Valid UTF-8 + "win1252"-encoding
            'Dänisch (Å/å, Æ/æ, Ø/ø) + ' . "\xe2\x82\xac"                          => ['Dänisch (Å/å, Æ/æ, Ø/ø) + €' => 'Dänisch (Å/å, Æ/æ, Ø/ø) + €'],
            // Valid UTF-8 + Invalid Chars
            "κόσμε\xa0\xa1-öäü-‽‽‽"                                                => ['κόσμε-öäü-‽‽‽' => 'κόσμε-öäü-‽‽‽'],
            // Valid emoji (non-UTF-8)
            '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ &#x267F; &#x26CE;'             => ['👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ &#x267F; &#x26CE;' => '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ &#x267F; &#x26CE;'],
            // Valid ASCII
            'a'                                                                    => ['a' => 'a'],
            // Valid ASCII + Invalid Chars
            "a\xa0\xa1-öäü"                                                        => ['a-öäü' => 'a-öäü'],
            // Valid 2 Octet Sequence
            "\xc3\xb1"                                                             => ['ñ' => 'ñ'],
            // Invalid 2 Octet Sequence
            "\xc3\x28"                                                             => ['�(' => '('],
            // Invalid Sequence Identifier
            "\xa0\xa1"                                                             => ['��' => ''],
            // Valid 3 Octet Sequence
            "\xe2\x82\xa1"                                                         => ['₡' => '₡'],
            // Invalid 3 Octet Sequence (in 2nd Octet)
            "\xe2\x28\xa1"                                                         => ['�(�' => '('],
            // Invalid 3 Octet Sequence (in 3rd Octet)
            "\xe2\x82\x28"                                                         => ['�(' => '('],
            // Valid 4 Octet Sequence
            "\xf0\x90\x8c\xbc"                                                     => ['𐌼' => '𐌼'],
            // Invalid 4 Octet Sequence (in 2nd Octet)
            "\xf0\x28\x8c\xbc"                                                     => ['�(��' => '('],
            // Invalid 4 Octet Sequence (in 3rd Octet)
            "\xf0\x90\x28\xbc"                                                     => ['�(�' => '('],
            // Invalid 4 Octet Sequence (in 4th Octet)
            "\xf0\x28\x8c\x28"                                                     => ['�(�(' => '(('],
            // Valid 5 Octet Sequence (but not Unicode!)
            "\xf8\xa1\xa1\xa1\xa1"                                                 => ['�' => ''],
            // Valid 6 Octet Sequence (but not Unicode!)
            "\xfc\xa1\xa1\xa1\xa1\xa1"                                             => ['�' => ''],
            // Valid UTF-8 string with null characters
            "\0\0\0\0中\0 -\0\0 &#20013; - &#128077; - %&? - \xc2\x80"              => ['中 - &#20013; - &#128077; - %&? - €' => '中 - &#20013; - &#128077; - %&? - €'],
            // InValid UTF-8 string with null characters + HMTL
            "\0\0\0\0中\0 -\0\0 &#20013; - &shy; - &nbsp; - %&? - \xc2\x80\x80\x80" => ['中 - &#20013; - &shy; - &nbsp; - %&? - €' => '中 - &#20013; - &shy; - &nbsp; - %&? - €'],
        ];

        $counter = 0;
        foreach ($examples as $testString => $testResults) {
            foreach ($testResults as $before => $after) {
                static::assertSame($after, UTF8::to_utf8(UTF8::cleanup($testString)), $counter . ' - ' . $before);
            }
            ++$counter;
        }

        $testString = 'test' . UTF8::html_entity_decode('&nbsp;') . 'test';
        static::assertSame('test' . "\xc2\xa0" . 'test', $testString);
        static::assertSame('test&nbsp;test', UTF8::htmlentities($testString));
        static::assertSame('test' . "\xc2\xa0" . 'test', UTF8::cleanup($testString));
    }

    public function testToUtf8ByLanguage()
    {
        // http://www.columbia.edu/~fdc/utf8/

        $testArray = [
            'Sanskrit: ﻿काचं शक्नोम्यत्तुम् । नोपहिनस्ति माम् ॥',
            'Sanskrit (standard transcription): kācaṃ śaknomyattum; nopahinasti mām.',
            'Classical Greek: ὕαλον ϕαγεῖν δύναμαι· τοῦτο οὔ με βλάπτει.',
            'Greek (monotonic): Μπορώ να φάω σπασμένα γυαλιά χωρίς να πάθω τίποτα.',
            'Greek (polytonic): Μπορῶ νὰ φάω σπασμένα γυαλιὰ χωρὶς νὰ πάθω τίποτα. ',
            'Etruscan: (NEEDED)',
            'Latin: Vitrum edere possum; mihi non nocet.',
            'Old French: Je puis mangier del voirre. Ne me nuit.',
            'French: Je peux manger du verre, ça ne me fait pas mal.',
            'Provençal / Occitan: Pòdi manjar de veire, me nafrariá pas.',
            "Québécois: J'peux manger d'la vitre, ça m'fa pas mal.",
            "Walloon: Dji pou magnî do vêre, çoula m' freut nén må. ",
            'Champenois: (NEEDED) ',
            'Lorrain: (NEEDED)',
            "Picard: Ch'peux mingi du verre, cha m'foé mie n'ma. ",
            'Corsican/Corsu: (NEEDED) ',
            'Jèrriais: (NEEDED)',
            "Kreyòl Ayisyen (Haitï): Mwen kap manje vè, li pa blese'm.",
            'Basque: Kristala jan dezaket, ez dit minik ematen.',
            'Catalan / Català: Puc menjar vidre, que no em fa mal.',
            'Spanish: Puedo comer vidrio, no me hace daño.',
            "Aragonés: Puedo minchar beire, no me'n fa mal . ",
            'Aranés: (NEEDED) ',
            'Mallorquín: (NEEDED)',
            'Galician: Eu podo xantar cristais e non cortarme.',
            'European Portuguese: Posso comer vidro, não me faz mal.',
            'Brazilian Portuguese (8): Posso comer vidro, não me machuca.',
            "Caboverdiano/Kabuverdianu (Cape Verde): M' podê cumê vidru, ca ta maguâ-m'.",
            'Papiamentu: Ami por kome glas anto e no ta hasimi daño.',
            'Italian: Posso mangiare il vetro e non mi fa male.',
            'Milanese: Sôn bôn de magnà el véder, el me fa minga mal.',
            "Roman: Me posso magna' er vetro, e nun me fa male.",
            "Napoletano: M' pozz magna' o'vetr, e nun m' fa mal.",
            "Venetian: Mi posso magnare el vetro, no'l me fa mae.",
            'Zeneise (Genovese): Pòsso mangiâ o veddro e o no me fà mâ.',
            'Sicilian: Puotsu mangiari u vitru, nun mi fa mali. ',
            'Campinadese (Sardinia): (NEEDED) ',
            'Lugudorese (Sardinia): (NEEDED)',
            'Romansch (Grischun): Jau sai mangiar vaider, senza che quai fa donn a mai. ',
            'Romany / Tsigane: (NEEDED)',
            'Romanian: Pot să mănânc sticlă și ea nu mă rănește.',
            'Esperanto: Mi povas manĝi vitron, ĝi ne damaĝas min. ',
            'Pictish: (NEEDED) ',
            'Breton: (NEEDED)',
            'Cornish: Mý a yl dybry gwéder hag éf ny wra ow ankenya.',
            "Welsh: Dw i'n gallu bwyta gwydr, 'dyw e ddim yn gwneud dolur i mi.",
            'Manx Gaelic: Foddym gee glonney agh cha jean eh gortaghey mee.',
            'Old Irish (Ogham): ᚛᚛ᚉᚑᚅᚔᚉᚉᚔᚋ ᚔᚈᚔ ᚍᚂᚐᚅᚑ ᚅᚔᚋᚌᚓᚅᚐ᚜',
            'Old Irish (Latin): Con·iccim ithi nglano. Ním·géna.',
            'Irish: Is féidir liom gloinne a ithe. Ní dhéanann sí dochar ar bith dom.',
            'Ulster Gaelic: Ithim-sa gloine agus ní miste damh é.',
            'Scottish Gaelic: S urrainn dhomh gloinne ithe; cha ghoirtich i mi.',
            'Anglo-Saxon (Runes): ᛁᚳ᛫ᛗᚨᚷ᛫ᚷᛚᚨᛋ᛫ᛖᚩᛏᚪᚾ᛫ᚩᚾᛞ᛫ᚻᛁᛏ᛫ᚾᛖ᛫ᚻᛖᚪᚱᛗᛁᚪᚧ᛫ᛗᛖ᛬',
            'Anglo-Saxon (Latin): Ic mæg glæs eotan ond hit ne hearmiað me.',
            'Middle English: Ich canne glas eten and hit hirtiþ me nouȝt.',
            "English: I can eat glass and it doesn't hurt me.",
            'English (IPA): [aɪ kæn iːt glɑːs ænd ɪt dɐz nɒt hɜːt miː] (Received Pronunciation)',
            'English (Braille): ⠊⠀⠉⠁⠝⠀⠑⠁⠞⠀⠛⠇⠁⠎⠎⠀⠁⠝⠙⠀⠊⠞⠀⠙⠕⠑⠎⠝⠞⠀⠓⠥⠗⠞⠀⠍⠑',
            'Jamaican: Mi kian niam glas han i neba hot mi.',
            'Lalland Scots / Doric: Ah can eat gless, it disnae hurt us. ',
            'Glaswegian: (NEEDED)',
            'Gothic (4): 𐌼𐌰𐌲 𐌲𐌻𐌴𐍃 𐌹̈𐍄𐌰𐌽, 𐌽𐌹 𐌼𐌹𐍃 𐍅𐌿 𐌽𐌳𐌰𐌽 𐌱𐍂𐌹𐌲𐌲𐌹𐌸.',
            'Old Norse (Runes): ᛖᚴ ᚷᛖᛏ ᛖᛏᛁ ᚧ ᚷᛚᛖᚱ ᛘᚾ ᚦᛖᛋᛋ ᚨᚧ ᚡᛖ ᚱᚧᚨ ᛋᚨᚱ',
            'Old Norse (Latin): Ek get etið gler án þess að verða sár.',
            'Norsk / Norwegian (Nynorsk): Eg kan eta glas utan å skada meg.',
            'Norsk / Norwegian (Bokmål): Jeg kan spise glass uten å skade meg.',
            'Føroyskt / Faroese: Eg kann eta glas, skaðaleysur.',
            'Íslenska / Icelandic: Ég get etið gler án þess að meiða mig.',
            'Svenska / Swedish: Jag kan äta glas utan att skada mig.',
            'Dansk / Danish: Jeg kan spise glas, det gør ikke ondt på mig.',
            'Sønderjysk: Æ ka æe glass uhen at det go mæ naue.',
            'Frysk / Frisian: Ik kin glês ite, it docht me net sear.',
            'Nederlands / Dutch: Ik kan glas eten, het doet mĳ geen kwaad.',
            "Kirchröadsj/Bôchesserplat: Iech ken glaas èèse, mer 't deet miech jing pieng.",
            'Afrikaans: Ek kan glas eet, maar dit doen my nie skade nie.',
            'Lëtzebuergescht / Luxemburgish: Ech kan Glas iessen, daat deet mir nët wei.',
            'Deutsch / German: Ich kann Glas essen, ohne mir zu schaden.',
            'Ruhrdeutsch: Ich kann Glas verkasematuckeln, ohne dattet mich wat jucken tut.',
            'Langenfelder Platt: Isch kann Jlaas kimmeln, uuhne datt mich datt weh dääd.',
            "Lausitzer Mundart ('Lusatian'): Ich koann Gloos assn und doas dudd merr ni wii.",
            'Odenwälderisch: Iech konn glaasch voschbachteln ohne dass es mir ebbs daun doun dud.',
            "Sächsisch / Saxon: 'sch kann Glos essn, ohne dass'sch mer wehtue.",
            'Pfälzisch: Isch konn Glass fresse ohne dasses mer ebbes ausmache dud.',
            'Schwäbisch / Swabian: I kå Glas frässa, ond des macht mr nix!',
            'Deutsch (Voralberg): I ka glas eassa, ohne dass mar weh tuat.',
            'Bayrisch / Bavarian: I koh Glos esa, und es duard ma ned wei.',
            'Allemannisch: I kaun Gloos essen, es tuat ma ned weh.',
            'Schwyzerdütsch (Zürich): Ich chan Glaas ässe, das schadt mir nöd.',
            'Schwyzerdütsch (Luzern): Ech cha Glâs ässe, das schadt mer ned. ',
            'Plautdietsch: (NEEDED)',
            'Hungarian: Meg tudom enni az üveget, nem lesz tőle bajom.',
            'Suomi / Finnish: Voin syödä lasia, se ei vahingoita minua.',
            'Sami (Northern): Sáhtán borrat lása, dat ii leat bávččas.',
            'Erzian: Мон ярсан суликадо, ды зыян эйстэнзэ а ули.',
            'Northern Karelian: Mie voin syvvä lasie ta minla ei ole kipie.',
            "Southern Karelian: Minä voin syvvä st'oklua dai minule ei ole kibie. ",
            'Vepsian: (NEEDED) ',
            'Votian: (NEEDED) ',
            'Livonian: (NEEDED)',
            'Estonian: Ma võin klaasi süüa, see ei tee mulle midagi.',
            'Latvian: Es varu ēst stiklu, tas man nekaitē.',
            'Lithuanian: Aš galiu valgyti stiklą ir jis manęs nežeidžia ',
            'Old Prussian: (NEEDED) ',
            'Sorbian (Wendish): (NEEDED)',
            'Czech: Mohu jíst sklo, neublíží mi.',
            'Slovak: Môžem jesť sklo. Nezraní ma.',
            'Polska / Polish: Mogę jeść szkło i mi nie szkodzi.',
            'Slovenian: Lahko jem steklo, ne da bi mi škodovalo.',
            'Croatian: Ja mogu jesti staklo i ne boli me.',
            'Serbian (Latin): Ja mogu da jedem staklo.',
            'Serbian (Cyrillic): Ја могу да једем стакло.',
            'Macedonian: Можам да јадам стакло, а не ме штета.',
            'Russian: Я могу есть стекло, оно мне не вредит.',
            'Belarusian (Cyrillic): Я магу есці шкло, яно мне не шкодзіць.',
            'Belarusian (Lacinka): Ja mahu jeści škło, jano mne ne škodzić.',
            'Ukrainian: Я можу їсти скло, і воно мені не зашкодить.',
            'Bulgarian: Мога да ям стъкло, то не ми вреди.',
            'Georgian: მინას ვჭამ და არა მტკივა.',
            'Armenian: Կրնամ ապակի ուտել և ինծի անհանգիստ չըներ։',
            'Albanian: Unë mund të ha qelq dhe nuk më gjen gjë.',
            'Turkish: Cam yiyebilirim, bana zararı dokunmaz.',
            'Turkish (Ottoman): جام ييه بلورم بڭا ضررى طوقونمز',
            'Bangla / Bengali: আমি কাঁচ খেতে পারি, তাতে আমার কোনো ক্ষতি হয় না।',
            'Marathi: मी काच खाऊ शकतो, मला ते दुखत नाही.',
            'Kannada: ನನಗೆ ಹಾನಿ ಆಗದೆ, ನಾನು ಗಜನ್ನು ತಿನಬಹುದು',
            'Hindi: मैं काँच खा सकता हूँ और मुझे उससे कोई चोट नहीं पहुंचती.',
            'Tamil: நான் கண்ணாடி சாப்பிடுவேன், அதனால் எனக்கு ஒரு கேடும் வராது.',
            'Telugu: నేను గాజు తినగలను మరియు అలా చేసినా నాకు ఏమి ఇబ్బంది లేదు',
            'Sinhalese: මට වීදුරු කෑමට හැකියි. එයින් මට කිසි හානියක් සිදු නොවේ.',
            'Urdu(3): میں کانچ کھا سکتا ہوں اور مجھے تکلیف نہیں ہوتی ۔',
            'Pashto(3): زه شيشه خوړلې شم، هغه ما نه خوږوي',
            'Farsi / Persian(3): .من می توانم بدونِ احساس درد شيشه بخورم',
            'Arabic(3): أنا قادر على أكل الزجاج و هذا لا يؤلمني. ',
            'Aramaic: (NEEDED)',
            "Maltese: Nista' niekol il-ħġieġ u ma jagħmilli xejn.",
            'Hebrew(3): אני יכול לאכול זכוכית וזה לא מזיק לי.',
            'Yiddish(3): איך קען עסן גלאָז און עס טוט מיר נישט װײ. ',
            'Judeo-Arabic: (NEEDED) ',
            'Ladino: (NEEDED) ',
            'Gǝʼǝz: (NEEDED) ',
            'Amharic: (NEEDED)',
            'Twi: Metumi awe tumpan, ɜnyɜ me hwee.',
            'Hausa (Latin): Inā iya taunar gilāshi kuma in gamā lāfiyā.',
            'Hausa (Ajami) (2): إِنا إِىَ تَونَر غِلَاشِ كُمَ إِن غَمَا لَافِىَا',
            'Yoruba(4): Mo lè je̩ dígí, kò ní pa mí lára.',
            'Lingala: Nakokí kolíya biténi bya milungi, ekosála ngáí mabé tɛ́.',
            '(Ki)Swahili: Naweza kula bilauri na sikunyui.',
            'Malay: Saya boleh makan kaca dan ia tidak mencederakan saya.',
            'Tagalog: Kaya kong kumain nang bubog at hindi ako masaktan.',
            "Chamorro: Siña yo' chumocho krestat, ti ha na'lalamen yo'.",
            'Fijian: Au rawa ni kana iloilo, ia au sega ni vakacacani kina.',
            'Javanese: Aku isa mangan beling tanpa lara.',
            'Burmese: က္ယ္ဝန္‌တော္‌၊က္ယ္ဝန္‌မ မ္ယက္‌စားနုိင္‌သည္‌။ ၎က္ရောင္‌့ ထိခုိက္‌မ္ဟု မရ္ဟိပာ။ (9)',
            'Vietnamese (quốc ngữ): Tôi có thể ăn thủy tinh mà không hại gì.',
            'Vietnamese (nôm) (4): 些 𣎏 世 咹 水 晶 𦓡 空 𣎏 害 咦',
            'Khmer: ខ្ញុំអាចញុំកញ្ចក់បាន ដោយគ្មានបញ្ហារ',
            'Lao: ຂອ້ຍກິນແກ້ວໄດ້ໂດຍທີ່ມັນບໍ່ໄດ້ເຮັດໃຫ້ຂອ້ຍເຈັບ.',
            'Thai: ฉันกินกระจกได้ แต่มันไม่ทำให้ฉันเจ็บ',
            'Mongolian (Cyrillic): Би шил идэй чадна, надад хортой биш',
            'Mongolian (Classic) (5): ᠪᠢ ᠰᠢᠯᠢ ᠢᠳᠡᠶᠦ ᠴᠢᠳᠠᠨᠠ ᠂ ᠨᠠᠳᠤᠷ ᠬᠣᠤᠷᠠᠳᠠᠢ ᠪᠢᠰᠢ ',
            'Dzongkha: (NEEDED)',
            'Nepali: ﻿म काँच खान सक्छू र मलाई केहि नी हुन्‍न् ।',
            'Tibetan: ཤེལ་སྒོ་ཟ་ནས་ང་ན་གི་མ་རེད།',
            'Chinese: 我能吞下玻璃而不伤身体。',
            'Chinese (Traditional): 我能吞下玻璃而不傷身體。',
            'Taiwanese(6): Góa ē-tàng chia̍h po-lê, mā bē tio̍h-siong.',
            'Japanese: 私はガラスを食べられます。それは私を傷つけません。',
            'Korean: 나는 유리를 먹을 수 있어요. 그래도 아프지 않아요',
            'Bislama: Mi save kakae glas, hemi no save katem mi.',
            'Hawaiian: Hiki iaʻu ke ʻai i ke aniani; ʻaʻole nō lā au e ʻeha.',
            'Marquesan: E koʻana e kai i te karahi, mea ʻā, ʻaʻe hauhau.',
            'Inuktitut (10): ᐊᓕᒍᖅ ᓂᕆᔭᕌᖓᒃᑯ ᓱᕋᙱᑦᑐᓐᓇᖅᑐᖓ',
            'Chinook Jargon: Naika məkmək kakshət labutay, pi weyk ukuk munk-sik nay.',
            'Navajo: Tsésǫʼ yishą́ągo bííníshghah dóó doo shił neezgai da. ',
            'Cherokee (and Cree, Chickasaw, Cree, Micmac, Ojibwa, Lakota, Náhuatl, Quechua, Aymara, and other American languages): (NEEDED) ',
            'Garifuna: (NEEDED) ',
            'Gullah: (NEEDED)',
            "Lojban: mi kakne le nu citka le blaci .iku'i le se go'i na xrani mi",
            'Nórdicg: Ljœr ye caudran créneþ ý jor cẃran.',
        ];

        // http://www.w3.org/2001/06/utf-8-test/UTF-8-demo.html

        $testArray[] = '
      ⡌⠁⠧⠑ ⠼⠁⠒  ⡍⠜⠇⠑⠹⠰⠎ ⡣⠕⠌

      ⡍⠜⠇⠑⠹ ⠺⠁⠎ ⠙⠑⠁⠙⠒ ⠞⠕ ⠃⠑⠛⠔ ⠺⠊⠹⠲ ⡹⠻⠑ ⠊⠎ ⠝⠕ ⠙⠳⠃⠞
      ⠱⠁⠞⠑⠧⠻ ⠁⠃⠳⠞ ⠹⠁⠞⠲ ⡹⠑ ⠗⠑⠛⠊⠌⠻ ⠕⠋ ⠙⠊⠎ ⠃⠥⠗⠊⠁⠇ ⠺⠁⠎
      ⠎⠊⠛⠝⠫ ⠃⠹ ⠹⠑ ⠊⠇⠻⠛⠹⠍⠁⠝⠂ ⠹⠑ ⠊⠇⠻⠅⠂ ⠹⠑ ⠥⠝⠙⠻⠞⠁⠅⠻⠂
      ⠁⠝⠙ ⠹⠑ ⠡⠊⠑⠋ ⠍⠳⠗⠝⠻⠲ ⡎⠊⠗⠕⠕⠛⠑ ⠎⠊⠛⠝⠫ ⠊⠞⠲ ⡁⠝⠙
      ⡎⠊⠗⠕⠕⠛⠑⠰⠎ ⠝⠁⠍⠑ ⠺⠁⠎ ⠛⠕⠕⠙ ⠥⠏⠕⠝ ⠰⡡⠁⠝⠛⠑⠂ ⠋⠕⠗ ⠁⠝⠹⠹⠔⠛ ⠙⠑
      ⠡⠕⠎⠑ ⠞⠕ ⠏⠥⠞ ⠙⠊⠎ ⠙⠁⠝⠙ ⠞⠕⠲

      ⡕⠇⠙ ⡍⠜⠇⠑⠹ ⠺⠁⠎ ⠁⠎ ⠙⠑⠁⠙ ⠁⠎ ⠁ ⠙⠕⠕⠗⠤⠝⠁⠊⠇⠲

      ⡍⠔⠙⠖ ⡊ ⠙⠕⠝⠰⠞ ⠍⠑⠁⠝ ⠞⠕ ⠎⠁⠹ ⠹⠁⠞ ⡊ ⠅⠝⠪⠂ ⠕⠋ ⠍⠹
      ⠪⠝ ⠅⠝⠪⠇⠫⠛⠑⠂ ⠱⠁⠞ ⠹⠻⠑ ⠊⠎ ⠏⠜⠞⠊⠊⠥⠇⠜⠇⠹ ⠙⠑⠁⠙ ⠁⠃⠳⠞
      ⠁ ⠙⠕⠕⠗⠤⠝⠁⠊⠇⠲ ⡊ ⠍⠊⠣⠞ ⠙⠁⠧⠑ ⠃⠑⠲ ⠔⠊⠇⠔⠫⠂ ⠍⠹⠎⠑⠇⠋⠂ ⠞⠕
      ⠗⠑⠛⠜⠙ ⠁ ⠊⠕⠋⠋⠔⠤⠝⠁⠊⠇ ⠁⠎ ⠹⠑ ⠙⠑⠁⠙⠑⠌ ⠏⠊⠑⠊⠑ ⠕⠋ ⠊⠗⠕⠝⠍⠕⠝⠛⠻⠹
      ⠔ ⠹⠑ ⠞⠗⠁⠙⠑⠲ ⡃⠥⠞ ⠹⠑ ⠺⠊⠎⠙⠕⠍ ⠕⠋ ⠳⠗ ⠁⠝⠊⠑⠌⠕⠗⠎
      ⠊⠎ ⠔ ⠹⠑ ⠎⠊⠍⠊⠇⠑⠆ ⠁⠝⠙ ⠍⠹ ⠥⠝⠙⠁⠇⠇⠪⠫ ⠙⠁⠝⠙⠎
      ⠩⠁⠇⠇ ⠝⠕⠞ ⠙⠊⠌⠥⠗⠃ ⠊⠞⠂ ⠕⠗ ⠹⠑ ⡊⠳⠝⠞⠗⠹⠰⠎ ⠙⠕⠝⠑ ⠋⠕⠗⠲ ⡹⠳
      ⠺⠊⠇⠇ ⠹⠻⠑⠋⠕⠗⠑ ⠏⠻⠍⠊⠞ ⠍⠑ ⠞⠕ ⠗⠑⠏⠑⠁⠞⠂ ⠑⠍⠏⠙⠁⠞⠊⠊⠁⠇⠇⠹⠂ ⠹⠁⠞
      ⡍⠜⠇⠑⠹ ⠺⠁⠎ ⠁⠎ ⠙⠑⠁⠙ ⠁⠎ ⠁ ⠙⠕⠕⠗⠤⠝⠁⠊⠇⠲
    ';

        $testArray[] = '
    Box drawing alignment tests:                                          █
                                                                      ▉
    ╔══╦══╗  ┌──┬──┐  ╭──┬──╮  ╭──┬──╮  ┏━━┳━━┓  ┎┒┏┑   ╷  ╻ ┏┯┓ ┌┰┐    ▊ ╱╲╱╲╳╳╳
    ║┌─╨─┐║  │╔═╧═╗│  │╒═╪═╕│  │╓─╁─╖│  ┃┌─╂─┐┃  ┗╃╄┙  ╶┼╴╺╋╸┠┼┨ ┝╋┥    ▋ ╲╱╲╱╳╳╳
    ║│╲ ╱│║  │║   ║│  ││ │ ││  │║ ┃ ║│  ┃│ ╿ │┃  ┍╅╆┓   ╵  ╹ ┗┷┛ └┸┘    ▌ ╱╲╱╲╳╳╳
    ╠╡ ╳ ╞╣  ├╢   ╟┤  ├┼─┼─┼┤  ├╫─╂─╫┤  ┣┿╾┼╼┿┫  ┕┛┖┚     ┌┄┄┐ ╎ ┏┅┅┓ ┋ ▍ ╲╱╲╱╳╳╳
    ║│╱ ╲│║  │║   ║│  ││ │ ││  │║ ┃ ║│  ┃│ ╽ │┃  ░░▒▒▓▓██ ┊  ┆ ╎ ╏  ┇ ┋ ▎
    ║└─╥─┘║  │╚═╤═╝│  │╘═╪═╛│  │╙─╀─╜│  ┃└─╂─┘┃  ░░▒▒▓▓██ ┊  ┆ ╎ ╏  ┇ ┋ ▏
    ╚══╩══╝  └──┴──┘  ╰──┴──╯  ╰──┴──╯  ┗━━┻━━┛           └╌╌┘ ╎ ┗╍╍┛ ┋  ▁▂▃▄▅▆▇█

    ';

        $testArray[] = 'Ã ñ àáâãäåæ ç èéêë ìíîï';

        $result = [];
        $i = 0;
        foreach ($testArray as $test) {
            $result[$i] = UTF8::to_utf8($test);

            static::assertSame($test, $result[$i]);

            ++$i;
        }

        // test with array
        static::assertSame($result, UTF8::to_utf8($testArray));

        foreach ($testArray as $test) {
            static::assertSame($test, UTF8::to_utf8(UTF8::to_utf8($test)));
        }
    }

    public function testToUtf8V2()
    {
        $testArray = [
            'Düsseldorf'                                                                                => 'Düsseldorf',
            'Ã'                                                                                         => 'Ã',
            'foobar  || 😃'                                                                             => 'foobar  || 😃',
            ' '                                                                                         => ' ',
            ''                                                                                          => '',
            "\n"                                                                                        => "\n",
            'test'                                                                                      => 'test',
            'Here&#39;s some quoted text.'                                                              => 'Here&#39;s some quoted text.',
            '&#39;'                                                                                     => '&#39;',
            "\u0063\u0061\u0074"                                                                        => 'cat',
            "\u0039&#39;\u0039"                                                                         => '9&#39;9',
            '&#35;&#8419;'                                                                              => '&#35;&#8419;',
            "\xcf\x80"                                                                                  => 'π',
            'ðñòó¡¡à±áâãäåæçèéêëì¡í¡îï¡¡¢£¤¥¦§¨©ª«¬­®¯ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞß°±²³´µ¶•¸¹º»¼½¾¿' => 'ðñòó¡¡à±áâãäåæçèéêëì¡í¡îï¡¡¢£¤¥¦§¨©ª«¬­®¯ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞß°±²³´µ¶•¸¹º»¼½¾¿',
            '%ABREPRESENT%C9%BB. «REPRESENTÉ»'                                                          => '%ABREPRESENT%C9%BB. «REPRESENTÉ»',
            'éæ'                                                                                        => 'éæ',
            "\x61\xc3\x8c\xc0"                                                                          => 'aÌÀ',
        ];

        foreach ($testArray as $before => $after) {
            static::assertSame($after, UTF8::to_utf8($before));
        }

        // ---

        $testArray = [
            'Düsseldorf'                                                                                => 'Düsseldorf',
            'Ã'                                                                                         => 'Ã',
            'foobar  || 😃'                                                                             => 'foobar  || 😃',
            ' '                                                                                         => ' ',
            ''                                                                                          => '',
            "\n"                                                                                        => "\n",
            'test'                                                                                      => 'test',
            'Here&#39;s some quoted text.'                                                              => 'Here\'s some quoted text.',
            '&#39;'                                                                                     => '\'',
            "\u0063\u0061\u0074"                                                                        => 'cat',
            "\u0039&#39;\u0039"                                                                         => '9\'9',
            '&#35;&#8419;'                                                                              => '#⃣',
            "\xcf\x80"                                                                                  => 'π',
            'ðñòó¡¡à±áâãäåæçèéêëì¡í¡îï¡¡¢£¤¥¦§¨©ª«¬­®¯ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞß°±²³´µ¶•¸¹º»¼½¾¿' => 'ðñòó¡¡à±áâãäåæçèéêëì¡í¡îï¡¡¢£¤¥¦§¨©ª«¬­®¯ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞß°±²³´µ¶•¸¹º»¼½¾¿',
            '%ABREPRESENT%C9%BB. «REPRESENTÉ»'                                                          => '%ABREPRESENT%C9%BB. «REPRESENTÉ»',
        ];

        foreach ($testArray as $before => $after) {
            static::assertSame($after, UTF8::to_utf8($before, true));
        }

        // ---

        $invalidTest = [
            // Min/max overlong
            "\xC0\x80a"                 => 'Overlong representation of U+0000 | 1',
            "\xE0\x80\x80a"             => 'Overlong representation of U+0000 | 2',
            "\xF0\x80\x80\x80a"         => 'Overlong representation of U+0000 | 3',
            "\xF8\x80\x80\x80\x80a"     => 'Overlong representation of U+0000 | 4',
            "\xFC\x80\x80\x80\x80\x80a" => 'Overlong representation of U+0000 | 5',
            "\xC1\xBFa"                 => 'Overlong representation of U+007F | 6',
            "\xE0\x9F\xBFa"             => 'Overlong representation of U+07FF | 7',
            "\xF0\x8F\xBF\xBFa"         => 'Overlong representation of U+FFFF | 8',
            "a\xDF"                     => 'Incomplete two byte sequence (missing final byte) | 9',
            "a\xEF\xBF"                 => 'Incomplete three byte sequence (missing final byte) | 10',
            "a\xF4\xBF\xBF"             => 'Incomplete four byte sequence (missing final byte) | 11',
            // Min/max continuation bytes
            "a\x80"                     => 'Lone 80 continuation byte | 12',
            "a\xBF"                     => 'Lone BF continuation byte | 13',
            // Invalid bytes (these can never occur)
            "a\xFE"                     => 'Invalid FE byte | 14',
            "a\xFF"                     => 'Invalid FF byte | 15',
        ];

        foreach ($invalidTest as $test => $note) {
            static::assertSame('a', UTF8::cleanup($test), $note);
        }
    }

    public function testToUtf8V3()
    {
        $utf8File = \file_get_contents(__DIR__ . '/fixtures/utf-8.txt');
        $latinFile = \file_get_contents(__DIR__ . '/fixtures/latin.txt');

        $utf8File = \explode("\n", \str_replace(["\r\n", "\r", '<br>', '<br />'], "\n", $utf8File));
        $latinFile = \explode("\n", \str_replace(["\r\n", "\r", '<br>', '<br />'], "\n", $latinFile));

        $testArray = \array_combine($latinFile, $utf8File);

        static::assertTrue(\count($testArray) > 0);
        foreach ($testArray as $before => $after) {
            static::assertSame($after, UTF8::to_utf8($before), 'tested: ' . $before);
        }
    }

    /**
     * @dataProvider trimProvider
     *
     * @param $input
     * @param $output
     */
    public function testTrim($input, $output)
    {
        for ($i = 0; $i <= 2; ++$i) { // keep this loop for simple performance tests

            if ($i === 0) {
                $this->disableNativeUtf8Support();
            } elseif ($i > 0) {
                $this->reactivateNativeUtf8Support();
            }

            static::assertSame($output, UTF8::trim($input));
        }
    }

    /**
     * @dataProvider trimProviderAdvanced
     *
     * @param $input
     * @param $output
     */
    public function testTrimAdvanced($input, $output)
    {
        static::assertSame($output, UTF8::trim($input, ' '));
    }

    /**
     * @dataProvider trimProviderAdvancedWithMoreThenTwoBytes
     *
     * @param $input
     * @param $output
     */
    public function testTrimAdvancedWithMoreThenTwoBytes($input, $output)
    {
        static::assertSame($output, UTF8::trim($input, '白'));
    }

    public function testUrldecode()
    {
        $testArray = [
            'W%F6bse' => 'Wöbse',
            'Ã' => 'Ã',
            'Ã¤' => 'ä',
            ' ' => ' ',
            '' => '',
            "\n" => "\n",
            "\u00ed" => 'í',
            'tes%20öäü%20\u00edtest+test' => 'tes öäü ítest test',
            'test+test@foo.bar' => 'test test@foo.bar',
            'con%5cu00%366irm' => 'confirm',
            '%3A%2F%2F%252567%252569%252573%252574' => '://gist',
            '%253A%252F%252F%25252567%25252569%25252573%25252574' => '://gist',
            "tes%20öäü%20\u00edtest" => 'tes öäü ítest',
            'Düsseldorf' => 'Düsseldorf',
            'Duesseldorf' => 'Duesseldorf',
            'D&#252;sseldorf' => 'Düsseldorf',
            'D%FCsseldorf' => 'Düsseldorf',
            'D&#xFC;sseldorf' => 'Düsseldorf',
            'D%26%23xFC%3Bsseldorf' => 'Düsseldorf',
            'DÃ¼sseldorf' => 'Düsseldorf',
            'D%C3%BCsseldorf' => 'Düsseldorf',
            'D%C3%83%C2%BCsseldorf' => 'Düsseldorf',
            'D%25C3%2583%25C2%25BCsseldorf' => 'Düsseldorf',
            '<strong>D&#252;sseldorf</strong>' => '<strong>Düsseldorf</strong>',
            'Hello%2BWorld%2B%253E%2Bhow%2Bare%2Byou%253F' => 'Hello World > how are you?',
            '%e7%ab%a0%e5%ad%90%e6%80%a1' => '章子怡',
            'Fran%c3%a7ois Truffaut' => 'François Truffaut',
            '%e1%83%a1%e1%83%90%e1%83%a5%e1%83%90%e1%83%a0%e1%83%97%e1%83%95%e1%83%94%e1%83%9a%e1%83%9d' => 'საქართველო',
            '%25e1%2583%25a1%25e1%2583%2590%25e1%2583%25a5%25e1%2583%2590%25e1%2583%25a0%25e1%2583%2597%25e1%2583%2595%25e1%2583%2594%25e1%2583%259a%25e1%2583%259d' => 'საქართველო',
            '%2525e1%252583%2525a1%2525e1%252583%252590%2525e1%252583%2525a5%2525e1%252583%252590%2525e1%252583%2525a0%2525e1%252583%252597%2525e1%252583%252595%2525e1%252583%252594%2525e1%252583%25259a%2525e1%252583%25259d' => 'საქართველო',
            'Bj%c3%b6rk Gu%c3%b0mundsd%c3%b3ttir' => 'Björk Guðmundsdóttir',
            '%e5%ae%ae%e5%b4%8e%e3%80%80%e9%a7%bf' => '宮崎　駿',
            '%u7AE0%u5B50%u6021' => '章子怡',
            '%u0046%u0072%u0061%u006E%u00E7%u006F%u0069%u0073%u0020%u0054%u0072%u0075%u0066%u0066%u0061%u0075%u0074' => 'François Truffaut',
            '%u10E1%u10D0%u10E5%u10D0%u10E0%u10D7%u10D5%u10D4%u10DA%u10DD' => 'საქართველო',
            '%u0042%u006A%u00F6%u0072%u006B%u0020%u0047%u0075%u00F0%u006D%u0075%u006E%u0064%u0073%u0064%u00F3%u0074%u0074%u0069%u0072' => 'Björk Guðmundsdóttir',
            '%u5BAE%u5D0E%u3000%u99FF' => '宮崎　駿',
            '&#31456;&#23376;&#24609;' => '章子怡',
            '&#70;&#114;&#97;&#110;&#231;&#111;&#105;&#115;&#32;&#84;&#114;&#117;&#102;&#102;&#97;&#117;&#116;' => 'François Truffaut',
            '&#4321;&#4304;&#4325;&#4304;&#4320;&#4311;&#4309;&#4308;&#4314;&#4317;' => 'საქართველო',
            '&#66;&#106;&#246;&#114;&#107;&#32;&#71;&#117;&#240;&#109;&#117;&#110;&#100;&#115;&#100;&#243;&#116;&#116;&#105;&#114;' => 'Björk Guðmundsdóttir',
            '&#23470;&#23822;&#12288;&#39423;' => '宮崎　駿',
            'https://foo.bar/tpl_preview.php?pid=122&json=%7B%22recipe_id%22%3A-1%2C%22recipe_created%22%3A%22%22%2C%22recipe_title%22%3A%22vxcvxc%22%2C%22recipe_description%22%3A%22%22%2C%22recipe_yield%22%3A0%2C%22recipe_prepare_time%22%3A0%2C%22recipe_image%22%3A%22%22%2C%22recipe_legal%22%3A0%2C%22recipe_live%22%3A0%2C%22recipe_user_guid%22%3A%22%22%2C%22recipe_category_id%22%3A%5B%5D%2C%22recipe_category_name%22%3A%5B%5D%2C%22recipe_variety_id%22%3A%5B%5D%2C%22recipe_variety_name%22%3A%5B%5D%2C%22recipe_tag_id%22%3A%5B%5D%2C%22recipe_tag_name%22%3A%5B%5D%2C%22recipe_instruction_id%22%3A%5B%5D%2C%22recipe_instruction_text%22%3A%5B%5D%2C%22recipe_ingredient_id%22%3A%5B%5D%2C%22recipe_ingredient_name%22%3A%5B%5D%2C%22recipe_ingredient_amount%22%3A%5B%5D%2C%22recipe_ingredient_unit%22%3A%5B%5D%2C%22formMatchingArray%22%3A%7B%22unites%22%3A%5B%22Becher%22%2C%22Beete%22%2C%22Beutel%22%2C%22Blatt%22%2C%22Bl%5Cu00e4tter%22%2C%22Bund%22%2C%22B%5Cu00fcndel%22%2C%22cl%22%2C%22cm%22%2C%22dicke%22%2C%22dl%22%2C%22Dose%22%2C%22Dose%5C%2Fn%22%2C%22d%5Cu00fcnne%22%2C%22Ecke%28n%29%22%2C%22Eimer%22%2C%22einige%22%2C%22einige+Stiele%22%2C%22EL%22%2C%22EL%2C+geh%5Cu00e4uft%22%2C%22EL%2C+gestr.%22%2C%22etwas%22%2C%22evtl.%22%2C%22extra%22%2C%22Fl%5Cu00e4schchen%22%2C%22Flasche%22%2C%22Flaschen%22%2C%22g%22%2C%22Glas%22%2C%22Gl%5Cu00e4ser%22%2C%22gr.+Dose%5C%2Fn%22%2C%22gr.+Fl.%22%2C%22gro%5Cu00dfe%22%2C%22gro%5Cu00dfen%22%2C%22gro%5Cu00dfer%22%2C%22gro%5Cu00dfes%22%2C%22halbe%22%2C%22Halm%28e%29%22%2C%22Handvoll%22%2C%22K%5Cu00e4stchen%22%2C%22kg%22%2C%22kl.+Bund%22%2C%22kl.+Dose%5C%2Fn%22%2C%22kl.+Glas%22%2C%22kl.+Kopf%22%2C%22kl.+Scheibe%28n%29%22%2C%22kl.+St%5Cu00fcck%28e%29%22%2C%22kl.Flasche%5C%2Fn%22%2C%22kleine%22%2C%22kleinen%22%2C%22kleiner%22%2C%22kleines%22%2C%22Knolle%5C%2Fn%22%2C%22Kopf%22%2C%22K%5Cu00f6pfe%22%2C%22K%5Cu00f6rner%22%2C%22Kugel%22%2C%22Kugel%5C%2Fn%22%2C%22Kugeln%22%2C%22Liter%22%2C%22m.-gro%5Cu00dfe%22%2C%22m.-gro%5Cu00dfer%22%2C%22m.-gro%5Cu00dfes%22%2C%22mehr%22%2C%22mg%22%2C%22ml%22%2C%22Msp.%22%2C%22n.+B.%22%2C%22Paar%22%2C%22Paket%22%2C%22Pck.%22%2C%22Pkt.%22%2C%22Platte%5C%2Fn%22%2C%22Port.%22%2C%22Prise%28n%29%22%2C%22Prisen%22%2C%22Prozent+%25%22%2C%22Riegel%22%2C%22Ring%5C%2Fe%22%2C%22Rippe%5C%2Fn%22%2C%22Rolle%28n%29%22%2C%22Sch%5Cu00e4lchen%22%2C%22Scheibe%5C%2Fn%22%2C%22Schuss%22%2C%22Spritzer%22%2C%22Stange%5C%2Fn%22%2C%22St%5Cu00e4ngel%22%2C%22Stiel%5C%2Fe%22%2C%22Stiele%22%2C%22St%5Cu00fcck%28e%29%22%2C%22Tafel%22%2C%22Tafeln%22%2C%22Tasse%22%2C%22Tasse%5C%2Fn%22%2C%22Teil%5C%2Fe%22%2C%22TL%22%2C%22TL+%28geh%5Cu00e4uft%29%22%2C%22TL+%28gestr.%29%22%2C%22Topf%22%2C%22Tropfen%22%2C%22Tube%5C%2Fn%22%2C%22T%5Cu00fcte%5C%2Fn%22%2C%22viel%22%2C%22wenig%22%2C%22W%5Cu00fcrfel%22%2C%22Wurzel%22%2C%22Wurzel%5C%2Fn%22%2C%22Zehe%5C%2Fn%22%2C%22Zweig%5C%2Fe%22%5D%2C%22yield%22%3A%7B%221%22%3A%221+Portion%22%2C%222%22%3A%222+Portionen%22%2C%223%22%3A%223+Portionen%22%2C%224%22%3A%224+Portionen%22%2C%225%22%3A%225+Portionen%22%2C%226%22%3A%226+Portionen%22%2C%227%22%3A%227+Portionen%22%2C%228%22%3A%228+Portionen%22%2C%229%22%3A%229+Portionen%22%2C%2210%22%3A%2210+Portionen%22%2C%2211%22%3A%2211+Portionen%22%2C%2212%22%3A%2212+Portionen%22%7D%2C%22prepare_time%22%3A%7B%221%22%3A%22schnell%22%2C%222%22%3A%22mittel%22%2C%223%22%3A%22aufwendig%22%7D%2C%22category%22%3A%7B%221%22%3A%22Vorspeise%22%2C%222%22%3A%22Suppe%22%2C%223%22%3A%22Salat%22%2C%224%22%3A%22Hauptspeise%22%2C%225%22%3A%22Beilage%22%2C%226%22%3A%22Nachtisch%5C%2FDessert%22%2C%227%22%3A%22Getr%5Cu00e4nke%22%2C%228%22%3A%22B%5Cu00fcffet%22%2C%229%22%3A%22Fr%5Cu00fchst%5Cu00fcck%5C%2FBrunch%22%7D%2C%22variety%22%3A%7B%221%22%3A%22Basmati+Reis%22%2C%222%22%3A%22Basmati+%26amp%3B+Wild+Reis%22%2C%223%22%3A%22R%5Cu00e4ucherreis%22%2C%224%22%3A%22Jasmin+Reis%22%2C%225%22%3A%221121+Basmati+Wunderreis%22%2C%226%22%3A%22Spitzen+Langkorn+Reis%22%2C%227%22%3A%22Wildreis%22%2C%228%22%3A%22Naturreis%22%2C%229%22%3A%22Sushi+Reis%22%7D%2C%22tag--ingredient%22%3A%7B%221%22%3A%22Eier%22%2C%222%22%3A%22Gem%5Cu00fcse%22%2C%223%22%3A%22Getreide%22%2C%224%22%3A%22Fisch%22%2C%225%22%3A%22Fleisch%22%2C%226%22%3A%22Meeresfr%5Cu00fcchte%22%2C%227%22%3A%22Milchprodukte%22%2C%228%22%3A%22Obst%22%2C%229%22%3A%22Salat%22%7D%2C%22tag--preparation%22%3A%7B%2210%22%3A%22Backen%22%2C%2211%22%3A%22Blanchieren%22%2C%2212%22%3A%22Braten%5C%2FSchmoren%22%2C%2213%22%3A%22D%5Cu00e4mpfen%5C%2FD%5Cu00fcnsten%22%2C%2214%22%3A%22Einmachen%22%2C%2215%22%3A%22Frittieren%22%2C%2216%22%3A%22Gratinieren%5C%2F%5Cu00dcberbacken%22%2C%2217%22%3A%22Grillen%22%2C%2218%22%3A%22Kochen%22%7D%2C%22tag--kitchen%22%3A%7B%2219%22%3A%22Afrikanisch%22%2C%2220%22%3A%22Alpenk%5Cu00fcche%22%2C%2221%22%3A%22Asiatisch%22%2C%2222%22%3A%22Deutsch+%28regional%29%22%2C%2223%22%3A%22Franz%5Cu00f6sisch%22%2C%2224%22%3A%22Mediterran%22%2C%2225%22%3A%22Orientalisch%22%2C%2226%22%3A%22Osteurop%5Cu00e4isch%22%2C%2227%22%3A%22Skandinavisch%22%2C%2228%22%3A%22S%5Cu00fcdamerikanisch%22%2C%2229%22%3A%22US-Amerikanisch%22%2C%2230%22%3A%22%22%7D%2C%22tag--difficulty%22%3A%7B%2231%22%3A%22Einfach%22%2C%2232%22%3A%22Mittelschwer%22%2C%2233%22%3A%22Anspruchsvoll%22%7D%2C%22tag--feature%22%3A%7B%2234%22%3A%22Gut+vorzubereiten%22%2C%2235%22%3A%22Kalorienarm+%5C%2F+leicht%22%2C%2236%22%3A%22Klassiker%22%2C%2237%22%3A%22Preiswert%22%2C%2238%22%3A%22Raffiniert%22%2C%2239%22%3A%22Vegetarisch+%5C%2F+Vegan%22%2C%2240%22%3A%22Vitaminreich%22%2C%2241%22%3A%22Vollwert%22%2C%2242%22%3A%22%22%7D%2C%22tag%22%3A%7B%221%22%3A%22Eier%22%2C%222%22%3A%22Gem%5Cu00fcse%22%2C%223%22%3A%22Getreide%22%2C%224%22%3A%22Fisch%22%2C%225%22%3A%22Fleisch%22%2C%226%22%3A%22Meeresfr%5Cu00fcchte%22%2C%227%22%3A%22Milchprodukte%22%2C%228%22%3A%22Obst%22%2C%229%22%3A%22Salat%22%2C%2210%22%3A%22Backen%22%2C%2211%22%3A%22Blanchieren%22%2C%2212%22%3A%22Braten%5C%2FSchmoren%22%2C%2213%22%3A%22D%5Cu00e4mpfen%5C%2FD%5Cu00fcnsten%22%2C%2214%22%3A%22Einmachen%22%2C%2215%22%3A%22Frittieren%22%2C%2216%22%3A%22Gratinieren%5C%2F%5Cu00dcberbacken%22%2C%2217%22%3A%22Grillen%22%2C%2218%22%3A%22Kochen%22%2C%2219%22%3A%22Afrikanisch%22%2C%2220%22%3A%22Alpenk%5Cu00fcche%22%2C%2221%22%3A%22Asiatisch%22%2C%2222%22%3A%22Deutsch+%28regional%29%22%2C%2223%22%3A%22Franz%5Cu00f6sisch%22%2C%2224%22%3A%22Mediterran%22%2C%2225%22%3A%22Orientalisch%22%2C%2226%22%3A%22Osteurop%5Cu00e4isch%22%2C%2227%22%3A%22Skandinavisch%22%2C%2228%22%3A%22S%5Cu00fcdamerikanisch%22%2C%2229%22%3A%22US-Amerikanisch%22%2C%2230%22%3A%22%22%2C%2231%22%3A%22Einfach%22%2C%2232%22%3A%22Mittelschwer%22%2C%2233%22%3A%22Anspruchsvoll%22%2C%2234%22%3A%22Gut+vorzubereiten%22%2C%2235%22%3A%22Kalorienarm+%5C%2F+leicht%22%2C%2236%22%3A%22Klassiker%22%2C%2237%22%3A%22Preiswert%22%2C%2238%22%3A%22Raffiniert%22%2C%2239%22%3A%22Vegetarisch+%5C%2F+Vegan%22%2C%2240%22%3A%22Vitaminreich%22%2C%2241%22%3A%22Vollwert%22%2C%2242%22%3A%22%22%7D%7D%2C%22errorArray%22%3A%7B%22recipe_prepare_time%22%3A%22error%22%2C%22recipe_yield%22%3A%22error%22%2C%22recipe_category_name%22%3A%22error%22%2C%22recipe_tag_name%22%3A%22error%22%2C%22recipe_instruction_text%22%3A%22error%22%2C%22recipe_ingredient_name%22%3A%22error%22%7D%2C%22errorMessage%22%3A%22Bitte+f%5Cu00fclle+die+rot+markierten+Felder+korrekt+aus.%22%2C%22db%22%3A%7B%22query_count%22%3A20%7D%7D' => 'https://foo.bar/tpl_preview.php?pid=122&json={"recipe_id":-1,"recipe_created":"","recipe_title":"vxcvxc","recipe_description":"","recipe_yield":0,"recipe_prepare_time":0,"recipe_image":"","recipe_legal":0,"recipe_live":0,"recipe_user_guid":"","recipe_category_id":[],"recipe_category_name":[],"recipe_variety_id":[],"recipe_variety_name":[],"recipe_tag_id":[],"recipe_tag_name":[],"recipe_instruction_id":[],"recipe_instruction_text":[],"recipe_ingredient_id":[],"recipe_ingredient_name":[],"recipe_ingredient_amount":[],"recipe_ingredient_unit":[],"formMatchingArray":{"unites":["Becher","Beete","Beutel","Blatt","Blätter","Bund","Bündel","cl","cm","dicke","dl","Dose","Dose\/n","dünne","Ecke(n)","Eimer","einige","einige Stiele","EL","EL, gehäuft","EL, gestr.","etwas","evtl.","extra","Fläschchen","Flasche","Flaschen","g","Glas","Gläser","gr. Dose\/n","gr. Fl.","große","großen","großer","großes","halbe","Halm(e)","Handvoll","Kästchen","kg","kl. Bund","kl. Dose\/n","kl. Glas","kl. Kopf","kl. Scheibe(n)","kl. Stück(e)","kl.Flasche\/n","kleine","kleinen","kleiner","kleines","Knolle\/n","Kopf","Köpfe","Körner","Kugel","Kugel\/n","Kugeln","Liter","m.-große","m.-großer","m.-großes","mehr","mg","ml","Msp.","n. B.","Paar","Paket","Pck.","Pkt.","Platte\/n","Port.","Prise(n)","Prisen","Prozent %","Riegel","Ring\/e","Rippe\/n","Rolle(n)","Schälchen","Scheibe\/n","Schuss","Spritzer","Stange\/n","Stängel","Stiel\/e","Stiele","Stück(e)","Tafel","Tafeln","Tasse","Tasse\/n","Teil\/e","TL","TL (gehäuft)","TL (gestr.)","Topf","Tropfen","Tube\/n","Tüte\/n","viel","wenig","Würfel","Wurzel","Wurzel\/n","Zehe\/n","Zweig\/e"],"yield":{"1":"1 Portion","2":"2 Portionen","3":"3 Portionen","4":"4 Portionen","5":"5 Portionen","6":"6 Portionen","7":"7 Portionen","8":"8 Portionen","9":"9 Portionen","10":"10 Portionen","11":"11 Portionen","12":"12 Portionen"},"prepare_time":{"1":"schnell","2":"mittel","3":"aufwendig"},"category":{"1":"Vorspeise","2":"Suppe","3":"Salat","4":"Hauptspeise","5":"Beilage","6":"Nachtisch\/Dessert","7":"Getränke","8":"Büffet","9":"Frühstück\/Brunch"},"variety":{"1":"Basmati Reis","2":"Basmati & Wild Reis","3":"Räucherreis","4":"Jasmin Reis","5":"1121 Basmati Wunderreis","6":"Spitzen Langkorn Reis","7":"Wildreis","8":"Naturreis","9":"Sushi Reis"},"tag--ingredient":{"1":"Eier","2":"Gemüse","3":"Getreide","4":"Fisch","5":"Fleisch","6":"Meeresfrüchte","7":"Milchprodukte","8":"Obst","9":"Salat"},"tag--preparation":{"10":"Backen","11":"Blanchieren","12":"Braten\/Schmoren","13":"Dämpfen\/Dünsten","14":"Einmachen","15":"Frittieren","16":"Gratinieren\/Überbacken","17":"Grillen","18":"Kochen"},"tag--kitchen":{"19":"Afrikanisch","20":"Alpenküche","21":"Asiatisch","22":"Deutsch (regional)","23":"Französisch","24":"Mediterran","25":"Orientalisch","26":"Osteuropäisch","27":"Skandinavisch","28":"Südamerikanisch","29":"US-Amerikanisch","30":""},"tag--difficulty":{"31":"Einfach","32":"Mittelschwer","33":"Anspruchsvoll"},"tag--feature":{"34":"Gut vorzubereiten","35":"Kalorienarm \/ leicht","36":"Klassiker","37":"Preiswert","38":"Raffiniert","39":"Vegetarisch \/ Vegan","40":"Vitaminreich","41":"Vollwert","42":""},"tag":{"1":"Eier","2":"Gemüse","3":"Getreide","4":"Fisch","5":"Fleisch","6":"Meeresfrüchte","7":"Milchprodukte","8":"Obst","9":"Salat","10":"Backen","11":"Blanchieren","12":"Braten\/Schmoren","13":"Dämpfen\/Dünsten","14":"Einmachen","15":"Frittieren","16":"Gratinieren\/Überbacken","17":"Grillen","18":"Kochen","19":"Afrikanisch","20":"Alpenküche","21":"Asiatisch","22":"Deutsch (regional)","23":"Französisch","24":"Mediterran","25":"Orientalisch","26":"Osteuropäisch","27":"Skandinavisch","28":"Südamerikanisch","29":"US-Amerikanisch","30":"","31":"Einfach","32":"Mittelschwer","33":"Anspruchsvoll","34":"Gut vorzubereiten","35":"Kalorienarm \/ leicht","36":"Klassiker","37":"Preiswert","38":"Raffiniert","39":"Vegetarisch \/ Vegan","40":"Vitaminreich","41":"Vollwert","42":""}},"errorArray":{"recipe_prepare_time":"error","recipe_yield":"error","recipe_category_name":"error","recipe_tag_name":"error","recipe_instruction_text":"error","recipe_ingredient_name":"error"},"errorMessage":"Bitte fülle die rot markierten Felder korrekt aus.","db":{"query_count":20}}',
            '<a href="&#38&#35&#49&#48&#54&#38&#35&#57&#55&#38&#35&#49&#49&#56&#38&#35&#57&#55&#38&#35&#49&#49&#53&#38&#35&#57&#57&#38&#35&#49&#49&#52&#38&#35&#49&#48&#53&#38&#35&#49&#49&#50&#38&#35&#49&#49&#54&#38&#35&#53&#56&#38&#35&#57&#57&#38&#35&#49&#49&#49&#38&#35&#49&#49&#48&#38&#35&#49&#48&#50&#38&#35&#49&#48&#53&#38&#35&#49&#49&#52&#38&#35&#49&#48&#57&#38&#35&#52&#48&#38&#35&#52&#57&#38&#35&#52&#49">Clickhere</a>' => '<a href="javascript:confirm(1)">Clickhere</a>',
        ];

        foreach ($testArray as $before => $after) {
            static::assertSame($after, UTF8::urldecode($before), 'testing: ' . $before);
        }
    }

    public function testUrldecodeFixWin1252Chars()
    {
        $urldecode_fix_win1252_chars = UTF8::urldecode_fix_win1252_chars();

        static::assertInternalType('array', $urldecode_fix_win1252_chars);
        static::assertTrue(\count($urldecode_fix_win1252_chars) > 0);
    }

    public function testUtf8DecodeEncodeUtf8()
    {
        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-中文空白-  ',
            '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
            'öäü'            => 'öäü',
            ''               => '',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::encode('UTF-8', $before));
        }

        // ---

        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-????-  ',
            '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
            'öäü'            => 'öäü',
            ''               => '',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::encode('UTF-8', UTF8::utf8_decode($before)));
        }

        // ---

        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-????-  ',
            '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
            'öäü'            => 'öäü',
            ''               => '',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::utf8_encode(UTF8::encode('ISO-8859-1', $before, false)));
        }
    }

    public function testUtf8DecodeUtf8Encode()
    {
        $tests = [
            '  -ABC-中文空白-  '    => '  -ABC-????-  ',
            '      - ÖÄÜ- '     => '      - ÖÄÜ- ',
            'öäü'               => 'öäü',
            // ''                  => '',
            // false               => '0',
            // null                => '',
            "\xe2\x28\xa1"      => '?',
            "\xa0\xa1"          => \html_entity_decode('&nbsp;') . '¡',
            "κόσμε\xa0\xa1-öäü" => '?????' . \html_entity_decode('&nbsp;') . '¡-öäü',
            'foobar'            => 'foobar',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::utf8_encode((UTF8::utf8_decode($before))));
        }
    }

    public function testUtf8Encode()
    {
        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-ä¸­æç©ºç½-  ',
            '      - ÖÄÜ- '  => '      - ÃÃÃ- ',
            'öäü'            => 'Ã¶Ã¤Ã¼',
            ''               => '',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::utf8_encode($before));
        }
    }

    public function testUtf8EncodeEncodeUtf8()
    {
        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-ä¸­æç©ºç½-  ',
            '      - ÖÄÜ- '  => '      - ÃÃÃ- ',
            'öäü'            => 'Ã¶Ã¤Ã¼',
            ''               => '',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::encode('UTF-8', UTF8::utf8_encode($before)));
        }
    }

    public function testUtf8EncodeUtf8Decode()
    {
        $tests = [
            'ا (Alif) · ب (Bāʾ) · ت (Tāʾ) · ث (Ṯāʾ) · ج (Ǧīm) · ح (Ḥāʾ) · خ (Ḫāʾ) · د (Dāl) · ذ (Ḏāl) · ر (Rāʾ) · ز (Zāy) · س (Sīn) · ش (Šīn) · ص (Ṣād) · ض (Ḍād) · ط (Ṭāʾ) · ظ (Ẓāʾ) · ع (ʿAin) · غ (Ġain) · ف (Fāʾ) · ق (Qāf) · ك (Kāf) · ل (Lām) · م (Mīm) · ن (Nūn) · ه (Hāʾ) · و (Wāw) · ي (Yāʾ)' => 'ا (Alif) · ب (Bāʾ) · ت (Tāʾ) · ث (Ṯāʾ) · ج (Ǧīm) · ح (Ḥāʾ) · خ (Ḫāʾ) · د (Dāl) · ذ (Ḏāl) · ر (Rāʾ) · ز (Zāy) · س (Sīn) · ش (Šīn) · ص (Ṣād) · ض (Ḍād) · ط (Ṭāʾ) · ظ (Ẓāʾ) · ع (ʿAin) · غ (Ġain) · ف (Fāʾ) · ق (Qāf) · ك (Kāf) · ل (Lām) · م (Mīm) · ن (Nūn) · ه (Hāʾ) · و (Wāw) · ي (Yāʾ)',
            'строка на русском'                                                                                                                                                                                                                                                                        => 'строка на русском',
            '  -ABC-中文空白-  '                                                                                                                                                                                                                                                                           => '  -ABC-中文空白-  ',
            '      - ÖÄÜ- '                                                                                                                                                                                                                                                                            => '      - ÖÄÜ- ',
            'öäü'                                                                                                                                                                                                                                                                                      => 'öäü',
            ''                                                                                                                                                                                                                                                                                         => '',
            'foobar'                                                                                                                                                                                                                                                                                   => 'foobar',
            ' 123'                                                                                                                                                                                                                                                                                     => ' 123',
            "κόσμε\xc2\xa0"                                                                                                                                                                                                                                                                            => "κόσμε\xc2\xa0",
            "\xd1\xd2"                                                                                                                                                                                                                                                                                 => "\xd1\xd2",
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::utf8_decode(UTF8::utf8_encode($before)));
        }
    }

    public function testUtf8EncodeUtf8Encode()
    {
        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-Ã¤Â¸Â­Ã¦ÂÂÃ§Â©ÂºÃ§ÂÂ½-  ',
            '      - ÖÄÜ- '  => '      - ÃÂÃÂÃÂ- ',
            'öäü'            => 'ÃÂ¶ÃÂ¤ÃÂ¼',
            ''               => '',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::utf8_encode(UTF8::utf8_encode($before)));
        }
    }

    public function testUtf8FileWithBom()
    {
        $bom = UTF8::file_has_bom(__DIR__ . '/fixtures/utf-8-bom.txt');
        static::assertTrue($bom);

        $bom = UTF8::file_has_bom(__DIR__ . '/fixtures/utf-8.txt');
        static::assertFalse($bom);
    }

    public function testUtf8FixWin1252Chars()
    {
        $testArray = [
            'Düsseldorf'          => 'Düsseldorf',
            'Ã'                   => 'Ã',
            'ñ'                   => 'ñ',
            'àáâãäåæ ç èéêë ìíîï' => 'àáâãäåæ ç èéêë ìíîï',
            ' '                   => ' ',
            ''                    => '',
            "\n"                  => "\n",
            "test\xc2\x88"        => 'testˆ',
            'DÃ¼sseldorf'         => 'Düsseldorf',
            'Ã¤'                  => 'ä',
        ];

        foreach ($testArray as $before => $after) {
            static::assertSame($after, UTF8::utf8_fix_win1252_chars($before));
        }
    }

    public function testUtf8Strstr()
    {
        $tests = [
            'ABC@中文空白.com' => [
                'ABC',
                '@中文空白.com',
            ],
            ' @ - ÖÄÜ- '   => [
                ' ',
                '@ - ÖÄÜ- ',
            ],
            'öä@ü'         => [
                'öä',
                '@ü',
            ],
            ''             => [
                false,
                false,
            ],
            '  '           => [
                false,
                false,
            ],
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after[0], UTF8::strstr($before, '@', true), 'tested: ' . $before);
            // alias
            static::assertSame($after[0], UTF8::strchr($before, '@', true), 'tested: ' . $before);
        }

        // ---

        foreach ($tests as $before => $after) {
            static::assertSame($after[1], UTF8::strstr($before, '@'), 'tested: ' . $before);
        }

        // --- UTF-8

        static::assertSame('ABC', UTF8::strstr('ABC@中文空白.com', '@', true, 'UTF-8'));
        static::assertSame('@中文空白.com', UTF8::strstr('ABC@中文空白.com', '@', false, 'UTF-8'));

        static::assertSame('ABC@', UTF8::strstr('ABC@中文空白.com', '中文空白', true, 'UTF-8'));
        static::assertSame('中文空白.com', UTF8::strstr('ABC@中文空白.com', '中文空白', false, 'UTF-8'));

        // --- ISO

        static::assertSame('ABC', UTF8::strstr('ABC@中文空白.com', '@', true, 'ISO'));
        static::assertSame('@中文空白.com', UTF8::strstr('ABC@中文空白.com', '@', false, 'ISO'));

        static::assertSame('ABC@', UTF8::strstr('ABC@中文空白.com', '中文空白', true, 'ISO'));
        static::assertSame('中文空白.com', UTF8::strstr('ABC@中文空白.com', '中文空白', false, 'ISO'));

        // --- false

        static::assertFalse(UTF8::strstr('ABC@中文空白.com', 'z', true, 'UTF-8'));
        static::assertFalse(UTF8::strstr('ABC@中文空白.com', 'z', false, 'UTF-8'));
        static::assertFalse(UTF8::strstr('', 'z', true, 'UTF-8'));
        static::assertFalse(UTF8::strstr('', 'z', false, 'UTF-8'));
    }

    public function testValidCharsViaUtf8Encode()
    {
        $tests = UTF8::json_decode(UTF8::file_get_contents(__DIR__ . '/fixtures/valid.json'), true);

        foreach ($tests as $test) {
            static::assertSame($test, UTF8::encode('UTF-8', $test));
        }
    }

    public function testWhitespace()
    {
        $whitespaces = UTF8::whitespace_table();
        foreach ($whitespaces as $whitespace) {
            static::assertSame(' ', UTF8::clean($whitespace, false, true));
        }
    }

    public function testWordCount()
    {
        $testArray = [
            '中文空白 öäü abc' => 3,
            'öäü öäü öäü'  => 3,
            'abc'          => 1,
            ''             => 0,
            ' '            => 0,
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::str_word_count($actual));
        }

        static::assertSame(3, UTF8::str_word_count('中文空白 foo öäü'));
        static::assertSame(3, UTF8::str_word_count('中文空白 foo öäü', 0));
        static::assertSame(
            [
                0 => '中文空白',
                1 => 'foo',
                2 => 'öäü',
            ],
            UTF8::str_word_count('中文空白 foo öäü', 1)
        );
        static::assertSame(3, UTF8::str_word_count('中文空白 foo öäü#s', 0, '#'));
        static::assertSame(4, UTF8::str_word_count('中文空白 foo öäü#s', 0, ''));
        static::assertSame(
            [
                '中文空白',
                'foo',
                'öäü#s',
            ],
            UTF8::str_word_count('中文空白 foo öäü#s', 1, '#')
        );
        static::assertSame(
            [
                0 => '中文空白',
                5 => 'foo',
                9 => 'öäü#s',
            ],
            UTF8::str_word_count('中文空白 foo öäü#s', 2, '#')
        );
        static::assertSame(
            [
                0 => '中文空白',
                5 => 'foo',
                9 => 'öäü',
            ],
            UTF8::str_word_count('中文空白 foo öäü', 2)
        );
        static::assertSame(
            [
                'test',
                'foo',
                'test',
                'test-test',
                'test',
                'test',
                'test\'s',
                'test’s',
                'test#s',
            ],
            UTF8::str_word_count('test,foo test test-test test_test test\'s test’s test#s', 1, '#')
        );
        static::assertSame(
            [
                'test',
                'foo',
                'test',
                'test-test',
                'test',
                'test',
                'test\'s',
                'test’s',
                'test',
                's',
            ],
            UTF8::str_word_count('test,foo test test-test test_test test\'s test’s test#s', 1)
        );
    }

    public function testWordsLimit()
    {
        $testArray = [
            ['this is a test', 'this is a test', 5, '...'],
            ['this is öäü-foo test', 'this is öäü-foo test', 8, '...'],
            ['fòô...öäü', 'fòô bàř fòô', 1, '...öäü'],
            ['fòô', 'fòô bàř fòô', 1, ''],
            ['fòô bàř', 'fòô bàř fòô', 2, ''],
            ['fòô', 'fòô', 1, ''],
            ['', 'fòô', 0, ''],
            ['', '', 1, '...'],
            ['', '', 0, '...'],
        ];

        foreach ($testArray as $test) {
            static::assertSame($test[0], UTF8::words_limit($test[1], $test[2], $test[3]), 'tested: ' . $test[1]);
        }
    }

    public function testWs()
    {
        $whitespace = UTF8::ws();

        static::assertInternalType('array', $whitespace);
        static::assertTrue(\count($whitespace) > 0);
    }

    public function testcleanParameter()
    {
        $dirtyTestString = "\xEF\xBB\xBF„ Abc d\00e\00f\xc2\xa0\x20…” — 😃";

        static::assertSame("\xEF\xBB\xBF„ Abc def\xc2\xa0\x20…” — 😃", UTF8::clean($dirtyTestString));
        static::assertSame("\xEF\xBB\xBF„ Abc def \x20…” — 😃", UTF8::clean($dirtyTestString, false, true, false, false));
        static::assertSame("\xEF\xBB\xBF„ Abc def\xc2\xa0\x20…” — 😃", UTF8::clean($dirtyTestString, false, false, false, true));
        static::assertSame("\xEF\xBB\xBF„ Abc def\xc2\xa0\x20…” — 😃", UTF8::clean($dirtyTestString, false, false, false, false));
        static::assertSame("\xEF\xBB\xBF\" Abc def\xc2\xa0\x20...\" - 😃", UTF8::clean($dirtyTestString, false, false, true, true));
        static::assertSame("\xEF\xBB\xBF\" Abc def\xc2\xa0\x20...\" - 😃", UTF8::clean($dirtyTestString, false, false, true, false));
        static::assertSame("\xEF\xBB\xBF\" Abc def  ...\" - 😃", UTF8::clean($dirtyTestString, false, true, true, false));
        static::assertSame("\xEF\xBB\xBF\" Abc def\xc2\xa0\x20...\" - 😃", UTF8::clean($dirtyTestString, false, true, true, true));
        static::assertSame("„ Abc def\xc2\xa0\x20…” — 😃", UTF8::clean($dirtyTestString, true, false, false, false));
        static::assertSame("„ Abc def\xc2\xa0\x20…” — 😃", UTF8::clean($dirtyTestString, true, false, false, true));
        static::assertSame("\" Abc def\xc2\xa0\x20...\" - 😃", UTF8::clean($dirtyTestString, true, false, true, false));
        static::assertSame("\" Abc def\xc2\xa0\x20...\" - 😃", UTF8::clean($dirtyTestString, true, false, true, true));
        static::assertSame('„ Abc def  …” — 😃', UTF8::clean($dirtyTestString, true, true, false, false));
        static::assertSame('„ Abc def' . \html_entity_decode('&nbsp;') . ' …” — 😃', UTF8::clean($dirtyTestString, true, true, false, true));
        static::assertSame('" Abc def  ..." - 😃', UTF8::clean($dirtyTestString, true, true, true, false));
        static::assertSame("\" Abc def\xc2\xa0 ...\" - 😃", UTF8::clean($dirtyTestString, true, true, true, true));
    }

    public function testhexToChr()
    {
        static::assertSame('<', UTF8::hex_to_chr('3c'));
        static::assertSame('<', UTF8::hex_to_chr('003c'));
        static::assertSame('&', UTF8::hex_to_chr('26'));
        static::assertSame('}', UTF8::hex_to_chr('7d'));
        static::assertSame('Σ', UTF8::hex_to_chr('3A3'));
        static::assertSame('Σ', UTF8::hex_to_chr('03A3'));
        static::assertSame('Σ', UTF8::hex_to_chr('3a3'));
        static::assertSame('Σ', UTF8::hex_to_chr('03a3'));
    }

    public function testhtmlEncodeChr()
    {
        static::assertSame('\'', UTF8::decimal_to_chr(39));
        static::assertSame('\'', UTF8::decimal_to_chr('39'));
        static::assertSame('&', UTF8::decimal_to_chr(38));
        static::assertSame('&', UTF8::decimal_to_chr('38'));
        static::assertSame('<', UTF8::decimal_to_chr(60));
        static::assertSame('Σ', UTF8::decimal_to_chr(931));
        static::assertSame('Σ', UTF8::decimal_to_chr('0931'));
        // alias
        static::assertSame('Σ', UTF8::int_to_chr('0931'));
    }

    /**
     * @return array
     */
    public function trimProvider(): array
    {
        return [
            [
                '  ',
                '',
            ],
            [
                '',
                '',
            ],
            [
                '　中文空白　 ',
                '中文空白',
            ],
            [
                'do not go gentle into that good night',
                'do not go gentle into that good night',
            ],
        ];
    }

    /**
     * @return array
     */
    public function trimProviderAdvanced(): array
    {
        return [
            [
                '  ',
                '',
            ],
            [
                '',
                '',
            ],
            [
                ' 白 ',
                '白',
            ],
            [
                '   白白 ',
                '白白',
            ],
            [
                '　中文空白',
                '　中文空白',
            ],
            [
                'do not go gentle into that good night',
                'do not go gentle into that good night',
            ],
        ];
    }

    /**
     * @return array
     */
    public function trimProviderAdvancedWithMoreThenTwoBytes(): array
    {
        return [
            [
                '  ',
                '  ',
            ],
            [
                '',
                '',
            ],
            [
                '白',
                '',
            ],
            [
                '白白',
                '',
            ],
            [
                '　中文空白',
                '　中文空',
            ],
            [
                'do not go gentle into that good night',
                'do not go gentle into that good night',
            ],
        ];
    }

    private function reactivateNativeUtf8Support()
    {
        if ($this->oldSupportArray === null) {
            return;
        }

        $refObject = new \ReflectionObject(new UTF8());
        $refProperty = $refObject->getProperty('SUPPORT');
        $refProperty->setAccessible(true);

        $refProperty->setValue(null, $this->oldSupportArray);
    }

    private function disableNativeUtf8Support()
    {
        $refObject = new \ReflectionObject(new UTF8());
        $refProperty = $refObject->getProperty('SUPPORT');
        $refProperty->setAccessible(true);

        if ($this->oldSupportArray === null) {
            $this->oldSupportArray = $refProperty->getValue(null);
        }

        // skip this if we already have different results from "mbstring_func_overload"
        if ($this->oldSupportArray['mbstring_func_overload'] === true) {
            return;
        }

        $testArray = [
            'already_checked_via_portable_utf8' => true,
            'mbstring'                          => false,
            'mbstring_func_overload'            => false,
            'iconv'                             => false,
            'intl'                              => false,
            'intl__transliterator_list_ids'     => [],
            'intlChar'                          => false,
            'pcre_utf8'                         => false,
        ];
        $refProperty->setValue(null, $testArray);
    }
}
