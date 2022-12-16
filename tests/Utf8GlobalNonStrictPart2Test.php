<?php

declare(strict_types=0);

namespace voku\tests;

use voku\helper\Bootup;
use voku\helper\UTF8;

/**
 * Class Utf8GlobalNonStrictPart2Test
 *
 * @internal
 */
final class Utf8GlobalNonStrictPart2Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $oldSupportArray;

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
        $method = (new \ReflectionClass(\get_class($object)))->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function testStrlenInByte()
    {
        // string with UTF-16 (LE) BOM + valid UTF-8 && invalid UTF-8
        $string = "\xFF\xFE" . 'string <strong>with utf-8 chars åèä</strong>' . "\xa0\xa1" . ' - doo-bee doo-bee dooh';

        static::assertSame(74, UTF8::strlen_in_byte($string));
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

        if (UTF8::mbstring_loaded()) { // only with "mbstring"
            static::assertSame(71, UTF8::strlen($string));
        }

        $string_test1 = \strip_tags($string);
        $string_test2 = UTF8::strip_tags($string);

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            static::assertSame(54, \strlen($string_test1));
        } else {
            static::assertSame(57, \strlen($string_test1)); // not correct
        }

        // only "mbstring" can handle broken UTF-8 by default
        if (UTF8::mbstring_loaded()) {
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
            ''                          => 0,
            1                           => 1,
            -1                          => 2,
        ];

        for ($i = 0; $i <= 2; ++$i) { // keep this loop for simple performance tests

            if ($i === 0) {
                $this->disableNativeUtf8Support();
            } elseif ($i > 0) {
                $this->reactivateNativeUtf8Support();
            }

            foreach ($testArray as $actual => $expected) {
                static::assertSame($expected, UTF8::strlen($actual), $actual);
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
            static::assertSame($expected, \strlen($actual), $actual);
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
                static::assertSame(UTF8::strncasecmp($before, 'ü', 10), 0, 'tested: ' . $before);
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
                static::assertSame(UTF8::strncmp($before, 'ü', 10), 0, 'tested: ' . $before);
            }
        }
    }

    public function testStrpbrk()
    {
        // php compatible tests

        $text = 'This is a Simple text.';

        if (!Bootup::is_php('8.0')) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertFalse(@\strpbrk($text, ''));

            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertSame(@\strpbrk($text, ''), UTF8::strpbrk($text, ''));
        }

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

    public function testStrrposInByte()
    {
        static::assertSame(40, UTF8::strrpos_in_byte('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白'));
        static::assertSame(40, UTF8::strrpos_in_byte('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', 0));
        static::assertSame(0, UTF8::strrpos_in_byte('ZBC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', 'Z', 0));
        static::assertFalse(UTF8::strrpos_in_byte('ZBC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', 'z', 0));
        static::assertFalse(UTF8::strrpos_in_byte('ZBC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', 'Z', 1));
    }

    public function testStrriposInByte()
    {
        static::assertSame(40, UTF8::strripos_in_byte('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白'));
        static::assertSame(40, UTF8::strripos_in_byte('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', 0));
        static::assertSame(0, UTF8::strripos_in_byte('ZBC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', 'z', 0));
        static::assertSame(0, UTF8::strripos_in_byte('ZBC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', 'Z', 0));
        static::assertFalse(UTF8::strripos_in_byte('ZBC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', 'z', 1));
    }

    public function testStriposInByte()
    {
        static::assertSame(27, UTF8::stripos_in_byte('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白'));
        static::assertSame(27, UTF8::stripos_in_byte('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', 0));
        static::assertSame(0, UTF8::stripos_in_byte('ZBC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', 'Z', 0));
        static::assertSame(0, UTF8::stripos_in_byte('ZBC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', 'z', 0));
        static::assertSame(47, UTF8::stripos_in_byte('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', 'A', 1));
    }

    public function testStrposInByte()
    {
        static::assertSame(27, UTF8::strpos_in_byte('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白'));
        static::assertSame(27, UTF8::strpos_in_byte('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', 0));
        static::assertSame(0, UTF8::strpos_in_byte('ZBC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', 'Z', 0));
        static::assertFalse(UTF8::strpos_in_byte('ZBC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', 'z', 0));
        static::assertFalse(UTF8::strpos_in_byte('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', 'A', 1));
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

            if (!Bootup::is_php('8.0')) {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                static::assertFalse(@\strpos('abc', ''));
                static::assertFalse(UTF8::strpos('abc', ''));
            } else {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                static::assertSame(0, @\strpos('abc', ''));
                static::assertSame(0, UTF8::strpos('abc', ''));
            }

            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertSame(@\strpos('', ''), UTF8::strpos('', ''));
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertSame(@\strpos(' ', ''), UTF8::strpos(' ', ''));
            static::assertSame(\strpos('', ' '), UTF8::strpos('', ' '));
            static::assertSame(\strpos(' ', ' '), UTF8::strpos(' ', ' '));
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertSame(@\strpos('DJ', ''), UTF8::strpos('DJ', ''));
            static::assertSame(\strpos('DJ', ' '), UTF8::strpos('DJ', ' '));
            static::assertSame(\strpos('', 'Σ'), UTF8::strpos('', 'Σ'));
            static::assertSame(\strpos(' ', 'Σ'), UTF8::strpos(' ', 'Σ'));
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertSame(@\strpos('DJ', ''), UTF8::strpos('DJ', ''));
            static::assertSame(\strpos('DJ', ' '), UTF8::strpos('DJ', ' '));
            static::assertSame(\strpos('', 'Σ'), UTF8::strpos('', 'Σ'));
            static::assertSame(\strpos(' ', 'Σ'), UTF8::strpos(' ', 'Σ'));

            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertSame(UTF8::strpos('abc', ''), @\strpos('abc', ''));

            static::assertFalse(\strpos('abc', 'd'));
            static::assertFalse(UTF8::strpos('abc', 'd'));

            static::assertFalse(\strpos('abc', 'a', 3));
            static::assertFalse(UTF8::strpos('abc', 'a', 3));

            static::assertFalse(\strpos('abc', 'a', 1));
            static::assertFalse(UTF8::strpos('abc', 'a', 1));

            static::assertSame(1, \strpos('abc', 'b', 1));
            static::assertSame(1, UTF8::strpos('abc', 'b', 1));

            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertFalse(@\strpos('abc', 'b', -1));
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

                if (Bootup::is_php('7.1')) {
                    static::assertSame(20, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', -8));
                } else {
                    static::assertFalse(UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', -8));
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

        if (UTF8::mbstring_loaded()) { // only with "mbstring"
            static::assertSame('κόσμε-äöü', UTF8::strrchr('κόσμεκόσμε-äöü', 'κόσμε', false, 'ISO'));
            static::assertFalse(UTF8::strrchr('Aκόσμεκόσμε-äöü', 'aκόσμε', false, 'ISO'));

            static::assertSame('κόσμε', UTF8::strrchr('κόσμεκόσμε-äöü', 'κόσμε', true, 'ISO'));
            static::assertFalse(UTF8::strrchr('Aκόσμεκόσμε-äöü', 'aκόσμε', true, 'ISO'));
        }
    }

    public function testStrrev()
    {
        $testArray = [
            'Hello from github'                                      => 'buhtig morf olleH',
            '1'                                                      => '1',
            'ab'                                                     => 'ba',
            'тест по UTF8'                                           => '8FTU оп тсет',
            'اهلا بك'                                                => 'كب الها',
            '👹👺💀👻'                                                   => '👻💀👺👹',
            "\u{1000}\u{1F7C9}\u{12043}𒁂\u{12042}\u{12030}\u{12031}" => '𒀱𒀰𒁂𒁂𒁃🟉က',
            '﷽﷽﷽﷽﷽﷽﷽﷽﷽﷽﷽﷽﷽﷽﷽﷽'                                       => '﷽﷽﷽﷽﷽﷽﷽﷽﷽﷽﷽﷽﷽﷽﷽﷽',
            'κ-öäü'                                                  => 'üäö-κ',
            'abc'                                                    => 'cba',
            'abcöäü'                                                 => 'üäöcba',
            '-白-'                                                    => '-白-',
            ''                                                       => '',
            ' '                                                      => ' ',
            '👱👱🏻👱🏼👱🏽👱🏾👱🏿'                                            => '👱🏿👱🏾👱🏽👱🏼👱🏻👱',
            '🧟‍♀️🧟‍♂️'                                               => '🧟‍♂️🧟‍♀️',
            '👨‍❤️‍💋‍👨👩‍👩‍👧‍👦'                                        => '👩‍👩‍👧‍👦👨‍❤️‍💋‍👨',
            'اختبار النص'                                            => 'صنلا رابتخا', // Right-to-left words
            'من left اليمين to الى right اليسار'                     => 'راسيلا thgir ىلا ot نيميلا tfel نم', // Mixed-direction words
        ];

        for ($i = 0; $i <= 2; ++$i) { // keep this loop for simple performance tests
            foreach ($testArray as $actual => $expected) {
                static::assertSame($expected, UTF8::strrev($actual), 'error by ' . $actual);
            }
        }

        if (UTF8::getSupportInfo('intl') === true) {
            static::assertSame('abcåö', UTF8::strrev('öåcba'));
            static::assertSame('Z̤͔ͧ̑̓ä͖̭̈̇lͮ̒ͫǧ̗͚̚o̙̔ͮ̇͐̇', UTF8::strrev('o̙̔ͮ̇͐̇ǧ̗͚̚lͮ̒ͫä͖̭̈̇Z̤͔ͧ̑̓')); // Vertically-stacked characters
        }
    }

    public function testDecodeEncodeEmoji()
    {
        $testArray = [
            '1',
            'a',
            'ö',
            '👻💀👺👹',
            '👱🏿👱🏾👱🏽👱🏼👱🏻👱',
            '🧟‍♂️🧟‍♀️',
            '👩‍👩‍👧‍👦👨‍❤️‍💋‍👨',
        ];

        foreach ($testArray as $actual) {
            static::assertSame($actual, UTF8::emoji_decode(UTF8::emoji_encode($actual)), 'tested: ' . $actual);
        }

        foreach ($testArray as $actual) {
            static::assertSame($actual, UTF8::emoji_decode(UTF8::emoji_encode($actual, true), true), 'tested: ' . $actual);
        }

        static::assertSame('foo CHARACTER_OGRE', UTF8::emoji_encode('foo 👹', false));
        static::assertSame('foo _-_PORTABLE_UTF8_-_308095726_-_627590803_-_8FTU_ELBATROP_-_', UTF8::emoji_encode('foo 👹', true));

        static::assertSame('foo 👹', UTF8::emoji_decode('foo CHARACTER_OGRE', false));
        static::assertSame('foo 👹', UTF8::emoji_decode('foo _-_PORTABLE_UTF8_-_308095726_-_627590803_-_8FTU_ELBATROP_-_', true));
    }

    public function testEmojiFromCountryCode()
    {
        static::assertSame('🇩🇪', UTF8::emoji_from_country_code('DE'));
        static::assertSame('🇯🇵', UTF8::emoji_from_country_code('JP'));
        static::assertSame('🇯🇵', UTF8::emoji_from_country_code('Jp'));
        static::assertSame('', UTF8::emoji_from_country_code('J'));
        static::assertSame('', UTF8::emoji_from_country_code(''));
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

        if (UTF8::mbstring_loaded()) { // only with "mbstring"
            static::assertSame('Aκόσμεκόσμε-äöü', UTF8::strrichr('Aκόσμεκόσμε-äöü', 'aκόσμε', false, 'ISO'));
            static::assertSame('ü-abc', UTF8::strrichr('äöü-abc', 'ü', false, 'ISO'));

            static::assertSame('', UTF8::strrichr('Aκόσμεκόσμε-äöü', 'aκόσμε', true, 'ISO'));
            static::assertSame('äö', UTF8::strrichr('äöü-abc', 'ü', true, 'ISO'));
        }
    }

    public function testStrrpos()
    {
        static::assertSame(\strrpos('', ''), UTF8::strrpos('', ''));
        static::assertSame(\strrpos(' ', ''), UTF8::strrpos(' ', ''));
        static::assertSame(\strrpos('', ' '), UTF8::strrpos('', ' '));
        static::assertSame(\strrpos(' ', ' '), UTF8::strrpos(' ', ' '));
        static::assertSame(\strrpos('DJ', ''), UTF8::strrpos('DJ', ''));
        static::assertSame(\strrpos('DJ', ' '), UTF8::strrpos('DJ', ' '));
        static::assertSame(\strrpos('', 'Σ'), UTF8::strrpos('', 'Σ'));
        static::assertSame(\strrpos(' ', 'Σ'), UTF8::strrpos(' ', 'Σ'));
        static::assertSame(\strrpos('DJ', ''), UTF8::strrpos('DJ', ''));
        static::assertSame(\strrpos('DJ', ' '), UTF8::strrpos('DJ', ' '));
        static::assertSame(\strrpos('', 'Σ'), UTF8::strrpos('', 'Σ'));
        static::assertSame(\strrpos(' ', 'Σ'), UTF8::strrpos(' ', 'Σ'));

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            static::assertSame(1, \strrpos('한국어', '국'));
        } else {
            static::assertSame(3, \strrpos('한국어', '국')); // not correct
        }

        // bug is reported: https://github.com/facebook/hhvm/issues/7318
        if (\defined('HHVM_VERSION')) {
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

        if (UTF8::mbstring_loaded()) { // only with "mbstring"
            static::assertSame(11, UTF8::strrpos("Iñtërnâtiôn\xE9àlizætiøn", 'à', 0, 'UTF-8', true));
            static::assertSame(12, UTF8::strrpos("Iñtërnâtiôn\xE9àlizætiøn", 'à', 0, 'UTF-8', false));
        }

        // ---

        static::assertSame(1, UTF8::strrpos('11--', '1-', 0, 'UTF-8', false));
        static::assertSame(2, UTF8::strrpos('-11--', '1-', 0, 'UTF-8', false));
        if (!\voku\helper\Bootup::is_php('8.0')) {
            static::assertFalse(UTF8::strrpos('한국어', '', 0, 'UTF-8', false));
        } else {
            static::assertSame(3, UTF8::strrpos('한국어', '', 0, 'UTF-8', false));
        }
        static::assertSame(1, UTF8::strrpos('한국어', '국', 0, 'UTF8', true));
        if (!\voku\helper\Bootup::is_php('8.0')) {
            static::assertFalse(UTF8::strrpos('한국어', ''));
        } else {
            static::assertSame(3, UTF8::strrpos('한국어', ''));
        }
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

        if (UTF8::mbstring_loaded()) { // only with "mbstring"
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
            UTF8::mbstring_loaded()
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
        if (UTF8::mbstring_loaded()) { // only with "mbstring"
            static::assertSame('iñtërnâtiôn?àlizætiøn', UTF8::strtolower("Iñtërnâtiôn\xE9àlizætiøn"));
            static::assertSame('iñtërnâtiôn?àlizætiøn', UTF8::strtolower("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', false));
        }

        static::assertSame('iñtërnâtiônàlizætiøn', UTF8::strtolower("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', true));

        // ---

        UTF8::checkForSupport();
        $supportNull = UTF8::getSupportInfo('foo');
        static::assertNull($supportNull);

        $support = UTF8::getSupportInfo();
        static::assertTrue(\is_array($support));

        // language === "tr"
        if (
            UTF8::intl_loaded()
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
            UTF8::mbstring_loaded()
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

        if (UTF8::mbstring_loaded()) { // only with "mbstring"
            static::assertSame('IÑTËRNÂTIÔN?ÀLIZÆTIØN', UTF8::strtoupper("Iñtërnâtiôn\xE9àlizætiøn"));
            static::assertSame('IÑTËRNÂTIÔN?ÀLIZÆTIØN', UTF8::strtoupper("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', false));
        }

        static::assertSame('IÑTËRNÂTIÔNÀLIZÆTIØN', UTF8::strtoupper("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', true));

        // ---

        UTF8::checkForSupport();
        $support = UTF8::getSupportInfo();

        // language === "tr"
        if (
            UTF8::intl_loaded()
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
        static::assertSame('Hello world Hello●◎', UTF8::strtr('Hello world ○●◎', '○', ['Hello']));
        static::assertSame('Hello world Hello●◎', UTF8::strtr('Hello world ○●◎', ['○'], ['Hello']));

        // specials
        static::assertSame('Hello world Hel', UTF8::strtr('Hello world ○●◎', '○●◎', 'Hello'));
        static::assertSame('Hello world Hello●◎', UTF8::strtr('Hello world ○●◎', '○●◎', ['Hello']));
        static::assertSame('Hello world Hello', UTF8::strtr('Hello world ○●◎', ['○●◎'], ['Hello']));
        static::assertSame('Hello world HelloHelloHello', UTF8::strtr('Hello world ○●◎', ['○', '●', '◎'], ['Hello', 'Hello', 'Hello']));
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

        if (UTF8::mbstring_loaded()) { // only with "mbstring"
            static::assertSame(21, UTF8::strwidth("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', false));
        }

        static::assertSame(20, UTF8::strwidth("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', true));

        if (UTF8::mbstring_loaded()) { // only with "mbstring"
            static::assertSame(20, UTF8::strlen("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', false));
        }

        static::assertSame(20, UTF8::strlen("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', true));

        // ISO

        if (UTF8::getSupportInfo('mbstring') === true) { // only with "mbstring"
            static::assertSame(28, UTF8::strlen("Iñtërnâtiôn\xE9àlizætiøn", 'ISO', false));
            static::assertSame(27, UTF8::strlen("Iñtërnâtiôn\xE9àlizætiøn", 'ISO', true));
        }
    }

    public function testEmpty()
    {
        static::assertTrue(UTF8::is_empty(''));
        static::assertTrue(UTF8::is_empty('0'));
        static::assertTrue(UTF8::is_empty([]));
        static::assertTrue(UTF8::is_empty(null));
        static::assertTrue(UTF8::is_empty(0));
        static::assertTrue(UTF8::is_empty(0000));
        static::assertTrue(UTF8::is_empty(0.0000));

        static::assertFalse(UTF8::is_empty('0000'));
        static::assertFalse(UTF8::is_empty(0.0001));
        static::assertFalse(UTF8::is_empty(-0.0001));
        static::assertFalse(UTF8::is_empty([null]));
        static::assertFalse(UTF8::is_empty([0]));
        static::assertFalse(UTF8::is_empty([0.0000]));
        static::assertFalse(UTF8::is_empty([1]));
        static::assertFalse(UTF8::is_empty(-1));
        static::assertFalse(UTF8::is_empty(1));
        static::assertFalse(UTF8::is_empty('1'));
    }

    public function testEncodeMimeheader()
    {
        if (Bootup::is_php('7.1')) {
            $text = UTF8::encode_mimeheader('💻 Issue 192 - Machine learning library for php.');
            static::assertSame(': =?UTF-8?Q?=F0=9F=92=BB=20Issue=20192=20-=20Machine=20learning=20library?=' . "\r\n" . ' =?UTF-8?Q?=20for=20php.?=', $text);
            static::assertSame(': 💻 Issue 192 - Machine learning library for php.', UTF8::decode_mimeheader($text));

            $text = UTF8::encode_mimeheader('Keld Jørn Simonsen <keld@example.com>');
            static::assertSame(': =?UTF-8?Q?Keld=20J=C3=B8rn=20Simonsen=20<keld@example.com>?=', $text);
            static::assertSame(': Keld Jørn Simonsen <keld@example.com>', UTF8::decode_mimeheader($text));

            $text = UTF8::encode_mimeheader('Keld Jørn Simonsen <keld@example.com>', 'UTF-8', 'ISO-8859-1');
            static::assertSame(': =?ISO-8859-1?Q?Keld=20J=F8rn=20Simonsen=20<keld@example.com>?=', $text);
            static::assertSame(': Keld Jørn Simonsen <keld@example.com>', UTF8::utf8_encode(UTF8::decode_mimeheader($text, 'ISO-8859-1')));
        } else {
            $text = UTF8::encode_mimeheader('💻 Issue 192 - Machine learning library for php.');
            static::assertSame(': =?UTF-8?Q?=F0=9F=92=BB=20Issue=20192=20-=20Mac?==?UTF-8?Q?hine?=' . "\r\n" . ' =?UTF-8?Q?=20learning=20library=20for?==?UTF-8?Q?=20php.?=', $text);
            static::assertSame(': 💻 Issue 192 - Machine learning library for php.', UTF8::decode_mimeheader($text));

            $text = UTF8::encode_mimeheader('Keld Jørn Simonsen <keld@example.com>');
            static::assertSame(': =?UTF-8?Q?Keld=20J=C3=B8rn=20Simonsen=20?==?UTF-8?Q?<keld@?=' . "\r\n" . ' =?UTF-8?Q?example.com>?=', $text);
            static::assertSame(': Keld Jørn Simonsen <keld@example.com>', UTF8::decode_mimeheader($text));

            $text = UTF8::encode_mimeheader('Keld Jørn Simonsen <keld@example.com>', 'UTF-8', 'ISO-8859-1');
            static::assertSame(': =?ISO-8859-1?Q?Keld=20J=F8rn=20Simonsen=20?==?ISO-8859-1?Q?<kel?=' . "\r\n" . ' =?ISO-8859-1?Q?d@example.com>?=', $text);
            static::assertSame(': Keld Jørn Simonsen <keld@example.com>', UTF8::utf8_encode(UTF8::decode_mimeheader($text, 'ISO-8859-1')));
        }
    }

    public function testDecodeMimeheader()
    {
        $text = '=?ISO-8859-1?Q?Keld_J=F8rn_Simonsen?= <keld@example.com>';
        static::assertSame('Keld Jørn Simonsen <keld@example.com>', UTF8::utf8_encode(UTF8::decode_mimeheader($text, 'ISO-8859-1')));

        $subject = 'Subject: =?UTF-8?B?UHLDvGZ1bmcgUHLDvGZ1bmc=?=';
        static::assertSame('Subject: Prüfung Prüfung', UTF8::decode_mimeheader($subject, 'UTF-8'));

        $subject_utf8 = 'Subject: =?UTF-8?Q?=F0=9F=92=BB_Issue_192_-_Machine_learning_library_for?=
 =?UTF-8?Q?_php.?=';
        static::assertSame('Subject: 💻 Issue 192 - Machine learning library for php.', UTF8::decode_mimeheader($subject_utf8));
    }

    public function testSubstrInByte()
    {
        static::assertSame('23', UTF8::substr_in_byte(1234, 1, 2));
        static::assertSame('bc', UTF8::substr_in_byte('abcde', 1, 2));
        static::assertSame('de', UTF8::substr_in_byte('abcde', -2, 2));
        static::assertSame('bc', UTF8::substr_in_byte('abcde', 1, 2));
        static::assertSame('bc', UTF8::substr_in_byte('abcde', 1, 2));
        static::assertSame('bc', UTF8::substr_in_byte('abcde', 1, 2));
        static::assertSame('bcde', UTF8::substr_in_byte('abcde', 1, null));
        static::assertSame('bcd', UTF8::substr_in_byte('abcde', 1, 3));
        static::assertSame('bc', UTF8::substr_in_byte('abcde', 1, 2));

        // ... no support for UTF-8
    }

    public function testSubstr()
    {
        static::assertSame('23', \substr(1234, 1, 2));
        static::assertSame('bc', \substr('abcde', 1, 2));
        static::assertSame('de', \substr('abcde', -2, 2));
        static::assertSame('bc', \substr('abcde', 1, 2));
        static::assertSame('bc', \substr('abcde', 1, 2));
        static::assertSame('bcd', \substr('abcde', 1, 3));
        static::assertSame('bc', \substr('abcde', 1, 2));

        static::assertSame('23', UTF8::substr(1234, 1, 2));
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

        static::assertSame(0, \substr_compare(12345, 23, 1, 2));
        static::assertSame(0, UTF8::substr_compare(12345, 23, 1, 2));

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

        static::assertSame(\substr_count('', ' '), UTF8::substr_count('', ' '));
        static::assertSame(\substr_count(' ', ' '), UTF8::substr_count(' ', ' '));
        static::assertSame(\substr_count('DJ', ' '), UTF8::substr_count('DJ', ' '));
        static::assertSame(\substr_count('', 'Σ'), UTF8::substr_count('', 'Σ'));
        static::assertSame(\substr_count(' ', 'Σ'), UTF8::substr_count(' ', 'Σ'));
        static::assertSame(\substr_count('DJ', ' '), UTF8::substr_count('DJ', ' '));
        static::assertSame(\substr_count('', 'Σ'), UTF8::substr_count('', 'Σ'));
        static::assertSame(\substr_count(' ', 'Σ'), UTF8::substr_count(' ', 'Σ'));

        // ---

        if (!\voku\helper\Bootup::is_php('8.0')) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertFalse(@\substr_count('', ''));
            static::assertFalse(UTF8::substr_count('', ''));
        }

        if (!\voku\helper\Bootup::is_php('8.0')) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertFalse(@\substr_count('', '', 1));
            static::assertFalse(UTF8::substr_count('', '', 1));
        }

        if (!\voku\helper\Bootup::is_php('8.0')) {
            if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                static::assertNull(@\substr_count('', '', 1, 1));
            } else {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                static::assertFalse(@\substr_count('', '', 1, 1));
            }
        }

        if (!\voku\helper\Bootup::is_php('8.0')) {
            static::assertFalse(UTF8::substr_count('', '', 1, 1));
        }

        if (!\voku\helper\Bootup::is_php('8.0')) {
            if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                static::assertNull(@\substr_count('', 'test', 1, 1));
            } else {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                static::assertFalse(@\substr_count('', 'test', 1, 1));
            }
        }

        static::assertSame(0, UTF8::substr_count('', 'test', 1, 1));
        static::assertSame(0, UTF8::substr_count('  ', 'test', 1, 1));
        static::assertSame(0, \substr_count('  ', 'test', 1, 1));

        if (!\voku\helper\Bootup::is_php('8.0')) {
            if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                static::assertNull(@\substr_count('test', '', 1, 1));
            } else {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                static::assertFalse(@\substr_count('test', '', 1, 1));
            }
        }

        static::assertFalse(UTF8::substr_count('test', '', 1, 1));

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            static::assertNull(\substr_count('test', 'test', 1, 1));
        } else {
            static::assertSame(0, \substr_count('test', 'test', 1, 1));
        }

        static::assertSame(0, UTF8::substr_count('test', 'test', 1, 1));

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            static::assertNull(\substr_count(12345, 23, 1, 2));
        } else {
            static::assertSame(1, \substr_count(12345, 23, 1, 2));
        }

        static::assertSame(1, UTF8::substr_count(12345, 23, 1, 2));

        static::assertSame(2, \substr_count('abcdebc', 'bc'));
        static::assertSame(2, UTF8::substr_count('abcdebc', 'bc'));

        if (Bootup::is_php('7.1')) {
            if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
                static::assertNull(\substr_count('abcde', 'de', -2, 2));
            } else {
                static::assertSame(1, \substr_count('abcde', 'de', -2, 2));
            }
        } else {
            if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                static::assertNull(@\substr_count('abcde', 'de', -2, 2));
            } else {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                static::assertFalse(@\substr_count('abcde', 'de', -2, 2));
            }
        }

        static::assertSame(1, UTF8::substr_count('abcde', 'de', -2, 2));

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertNull(@\substr_count('abcde', 'bcg', 1, 2));
        } else {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertSame(0, @\substr_count('abcde', 'bcg', 1, 2));
        }

        static::assertSame(0, UTF8::substr_count('abcde', 'bcg', 1, 2));

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertNull(@\substr_count('abcde', 'BC', 1, 2));
        } else {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertSame(0, @\substr_count('abcde', 'BC', 1, 2));
        }

        static::assertSame(0, UTF8::substr_count('abcde', 'BC', 1, 2));

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertNull(@\substr_count('abcde', 'bc', 1, 3));
        } else {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertSame(1, @\substr_count('abcde', 'bc', 1, 3));
        }

        static::assertSame(1, UTF8::substr_count('abcde', 'bc', 1, 3));

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertNull(@\substr_count('abcde', 'cd', 1, 2));
        } else {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertSame(0, @\substr_count('abcde', 'cd', 1, 2));
        }

        static::assertSame(0, UTF8::substr_count('abcde', 'cd', 1, 2));

        // UTF-8 tests

        static::assertSame(0, UTF8::substr_count('', '文空'));
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
            ''      => 'ΚόσμεMiddleEnd',
            ' '     => 'ΚόσμεMiddleEnd',
            false   => 'ΚόσμεMiddleEnd',
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
            ''      => 'BeginMiddleΚόσμε',
            ' '     => 'BeginMiddleΚόσμε',
            false   => 'BeginMiddleΚόσμε',
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
            ''      => 'ΚόσμεMiddleEnd',
            ' '     => 'ΚόσμεMiddleEnd',
            false   => 'ΚόσμεMiddleEnd',
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
            ''      => 'BeginMiddleΚόσμε',
            ' '     => 'BeginMiddleΚόσμε',
            false   => 'BeginMiddleΚόσμε',
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
        /** @noinspection SuspiciousArrayElementInspection */
        $tests = [
            1                                      => '1',
            -1                                     => '-1',
            ' '                                    => ' ',
            ''                                     => '',
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
            static::assertSame($after, UTF8::to_utf8(UTF8::to_iso8859($before)));
        }

        static::assertSame($tests, UTF8::to_utf8(UTF8::to_iso8859($tests)));
    }

    private function reactivateNativeUtf8Support()
    {
        if ($this->oldSupportArray === null) {
            return;
        }

        $refProperty = (new \ReflectionObject(new UTF8()))->getProperty('SUPPORT');
        $refProperty->setAccessible(true);

        $refProperty->setValue(null, $this->oldSupportArray);
    }

    private function disableNativeUtf8Support()
    {
        $refProperty = (new \ReflectionObject(new UTF8()))->getProperty('SUPPORT');
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
            'mbstring_internal_encoding'        => 'UTF-8',
            'iconv'                             => false,
            'intl'                              => false,
            'intl__transliterator_list_ids'     => [],
            'intlChar'                          => false,
            'pcre_utf8'                         => false,
            'ctype'                             => true,
            'finfo'                             => true,
            'json'                              => true,
            'symfony_polyfill_used'             => true,
        ];
        $refProperty->setValue(null, $testArray);
    }
}
