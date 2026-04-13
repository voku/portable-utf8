<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\Bootup;
use voku\helper\UTF8;

/**
 * @internal
 */
final class Utf8GlobalPart1Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $oldSupportArray;

    /**
     * helper-function for test -> "testCombineSomeUtf8Functions()"
     *
     * @param $comment
     *
     * @return string
     */
    public function cleanString($comment): string
    {
        foreach (['fuck', 'foo', 'bar'] as $value) {
            $value = UTF8::trim($value);

            if (UTF8::stripos($comment, $value) !== false) {
                $comment = UTF8::str_ireplace($value, '*****', $comment);
            }
        }

        $comment = UTF8::trim(\strip_tags($comment));

        return (string) $comment;
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

    public function stripWhitespaceProvider(): \Iterator
    {
        yield ['foobar', '  foo   bar  '];
        yield ['teststring', 'test string'];
        yield ['Οσυγγραφέας', '   Ο     συγγραφέας  '];
        yield ['123', ' 123 '];
        yield ['', ' ', 'UTF-8'];
        // no-break space (U+00A0)
        yield ['', '           ', 'UTF-8'];
        // spaces U+2000 to U+200A
        yield ['', ' ', 'UTF-8'];
        // narrow no-break space (U+202F)
        yield ['', ' ', 'UTF-8'];
        // medium mathematical space (U+205F)
        yield ['', '　', 'UTF-8'];
        // ideographic space (U+3000)
        yield ['123', '  1  2  3　　', 'UTF-8'];
        yield ['', ' '];
        yield ['', ''];
    }

    public function testAccess()
    {
        $testArray = [
            ' -1'       => [-1 => ''],
            ' '         => [1 => ''],
            '中文空白'      => [2 => '空'],
            '中文空白-test' => [3 => '白'],
            'fòô'       => [1 => 'ò'],
        ];

        foreach ($testArray as $actualString => $testDataArray) {
            foreach ($testDataArray as $stringPos => $expectedString) {
                static::assertSame($expectedString, UTF8::access($actualString, $stringPos), 'tested: ' . $actualString);
            }
        }
    }

    public function testCallback()
    {
        $actual = UTF8::callback(
            [
                UTF8::class,
                'strtolower',
            ],
            'Κόσμε-ÖÄÜ'
        );
        $expected = [
            'κ',
            'ό',
            'σ',
            'μ',
            'ε',
            '-',
            'ö',
            'ä',
            'ü',
        ];
        static::assertSame($expected, $actual);
    }

    public function testChangeKeyCase()
    {
        // upper

        $array = [
            'foo'   => 'a',
            1       => 'b',
            0       => 'c',
            'Foo'   => 'd',
            'FOO'   => 'e',
            'ΣΣΣ'   => 'f',
            'Κόσμε' => 'g',
        ];

        $result = UTF8::array_change_key_case($array, \CASE_UPPER);

        $expected = [
            'FOO'   => 'e',
            1       => 'b',
            0       => 'c',
            'ΣΣΣ'   => 'f',
            'ΚΌΣΜΕ' => 'g',
        ];

        static::assertSame($expected, $result);

        // lower

        $array = [
            'foo'    => 'a',
            1        => 'b',
            0        => 'c',
            'Foo'    => 'd',
            'FOO'    => 'e',
            'ΣΣΣ'    => 'f',
            'Κόσμε'  => 'g',
            'test-ß' => 'h',
            'TEST-ẞ' => 'i',
        ];

        $result = UTF8::array_change_key_case($array, \CASE_LOWER);

        if (Bootup::is_php('8.3')) {
            $expected = [
                'foo'    => 'e',
                1        => 'b',
                0        => 'c',
                'σσς'    => 'f',
                'κόσμε'  => 'g',
                'test-ß' => 'i',
            ];
        } else {
            $expected = [
                'foo'    => 'e',
                1        => 'b',
                0        => 'c',
                'σσσ'    => 'f',
                'κόσμε'  => 'g',
                'test-ß' => 'i',
            ];
        }

        static::assertSame($expected, $result);
    }

    public function testCharOtherEncoding()
    {
        $testArray = [
            ''     => null,
            '39'   => '\'',
            '40'   => '(',
            '41'   => ')',
            '42'   => '*',
            '160'  => \html_entity_decode('&nbsp;'),
            0x666  => '٦',
            0x165  => 'ť',
            0x8469 => '葩',
            0x2603 => '☃',
        ];

        foreach ($testArray as $before => $after) {
            static::assertSame($after, UTF8::chr($before, ''), 'tested: ' . $before);
        }
    }

    public function testChar()
    {
        $testArray = [
            '39'   => '\'',
            '40'   => '(',
            '41'   => ')',
            '42'   => '*',
            '160'  => \html_entity_decode('&nbsp;'),
            0x666  => '٦',
            0x165  => 'ť',
            0x8469 => '葩',
            0x2603 => '☃',
        ];

        foreach ($testArray as $before => $after) {
            static::assertSame($after, UTF8::chr($before), 'tested: ' . $before);
        }

        for ($i = 0; $i < 200; ++$i) { // keep this loop for simple performance tests
            if ($i === 0) {
                $this->disableNativeUtf8Support();
            } elseif ($i > 0) {
                $this->reactivateNativeUtf8Support();
            }

            foreach ($testArray as $before => $after) {
                static::assertSame($after, UTF8::chr(UTF8::ord(UTF8::chr($before))), 'tested: ' . $before);
            }
        }

        // -- with encoding

        static::assertSame(97, UTF8::ord('a', 'ISO'));
        static::assertSame('a', UTF8::chr(97, 'ISO'));

        // --

        $testArrayFail = [
            null  => null, // fail
            ''    => null, // fail
            'foo' => null, // fail
            'fòô' => null, // fail
        ];

        foreach ($testArrayFail as $before => $after) {
            static::assertSame($after, UTF8::chr($before), 'tested: ' . $before);
        }
    }

    public function testChrSizeList()
    {
        $testArray = [
            "中文空白\xF0\x90\x8C\xBC" => [
                3,
                3,
                3,
                3,
                4,
            ],
            'öäü' => [
                2,
                2,
                2,
            ],
            'abc' => [
                1,
                1,
                1,
            ],
            ''          => [],
            '中文空白-test' => [
                3,
                3,
                3,
                3,
                1,
                1,
                1,
                1,
                1,
            ],
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::chr_size_list($actual));
        }
    }

    public function testChrToDecimal()
    {
        $tests = [
            '~' => 0x7e,
            '§' => 0xa7,
            'ሇ' => 0x1207,
        ];

        for ($i = 0; $i < 2; ++$i) { // keep this loop for simple performance tests
            foreach ($tests as $before => $after) {
                static::assertSame($after, UTF8::chr_to_decimal($before));
                static::assertSame($after, UTF8::chr_to_decimal(UTF8::decimal_to_chr(UTF8::chr_to_decimal($before))));
            }
        }
    }

    public function testChrToHex()
    {
        $tests = [
            ''  => 'U+0000',
            ' ' => 'U+0020',
            0   => 'U+0030',
            'a' => 'U+0061',
            'ä' => 'U+00e4',
            'ό' => 'U+1f79',
            '❤' => 'U+2764',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::chr_to_hex(UTF8::hex_to_chr(UTF8::chr_to_hex($before))), 'tested: ' . $before);
        }

        // ---

        static::assertSame('U+2764', UTF8::chr_to_hex('❤'));
        static::assertSame('U+00a7', UTF8::chr_to_hex('§'));

        // ---

        static::assertSame('U+0000', UTF8::chr_to_hex(UTF8::hex_to_chr(UTF8::chr_to_hex(''))));
    }

    public function testChunkSplit()
    {
        $result = UTF8::chunk_split('ABC-ÖÄÜ-中文空白-κόσμε', 3);
        $expected = "ABC\r\n-ÖÄ\r\nÜ-中\r\n文空白\r\n-κό\r\nσμε";

        static::assertSame($expected, $result);

        // ---

        $result = UTF8::chunk_split('', 3);

        static::assertSame('', $result);

        // ---

        $result = UTF8::chunk_split('ABC-ÖÄÜ-中文空白-κόσμε', 0);

        static::assertSame('', $result);
    }

    public function testNormalizeLineEnding()
    {
        $resultTmp = UTF8::chunk_split("\n\r" . 'ABC-ÖÄÜ-中文空白-κόσμε' . "\n", 3);
        $expected = "\n\nA\nBC-\nÖÄÜ\n-中文\n空白-\nκόσ\nμε\n";

        $result = UTF8::normalize_line_ending($resultTmp);
        static::assertSame($expected, $result);
    }

    public function testClean()
    {
        $examples = [
            // Valid defaults
            ''   => ['' => ''],
            ' '  => [' ' => ' '],
            '1a' => ['1a' => '1a'],
            '2a' => ['2a' => '2a'],
            '+1' => ['+1' => '+1'],
            // Valid UTF-8
            '纳达尔绝境下大反击拒绝冷门逆转晋级中网四强' => ['纳达尔绝境下大反击拒绝冷门逆转晋级中网四强' => '纳达尔绝境下大反击拒绝冷门逆转晋级中网四强'],
            'κόσμε'                 => ['κόσμε' => 'κόσμε'],
            '中'                     => ['中' => '中'],
            '«foobar»'              => ['«foobar»' => '«foobar»'],
            // Valid UTF-8 + UTF-8 NO-BREAK SPACE
            "κόσμε\xc2\xa0" => ["κόσμε\xc2\xa0" => "κόσμε\xc2\xa0"],
            // Valid UTF-8 + Invalid Chars
            "κόσμε\xa0\xa1-öäü" => ['κόσμε-öäü' => 'κόσμε-öäü'],
            // Valid UTF-8 + ISO-Errors
            'DÃ¼sseldorf' => ['DÃ¼sseldorf' => 'DÃ¼sseldorf'],
            // Valid invisible char
            '<x%0Conxxx=1' => ['<xonxxx=1' => '<x%0Conxxx=1'],
            // Valid ASCII
            'a' => ['a' => 'a'],
            // Valid emoji (non-UTF-8)
            '😃'                                                          => ['😃' => '😃'],
            '🐵 🙈 🙉 🙊 | ❤️ 💔 💌 💕 💞 💓 💗 💖 💘 💝 💟 💜 💛 💚 💙 | 🚾 🆒 🆓 🆕 🆖 🆗 🆙 🏧' => ['🐵 🙈 🙉 🙊 | ❤️ 💔 💌 💕 💞 💓 💗 💖 💘 💝 💟 💜 💛 💚 💙 | 🚾 🆒 🆓 🆕 🆖 🆗 🆙 🏧' => '🐵 🙈 🙉 🙊 | ❤️ 💔 💌 💕 💞 💓 💗 💖 💘 💝 💟 💜 💛 💚 💙 | 🚾 🆒 🆓 🆕 🆖 🆗 🆙 🏧'],
            // Valid ASCII + Invalid Chars
            "a\xa0\xa1-öäü" => ['a-öäü' => 'a-öäü'],
            // Valid 2 Octet Sequence
            "\xc3\xb1" => ['ñ' => 'ñ'],
            // Invalid 2 Octet Sequence
            "\xc3\x28" => ['�(' => '('],
            // Invalid
            "\x00"   => ['�' => ''],
            "a\xDFb" => ['ab' => 'ab'],
            // Invalid Sequence Identifier
            "\xa0\xa1" => ['��' => ''],
            // Valid 3 Octet Sequence
            "\xc2\x80"     => ['' => ''],
            "\xe2\x82\xa1" => ['₡' => '₡'],
            // Invalid 3 Octet Sequence (in 2nd Octet)
            "\xe2\x28\xa1" => ['�(�' => '('],
            // Invalid 3 Octet Sequence (in 3rd Octet)
            "\xe2\x82\x28" => ['�(' => '('],
            // Valid 4 Octet Sequence
            "\xf0\x90\x8c\xbc" => ['𐌼' => '𐌼'],
            // Invalid 4 Octet Sequence (in 2nd Invalid 4 Octet Sequence (in 2ndOctet)
            "\xf0\x28\x8c\xbc" => ['�(��' => '('],
            // Invalid 4 Octet Sequence (in 3rd Octet)
            "\xf0\x90\x28\xbc" => ['�(�' => '('],
            // Invalid 4 Octet Sequence (in 4th Octet)
            "\xf0\x28\x8c\x28" => ['�(�(' => '(('],
            // Valid 5 Octet Sequence (but not Unicode!)
            "\xf8\xa1\xa1\xa1\xa1" => ['�' => ''],
            // Valid 6 Octet Sequence (but not Unicode!)
            "\xfc\xa1\xa1\xa1\xa1\xa1" => ['�' => ''],
            // Valid 6 Octet Sequence (but not Unicode!) + UTF-8 EN SPACE
            "\xfc\xa1\xa1\xa1\xa1\xa1\xe2\x80\x82" => ['�' => ' '],
        ];

        $counter = 0;
        foreach ($examples as $testString => $testResults) {
            foreach ($testResults as $before => $after) {
                static::assertSame($after, UTF8::clean($testString, true), 'tested: ' . $counter);
            }
            ++$counter;
        }
    }

    public function testCleanup()
    {
        $examples = [
            // Valid defaults
            ''   => ['' => ''],
            ' '  => [' ' => ' '],
            null => [null => ''],
            1    => [1 => '1'],
            '2'  => ['2' => '2'],
            '+1' => ['+1' => '+1'],
            // Valid UTF-8
            '纳达尔绝境下大反击拒绝冷门逆转晋级中网四强' => ['纳达尔绝境下大反击拒绝冷门逆转晋级中网四强' => '纳达尔绝境下大反击拒绝冷门逆转晋级中网四强'],
            'κόσμε'                 => ['κόσμε' => 'κόσμε'],
            '中'                     => ['中' => '中'],
            '«foobar»'              => ['«foobar»' => '«foobar»'],
            // Valid UTF-8 + UTF-8 NO-BREAK SPACE
            "κόσμε\xc2\xa0" => ["κόσμε\xc2\xa0" => "κόσμε\xc2\xa0"],
            // Valid UTF-8 + Invalid Chars
            "κόσμε\xa0\xa1-öäü" => ['κόσμε-öäü' => 'κόσμε-öäü'],
            // Valid UTF-8 + ISO-Errors
            'DÃ¼sseldorf' => ['Düsseldorf' => 'Düsseldorf'],
            // Valid ASCII
            'a' => ['a' => 'a'],
            // Valid emoji (non-UTF-8)
            '😃'                                                          => ['😃' => '😃'],
            '🐵 🙈 🙉 🙊 | ❤️ 💔 💌 💕 💞 💓 💗 💖 💘 💝 💟 💜 💛 💚 💙 | 🚾 🆒 🆓 🆕 🆖 🆗 🆙 🏧' => ['🐵 🙈 🙉 🙊 | ❤️ 💔 💌 💕 💞 💓 💗 💖 💘 💝 💟 💜 💛 💚 💙 | 🚾 🆒 🆓 🆕 🆖 🆗 🆙 🏧' => '🐵 🙈 🙉 🙊 | ❤️ 💔 💌 💕 💞 💓 💗 💖 💘 💝 💟 💜 💛 💚 💙 | 🚾 🆒 🆓 🆕 🆖 🆗 🆙 🏧'],
            // Valid ASCII + Invalid Chars
            "a\xa0\xa1-öäü" => ['a-öäü' => 'a-öäü'],
            // Valid 2 Octet Sequence
            "\xc3\xb1" => ['ñ' => 'ñ'],
            // Invalid 2 Octet Sequence
            "\xc3\x28" => ['�(' => '('],
            // Invalid
            "\x00" => ['�' => ''],
            // Invalid Sequence Identifier
            "\xa0\xa1" => ['��' => ''],
            // Valid 3 Octet Sequence
            "\xe2\x82\xa1" => ['₡' => '₡'],
            // Invalid 3 Octet Sequence (in 2nd Octet)
            "\xe2\x28\xa1" => ['�(�' => '('],
            // Invalid 3 Octet Sequence (in 3rd Octet)
            "\xe2\x82\x28" => ['�(' => '('],
            // Valid 4 Octet Sequence
            "\xf0\x90\x8c\xbc" => ['𐌼' => '𐌼'],
            // Invalid 4 Octet Sequence (in 2nd Invalid 4 Octet Sequence (in 2ndOctet)
            "\xf0\x28\x8c\xbc" => ['�(��' => '('],
            // Invalid 4 Octet Sequence (in 3rd Octet)
            "\xf0\x90\x28\xbc" => ['�(�' => '('],
            // Invalid 4 Octet Sequence (in 4th Octet)
            "\xf0\x28\x8c\x28" => ['�(�(' => '(('],
            // Valid 5 Octet Sequence (but not Unicode!)
            "\xf8\xa1\xa1\xa1\xa1" => ['�' => ''],
            // Valid 6 Octet Sequence (but not Unicode!)
            "\xfc\xa1\xa1\xa1\xa1\xa1" => ['�' => ''],
            // Valid 6 Octet Sequence (but not Unicode!) + UTF-8 EN SPACE
            "\xfc\xa1\xa1\xa1\xa1\xa1\xe2\x80\x82" => ['�' => ' '],
        ];

        $counter = 0;
        foreach ($examples as $testString => $testResults) {
            foreach ($testResults as $before => $after) {
                static::assertSame($after, UTF8::cleanup($testString), 'tested: ' . $counter);
            }
            ++$counter;
        }
    }

    public function testCleanup2()
    {
        $examples = [
            // Valid defaults
            ''   => ['' => ''],
            ' '  => [' ' => ' '],
            null => [null => ''],
            1    => [1 => '1'],
            '2'  => ['2' => '2'],
            '+1' => ['+1' => '+1'],
            // Valid UTF-8 + UTF-8 NO-BREAK SPACE
            "κόσμε\xc2\xa0" => ['κόσμε' . "\xc2\xa0" => 'κόσμε' . "\xc2\xa0"],
            // Valid UTF-8
            '中' => ['中' => '中'],
            // Valid UTF-8 + ISO-Error
            'DÃ¼sseldorf' => ['Düsseldorf' => 'Düsseldorf'],
            // Valid UTF-8 + Invalid Chars
            "κόσμε\xa0\xa1-öäü" => ['κόσμε-öäü' => 'κόσμε-öäü'],
            // Valid ASCII
            'a' => ['a' => 'a'],
            // Valid ASCII + Invalid Chars
            "a\xa0\xa1-öäü" => ['a-öäü' => 'a-öäü'],
            // Valid 2 Octet Sequence
            "\xc3\xb1" => ['ñ' => 'ñ'],
            // Invalid
            "\x00" => ['�' => ''],
            // Invalid 2 Octet Sequence
            "\xc3\x28" => ['�(' => '('],
            // Invalid Sequence Identifier
            "\xa0\xa1" => ['��' => ''],
            // Valid 3 Octet Sequence
            "\xe2\x82\xa1" => ['₡' => '₡'],
            // Invalid 3 Octet Sequence (in 2nd Octet)
            "\xe2\x28\xa1" => ['�(�' => '('],
            // Invalid 3 Octet Sequence (in 3rd Octet)
            "\xe2\x82\x28" => ['�(' => '('],
            // Valid 4 Octet Sequence
            "\xf0\x90\x8c\xbc" => ['𐌼' => '𐌼'],
            // Invalid 4 Octet Sequence (in 2nd Octet)
            "\xf0\x28\x8c\xbc" => ['�(��' => '('],
            // Invalid 4 Octet Sequence (in 3rd Octet)
            "\xf0\x90\x28\xbc" => ['�(�' => '('],
            // Invalid 4 Octet Sequence (in 4th Octet)
            " \xf0\x28\x8c\x28" => ['�(�(' => ' (('],
            // Valid 5 Octet Sequence (but not Unicode!)
            "\xf8\xa1\xa1\xa1\xa1" => ['�' => ''],
            // Valid 6 Octet Sequence (but not Unicode!) + UTF-8 EN SPACE
            "\xfc\xa1\xa1\xa1\xa1\xa1\xe2\x80\x82" => ['�' => ' '],
            // test for database-insert
            '
        <h1>«DÃ¼sseldorf» &ndash; &lt;Köln&gt;</h1>
        <br /><br />
        <!--suppress CheckDtdRefs -->
<p>
          &nbsp;�&foo;❤&nbsp;
        </p>
        ' => [
                '' => '
        <h1>«Düsseldorf» &ndash; &lt;Köln&gt;</h1>
        <br /><br />
        <!--suppress CheckDtdRefs -->
<p>
          &nbsp;&foo;❤&nbsp;
        </p>
        ',
            ],
        ];

        foreach ($examples as $testString => $testResults) {
            foreach ($testResults as $before => $after) {
                static::assertSame($after, UTF8::cleanup($testString));
            }
        }
    }

    public function testCodepoints()
    {
        $testArray = [
            "\xF0\x90\x8C\xBC---" => [
                0 => 66364,
                1 => 45,
                2 => 45,
                3 => 45,
            ],
            '中-abc' => [
                0 => 20013,
                1 => 45,
                2 => 97,
                3 => 98,
                4 => 99,
            ],
            '₧{abc}' => [
                0 => 8359,
                1 => 123,
                2 => 97,
                3 => 98,
                4 => 99,
                5 => 125,
            ],
            'κöñ' => [
                0 => 954,
                1 => 246,
                2 => 241,
            ],
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::codepoints($actual));
        }

        // --- U+xxxx format

        static::assertSame([0 => 'U+03ba', 1 => 'U+00f6', 2 => 'U+00f1'], UTF8::codepoints(['κ', 'ö', 'ñ'], true));
        static::assertSame([0 => 'U+03ba', 1 => 'U+00f6', 2 => 'U+00f1'], UTF8::codepoints('κöñ', true));
        static::assertSame(
            [0 => 'U+03ba', 1 => 'U+00f6', 2 => 'U+00f1'],
            UTF8::codepoints(
                [
                    'κ',
                    'ö',
                    'ñ',
                ],
                true
            )
        );
    }

    public function testCombineSomeUtf8Functions()
    {
        $testArray = [
            "<h1>test\n</h1>"               => 'test',
            "test\n\nöfuckäü"               => "test\n\nö*****äü",
            "<b>FUCK\n</b>"                 => '*****',
            "öäüfoo<strong>lall\n</strong>" => 'öäü*****lall',
            ' <b>lall</b>'                  => 'lall',
            "\n"                            => '',
            "<ul><li>test\n\n</li></ul>"    => 'test',
            "<blockquote>\n</blockquote>"   => '',
            '</br>'                         => '',
            ''                              => '',
            ' '                             => '',
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, $this->cleanString($testString));
        }
    }

    public function testCountChars()
    {
        $testArray = [
            'κaκbκc' => [
                'κ' => 3,
                'a' => 1,
                'b' => 1,
                'c' => 1,
            ],
            'cba' => [
                'c' => 1,
                'b' => 1,
                'a' => 1,
            ],
            'abcöäü' => [
                'a' => 1,
                'b' => 1,
                'c' => 1,
                'ö' => 1,
                'ä' => 1,
                'ü' => 1,
            ],
            '白白' => ['白' => 2],
            ''   => [],
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::count_chars($actual), 'error by ' . $actual);
        }

        // added invalid UTF-8
        $testArray['白' . "\xa0\xa1" . '白'] = ['白' => 2];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::count_chars($actual, true), 'error by ' . $actual);
        }
    }

    public function testDecimalToChr()
    {
        $tests = [
            0x7e   => '~',
            0xa7   => '§',
            0x1207 => 'ሇ',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::decimal_to_chr($before));
            static::assertSame($before, UTF8::chr_to_decimal(UTF8::decimal_to_chr($before)), 'tested: ' . $after);
            static::assertSame($after, UTF8::decimal_to_chr(UTF8::chr_to_decimal(UTF8::decimal_to_chr($before))));
        }
    }

    public function testEncode()
    {
        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-中文空白-  ',
            '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
            'öäü'            => 'öäü',
            ''               => '',
            'abc'            => 'abc',
            'Berbée'         => 'Berbée',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::encode('', $before, true), 'tested: ' . $before); // do nothing
        }

        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-中文空白-  ',
            '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
            'öäü'            => 'öäü',
            ''               => '',
            'abc'            => 'abc',
            'Berbée'         => 'Berbée',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::encode('UTF8', $before, true), 'tested: ' . $before); // UTF-8
        }

        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-????-  ',
            '      - ÖÄÜ- '  => '      - ???- ',
            'öäü'            => '???',
            ''               => '',
            'abc'            => 'abc',
            'Berbée'         => 'Berb?e',
        ];

        if (UTF8::mbstring_loaded()) { // only with "mbstring"
            foreach ($tests as $before => $after) {
                static::assertSame($after, UTF8::encode('CP367', $before, true), 'tested: ' . $before); // CP367
            }
        }

        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-????-  ',
            '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
            'öäü'            => 'öäü',
            ''               => '',
            'abc'            => 'abc',
            'Berbée'         => 'Berbée',
        ];

        // TODO: Is this is a bug in PHP 8.1 or not? :/
        if (\PHP_VERSION_ID >= 80100) {
            unset($tests['Berbée']);
        }

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::filter(UTF8::encode('ISo88591', $before, true)), 'tested: ' . $before); // ISO-8859-1
        }

        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-????-  ',
            '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
            'öäü'            => 'öäü',
            ''               => '',
            'abc'            => 'abc',
            'Berbée'         => 'Berbée',
            '15,99 €'        => '15,99 ?',
        ];

        // TODO: Is this is a bug in PHP 8.1 or not? :/
        if (\PHP_VERSION_ID >= 80100) {
            unset($tests['Berbée']);
        }

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::filter(UTF8::encode('IsO-8859-15', UTF8::encode('iso-8859-1', $before, true), true))); // ISO-8859-15
        }

        // TODO: Is this is a bug in PHP 8.1 or not? :/
        if (\PHP_VERSION_ID < 80100) {
            static::assertSame('éàa', UTF8::encode('UTF-8', UTF8::encode('ISO-8859-1', 'éàa', true), true));
        }

        // --- do not force the encoding ...

        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-中文空白-  ',
            '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
            'öäü'            => 'öäü',
            ''               => '',
            'abc'            => 'abc',
            'Berbée'         => 'Berbée',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::encode('', $before, false), 'tested: ' . $before); // do nothing
        }

        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-中文空白-  ',
            '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
            'öäü'            => 'öäü',
            ''               => '',
            'abc'            => 'abc',
            'Berbée'         => 'Berbée',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::encode('UTF8', $before, false), 'tested: ' . $before); // UTF-8
        }

        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-????-  ',
            '      - ÖÄÜ- '  => '      - ???- ',
            'öäü'            => '???',
            ''               => '',
            'abc'            => 'abc',
            'Berbée'         => 'Berb?e',
        ];

        if (UTF8::mbstring_loaded()) { // only with "mbstring"
            foreach ($tests as $before => $after) {
                static::assertSame($after, UTF8::encode('CP367', $before, false), 'tested: ' . $before); // CP367
            }
        }

        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-????-  ',
            '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
            'öäü'            => 'öäü',
            ''               => '',
            'abc'            => 'abc',
            'Berbée'         => 'Berbée',
        ];

        // TODO: Is this is a bug in PHP 8.1 or not? :/
        if (\PHP_VERSION_ID >= 80100) {
            unset($tests['Berbée']);
        }

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::filter(UTF8::encode('ISo88591', $before, false)), 'tested: ' . $before); // ISO-8859-1
        }

        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-????-  ',
            '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
            'öäü'            => 'öäü',
            ''               => '',
            'abc'            => 'abc',
            'Berbée'         => 'Berbée',
        ];

        // TODO: Is this is a bug in PHP 8.1 or not? :/
        if (\PHP_VERSION_ID >= 80100) {
            unset($tests['Berbée']);
        }

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::filter(UTF8::encode('IsO-8859-15', UTF8::encode('iso-8859-1', $before, false), false))); // ISO-8859-15
        }

        // TODO: Is this is a bug in PHP 8.1 or not? :/
        if (\PHP_VERSION_ID < 80100) {
            static::assertSame('éàa', UTF8::encode('UTF-8', UTF8::encode('ISO-8859-1', 'éàa', false), false));
        }

        // --- JSON

        static::assertSame('"\u00e9\u00e0a"', UTF8::encode('JSON', 'éàa'));

        static::assertSame('éàa', UTF8::encode('UTF-8', '"\u00e9\u00e0a"', false, 'JSON'));

        // --- BASE64

        static::assertSame('w6nDoGE=', UTF8::encode('BASE64', 'éàa'));

        static::assertSame('éàa', UTF8::encode('UTF-8', 'w6nDoGE=', false, 'BASE64'));

        // --- HTML

        static::assertSame('&#233;&#224;a', UTF8::encode('HTML', 'éàa'));

        static::assertSame('éàa', UTF8::encode('UTF-8', '&#233;&#224;a', false, 'HTML'));
    }

    public function testEncodeUtf8EncodeUtf8()
    {
        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-中文空白-  ',
            '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
            'öäü'            => 'öäü',
            ''               => '',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::encode('UTF-8', UTF8::encode('UTF-8', $before)));
        }
    }

    public function testEncodeUtf8Utf8Encode()
    {
        $tests = [
            '  -ABC-中文空白-  ' => '  -ABC-ä¸­æç©ºç½-  ',
            '      - ÖÄÜ- '  => '      - ÃÃÃ- ',
            'öäü'            => 'Ã¶Ã¤Ã¼',
            ''               => '',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::utf8_encode(UTF8::encode('UTF-8', $before)));
        }
    }

    public function testFilterFile()
    {
        $testArray = [
            "test-\xe9\x00\x0é大般若經.txt"      => 'test-.txt',
            'test-大般若經.txt'                  => 'test-.txt',
            'фото.jpg'                       => '.jpg',
            'Фото.jpg'                       => '.jpg',
            'öäü  - test'                    => 'test',
            'שדגשדג.png'                     => '.png',
            '—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–.jpg' => '.jpg',
            '000—©—©.txt'                    => '000.txt',
            ' '                              => '',
        ];

        foreach ($testArray as $before => $after) {
            static::assertSame($after, UTF8::to_filename($before, false));
        }

        // ---

        $testArray = [
            "test-\xe9\x00\x0é大般若經.txt"      => 'test-eDa-Ban-Ruo-Jing-.txt',
            'test-大般若經.txt'                  => 'test-Da-Ban-Ruo-Jing-.txt',
            'фото.jpg'                       => 'foto.jpg',
            'Фото.jpg'                       => 'Foto.jpg',
            'öäü  - test'                    => 'oau-test',
            'שדגשדג.png'                     => 'SHdgSHdg.png',
            '—©®±àáâãäåæÒÓÔÕÖ¼½¾§µçðþú–.jpg' => 'cr-aaaaaaaeOOOOO141234SSucdthu-.jpg',
            '000—©—©.txt'                    => '000-c-c.txt',
            ' '                              => '',
        ];

        foreach ($testArray as $before => $after) {
            static::assertSame($after, UTF8::to_filename($before, true));
        }
    }

    public function testFileGetContents()
    {
        if (\method_exists(__CLASS__, 'assertStringContainsString')) {
            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/sample-unicode-chart.txt');
            static::assertStringContainsString('M	𝐌	𝑀	𝑴	𝖬	𝗠	𝘔	𝙈	ℳ	𝓜	𝔐	𝕸	𝙼	𝕄', $testString);

            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/sample-html.txt');
            static::assertStringContainsString('վṩ鼦Ѷ鼦ַ鼦ٷվݡ', $testString);

            $testString = \file_get_contents(__DIR__ . '/fixtures/sample-html.txt');
            static::assertStringContainsString('վṩ鼦Ѷ鼦ַ鼦ٷվݡ', $testString);

            $testString = \file_get_contents(__DIR__ . '/fixtures/sample-html.txt');
            $testStringUtf8 = UTF8::clean($testString, true, true, true);
            static::assertStringContainsString('վṩ鼦Ѷ鼦ַ鼦ٷվݡ', $testStringUtf8);
            static::assertStringContainsString('<p>鼦</p>', $testStringUtf8);
            static::assertStringContainsString('<li><a href="/">鼦վͼ</a></li>', $testStringUtf8);
            static::assertStringContainsString('<B><a href="http://www.baidu.com/" >ٶ</a></B>', $testStringUtf8);

            // ---

            if (UTF8::mbstring_loaded()) { // only with "mbstring"
                static::assertTrue(UTF8::is_binary_file(__DIR__ . '/fixtures/utf-16-be.txt'));
                $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-16-be.txt');
                static::assertStringContainsString(
                    '<p>Today’s Internet users are not the same users who were online a decade ago. There are better connections.',
                    $testString
                );

                static::assertTrue(UTF8::is_binary_file(__DIR__ . '/fixtures/utf-16-le.txt'));
                $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-16-le.txt');
                static::assertStringContainsString(
                    '<p>Today’s Internet users are not the same users who were online a decade ago. There are better connections.',
                    $testString
                );
            }

            static::assertFalse(UTF8::is_binary_file(__DIR__ . '/fixtures/utf-8.txt'));
            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-8.txt');
            static::assertStringContainsString('Iñtërnâtiônàlizætiøn', $testString);

            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/latin.txt');
            static::assertStringContainsString('Iñtërnâtiônàlizætiøn', $testString);

            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/iso-8859-7.txt');
            static::assertStringContainsString('Iñtërnâtiônàlizætiøn', $testString);

            if (UTF8::mbstring_loaded()) { // only with "mbstring"
                $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-16-be.txt');
                static::assertStringContainsString(
                    '<p>Today’s Internet users are not the same users who were online a decade ago. There are better connections.',
                    $testString
                );

                $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-16-le.txt', false, null, 0);
                static::assertStringContainsString(
                    '<p>Today’s Internet users are not the same users who were online a decade ago. There are better connections.',
                    $testString
                );

                // text: with offset
                $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-16-le.txt', false, null, 5);
                static::assertStringContainsString('There are better connections.', $testString);
            }

            // text: with offset & max-length
            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-8.txt', false, null, 7, 11);
            static::assertStringContainsString('Iñtërnât', $testString);

            // text: with offset & max-length + timeout
            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/latin.txt', false, null, 7, 10, 15);
            static::assertStringContainsString('ñtërnâtiôn', $testString);

            // text: with timeout
            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/iso-8859-7.txt', false, null, 7, null, 10);
            static::assertStringContainsString('Iñtërnâtiônàlizætiøn', $testString);

            // text: with max-length + timeout
            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/iso-8859-7.txt', false, null, null, 10, 10);
            static::assertStringContainsString('Hírek', $testString);

            $context = \stream_context_create(
                [
                    'http' => [
                        'timeout' => 10,
                    ],
                ]
            );

            // text: with max-length + timeout
            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/iso-8859-7.txt', false, $context, null, 10, 10);
            static::assertStringContainsString('Hírek', $testString);

            // text: do not convert to utf-8 + timeout
            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/iso-8859-7.txt', false, $context, null, 10, 10, false);
            if (\method_exists(__CLASS__, 'assertMatchesRegularExpression')) {
                static::assertMatchesRegularExpression('#H.*rek#', $testString);
            } else {
                static::assertRegExp('#H.*rek#', $testString);
            }

            // text: do not convert to utf-8 + timeout
            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-8.txt', false, $context, null, 10, 10, false);
            static::assertStringContainsString('Hírek', $testString);
        } else {
            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/sample-unicode-chart.txt');
            static::assertContains('M	𝐌	𝑀	𝑴	𝖬	𝗠	𝘔	𝙈	ℳ	𝓜	𝔐	𝕸	𝙼	𝕄', $testString);

            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/sample-html.txt');
            static::assertContains('վṩ鼦Ѷ鼦ַ鼦ٷվݡ', $testString);

            $testString = \file_get_contents(__DIR__ . '/fixtures/sample-html.txt');
            static::assertContains('վṩ鼦Ѷ鼦ַ鼦ٷվݡ', $testString);

            $testString = \file_get_contents(__DIR__ . '/fixtures/sample-html.txt');
            $testStringUtf8 = UTF8::clean($testString, true, true, true);
            static::assertContains('վṩ鼦Ѷ鼦ַ鼦ٷվݡ', $testStringUtf8);
            static::assertContains('<p>鼦</p>', $testStringUtf8);
            static::assertContains('<li><a href="/">鼦վͼ</a></li>', $testStringUtf8);
            static::assertContains('<B><a href="http://www.baidu.com/" >ٶ</a></B>', $testStringUtf8);

            // ---

            if (UTF8::mbstring_loaded()) { // only with "mbstring"
                static::assertTrue(UTF8::is_binary_file(__DIR__ . '/fixtures/utf-16-be.txt'));
                $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-16-be.txt');
                static::assertContains(
                    '<p>Today’s Internet users are not the same users who were online a decade ago. There are better connections.',
                    $testString
                );

                static::assertTrue(UTF8::is_binary_file(__DIR__ . '/fixtures/utf-16-le.txt'));
                $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-16-le.txt');
                static::assertContains(
                    '<p>Today’s Internet users are not the same users who were online a decade ago. There are better connections.',
                    $testString
                );
            }

            static::assertFalse(UTF8::is_binary_file(__DIR__ . '/fixtures/utf-8.txt'));
            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-8.txt');
            static::assertContains('Iñtërnâtiônàlizætiøn', $testString);

            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/latin.txt');
            static::assertContains('Iñtërnâtiônàlizætiøn', $testString);

            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/iso-8859-7.txt');
            static::assertContains('Iñtërnâtiônàlizætiøn', $testString);

            if (UTF8::mbstring_loaded()) { // only with "mbstring"
                $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-16-be.txt');
                static::assertContains(
                    '<p>Today’s Internet users are not the same users who were online a decade ago. There are better connections.',
                    $testString
                );

                $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-16-le.txt', false, null, 0);
                static::assertContains(
                    '<p>Today’s Internet users are not the same users who were online a decade ago. There are better connections.',
                    $testString
                );

                // text: with offset
                $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-16-le.txt', false, null, 5);
                static::assertContains('There are better connections.', $testString);
            }

            // text: with offset & max-length
            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-8.txt', false, null, 7, 11);
            static::assertContains('Iñtërnât', $testString);

            // text: with offset & max-length + timeout
            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/latin.txt', false, null, 7, 10, 15);
            static::assertContains('ñtërnâtiôn', $testString);

            // text: with timeout
            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/iso-8859-7.txt', false, null, 7, null, 10);
            static::assertContains('Iñtërnâtiônàlizætiøn', $testString);

            // text: with max-length + timeout
            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/iso-8859-7.txt', false, null, null, 10, 10);
            static::assertContains('Hírek', $testString);

            $context = \stream_context_create(
                [
                    'http' => [
                        'timeout' => 10,
                    ],
                ]
            );

            // text: with max-length + timeout
            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/iso-8859-7.txt', false, $context, null, 10, 10);
            static::assertContains('Hírek', $testString);

            // text: do not convert to utf-8 + timeout
            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/iso-8859-7.txt', false, $context, null, 10, 10, false);
            static::assertRegExp('#H.*rek#', $testString);

            // text: do not convert to utf-8 + timeout
            $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-8.txt', false, $context, null, 10, 10, false);
            static::assertContains('Hírek', $testString);
        }
    }

    public function testFileGetContentsBinary()
    {
        $context = \stream_context_create(
            [
                'http' => [
                    'timeout' => 10,
                ],
            ]
        );

        // image: do not convert to utf-8 + timeout
        $image = UTF8::file_get_contents(__DIR__ . '/fixtures/image.png', false, $context, null, null, 10, false);
        static::assertTrue(UTF8::is_binary($image));

        // image: convert to utf-8 + timeout (ERROR)
        $image2 = UTF8::file_get_contents(__DIR__ . '/fixtures/image.png', false, $context, null, null, 10, true);
        static::assertTrue(UTF8::is_binary($image2));

        // image: do not convert to utf-8 + timeout
        $image = UTF8::file_get_contents(__DIR__ . '/fixtures/image_small.png', false, $context, null, null, 10, false);
        static::assertTrue(UTF8::is_binary($image));

        // image: convert to utf-8 + timeout (ERROR)
        $image2 = UTF8::file_get_contents(__DIR__ . '/fixtures/image_small.png', false, $context, null, null, 10, true);
        static::assertTrue(UTF8::is_binary($image2));

        // zip: do not convert to utf-8 + timeout
        $image = UTF8::file_get_contents(__DIR__ . '/fixtures/test.zip', false, $context, null, null, 10, false);
        static::assertTrue(UTF8::is_binary($image));

        // zip: convert to utf-8 + timeout (ERROR)
        $image2 = UTF8::file_get_contents(__DIR__ . '/fixtures/test.zip', false, $context, null, null, 10, true);
        static::assertTrue(UTF8::is_binary($image2));

        static::assertSame($image2, $image);
    }

    public function testFilter()
    {
        static::assertSame('é', UTF8::filter("\xE9"));

        // ---

        $c = 'à';
        $d = \Normalizer::normalize($c, \Normalizer::NFD);
        $a = [
            'n' => 4,
            'a' => "\xE9",
            'b' => \substr($d, 1),
            'c' => $c,
            'd' => $d,
            'e' => "\n\r\n\r",
        ];
        $a['f'] = (object) $a;
        $b = UTF8::filter($a);
        $b['f'] = (array) $a['f'];

        $expect = [
            'n' => 4,
            'a' => 'é',
            'b' => '◌' . \substr($d, 1),
            'c' => $c,
            'd' => $c,
            'e' => "\n\n\n",
        ];
        $expect['f'] = $expect;

        static::assertSame($expect, $b);

        // -----

        $result = UTF8::filter(["\xE9", 'à', 'a', "\xe2\x80\xa8"], \Normalizer::FORM_D);

        static::assertSame([0 => 'é', 1 => 'à', 2 => 'a', 3 => "\xe2\x80\xa8"], $result);
    }

    public function testFilterVar()
    {
        $options = [
            'options' => [
                'default' => -1,
                // value to return if the filter fails
                'min_range' => 90,
                'max_range' => 99,
            ],
        ];

        static::assertSame('  -ABC-中文空白-  ', UTF8::filter_var('  -ABC-中文空白-  ', \FILTER_DEFAULT));
        static::assertFalse(UTF8::filter_var('  -ABC-中文空白-  ', \FILTER_VALIDATE_URL));
        static::assertFalse(UTF8::filter_var('  -ABC-中文空白-  ', \FILTER_VALIDATE_EMAIL));
        static::assertSame(-1, UTF8::filter_var('中文空白 ', \FILTER_VALIDATE_INT, $options));
        static::assertSame(99, UTF8::filter_var(99, \FILTER_VALIDATE_INT, $options));
        static::assertSame(-1, UTF8::filter_var(100, \FILTER_VALIDATE_INT, $options));
    }

    public function testFilterVarArray()
    {
        $filters = [
            'name' => [
                'filter'  => \FILTER_CALLBACK,
                'options' => [UTF8::class, 'ucwords'],
            ],
            'age' => [
                'filter'  => \FILTER_VALIDATE_INT,
                'options' => [
                    'min_range' => 1,
                    'max_range' => 120,
                ],
            ],
            'email' => \FILTER_VALIDATE_EMAIL,
        ];

        $data['name'] = 'κόσμε';
        $data['age'] = '18';
        $data['email'] = 'foo@bar.de';

        static::assertSame(
            [
                'name'  => 'Κόσμε',
                'age'   => 18,
                'email' => 'foo@bar.de',
            ],
            UTF8::filter_var_array($data, $filters, true)
        );

        static::assertSame(
            [
                'name'  => 'κόσμε',
                'age'   => '18',
                'email' => 'foo@bar.de',
            ],
            UTF8::filter_var_array($data)
        );
    }

    public function testFitsInside()
    {
        $testArray = [
            'κόσμε'  => [5 => true],
            'test'   => [4 => true],
            ''       => [0 => true],
            ' '      => [0 => false],
            'abcöäü' => [2 => false],
        ];

        foreach ($testArray as $actual => $data) {
            foreach ($data as $size => $expected) {
                static::assertSame($expected, UTF8::fits_inside($actual, $size), 'error by ' . $actual);
            }
        }
    }

    public function testFixBrokenUtf8()
    {
        $testArray = [
            'ا (Alif) · ب (Bāʾ) · ت (Tāʾ) · ث (Ṯāʾ) · ج (Ǧīm) · ح (Ḥāʾ) · خ (Ḫāʾ) · د (Dāl) · ذ (Ḏāl) · ر (Rāʾ) · ز (Zāy) · س (Sīn) · ش (Šīn) · ص (Ṣād) · ض (Ḍād) · ط (Ṭāʾ) · ظ (Ẓāʾ) · ع (ʿAin) · غ (Ġain) · ف (Fāʾ) · ق (Qāf) · ك (Kāf) · ل (Lām) · م (Mīm) · ن (Nūn) · ه (Hāʾ) · و (Wāw) · ي (Yāʾ)' => 'ا (Alif) · ب (Bāʾ) · ت (Tāʾ) · ث (Ṯāʾ) · ج (Ǧīm) · ح (Ḥāʾ) · خ (Ḫāʾ) · د (Dāl) · ذ (Ḏāl) · ر (Rāʾ) · ز (Zāy) · س (Sīn) · ش (Šīn) · ص (Ṣād) · ض (Ḍād) · ط (Ṭāʾ) · ظ (Ẓāʾ) · ع (ʿAin) · غ (Ġain) · ف (Fāʾ) · ق (Qāf) · ك (Kāf) · ل (Lām) · م (Mīm) · ن (Nūn) · ه (Hāʾ) · و (Wāw) · ي (Yāʾ)',
            'строка на русском'                                                                                                                                                                                                                                                                        => 'строка на русском',
            'Düsseldorf'                                                                                                                                                                                                                                                                               => 'Düsseldorf',
            'Ã'                                                                                                                                                                                                                                                                                        => 'Ã',
            ' '                                                                                                                                                                                                                                                                                        => ' ',
            ''                                                                                                                                                                                                                                                                                         => '',
            "\n"                                                                                                                                                                                                                                                                                       => "\n",
            "test\xc2\x88"                                                                                                                                                                                                                                                                             => 'test',
            'DÃ¼sseldorf'                                                                                                                                                                                                                                                                              => 'Düsseldorf',
            'Ã¤'                                                                                                                                                                                                                                                                                       => 'ä',
            'test'                                                                                                                                                                                                                                                                                     => 'test',
            'FÃÂ©dération Camerounaise de Football'                                                                                                                                                                                                                                                    => 'Fédération Camerounaise de Football',
            "FÃÂ©dération Camerounaise de Football\n"                                                                                                                                                                                                                                                  => "Fédération Camerounaise de Football\n",
            'FÃ©dÃ©ration Camerounaise de Football'                                                                                                                                                                                                                                                    => 'Fédération Camerounaise de Football',
            "FÃ©dÃ©ration Camerounaise de Football\n"                                                                                                                                                                                                                                                  => "Fédération Camerounaise de Football\n",
            'FÃÂ©dÃÂ©ration Camerounaise de Football'                                                                                                                                                                                                                                                  => 'Fédération Camerounaise de Football',
            "FÃÂ©dÃÂ©ration Camerounaise de Football\n"                                                                                                                                                                                                                                                => "Fédération Camerounaise de Football\n",
            'FÃÂÂÂÂ©dÃÂÂÂÂ©ration Camerounaise de Football'                                                                                                                                                                                                                                            => 'Fédération Camerounaise de Football',
            "FÃÂÂÂÂ©dÃÂÂÂÂ©ration Camerounaise de Football\n"                                                                                                                                                                                                                                          => "Fédération Camerounaise de Football\n",
        ];

        if (UTF8::mbstring_loaded()) { // only with "mbstring"
            foreach ($testArray as $before => $after) {
                static::assertSame($after, UTF8::fix_utf8($before), 'tested: ' . $before);
            }
        }

        static::assertSame('Chris’d', UTF8::fix_utf8('Chris’d'));

        static::assertSame(['Düsseldorf', 'Fédération'], UTF8::fix_utf8(['DÃ¼sseldorf', 'FÃÂÂÂÂ©dÃÂÂÂÂ©ration']));
    }

    public function testFixSimpleUtf8()
    {
        $testArray = [
            'Düsseldorf'   => 'Düsseldorf',
            'Ã'            => 'Ã',
            ' '            => ' ',
            ''             => '',
            "\n"           => "\n",
            "test\xc2\x88" => 'testˆ',
            'DÃ¼sseldorf'  => 'Düsseldorf',
            'Ã¤'           => 'ä',
            'test'         => 'test',
        ];

        for ($i = 0; $i < 2; ++$i) { // keep this loop for simple performance tests
            if ($i === 0) {
                $this->disableNativeUtf8Support();
            } elseif ($i > 0) {
                $this->reactivateNativeUtf8Support();
            }

            foreach ($testArray as $before => $after) {
                static::assertSame($after, UTF8::fix_simple_utf8($before), 'tested: ' . $before);
            }
        }
    }

    public function testGetCharDirection()
    {
        $testArray = [
            'ا'                 => 'RTL',
            'أحبك'              => 'RTL',
            'זאת השפה העברית.א' => 'RTL',
            // http://dotancohen.com/howto/rtl_right_to_left.html
            'זאת השפה העברית.‏' => 'RTL',
            'abc'               => 'LTR',
            'öäü'               => 'LTR',
            '?'                 => 'LTR',
            '💩'                 => 'LTR',
            '中文空白'              => 'LTR',
            'मोनिच'             => 'LTR',
            'क्षȸ'              => 'LTR',
            'ࡘ'                 => 'RTL',
            '𐤹'                 => 'RTL',
            // https://www.compart.com/de/unicode/U+10939
            '𐠅' => 'RTL',
            // https://www.compart.com/de/unicode/U+10805
            'ますだ, よしひこ'                                             => 'LTR',
            '𐭠 𐭡 𐭢 𐭣 𐭤 𐭥 𐭦 𐭧 𐭨 𐭩 𐭪 𐭫 𐭬 𐭭 𐭮 𐭯 𐭰 𐭱 𐭲 𐭸 𐭹 𐭺 𐭻 𐭼 𐭽 𐭾 𐭿' => 'RTL',
            // http://www.sonderzeichen.de/Inscriptional_Pahlavi/Unicode-10B7F.html

        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::getCharDirection($actual), 'error by ' . $actual);
        }
    }

    public function testHexToIntAndIntToHex()
    {
        $tests = [
            'U+2026' => 8230,
            'U+03ba' => 954,
            'U+00f6' => 246,
            'U+00f1' => 241,
            'U+0000' => 0,
        ];

        $testsForHexToInt = [
            '\u2026' => 8230,
            '\u03ba' => 954,
            '\u00f6' => 246,
            '\u00f1' => 241,
            '\u0000' => 0,

            '2026' => 8230,
            '03ba' => 954,
            '00f6' => 246,
            '00f1' => 241,
            '0000' => 0,
        ];

        foreach (\array_replace($testsForHexToInt, $tests) as $before => $after) {
            static::assertSame($after, UTF8::hex_to_int($before), 'tested: ' . $before);
        }

        foreach ($tests as $after => $before) {
            static::assertSame($after, UTF8::int_to_hex($before), 'tested: ' . $before);
        }

        // --- fail (hex_to_int)

        static::assertFalse(UTF8::hex_to_int(''));
        static::assertFalse(UTF8::hex_to_int('abc-öäü'));
    }

    public function testHtmlEncode()
    {
        $testArray = [
            '{-test'                          => '&#123;&#45;&#116;&#101;&#115;&#116;',
            '中文空白'                            => '&#20013;&#25991;&#31354;&#30333;',
            'Dänisch (Å/å, Æ/æ, Ø/ø)'         => '&#68;&#228;&#110;&#105;&#115;&#99;&#104;&#32;&#40;&#197;&#47;&#229;&#44;&#32;&#198;&#47;&#230;&#44;&#32;&#216;&#47;&#248;&#41;',
            '👍 💩 😄 ❤ 👍 💩 😄 ❤'                 => '&#128077;&#32;&#128169;&#32;&#128516;&#32;&#10084;&#32;&#128077;&#32;&#128169;&#32;&#128516;&#32;&#10084;',
            'κόσμε'                           => '&#954;&#8057;&#963;&#956;&#949;',
            'öäü'                             => '&#246;&#228;&#252;',
            ' '                               => '&#32;',
            ''                                => '',
            '�'                               => '&#65533;',
            'Test-,;:'                        => '&#84;&#101;&#115;&#116;&#45;&#44;&#59;&#58;',
            '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ ♿ ⛎' => '&#128077;&#32;&#128169;&#32;&#128516;&#32;&#10084;&#32;&#128077;&#32;&#128169;&#32;&#128516;&#32;&#10084;&#32;&#128054;&#32;&#128169;&#32;&#128049;&#32;&#128056;&#32;&#127744;&#32;&#10084;&#32;&#9855;&#32;&#9934;',
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::html_encode($actual), 'tested:' . $actual);
        }

        foreach ($testArray as $actual => $expected) {
            static::assertSame($actual, UTF8::html_entity_decode(UTF8::html_encode($actual)), 'tested:' . $actual);
        }

        // ---

        $testArray = [
            '{-test'                          => '{-test',
            '中文空白'                            => '&#20013;&#25991;&#31354;&#30333;',
            'Dänisch (Å/å, Æ/æ, Ø/ø)'         => 'D&#228;nisch (&#197;/&#229;, &#198;/&#230;, &#216;/&#248;)',
            '👍 💩 😄 ❤ 👍 💩 😄 ❤'                 => '&#128077; &#128169; &#128516; &#10084; &#128077; &#128169; &#128516; &#10084;',
            'κόσμε'                           => '&#954;&#8057;&#963;&#956;&#949;',
            'öäü'                             => '&#246;&#228;&#252;',
            ' '                               => ' ',
            ''                                => '',
            '�'                               => '&#65533;',
            'Test-,;:'                        => 'Test-,;:',
            '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ ♿ ⛎' => '&#128077; &#128169; &#128516; &#10084; &#128077; &#128169; &#128516; &#10084; &#128054; &#128169; &#128049; &#128056; &#127744; &#10084; &#9855; &#9934;',
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::html_encode($actual, true), 'tested:' . $actual);
            static::assertSame($actual, UTF8::html_entity_decode(UTF8::html_encode($actual, true)), 'tested:' . $actual);
        }

        // ---

        $testArray = [
            '{-test'                          => '{-test',
            '中文空白'                            => '中文空白',
            'κόσμε'                           => 'κόσμε',
            'öäü'                             => 'öäü',
            'Dänisch (Å/å, Æ/æ, Ø/ø)'         => 'Dänisch (Å/å, Æ/æ, Ø/ø)',
            '👍 💩 😄 ❤ 👍 💩 😄 ❤'                 => '👍 💩 😄 ❤ 👍 💩 😄 ❤',
            ' '                               => ' ',
            ''                                => '',
            '&#d;'                            => '&#d;',
            '&d;'                             => '&d;',
            '&gt;'                            => '>',
            '%ABREPRESENT%C9%BB'              => '%ABREPRESENT%C9%BB',
            'Test-,;:'                        => 'Test-,;:',
            '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ ♿ ⛎' => '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ ♿ ⛎',
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::html_entity_decode(UTF8::html_encode($actual, true)), 'tested:' . $actual);
        }

        // --- ISO

        $testArray = [
            '中文空白'  => '中文空白',
            'κόσμε' => 'κόσμε',
            // 'öäü'                   => 'öäü',
            '(Å/å, Æ/æ, Ø/ø, Σ/σ)' => '(Å/å, Æ/æ, Ø/ø, Σ/σ)',
            '👍 💩 😄 ❤ 👍 💩 😄 ❤'      => '👍 💩 😄 ❤ 👍 💩 😄 ❤',
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertNotSame($expected, UTF8::html_entity_decode(UTF8::html_encode($actual, true, 'ISO')), 'tested:' . $actual);
        }

        $testArray = [
            '{-test'   => '{-test',
            'abc'      => 'abc',
            ' '        => ' ',
            ''         => '',
            '&#d;'     => '&#d;',
            '&d;'      => '&d;',
            '&gt;'     => '>',
            '&#39;'    => '\'',
            'Test-,;:' => 'Test-,;:',
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::html_entity_decode(UTF8::html_encode($actual, true, 'ISO'), \ENT_QUOTES), 'tested:' . $actual);
        }

        // ---

        // bug is reported: https://github.com/facebook/hhvm/issues/6303#issuecomment-234739899
        if (!\defined('HHVM_VERSION')) {
            $testArray = [
                '&#d;'  => '&#d;',
                '&d;'   => '&d;',
                '&gt;'  => '>',
                '&#39;' => '&#39;',
            ];

            foreach ($testArray as $actual => $expected) {
                static::assertSame($expected, UTF8::html_entity_decode(UTF8::html_encode($actual, true, 'ISO'), \ENT_COMPAT), 'tested:' . $actual);
            }
        }
    }

    /**
     * @noinspection HtmlDeprecatedTag
     */
    public function testHtmlEntityDecode()
    {
        $testArray = [
            'κόσμε'                                                                                     => 'κόσμε',
            'Κόσμε'                                                                                     => 'Κόσμε',
            'öäü-κόσμεκόσμε-äöü'                                                                        => 'öäü-κόσμεκόσμε-äöü',
            'öäü-κόσμεκόσμε-äöüöäü-κόσμεκόσμε-äöü'                                                      => 'öäü-κόσμεκόσμε-äöüöäü-κόσμεκόσμε-äöü',
            'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε'                              => 'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε',
            'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-Κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε'          => 'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-Κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε',
            '  '                                                                                        => '  ',
            ''                                                                                          => '',
            '&lt;abcd&gt;\'$1\'(&quot;&amp;2&quot;)'                                                    => '<abcd>\'$1\'("&2")',
            '&lt;script&gt;alert(&quot;foo&quot;);&lt;/script&gt;, &lt;marquee&gt;test&lt;/marquee&gt;' => '<script>alert("foo");</script>, <marquee>test</marquee>',
            '&amp;lt;script&amp;gt;alert(&amp;quot;XSS&amp;quot;)&amp;lt;/script&amp;gt;'               => '<script>alert("XSS")</script>',
            "Who\'s Online&#x0003A;"                                                                    => 'Who\\\'s Online:',
            '&lt;&copy; W3S&ccedil;h&deg;&deg;&brvbar;&sect;&gt;'                                       => '<© W3Sçh°°¦§>',
            '&#20013;&#25991;&#31354;&#30333;'                                                          => '中文空白',
        ];

        // bug is reported: https://github.com/facebook/hhvm/issues/6303#issuecomment-234739899
        if (!\defined('HHVM_VERSION')) {
            $tmpTestArray = [
                'who&#039;s online'                  => 'who&#039;s online',
                'who&amp;#039;s online'              => 'who&#039;s online',
                'who&#039;s online-'                 => 'who&#039;s online-',
                'Who&#039;s Online'                  => 'Who&#039;s Online',
                'Who&amp;#039;s Online'              => 'Who&#039;s Online',
                'Who&amp;amp;#039;s Online &#20013;' => 'Who&#039;s Online 中',
                'who\'s online&colon;'               => 'who\'s online&colon;',
            ];

            // long string ...
            $tmpTestArray['Who&amp;amp;#039;s Online &#20013; - Who&amp;amp;#039;s Online &#20013; - Who&amp;amp;#039;s Online &#20013; - Who&amp;amp;#039;s Online &#20013; - Who&amp;amp;#039;s Online &#20013; - Who&amp;amp;#039;s Online &#20013; - Who&amp;amp;#039;s Online &#20013; - Who&amp;amp;#039;s Online &#20013;'] = 'Who&#039;s Online 中 - Who&#039;s Online 中 - Who&#039;s Online 中 - Who&#039;s Online 中 - Who&#039;s Online 中 - Who&#039;s Online 中 - Who&#039;s Online 中 - Who&#039;s Online 中';

            $testArray = \array_merge($testArray, $tmpTestArray);
        }

        for ($i = 0; $i < 2; ++$i) { // keep this loop for simple performance tests
            foreach ($testArray as $before => $after) {
                static::assertSame($after, UTF8::html_entity_decode($before, \ENT_COMPAT), 'error by ' . $before);
            }
        }
    }

    /**
     * @noinspection NonShortCircuitBooleanExpressionJS
     * @noinspection HtmlDeprecatedTag
     */
    public function testHtmlEntityDecodeWithEntNoQuotes()
    {
        $testArray = [
            'κόσμε'                                                                                     => 'κόσμε',
            'Κόσμε'                                                                                     => 'Κόσμε',
            'öäü-κόσμεκόσμε-äöü'                                                                        => 'öäü-κόσμεκόσμε-äöü',
            'öäü-κόσμεκόσμε-äöüöäü-κόσμεκόσμε-äöü'                                                      => 'öäü-κόσμεκόσμε-äöüöäü-κόσμεκόσμε-äöü',
            'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε'                              => 'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε',
            'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-Κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε'          => 'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-Κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε',
            '  '                                                                                        => '  ',
            ''                                                                                          => '',
            '&lt;abcd&gt;\'$1\'(&quot;&amp;2&quot;)'                                                    => '<abcd>\'$1\'(&quot;&2&quot;)',
            '&lt;script&gt;alert(&quot;foo&quot;);&lt;/script&gt;, &lt;marquee&gt;test&lt;/marquee&gt;' => '<script>alert(&quot;foo&quot;);</script>, <marquee>test</marquee>',
            '&amp;lt;script&amp;gt;alert(&amp;quot;XSS&amp;quot;)&amp;lt;/script&amp;gt;'               => '<script>alert(&quot;XSS&quot;)</script>',
            '&lt;&copy; W3S&ccedil;h&deg;&deg;&brvbar;&sect;&gt;'                                       => '<© W3Sçh°°¦§>',
        ];

        // bug is reported: https://github.com/facebook/hhvm/issues/6303#issuecomment-234739899
        if (!\defined('HHVM_VERSION')) {
            $tmpTestArray = [
                'who&#039;s online'                  => 'who&#039;s online',
                'who&amp;#039;s online'              => 'who&#039;s online',
                'who&#039;s online-'                 => 'who&#039;s online-',
                'Who&#039;s Online'                  => 'Who&#039;s Online',
                'Who&amp;#039;s Online'              => 'Who&#039;s Online',
                'Who&amp;amp;#039;s Online &#20013;' => 'Who&#039;s Online 中',
                'who\'s online&colon;'               => 'who\'s online&colon;',
            ];

            $testArray = \array_merge($testArray, $tmpTestArray);
        }

        foreach ($testArray as $before => $after) {
            static::assertSame($after, UTF8::html_entity_decode($before, \ENT_NOQUOTES, 'UTF-8'), 'error by ' . $before);
        }
    }

    /**
     * @noinspection HtmlDeprecatedTag
     */
    public function testHtmlEntityDecodeWithEntQuotes()
    {
        $testArray = [
            'κόσμε'                                                                                     => 'κόσμε',
            'Κόσμε'                                                                                     => 'Κόσμε',
            'öäü-κόσμεκόσμε-äöü'                                                                        => 'öäü-κόσμεκόσμε-äöü',
            'öäü-κόσμεκόσμε-äöüöäü-κόσμεκόσμε-äöü'                                                      => 'öäü-κόσμεκόσμε-äöüöäü-κόσμεκόσμε-äöü',
            'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε'                              => 'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε',
            'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-Κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε'          => 'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-Κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε',
            '  '                                                                                        => '  ',
            ''                                                                                          => '',
            '&lt;abcd&gt;\'$1\'(&quot;&amp;2&quot;)'                                                    => '<abcd>\'$1\'("&2")',
            '&lt;script&gt;alert(&quot;foo&quot;);&lt;/script&gt;, &lt;marquee&gt;test&lt;/marquee&gt;' => '<script>alert("foo");</script>, <marquee>test</marquee>',
            '&amp;lt;script&amp;gt;alert(&amp;quot;XSS&amp;quot;)&amp;lt;/script&amp;gt;'               => '<script>alert("XSS")</script>',
            'who&#039;s online'                                                                         => 'who\'s online',
            'who&amp;#039;s online'                                                                     => 'who\'s online',
            'who&#039;s online-'                                                                        => 'who\'s online-',
            'Who&#039;s Online'                                                                         => 'Who\'s Online',
            'Who&amp;#039;s Online &#20013;'                                                            => 'Who\'s Online 中',
            'Who&amp;amp;#039;s Online'                                                                 => 'Who\'s Online',
            "Who\'s Online&#x0003A;"                                                                    => 'Who\\\'s Online:',
            '&lt;&copy; W3S&ccedil;h&deg;&deg;&brvbar;&sect;&gt;'                                       => '<© W3Sçh°°¦§>',
        ];

        // bug is reported: https://github.com/facebook/hhvm/issues/6303#issuecomment-234739899
        if (!\defined('HHVM_VERSION')) {
            $tmpTestArray = [
                'who\'s online&colon;' => 'who\'s online&colon;',
            ];

            $testArray = \array_merge($testArray, $tmpTestArray);
        }

        foreach ($testArray as $before => $after) {
            static::assertSame($after, UTF8::html_entity_decode($before, \ENT_QUOTES, 'UTF-8'), 'error by ' . $before);
        }

        // ---

        $testArray = [
            'κόσμε'                     => 'κόσμε',
            'who&#039;s online'         => 'who\'s online',
            'who&amp;#039;s online'     => 'who\'s online',
            'who&#039;s online-'        => 'who\'s online-',
            'Who&#039;s Online'         => 'Who\'s Online',
            'Who&amp;amp;#039;s Online' => 'Who\'s Online',
            "Who\'s Online&#x0003A;"    => 'Who\\\'s Online:',
        ];

        foreach ($testArray as $before => $after) {
            static::assertSame($after, UTF8::html_entity_decode($before, \ENT_QUOTES, 'ISO'), 'error by ' . $before); // 'ISO-8859-1'
        }

        static::assertSame('Who\'s Online 中', UTF8::html_entity_decode('Who&amp;#039;s Online &#20013;', \ENT_QUOTES, 'UTF8'));
        static::assertSame('Who\'s Online &#20013;', UTF8::html_entity_decode('Who&amp;#039;s Online &#20013;', \ENT_QUOTES, 'ISO'));
    }

    /**
     * @noinspection HtmlDeprecatedTag
     */
    public function testHtmlEntityDecodeWithHtml5()
    {
        $testArray = [
            'κόσμε'                                                                                     => 'κόσμε',
            'Κόσμε'                                                                                     => 'Κόσμε',
            'öäü-κόσμεκόσμε-äöü'                                                                        => 'öäü-κόσμεκόσμε-äöü',
            'öäü-κόσμεκόσμε-äöüöäü-κόσμεκόσμε-äöü'                                                      => 'öäü-κόσμεκόσμε-äöüöäü-κόσμεκόσμε-äöü',
            'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε'                              => 'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε',
            'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-Κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε'          => 'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-Κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε',
            '  '                                                                                        => '  ',
            ''                                                                                          => '',
            '&lt;abcd&gt;\'$1\'(&quot;&amp;2&quot;)'                                                    => '<abcd>\'$1\'("&2")',
            '&lt;script&gt;alert(&quot;foo&quot;);&lt;/script&gt;, &lt;marquee&gt;test&lt;/marquee&gt;' => '<script>alert("foo");</script>, <marquee>test</marquee>',
            '&amp;lt;script&amp;gt;alert(&amp;quot;XSS&amp;quot;)&amp;lt;/script&amp;gt;'               => '<script>alert("XSS")</script>',
            'who&#039;s online'                                                                         => 'who\'s online',
            'who&amp;#039;s online'                                                                     => 'who\'s online',
            'who&#039;s online-'                                                                        => 'who\'s online-',
            'Who&#039;s Online'                                                                         => 'Who\'s Online',
            'Who&amp;#039;s Online'                                                                     => 'Who\'s Online',
            'Who&amp;amp;#039;s Online'                                                                 => 'Who\'s Online',
            "Who\'s Online&#x0003A;"                                                                    => 'Who\\\'s Online:',
            '&lt;&copy; W3S&ccedil;h&deg;&deg;&brvbar;&sect;&gt;'                                       => '<© W3Sçh°°¦§>',
        ];

        // bug is reported: https://github.com/facebook/hhvm/issues/6303#issuecomment-234739899
        if (!\defined('HHVM_VERSION')) {
            $tmpTestArray = [
                'who\'s online&colon;' => 'who\'s online:',
            ];

            $testArray = \array_merge($testArray, $tmpTestArray);
        }

        foreach ($testArray as $before => $after) {
            static::assertSame($after, UTF8::html_entity_decode($before, \ENT_QUOTES | \ENT_HTML5, 'UTF-8'), 'error by ' . $before);
        }
    }

    public function testHtmlentities()
    {
        $testArray = [
            '&force_open_dashboard=0'                                                                                     => '&amp;force_open_dashboard=0',
            '<\\\'öäü>'                                                                                                   => '&lt;&#92;\'&ouml;&auml;&uuml;&gt;',
            '<白>'                                                                                                         => '&lt;&#30333;&gt;',
            '<白-öäü>'                                                                                                     => '&lt;&#30333;-&ouml;&auml;&uuml;&gt;',
            'dies ist ein test „Goldenen Regeln und Checklisten“.<br /><br /><br />' . UTF8::html_entity_decode('&nbsp;') => 'dies ist ein test &bdquo;Goldenen Regeln und Checklisten&ldquo;.&lt;br /&gt;&lt;br /&gt;&lt;br /&gt;&nbsp;',
            'öäü'                                                                                                         => '&ouml;&auml;&uuml;',
            ' '                                                                                                           => ' ',
            ''                                                                                                            => '',
            'Test-,;:'                                                                                                    => 'Test-,;:',
            '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ ♿ ⛎'                                                                             => '&#128077; &#128169; &#128516; &#10084; &#128077; &#128169; &#128516; &#10084; &#128054; &#128169; &#128049; &#128056; &#127744; &#10084; &#9855; &#9934;',
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::htmlentities($actual));

            static::assertSame(
                $actual,
                UTF8::html_entity_decode(
                    UTF8::htmlentities($actual)
                )
            );
        }

        // ---

        $testArray = [
            'abc' => 'abc',
            'öäü' => '&Atilde;&para;&Atilde;&curren;&Atilde;&frac14;',
            ' '   => ' ',
            ''    => '',
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::htmlentities($actual, \ENT_COMPAT, 'ISO-8859-1', false));

            static::assertSame(
                $actual,
                UTF8::html_entity_decode(
                    UTF8::htmlentities($actual, \ENT_COMPAT, 'ISO-8859-1', false),
                    \ENT_COMPAT,
                    'ISO-8859-1'
                )
            );
        }
    }

    public function testHtmlspecialchars()
    {
        $testArray = [
            "<a href='κόσμε'>κόσμε</a>"                     => "&lt;a href='κόσμε'&gt;κόσμε&lt;/a&gt;",
            '<白>'                                           => '&lt;白&gt;',
            'öäü'                                           => 'öäü',
            ' '                                             => ' ',
            ''                                              => '',
            'Test-,;:'                                      => 'Test-,;:',
            '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ &#x267F; &#x26CE;' => '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ &amp;#x267F; &amp;#x26CE;',
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::htmlspecialchars($actual));
            static::assertSame($expected, UTF8::htmlspecialchars($actual, \ENT_COMPAT, 'UTF8'));
        }

        // ---

        $testArray = [
            "<a href='κόσμε'>κόσμε</a>"                     => '&lt;a href=&#039;κόσμε&#039;&gt;κόσμε&lt;/a&gt;',
            '<白>'                                           => '&lt;白&gt;',
            'öäü'                                           => 'öäü',
            ' '                                             => ' ',
            ''                                              => '',
            'Test-,;:'                                      => 'Test-,;:',
            '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ &#x267F; &#x26CE;' => '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ &amp;#x267F; &amp;#x26CE;',
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::htmlspecialchars($actual, \ENT_QUOTES, 'UTF8'));
        }
    }

    public function testIsAscii()
    {
        $testArray = [
            'κ'      => false,
            'abc'    => true,
            'abcöäü' => false,
            '白'      => false,
            ' '      => true,
            ''       => true,
            '!!!'    => true,
            '§§§'    => false,
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::is_ascii($actual), 'error by ' . $actual);
        }
    }

    public function testIsBase64()
    {
        $tests = [
            0                                     => false,
            1                                     => false,
            -1                                    => false,
            ' '                                   => false,
            ''                                    => false,
            'أبز'                                 => false,
            "\xe2\x80\x99"                        => false,
            'Ɓtest'                               => false,
            \base64_encode('true')                => true,
            \base64_encode('  -ABC-中文空白-  ')      => true,
            'キャンパス'                               => false,
            'биологическом'                       => false,
            '정, 병호'                               => false,
            'on'                                  => false,
            'ますだ, よしひこ'                           => false,
            'मोनिच'                               => false,
            'क्षȸ'                                => false,
            \base64_encode('👍 💩 😄 ❤ 👍 💩 😄 ❤أحبك') => true,
            '👍 💩 😄 ❤ 👍 💩 😄 ❤أحبك'                 => false,
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::is_base64($before), 'tested:  ' . $before);
        }
    }

    public function testIsBinaryNonStrict()
    {
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        static::assertFalse(@UTF8::is_binary_file(__DIR__ . '/fixtures/fileNotExists.txt'));

        static::assertFalse(UTF8::is_binary_file(__DIR__ . '/fixtures/latin.txt'));

        $testString1 = \file_get_contents(__DIR__ . '/fixtures/latin.txt');
        static::assertFalse(UTF8::is_binary($testString1, false));

        $testString2 = UTF8::file_get_contents(__DIR__ . '/fixtures/latin.txt');
        static::assertFalse(UTF8::is_binary($testString2, false));

        static::assertSame(UTF8::to_utf8($testString1), $testString2);

        // ---

        static::assertTrue(UTF8::is_binary_file(__DIR__ . '/fixtures/test.xlsx'));
        $testString1 = \file_get_contents(__DIR__ . '/fixtures/test.xlsx');
        static::assertFalse(UTF8::is_binary($testString1, false));
        $testString2 = UTF8::file_get_contents(__DIR__ . '/fixtures/test.xlsx');
        static::assertFalse(UTF8::is_binary($testString2, false));

        static::assertSame($testString1, $testString2);

        // ---

        static::assertTrue(UTF8::is_binary_file(__DIR__ . '/fixtures/test.xls'));
        $testString1 = \file_get_contents(__DIR__ . '/fixtures/test.xls');
        static::assertTrue(UTF8::is_binary($testString1, false));
        $testString2 = UTF8::file_get_contents(__DIR__ . '/fixtures/test.xls');
        static::assertTrue(UTF8::is_binary($testString2, false));

        static::assertSame($testString1, $testString2);

        // ---

        static::assertTrue(UTF8::is_binary_file(__DIR__ . '/fixtures/test.pdf'));
        $testString1 = \file_get_contents(__DIR__ . '/fixtures/test.pdf');
        static::assertFalse(UTF8::is_binary($testString1, false));
        $testString2 = UTF8::file_get_contents(__DIR__ . '/fixtures/test.pdf');
        static::assertFalse(UTF8::is_binary($testString2, false));

        static::assertSame($testString1, $testString2);

        // ---

        static::assertTrue(UTF8::is_binary_file(__DIR__ . '/fixtures/image.png'));
        $testString1 = \file_get_contents(__DIR__ . '/fixtures/image.png');
        static::assertTrue(UTF8::is_binary($testString1, false));
        $testString2 = UTF8::file_get_contents(__DIR__ . '/fixtures/image.png');
        static::assertTrue(UTF8::is_binary($testString2, false));

        static::assertSame($testString1, $testString2);

        // ---

        static::assertTrue(UTF8::is_binary_file(__DIR__ . '/fixtures/image_small.png'));
        $testString1 = \file_get_contents(__DIR__ . '/fixtures/image_small.png');
        static::assertTrue(UTF8::is_binary($testString1, false));

        $testString2 = UTF8::file_get_contents(__DIR__ . '/fixtures/image_small.png');
        static::assertTrue(UTF8::is_binary($testString2, false));

        static::assertSame($testString1, $testString2);

        // ---

        static::assertFalse(UTF8::is_binary_file(__DIR__ . '/fixtures/test.js'));
        $testString3 = \file_get_contents(__DIR__ . '/fixtures/test.js');
        static::assertFalse(UTF8::is_binary($testString3, false));

        // ---

        static::assertTrue(UTF8::is_binary_file(__DIR__ . '/fixtures/test.zip'));
        $testString1 = \file_get_contents(__DIR__ . '/fixtures/test.zip');
        static::assertTrue(UTF8::is_binary($testString1, false));
        $testString2 = UTF8::file_get_contents(__DIR__ . '/fixtures/test.zip');
        static::assertTrue(UTF8::is_binary($testString2, false));

        static::assertSame($testString1, $testString2);

        // ---

        $tests = [
            'öäü'           => false,
            ''              => false,
            '1'             => false,
            '01010101'      => true,
            \decbin(324546) => true,
            01              => true,
            1020304         => false,
            01020304        => false,
            11020304        => false,
            '1010101'       => true,
            11111111        => true,
            00000000        => true,
            "\x00\x01"      => true,
            "\x01\x00"      => true,
            "\x01\x02"      => false,
            "\x01\x01ab"    => false,
            "\x01\x01b"     => false,
            "\x01\x00a"     => true, // >= 30% binary
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::is_binary($before, false), 'value: ' . $before);
        }
    }

    public function testIsBinaryStrict()
    {
        if (!\class_exists('finfo')) {
            static::markTestSkipped('finfo is not supported');
        }

        static::assertFalse(UTF8::is_binary_file(__DIR__ . '/fixtures/latin.txt'));
        $testString1 = \file_get_contents(__DIR__ . '/fixtures/latin.txt');
        static::assertFalse(UTF8::is_binary($testString1, true));
        $testString2 = UTF8::file_get_contents(__DIR__ . '/fixtures/latin.txt');
        static::assertFalse(UTF8::is_binary($testString2, true));

        static::assertSame(UTF8::to_utf8($testString1), $testString2);

        // ---

        static::assertTrue(UTF8::is_binary_file(__DIR__ . '/fixtures/test.xlsx'));
        $testString1 = \file_get_contents(__DIR__ . '/fixtures/test.xlsx');
        static::assertTrue(UTF8::is_binary($testString1, true));
        $testString2 = UTF8::file_get_contents(__DIR__ . '/fixtures/test.xlsx');
        static::assertTrue(UTF8::is_binary($testString2, true));

        static::assertSame($testString1, $testString2);

        // ---

        static::assertTrue(UTF8::is_binary_file(__DIR__ . '/fixtures/test.xls'));
        $testString1 = \file_get_contents(__DIR__ . '/fixtures/test.xls');
        static::assertTrue(UTF8::is_binary($testString1, true));
        $testString2 = UTF8::file_get_contents(__DIR__ . '/fixtures/test.xls');
        static::assertTrue(UTF8::is_binary($testString2, true));

        static::assertSame($testString1, $testString2);

        // ---

        static::assertTrue(UTF8::is_binary_file(__DIR__ . '/fixtures/test.pdf'));
        $testString1 = \file_get_contents(__DIR__ . '/fixtures/test.pdf');
        static::assertTrue(UTF8::is_binary($testString1, true));
        $testString2 = UTF8::file_get_contents(__DIR__ . '/fixtures/test.pdf');
        static::assertTrue(UTF8::is_binary($testString2, true));

        static::assertSame($testString1, $testString2);

        // ---

        static::assertTrue(UTF8::is_binary_file(__DIR__ . '/fixtures/image.png'));
        $testString1 = \file_get_contents(__DIR__ . '/fixtures/image.png');
        static::assertTrue(UTF8::is_binary($testString1, true));
        $testString2 = UTF8::file_get_contents(__DIR__ . '/fixtures/image.png');
        static::assertTrue(UTF8::is_binary($testString2, true));

        static::assertSame($testString1, $testString2);

        // ---

        static::assertTrue(UTF8::is_binary_file(__DIR__ . '/fixtures/image_small.png'));
        $testString1 = \file_get_contents(__DIR__ . '/fixtures/image_small.png');
        static::assertTrue(UTF8::is_binary($testString1, true));
        $testString2 = UTF8::file_get_contents(__DIR__ . '/fixtures/image_small.png');
        static::assertTrue(UTF8::is_binary($testString2, true));

        static::assertSame($testString1, $testString2);

        // ---

        static::assertTrue(UTF8::is_binary_file(__DIR__ . '/fixtures/test.zip'));
        $testString1 = \file_get_contents(__DIR__ . '/fixtures/test.zip');
        static::assertTrue(UTF8::is_binary($testString1, true));
        $testString2 = UTF8::file_get_contents(__DIR__ . '/fixtures/test.zip');
        static::assertTrue(UTF8::is_binary($testString2, true));

        static::assertSame($testString1, $testString2);

        // ---

        $tests = [
            'öäü'           => false,
            ''              => false,
            '1'             => false,
            '01010101'      => true,
            \decbin(324546) => true,
            01              => true,
            1020304         => false,
            01020304        => false,
            11020304        => false,
            '1010101'       => true,
            11111111        => true,
            00000000        => true,
            "\x00\x01"      => true,
            "\x01\x00"      => true,
            "\x01\x02"      => true,
            "\x01\x01ab"    => true,
            "\x01\x01b"     => true,
            "\x01\x00a"     => true, // >= 30% binary
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::is_binary($before, true), 'value: ' . $before);
        }
    }

    public function testIsBom()
    {
        $testArray = [
            "\xef\xbb\xbf"    => true,
            '  þÿ'            => true,
            "foo\xef\xbb\xbf" => false,
            '   þÿ'           => false,
            'foo'             => false,
            ''                => false,
            ' '               => false,
        ];

        foreach ($testArray as $test => $expected) {
            static::assertSame($expected, UTF8::is_bom($test), 'tested: ' . $test);
        }
    }

    public function testIsHtml()
    {
        $testArray = [
            '<h1>test</h1>'                     => true,
            '<html><body class="no-js"></html>' => true,
            '<html   f=\'\'    d="">'           => true,
            '<b>lall</b>'                       => true,
            'öäü<strong>lall</strong>'          => true,
            ' <b>lall</b>'                      => true,
            '<b><b>lall</b>'                    => true,
            '</b>lall</b>'                      => true,
            '<html><foo></html>'                => true,
            '<html><html>'                      => true,
            '<html>'                            => true,
            '</html>'                           => true,
            '<img src="#" alt="#" />'           => true,
            ''                                  => false,
            ' '                                 => false,
            'test'                              => false,
            '[b]lall[b]'                        => false,
            '<img src="" ...'                   => false, // non closed tag
            'html>'                             => false, // non opened tag
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::is_html($testString), 'tested: ' . $testString);
        }
    }

    public function testIsUtf16()
    {
        if (UTF8::mbstring_loaded() === false) { // only with "mbstring"
            static::markTestSkipped('only with "mbstring"');

            return;
        }

        $testArray = [
            1                                                                  => false,
            -1                                                                 => false,
            'κ'                                                                => false,
            ''                                                                 => false,
            ' '                                                                => false,
            "\n"                                                               => false,
            'abc'                                                              => false,
            'abcöäü'                                                           => false,
            '白'                                                                => false,
            'សាកល្បង!'                                                         => false,
            'דיעס איז אַ פּרובירן!'                                            => false,
            'Штампи іст Ейн тест!'                                             => false,
            'Штампы гіст Эйн тэст!'                                            => false,
            '測試！'                                                              => false,
            'ການທົດສອບ!'                                                       => false,
            'Iñtërnâtiônàlizætiøn'                                             => false,
            'ABC 123'                                                          => false,
            "Iñtërnâtiôn\xE9àlizætiøn"                                         => false,
            "\xf0\x28\x8c\x28"                                                 => false,
            "this is an invalid char '\xE9' here"                              => false,
            "\xC3\xB1"                                                         => false,
            "Iñtërnâtiônàlizætiøn \xC3\x28 Iñtërnâtiônàlizætiøn"               => false,
            "Iñtërnâtiônàlizætiøn\xA0\xA1Iñtërnâtiônàlizætiøn"                 => false,
            "Iñtërnâtiônàlizætiøn\xE2\x82\xA1Iñtërnâtiônàlizætiøn"             => false,
            "Iñtërnâtiônàlizætiøn\xE2\x28\xA1Iñtërnâtiônàlizætiøn"             => false,
            "Iñtërnâtiônàlizætiøn\xE2\x82\x28Iñtërnâtiônàlizætiøn"             => false,
            "Iñtërnâtiônàlizætiøn\xF0\x90\x8C\xBCIñtërnâtiônàlizætiøn"         => false,
            "Iñtërnâtiônàlizætiøn\xF0\x28\x8C\xBCIñtërnâtiônàlizætiøn"         => false,
            "Iñtërnâtiônàlizætiøn\xf8\xa1\xa1\xa1\xa1Iñtërnâtiônàlizætiøn"     => false,
            "Iñtërnâtiônàlizætiøn\xFC\xA1\xA1\xA1\xA1\xA1Iñtërnâtiônàlizætiøn" => false,
            "\xC3\x28"                                                         => false,
            "\xA0\xA1"                                                         => false,
            "\xE2\x82\xA1"                                                     => false,
            "\xE2\x28\xA1"                                                     => false,
            "\xE2\x82\x28"                                                     => false,
            "\xF0\x90\x8C\xBC"                                                 => false,
            "\xF0\x28\x8C\xBC"                                                 => false,
            "\xF0\x90\x28\xBC"                                                 => false,
            "\xF0\x28\x8C\x28"                                                 => false,
            "\xF8\xA1\xA1\xA1\xA1"                                             => false,
            "\xFC\xA1\xA1\xA1\xA1\xA1"                                         => false,
        ];

        $counter = 0;
        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::is_utf16($actual), 'error by - ' . $counter . ' :' . $actual);
            ++$counter;
        }

        $counter = 0;
        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::is_utf16($actual), 'error by - ' . $counter . ' :' . $actual);
            ++$counter;
        }

        static::assertFalse(UTF8::is_utf16(\file_get_contents(__DIR__ . '/fixtures/utf-8.txt')));
        static::assertFalse(UTF8::is_utf16(\file_get_contents(__DIR__ . '/fixtures/utf-8-bom.txt')));

        static::assertSame(2, UTF8::is_utf16(\file_get_contents(__DIR__ . '/fixtures/utf-16-be.txt')));
        static::assertSame(2, UTF8::is_utf16(\file_get_contents(__DIR__ . '/fixtures/utf-16-be-bom.txt')));

        static::assertSame(1, UTF8::is_utf16(\file_get_contents(__DIR__ . '/fixtures/utf-16-le.txt')));
        static::assertSame(1, UTF8::is_utf16(\file_get_contents(__DIR__ . '/fixtures/utf-16-le-bom.txt')));

        static::assertSame(1, UTF8::is_utf16(\file_get_contents(__DIR__ . '/fixtures/sample-utf-16-le-bom.txt')));
        static::assertSame(2, UTF8::is_utf16(\file_get_contents(__DIR__ . '/fixtures/sample-utf-16-be-bom.txt')));
    }

    public function testIsUtf32()
    {
        if (UTF8::mbstring_loaded() === false) { // only with "mbstring"
            static::markTestSkipped('only with "mbstring"');

            return;
        }

        $testArray = [
            1                                                                  => false,
            -1                                                                 => false,
            'κ'                                                                => false,
            ''                                                                 => false,
            ' '                                                                => false,
            "\n"                                                               => false,
            'abc'                                                              => false,
            'abcöäü'                                                           => false,
            '白'                                                                => false,
            'សាកល្បង!'                                                         => false,
            'דיעס איז אַ פּרובירן!'                                            => false,
            'Штампи іст Ейн тест!'                                             => false,
            'Штампы гіст Эйн тэст!'                                            => false,
            '測試！'                                                              => false,
            'ການທົດສອບ!'                                                       => false,
            'Iñtërnâtiônàlizætiøn'                                             => false,
            'ABC 123'                                                          => false,
            "Iñtërnâtiôn\xE9àlizætiøn"                                         => false,
            "\xf0\x28\x8c\x28"                                                 => false,
            "this is an invalid char '\xE9' here"                              => false,
            "\xC3\xB1"                                                         => false,
            "Iñtërnâtiônàlizætiøn \xC3\x28 Iñtërnâtiônàlizætiøn"               => false,
            "Iñtërnâtiônàlizætiøn\xA0\xA1Iñtërnâtiônàlizætiøn"                 => false,
            "Iñtërnâtiônàlizætiøn\xE2\x82\xA1Iñtërnâtiônàlizætiøn"             => false,
            "Iñtërnâtiônàlizætiøn\xE2\x28\xA1Iñtërnâtiônàlizætiøn"             => false,
            "Iñtërnâtiônàlizætiøn\xE2\x82\x28Iñtërnâtiônàlizætiøn"             => false,
            "Iñtërnâtiônàlizætiøn\xF0\x90\x8C\xBCIñtërnâtiônàlizætiøn"         => false,
            "Iñtërnâtiônàlizætiøn\xF0\x28\x8C\xBCIñtërnâtiônàlizætiøn"         => false,
            "Iñtërnâtiônàlizætiøn\xf8\xa1\xa1\xa1\xa1Iñtërnâtiônàlizætiøn"     => false,
            "Iñtërnâtiônàlizætiøn\xFC\xA1\xA1\xA1\xA1\xA1Iñtërnâtiônàlizætiøn" => false,
            "\xC3\x28"                                                         => false,
            "\xA0\xA1"                                                         => false,
            "\xE2\x82\xA1"                                                     => false,
            "\xE2\x28\xA1"                                                     => false,
            "\xE2\x82\x28"                                                     => false,
            "\xF0\x90\x8C\xBC"                                                 => false,
            "\xF0\x28\x8C\xBC"                                                 => false,
            "\xF0\x90\x28\xBC"                                                 => false,
            "\xF0\x28\x8C\x28"                                                 => false,
            "\xF8\xA1\xA1\xA1\xA1"                                             => false,
            "\xFC\xA1\xA1\xA1\xA1\xA1"                                         => false,
        ];

        $counter = 0;
        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::is_utf32($actual), 'error by - ' . $counter . ' :' . $actual);
            ++$counter;
        }

        $counter = 0;
        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::is_utf32($actual), 'error by - ' . $counter . ' :' . $actual);
            ++$counter;
        }

        static::assertFalse(UTF8::is_utf32(\file_get_contents(__DIR__ . '/fixtures/utf-8.txt')));
        static::assertFalse(UTF8::is_utf32(\file_get_contents(__DIR__ . '/fixtures/utf-8-bom.txt')));

        static::assertSame(1, UTF8::is_utf32(\file_get_contents(__DIR__ . '/fixtures/sample-utf-32-le-bom.txt')));
        static::assertSame(2, UTF8::is_utf32(\file_get_contents(__DIR__ . '/fixtures/sample-utf-32-be-bom.txt')));
    }

    public function testIsUtf8()
    {
        $testArray = [
            1                          => true,
            -1                         => true,
            'κ'                        => true,
            ''                         => true,
            ' '                        => true,
            "\n"                       => true,
            'abc'                      => true,
            'abcöäü'                   => true,
            '白'                        => true,
            'សាកល្បង!'                 => true,
            'דיעס איז אַ פּרובירן!'    => true,
            'Штампи іст Ейн тест!'     => true,
            'Штампы гіст Эйн тэст!'    => true,
            '測試！'                      => true,
            'ການທົດສອບ!'               => true,
            'Iñtërnâtiônàlizætiøn'     => true,
            'ABC 123'                  => true,
            "Iñtërnâtiôn\xE9àlizætiøn" => false,
            '𐤹'                        => true,
            // https://www.compart.com/de/unicode/U+10939
            '𐠅' => true,
            // https://www.compart.com/de/unicode/U+10805
            'ますだ, よしひこ'                                             => true,
            '𐭠 𐭡 𐭢 𐭣 𐭤 𐭥 𐭦 𐭧 𐭨 𐭩 𐭪 𐭫 𐭬 𐭭 𐭮 𐭯 𐭰 𐭱 𐭲 𐭸 𐭹 𐭺 𐭻 𐭼 𐭽 𐭾 𐭿' => true,
            // http://www.sonderzeichen.de/Inscriptional_Pahlavi/Unicode-10B7F.html
            "\xf0\x28\x8c\x28"                                                                                                             => false,
            "this is an invalid char '\xE9' here"                                                                                          => false,
            "\xC3\xB1"                                                                                                                     => true,
            "Iñtërnâtiônàlizætiøn \xC3\x28 Iñtërnâtiônàlizætiøn"                                                                           => false,
            "Iñtërnâtiônàlizætiøn\xA0\xA1Iñtërnâtiônàlizætiøn"                                                                             => false,
            "Iñtërnâtiônàlizætiøn\xE2\x82\xA1Iñtërnâtiônàlizætiøn"                                                                         => true,
            "Iñtërnâtiônàlizætiøn\xE2\x28\xA1Iñtërnâtiônàlizætiøn"                                                                         => false,
            "Iñtërnâtiônàlizætiøn\xE2\x82\x28IñtërnâtiônàlizætiønIñtërnâtiônàlizætiønIñtërnâtiônàlizætiønIñtërnâtiônàlizætiøn"             => false,
            "Iñtërnâtiônàlizætiøn\xF0\x90\x8C\xBCIñtërnâtiônàlizætiønIñtërnâtiônàlizætiønIñtërnâtiônàlizætiønIñtërnâtiônàlizætiøn"         => true,
            "Iñtërnâtiônàlizætiøn\xF0\x28\x8C\xBCIñtërnâtiônàlizætiønIñtërnâtiônàlizætiønIñtërnâtiônàlizætiønIñtërnâtiônàlizætiøn"         => false,
            "Iñtërnâtiônàlizætiøn\xf8\xa1\xa1\xa1\xa1IñtërnâtiônàlizætiønIñtërnâtiônàlizætiønIñtërnâtiônàlizætiønIñtërnâtiônàlizætiøn"     => false,
            "Iñtërnâtiônàlizætiøn\xFC\xA1\xA1\xA1\xA1\xA1IñtërnâtiônàlizætiønIñtërnâtiônàlizætiønIñtërnâtiônàlizætiønIñtërnâtiônàlizætiøn" => false,
            "\xC3\x28"                                                                                                                     => false,
            "\xA0\xA1"                                                                                                                     => false,
            "\xE2\x82\xA1"                                                                                                                 => true,
            "\xE2\x28\xA1"                                                                                                                 => false,
            "\xE2\x82\x28"                                                                                                                 => false,
            "\xF0\x90\x8C\xBC"                                                                                                             => true,
            "\xF0\x28\x8C\xBC"                                                                                                             => false,
            "\xF0\x90\x28\xBC"                                                                                                             => false,
            "\xF0\x28\x8C\x28"                                                                                                             => false,
            "\xF8\xA1\xA1\xA1\xA1"                                                                                                         => false,
            "\xFC\xA1\xA1\xA1\xA1\xA1"                                                                                                     => false,
        ];

        static::assertFalse(UTF8::is_utf8(\array_keys($testArray)));

        for ($i = 0; $i < 1000; ++$i) { // keep this loop for simple performance tests
            $counter = 0;
            foreach ($testArray as $actual => $expected) {
                static::assertSame($expected, UTF8::is_utf8($actual), 'error by - ' . $counter . ' :' . $actual);
                ++$counter;
            }
        }

        $counter = 0;
        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::is_utf8((string) $actual), 'error by - ' . $counter . ' :' . $actual);
            ++$counter;
        }

        static::assertFalse(UTF8::is_utf8(\file_get_contents(__DIR__ . '/fixtures/utf-16-be.txt'), true));
        static::assertFalse(UTF8::is_utf8(\file_get_contents(__DIR__ . '/fixtures/utf-16-be-bom.txt'), true));
    }

    public function testJsonDecode()
    {
        $testArray = [
            '{"recipe_id":-1,"recipe_created":"","recipe_title":"FSDFSDF","recipe_description":"","recipe_yield":0,"recipe_prepare_time":"fast","recipe_image":"","recipe_legal":0,"recipe_license":0,"recipe_category_id":[],"recipe_category_name":[],"recipe_variety_id":[],"recipe_variety_name":[],"recipe_tag_id":[],"recipe_tag_name":[],"recipe_instruction_id":[],"recipe_instruction_text":[],"recipe_ingredient_id":[],"recipe_ingredient_name":[],"recipe_ingredient_amount":[],"recipe_ingredient_unit":[],"errorArray":{"recipe_legal":"error","recipe_license":"error","recipe_description":"error","recipe_yield":"error","recipe_category_name":"error","recipe_tag_name":"error","recipe_instruction_text":"error","recipe_ingredient_amount":"error","recipe_ingredient_unit":"error"},"errorMessage":"[[Bitte f\u00fclle die rot markierten Felder korrekt aus.]]","db":{"query_count":15}}'                            => '{"recipe_id":-1,"recipe_created":"","recipe_title":"FSDFSDF","recipe_description":"","recipe_yield":0,"recipe_prepare_time":"fast","recipe_image":"","recipe_legal":0,"recipe_license":0,"recipe_category_id":[],"recipe_category_name":[],"recipe_variety_id":[],"recipe_variety_name":[],"recipe_tag_id":[],"recipe_tag_name":[],"recipe_instruction_id":[],"recipe_instruction_text":[],"recipe_ingredient_id":[],"recipe_ingredient_name":[],"recipe_ingredient_amount":[],"recipe_ingredient_unit":[],"errorArray":{"recipe_legal":"error","recipe_license":"error","recipe_description":"error","recipe_yield":"error","recipe_category_name":"error","recipe_tag_name":"error","recipe_instruction_text":"error","recipe_ingredient_amount":"error","recipe_ingredient_unit":"error"},"errorMessage":"[[Bitte f\u00fclle die rot markierten Felder korrekt aus.]]","db":{"query_count":15}}',
            '{"recipe_id":-1,"recipe_created":"","recipe_title":"FSDFSκόσμε' . "\xa0\xa1" . '-öäüDF","recipe_description":"","recipe_yield":0,"recipe_prepare_time":"fast","recipe_image":"","recipe_legal":0,"recipe_license":0,"recipe_category_id":[],"recipe_category_name":[],"recipe_variety_id":[],"recipe_variety_name":[],"recipe_tag_id":[],"recipe_tag_name":[],"recipe_instruction_id":[],"recipe_instruction_text":[],"recipe_ingredient_id":[],"recipe_ingredient_name":[],"recipe_ingredient_amount":[],"recipe_ingredient_unit":[],"errorArray":{"recipe_legal":"error","recipe_license":"error","recipe_description":"error","recipe_yield":"error","recipe_category_name":"error","recipe_tag_name":"error","recipe_instruction_text":"error","recipe_ingredient_amount":"error","recipe_ingredient_unit":"error"},"errorMessage":"[[Bitte f\u00fclle die rot markierten Felder korrekt aus.]]","db":{"query_count":15}}' => '{"recipe_id":-1,"recipe_created":"","recipe_title":"FSDFSκόσμε' . \html_entity_decode('&nbsp;') . '¡-öäüDF","recipe_description":"","recipe_yield":0,"recipe_prepare_time":"fast","recipe_image":"","recipe_legal":0,"recipe_license":0,"recipe_category_id":[],"recipe_category_name":[],"recipe_variety_id":[],"recipe_variety_name":[],"recipe_tag_id":[],"recipe_tag_name":[],"recipe_instruction_id":[],"recipe_instruction_text":[],"recipe_ingredient_id":[],"recipe_ingredient_name":[],"recipe_ingredient_amount":[],"recipe_ingredient_unit":[],"errorArray":{"recipe_legal":"error","recipe_license":"error","recipe_description":"error","recipe_yield":"error","recipe_category_name":"error","recipe_tag_name":"error","recipe_instruction_text":"error","recipe_ingredient_amount":"error","recipe_ingredient_unit":"error"},"errorMessage":"[[Bitte fülle die rot markierten Felder korrekt aus.]]","db":{"query_count":15}}',
            '{"array":[1,2,3],"boolean":true,"null":null,"number":123,"object":{"a":"b","c":"d","e":"f"},"string":"Hello World | öäü"}'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     => '{"array":[1,2,3],"boolean":true,"null":null,"number":123,"object":{"a":"b","c":"d","e":"f"},"string":"Hello World | öäü"}',
            '{"array":[1,"¥","ä"]}'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         => '{"array":[1,"¥","ä"]}',
        ];

        foreach ($testArray as $before => $after) {
            static::assertSame($after, UTF8::json_decode(UTF8::json_encode($before)));
        }

        // ---

        // add more tests
        $testArray[''] = false;
        $testArray['{"array":[1,2,3],,...}}'] = false;
        $testArray['{"test": 123}'] = true;
        $testArray['[{"test": 123}]'] = true;

        foreach ($testArray as $before => $after) {
            static::assertSame(
                ($after === false ? false : true),
                UTF8::is_json($before),
                'tested: ' . $before
            );
        }

        // ----

        $expected = new \stdClass();
        $expected->array = [1, '¥', 'ä'];
        static::assertSame((array) $expected, (array) UTF8::json_decode('{"array":[1,"¥","ä"]}'));

        // ----

        static::assertSame([1, '¥', 'ä'], UTF8::json_decode('[1,"\u00a5","\u00e4"]'));
    }

    public function testShowSupport()
    {
        \ob_start();
        /** @noinspection UnusedFunctionResultInspection */
        UTF8::showSupport();
        $support = \ob_get_clean();

        if (\method_exists(__CLASS__, 'assertStringContainsString')) {
            static::assertStringContainsString('mbstring_func_overload', $support);
        } else {
            static::assertContains('mbstring_func_overload', $support);
        }
    }

    public function testJsonEncode()
    {
        $test = new \stdClass();
        $test->array = [1, '¥', 'ä'];
        static::assertSame('{"array":[1,"\u00a5","\u00e4"]}', UTF8::json_encode($test));

        // ----

        static::assertSame('[1,"\u00a5","\u00e4"]', UTF8::json_encode([1, '¥', 'ä']));
    }

    public function testLcWords()
    {
        static::assertSame('iñt ërn âTi ônà liz æti øn', UTF8::lcwords('Iñt ërn âTi ônà liz æti øn'));
        static::assertSame("iñt ërn âti\n ônà liz æti  øn", UTF8::lcwords("Iñt Ërn Âti\n Ônà Liz Æti  Øn"));
        static::assertSame('中文空白 foo oo oöäü#s', UTF8::lcwords('中文空白 foo oo oöäü#s', ['foo'], '#'));
        static::assertSame('中文空白 foo oo oöäü#s', UTF8::lcwords('中文空白 foo oo oöäü#s', ['foo'], ''));
        static::assertSame('', UTF8::lcwords(''));
        static::assertSame('ñ', UTF8::lcwords('Ñ'));
        static::assertSame("iñt ërN âti\n ônà liz æti øn", UTF8::lcwords("Iñt ËrN Âti\n Ônà Liz Æti Øn"));
        static::assertSame('ñtërnâtiônàlizætIøN', UTF8::lcwords('ÑtërnâtiônàlizætIøN'));
        static::assertSame('ñtërnâtiônàlizætIøN test câse', UTF8::lcwords('ÑtërnâtiônàlizætIøN Test câse', ['câse']));
        static::assertSame('deja σσς dEJa σσΣ', UTF8::lcwords('Deja Σσς DEJa ΣσΣ'));

        static::assertSame('deja σσς dEJa σσΣ', UTF8::lcwords('Deja Σσς DEJa ΣσΣ', ['de']));
        static::assertSame('deja σσς dEJa σσΣ', UTF8::lcwords('Deja Σσς DEJa ΣσΣ', ['d', 'e']));

        static::assertSame('DejA σσς dEJa σσΣ', UTF8::lcwords('DejA σσς dEJa σσΣ', ['DejA']));
        static::assertSame('deja σσς dEJa σσΣ', UTF8::lcwords('deja σσς dEJa σσΣ', ['deja', 'σσΣ']));
    }

    public function testLcfirst()
    {
        static::assertSame('', UTF8::lcfirst(''));
        static::assertSame('ö', UTF8::lcfirst('Ö'));
        static::assertSame('öäü', UTF8::lcfirst('Öäü'));
        static::assertSame('κόσμε', UTF8::lcfirst('Κόσμε'));
        static::assertSame('aBC-ÖÄÜ-中文空白', UTF8::lcfirst('ABC-ÖÄÜ-中文空白'));
        static::assertSame('ñTËRNÂTIÔNÀLIZÆTIØN', UTF8::lcfirst('ÑTËRNÂTIÔNÀLIZÆTIØN'));
        static::assertSame('ñTËRNÂTIÔNÀLIZÆTIØN', UTF8::lcfirst('ñTËRNÂTIÔNÀLIZÆTIØN'));
        static::assertSame('', UTF8::lcfirst(''));
        static::assertSame(' ', UTF8::lcfirst(' '));
        static::assertSame("\t test", UTF8::lcfirst("\t test"));
        static::assertSame('ñ', UTF8::lcfirst('Ñ'));
        static::assertSame("ñTËRN\nâtiônàlizætiøn", UTF8::lcfirst("ÑTËRN\nâtiônàlizætiøn"));
        static::assertSame('deja', UTF8::lcfirst('Deja'));
        static::assertSame('σσς', UTF8::lcfirst('Σσς'));
        static::assertSame('dEJa', UTF8::lcfirst('dEJa'));
        static::assertSame('σσΣ', UTF8::lcfirst('σσΣ'));

        static::assertSame('deja', UTF8::lcwords('Deja'));
    }

    public function testLtrim()
    {
        $tests = [
            '  -ABC-中文空白-  ' => '-ABC-中文空白-  ',
            '      - ÖÄÜ- '  => '- ÖÄÜ- ',
            'öäü'            => 'öäü',
            // 1                => '1',
            // ''               => '',
            // null             => '',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::ltrim($before), 'tested: ' . $before);
            static::assertSame($after, \ltrim($before));
        }

        static::assertSame('tërnâtiônàlizætiøn', UTF8::ltrim('ñtërnâtiônàlizætiøn', 'ñ'));
        static::assertSame('tërnâtiônàlizætiøn', \ltrim('ñtërnâtiônàlizætiøn', 'ñ'));

        static::assertSame('Iñtërnâtiônàlizætiøn', UTF8::ltrim('Iñtërnâtiônàlizætiøn', 'ñ'));
        static::assertSame('Iñtërnâtiônàlizætiøn', \ltrim('Iñtërnâtiônàlizætiøn', 'ñ'));

        static::assertSame('', UTF8::ltrim(''));
        static::assertSame('', \ltrim(''));

        static::assertSame('', UTF8::ltrim(' '));
        static::assertSame('', \ltrim(' '));

        static::assertSame('Iñtërnâtiônàlizætiøn', UTF8::ltrim('/Iñtërnâtiônàlizætiøn', '/'));
        static::assertSame('Iñtërnâtiônàlizætiøn', \ltrim('/Iñtërnâtiônàlizætiøn', '/'));

        static::assertSame('Iñtërnâtiônàlizætiøn', UTF8::ltrim('Iñtërnâtiônàlizætiøn', '^s'));
        static::assertSame('Iñtërnâtiônàlizætiøn', \ltrim('Iñtërnâtiônàlizætiøn', '^s'));

        static::assertSame("\nñtërnâtiônàlizætiøn", UTF8::ltrim("ñ\nñtërnâtiônàlizætiøn", 'ñ'));
        static::assertSame("\nñtërnâtiônàlizætiøn", \ltrim("ñ\nñtërnâtiônàlizætiøn", 'ñ'));

        static::assertSame('tërnâtiônàlizætiøn', UTF8::ltrim("ñ\nñtërnâtiônàlizætiøn", "ñ\n"));
        static::assertSame('tërnâtiônàlizætiøn', \ltrim("ñ\nñtërnâtiônàlizætiøn", "ñ\n"));

        // UTF-8

        static::assertSame("#string#\xc2\xa0\xe1\x9a\x80", UTF8::ltrim("\xe2\x80\x83\x20#string#\xc2\xa0\xe1\x9a\x80"));
    }

    public function testMax()
    {
        $tests = [
            'abc-äöü-中文空白'         => '空',
            'öäü'                  => 'ü',
            'öäü test öäü'         => 'ü',
            'ÖÄÜ'                  => 'Ü',
            '中文空白'                 => '空',
            'Intërnâtiônàlizætiøn' => 'ø',
            false                  => null,
            null                   => null,
            ''                     => null,
            ' '                    => ' ',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::max($before), 'tested: "' . $before . '"');
        }

        static::assertSame('空', UTF8::max(['öäü', '1,2,3,4', 'test', '中 文 空 白', 'abc']));
    }

    public function testMaxChrWidth()
    {
        $testArray = [
            '中文空白'                 => 3,
            'Intërnâtiônàlizætiøn' => 2,
            'öäü'                  => 2,
            'abc'                  => 1,
            ''                     => 0,
            null                   => 0,
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::max_chr_width($actual));
        }
    }

    public function testMin()
    {
        $tests = [
            'abc-äöü-中文空白' => '-',
            'öäü'          => 'ä',
            'öäü test öäü' => ' ',
            'ÖÄÜ'          => 'Ä',
            '中文空白'         => '中',
            false          => null,
            null           => null,
            ''             => null,
            ' '            => ' ',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::min($before));
        }

        static::assertSame(' ', UTF8::min(['öäü', '1,2,3,4', ' ', 'test', 'abc']));
    }

    public function testNormalizeEncoding()
    {
        $tests = [
            'ISO'          => 'ISO-8859-1',
            'UTF8'         => 'UTF-8',
            'WINDOWS-1251' => 'WINDOWS-1251',
            ''             => '',
            'Utf-8'        => 'UTF-8',
            'UTF-8'        => 'UTF-8',
            'ISO-8859-5'   => 'ISO-8859-5',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::normalize_encoding($before, ''), 'tested: ' . $before);
        }
    }

    public function testNormalizeMsword()
    {
        $tests = [
            ''                                                                         => '',
            ' '                                                                        => ' ',
            '«foobar»'                                                                 => '<<foobar>>',
            '中文空白 ‟'                                                                   => '中文空白 "',
            "<ㅡㅡ></ㅡㅡ><div>…</div><input type='email' name='user[email]' /><a>wtf</a>" => "<ㅡㅡ></ㅡㅡ><div>...</div><input type='email' name='user[email]' /><a>wtf</a>",
            '– DÃ¼sseldorf —'                                                          => '- DÃ¼sseldorf -',
            '„Abcdef…”'                                                                => '"Abcdef..."',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::normalize_msword($before));
        }
    }

    public function testNormalizeWhitespace()
    {
        $tests = [
            ''                                                                                    => '',
            ' '                                                                                   => ' ',
            ' foo ' . "\xe2\x80\xa8" . ' öäü' . "\xe2\x80\xa9"                                    => ' foo   öäü ',
            "«\xe2\x80\x80foobar\xe2\x80\x80»"                                                    => '« foobar »',
            '中文空白 ‟'                                                                              => '中文空白 ‟',
            "<ㅡㅡ></ㅡㅡ><div>\xe2\x80\x85</div><input type='email' name='user[email]' /><a>wtf</a>" => "<ㅡㅡ></ㅡㅡ><div> </div><input type='email' name='user[email]' /><a>wtf</a>",
            "–\xe2\x80\x8bDÃ¼sseldorf\xe2\x80\x8b—"                                               => '– DÃ¼sseldorf —',
            "„Abcdef\xe2\x81\x9f”"                                                                => '„Abcdef ”',
            " foo\t foo "                                                                         => ' foo	 foo ',
        ];

        for ($i = 0; $i < 2; ++$i) { // keep this loop for simple performance tests
            if ($i === 0) {
                $this->disableNativeUtf8Support();
            } elseif ($i > 0) {
                $this->reactivateNativeUtf8Support();
            }

            foreach ($tests as $before => $after) {
                static::assertSame($after, UTF8::normalize_whitespace($before));
            }
        }

        // replace "non breaking space"
        static::assertSame('abc- -öäü- -', UTF8::normalize_whitespace("abc-\xc2\xa0-öäü-\xe2\x80\xaf-\xE2\x80\xAC"));

        // keep "non breaking space"
        static::assertSame("abc-\xc2\xa0-öäü- -", UTF8::normalize_whitespace("abc-\xc2\xa0-öäü-\xe2\x80\xaf-\xE2\x80\xAC", true));

        // ... and keep "bidirectional text chars"
        static::assertSame("abc-\xc2\xa0-öäü- -\xE2\x80\xAC", UTF8::normalize_whitespace("abc-\xc2\xa0-öäü-\xe2\x80\xaf-\xE2\x80\xAC", true, true));
    }

    public function testOrd()
    {
        $nbsp = UTF8::html_entity_decode('&nbsp;');

        $testArray = [
            "\xF0\x90\x8C\xBC" => 66364,
            '中'                => 20013,
            '₧'                => 8359,
            'κ'                => 954,
            'ö'                => 246,
            'ñ'                => 241,
            $nbsp              => 160,
            '{'                => 123,
            'a'                => 97,
            '&'                => 38,
            ' '                => 32,
            ''                 => 0,
        ];

        for ($i = 0; $i < 2; ++$i) { // keep this loop for simple performance tests
            if ($i === 0) {
                $this->disableNativeUtf8Support();
            } elseif ($i > 0) {
                $this->reactivateNativeUtf8Support();
            }

            foreach ($testArray as $actual => $expected) {
                static::assertSame($expected, UTF8::ord($actual));
            }
        }
    }

    public function testParseStr()
    {
        // test-string
        $str = "Iñtërnâtiôn\xE9àlizætiøn=測試&arr[]=foo+測試&arr[]=ການທົດສອບ";

        $result = UTF8::parse_str($str, $array, true);

        static::assertTrue($result);

        // bug is already reported: https://github.com/facebook/hhvm/issues/6340
        if (!\defined('HHVM_VERSION')) {
            static::assertSame('foo 測試', $array['arr'][0]);
            static::assertSame('ການທົດສອບ', $array['arr'][1]);
        }

        // bug is already reported: https://github.com/facebook/hhvm/issues/6340
        // -> mb_parse_str not parsing multidimensional array
        if (!\defined('HHVM_VERSION')) {
            static::assertSame('測試', $array['Iñtërnâtiônàlizætiøn']);
        }

        // ---

        // test-string
        $str = 'Iñtërnâtiônàlizætiøn=測試&arr[]=foo+測試&arr[]=ການທົດສອບ';

        $result = UTF8::parse_str($str, $array, false);

        static::assertTrue($result);

        // bug is already reported: https://github.com/facebook/hhvm/issues/6340
        if (!\defined('HHVM_VERSION')) {
            static::assertSame('foo 測試', $array['arr'][0]);
            static::assertSame('ການທົດສອບ', $array['arr'][1]);
        }

        // bug is already reported: https://github.com/facebook/hhvm/issues/6340
        // -> mb_parse_str not parsing multidimensional array
        if (!\defined('HHVM_VERSION')) {
            static::assertSame('測試', $array['Iñtërnâtiônàlizætiøn']);
        }

        // ---

        $str = 'foo[]=bar&test=lall';

        $foo = '123';
        $test = '';

        if (!Bootup::is_php('8.0')) {
            /** @noinspection NonSecureParseStrUsageInspection */
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            @\parse_str($str); // <- you don't need to use the second parameter, but it is more then recommended!!!

            static::assertSame($foo, [0 => 'bar']);
            static::assertSame($test, 'lall');
            static::assertSame($str, 'foo[]=bar&test=lall');
        }

        $foo = '123';
        $test = '';

        if (!Bootup::is_php('7.1')) {
            /** @noinspection NonSecureParseStrUsageInspection */
            /** @noinspection PhpParamsInspection */
            UTF8::parse_str($str, $result); // <- you need to use the second parameter!!!

            static::assertSame($foo, '123');
            static::assertSame($test, '');
            static::assertSame($str, 'foo[]=bar&test=lall');
        }

        // ---

        $str = '[]';

        $result = UTF8::parse_str($str, $array);

        // bug reported (hhvm (3.6.6~precise)): https://github.com/facebook/hhvm/issues/7247
        if (!\defined('HHVM_VERSION')) {
            static::assertFalse($result);
        }
    }

    public function testIssue94()
    {
        $input = "Montr\xE9al";
        $output = UTF8::filter($input);
        static::assertSame('Montréal', $output);
        // Expected output: Montréal
        // Real output: Montréal

        $input = "Quantit\xE9";
        $output = UTF8::filter($input);
        static::assertSame('Quantité', $output);
        // Expected output: Quantité
        // Real output: Quantit�
    }

    public function testRange()
    {
        // --- INT

        $expected = ['a', 'b', 'c', 'd'];
        static::assertSame($expected, UTF8::range(97, 100, false));

        // --- ISO chars

        $expected = ['a', 'b', 'c', 'd'];
        static::assertSame($expected, UTF8::range('a', 'd', false));

        $expected = \range('A', 'Z');
        static::assertSame($expected, UTF8::range('A', 'Z', false));
        static::assertCount(0, UTF8::range('a', ''));

        // --- UTF-8 chars

        $expected = ['κ', 'ι', 'θ', 'η', 'ζ'];
        static::assertSame($expected, UTF8::range('κ', 'ζ', false, 'UTF8'));
        static::assertCount(0, UTF8::range('κ', ''));

        // --- UTF-8 chars

        $expected = ['κ', 'θ', 'ζ'];
        static::assertSame($expected, UTF8::range('κ', 'ζ', false, 'UTF8', 2));
        static::assertCount(0, UTF8::range('κ', ''));

        // --- code points

        $expected = ['₧', '₨', '₩'];
        static::assertSame($expected, UTF8::range(8359, 8361));

        // --- HEX

        $expected = [' ', '!', '"', '#'];
        static::assertSame($expected, UTF8::range("\x20", "\x23"));
    }

    public function testRawurldecode()
    {
        $testArray = [
            'W%F6bse'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            => 'Wöbse',
            'Ã'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  => 'Ã',
            'Ã¤'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 => 'ä',
            ' '                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  => ' ',
            ''                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   => '',
            "\n"                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 => "\n",
            "\u00ed"                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             => 'í',
            'tes%20öäü%20\u00edtest+test'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        => 'tes öäü ítest+test',
            'test+test@foo.bar'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  => 'test+test@foo.bar',
            'con%5cu00%366irm'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   => 'confirm',
            '%3A%2F%2F%252567%252569%252573%252574'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              => '://gist',
            '%253A%252F%252F%25252567%25252569%25252573%25252574'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                => '://gist',
            "tes%20öäü%20\u00edtest"                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             => 'tes öäü ítest',
            'Düsseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         => 'Düsseldorf',
            'Duesseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        => 'Duesseldorf',
            'D&#252;sseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    => 'Düsseldorf',
            'D%FCsseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       => 'Düsseldorf',
            'D&#xFC;sseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    => 'Düsseldorf',
            'D%26%23xFC%3Bsseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              => 'Düsseldorf',
            'DÃ¼sseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        => 'Düsseldorf',
            'D%C3%BCsseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    => 'Düsseldorf',
            'D%C3%83%C2%BCsseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              => 'Düsseldorf',
            'D%25C3%2583%25C2%25BCsseldorf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      => 'Düsseldorf',
            '<strong>D&#252;sseldorf</strong>'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   => '<strong>Düsseldorf</strong>',
            'Hello%2BWorld%2B%253E%2Bhow%2Bare%2Byou%253F'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       => 'Hello+World+>+how+are+you?',
            '%e7%ab%a0%e5%ad%90%e6%80%a1'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        => '章子怡',
            'Fran%c3%a7ois Truffaut'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             => 'François Truffaut',
            '%e1%83%a1%e1%83%90%e1%83%a5%e1%83%90%e1%83%a0%e1%83%97%e1%83%95%e1%83%94%e1%83%9a%e1%83%9d'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         => 'საქართველო',
            '%25e1%2583%25a1%25e1%2583%2590%25e1%2583%25a5%25e1%2583%2590%25e1%2583%25a0%25e1%2583%2597%25e1%2583%2595%25e1%2583%2594%25e1%2583%259a%25e1%2583%259d'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             => 'საქართველო',
            '%2525e1%252583%2525a1%2525e1%252583%252590%2525e1%252583%2525a5%2525e1%252583%252590%2525e1%252583%2525a0%2525e1%252583%252597%2525e1%252583%252595%2525e1%252583%252594%2525e1%252583%25259a%2525e1%252583%25259d'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 => 'საქართველო',
            'Bj%c3%b6rk Gu%c3%b0mundsd%c3%b3ttir'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                => 'Björk Guðmundsdóttir',
            '%e5%ae%ae%e5%b4%8e%e3%80%80%e9%a7%bf'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               => '宮崎　駿',
            '%u7AE0%u5B50%u6021'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 => '章子怡',
            '%u0046%u0072%u0061%u006E%u00E7%u006F%u0069%u0073%u0020%u0054%u0072%u0075%u0066%u0066%u0061%u0075%u0074'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             => 'François Truffaut',
            '%u10E1%u10D0%u10E5%u10D0%u10E0%u10D7%u10D5%u10D4%u10DA%u10DD'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       => 'საქართველო',
            '%u0042%u006A%u00F6%u0072%u006B%u0020%u0047%u0075%u00F0%u006D%u0075%u006E%u0064%u0073%u0064%u00F3%u0074%u0074%u0069%u0072'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           => 'Björk Guðmundsdóttir',
            '%u5BAE%u5D0E%u3000%u99FF'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           => '宮崎　駿',
            '&#31456;&#23376;&#24609;'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           => '章子怡',
            '&#70;&#114;&#97;&#110;&#231;&#111;&#105;&#115;&#32;&#84;&#114;&#117;&#102;&#102;&#97;&#117;&#116;'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  => 'François Truffaut',
            '&#4321;&#4304;&#4325;&#4304;&#4320;&#4311;&#4309;&#4308;&#4314;&#4317;'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             => 'საქართველო',
            '&#66;&#106;&#246;&#114;&#107;&#32;&#71;&#117;&#240;&#109;&#117;&#110;&#100;&#115;&#100;&#243;&#116;&#116;&#105;&#114;'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              => 'Björk Guðmundsdóttir',
            '&#23470;&#23822;&#12288;&#39423;'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   => '宮崎　駿',
            'https://foo.bar/tpl_preview.php?pid=122&json=%7B%22recipe_id%22%3A-1%2C%22recipe_created%22%3A%22%22%2C%22recipe_title%22%3A%22vxcvxc%22%2C%22recipe_description%22%3A%22%22%2C%22recipe_yield%22%3A0%2C%22recipe_prepare_time%22%3A0%2C%22recipe_image%22%3A%22%22%2C%22recipe_legal%22%3A0%2C%22recipe_live%22%3A0%2C%22recipe_user_guid%22%3A%22%22%2C%22recipe_category_id%22%3A%5B%5D%2C%22recipe_category_name%22%3A%5B%5D%2C%22recipe_variety_id%22%3A%5B%5D%2C%22recipe_variety_name%22%3A%5B%5D%2C%22recipe_tag_id%22%3A%5B%5D%2C%22recipe_tag_name%22%3A%5B%5D%2C%22recipe_instruction_id%22%3A%5B%5D%2C%22recipe_instruction_text%22%3A%5B%5D%2C%22recipe_ingredient_id%22%3A%5B%5D%2C%22recipe_ingredient_name%22%3A%5B%5D%2C%22recipe_ingredient_amount%22%3A%5B%5D%2C%22recipe_ingredient_unit%22%3A%5B%5D%2C%22formMatchingArray%22%3A%7B%22unites%22%3A%5B%22Becher%22%2C%22Beete%22%2C%22Beutel%22%2C%22Blatt%22%2C%22Bl%5Cu00e4tter%22%2C%22Bund%22%2C%22B%5Cu00fcndel%22%2C%22cl%22%2C%22cm%22%2C%22dicke%22%2C%22dl%22%2C%22Dose%22%2C%22Dose%5C%2Fn%22%2C%22d%5Cu00fcnne%22%2C%22Ecke%28n%29%22%2C%22Eimer%22%2C%22einige%22%2C%22einige+Stiele%22%2C%22EL%22%2C%22EL%2C+geh%5Cu00e4uft%22%2C%22EL%2C+gestr.%22%2C%22etwas%22%2C%22evtl.%22%2C%22extra%22%2C%22Fl%5Cu00e4schchen%22%2C%22Flasche%22%2C%22Flaschen%22%2C%22g%22%2C%22Glas%22%2C%22Gl%5Cu00e4ser%22%2C%22gr.+Dose%5C%2Fn%22%2C%22gr.+Fl.%22%2C%22gro%5Cu00dfe%22%2C%22gro%5Cu00dfen%22%2C%22gro%5Cu00dfer%22%2C%22gro%5Cu00dfes%22%2C%22halbe%22%2C%22Halm%28e%29%22%2C%22Handvoll%22%2C%22K%5Cu00e4stchen%22%2C%22kg%22%2C%22kl.+Bund%22%2C%22kl.+Dose%5C%2Fn%22%2C%22kl.+Glas%22%2C%22kl.+Kopf%22%2C%22kl.+Scheibe%28n%29%22%2C%22kl.+St%5Cu00fcck%28e%29%22%2C%22kl.Flasche%5C%2Fn%22%2C%22kleine%22%2C%22kleinen%22%2C%22kleiner%22%2C%22kleines%22%2C%22Knolle%5C%2Fn%22%2C%22Kopf%22%2C%22K%5Cu00f6pfe%22%2C%22K%5Cu00f6rner%22%2C%22Kugel%22%2C%22Kugel%5C%2Fn%22%2C%22Kugeln%22%2C%22Liter%22%2C%22m.-gro%5Cu00dfe%22%2C%22m.-gro%5Cu00dfer%22%2C%22m.-gro%5Cu00dfes%22%2C%22mehr%22%2C%22mg%22%2C%22ml%22%2C%22Msp.%22%2C%22n.+B.%22%2C%22Paar%22%2C%22Paket%22%2C%22Pck.%22%2C%22Pkt.%22%2C%22Platte%5C%2Fn%22%2C%22Port.%22%2C%22Prise%28n%29%22%2C%22Prisen%22%2C%22Prozent+%25%22%2C%22Riegel%22%2C%22Ring%5C%2Fe%22%2C%22Rippe%5C%2Fn%22%2C%22Rolle%28n%29%22%2C%22Sch%5Cu00e4lchen%22%2C%22Scheibe%5C%2Fn%22%2C%22Schuss%22%2C%22Spritzer%22%2C%22Stange%5C%2Fn%22%2C%22St%5Cu00e4ngel%22%2C%22Stiel%5C%2Fe%22%2C%22Stiele%22%2C%22St%5Cu00fcck%28e%29%22%2C%22Tafel%22%2C%22Tafeln%22%2C%22Tasse%22%2C%22Tasse%5C%2Fn%22%2C%22Teil%5C%2Fe%22%2C%22TL%22%2C%22TL+%28geh%5Cu00e4uft%29%22%2C%22TL+%28gestr.%29%22%2C%22Topf%22%2C%22Tropfen%22%2C%22Tube%5C%2Fn%22%2C%22T%5Cu00fcte%5C%2Fn%22%2C%22viel%22%2C%22wenig%22%2C%22W%5Cu00fcrfel%22%2C%22Wurzel%22%2C%22Wurzel%5C%2Fn%22%2C%22Zehe%5C%2Fn%22%2C%22Zweig%5C%2Fe%22%5D%2C%22yield%22%3A%7B%221%22%3A%221+Portion%22%2C%222%22%3A%222+Portionen%22%2C%223%22%3A%223+Portionen%22%2C%224%22%3A%224+Portionen%22%2C%225%22%3A%225+Portionen%22%2C%226%22%3A%226+Portionen%22%2C%227%22%3A%227+Portionen%22%2C%228%22%3A%228+Portionen%22%2C%229%22%3A%229+Portionen%22%2C%2210%22%3A%2210+Portionen%22%2C%2211%22%3A%2211+Portionen%22%2C%2212%22%3A%2212+Portionen%22%7D%2C%22prepare_time%22%3A%7B%221%22%3A%22schnell%22%2C%222%22%3A%22mittel%22%2C%223%22%3A%22aufwendig%22%7D%2C%22category%22%3A%7B%221%22%3A%22Vorspeise%22%2C%222%22%3A%22Suppe%22%2C%223%22%3A%22Salat%22%2C%224%22%3A%22Hauptspeise%22%2C%225%22%3A%22Beilage%22%2C%226%22%3A%22Nachtisch%5C%2FDessert%22%2C%227%22%3A%22Getr%5Cu00e4nke%22%2C%228%22%3A%22B%5Cu00fcffet%22%2C%229%22%3A%22Fr%5Cu00fchst%5Cu00fcck%5C%2FBrunch%22%7D%2C%22variety%22%3A%7B%221%22%3A%22Basmati+Reis%22%2C%222%22%3A%22Basmati+%26amp%3B+Wild+Reis%22%2C%223%22%3A%22R%5Cu00e4ucherreis%22%2C%224%22%3A%22Jasmin+Reis%22%2C%225%22%3A%221121+Basmati+Wunderreis%22%2C%226%22%3A%22Spitzen+Langkorn+Reis%22%2C%227%22%3A%22Wildreis%22%2C%228%22%3A%22Naturreis%22%2C%229%22%3A%22Sushi+Reis%22%7D%2C%22tag--ingredient%22%3A%7B%221%22%3A%22Eier%22%2C%222%22%3A%22Gem%5Cu00fcse%22%2C%223%22%3A%22Getreide%22%2C%224%22%3A%22Fisch%22%2C%225%22%3A%22Fleisch%22%2C%226%22%3A%22Meeresfr%5Cu00fcchte%22%2C%227%22%3A%22Milchprodukte%22%2C%228%22%3A%22Obst%22%2C%229%22%3A%22Salat%22%7D%2C%22tag--preparation%22%3A%7B%2210%22%3A%22Backen%22%2C%2211%22%3A%22Blanchieren%22%2C%2212%22%3A%22Braten%5C%2FSchmoren%22%2C%2213%22%3A%22D%5Cu00e4mpfen%5C%2FD%5Cu00fcnsten%22%2C%2214%22%3A%22Einmachen%22%2C%2215%22%3A%22Frittieren%22%2C%2216%22%3A%22Gratinieren%5C%2F%5Cu00dcberbacken%22%2C%2217%22%3A%22Grillen%22%2C%2218%22%3A%22Kochen%22%7D%2C%22tag--kitchen%22%3A%7B%2219%22%3A%22Afrikanisch%22%2C%2220%22%3A%22Alpenk%5Cu00fcche%22%2C%2221%22%3A%22Asiatisch%22%2C%2222%22%3A%22Deutsch+%28regional%29%22%2C%2223%22%3A%22Franz%5Cu00f6sisch%22%2C%2224%22%3A%22Mediterran%22%2C%2225%22%3A%22Orientalisch%22%2C%2226%22%3A%22Osteurop%5Cu00e4isch%22%2C%2227%22%3A%22Skandinavisch%22%2C%2228%22%3A%22S%5Cu00fcdamerikanisch%22%2C%2229%22%3A%22US-Amerikanisch%22%2C%2230%22%3A%22%22%7D%2C%22tag--difficulty%22%3A%7B%2231%22%3A%22Einfach%22%2C%2232%22%3A%22Mittelschwer%22%2C%2233%22%3A%22Anspruchsvoll%22%7D%2C%22tag--feature%22%3A%7B%2234%22%3A%22Gut+vorzubereiten%22%2C%2235%22%3A%22Kalorienarm+%5C%2F+leicht%22%2C%2236%22%3A%22Klassiker%22%2C%2237%22%3A%22Preiswert%22%2C%2238%22%3A%22Raffiniert%22%2C%2239%22%3A%22Vegetarisch+%5C%2F+Vegan%22%2C%2240%22%3A%22Vitaminreich%22%2C%2241%22%3A%22Vollwert%22%2C%2242%22%3A%22%22%7D%2C%22tag%22%3A%7B%221%22%3A%22Eier%22%2C%222%22%3A%22Gem%5Cu00fcse%22%2C%223%22%3A%22Getreide%22%2C%224%22%3A%22Fisch%22%2C%225%22%3A%22Fleisch%22%2C%226%22%3A%22Meeresfr%5Cu00fcchte%22%2C%227%22%3A%22Milchprodukte%22%2C%228%22%3A%22Obst%22%2C%229%22%3A%22Salat%22%2C%2210%22%3A%22Backen%22%2C%2211%22%3A%22Blanchieren%22%2C%2212%22%3A%22Braten%5C%2FSchmoren%22%2C%2213%22%3A%22D%5Cu00e4mpfen%5C%2FD%5Cu00fcnsten%22%2C%2214%22%3A%22Einmachen%22%2C%2215%22%3A%22Frittieren%22%2C%2216%22%3A%22Gratinieren%5C%2F%5Cu00dcberbacken%22%2C%2217%22%3A%22Grillen%22%2C%2218%22%3A%22Kochen%22%2C%2219%22%3A%22Afrikanisch%22%2C%2220%22%3A%22Alpenk%5Cu00fcche%22%2C%2221%22%3A%22Asiatisch%22%2C%2222%22%3A%22Deutsch+%28regional%29%22%2C%2223%22%3A%22Franz%5Cu00f6sisch%22%2C%2224%22%3A%22Mediterran%22%2C%2225%22%3A%22Orientalisch%22%2C%2226%22%3A%22Osteurop%5Cu00e4isch%22%2C%2227%22%3A%22Skandinavisch%22%2C%2228%22%3A%22S%5Cu00fcdamerikanisch%22%2C%2229%22%3A%22US-Amerikanisch%22%2C%2230%22%3A%22%22%2C%2231%22%3A%22Einfach%22%2C%2232%22%3A%22Mittelschwer%22%2C%2233%22%3A%22Anspruchsvoll%22%2C%2234%22%3A%22Gut+vorzubereiten%22%2C%2235%22%3A%22Kalorienarm+%5C%2F+leicht%22%2C%2236%22%3A%22Klassiker%22%2C%2237%22%3A%22Preiswert%22%2C%2238%22%3A%22Raffiniert%22%2C%2239%22%3A%22Vegetarisch+%5C%2F+Vegan%22%2C%2240%22%3A%22Vitaminreich%22%2C%2241%22%3A%22Vollwert%22%2C%2242%22%3A%22%22%7D%7D%2C%22errorArray%22%3A%7B%22recipe_prepare_time%22%3A%22error%22%2C%22recipe_yield%22%3A%22error%22%2C%22recipe_category_name%22%3A%22error%22%2C%22recipe_tag_name%22%3A%22error%22%2C%22recipe_instruction_text%22%3A%22error%22%2C%22recipe_ingredient_name%22%3A%22error%22%7D%2C%22errorMessage%22%3A%22Bitte+f%5Cu00fclle+die+rot+markierten+Felder+korrekt+aus.%22%2C%22db%22%3A%7B%22query_count%22%3A20%7D%7D' => 'https://foo.bar/tpl_preview.php?pid=122&json={"recipe_id":-1,"recipe_created":"","recipe_title":"vxcvxc","recipe_description":"","recipe_yield":0,"recipe_prepare_time":0,"recipe_image":"","recipe_legal":0,"recipe_live":0,"recipe_user_guid":"","recipe_category_id":[],"recipe_category_name":[],"recipe_variety_id":[],"recipe_variety_name":[],"recipe_tag_id":[],"recipe_tag_name":[],"recipe_instruction_id":[],"recipe_instruction_text":[],"recipe_ingredient_id":[],"recipe_ingredient_name":[],"recipe_ingredient_amount":[],"recipe_ingredient_unit":[],"formMatchingArray":{"unites":["Becher","Beete","Beutel","Blatt","Blätter","Bund","Bündel","cl","cm","dicke","dl","Dose","Dose\/n","dünne","Ecke(n)","Eimer","einige","einige+Stiele","EL","EL,+gehäuft","EL,+gestr.","etwas","evtl.","extra","Fläschchen","Flasche","Flaschen","g","Glas","Gläser","gr.+Dose\/n","gr.+Fl.","große","großen","großer","großes","halbe","Halm(e)","Handvoll","Kästchen","kg","kl.+Bund","kl.+Dose\/n","kl.+Glas","kl.+Kopf","kl.+Scheibe(n)","kl.+Stück(e)","kl.Flasche\/n","kleine","kleinen","kleiner","kleines","Knolle\/n","Kopf","Köpfe","Körner","Kugel","Kugel\/n","Kugeln","Liter","m.-große","m.-großer","m.-großes","mehr","mg","ml","Msp.","n.+B.","Paar","Paket","Pck.","Pkt.","Platte\/n","Port.","Prise(n)","Prisen","Prozent+%","Riegel","Ring\/e","Rippe\/n","Rolle(n)","Schälchen","Scheibe\/n","Schuss","Spritzer","Stange\/n","Stängel","Stiel\/e","Stiele","Stück(e)","Tafel","Tafeln","Tasse","Tasse\/n","Teil\/e","TL","TL+(gehäuft)","TL+(gestr.)","Topf","Tropfen","Tube\/n","Tüte\/n","viel","wenig","Würfel","Wurzel","Wurzel\/n","Zehe\/n","Zweig\/e"],"yield":{"1":"1+Portion","2":"2+Portionen","3":"3+Portionen","4":"4+Portionen","5":"5+Portionen","6":"6+Portionen","7":"7+Portionen","8":"8+Portionen","9":"9+Portionen","10":"10+Portionen","11":"11+Portionen","12":"12+Portionen"},"prepare_time":{"1":"schnell","2":"mittel","3":"aufwendig"},"category":{"1":"Vorspeise","2":"Suppe","3":"Salat","4":"Hauptspeise","5":"Beilage","6":"Nachtisch\/Dessert","7":"Getränke","8":"Büffet","9":"Frühstück\/Brunch"},"variety":{"1":"Basmati+Reis","2":"Basmati+&+Wild+Reis","3":"Räucherreis","4":"Jasmin+Reis","5":"1121+Basmati+Wunderreis","6":"Spitzen+Langkorn+Reis","7":"Wildreis","8":"Naturreis","9":"Sushi+Reis"},"tag--ingredient":{"1":"Eier","2":"Gemüse","3":"Getreide","4":"Fisch","5":"Fleisch","6":"Meeresfrüchte","7":"Milchprodukte","8":"Obst","9":"Salat"},"tag--preparation":{"10":"Backen","11":"Blanchieren","12":"Braten\/Schmoren","13":"Dämpfen\/Dünsten","14":"Einmachen","15":"Frittieren","16":"Gratinieren\/Überbacken","17":"Grillen","18":"Kochen"},"tag--kitchen":{"19":"Afrikanisch","20":"Alpenküche","21":"Asiatisch","22":"Deutsch+(regional)","23":"Französisch","24":"Mediterran","25":"Orientalisch","26":"Osteuropäisch","27":"Skandinavisch","28":"Südamerikanisch","29":"US-Amerikanisch","30":""},"tag--difficulty":{"31":"Einfach","32":"Mittelschwer","33":"Anspruchsvoll"},"tag--feature":{"34":"Gut+vorzubereiten","35":"Kalorienarm+\/+leicht","36":"Klassiker","37":"Preiswert","38":"Raffiniert","39":"Vegetarisch+\/+Vegan","40":"Vitaminreich","41":"Vollwert","42":""},"tag":{"1":"Eier","2":"Gemüse","3":"Getreide","4":"Fisch","5":"Fleisch","6":"Meeresfrüchte","7":"Milchprodukte","8":"Obst","9":"Salat","10":"Backen","11":"Blanchieren","12":"Braten\/Schmoren","13":"Dämpfen\/Dünsten","14":"Einmachen","15":"Frittieren","16":"Gratinieren\/Überbacken","17":"Grillen","18":"Kochen","19":"Afrikanisch","20":"Alpenküche","21":"Asiatisch","22":"Deutsch+(regional)","23":"Französisch","24":"Mediterran","25":"Orientalisch","26":"Osteuropäisch","27":"Skandinavisch","28":"Südamerikanisch","29":"US-Amerikanisch","30":"","31":"Einfach","32":"Mittelschwer","33":"Anspruchsvoll","34":"Gut+vorzubereiten","35":"Kalorienarm+\/+leicht","36":"Klassiker","37":"Preiswert","38":"Raffiniert","39":"Vegetarisch+\/+Vegan","40":"Vitaminreich","41":"Vollwert","42":""}},"errorArray":{"recipe_prepare_time":"error","recipe_yield":"error","recipe_category_name":"error","recipe_tag_name":"error","recipe_instruction_text":"error","recipe_ingredient_name":"error"},"errorMessage":"Bitte+fülle+die+rot+markierten+Felder+korrekt+aus.","db":{"query_count":20}}',
            '<a href="&#38&#35&#49&#48&#54&#38&#35&#57&#55&#38&#35&#49&#49&#56&#38&#35&#57&#55&#38&#35&#49&#49&#53&#38&#35&#57&#57&#38&#35&#49&#49&#52&#38&#35&#49&#48&#53&#38&#35&#49&#49&#50&#38&#35&#49&#49&#54&#38&#35&#53&#56&#38&#35&#57&#57&#38&#35&#49&#49&#49&#38&#35&#49&#49&#48&#38&#35&#49&#48&#50&#38&#35&#49&#48&#53&#38&#35&#49&#49&#52&#38&#35&#49&#48&#57&#38&#35&#52&#48&#38&#35&#52&#57&#38&#35&#52&#49">Clickhere</a>'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       => '<a href="javascript:confirm(1)">Clickhere</a>',
            '🐶 | 💩 | 🐱 | 🐸 | 🌀 | ❤ | &#x267F; | &#x26CE; | '                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     => '🐶 | 💩 | 🐱 | 🐸 | 🌀 | ❤ | ♿ | ⛎ | ',
            // view-source:https://twitter.github.io/twemoji/preview.html
        ];

        foreach ($testArray as $before => $after) {
            static::assertSame($after, UTF8::rawurldecode($before), 'testing: ' . $before);
        }
    }

    public function testRemoveBom()
    {
        $testBom = [
            "\xEF\xBB\xBFΜπορώ να φάω σπασμένα γυαλιά χωρίς να πάθω τίποτα",
            "\xFE\xFFΜπορώ να φάω σπασμένα γυαλιά χωρίς να πάθω τίποτα",
            "\xFF\xFEΜπορώ να φάω σπασμένα γυαλιά χωρίς να πάθω τίποτα",
            "\x00\x00\xFE\xFFΜπορώ να φάω σπασμένα γυαλιά χωρίς να πάθω τίποτα",
            "\xFF\xFE\x00\x00Μπορώ να φάω σπασμένα γυαλιά χωρίς να πάθω τίποτα",
        ];

        foreach ($testBom as $count => &$test) {
            $test = UTF8::remove_bom($test);

            static::assertSame(
                'Μπορώ να φάω σπασμένα γυαλιά χωρίς να πάθω τίποτα',
                $test,
                'error by ' . $count
            );

            $test = UTF8::add_bom_to_string($test);
            static::assertTrue(UTF8::string_has_bom($test));
        }
        unset($test);
    }

    public function testRemoveDuplicates()
    {
        $testArray = [
            'öäü-κόσμεκόσμε-äöü' => [
                'öäü-κόσμε-äöü' => 'κόσμε',
            ],
            'äöüäöüäöü-κόσμεκόσμε' => [
                'äöü-κόσμε' => [
                    'äöü',
                    'κόσμε',
                ],
            ],
        ];

        foreach ($testArray as $actual => $data) {
            foreach ($data as $expected => $filter) {
                static::assertSame($expected, UTF8::remove_duplicates($actual, $filter));
            }
        }
    }

    public function testRemoveInvisibleCharacters()
    {
        $testArray = [
            "κόσ\0με"                                                                          => 'κόσμε',
            "Κόσμε\x20"                                                                        => 'Κόσμε ',
            "öäü-κόσμ\x0εκόσμε-äöü"                                                            => 'öäü-κόσμεκόσμε-äöü',
            'öäü-κόσμεκόσμε-äöüöäü-κόσμεκόσμε-äöü'                                             => 'öäü-κόσμεκόσμε-äöüöäü-κόσμεκόσμε-äöü',
            "äöüäöüäöü-κόσμεκόσμεäöüäöüäöü\xe1\x9a\x80κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε"          => 'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε',
            'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-Κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε' => 'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-Κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε',
            '  '                                                                               => '  ',
            ''                                                                                 => '',
        ];

        $count = 0;
        foreach ($testArray as $before => $after) {
            static::assertSame($after, UTF8::remove_invisible_characters($before), 'error by ' . $before);

            if ($before !== $after) {
                if (!UTF8::is_printable($before)) {
                    ++$count;
                }
            }
        }
        static::assertSame(2, $count);

        static::assertSame('κόσ?με 	%00 | tes%20öäü%20\u00edtest', UTF8::remove_invisible_characters("κόσ\0με 	%00 | tes%20öäü%20\u00edtest", false, '?'));
        static::assertSame('κόσμε 	 | tes%20öäü%20\u00edtest', UTF8::remove_invisible_characters("κόσ\0με 	%00 | tes%20öäü%20\u00edtest", true, ''));
    }

    public function testReplaceDiamondQuestionMark()
    {
        $tests = [
            ''                                                                         => '',
            ' '                                                                        => ' ',
            '�'                                                                        => '',
            '中文空白 �'                                                                   => '中文空白 ',
            "<ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a>" => "<ㅡㅡ></ㅡㅡ><div></div><input type='email' name='user[email]' /><a>wtf</a>",
            'DÃ¼�sseldorf'                                                             => 'DÃ¼sseldorf',
            'Abcdef'                                                                   => 'Abcdef',
            "\xC0\x80foo|&#65533;"                                                     => 'foo|&#65533;',
        ];

        $counter = 0;
        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::replace_diamond_question_mark($before, ''), 'tested: ' . $before . ' | counter: ' . $counter);
            ++$counter;
        }

        // ---

        $tests = [
            "Iñtërnâtiôn\xe9àlizætiøn" => 'Iñtërnâtiônàlizætiøn',
            // invalid UTF-8 string
            "Iñtërnâtiônàlizætiøn\xfc\xa1\xa1\xa1\xa1\xa1Iñtërnâtiônàlizætiøn" => 'IñtërnâtiônàlizætiønIñtërnâtiônàlizætiøn',
            // invalid six octet sequence
            "Iñtërnâtiônàlizætiøn\xf0\x28\x8c\xbcIñtërnâtiônàlizætiøn" => 'Iñtërnâtiônàlizætiøn(Iñtërnâtiônàlizætiøn',
            // invalid four octet sequence
            "Iñtërnâtiônàlizætiøn \xc3\x28 Iñtërnâtiônàlizætiøn" => 'Iñtërnâtiônàlizætiøn ( Iñtërnâtiônàlizætiøn',
            // invalid two octet sequence
            "this is an invalid char '\xe9' here" => "this is an invalid char '' here",
            // invalid ASCII string
            "Iñtërnâtiônàlizætiøn\xa0\xa1Iñtërnâtiônàlizætiøn" => 'IñtërnâtiônàlizætiønIñtërnâtiônàlizætiøn',
            // invalid id between two and three
            "Iñtërnâtiônàlizætiøn\xf8\xa1\xa1\xa1\xa1Iñtërnâtiônàlizætiøn" => 'IñtërnâtiônàlizætiønIñtërnâtiônàlizætiøn',
            //  invalid five octet sequence
            "Iñtërnâtiônàlizætiøn\xe2\x82\x28Iñtërnâtiônàlizætiøn" => 'Iñtërnâtiônàlizætiøn(Iñtërnâtiônàlizætiøn',
            // invalid three octet sequence third
            "Iñtërnâtiônàlizætiøn\xe2\x28\xa1Iñtërnâtiônàlizætiøn" => 'Iñtërnâtiônàlizætiøn(Iñtërnâtiônàlizætiøn',
            // invalid three octet sequence second
        ];

        $counter = 0;
        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::replace_diamond_question_mark($before, ''), 'tested: ' . $before . ' | counter: ' . $counter);
            ++$counter;
        }

        // ---

        if (UTF8::mbstring_loaded()) { // only with "mbstring"
            static::assertSame('Iñtërnâtiônàlizætiøn??Iñtërnâtiônàlizætiøn', UTF8::replace_diamond_question_mark("Iñtërnâtiônàlizætiøn\xa0\xa1Iñtërnâtiônàlizætiøn", '?', true));
        } else {
            static::assertSame('IñtërnâtiônàlizætiønIñtërnâtiônàlizætiøn', UTF8::replace_diamond_question_mark("Iñtërnâtiônàlizætiøn\xa0\xa1Iñtërnâtiônàlizætiøn", '?', true));
        }

        // ---

        static::assertSame("Iñtërnâtiônàlizætiøn\xa0\xa1Iñtërnâtiônàlizætiøn", UTF8::replace_diamond_question_mark("Iñtërnâtiônàlizætiøn\xa0\xa1Iñtërnâtiônàlizætiøn", '?', false));
    }

    /**
     * @return void
     */
    public function testIsUrl()
    {
        $testArray = [
            'test'                                                                                            => false,
            'öäü'                                                                                             => false,
            'lall'                                                                                            => false,
            'foo.jpg'                                                                                         => false,
            'http://test.de'                                                                                  => true,
            'http://test.de/lall'                                                                             => true,
            'http:/google.de'                                                                                 => false,
            'http/öäü.de'                                                                                     => false,
            'http//öäü.de'                                                                                    => false,
            'https//öäü.de'                                                                                   => false,
            'https://öäü.de'                                                                                  => true,
            '//google.de'                                                                                     => false,
            'http://heise.de'                                                                                 => true,
            'http://heise.de/foo/lall.jpg'                                                                    => true,
            'https://test.de'                                                                                 => true,
            'http://test.localhost/'                                                                          => true,
            'https://lall123.localhost/'                                                                      => true,
            'http://test_test.localhost/test'                                                                 => true,
            'http://test_öäü.localhost'                                                                       => true,
            'http://localhost/test'                                                                           => true,
            'https://localhost/'                                                                              => true,
            'http://testestestest.localhost'                                                                  => true,
            'http://www.bbc.co.uk'                                                                            => true,
            'http://www.müller.de'                                                                            => true,
            'http://foobar.com/hlk/lall.php?tracking=foobar&email=foo@bar.com&winner=1&winnercomment=foo-öäü' => true,
            'http://.com'                                                                                     => false,
            'foo://bar'                                                                                       => false,
            'javascript://test%0Aalert(321)'                                                                  => false,
            'phar:///home/lmoelleken/hackme.php'                                                              => false,
            ' '                                                                                               => false,
            ''                                                                                                => false,
            'file:///etc/passwd'                                                                              => false,
            'file://hack'                                                                                     => false,
            '/etc/passwd'                                                                                     => false,
            'https://127.0.0.1/'                                                                              => true,
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::is_url($testString), $testString);
        }

        // ---

        $testArray = [
            'test'                                                                                            => false,
            'öäü'                                                                                             => false,
            'lall'                                                                                            => false,
            'foo.jpg'                                                                                         => false,
            'http://test.de'                                                                                  => true,
            'http://test.de/lall'                                                                             => true,
            'http:/google.de'                                                                                 => false,
            'http/öäü.de'                                                                                     => false,
            'http//öäü.de'                                                                                    => false,
            'https//öäü.de'                                                                                   => false,
            'https://öäü.de'                                                                                  => true,
            '//google.de'                                                                                     => false,
            'http://heise.de'                                                                                 => true,
            'http://heise.de/foo/lall.jpg'                                                                    => true,
            'https://test.de'                                                                                 => true,
            'http://test.localhost/'                                                                          => false,
            'https://lall123.localhost/'                                                                      => false,
            'http://test_test.localhost/test'                                                                 => false,
            'http://test_öäü.localhost'                                                                       => false,
            'http://localhost/test'                                                                           => false,
            'https://localhost/'                                                                              => false,
            'http://testestestest.localhost'                                                                  => false,
            'http://www.bbc.co.uk'                                                                            => true,
            'http://www.müller.de'                                                                            => true,
            'http://foobar.com/hlk/lall.php?tracking=foobar&email=foo@bar.com&winner=1&winnercomment=foo-öäü' => true,
            'http://.com'                                                                                     => false,
            'foo://bar'                                                                                       => false,
            'javascript://test%0Aalert(321)'                                                                  => false,
            'phar:///home/lmoelleken/hackme.php'                                                              => false,
            ' '                                                                                               => false,
            ''                                                                                                => false,
            'file:///etc/passwd'                                                                              => false,
            'file://hack'                                                                                     => false,
            '/etc/passwd'                                                                                     => false,
            'https://127.0.0.1/'                                                                              => false,
            'https://::1/'                                                                                    => false,
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::is_url($testString, true), $testString);
        }
    }

    public function testRtrim()
    {
        $tests = [
            '-ABC-中文空白-  '        => '-ABC-中文空白-',
            '- ÖÄÜ-             ' => '- ÖÄÜ-',
            'öäü'                 => 'öäü',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::rtrim($before));
        }

        static::assertSame('Iñtërnâtiônàlizæti', UTF8::rtrim('Iñtërnâtiônàlizætiø', 'ø'));
        static::assertSame('Iñtërnâtiônàlizætiøn ', UTF8::rtrim('Iñtërnâtiônàlizætiøn ', 'ø'));
        static::assertSame('', UTF8::rtrim(''));
        static::assertSame("Iñtërnâtiônàlizætiø\n", UTF8::rtrim("Iñtërnâtiônàlizætiø\nø", 'ø'));
        static::assertSame('Iñtërnâtiônàlizæti', UTF8::rtrim("Iñtërnâtiônàlizætiø\nø", "\nø"));
        static::assertSame("\xe2\x80\x83\x20#string#", UTF8::rtrim("\xe2\x80\x83\x20#string#\xc2\xa0\xe1\x9a\x80"));
    }

    public function testSingleChrHtmlEncode()
    {
        $testArray = [
            '{' => '&#123;',
            '中' => '&#20013;',
            'κ' => '&#954;',
            'ö' => '&#246;',
            ''  => '',
        ];

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::single_chr_html_encode($actual));
        }

        static::assertSame('a', UTF8::single_chr_html_encode('a', true));

        static::assertSame('&#246;', UTF8::single_chr_html_encode('ö', false, 'ISO'));
        static::assertSame('&#246;', UTF8::single_chr_html_encode('ö', false, 'UTF8'));
    }

    public function testSplit()
    {
        $oldSupportArray = null;
        for ($i = 0; $i <= 2; ++$i) { // keep this loop for simple performance tests
            if ($i === 0) {
                $this->disableNativeUtf8Support();
            } elseif ($i > 0) {
                $this->reactivateNativeUtf8Support();
            }

            static::assertSame(
                [
                    '中',
                    '文',
                    '空',
                    '白',
                ],
                UTF8::str_split('中文空白')
            );
            static::assertSame(
                [
                    '中文',
                    '空白',
                ],
                UTF8::str_split('中文空白', 2)
            );
            static::assertSame(['中文空白'], UTF8::str_split('中文空白', 4));
            static::assertSame(['中文空白'], UTF8::str_split('中文空白', 8));

            static::assertSame(['1234'], UTF8::str_split(1234, 8));
        }
    }

    public function testStrDetectEncoding()
    {
        $tests = [
            'に対するパッチです'                     => 'UTF-8', // ISO-2022-JP, but PHP can't detect it ...
            'ASCII'                         => 'ASCII', // ASCII
            'Abc'                           => 'ASCII', // ASCII
            'Iñtërnâtiônàlizætiøn'          => 'UTF-8', // UTF-8
            '亜 唖 娃 阿 哀 愛 挨 姶 逢 葵 茜 穐 悪 握 渥' => 'UTF-8', // EUC-JP
            'áéóú'                          => 'UTF-8', // ISO-8859-1
            '☺'                             => 'UTF-8',
            '☃'                             => 'UTF-8',
            '○●◎'                           => 'UTF-8',
            'öäü'                           => 'UTF-8', // ISO-8859-1
            ''                              => 'ASCII', // ASCII
            '1'                             => false, // binary
            \decbin(324546)                 => false, // binary
            01                              => false, // binary
        ];

        for ($i = 0; $i <= 2; ++$i) { // keep this loop for simple performance tests
            if ($i === 0) {
                $this->disableNativeUtf8Support();
            } elseif ($i > 0) {
                $this->reactivateNativeUtf8Support();
            }

            foreach ($tests as $before => $after) {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                static::assertSame($after, @UTF8::str_detect_encoding($before), 'value: ' . $before);
            }
        }

        if (\method_exists(__CLASS__, 'assertStringContainsString')) {
            $testString = \file_get_contents(__DIR__ . '/fixtures/sample-html.txt');
            static::assertStringContainsString('UTF-8', UTF8::str_detect_encoding($testString));

            $testString = \file_get_contents(__DIR__ . '/fixtures/latin.txt');
            static::assertStringContainsString('ISO-8859-1', UTF8::str_detect_encoding($testString));

            $testString = \file_get_contents(__DIR__ . '/fixtures/iso-8859-7.txt');
            static::assertStringContainsString('ISO-8859-1', UTF8::str_detect_encoding($testString)); // ?
        } else {
            $testString = \file_get_contents(__DIR__ . '/fixtures/sample-html.txt');
            static::assertContains('UTF-8', UTF8::str_detect_encoding($testString));

            $testString = \file_get_contents(__DIR__ . '/fixtures/latin.txt');
            static::assertContains('ISO-8859-1', UTF8::str_detect_encoding($testString));

            $testString = \file_get_contents(__DIR__ . '/fixtures/iso-8859-7.txt');
            static::assertContains('ISO-8859-1', UTF8::str_detect_encoding($testString)); // ?
        }
    }

    public function testStrEndsWith()
    {
        $str = 'BeginMiddleΚόσμε';

        $tests = [
            'Κόσμε' => true,
            'κόσμε' => false,
            // null    => false,
            ''  => true,
            ' ' => false,
            // false   => false,
            'ε'   => true,
            'End' => false,
            'end' => false,
        ];

        foreach ($tests as $test => $result) {
            static::assertSame($result, UTF8::str_ends_with($str, $test), 'tested: ' . $test);
        }

        if (\PHP_VERSION_ID >= 80000) {
            static::assertSame(\str_ends_with('', ''), UTF8::str_ends_with('', ''));
            static::assertSame(str_ends_with(' ', ''), UTF8::str_ends_with(' ', ''));
            static::assertSame(str_ends_with('', ' '), UTF8::str_ends_with('', ' '));
            static::assertSame(str_ends_with(' ', ' '), UTF8::str_ends_with(' ', ' '));
            static::assertSame(str_ends_with('DJ', ''), UTF8::str_ends_with('DJ', ''));
            static::assertSame(str_ends_with('DJ', ' '), UTF8::str_ends_with('DJ', ' '));
            static::assertSame(str_ends_with('', 'Σ'), UTF8::str_ends_with('', 'Σ'));
            static::assertSame(str_ends_with(' ', 'Σ'), UTF8::str_ends_with(' ', 'Σ'));
            static::assertSame(str_ends_with('DJ', ''), UTF8::str_ends_with('DJ', ''));
            static::assertSame(str_ends_with('DJ', ' '), UTF8::str_ends_with('DJ', ' '));
            static::assertSame(str_ends_with('', 'Σ'), UTF8::str_ends_with('', 'Σ'));
            static::assertSame(str_ends_with(' ', 'Σ'), UTF8::str_ends_with(' ', 'Σ'));
        }
    }

    public function testStrIEndsWith()
    {
        $str = 'BeginMiddleΚόσμε';

        $tests = [
            'Κόσμε' => true,
            'κόσμε' => true,
            ''      => true,
            ' '     => false,
            // false   => false,
            'ε'   => true,
            'End' => false,
            'end' => false,
        ];

        foreach ($tests as $test => $result) {
            static::assertSame($result, UTF8::str_iends_with($str, $test), 'tested: ' . $test);
        }
    }

    public function testStrIStartsWith()
    {
        $str = 'ΚόσμεMiddleEnd';

        $tests = [
            'Κόσμε' => true,
            'κόσμε' => true,
            ''      => true,
            ' '     => false,
            // false   => false,
            'Κ'   => true,
            'End' => false,
            'end' => false,
        ];

        foreach ($tests as $test => $result) {
            static::assertSame($result, UTF8::str_istarts_with($str, $test), 'tested: ' . $test);
        }
    }

    public function testStrLimit()
    {
        $testArray = [
            ['th...', 'this is a test', 5, '...'],
            ['this ...', 'this is öäü-foo test', 8, '...'],
            ['fòô bà', 'fòô bàř fòô', 6, ''],
            ['fòô bàř ', 'fòô bàř fòô', 8, ''],
            ['fòô bàř ', "fòô bàř fòô \x00", 8, ''],
            ['', "fòô bàř \x00fòô", 0, ''],
            ['', "fòô bàř \x00fòô", -1, ''],
            ['fòô bàř白', "fòô bàř \x00fòô", 8, '白'],
            ['白', '白白 白白', 1, ''],
            ['白白 ', '白白 白白', 3, ''],
            ['白白白', '白白白', 100, ''],
            ['', '', 1, ''],
        ];

        foreach ($testArray as $test) {
            static::assertSame($test[0], UTF8::str_limit($test[1], $test[2], $test[3]), 'tested: ' . $test[1]);
        }
    }

    public function testStrLimitInByte()
    {
        $testArray = [
            ['th...', 'this is a test', 5, '...'],
            ['this ...', 'this is öäü-foo test', 8, '...'],
            ['fòô ', 'fòô bàř fòô', 6, ''],
            ['', "fòô bàř \x00fòô", 0, ''],
            ['', "fòô bàř \x00fòô", -1, ''],
            ['fòô白', "fòô bàř \x00fòô", 8, '白'],
            ['白', '白白 白白', 3, ''],
            ['白白白', '白白白', 100, ''],
            ['', '', 1, ''],
        ];

        foreach ($testArray as $test) {
            static::assertSame($test[0], UTF8::str_limit_in_byte($test[1], $test[2], $test[3]), 'tested: ' . $test[1]);
        }
    }

    public function testStrLimitAfterWord()
    {
        $testArray = [
            ['this...', 'this is a test', 5, '...'],
            ['this is...', 'this is öäü-foo test', 8, '...'],
            ['fòô', 'fòô bàř fòô', 6, ''],
            ['fòô bàř', 'fòô bàř fòô', 8, ''],
            ['fòô bàř', "fòô bàř fòô \x00", 8, ''],
            ['', "fòô bàř \x00fòô", 0, ''],
            ['', "fòô bàř \x00fòô", -1, ''],
            ['fòô bàř白', "fòô bàř \x00fòô", 8, '白'],
            ['', '白白 白白', 1, ''],
            ['白白', '白白 白白', 3, ''],
            ['白白白', '白白白', 100, ''],
            ['', '', 1, ''],
        ];

        foreach ($testArray as $test) {
            static::assertSame($test[0], UTF8::str_limit_after_word($test[1], $test[2], $test[3]), 'tested: ' . $test[1]);
        }
    }

    public function testStrPad()
    {
        $firstString = "Though wise men at their end know dark is right,\nBecause their words had forked no lightning they\n";
        $secondString = 'Do not go gentle into that good night.';
        $expectedString = $firstString . $secondString;
        $actualString = UTF8::str_pad(
            $firstString,
            UTF8::strlen($firstString) + UTF8::strlen($secondString),
            $secondString
        );

        static::assertSame($expectedString, $actualString);

        static::assertSame('中文空白______', UTF8::str_pad('中文空白', 10, '_', \STR_PAD_RIGHT));
        static::assertSame('______中文空白', UTF8::str_pad('中文空白', 10, '_', \STR_PAD_LEFT));
        static::assertSame('___中文空白___', UTF8::str_pad('中文空白', 10, '_', \STR_PAD_BOTH));

        $toPad = '<IñtërnëT>'; // 10 characters
        $padding = 'ø__'; // 4 characters

        static::assertSame($toPad . '          ', UTF8::str_pad($toPad, 20));
        static::assertSame('          ' . $toPad, UTF8::str_pad($toPad, 20, ' ', \STR_PAD_LEFT));
        static::assertSame('     ' . $toPad . '     ', UTF8::str_pad($toPad, 20, ' ', \STR_PAD_BOTH));

        static::assertSame($toPad, UTF8::str_pad($toPad, 10));
        static::assertSame('5char', \str_pad('5char', 4)); // str_pos won't truncate input string
        static::assertSame($toPad, UTF8::str_pad($toPad, 8));

        static::assertSame($toPad . 'ø__ø__ø__ø', UTF8::str_pad($toPad, 20, $padding, \STR_PAD_RIGHT));
        static::assertSame('ø__ø__ø__ø' . $toPad, UTF8::str_pad($toPad, 20, $padding, \STR_PAD_LEFT));
        static::assertSame('ø__ø_' . $toPad . 'ø__ø_', UTF8::str_pad($toPad, 20, $padding, \STR_PAD_BOTH));
    }

    public function testStrRepeat()
    {
        $tests = [
            ''                                                                         => '',
            ' '                                                                        => '                 ',
            '�'                                                                        => '�����������������',
            '中文空白 �'                                                                   => '中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �',
            "<ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a>" => "<ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a>",
            'DÃ¼�sseldorf'                                                             => 'DÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorf',
            'Abcdef'                                                                   => 'AbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdef',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::str_repeat($before, 17));
        }

        // ---

        // TODO: Is this is a bug in PHP 8.1 or not? :/
        if (\PHP_VERSION_ID >= 80100) {
            $tests = [
                "°~\xf0\x90\x28\xbc" => 'В°~рђ(јВ°~рђ(јВ°~рђ(јВ°~рђ(јВ°~рђ(јВ°~рђ(јВ°~рђ(јВ°~рђ(јВ°~рђ(јВ°~рђ(јВ°~рђ(јВ°~рђ(јВ°~рђ(јВ°~рђ(јВ°~рђ(јВ°~рђ(јВ°~рђ(ј',
            ];
        } else {
            $tests = [
                "°~\xf0\x90\x28\xbc" => '°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼',
            ];
        }

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::str_repeat($before, 17));
        }
    }

    public function testStrReplaceFirst()
    {
        $testArray = [
            ''                                              => ['', '', ''],
            ' lall lall'                                    => ['lall', '', 'lall lall lall'],
            'ö a l l '                                      => ['l', 'ö', 'l a l l '],
            'κöäüσμε' . \html_entity_decode('&nbsp;') . 'ό' => ['ό', 'öäü', "κόσμε\xc2\xa0ό"],
        ];

        foreach ($testArray as $after => $test) {
            static::assertSame($after, UTF8::str_replace_first($test[0], $test[1], $test[2]));
        }
    }

    public function testStrReplaceLast()
    {
        $testArray = [
            ''                                              => ['', '', ''],
            'lall lall '                                    => ['lall', '', 'lall lall lall'],
            'l a l ö '                                      => ['l', 'ö', 'l a l l '],
            'κόσμε' . \html_entity_decode('&nbsp;') . 'öäü' => ['ό', 'öäü', "κόσμε\xc2\xa0ό"],
        ];

        foreach ($testArray as $after => $test) {
            static::assertSame($after, UTF8::str_replace_last($test[0], $test[1], $test[2]));
        }
    }

    public function testToUtf8()
    {
        // test string from: https://r12a.github.io/app-conversion/

        $testStr = "
        The decomposition mapping is <츠, U+11B8>, and not <0x110E, ᅳ, 11B8>.  1234 (ethiopic/latin?)
        <p>The title says ‫פעילות הבינאום, W3C‬ &rlm;in Hebrew</p>
        \nabc\x09áßç\tक際👽 	%E0%A4%95%E9%9A%9B%F0%9F%91%BD
        html/xml (hex)\x09&#x00E7;&#x0916;&#x0940;&#x570B;&#x1F47D;&Aacute;
        html/xml (dec)\x09&#231;&#2326;&#2368;&#22283;&#128125;&aacute;
        javascript\x09\u00E7\u0916\u0940\u570B\uD83D\uDC7D
        js/rust/ruby\x09\u{E7}\u{916}\u{940}\u{570B}\u{1F47D}
        ";

        $expected = '
        The decomposition mapping is <츠, U+11B8>, and not <0x110E, ᅳ, 11B8>.  1234 (ethiopic/latin?)
        <p>The title says ‫פעילות הבינאום, W3C‬ ‏in Hebrew</p>
        
abc	áßç	क際👽 	क際👽
        html/xml (hex)	çखी國👽Á
        html/xml (dec)	çखी國👽á
        javascript	çखी國👽
        js/rust/ruby	çखी國👽
        ';

        static::assertSame($expected, UTF8::rawurldecode(UTF8::to_utf8($testStr, true)));
    }

    public function testStrShuffle()
    {
        $testArray = [
            'this is a test',
            'this is öäü-foo test',
            'fòô bàř fòô',
            '',
            "\t",
            "\t\t",
        ];

        foreach ($testArray as $test) {
            static::assertSame(
                [],
                \array_diff(
                    UTF8::str_split($test),
                    UTF8::str_split(UTF8::str_shuffle($test))
                ),
                'tested: ' . $test
            );
        }
    }

    public function testStrSort()
    {
        for ($i = 0; $i <= 2; ++$i) { // keep this loop for simple performance tests
            $tests = [
                ''               => '',
                '  -ABC-中文空白-  ' => '    ---ABC中文白空',
                '      - ÖÄÜ- '  => '        --ÄÖÜ',
                'öäü'            => 'äöü',
            ];

            foreach ($tests as $before => $after) {
                static::assertSame($after, UTF8::str_sort($before));
            }
        }

        $tests = [
            '  -ABC-中文空白-  ' => '空白文中CBA---    ',
            '      - ÖÄÜ- '  => 'ÜÖÄ--        ',
            'öäü'            => 'üöä',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::str_sort($before, false, true));
        }

        $tests = [
            '    '           => ' ',
            '  -ABC-中文空白-  ' => ' -ABC中文白空',
            '      - ÖÄÜ- '  => ' -ÄÖÜ',
            'öäü'            => 'äöü',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::str_sort($before, true));
        }

        $tests = [
            '  -ABC-中文空白-  ' => '空白文中CBA- ',
            '      - ÖÄÜ- '  => 'ÜÖÄ- ',
            'öäü'            => 'üöä',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::str_sort($before, true, true));
        }
    }

    public function testStrStartsWith()
    {
        $loops = 2;

        // ---

        $str = 'ΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEndΚόσμεMiddleEnd';

        $tests = [
            'Κόσμε' => true,
            'κόσμε' => false,
            ''      => true,
            ' '     => false,
            // false   => false,
            'Κ'   => true,
            'End' => false,
            'end' => false,
        ];

        for ($i = 0; $i <= $loops; ++$i) { // keep this loop for simple performance tests
            foreach ($tests as $test => $result) {
                static::assertSame($result, UTF8::str_starts_with($str, $test), 'tested: ' . $test);
            }
        }

        // ---

        $str = 'ΚόσμεMiddleEnd';

        $tests = [
            'Κόσμε' => true,
            'κόσμε' => false,
            ''      => true,
            ' '     => false,
            // false   => false,
            'Κ'   => true,
            'End' => false,
            'end' => false,
        ];

        for ($i = 0; $i <= $loops; ++$i) { // keep this loop for simple performance tests
            foreach ($tests as $test => $result) {
                static::assertSame($result, UTF8::str_starts_with($str, $test), 'tested: ' . $test);
            }
        }

        if (\PHP_VERSION_ID >= 80000) {
            static::assertSame(str_starts_with('', ''), UTF8::str_starts_with('', ''));
            static::assertSame(str_starts_with(' ', ''), UTF8::str_starts_with(' ', ''));
            static::assertSame(str_starts_with('', ' '), UTF8::str_starts_with('', ' '));
            static::assertSame(str_starts_with(' ', ' '), UTF8::str_starts_with(' ', ' '));
            static::assertSame(str_starts_with('DJ', ''), UTF8::str_starts_with('DJ', ''));
            static::assertSame(str_starts_with('DJ', ' '), UTF8::str_starts_with('DJ', ' '));
            static::assertSame(str_starts_with('', 'Σ'), UTF8::str_starts_with('', 'Σ'));
            static::assertSame(str_starts_with(' ', 'Σ'), UTF8::str_starts_with(' ', 'Σ'));
            static::assertSame(str_starts_with('DJ', ''), UTF8::str_starts_with('DJ', ''));
            static::assertSame(str_starts_with('DJ', ' '), UTF8::str_starts_with('DJ', ' '));
            static::assertSame(str_starts_with('', 'Σ'), UTF8::str_starts_with('', 'Σ'));
            static::assertSame(str_starts_with(' ', 'Σ'), UTF8::str_starts_with(' ', 'Σ'));
        }
    }

    public function testStrToBinary()
    {
        $tests = [
            // ''   => '0',
            // '0'  => '110000',
            // '1'  => '110001',
            '~' => '1111110',
            '§' => '1100001010100111',
            'ሇ' => '111000011000100010000111',
            '😃' => '11110000100111111001100010000011',

        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::str_to_binary($before), 'tested: ' . $before);
        }

        foreach ($tests as $before => $after) {
            static::assertSame((string) $before, UTF8::binary_to_str(UTF8::str_to_binary($before)), 'tested: ' . $before);
        }
    }

    public function testStrToWords()
    {
        static::assertSame(['', 'iñt', ' ', 'ërn', ' ', 'I', ''], UTF8::str_to_words('iñt ërn I'));
        static::assertSame(['iñt', 'ërn', 'I'], UTF8::str_to_words('iñt ërn I', '', true));
        static::assertSame(['iñt', 'ërn'], UTF8::str_to_words('iñt ërn I', '', false, 1));

        // ---

        static::assertSame(['', 'âti', "\n ", 'ônà', ''], UTF8::str_to_words("âti\n ônà"));
        static::assertSame(["\t\t"], UTF8::str_to_words("\t\t", "\n"));
        static::assertSame(['', "\t\t", ''], UTF8::str_to_words("\t\t", "\t"));
        static::assertSame(['', '中文空白', ' ', 'oöäü#s', ''], UTF8::str_to_words('中文空白 oöäü#s', '#'));
        static::assertSame(['', 'foo', ' ', 'oo', ' ', 'oöäü', '#', 's', ''], UTF8::str_to_words('foo oo oöäü#s', ''));
        static::assertSame([''], UTF8::str_to_words(''));

        $testArray = [
            'Düsseldorf'                                                                                => 'Düsseldorf',
            'Ã'                                                                                         => 'Ã',
            'foobar  || 😃'                                                                              => 'foobar  || 😃',
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
        ];

        foreach ($testArray as $test => $unused) {
            static::assertSame($test, \implode(UTF8::str_to_words($test)), '');
        }
    }

    public function testStrSplit()
    {
        static::assertSame(
            [0 => '12', 1 => '34'],
            UTF8::str_split(1234, 2)
        );

        static::assertSame(
            [[0 => '12', 1 => '34']],
            UTF8::str_split_array([1234], 2)
        );

        static::assertSame(
            [['1', '2', '3', '4']],
            UTF8::str_split_array([1234], 1)
        );

        static::assertSame(
            [[]],
            UTF8::str_split_array([1234], 0)
        );

        static::assertSame(
            [[]],
            UTF8::str_split_array([1234], -1)
        );

        static::assertSame(
            [0 => '12', 1 => '34'],
            UTF8::str_split('1234', 2)
        );

        static::assertSame(
            [[0 => '12', 1 => '34']],
            UTF8::str_split_array(['1234'], 2)
        );

        static::assertSame(
            [
                [0 => '12', 1 => '34'],
                [0 => '56', 1 => '78'],
            ],
            UTF8::str_split_array(['1234', '5678'], 2)
        );

        static::assertSame(
            [],
            UTF8::str_split('déjà', 0)
        );

        static::assertSame(
            [
                'd',
                'é',
                'j',
                'à',
            ],
            UTF8::str_split('déjà', 1)
        );
        static::assertSame(
            [
                'dé',
                'jà',
            ],
            UTF8::str_split('déjà', 2)
        );
    }

    public function testString()
    {
        static::assertSame('', UTF8::string([]));
        static::assertSame(
            'öäü',
            UTF8::string(
                [
                    246,
                    228,
                    252,
                ]
            )
        );
        static::assertSame(
            'ö',
            UTF8::string(246)
        );
        static::assertSame(
            'ㅡㅡ',
            UTF8::string(
                [
                    12641,
                    12641,
                ]
            )
        );
        static::assertSame('中文空白', UTF8::string(UTF8::codepoints('中文空白')));
    }

    public function testStringHasBom()
    {
        $testArray = [
            ' '                    => false,
            ''                     => false,
            UTF8::bom() . 'κ'      => true,
            'abc'                  => false,
            UTF8::bom() . 'abcöäü' => true,
            '白'                    => false,
            UTF8::bom()            => true,
        ];

        $utf8_bom = \file_get_contents(__DIR__ . '/fixtures/sample-utf-8-bom.txt');
        $utf8_bom_only = \file_get_contents(__DIR__ . '/fixtures/sample-utf-8-bom-only.txt');
        $utf16_be_bom = \file_get_contents(__DIR__ . '/fixtures/sample-utf-16-be-bom.txt');
        $utf16_be_bom_only = \file_get_contents(__DIR__ . '/fixtures/sample-utf-16-be-bom-only.txt');
        $utf16_le_bom = \file_get_contents(__DIR__ . '/fixtures/sample-utf-16-le-bom.txt');
        $utf16_le_bom_only = \file_get_contents(__DIR__ . '/fixtures/sample-utf-16-le-bom-only.txt');
        $utf32_be_bom = \file_get_contents(__DIR__ . '/fixtures/sample-utf-32-be-bom.txt');
        $utf32_be_bom_only = \file_get_contents(__DIR__ . '/fixtures/sample-utf-32-be-bom-only.txt');
        $utf32_le_bom = \file_get_contents(__DIR__ . '/fixtures/sample-utf-32-le-bom.txt');
        $utf32_le_bom_only = \file_get_contents(__DIR__ . '/fixtures/sample-utf-32-le-bom-only.txt');

        $testArray[$utf8_bom] = true;
        $testArray[$utf8_bom_only] = true;
        $testArray[$utf16_be_bom] = true;
        $testArray[$utf16_be_bom_only] = true;
        $testArray[$utf16_le_bom] = true;
        $testArray[$utf16_le_bom_only] = true;
        $testArray[$utf32_be_bom] = true;
        $testArray[$utf32_be_bom_only] = true;
        $testArray[$utf32_le_bom] = true;
        $testArray[$utf32_le_bom_only] = true;

        foreach ($testArray as $actual => $expected) {
            static::assertSame($expected, UTF8::string_has_bom($actual), 'error by ' . $actual);
        }
    }

    public function testStripTags()
    {
        $tests = [
            // null                                                                      => '',
            // ''                                                                        => '',
            ' ' => ' ',
            // 1                                                                         => '1',
            // '2'                                                                       => '2',
            'Abcdef'                                                                  => 'Abcdef',
            '<nav>DÃ¼sseldorf</nav>'                                                  => 'DÃ¼sseldorf',
            "<ㅡㅡ></ㅡㅡ><div></div><input type='email' name='user[email]' /><a>wtf</a>" => 'wtf',
            '<nav>中文空白 </nav>'                                                        => '中文空白 ',
            "<span>κόσμε\xa0\xa1</span>-<span>öäü</span>öäü"                          => 'κόσμε-öäüöäü',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::strip_tags($before, null, true));
        }

        // ---

        $tests = [
            // null                                                                      => '',
            // ''                                                                        => '',
            ' ' => ' ',
            // 1                                                                         => '1',
            // '2'                                                                       => '2',
            'Abcdef'                                                                  => 'Abcdef',
            '<nav>DÃ¼sseldorf</nav>'                                                  => 'DÃ¼sseldorf',
            "<ㅡㅡ></ㅡㅡ><div></div><input type='email' name='user[email]' /><a>wtf</a>" => 'wtf',
            '<nav>中文空白 </nav>'                                                        => '中文空白 ',
            '<span>κόσμε</span>-<span>öäü</span>öäü'                                  => '<span>κόσμε</span>-<span>öäü</span>öäü',
        ];

        foreach ($tests as $before => $after) {
            static::assertSame($after, UTF8::strip_tags($before, '<span>', false));
        }
    }

    /**
     * @dataProvider stripWhitespaceProvider()
     *
     * @param string $expected
     * @param string $str
     */
    public function testStripWhitespace($expected, $str)
    {
        $result = UTF8::strip_whitespace($str);

        static::assertSame($expected, $result);
    }

    public function testStripos()
    {
        for ($i = 0; $i <= 2; ++$i) { // keep this loop for simple performance tests
            if ($i === 0) {
                $this->disableNativeUtf8Support();
            } elseif ($i > 0) {
                $this->reactivateNativeUtf8Support();
            }

            static::assertSame(\stripos('', ''), UTF8::stripos('', ''));
            static::assertSame(\stripos(' ', ''), UTF8::stripos(' ', ''));
            static::assertSame(\stripos('', ' '), UTF8::stripos('', ' '));
            static::assertSame(\stripos(' ', ' '), UTF8::stripos(' ', ' '));
            static::assertSame(\stripos('DJ', ''), UTF8::stripos('DJ', ''));
            static::assertSame(\stripos('DJ', ' '), UTF8::stripos('DJ', ' '));
            static::assertSame(\stripos('', 'Σ'), UTF8::stripos('', 'Σ'));
            static::assertSame(\stripos(' ', 'Σ'), UTF8::stripos(' ', 'Σ'));
            static::assertSame(\stripos('DJ', ''), UTF8::stripos('DJ', ''));
            static::assertSame(\stripos('DJ', ' '), UTF8::stripos('DJ', ' '));
            static::assertSame(\stripos('', 'Σ'), UTF8::stripos('', 'Σ'));
            static::assertSame(\stripos(' ', 'Σ'), UTF8::stripos(' ', 'Σ'));

            static::assertFalse(UTF8::stripos('DÉJÀ', 'ä'));
            static::assertFalse(UTF8::stripos('DÉJÀ', ' '));
            if (!\voku\helper\Bootup::is_php('8.0')) {
                static::assertFalse(UTF8::stripos('DÉJÀ', ''));
            } else {
                static::assertSame(0, UTF8::stripos('DÉJÀ', ''));
            }
            static::assertFalse(UTF8::stripos('', 'ä'));
            static::assertFalse(UTF8::stripos('', ' '));
            if (!\voku\helper\Bootup::is_php('8.0')) {
                static::assertFalse(UTF8::stripos('', ''));
            } else {
                static::assertSame(0, UTF8::stripos('', ''));
            }
            static::assertSame(1, UTF8::stripos('aςσb', 'ΣΣ'));
            static::assertSame(3, UTF8::stripos('DÉJÀ', 'à'));
            static::assertSame(4, UTF8::stripos('öäü-κόσμε-κόσμε-κόσμε', 'Κ'));
            static::assertSame(4, UTF8::stripos('ABC-ÖÄÜ-中文空白-中文空白', 'ö'));
            static::assertSame(5, UTF8::stripos('Test κόσμε test κόσμε', 'Κ'));
            static::assertSame(16, UTF8::stripos('der Straße nach Paris', 'Paris'));

            // ---

            static::assertSame(3, UTF8::stripos('DÉJÀ', 'à'));
            static::assertSame(3, UTF8::stripos('DÉJÀ', 'à', 1));
            static::assertSame(3, UTF8::stripos('DÉJÀ', 'à', 1, 'UTF-8'));
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            static::assertFalse(@UTF8::stripos('DÉJÀ', 'à', 1, 'ISO'));
        }
    }

    public function testStrstrInByte()
    {
        static::assertSame('ello', UTF8::strstr_in_byte('Hello', 'e'));
    }

    public function testStrrirpos()
    {
        if (!Bootup::is_php('8.0')) {
            static::assertFalse(\strripos('', ''));
            static::assertFalse(\strripos(' ', ''));
        } else {
            static::assertSame(0, \strripos('', ''));
            static::assertSame(1, \strripos(' ', ''));
        }
        static::assertFalse(\strripos('', ' '));
        if (!Bootup::is_php('8.0')) {
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
            'mbstring_regex'                    => false,
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
