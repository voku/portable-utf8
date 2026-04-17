<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;

final class IncreaseCoverageTest extends \PHPUnit\Framework\TestCase
{
    public function testAccessNonUtf8()
    {
        // Line 284: return (string) self::substr($str, $pos, 1, $encoding);
        static::assertSame('t', UTF8::access('test', 0, 'ISO-8859-1'));
    }

    public function testArrayChangeKeyCaseInvalidCase()
    {
        // Line 333: $case = \CASE_LOWER;
        static::assertSame(['a' => 1], UTF8::array_change_key_case(['A' => 1], 999));
    }

    public function testBinaryToStrEncoding()
    {
        // Line 428 in binary_to_str
        // '01100001' is 'a'
        static::assertSame('a', UTF8::binary_to_str('01100001'));
    }

    public function testChrToHexInvalid()
    {
        // Line 873 in chr_to_hex
        static::assertSame('', UTF8::chr_to_hex(''));
    }

    public function testFileHasBomEmptyFile()
    {
        static::assertFalse(UTF8::file_has_bom(__DIR__ . '/fixtures/empty.txt'));
    }

    public function testFilterInputNoInput()
    {
        // Line 2071 in filter_input
        static::assertNull(UTF8::filter_input(\INPUT_GET, 'non_existent'));
    }

    public function testFilterInputArrayNoInput()
    {
        // Line 2135 in filter_input_array
        static::assertNull(UTF8::filter_input_array(\INPUT_GET, ['test' => \FILTER_DEFAULT]));
    }

    public function testStrDelimitWithLang()
    {
        // Line 5977 or 5989 depending on mbstring_regex
        static::assertSame('test-string', UTF8::str_delimit('testString', '-', 'UTF-8', false, 'Any'));
    }

    public function testStrIendsWithEmptyHaystack()
    {
        // Line 6287
        static::assertFalse(UTF8::str_iends_with('', 'test'));
    }

    public function testStrInsertOutOfBounds()
    {
        // Line 6340
        static::assertSame('test', UTF8::str_insert('test', 'X', 10));
    }

    public function testCssIdentifierWithUnderscore()
    {
        // Line 1198
        static::assertSame('a__b', UTF8::css_identifier('a__b'));
    }

    public function testEmojiDecodeReversible()
    {
        // Line 1345
        static::assertSame('😀', UTF8::emoji_decode('😀', true));
    }

    public function testRemoveBomOnlyBom()
    {
        // Line 5185
        static::assertSame('', UTF8::remove_bom("\xef\xbb\xbf"));
    }

    public function testStrEnsureLeftAlreadyExists()
    {
        static::assertSame('test', UTF8::str_ensure_left('test', 'te'));
    }

    public function testStrEnsureRightAlreadyExists()
    {
        static::assertSame('test', UTF8::str_ensure_right('test', 'st'));
    }

    public function testStrContainsAny()
    {
        static::assertTrue(UTF8::str_contains_any('abc', ['a', 'b']));
        static::assertFalse(UTF8::str_contains_any('abc', ['d', 'e']));
    }

    public function testReplaceDiamondQuestionMark()
    {
        static::assertSame('?', UTF8::replace_diamond_question_mark("\xef\xbf\xbd", '?'));
    }

    public function testStrDetectEncoding()
    {
        static::assertSame('UTF-8', UTF8::str_detect_encoding('中文空白'));
    }
}
