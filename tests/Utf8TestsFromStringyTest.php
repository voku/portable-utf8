<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\Bootup;
use voku\helper\UTF8;

/**
 * Class Utf8TestsFromStringyTest
 *
 * @internal
 */
final class Utf8TestsFromStringyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return \Iterator
     */
    public function appendProvider(): \Iterator
    {
        yield ['foobar', 'foo', 'bar'];
        yield ['fòôbàř', 'fòô', 'bàř', 'UTF-8'];
    }

    /**
     * Asserts that a variable is of a UTF8 instance.
     *
     * @param string $str
     */
    public static function assertUtf8String(string $str)
    {
        static::assertTrue(UTF8::is_utf8($str));
    }

    /**
     * @return \Iterator
     */
    public function atProvider(): \Iterator
    {
        yield ['f', 'foo bar', 0];
        yield ['o', 'foo bar', 1];
        yield ['r', 'foo bar', 6];
        yield ['', 'foo bar', 7];
        yield ['f', 'fòô bàř', 0, 'UTF-8'];
        yield ['ò', 'fòô bàř', 1, 'UTF-8'];
        yield ['ř', 'fòô bàř', 6, 'UTF-8'];
        yield ['', 'fòô bàř', 7, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function betweenProvider(): \Iterator
    {
        yield ['', 'foo', '{', '}'];
        yield ['', '{foo', '{', '}'];
        yield ['foo', '{foo}', '{', '}'];
        yield ['{foo', '{{foo}', '{', '}'];
        yield ['', '{}foo}', '{', '}'];
        yield ['foo', '}{foo}', '{', '}'];
        yield ['foo', 'A description of {foo} goes here', '{', '}'];
        yield ['bar', '{foo} and {bar}', '{', '}', 1];
        yield ['', 'fòô', '{', '}', 0, 'UTF-8'];
        yield ['', '{fòô', '{', '}', 0, 'UTF-8'];
        yield ['fòô', '{fòô}', '{', '}', 0, 'UTF-8'];
        yield ['{fòô', '{{fòô}', '{', '}', 0, 'UTF-8'];
        yield ['', '{}fòô}', '{', '}', 0, 'UTF-8'];
        yield ['fòô', '}{fòô}', '{', '}', 0, 'UTF-8'];
        yield ['fòô', 'A description of {fòô} goes here', '{', '}', 0, 'UTF-8'];
        yield ['bàř', '{fòô} and {bàř}', '{', '}', 1, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function camelizeProvider(): \Iterator
    {
        yield ['camelCase', 'CamelCase'];
        yield ['camelCase', 'Camel-Case'];
        yield ['camelCase', 'camel case'];
        yield ['camelCase', 'camel -case'];
        yield ['camelCase', 'camel - case'];
        yield ['camelCase', 'camel_case'];
        yield ['camelCTest', 'camel c test'];
        yield ['stringWith1Number', 'string_with1number'];
        yield ['stringWith22Numbers', 'string-with-2-2 numbers'];
        yield ['dataRate', 'data_rate'];
        yield ['backgroundColor', 'background-color'];
        yield ['yesWeCan', 'yes_we_can'];
        yield ['mozSomething', '-moz-something'];
        yield ['carSpeed', '_car_speed_'];
        yield ['serveHTTP', 'ServeHTTP'];
        yield ['1Camel2Case', '1camel2case'];
        yield ['camelΣase', 'camel σase', 'UTF-8'];
        yield ['στανιλCase', 'Στανιλ case', 'UTF-8'];
        yield ['σamelCase', 'σamel  Case', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function capitalizePersonalNameProvider(): \Iterator
    {
        yield ['Marcus Aurelius', 'marcus aurelius'];
        yield ['Torbjørn Færøvik', 'torbjørn færøvik'];
        yield ['Jaap de Hoop Scheffer', 'jaap de hoop scheffer'];
        yield ['K. Anders Ericsson', 'k. anders ericsson'];
        yield ['Per-Einar', 'per-einar'];
        yield [
            'Line Break',
            'line
             break',
        ];
        yield ['ab', 'ab'];
        yield ['af', 'af'];
        yield ['al', 'al'];
        yield ['and', 'and'];
        yield ['ap', 'ap'];
        yield ['bint', 'bint'];
        yield ['binte', 'binte'];
        yield ['da', 'da'];
        yield ['de', 'de'];
        yield ['del', 'del'];
        yield ['den', 'den'];
        yield ['der', 'der'];
        yield ['di', 'di'];
        yield ['dit', 'dit'];
        yield ['ibn', 'ibn'];
        yield ['la', 'la'];
        yield ['mac', 'mac'];
        yield ['nic', 'nic'];
        yield ['of', 'of'];
        yield ['ter', 'ter'];
        yield ['the', 'the'];
        yield ['und', 'und'];
        yield ['van', 'van'];
        yield ['von', 'von'];
        yield ['y', 'y'];
        yield ['zu', 'zu'];
        yield ['Bashar al-Assad', 'bashar al-assad'];
        yield ["d'Name", "d'Name"];
        yield ['ffName', 'ffName'];
        yield ["l'Name", "l'Name"];
        yield ['macDuck', 'macDuck'];
        yield ['mcDuck', 'mcDuck'];
        yield ['nickMick', 'nickMick'];
    }

    /**
     * @return \Iterator
     */
    public function charsProvider(): \Iterator
    {
        yield [[], ''];
        yield [['T', 'e', 's', 't'], 'Test'];
        yield [['F', 'ò', 'ô', ' ', 'B', 'à', 'ř'], 'Fòô Bàř'];
    }

    /**
     * @return \Iterator
     */
    public function hasWhitespaceProvider(): \Iterator
    {
        yield ['foo bar', '  foo   bar  '];
        yield ['test string', 'test string'];
        yield ['Ο συγγραφέας', '   Ο     συγγραφέας  '];
        yield ['123' . "\n", ' 123 '];
        yield [' ', ' ', 'UTF-8'];
        // no-break space (U+00A0)
        yield [' ', '           '];
        // spaces U+2000 to U+200A
        yield [' ', ' ', 'UTF-8'];
        // narrow no-break space (U+202F)
        yield [' ', ' ', 'UTF-8'];
        // medium mathematical space (U+205F)
        yield [' ', '　', 'UTF-8'];
        // ideographic space (U+3000)
        yield ['1 2 3', '  1  2  3　　'];
        yield ['  ', ' '];
    }

    /**
     * @return \Iterator
     */
    public function collapseWhitespaceProvider(): \Iterator
    {
        yield ['foo bar', '  foo   bar  '];
        yield ['test string', 'test string'];
        yield ['Ο συγγραφέας', '   Ο     συγγραφέας  '];
        yield ['123', ' 123 '];
        yield ['', ' ', 'UTF-8'];
        // no-break space (U+00A0)
        yield ['', '           '];
        // spaces U+2000 to U+200A
        yield ['', ' ', 'UTF-8'];
        // narrow no-break space (U+202F)
        yield ['', ' ', 'UTF-8'];
        // medium mathematical space (U+205F)
        yield ['', '　', 'UTF-8'];
        // ideographic space (U+3000)
        yield ['1 2 3', '  1  2  3　　'];
        yield ['', ' '];
        yield ['', ''];
    }

    /**
     * @return array
     */
    public function containsAllProvider(): array
    {
        $containsProvider = [];
        foreach ($this->containsProvider() as $data) {
            $containsProvider[] = $data;
        }

        // One needle
        $singleNeedle = \array_map(
            static function (array $array) {
                $array[2] = [$array[2]];

                return $array;
            },
            $containsProvider
        );

        $provider = [
            // One needle
            [false, 'Str contains foo bar', []],
            [false, 'Str contains foo bar', ['']],
            // Multiple needles
            [true, 'Str contains foo bar', ['foo', 'bar']],
            [true, '12398!@(*%!@# @!%#*&^%', [' @!%#*', '&^%']],
            [true, 'Ο συγγραφέας είπε', ['συγγρ', 'αφέας'], true],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['å´¥', '©'], true],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['å˚ ', '∆'], true],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['øœ', '¬'], true],
            [false, 'Str contains foo bar', ['Foo', 'bar']],
            [false, 'Str contains foo bar', ['foobar', 'bar']],
            [false, 'Str contains foo bar', ['foo bar ', 'bar']],
            [false, 'Str contains foo bar', ['Str', 'foo bar ', 'bar']],
            [false, 'Ο συγγραφέας είπε', ['  συγγραφέας ', '  συγγραφ '], true],
            [false, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', [' ßå˚', ' ß '], true],
            [true, 'Str contains foo bar', ['Foo bar', 'bar'], false],
            [true, '12398!@(*%!@# @!%#*&^%', [' @!%#*&^%', '*&^%'], false],
            [true, 'Ο συγγραφέας είπε', ['ΣΥΓΓΡΑΦΈΑΣ', 'ΑΦΈΑ'], false],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['Å´¥©', '¥©'], false],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['Å˚ ∆', ' ∆'], false],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['ØŒ¬', 'Œ'], false],
            [false, 'Str contains foo bar', ['foobar', 'none'], false],
            [false, 'Str contains foo bar', ['foo bar ', ' ba'], false],
            [false, 'Ο συγγραφέας είπε', ['  συγγραφέας ', ' ραφέ '], false],
            [false, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', [' ßÅ˚', ' Å˚ '], false],
        ];

        return \array_merge($singleNeedle, $provider);
    }

    /**
     * @return array
     */
    public function containsAnyProvider(): array
    {
        $containsProvider = [];
        foreach ($this->containsProvider() as $data) {
            $containsProvider[] = $data;
        }

        // One needle
        $singleNeedle = \array_map(
            static function (array $array) {
                $array[2] = [$array[2]];

                return $array;
            },
            $containsProvider
        );

        $provider = [
            // No needles
            [false, 'Str contains foo bar', []],
            // Multiple needles
            [true, 'foo bar', ['something', 'bar', 'somethingelse']],
            [true, 'foo bar', ['something', 'foo']],
            [false, 'foo bar', ['something', 'somethingelse']],
            [true, 'Str contains foo bar', ['foo', 'bar']],
            [true, '12398!@(*%!@# @!%#*&^%', [' @!%#*', '&^%']],
            [true, 'Ο συγγραφέας είπε', ['συγγρ', 'αφέας']],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['å´¥', '©'], true],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['å˚ ', '∆'], true],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['øœ', '¬'], true],
            [false, 'Str contains foo bar', ['Foo', 'Bar']],
            [false, 'Str contains foo bar', ['foobar', 'bar ']],
            [false, 'Str contains foo bar', ['foo bar ', '  foo']],
            [false, 'Ο συγγραφέας είπε', ['  συγγραφέας ', '  συγγραφ '], true],
            [false, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', [' ßå˚', ' ß '], true],
            [true, 'Str contains foo bar', ['Foo bar', 'bar'], false],
            [true, '12398!@(*%!@# @!%#*&^%', [' @!%#*&^%', '*&^%'], false],
            [true, 'Ο συγγραφέας είπε', ['ΣΥΓΓΡΑΦΈΑΣ', 'ΑΦΈΑ'], false],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['Å´¥©', '¥©'], false],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['Å˚ ∆', ' ∆'], false],
            [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['ØŒ¬', 'Œ'], false],
            [false, 'Str contains foo bar', ['foobar', 'none'], false],
            [false, 'Str contains foo bar', ['foo bar ', ' ba '], false],
            [false, 'Ο συγγραφέας είπε', ['  συγγραφέας ', ' ραφέ '], false],
            [false, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', [' ßÅ˚', ' Å˚ '], false],
        ];

        return \array_merge($singleNeedle, $provider);
    }

    /**
     * @return \Iterator
     */
    public function containsProvider(): \Iterator
    {
        yield [true, 'Str contains foo bar', 'foo bar'];
        yield [true, '12398!@(*%!@# @!%#*&^%', ' @!%#*&^%'];
        yield [true, 'Ο συγγραφέας είπε', 'συγγραφέας', true];
        yield [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'å´¥©', true];
        yield [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'å˚ ∆', true];
        yield [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'øœ¬', true];
        yield [false, 'Str contains foo bar', 'Foo bar'];
        yield [false, 'Str contains foo bar', 'foobar'];
        yield [false, 'Str contains foo bar', 'foo bar '];
        yield [false, 'Ο συγγραφέας είπε', '  συγγραφέας ', true];
        yield [false, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ' ßå˚', true];
        yield [true, 'Str contains foo bar', 'Foo bar', false];
        yield [true, '12398!@(*%!@# @!%#*&^%', ' @!%#*&^%', false];
        yield [true, 'Ο συγγραφέας είπε', 'ΣΥΓΓΡΑΦΈΑΣ', false];
        yield [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'Å´¥©', false];
        yield [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'Å˚ ∆', false];
        yield [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'ØŒ¬', false];
        yield [false, 'Str contains foo bar', 'foobar', false];
        yield [false, 'Str contains foo bar', 'foo bar ', false];
        yield [false, 'Ο συγγραφέας είπε', '  συγγραφέας ', false];
        yield [false, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ' ßÅ˚', false];
    }

    /**
     * @return \Iterator
     */
    public function countSubstrByteProvider(): \Iterator
    {
        yield [0, '', 'foo'];
        yield [0, 'foo', 'bar'];
        yield [1, 'foo bar', 'foo'];
        yield [2, 'foo bar', 'o'];
    }

    /**
     * @return \Iterator
     */
    public function countSubstrProvider(): \Iterator
    {
        yield [0, '', 'foo'];
        yield [0, 'foo', 'bar'];
        yield [1, 'foo bar', 'foo'];
        yield [2, 'foo bar', 'o'];
        yield [0, '', 'fòô', false, 'UTF-8'];
        yield [0, 'fòô', 'bàř', false, 'UTF-8'];
        yield [1, 'fòô bàř', 'fòô', false, 'UTF-8'];
        yield [2, 'fôòô bàř', 'ô', false, 'UTF-8'];
        yield [2, 'fÔÒÔ bàř', 'ô', false, 'UTF-8'];
        yield [0, 'foo', 'BAR', false];
        yield [1, 'foo bar', 'FOo', false];
        yield [2, 'foo bar', 'O', false];
        yield [1, 'fòô bàř', 'fÒÔ', false, 'UTF-8'];
        yield [2, 'fôòô bàř', 'Ô', false, 'UTF-8'];
        yield [2, 'συγγραφέας', 'Σ', false, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function dasherizeProvider(): \Iterator
    {
        yield ['test-case', 'testCase'];
        yield ['test-case', 'Test-Case'];
        yield ['test-case', 'test case'];
        yield ['-test-case', '-test -case'];
        yield ['test-case', 'test - case'];
        yield ['test-case', 'test_case'];
        yield ['test-c-test', 'test c test'];
        yield ['test-d-case', 'TestDCase'];
        yield ['test-c-c-test', 'TestCCTest'];
        yield ['string-with1number', 'string_with1number'];
        yield ['string-with-2-2-numbers', 'String-with_2_2 numbers'];
        yield ['1test2case', '1test2case'];
        yield ['data-rate', 'dataRate'];
        yield ['car-speed', 'CarSpeed'];
        yield ['yes-we-can', 'yesWeCan'];
        yield ['background-color', 'backgroundColor'];
        yield ['dash-σase', 'dash Σase', 'UTF-8'];
        yield ['στανιλ-case', 'Στανιλ case', 'UTF-8'];
        yield ['σash-case', 'Σash  Case', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function delimitProvider(): \Iterator
    {
        yield ['', '', ''];
        yield ['', '', '*'];
        yield ['testcase', 'testCase', ''];
        yield ['test*case', 'testCase', '*'];
        yield ['test&case', 'Test-Case', '&'];
        yield ['test#case', 'test case', '#'];
        yield ['test**case', 'test -case', '**'];
        yield ['~!~test~!~case', '-test - case', '~!~'];
        yield ['test*case', 'test_case', '*'];
        yield ['test%c%test', '  test c test', '%'];
        yield ['test+u+case', 'TestUCase', '+'];
        yield ['test=c=c=test', 'TestCCTest', '='];
        yield ['string#>with1number', 'string_with1number', '#>'];
        yield ['1test2case', '1test2case', '*'];
        yield ['test ύα σase', 'test Σase', ' ύα ', 'UTF-8'];
        yield ['στανιλαcase', 'Στανιλ case', 'α', 'UTF-8'];
        yield ['σashΘcase', 'Σash  Case', 'Θ', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function endsWithAnyProvider(): \Iterator
    {
        yield [true, 'foo bars', ['foo', 'o bars']];
        yield [true, 'FOO bars', ['foo', 'o bars'], false];
        yield [true, 'FOO bars', ['foo', 'o BARs'], false];
        yield [true, 'FÒÔ bàřs', ['foo', 'ô bàřs'], false, 'UTF-8'];
        yield [true, 'fòô bàřs', ['foo', 'ô BÀŘs'], false, 'UTF-8'];
        yield [false, 'foo bar', ['foo']];
        yield [false, 'foo bar', ['foo', 'foo bars']];
        yield [false, 'FOO bar', ['foo', 'foo bars']];
        yield [false, 'FOO bars', ['foo', 'foo BARS']];
        yield [false, 'FÒÔ bàřs', ['fòô', 'fòô bàřs'], true, 'UTF-8'];
        yield [false, 'fòô bàřs', ['fòô', 'fòô BÀŘS'], true, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function endsWithProvider(): \Iterator
    {
        yield [true, 'foo bars', 'o bars'];
        yield [true, 'FOO bars', 'o bars', false];
        yield [true, 'FOO bars', 'o BARs', false];
        yield [true, 'FÒÔ bàřs', 'ô bàřs', false];
        yield [true, 'fòô bàřs', 'ô BÀŘs', false];
        yield [false, 'foo bar', 'foo'];
        yield [false, 'foo bar', 'foo bars'];
        yield [false, 'FOO bar', 'foo bars'];
        yield [false, 'FOO bars', 'foo BARS'];
        yield [false, 'FÒÔ bàřs', 'fòô bàřs', true];
        yield [false, 'fòô bàřs', 'fòô BÀŘS', true];
    }

    /**
     * @return \Iterator
     */
    public function ensureLeftProvider(): \Iterator
    {
        yield ['foobar', 'foobar', 'f'];
        yield ['foobar', 'foobar', 'foo'];
        yield ['foo/foobar', 'foobar', 'foo/'];
        yield ['http://foobar', 'foobar', 'http://'];
        yield ['http://foobar', 'http://foobar', 'http://'];
        yield ['fòôbàř', 'fòôbàř', 'f'];
        yield ['fòôbàř', 'fòôbàř', 'fòô'];
        yield ['fòô/fòôbàř', 'fòôbàř', 'fòô/'];
        yield ['http://fòôbàř', 'fòôbàř', 'http://'];
        yield ['http://fòôbàř', 'http://fòôbàř', 'http://'];
    }

    /**
     * @return \Iterator
     */
    public function ensureRightProvider(): \Iterator
    {
        yield ['foobar', 'foobar', 'r'];
        yield ['foobar', 'foobar', 'bar'];
        yield ['foobar/bar', 'foobar', '/bar'];
        yield ['foobar.com/', 'foobar', '.com/'];
        yield ['foobar.com/', 'foobar.com/', '.com/'];
        yield ['fòôbàř', 'fòôbàř', 'ř'];
        yield ['fòôbàř', 'fòôbàř', 'bàř'];
        yield ['fòôbàř/bàř', 'fòôbàř', '/bàř'];
        yield ['fòôbàř.com/', 'fòôbàř', '.com/'];
        yield ['fòôbàř.com/', 'fòôbàř.com/', '.com/'];
    }

    /**
     * @return \Iterator
     */
    public function escapeProvider(): \Iterator
    {
        yield ['', ''];
        yield ['raboof &lt;3', 'raboof <3'];
        yield ['řàbôòf&lt;foo&lt;lall&gt;&gt;&gt;', 'řàbôòf<foo<lall>>>'];
        yield ['řàb &lt;ô&gt;òf', 'řàb <ô>òf'];
        yield ['&lt;∂∆ onerro=&quot;alert(xss)&quot;&gt; ˚åß', '<∂∆ onerro="alert(xss)"> ˚åß'];
        yield ['&#039;œ … &#039;’)', '\'œ … \'’)'];
    }

    /**
     * @return \Iterator
     */
    public function firstProvider(): \Iterator
    {
        yield ['', '', 1];
        yield ['', 'foo bar', -5];
        yield ['', 'foo bar', 0];
        yield ['f', 'foo bar', 1];
        yield ['foo', 'foo bar', 3];
        yield ['foo bar', 'foo bar', 7];
        yield ['foo bar', 'foo bar', 8];
        yield ['', 'fòô bàř', -5, 'UTF-8'];
        yield ['', 'fòô bàř', 0, 'UTF-8'];
        yield ['f', 'fòô bàř', 1, 'UTF-8'];
        yield ['fòô', 'fòô bàř', 3, 'UTF-8'];
        yield ['fòô bàř', 'fòô bàř', 7, 'UTF-8'];
        yield ['fòô bàř', 'fòô bàř', 8, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function hasLowerCaseProvider(): \Iterator
    {
        yield [false, ''];
        yield [true, 'foobar'];
        yield [false, 'FOO BAR'];
        yield [true, 'fOO BAR'];
        yield [true, 'foO BAR'];
        yield [true, 'FOO BAr'];
        yield [true, 'Foobar'];
        yield [false, 'FÒÔBÀŘ'];
        yield [true, 'fòôbàř'];
        yield [true, 'fòôbàř2'];
        yield [true, 'Fòô bàř'];
        yield [true, 'fòôbÀŘ'];
    }

    /**
     * @return \Iterator
     */
    public function hasUpperCaseProvider(): \Iterator
    {
        yield [false, ''];
        yield [true, 'FOOBAR'];
        yield [false, 'foo bar'];
        yield [true, 'Foo bar'];
        yield [true, 'FOo bar'];
        yield [true, 'foo baR'];
        yield [true, 'fOOBAR'];
        yield [false, 'fòôbàř'];
        yield [true, 'FÒÔBÀŘ'];
        yield [true, 'FÒÔBÀŘ2'];
        yield [true, 'fÒÔ BÀŘ'];
        yield [true, 'FÒÔBàř'];
    }

    /**
     * @return \Iterator
     */
    public function htmlDecodeProvider(): \Iterator
    {
        yield ['&', '&amp;'];
        yield ['"', '&quot;'];
        yield ["'", '&#039;', \ENT_QUOTES];
        yield ['<', '&lt;'];
        yield ['>', '&gt;'];
    }

    /**
     * @return \Iterator
     */
    public function htmlEncodeProvider(): \Iterator
    {
        yield ['&amp;', '&'];
        yield ['&quot;', '"'];
        yield ['&#039;', "'", \ENT_QUOTES];
        yield ['&lt;', '<'];
        yield ['&gt;', '>'];
    }

    /**
     * @return \Iterator
     */
    public function humanizeProvider(): \Iterator
    {
        yield ['Author', 'author_id'];
        yield ['Test user', ' _test_user_'];
        yield ['Συγγραφέας', ' συγγραφέας_id ', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function indexOfLastProvider(): \Iterator
    {
        yield [6, 'foo & bar', 'bar'];
        yield [6, 'foo & bar', 'bar', 0];
        yield [false, 'foo & bar', 'baz'];
        yield [false, 'foo & bar', 'baz', 0];
        yield [12, 'foo & bar & foo', 'foo', 0];
        yield [0, 'foo & bar & foo', 'foo', -5];
        yield [6, 'fòô & bàř', 'bàř', 0, 'UTF-8'];
        yield [false, 'fòô & bàř', 'baz', 0, 'UTF-8'];
        yield [12, 'fòô & bàř & fòô', 'fòô', 0, 'UTF-8'];
        yield [0, 'fòô & bàř & fòô', 'fòô', -5, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function indexOfProvider(): \Iterator
    {
        yield [6, 'foo & bar', 'bar'];
        yield [6, 'foo & bar', 'bar', 0];
        yield [false, 'foo & bar', 'baz'];
        yield [false, 'foo & bar', 'baz', 0];
        yield [0, 'foo & bar & foo', 'foo', 0];
        yield [12, 'foo & bar & foo', 'foo', 5];
        yield [6, 'fòô & bàř', 'bàř', 0, 'UTF-8'];
        yield [false, 'fòô & bàř', 'baz', 0, 'UTF-8'];
        yield [0, 'fòô & bàř & fòô', 'fòô', 0, 'UTF-8'];
        yield [12, 'fòô & bàř & fòô', 'fòô', 5, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function insertProvider(): \Iterator
    {
        yield ['foo bar', 'oo bar', 'f', 0];
        yield ['foo bar', 'f bar', 'oo', 1];
        yield ['f bar', 'f bar', 'oo', 20];
        yield ['foo bar', 'foo ba', 'r', 6];
        yield ['fòôbàř', 'fòôbř', 'à', 4, 'UTF-8'];
        yield ['fòô bàř', 'òô bàř', 'f', 0, 'UTF-8'];
        yield ['fòô bàř', 'f bàř', 'òô', 1, 'UTF-8'];
        yield ['fòô bàř', 'fòô bà', 'ř', 6, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function isAlphaProvider(): \Iterator
    {
        yield [true, ''];
        yield [true, 'foobar'];
        yield [false, 'foo bar'];
        yield [false, 'foobar2'];
        yield [true, 'fòôbàř', 'UTF-8'];
        yield [false, 'fòô bàř', 'UTF-8'];
        yield [false, 'fòôbàř2', 'UTF-8'];
        yield [true, 'ҠѨњфгШ', 'UTF-8'];
        yield [false, 'ҠѨњ¨ˆфгШ', 'UTF-8'];
        yield [true, '丹尼爾', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function isPunctuationProvider(): \Iterator
    {
        yield [true, '****'];
        yield [true, '*&$();,.?'];
        yield [false, 'foo bar'];
        yield [false, 'foobar2"'];
        yield [false, "\nfoobar\n"];
        yield [true, '*&$();,.?', 'UTF-8'];
        yield [false, 'fòô bàř', 'UTF-8'];
        yield [false, 'fòôbàř2"', 'UTF-8'];
        yield [false, 'ҠѨњ¨ˆфгШ', 'UTF-8'];
        yield [false, 'دانيال1 ', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function isAlphanumericProvider(): \Iterator
    {
        yield [true, ''];
        yield [true, 'foobar1'];
        yield [false, 'foo bar'];
        yield [false, 'foobar2"'];
        yield [false, "\nfoobar\n"];
        yield [true, 'fòôbàř1', 'UTF-8'];
        yield [false, 'fòô bàř', 'UTF-8'];
        yield [false, 'fòôbàř2"', 'UTF-8'];
        yield [true, 'ҠѨњфгШ', 'UTF-8'];
        yield [false, 'ҠѨњ¨ˆфгШ', 'UTF-8'];
        yield [true, '丹尼爾111', 'UTF-8'];
        yield [true, 'دانيال1', 'UTF-8'];
        yield [false, 'دانيال1 ', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function isBase64Provider(): \Iterator
    {
        yield [false, ' '];
        yield [false, ''];
        yield [true, \base64_encode('FooBar')];
        yield [true, \base64_encode(' ')];
        yield [true, \base64_encode('FÒÔBÀŘ')];
        yield [true, \base64_encode('συγγραφέας')];
        yield [false, 'Foobar'];
    }

    /**
     * @return \Iterator
     */
    public function isBase64EmptyStringIsAlsoValidProvider(): \Iterator
    {
        yield [false, ' '];
        yield [true, ''];
        yield [true, \base64_encode('FooBar')];
        yield [true, \base64_encode(' ')];
        yield [true, \base64_encode('FÒÔBÀŘ')];
        yield [true, \base64_encode('συγγραφέας')];
        yield [false, 'Foobar'];
    }

    /**
     * @return \Iterator
     */
    public function isBlankProvider(): \Iterator
    {
        yield [true, ''];
        yield [true, ' '];
        yield [true, "\n\t "];
        yield [true, "\n\t  \v\f"];
        yield [false, "\n\t a \v\f"];
        yield [false, "\n\t ' \v\f"];
        yield [false, "\n\t 2 \v\f"];
        yield [true, '', 'UTF-8'];
        yield [true, ' ', 'UTF-8'];
        // no-break space (U+00A0)
        yield [true, '           ', 'UTF-8'];
        // spaces U+2000 to U+200A
        yield [true, ' ', 'UTF-8'];
        // narrow no-break space (U+202F)
        yield [true, ' ', 'UTF-8'];
        // medium mathematical space (U+205F)
        yield [true, '　', 'UTF-8'];
        // ideographic space (U+3000)
        yield [false, '　z', 'UTF-8'];
        yield [false, '　1', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function isHexadecimalProvider(): \Iterator
    {
        yield [true, ''];
        yield [true, 'abcdef'];
        yield [true, 'ABCDEF'];
        yield [true, '0123456789'];
        yield [true, '0123456789AbCdEf'];
        yield [false, '0123456789x'];
        yield [false, 'ABCDEFx'];
        yield [true, 'abcdef', 'UTF-8'];
        yield [true, 'ABCDEF', 'UTF-8'];
        yield [true, '0123456789', 'UTF-8'];
        yield [true, '0123456789AbCdEf', 'UTF-8'];
        yield [false, '0123456789x', 'UTF-8'];
        yield [false, 'ABCDEFx', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function isJsonProvider(): \Iterator
    {
        yield [false, ''];
        yield [false, '  '];
        yield [false, 'null'];
        yield [false, 'true'];
        yield [false, 'false'];
        yield [true, '[]'];
        yield [true, '{}'];
        yield [false, '123'];
        yield [true, '{"foo": "bar"}'];
        yield [false, '{"foo":"bar",}'];
        yield [false, '{"foo"}'];
        yield [true, '["foo"]'];
        yield [false, '{"foo": "bar"]'];
        yield [false, '123', 'UTF-8'];
        yield [true, '{"fòô": "bàř"}', 'UTF-8'];
        yield [false, '{"fòô":"bàř",}', 'UTF-8'];
        yield [false, '{"fòô"}', 'UTF-8'];
        yield [false, '["fòô": "bàř"]', 'UTF-8'];
        yield [true, '["fòô"]', 'UTF-8'];
        yield [false, '{"fòô": "bàř"]', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function isJsonDoNotIgnoreProvider(): \Iterator
    {
        yield [false, ''];
        yield [false, '  '];
        yield [true, 'null'];
        yield [true, 'true'];
        yield [true, 'false'];
        yield [true, '[]'];
        yield [true, '{}'];
        yield [true, '123'];
        yield [true, '{"foo": "bar"}'];
        yield [false, '{"foo":"bar",}'];
        yield [false, '{"foo"}'];
        yield [true, '["foo"]'];
        yield [false, '{"foo": "bar"]'];
        yield [true, '123', 'UTF-8'];
        yield [true, '{"fòô": "bàř"}', 'UTF-8'];
        yield [false, '{"fòô":"bàř",}', 'UTF-8'];
        yield [false, '{"fòô"}', 'UTF-8'];
        yield [false, '["fòô": "bàř"]', 'UTF-8'];
        yield [true, '["fòô"]', 'UTF-8'];
        yield [false, '{"fòô": "bàř"]', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function isLowerCaseProvider(): \Iterator
    {
        yield [true, ''];
        yield [true, 'foobar'];
        yield [false, 'foo bar'];
        yield [false, 'Foobar'];
        yield [true, 'fòôbàř', 'UTF-8'];
        yield [false, 'fòôbàř2', 'UTF-8'];
        yield [false, 'fòô bàř', 'UTF-8'];
        yield [false, 'fòôbÀŘ', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function isProvider(): \Iterator
    {
        yield [true, 'Gears\\String\\Str', 'Gears\\String\\Str'];
        yield [true, 'Gears\\String\\Str', 'Gears\\*\\Str'];
        yield [true, 'Gears\\String\\Str', 'Gears\\*\\*'];
        yield [true, 'Gears\\String\\Str', '*\\*\\*'];
        yield [true, 'Gears\\String\\Str', '*\\String\\*'];
        yield [true, 'Gears\\String\\Str', '*\\*\\Str'];
        yield [true, 'Gears\\String\\Str', '*\\Str'];
        yield [true, 'Gears\\String\\Str', '*'];
        yield [true, 'Gears\\String\\Str', '**'];
        yield [true, 'Gears\\String\\Str', '****'];
        yield [true, 'Gears\\String\\Str', '*Str'];
        yield [false, 'Gears\\String\\Str', '*\\'];
        yield [false, 'Gears\\String\\Str', 'Gears-*-*'];
    }

    /**
     * @return \Iterator
     */
    public function isSerializedProvider(): \Iterator
    {
        yield [false, ''];
        yield [true, 'a:1:{s:3:"foo";s:3:"bar";}'];
        yield [false, 'a:1:{s:3:"foo";s:3:"bar"}'];
        yield [true, \serialize(['foo' => 'bar'])];
        yield [true, 'a:1:{s:5:"fòô";s:5:"bàř";}', 'UTF-8'];
        yield [false, 'a:1:{s:5:"fòô";s:5:"bàř"}', 'UTF-8'];
        yield [true, \serialize(['fòô' => 'bár']), 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function isUpperCaseProvider(): \Iterator
    {
        yield [true, ''];
        yield [true, 'FOOBAR'];
        yield [false, 'FOO BAR'];
        yield [false, 'fOOBAR'];
        yield [true, 'FÒÔBÀŘ', 'UTF-8'];
        yield [false, 'FÒÔBÀŘ2', 'UTF-8'];
        yield [false, 'FÒÔ BÀŘ', 'UTF-8'];
        yield [false, 'FÒÔBàř', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function lastProvider(): \Iterator
    {
        yield ['', 'foo bar', -5];
        yield ['', 'foo bar', 0];
        yield ['r', 'foo bar', 1];
        yield ['bar', 'foo bar', 3];
        yield ['foo bar', 'foo bar', 7];
        yield ['foo bar', 'foo bar', 8];
        yield ['', 'fòô bàř', -5, 'UTF-8'];
        yield ['', 'fòô bàř', 0, 'UTF-8'];
        yield ['ř', 'fòô bàř', 1, 'UTF-8'];
        yield ['bàř', 'fòô bàř', 3, 'UTF-8'];
        yield ['fòô bàř', 'fòô bàř', 7, 'UTF-8'];
        yield ['fòô bàř', 'fòô bàř', 8, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function lengthProvider(): \Iterator
    {
        yield [11, '  foo bar  '];
        yield [1, 'f'];
        yield [0, ''];
        yield [7, 'fòô bàř', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function linesProvider(): \Iterator
    {
        yield [[], ''];
        yield [[''], "\r\n"];
        yield [['foo', 'bar'], "foo\nbar"];
        yield [['foo', 'bar'], "foo\rbar"];
        yield [['foo', 'bar'], "foo\r\nbar"];
        yield [['foo', '', 'bar'], "foo\r\n\r\nbar"];
        yield [['foo', 'bar', ''], "foo\r\nbar\r\n"];
        yield [['', 'foo', 'bar'], "\r\nfoo\r\nbar"];
        yield [['fòô', 'bàř'], "fòô\nbàř", 'UTF-8'];
        yield [['fòô', 'bàř'], "fòô\rbàř", 'UTF-8'];
        yield [['fòô', 'bàř'], "fòô\n\rbàř", 'UTF-8'];
        yield [['fòô', 'bàř'], "fòô\r\nbàř", 'UTF-8'];
        yield [['fòô', '', 'bàř'], "fòô\r\n\r\nbàř", 'UTF-8'];
        yield [['fòô', 'bàř', ''], "fòô\r\nbàř\r\n", 'UTF-8'];
        yield [['', 'fòô', 'bàř'], "\r\nfòô\r\nbàř", 'UTF-8'];
        yield [['1111111111111111111'], '1111111111111111111', 'UTF-8'];
        yield [['1111111111111111111111'], '1111111111111111111111', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function longestCommonPrefixProvider(): \Iterator
    {
        yield ['foo', 'foobar', 'foo bar'];
        yield ['foo bar', 'foo bar', 'foo bar'];
        yield ['f', 'foo bar', 'far boo'];
        yield ['', 'toy car', 'foo bar'];
        yield ['', 'foo bar', ''];
        yield ['fòô', 'fòôbar', 'fòô bar', 'UTF-8'];
        yield ['fòô bar', 'fòô bar', 'fòô bar', 'UTF-8'];
        yield ['fò', 'fòô bar', 'fòr bar', 'UTF-8'];
        yield ['', 'toy car', 'fòô bar', 'UTF-8'];
        yield ['', 'fòô bar', '', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function longestCommonSubstringProvider(): \Iterator
    {
        yield ['foo', 'foobar', 'foo bar'];
        yield ['foo bar', 'foo bar', 'foo bar'];
        yield ['oo ', 'foo bar', 'boo far'];
        yield ['foo ba', 'foo bad', 'foo bar'];
        yield ['', 'foo bar', ''];
        yield ['', 'foo', 'lall'];
        yield ['fòô', 'fòôbàř', 'fòô bàř', 'UTF-8'];
        yield ['fòô bàř', 'fòô bàř', 'fòô bàř', 'UTF-8'];
        yield [' bàř', 'fòô bàř', 'fòr bàř', 'UTF-8'];
        yield [' ', 'toy car', 'fòô bàř', 'UTF-8'];
        yield ['', 'fòô bàř', '', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function longestCommonSuffixProvider(): \Iterator
    {
        yield ['bar', 'foobar', 'foo bar'];
        yield ['foo bar', 'foo bar', 'foo bar'];
        yield ['ar', 'foo bar', 'boo far'];
        yield ['', 'foo bad', 'foo bar'];
        yield ['', 'foo bar', ''];
        yield ['bàř', 'fòôbàř', 'fòô bàř', 'UTF-8'];
        yield ['fòô bàř', 'fòô bàř', 'fòô bàř', 'UTF-8'];
        yield [' bàř', 'fòô bàř', 'fòr bàř', 'UTF-8'];
        yield ['', 'toy car', 'fòô bàř', 'UTF-8'];
        yield ['', 'fòô bàř', '', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function lowerCaseFirstProvider(): \Iterator
    {
        yield ['test', 'Test'];
        yield ['test', 'test'];
        yield ['1a', '1a'];
        yield ['σ test', 'Σ test', 'UTF-8'];
        yield [' Σ test', ' Σ test', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function offsetExistsProvider(): \Iterator
    {
        yield [true, 0];
        yield [true, 2];
        yield [false, 3];
        yield [true, -1];
        yield [true, -3];
        yield [false, -4];
    }

    /**
     * @return \Iterator
     */
    public function padBothProvider(): \Iterator
    {
        yield ['foo bar ', 'foo bar', 8];
        yield [' foo bar ', 'foo bar', 9, ' '];
        yield ['fòô bàř ', 'fòô bàř', 8, ' ', 'UTF-8'];
        yield [' fòô bàř ', 'fòô bàř', 9, ' ', 'UTF-8'];
        yield ['fòô bàř¬', 'fòô bàř', 8, '¬ø', 'UTF-8'];
        yield ['¬fòô bàř¬', 'fòô bàř', 9, '¬ø', 'UTF-8'];
        yield ['¬fòô bàř¬ø', 'fòô bàř', 10, '¬ø', 'UTF-8'];
        yield ['¬øfòô bàř¬ø', 'fòô bàř', 11, '¬ø', 'UTF-8'];
        yield ['¬fòô bàř¬ø', 'fòô bàř', 10, '¬øÿ', 'UTF-8'];
        yield ['¬øfòô bàř¬ø', 'fòô bàř', 11, '¬øÿ', 'UTF-8'];
        yield ['¬øfòô bàř¬øÿ', 'fòô bàř', 12, '¬øÿ', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function padLeftProvider(): \Iterator
    {
        yield ['  foo bar', 'foo bar', 9];
        yield ['_*foo bar', 'foo bar', 9, '_*'];
        yield ['_*_foo bar', 'foo bar', 10, '_*'];
        yield ['  fòô bàř', 'fòô bàř', 9, ' ', 'UTF-8'];
        yield ['¬øfòô bàř', 'fòô bàř', 9, '¬ø', 'UTF-8'];
        yield ['¬ø¬fòô bàř', 'fòô bàř', 10, '¬ø', 'UTF-8'];
        yield ['¬ø¬øfòô bàř', 'fòô bàř', 11, '¬ø', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function padProvider(): \Iterator
    {
        // length <= str
        yield ['foo bar', 'foo bar', -1];
        yield ['foo bar', 'foo bar', 7];
        yield ['fòô bàř', 'fòô bàř', 7, ' ', 'right', 'UTF-8'];
        // right
        yield ['foo bar  ', 'foo bar', 9];
        yield ['foo bar_*', 'foo bar', 9, '_*', 'right'];
        yield ['fòô bàř¬ø¬', 'fòô bàř', 10, '¬ø', 'right', 'UTF-8'];
        // left
        yield ['  foo bar', 'foo bar', 9, ' ', 'left'];
        yield ['_*foo bar', 'foo bar', 9, '_*', 'left'];
        yield ['¬ø¬fòô bàř', 'fòô bàř', 10, '¬ø', 'left', 'UTF-8'];
        // both
        yield ['foo bar ', 'foo bar', 8, ' ', 'both'];
        yield ['¬fòô bàř¬ø', 'fòô bàř', 10, '¬ø', 'both', 'UTF-8'];
        yield ['¬øfòô bàř¬øÿ', 'fòô bàř', 12, '¬øÿ', 'both', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function padRightProvider(): \Iterator
    {
        yield ['foo bar  ', 'foo bar', 9];
        yield ['foo bar_*', 'foo bar', 9, '_*'];
        yield ['foo bar_*_', 'foo bar', 10, '_*'];
        yield ['fòô bàř  ', 'fòô bàř', 9, ' ', 'UTF-8'];
        yield ['fòô bàř¬ø', 'fòô bàř', 9, '¬ø', 'UTF-8'];
        yield ['fòô bàř¬ø¬', 'fòô bàř', 10, '¬ø', 'UTF-8'];
        yield ['fòô bàř¬ø¬ø', 'fòô bàř', 11, '¬ø', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function prependProvider(): \Iterator
    {
        yield ['foobar', 'bar', 'foo'];
        yield ['fòôbàř', 'bàř', 'fòô', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function regexReplaceProvider(): \Iterator
    {
        yield ['', '', '', ''];
        yield ['bar', 'foo', 'f[o]+', 'bar'];
        yield ['//bar//', '/foo/', '/f[o]+/', '//bar//', 'msr', '#'];
        yield ['o bar', 'foo bar', 'f(o)o', '\1'];
        yield ['bar', 'foo bar', 'f[O]+\s', '', 'i'];
        yield ['foo', 'bar', '[[:alpha:]]{3}', 'foo'];
        yield ['', '', '', '', 'msr', '/', 'UTF-8'];
        yield ['bàř', 'fòô ', 'f[òô]+\s', 'bàř', 'msr', '/', 'UTF-8'];
        yield ['fòô', 'fò', '(ò)', '\\1ô', 'msr', '/', 'UTF-8'];
        yield ['fòô', 'bàř', '[[:alpha:]]{3}', 'fòô', 'msr', '/', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function removeHtmlBreakProvider(): \Iterator
    {
        yield ['', ''];
        yield ['raboof <3', 'raboof <3', '<ä>'];
        yield ['řàbôòf <foo<lall>>>', 'řàbôòf<br/><foo<lall>>>', ' '];
        yield [
            'řàb <ô>òf\', ô<br><br/>foo <a href="#">lall</a>',
            'řàb <ô>òf\', ô<br/>foo <a href="#">lall</a>',
            '<br><br/>',
        ];
        yield ['<∂∆ onerror="alert(xss)">˚åß', '<∂∆ onerror="alert(xss)">' . "\n" . '˚åß'];
        yield ['\'œ … \'’)', '\'œ … \'’)'];
    }

    /**
     * @return \Iterator
     */
    public function removeHtmlProvider(): \Iterator
    {
        yield ['', ''];
        yield ['raboof ', 'raboof <3', '<3>'];
        yield ['řàbôòf>', 'řàbôòf<foo<lall>>>', '<lall><lall/>'];
        yield ['řàb òf\', ô<br/>foo lall', 'řàb <ô>òf\', ô<br/>foo <a href="#">lall</a>', '<br><br/>'];
        yield [' ˚åß', '<∂∆ onerror="alert(xss)"> ˚åß'];
        yield ['\'œ … \'’)', '\'œ … \'’)'];
    }

    /**
     * @return \Iterator
     */
    public function removeLeftProvider(): \Iterator
    {
        yield ['foo bar', 'foo bar', ''];
        yield ['oo bar', 'foo bar', 'f'];
        yield ['bar', 'foo bar', 'foo '];
        yield ['foo bar', 'foo bar', 'oo'];
        yield ['foo bar', 'foo bar', 'oo bar'];
        yield ['oo bar', 'foo bar', UTF8::first_char('foo bar', 1), 'UTF-8'];
        yield ['oo bar', 'foo bar', UTF8::char_at('foo bar', 0), 'UTF-8'];
        yield ['fòô bàř', 'fòô bàř', '', 'UTF-8'];
        yield ['òô bàř', 'fòô bàř', 'f', 'UTF-8'];
        yield ['bàř', 'fòô bàř', 'fòô ', 'UTF-8'];
        yield ['fòô bàř', 'fòô bàř', 'òô', 'UTF-8'];
        yield ['fòô bàř', 'fòô bàř', 'òô bàř', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function removeRightProvider(): \Iterator
    {
        yield ['foo bar', 'foo bar', ''];
        yield ['foo ba', 'foo bar', 'r'];
        yield ['foo', 'foo bar', ' bar'];
        yield ['foo bar', 'foo bar', 'ba'];
        yield ['foo bar', 'foo bar', 'foo ba'];
        yield ['foo ba', 'foo bar', UTF8::str_last_char('foo bar', 1), 'UTF-8'];
        yield ['foo ba', 'foo bar', UTF8::char_at('foo bar', 6), 'UTF-8'];
        yield ['fòô bàř', 'fòô bàř', '', 'UTF-8'];
        yield ['fòô bà', 'fòô bàř', 'ř', 'UTF-8'];
        yield ['fòô', 'fòô bàř', ' bàř', 'UTF-8'];
        yield ['fòô bàř', 'fòô bàř', 'bà', 'UTF-8'];
        yield ['fòô bàř', 'fòô bàř', 'fòô bà', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function removeXssProvider(): \Iterator
    {
        yield ['', ''];
        yield [
            'Hello, i try to alert&#40;\'Hack\'&#41;; your site',
            'Hello, i try to <script>alert(\'Hack\');</script> your site',
        ];
        yield [
            '<IMG >',
            '<IMG SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29>',
        ];
        yield ['&lt;XSS &gt;', '<XSS STYLE="behavior: url(xss.htc);">'];
        yield ['<∂∆ > ˚åß', '<∂∆ onerror="alert(xss)"> ˚åß'];
        yield ['\'œ … <a href="#foo"> \'’)', '\'œ … <a href="#foo"> \'’)'];
    }

    /**
     * @return \Iterator
     */
    public function repeatProvider(): \Iterator
    {
        yield ['', 'foo', 0];
        yield ['foo', 'foo', 1];
        yield ['foofoo', 'foo', 2];
        yield ['foofoofoo', 'foo', 3];
        yield ['fòô', 'fòô', 1, 'UTF-8'];
        yield ['fòôfòô', 'fòô', 2, 'UTF-8'];
        yield ['fòôfòôfòô', 'fòô', 3, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function replaceAllProvider(): \Iterator
    {
        yield ['', '', [], ''];
        yield ['', '', [''], ''];
        yield ['foo', ' ', [' ', ''], 'foo'];
        yield ['foo', '\s', ['\s', '\t'], 'foo'];
        yield ['foo bar', 'foo bar', [''], ''];
        yield ['\1 bar', 'foo bar', ['f(o)o', 'foo'], '\1'];
        yield ['\1 \1', 'foo bar', ['foo', 'föö', 'bar'], '\1'];
        yield ['bar', 'foo bar', ['foo '], ''];
        yield ['far bar', 'foo bar', ['foo'], 'far'];
        yield ['bar bar', 'foo bar foo bar', ['foo ', ' foo'], ''];
        yield ['bar bar bar bar', 'foo bar foo bar', ['foo ', ' foo'], ['bar ', ' bar']];
        yield ['', '', [''], '', 'UTF-8'];
        yield ['fòô', ' ', [' ', '', '  '], 'fòô', 'UTF-8'];
        yield ['fòôòô', '\s', ['\s', 'f'], 'fòô', 'UTF-8'];
        yield ['fòô bàř', 'fòô bàř', [''], '', 'UTF-8'];
        yield ['bàř', 'fòô bàř', ['fòô '], '', 'UTF-8'];
        yield ['far bàř', 'fòô bàř', ['fòô'], 'far', 'UTF-8'];
        yield ['bàř bàř', 'fòô bàř fòô bàř', ['fòô ', 'fòô'], '', 'UTF-8'];
        yield ['bàř bàř', 'fòô bàř fòô bàř', ['fòô '], ''];
        yield ['bàř bàř', 'fòô bàř fòô bàř', ['fòô '], ''];
        yield ['fòô bàř fòô bàř', 'fòô bàř fòô bàř', ['Fòô '], ''];
        yield ['fòô bàř fòô bàř', 'fòô bàř fòô bàř', ['fòÔ '], ''];
        yield ['fòô bàř bàř', 'fòô bàř [[fòô]] bàř', ['[[fòô]] ', '[]'], ''];
        yield ['', '', [''], '', 'UTF-8', false];
        yield ['fòô', ' ', [' ', '', '  '], 'fòô', 'UTF-8', false];
        yield ['fòôòô', '\s', ['\s', 'f'], 'fòô', 'UTF-8', false];
        yield ['fòô bàř', 'fòô bàř', [''], '', 'UTF-8', false];
        yield ['bàř', 'fòô bàř', ['fòÔ '], '', 'UTF-8', false];
        yield ['bàř', 'fòô bàř', ['fòÔ '], [''], 'UTF-8', false];
        yield ['far bàř', 'fòô bàř', ['Fòô'], 'far', 'UTF-8', false];
    }

    /**
     * @return \Iterator
     */
    public function replaceBeginningProvider(): \Iterator
    {
        yield ['', '', '', ''];
        yield ['foo', '', '', 'foo'];
        yield ['foo', '\s', '\s', 'foo'];
        yield ['foo bar', 'foo bar', '', ''];
        yield ['foo bar', 'foo bar', 'f(o)o', '\1'];
        yield ['\1 bar', 'foo bar', 'foo', '\1'];
        yield ['Foo bar', 'Foo bar', 'foo', '\1'];
        yield ['bar', 'foo bar', 'foo ', ''];
        yield ['far bar', 'foo bar', 'foo', 'far'];
        yield ['bar foo bar', 'foo bar foo bar', 'foo ', ''];
        yield ['', '', '', '', 'UTF-8'];
        yield ['fòô', '', '', 'fòô', 'UTF-8'];
        yield ['fòô', '\s', '\s', 'fòô', 'UTF-8'];
        yield ['fòô bàř', 'fòô bàř', '', '', 'UTF-8'];
        yield ['bàř', 'fòô bàř', 'fòô ', '', 'UTF-8'];
        yield ['far bàř', 'fòô bàř', 'fòô', 'far', 'UTF-8'];
        yield ['bàř fòô bàř', 'fòô bàř fòô bàř', 'fòô ', '', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function ireplaceBeginningProvider(): \Iterator
    {
        yield ['', '', '', ''];
        yield ['foo', '', '', 'foo'];
        yield ['foo', '\s', '\s', 'foo'];
        yield ['foo bar', 'foo bar', '', ''];
        yield ['foo bar', 'foo bar', 'f(o)o', '\1'];
        yield ['\1 bar', 'foo bar', 'foo', '\1'];
        yield ['\1 bar', 'Foo bar', 'foo', '\1'];
        yield ['bar', 'foo bar', 'foo ', ''];
        yield ['far bar', 'foo bar', 'foo', 'far'];
        yield ['bar foo bar', 'foo bar foo bar', 'foo ', ''];
        yield ['', '', '', '', 'UTF-8'];
        yield ['fòô', '', '', 'fòô', 'UTF-8'];
        yield ['fòô', '\s', '\s', 'fòô', 'UTF-8'];
        yield ['fòô bàř', 'fòô bàř', '', '', 'UTF-8'];
        yield ['bàř', 'fòô bàř', 'fòô ', '', 'UTF-8'];
        yield ['far bàř', 'fòô bàř', 'fòô', 'far', 'UTF-8'];
        yield ['bàř fòô bàř', 'fòô bàř fòô bàř', 'fòô ', '', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function replaceEndingProvider(): \Iterator
    {
        yield ['', '', '', ''];
        yield ['foo', '', '', 'foo'];
        yield ['foo', '\s', '\s', 'foo'];
        yield ['foo bar', 'foo bar', '', ''];
        yield ['foo bar', 'foo bar', 'f(o)o', '\1'];
        yield ['foo \1', 'foo bar', 'bar', '\1'];
        yield ['foo Bar', 'foo Bar', 'bar', '\1'];
        yield ['foo bar', 'foo bar', 'foo ', ''];
        yield ['foo lall', 'foo bar', 'bar', 'lall'];
        yield ['foo bar foo ', 'foo bar foo bar', 'bar', ''];
        yield ['', '', '', '', 'UTF-8'];
        yield ['fòô', '', '', 'fòô', 'UTF-8'];
        yield ['fòô', '\s', '\s', 'fòô', 'UTF-8'];
        yield ['fòô bàř', 'fòô bàř', '', '', 'UTF-8'];
        yield ['fòô', 'fòô bàř', ' bàř', '', 'UTF-8'];
        yield ['fòôfar', 'fòô bàř', ' bàř', 'far', 'UTF-8'];
        yield ['fòô bàř fòô', 'fòô bàř fòô bàř', ' bàř', '', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function ireplaceEndingProvider(): \Iterator
    {
        yield ['', '', '', ''];
        yield ['foo', '', '', 'foo'];
        yield ['foo', '\s', '\s', 'foo'];
        yield ['foo bar', 'foo bar', '', ''];
        yield ['foo bar', 'foo bar', 'f(o)o', '\1'];
        yield ['foo \1', 'foo bar', 'bar', '\1'];
        yield ['foo \1', 'foo Bar', 'bar', '\1'];
        yield ['foo bar', 'foo bar', 'foo ', ''];
        yield ['foo lall', 'foo bar', 'bar', 'lall'];
        yield ['foo bar foo ', 'foo bar foo bar', 'bar', ''];
        yield ['', '', '', '', 'UTF-8'];
        yield ['fòô', '', '', 'fòô', 'UTF-8'];
        yield ['fòô', '\s', '\s', 'fòô', 'UTF-8'];
        yield ['fòô bàř', 'fòô bàř', '', '', 'UTF-8'];
        yield ['fòô', 'fòô bàř', ' bàř', '', 'UTF-8'];
        yield ['fòôfar', 'fòô bàř', ' bàř', 'far', 'UTF-8'];
        yield ['fòô bàř fòô', 'fòô bàř fòô bàř', ' bàř', '', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function replaceProvider(): \Iterator
    {
        yield ['', '', '', ''];
        yield ['foo', ' ', ' ', 'foo'];
        yield ['foo', '\s', '\s', 'foo'];
        yield ['foo bar', 'foo bar', '', ''];
        yield ['foo bar', 'foo bar', 'f(o)o', '\1'];
        yield ['\1 bar', 'foo bar', 'foo', '\1'];
        yield ['bar', 'foo bar', 'foo ', ''];
        yield ['far bar', 'foo bar', 'foo', 'far'];
        yield ['bar bar', 'foo bar foo bar', 'foo ', ''];
        yield ['', '', '', '', 'UTF-8'];
        yield ['fòô', ' ', ' ', 'fòô', 'UTF-8'];
        yield ['fòô', '\s', '\s', 'fòô', 'UTF-8'];
        yield ['fòô bàř', 'fòô bàř', '', '', 'UTF-8'];
        yield ['bàř', 'fòô bàř', 'fòô ', '', 'UTF-8'];
        yield ['far bàř', 'fòô bàř', 'fòô', 'far', 'UTF-8'];
        yield ['bàř bàř', 'fòô bàř fòô bàř', 'fòô ', '', 'UTF-8'];
        yield ['bàř bàř', 'fòô bàř fòô bàř', 'fòô ', ''];
        yield ['bàř bàř', 'fòô bàř fòô bàř', 'fòô ', ''];
        yield ['fòô bàř fòô bàř', 'fòô bàř fòô bàř', 'Fòô ', ''];
        yield ['fòô bàř fòô bàř', 'fòô bàř fòô bàř', 'fòÔ ', ''];
        yield ['fòô bàř bàř', 'fòô bàř [[fòô]] bàř', '[[fòô]] ', ''];
        yield ['', '', '', '', 'UTF-8', false];
        yield ['òô', ' ', ' ', 'òô', 'UTF-8', false];
        yield ['fòô', '\s', '\s', 'fòô', 'UTF-8', false];
        yield ['fòô bàř', 'fòô bàř', '', '', 'UTF-8', false];
        yield ['bàř', 'fòô bàř', 'Fòô ', '', 'UTF-8', false];
        yield ['far bàř', 'fòô bàř', 'fòÔ', 'far', 'UTF-8', false];
        yield ['bàř bàř', 'fòô bàř fòô bàř', 'Fòô ', '', 'UTF-8', false];
    }

    /**
     * @return \Iterator
     */
    public function reverseProvider(): \Iterator
    {
        yield ['', ''];
        yield ['raboof', 'foobar'];
        yield ['řàbôòf', 'fòôbàř', 'UTF-8'];
        yield ['řàb ôòf', 'fòô bàř', 'UTF-8'];
        yield ['∂∆ ˚åß', 'ßå˚ ∆∂', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function safeTruncateProvider(): \Iterator
    {
        yield ['Test foo bar', 'Test foo bar', 12];
        yield ['Test foo', 'Test foo bar', 11];
        yield ['Test foo', 'Test foo bar', 8];
        yield ['Test', 'Test foo bar', 7];
        yield ['Test', 'Test foo bar', 4];
        yield ['Test', 'Testfoobar', 4];
        yield ['Test foo bar', 'Test foo bar', 12, '...'];
        yield ['Test foo...', 'Test foo bar', 11, '...'];
        yield ['Test...', 'Test foo bar', 8, '...'];
        yield ['Test...', 'Test foo bar', 7, '...'];
        yield ['...', 'Test foo bar', 4, '...'];
        yield ['Test....', 'Test foo bar', 11, '....'];
        yield ['Test fòô bàř', 'Test fòô bàř', 12, '', 'UTF-8'];
        yield ['Test fòô', 'Test fòô bàř', 11, '', 'UTF-8'];
        yield ['Test fòô', 'Test fòô bàř', 8, '', 'UTF-8'];
        yield ['Test', 'Test fòô bàř', 7, '', 'UTF-8'];
        yield ['Test', 'Test fòô bàř', 4, '', 'UTF-8'];
        yield ['Test fòô bàř', 'Test fòô bàř', 12, 'ϰϰ', 'UTF-8'];
        yield ['Test fòôϰϰ', 'Test fòô bàř', 11, 'ϰϰ', 'UTF-8'];
        yield ['Testϰϰ', 'Test fòô bàř', 8, 'ϰϰ', 'UTF-8'];
        yield ['Testϰϰ', 'Test fòô bàř', 7, 'ϰϰ', 'UTF-8'];
        yield ['ϰϰ', 'Test fòô bàř', 4, 'ϰϰ', 'UTF-8'];
        yield ['What are your plans...', 'What are your plans today?', 22, '...'];
    }

    /**
     * @return \Iterator
     */
    public function safeTruncateIgnoreWordsProvider(): \Iterator
    {
        yield ['Test foo bar', 'Test foo bar', 12];
        yield ['Test foo', 'Test foo bar', 11];
        yield ['Test foo', 'Test foo bar', 8];
        yield ['Test', 'Test foo bar', 7];
        yield ['Test', 'Test foo bar', 4];
        yield ['Test', 'Testfoobar', 4];
        yield ['Test foo bar', 'Test foo bar', 12, '...'];
        yield ['Test foo...', 'Test foo bar', 11, '...'];
        yield ['Test...', 'Test foo bar', 8, '...'];
        yield ['Test...', 'Test foo bar', 7, '...'];
        yield ['T...', 'Test foo bar', 4, '...'];
        yield ['Test....', 'Test foo bar', 11, '....'];
        yield ['Test fòô bàř', 'Test fòô bàř', 12, '', 'UTF-8'];
        yield ['Test fòô', 'Test fòô bàř', 11, '', 'UTF-8'];
        yield ['Test fòô', 'Test fòô bàř', 8, '', 'UTF-8'];
        yield ['Test', 'Test fòô bàř', 7, '', 'UTF-8'];
        yield ['Test', 'Test fòô bàř', 4, '', 'UTF-8'];
        yield ['Test fòô bàř', 'Test fòô bàř', 12, 'ϰϰ', 'UTF-8'];
        yield ['Test fòôϰϰ', 'Test fòô bàř', 11, 'ϰϰ', 'UTF-8'];
        yield ['Testϰϰ', 'Test fòô bàř', 8, 'ϰϰ', 'UTF-8'];
        yield ['Testϰϰ', 'Test fòô bàř', 7, 'ϰϰ', 'UTF-8'];
        yield ['Teϰϰ', 'Test fòô bàř', 4, 'ϰϰ', 'UTF-8'];
        yield ['What are your plans...', 'What are your plans today?', 22, '...'];
    }

    /**
     * @return \Iterator
     */
    public function shortenAfterWordProvider(): \Iterator
    {
        yield ['this...', 'this is a test', 5, '...'];
        yield ['this is...', 'this is öäü-foo test', 8, '...'];
        yield ['fòô', 'fòô bàř fòô', 6, ''];
        yield ['fòô bàř', 'fòô bàř fòô', 8, ''];
    }

    /**
     * @return \Iterator
     */
    public function shuffleProvider(): \Iterator
    {
        yield ['foo bar'];
        yield ['∂∆ ˚åß', 'UTF-8'];
        yield ['å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function sliceProvider(): \Iterator
    {
        yield ['r', 'foobar', -1];
        yield ['', 'foobar', 999];
        yield ['foobar', 'foobar', 0];
        yield ['foobar', 'foobar', 0, null];
        yield ['foobar', 'foobar', 0, 6];
        yield ['fooba', 'foobar', 0, 5];
        yield ['', 'foobar', 3, 0];
        yield ['', 'foobar', 3, 2];
        yield ['ba', 'foobar', 3, 5];
        yield ['ba', 'foobar', 3, -1];
        yield ['fòôbàř', 'fòôbàř', 0, null, 'UTF-8'];
        yield ['fòôbàř', 'fòôbàř', 0, null];
        yield ['fòôbàř', 'fòôbàř', 0, 6, 'UTF-8'];
        yield ['fòôbà', 'fòôbàř', 0, 5, 'UTF-8'];
        yield ['', 'fòôbàř', 3, 0, 'UTF-8'];
        yield ['', 'fòôbàř', 3, 2, 'UTF-8'];
        yield ['bà', 'fòôbàř', 3, 5, 'UTF-8'];
        yield ['bà', 'fòôbàř', 3, -1, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function slugifyProvider(): \Iterator
    {
        yield ['foo-bar', ' foo  bar '];
        yield ['foo-bar', 'foo -.-"-...bar'];
        yield ['another-und-foo-bar', 'another..& foo -.-"-...bar'];
        yield ['foo-dbar', " Foo d'Bar "];
        yield ['a-string-with-dashes', 'A string-with-dashes'];
        yield ['using-strings-like-foo-bar', 'Using strings like fòô bàř'];
        yield ['numbers-1234', 'numbers 1234'];
        yield ['perevirka-ryadka', 'перевірка рядка'];
        yield ['bukvar-s-bukvoj-y', 'букварь с буквой ы'];
        yield ['podehal-k-podezdu-moego-doma', 'подъехал к подъезду моего дома'];
        yield ['foo:bar:baz', 'Foo bar baz', ':'];
        yield ['a_string_with_underscores', 'A_string with_underscores', '_'];
        yield ['a_string_with_dashes', 'A string-with-dashes', '_'];
        yield ['a\string\with\dashes', 'A string-with-dashes', '\\'];
        yield ['an_odd_string', '--   An odd__   string-_', '_'];
    }

    /**
     * @return \Iterator
     */
    public function snakeizeProvider(): \Iterator
    {
        yield ['snake_case', 'SnakeCase'];
        yield ['snake_case', 'Snake-Case'];
        yield ['snake_case', 'snake case'];
        yield ['snake_case', 'snake -case'];
        yield ['snake_case', ' snake -case  '];
        yield ['snake_case', "\n\t " . ' snake -case  ' . "\n"];
        yield ['snake_case', 'snake - case'];
        yield ['snake_case', 'snake_case'];
        yield ['camel_c_test', 'camel c test'];
        yield ['string_with_1_number', 'string_with 1 number'];
        yield ['string_with_1_number', 'string_with1number'];
        yield ['string_with_2_2_numbers', 'string-with-2-2 numbers'];
        yield ['data_rate', 'data_rate'];
        yield ['background_color', 'background-color'];
        yield ['yes_we_can', 'yes_we_can'];
        yield ['moz_something', '-moz-something'];
        yield ['car_speed', '_car_speed_'];
        yield ['serve_h_t_t_p', 'ServeHTTP'];
        yield ['1_camel_2_case', '1camel2case'];
        yield ['camel_σase', 'camel σase', 'UTF-8'];
        yield ['στανιλ_case', 'Στανιλ case', 'UTF-8'];
        yield ['σamel_case', 'σamel  Case', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function splitProvider(): \Iterator
    {
        yield [['foo,bar,baz'], 'foo,bar,baz', ''];
        yield [['foo,bar,baz'], 'foo,bar,baz', '-'];
        yield [['foo', 'bar', 'baz'], 'foo,bar,baz', ','];
        yield [['foo', 'bar', 'baz'], 'foo,bar,baz', ',', -1];
        yield [[], 'foo,bar,baz', ',', 0];
        yield [['foo'], 'foo,bar,baz', ',', 1];
        yield [['foo', 'bar'], 'foo,bar,baz', ',', 2];
        yield [['foo', 'bar', 'baz'], 'foo,bar,baz', ',', 3];
        yield [['foo', 'bar', 'baz'], 'foo,bar,baz', ',', 10];
        yield [['fòô,bàř,baz'], 'fòô,bàř,baz', '-', -1, 'UTF-8'];
        yield [['fòô', 'bàř', 'baz'], 'fòô,bàř,baz', ',', -1, 'UTF-8'];
        yield [[], 'fòô,bàř,baz', ',', 0, 'UTF-8'];
        yield [['fòô'], 'fòô,bàř,baz', ',', 1, 'UTF-8'];
        yield [['fòô', 'bàř'], 'fòô,bàř,baz', ',', 2, 'UTF-8'];
        yield [['fòô', 'bàř', 'baz'], 'fòô,bàř,baz', ',', 3, 'UTF-8'];
        yield [['fòô', 'bàř', 'baz'], 'fòô,bàř,baz', ',', 10, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function startsWithProvider(): \Iterator
    {
        yield [true, 'foo bars', 'foo bar'];
        yield [true, 'FOO bars', 'foo bar', false];
        yield [true, 'FOO bars', 'foo BAR', false];
        yield [true, 'FÒÔ bàřs', 'fòô bàř', false, 'UTF-8'];
        yield [true, 'fòô bàřs', 'fòô BÀŘ', false, 'UTF-8'];
        yield [false, 'foo bar', 'bar'];
        yield [false, 'foo bar', 'foo bars'];
        yield [false, 'FOO bar', 'foo bars'];
        yield [false, 'FOO bars', 'foo BAR'];
        yield [false, 'FÒÔ bàřs', 'fòô bàř', true, 'UTF-8'];
        yield [false, 'fòô bàřs', 'fòô BÀŘ', true, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function startsWithProviderAny(): \Iterator
    {
        yield [true, 'foo bars', ['foo bar']];
        yield [true, 'foo bars', ['foo', 'bar']];
        yield [true, 'FOO bars', ['foo', 'bar'], false];
        yield [true, 'FOO bars', ['foo', 'BAR'], false];
        yield [true, 'FÒÔ bàřs', ['fòô', 'bàř'], false, 'UTF-8'];
        yield [true, 'fòô bàřs', ['fòô BÀŘ'], false, 'UTF-8'];
        yield [false, 'foo bar', ['bar']];
        yield [false, 'foo bar', ['foo bars']];
        yield [false, 'FOO bar', ['foo bars']];
        yield [false, 'FOO bars', ['foo BAR']];
        yield [false, 'FÒÔ bàřs', ['fòô bàř'], true, 'UTF-8'];
        yield [false, 'fòô bàřs', ['fòô BÀŘ'], true, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
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

    /**
     * @return \Iterator
     */
    public function substrProvider(): \Iterator
    {
        yield ['foo bar', 'foo bar', 0];
        yield ['bar', 'foo bar', 4];
        yield ['bar', 'foo bar', 4, null];
        yield ['o b', 'foo bar', 2, 3];
        yield ['', 'foo bar', 4, 0];
        yield ['fòô bàř', 'fòô bàř', 0, null, 'UTF-8'];
        yield ['bàř', 'fòô bàř', 4, null, 'UTF-8'];
        yield ['ô b', 'fòô bàř', 2, 3, 'UTF-8'];
        yield ['', 'fòô bàř', 4, 0, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function surroundProvider(): \Iterator
    {
        yield ['__foobar__', 'foobar', '__'];
        yield ['test', 'test', ''];
        yield ['**', '', '*'];
        yield ['¬fòô bàř¬', 'fòô bàř', '¬'];
        yield ['ßå∆˚ test ßå∆˚', ' test ', 'ßå∆˚'];
    }

    /**
     * @return \Iterator
     */
    public function swapCaseProvider(): \Iterator
    {
        yield ['TESTcASE', 'testCase'];
        yield ['tEST-cASE', 'Test-Case'];
        yield [' - σASH  cASE', ' - Σash  Case', 'UTF-8'];
        yield ['νΤΑΝΙΛ', 'Ντανιλ', 'UTF-8'];
    }

    public function testAddRandomString()
    {
        $testArray = [
            'abc'       => [1, 1],
            'öäü'       => [10, 10],
            ''          => [10, 0],
            ' '         => [10, 10],
            'κόσμε-öäü' => [10, 10],
        ];

        foreach ($testArray as $testString => $testResult) {
            $tmpString = UTF8::get_random_string($testResult[0], $testString);

            static::assertSame($testResult[1], UTF8::strlen($tmpString), 'tested: ' . $testString . ' | ' . $tmpString);
        }
    }

    public function testAddUniqueIdentifier()
    {
        $uniquIDs = [];
        for ($i = 0; $i <= 100; ++$i) {
            $uniquIDs[] = UTF8::get_unique_string();
        }

        // detect duplicate values in the array
        foreach (\array_count_values($uniquIDs) as $uniquID => $count) {
            static::assertSame(1, $count);
        }

        // check the string length
        foreach ($uniquIDs as $uniquID) {
            static::assertSame(32, \strlen($uniquID));
        }
    }

    public function testAfterFirst()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>bar'          => 'ar',
            '<h1></h1> '               => '',
            '</b></b>'                 => '></b>',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => '<b></b>',
            '<b><b>lall</b>'           => '><b>lall</b>',
            '</b>lall</b>'             => '>lall</b>',
            '[B][/B]'                  => '',
            '[b][/b]'                  => '][/b]',
            'κόσμbε ¡-öäü'             => 'ε ¡-öäü',
            'bκόσμbε'                  => 'κόσμbε',
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::str_substr_after_first_separator($testString, 'b'));
        }
    }

    public function testAfterFirstIgnoreCase()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>Bar'          => 'ar',
            'foo<h1></h1>bar'          => 'ar',
            '<h1></h1> '               => '',
            '</b></b>'                 => '></b>',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => '<b></b>',
            '<b><b>lall</b>'           => '><b>lall</b>',
            '</b>lall</b>'             => '>lall</b>',
            '[B][/B]'                  => '][/B]',
            'κόσμbε ¡-öäü'             => 'ε ¡-öäü',
            'bκόσμbε'                  => 'κόσμbε',
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::str_isubstr_after_first_separator($testString, 'b'));
        }
    }

    public function testAfterLasIgnoreCaset()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>bar'          => 'ar',
            'foo<h1></h1>Bar'          => 'ar',
            '<h1></h1> '               => '',
            '</b></b>'                 => '>',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => '>',
            '<b><b>lall</b>'           => '>',
            '</b>lall</b>'             => '>',
            '[B][/B]'                  => ']',
            '[b][/b]'                  => ']',
            'κόσμbε ¡-öäü'             => 'ε ¡-öäü',
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::str_isubstr_after_last_separator($testString, 'b'));
        }
    }

    public function testAfterLast()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>bar'          => 'ar',
            '<h1></h1> '               => '',
            '</b></b>'                 => '>',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => '>',
            '<b><b>lall</b>'           => '>',
            '</b>lall</b>'             => '>',
            '[b][/b]'                  => ']',
            '[B][/B]'                  => '',
            'κόσμbε ¡-öäü'             => 'ε ¡-öäü',
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::str_substr_after_last_separator($testString, 'b'));
        }
    }

    /**
     * @dataProvider atProvider()
     *
     * @param $expected
     * @param $str
     * @param $index
     * @param $encoding
     */
    public function testAt($expected, $str, $index, $encoding = '')
    {
        $result = UTF8::char_at($str, $index, $encoding);

        static::assertSame($expected, $result);
    }

    public function testBeforeFirst()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>bar'          => 'foo<h1></h1>',
            '<h1></h1> '               => '',
            '</b></b>'                 => '</',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => ' ',
            '<b><b>lall</b>'           => '<',
            '</b>lall</b>'             => '</',
            '[b][/b]'                  => '[',
            '[B][/B]'                  => '',
            'κόσμbε ¡-öäü'             => 'κόσμ',
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::str_substr_before_first_separator($testString, 'b'));
            static::assertSame($testResult, UTF8::str_substr_first($testString, 'b', true));
        }
    }

    public function testBeforeFirstIgnoreCase()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>Bar'          => 'foo<h1></h1>',
            'foo<h1></h1>bar'          => 'foo<h1></h1>',
            '<h1></h1> '               => '',
            '</b></b>'                 => '</',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => ' ',
            '<b><b>lall</b>'           => '<',
            '</b>lall</b>'             => '</',
            '[B][/B]'                  => '[',
            'κόσμbε ¡-öäü'             => 'κόσμ',
            'Bκόσμbε'                  => '',
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::str_isubstr_before_first_separator($testString, 'b'));
            static::assertSame($testResult, UTF8::str_isubstr_first($testString, 'b', true));
        }
    }

    public function testBeforeLast()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>bar'          => 'foo<h1></h1>',
            '<h1></h1> '               => '',
            '</b></b>'                 => '</b></',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => ' b<b></',
            '<b><b>lall</b>'           => '<b><b>lall</',
            '</b>lall</b>'             => '</b>lall</',
            '[b][/b]'                  => '[b][/',
            '[B][/B]'                  => '',
            'κόσμbε ¡-öäü'             => 'κόσμ',
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::str_substr_before_last_separator($testString, 'b'));
            static::assertSame($testResult, UTF8::str_substr_last($testString, 'b', true));
        }
    }

    public function testBeforeLastIgnoreCase()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>Bar'          => 'foo<h1></h1>',
            'foo<h1></h1>bar'          => 'foo<h1></h1>',
            '<h1></h1> '               => '',
            '</b></b>'                 => '</b></',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => ' b<b></',
            '<b><b>lall</b>'           => '<b><b>lall</',
            '</b>lall</b>'             => '</b>lall</',
            '[B][/B]'                  => '[B][/',
            'κόσμbε ¡-öäü'             => 'κόσμ',
            'bκόσμbε'                  => 'bκόσμ',
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::str_isubstr_before_last_separator($testString, 'b'));
        }
    }

    /**
     * @dataProvider betweenProvider()
     *
     * @param          $expected
     * @param          $str
     * @param          $start
     * @param          $end
     * @param int|null $offset
     * @param string   $encoding
     */
    public function testBetween($expected, $str, $start, $end, $offset = 0, $encoding = '')
    {
        $result = UTF8::between($str, $start, $end, $offset, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider camelizeProvider()
     *
     * @param $expected
     * @param $str
     * @param $encoding
     */
    public function testCamelize($expected, $str, $encoding = '')
    {
        $result = UTF8::str_camelize($str, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider charsProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testChars($expected, $str)
    {
        $result = UTF8::chars($str);
        static::assertTrue(\is_array($result));
        foreach ($result as $char) {
            static::assertTrue(\is_string($char));
        }
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider collapseWhitespaceProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testCollapseWhitespace($expected, $str)
    {
        $result = UTF8::collapse_whitespace($str);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider containsProvider()
     *
     * @param      $expected
     * @param      $haystack
     * @param      $needle
     * @param bool $caseSensitive
     */
    public function testContains($expected, $haystack, $needle, $caseSensitive = true)
    {
        $result = UTF8::str_contains($haystack, $needle, $caseSensitive);
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result, 'tested: "' . $haystack . '" and "' . $needle . '"');
    }

    /**
     * @dataProvider containsAllProvider()
     *
     * @param bool     $expected
     * @param string   $haystack
     * @param string[] $needles
     * @param bool     $caseSensitive
     */
    public function testContainsAll($expected, $haystack, $needles, $caseSensitive = true)
    {
        $result = UTF8::str_contains_all($haystack, $needles, $caseSensitive);
        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result, 'tested: ' . $haystack);
    }

    /**
     * @dataProvider countSubstrByteProvider
     *
     * @param $expected
     * @param $str
     * @param $substring
     */
    public function testCountSubstrInByte($expected, $str, $substring)
    {
        $result = UTF8::substr_count_in_byte($str, $substring);
        static::assertSame($expected, $result, 'tested:' . $str);
    }

    /**
     * @dataProvider countSubstrProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $substring
     * @param bool $caseSensitive
     * @param      $encoding
     */
    public function testCountSubstr($expected, $str, $substring, $caseSensitive = true, $encoding = '')
    {
        $result = UTF8::substr_count_simple($str, $substring, $caseSensitive, $encoding);
        static::assertSame($expected, $result, 'tested:' . $str);
    }

    /**
     * @dataProvider dasherizeProvider()
     *
     * @param $expected
     * @param $str
     * @param $encoding
     */
    public function testDasherize($expected, $str, $encoding = '')
    {
        $result = UTF8::str_dasherize($str, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider delimitProvider()
     *
     * @param $expected
     * @param $str
     * @param $delimiter
     * @param $encoding
     */
    public function testDelimit($expected, $str, $delimiter, $encoding = '')
    {
        $result = UTF8::str_delimit($str, $delimiter, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider endsWithProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $substring
     * @param bool $caseSensitive
     */
    public function testEndsWith($expected, $str, $substring, $caseSensitive = true)
    {
        if ($caseSensitive) {
            $result = UTF8::str_ends_with($str, $substring);
        } else {
            $result = UTF8::str_iends_with($str, $substring);
        }

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider endsWithAnyProvider()
     *
     * @param string $expected
     * @param string $str
     * @param array  $substrings
     * @param bool   $caseSensitive
     */
    public function testEndsWithAny($expected, $str, $substrings, $caseSensitive = true)
    {
        if ($caseSensitive) {
            $result = UTF8::str_ends_with_any($str, $substrings);
        } else {
            $result = UTF8::str_iends_with_any($str, $substrings);
        }

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider ensureLeftProvider()
     *
     * @param $expected
     * @param $str
     * @param $substring
     */
    public function testEnsureLeft($expected, $str, $substring)
    {
        $result = UTF8::str_ensure_left($str, $substring);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider ensureRightProvider()
     *
     * @param $expected
     * @param $str
     * @param $substring
     */
    public function testEnsureRight($expected, $str, $substring)
    {
        $result = UTF8::str_ensure_right($str, $substring);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider escapeProvider()
     *
     * @param $expected
     * @param $str
     * @param $encoding
     */
    public function testEscape($expected, $str, $encoding = '')
    {
        $result = UTF8::html_escape($str, $encoding);

        static::assertSame($expected, $result);
    }

    public function testStrCapitalizeName()
    {
        $testArray = [
            ''                => '',
            '<h1>test</h1>'   => '<h1>test</h1>',
            'Test'            => 'Test',
            'foo bar'         => 'Foo Bar',
            '中文空白'            => '中文空白',
            'mc donalds'      => 'mc Donalds',
            'marcus aurelius' => 'Marcus Aurelius',
            'marcus-aurelius' => 'Marcus-Aurelius',
            'van der meer'    => 'van der Meer',
        ];

        foreach ($testArray as $testString => $testExpected) {
            static::assertSame($testExpected, UTF8::str_capitalize_name($testString), 'tested: ' . $testString);
        }
    }

    public function testExtractText()
    {
        $testArray = [
            ''                                                                                                                                          => '',
            '<h1>test</h1>'                                                                                                                             => '<h1>test</h1>',
            'test'                                                                                                                                      => 'test',
            'A PHP string manipulation library with multibyte support. Compatible with PHP 5.3+, PHP 7, and HHVM.'                                      => 'A PHP string manipulation library with multibyte support…',
            'A PHP string manipulation library with multibyte support. κόσμε-öäü κόσμε-öäü κόσμε-öäü foobar Compatible with PHP 5.3+, PHP 7, and HHVM.' => '…support. κόσμε-öäü κόσμε-öäü κόσμε-öäü foobar Compatible with PHP 5…',
            'A PHP string manipulation library with multibyte support. foobar Compatible with PHP 5.3+, PHP 7, and HHVM.'                               => '…with multibyte support. foobar Compatible with PHP 5…',
        ];

        foreach ($testArray as $testString => $testExpected) {
            static::assertSame($testExpected, UTF8::extract_text($testString, 'foobar'), 'tested: ' . $testString);
        }

        // ----------------

        $testString = 'this is only a Fork of UTF8';
        static::assertSame('…Fork of UTF8', UTF8::extract_text($testString, 'Fork', 0), 'tested: ' . $testString);

        // ----------------

        $testString = 'this is only a Fork of UTF8';
        static::assertSame('…a Fork of UTF8', UTF8::extract_text($testString, 'Fork', 5), 'tested: ' . $testString);

        // ----------------

        $testString = 'This is only a Fork of UTF8, take a look at the new features.';
        static::assertSame('…Fork of UTF8…', UTF8::extract_text($testString, 'UTF8', 15), 'tested: ' . $testString);

        // ----------------

        $testString = 'This is only a Fork of UTF8, take a look at the new features.';
        static::assertSame('…only a Fork of UTF8, take a look…', UTF8::extract_text($testString, 'UTF8'), 'tested: ' . $testString);

        // ----------------

        $testString = 'This is only a Fork of UTF8, take a look at the new features.';
        static::assertSame('This is only a Fork of UTF8, take…', UTF8::extract_text($testString), 'tested: ' . $testString);

        // ----------------

        $testString = 'This is only a Fork of UTF8, take a look at the new features.';
        static::assertSame('This…', UTF8::extract_text($testString, '', 0), 'tested: ' . $testString);

        // ----------------

        $testString = 'This is only a Fork of UTF8, take a look at the new features.';
        static::assertSame('…UTF8, take a look at the new features.', UTF8::extract_text($testString, 'UTF8', 0), 'tested: ' . $testString);

        // ----------------

        $testArray = [
            'Yes. The bird is flying in the wind. The fox is jumping in the garden when he is happy. But that is not the whole story.' => '…The fox is jumping in the <strong>garden</strong> when he is happy. But that…',
            'The bird is flying in the wind. The fox is jumping in the garden when he is happy. But that is not the whole story.'      => '…The fox is jumping in the <strong>garden</strong> when he is happy. But that…',
            'The fox is jumping in the garden when he is happy. But that is not the whole story.'                                      => '…is jumping in the <strong>garden</strong> when he is happy…',
            'Yes. The fox is jumping in the garden when he is happy. But that is not the whole story.'                                 => '…fox is jumping in the <strong>garden</strong> when he is happy…',
            'Yes. The fox is jumping in the garden when he is happy. But that is not the whole story of the garden story.'             => '…The fox is jumping in the <strong>garden</strong> when he is happy. But…',
        ];

        $searchString = 'garden';
        foreach ($testArray as $testString => $testExpected) {
            $result = UTF8::extract_text($testString, $searchString);
            $result = UTF8::replace($result, $searchString, '<strong>' . $searchString . '</strong>');

            static::assertSame($testExpected, $result, 'tested: ' . $testString);
        }

        // ----------------

        $testArray = [
            'Yes. The bird is flying in the wind. The fox is jumping in the garden when he is happy. But that is not the whole story.' => '…flying in the wind. <strong>The fox is jumping in the garden</strong> when he…',
            'The bird is flying in the wind. The fox is jumping in the garden when he is happy. But that is not the whole story.'      => '…in the wind. <strong>The fox is jumping in the garden</strong> when he is…',
            'The fox is jumping in the garden when he is happy. But that is not the whole story.'                                      => '<strong>The fox is jumping in the garden</strong> when he is…',
            'Yes. The fox is jumping in the garden when he is happy. But that is not the whole story.'                                 => 'Yes. <strong>The fox is jumping in the garden</strong> when he…',
            'Yes. The fox is jumping in the garden when he is happy. But that is not the whole story of the garden story.'             => 'Yes. <strong>The fox is jumping in the garden</strong> when he is happy…',
        ];

        $searchString = 'The fox is jumping in the garden';
        foreach ($testArray as $testString => $testExpected) {
            $result = UTF8::extract_text($testString, $searchString);
            $result = UTF8::replace($result, $searchString, '<strong>' . $searchString . '</strong>');

            static::assertSame($testExpected, $result, 'tested: ' . $testString);
        }
    }

    /**
     * @dataProvider firstProvider()
     *
     * @param $expected
     * @param $str
     * @param $n
     * @param $encoding
     */
    public function testFirst($expected, $str, $n, $encoding = '')
    {
        $result = UTF8::first_char($str, $n, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider hasLowerCaseProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testHasLowerCase($expected, $str)
    {
        $result = UTF8::has_lowercase($str);

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider hasWhitespaceProvider()
     *
     * @param $str1
     * @param $str2
     */
    public function testHasWhitespace($str1, $str2)
    {
        $result = UTF8::has_whitespace($str1);

        static::assertTrue(\is_bool($result));
        static::assertTrue($result, 'tested: ' . $str1);

        // ---

        $result = UTF8::has_whitespace($str2);

        static::assertTrue(\is_bool($result));
        static::assertTrue($result, 'tested: ' . $str2);

        // ---

        $result = UTF8::has_whitespace('');

        static::assertTrue(\is_bool($result));
        static::assertFalse($result);

        // ---

        $result = UTF8::has_whitespace('abc-öäü');

        static::assertTrue(\is_bool($result));
        static::assertFalse($result);
    }

    /**
     * @dataProvider hasUpperCaseProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testHasUpperCase($expected, $str)
    {
        $result = UTF8::has_uppercase($str);

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider htmlDecodeProvider()
     *
     * @param     $expected
     * @param     $str
     * @param int $flags
     * @param     $encoding
     */
    public function testHtmlDecode($expected, $str, $flags = \ENT_COMPAT, $encoding = '')
    {
        $result = UTF8::html_entity_decode($str, $flags, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider htmlEncodeProvider()
     *
     * @param     $expected
     * @param     $str
     * @param int $flags
     * @param     $encoding
     */
    public function testHtmlEncode($expected, $str, $flags = \ENT_COMPAT, $encoding = '')
    {
        $result = UTF8::htmlentities($str, $flags, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider humanizeProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testHumanize($expected, $str)
    {
        $result = UTF8::str_humanize($str);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider indexOfProvider()
     *
     * @param     $expected
     * @param     $str
     * @param     $subStr
     * @param int $offset
     * @param     $encoding
     */
    public function testIndexOf($expected, $str, $subStr, $offset = 0, $encoding = '')
    {
        $result = UTF8::strpos($str, $subStr, $offset, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider indexOfLastProvider()
     *
     * @param     $expected
     * @param     $str
     * @param     $subStr
     * @param int $offset
     * @param     $encoding
     */
    public function testIndexOfLast($expected, $str, $subStr, $offset = 0, $encoding = '')
    {
        $result = UTF8::strrpos($str, $subStr, $offset, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider indexOfLastProvider()
     *
     * @param     $expected
     * @param     $str
     * @param     $subStr
     * @param int $offset
     * @param     $encoding
     */
    public function testIindexOfLast($expected, $str, $subStr, $offset = 0, $encoding = '')
    {
        $result = UTF8::strripos($str, $subStr, $offset, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider insertProvider()
     *
     * @param $expected
     * @param $str
     * @param $substring
     * @param $index
     * @param $encoding
     */
    public function testInsert($expected, $str, $substring, $index, $encoding = '')
    {
        $result = UTF8::str_insert($str, $substring, $index, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider isAlphaProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testIsAlpha($expected, $str)
    {
        $result = UTF8::is_alpha($str);

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider isAlphanumericProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testIsAlphanumeric($expected, $str)
    {
        $result = UTF8::is_alphanumeric($str);

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider isPunctuationProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testIsPunctuation($expected, $str)
    {
        $result = UTF8::is_punctuation($str);

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider isBase64Provider()
     *
     * @param $expected
     * @param $str
     */
    public function testIsBase64($expected, $str)
    {
        $result = UTF8::is_base64($str);

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider isBase64EmptyStringIsAlsoValidProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testIsBase64EmptyStringIsAlsoValid($expected, $str)
    {
        $result = UTF8::is_base64($str, true);

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider isBlankProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testIsBlank($expected, $str)
    {
        $result = UTF8::is_blank($str);

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider isHexadecimalProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testIsHexadecimal($expected, $str)
    {
        $result = UTF8::is_hexadecimal($str);

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
    }

    public function testIsHtml()
    {
        $testArray = [
            ''                         => false,
            '<h1>test</h1>'            => true,
            '<😃>test</😃>'              => true,
            'test'                     => false,
            '<b>lall</b>'              => true,
            'öäü<strong>lall</strong>' => true,
            ' <b>lall</b>'             => true,
            '<b><b>lall</b>'           => true,
            '</b>lall</b>'             => true,
            '[b]lall[b]'               => false,
            ' <test>κόσμε</test> '     => true,
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::is_html($testString), 'tested: ' . $testString);
        }
    }

    /**
     * @dataProvider isJsonDoNotIgnoreProvider()
     *
     * @param bool   $expected
     * @param string $str
     */
    public function testIsJsonDoNotIgnore($expected, $str)
    {
        $result = UTF8::is_json($str, false);

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result, 'tested: ' . $str);
    }

    /**
     * @dataProvider isJsonProvider()
     *
     * @param bool   $expected
     * @param string $str
     */
    public function testIsJson($expected, $str)
    {
        $result = UTF8::is_json($str);

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result, 'tested: ' . $str);
    }

    /**
     * @dataProvider isLowerCaseProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testIsLowerCase($expected, $str)
    {
        $result = UTF8::is_lowercase($str);

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider isSerializedProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testIsSerialized($expected, $str)
    {
        $result = UTF8::is_serialized($str);

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider isUpperCaseProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testIsUpperCase($expected, $str)
    {
        $result = UTF8::is_uppercase($str);

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider lastProvider()
     *
     * @param $expected
     * @param $str
     * @param $n
     * @param $encoding
     */
    public function testLast($expected, $str, $n, $encoding = '')
    {
        $result = UTF8::str_last_char($str, $n, $encoding);

        static::assertSame($expected, $result);
    }

    public function testLastSubstringOf()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>bar'          => 'bar',
            '<h1></h1> '               => '',
            '</b></b>'                 => 'b>',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => 'b>',
            '<b><b>lall</b>'           => 'b>',
            '</b>lall</b>'             => 'b>',
            '[b][/b]'                  => 'b]',
            '[B][/B]'                  => '',
            'κόσμbε ¡-öäü'             => 'bε ¡-öäü',
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::str_substr_last($testString, 'b', false));
        }
    }

    public function testLastSubstringOfIgnoreCase()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>bar'          => 'bar',
            'foo<h1></h1>Bar'          => 'Bar',
            '<h1></h1> '               => '',
            '</b></b>'                 => 'b>',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => 'b>',
            '<b><b>lall</b>'           => 'b>',
            '</b>lall</b>'             => 'b>',
            '[B][/B]'                  => 'B]',
            '[b][/b]'                  => 'b]',
            'κόσμbε ¡-öäü'             => 'bε ¡-öäü',
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::str_isubstr_last($testString, 'b', false));
        }
    }

    /**
     * @dataProvider lengthProvider()
     *
     * @param $expected
     * @param $str
     * @param $encoding
     */
    public function testLength($expected, $str, $encoding = '')
    {
        $result = UTF8::strlen($str, $encoding);

        static::assertTrue(\is_int($result));
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider linesProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testLines($expected, $str)
    {
        $result = UTF8::str_to_lines($str);

        static::assertTrue(\is_array($result));
        foreach ($result as $line) {
            self::assertUtf8String($line);
        }

        $counter = \count($expected);
        /** @noinspection ForeachInvariantsInspection */
        for ($i = 0; $i < $counter; ++$i) {
            static::assertSame($expected[$i], $result[$i]);
        }
    }

    public function testLinewrap()
    {
        $testArray = [
            ''                                                                                                      => "\n",
            ' '                                                                                                     => ' ' . "\n",
            'http:// moelleken.org'                                                                                 => 'http://' . "\n" . 'moelleken.org' . "\n",
            'http://test.de'                                                                                        => 'http://test.de' . "\n",
            'http://öäü.de'                                                                                         => 'http://öäü.de' . "\n",
            'http://menadwork.com'                                                                                  => 'http://menadwork.com' . "\n",
            'test.de'                                                                                               => 'test.de' . "\n",
            'test'                                                                                                  => 'test' . "\n",
            '0123456 789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789' => '0123456' . "\n" . '789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789' . "\n",
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::wordwrap_per_line($testString, 10), 'tested: "' . $testString . '"');
        }

        // ---

        $str = 'Iñtër' . "\n" . 'n' . "#\r\n#" . 'â' . "#\r#" . 't#i#ô#n#à#lizætiøndsdadadadadadadadasdadadasd';
        $wrapped = 'Iñtër
n#
#â#
#t#i#ô#n#à
#lizætiønd
sdadadadad
adadadasda
dadasd';
        static::assertSame($wrapped, UTF8::wordwrap_per_line($str, 10, "\n", true, false));

        // ---

        $str = 'Iñtër' . "\n" . 'n' . "#\r\n#" . 'â' . "#\r#" . 't#i#ô#n#à#lizætiøndsdadadadadadadadasdadadasd';
        $wrapped = 'Iñtër
n#
#â#
#t#i#ô#n#à#lizætiøndsdadadadadadadadasdadadasd
';
        static::assertSame($wrapped, UTF8::wordwrap_per_line($str, 10, "\n", false, true));

        // ---

        $str = 'Iñtër<br>n' . "#\n#" . 'â#<br>#t#i#ô#n#à#lizætiøndsdad<br>adadadadadadasdadadasd';
        $wrapped = 'Iñt
ër<br>n#
#â#<br>#t#
i#ô
#n#
à#l
izæ
tiø
nds
dad<br>ada
dad
ada
dad
asd
ada
das
d
';
        static::assertSame($wrapped, UTF8::wordwrap_per_line($str, 3, "\n", true, true, '<br>'));

        // ---

        $str = 'Iñtër<br>n' . "#\n#" . 'â#<br>#t#i#ô#n#à#lizætiøndsdad<br>adadadadadadasdadadasd';
        $wrapped = 'Iñtër<br>n#
#â#<br>#t#i#ô#n#à#lizætiøndsdad<br>adadadadadadasdadadasd';
        static::assertSame($wrapped, UTF8::wordwrap_per_line($str, 3, "\n", false, false, '<br>'));

        // ---

        $str = 'Iñtër<br>n' . "#\n#" . 'â#<br>#t#i#ô#n#à#lizætiøndsdad<br>adadad ada dadasda dadasd';
        $wrapped = 'Iñtër<br>n#
#â#<br>#t#i#ô#n#à#lizætiøndsdad<br>adadad{BREAK}ada{BREAK}dadasda{BREAK}dadasd';
        static::assertSame($wrapped, UTF8::wordwrap_per_line($str, 3, '{BREAK}', false, false, '<br>'));

        // ---

        $str = 'Iñtër<br>n' . "#\n#" . 'â#<br>#t#i#ô#n#à#lizætiøndsdad<br>adadad ada dadasda dadasd';
        $wrapped = 'Iñtër<br>n#
#â#<br>#t#i#ô#n#à#lizætiøndsdad<br>adadad ada{BREAK}dadasda{BREAK}dadasd';
        static::assertSame($wrapped, UTF8::wordwrap_per_line($str, 10, '{BREAK}', false, false, '<br>'));
    }

    /**
     * @dataProvider longestCommonPrefixProvider()
     *
     * @param $expected
     * @param $str
     * @param $otherStr
     * @param $encoding
     */
    public function testLongestCommonPrefix($expected, $str, $otherStr, $encoding = '')
    {
        $result = UTF8::str_longest_common_prefix($str, $otherStr, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider longestCommonSubstringProvider()
     *
     * @param $expected
     * @param $str
     * @param $otherStr
     * @param $encoding
     */
    public function testLongestCommonSubstring($expected, $str, $otherStr, $encoding = '')
    {
        $result = UTF8::str_longest_common_substring($str, $otherStr, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider longestCommonSuffixProvider()
     *
     * @param $expected
     * @param $str
     * @param $otherStr
     * @param $encoding
     */
    public function testLongestCommonSuffix($expected, $str, $otherStr, $encoding = '')
    {
        $result = UTF8::str_longest_common_suffix($str, $otherStr, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider lowerCaseFirstProvider()
     *
     * @param $expected
     * @param $str
     * @param $encoding
     */
    public function testLowerCaseFirst($expected, $str, $encoding = '')
    {
        $result = UTF8::lcfirst($str, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider offsetExistsProvider()
     *
     * @param $expected
     * @param $offset
     */
    public function testOffsetExists($expected, $offset)
    {
        static::assertSame($expected, UTF8::str_offset_exists('fòô', $offset));
    }

    public function testOffsetGet()
    {
        static::assertSame('f', UTF8::str_offset_get('fòô', 0));
        static::assertSame('ô', UTF8::str_offset_get('fòô', 2));
    }

    public function testOffsetGetOutOfBoundsException()
    {
        $this->expectException(\OutOfBoundsException::class);

        /** @noinspection UnusedFunctionResultInspection */
        UTF8::str_offset_get('fòô', -999);
    }

    /**
     * @dataProvider padProvider()
     *
     * @param        $expected
     * @param        $str
     * @param        $length
     * @param string $padStr
     * @param string $padType
     * @param        $encoding
     */
    public function testPad($expected, $str, $length, $padStr = ' ', $padType = 'right', $encoding = '')
    {
        $result = UTF8::str_pad($str, $length, $padStr, $padType, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider padBothProvider()
     *
     * @param        $expected
     * @param        $str
     * @param        $length
     * @param string $padStr
     * @param        $encoding
     */
    public function testPadBoth($expected, $str, $length, $padStr = ' ', $encoding = '')
    {
        $result = UTF8::str_pad_both($str, $length, $padStr, $encoding);

        static::assertSame($expected, $result);
    }

    public function testPadException()
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @noinspection UnusedFunctionResultInspection */
        UTF8::str_pad('foo', 5, 'foo', 'bar');
    }

    /**
     * @dataProvider padLeftProvider()
     *
     * @param        $expected
     * @param        $str
     * @param        $length
     * @param string $padStr
     * @param        $encoding
     */
    public function testPadLeft($expected, $str, $length, $padStr = ' ', $encoding = '')
    {
        $result = UTF8::str_pad_left($str, $length, $padStr, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider padRightProvider()
     *
     * @param        $expected
     * @param        $str
     * @param        $length
     * @param string $padStr
     * @param        $encoding
     */
    public function testPadRight($expected, $str, $length, $padStr = ' ', $encoding = '')
    {
        $result = UTF8::str_pad_right($str, $length, $padStr, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider removeHtmlProvider()
     *
     * @param        $expected
     * @param        $str
     * @param string $allowableTags
     */
    public function testRemoveHtml($expected, $str, $allowableTags = '')
    {
        $result = UTF8::remove_html($str, $allowableTags);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider removeHtmlBreakProvider()
     *
     * @param        $expected
     * @param        $str
     * @param string $replacement
     */
    public function testRemoveHtmlBreak($expected, $str, $replacement = '')
    {
        $result = UTF8::remove_html_breaks($str, $replacement);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider removeLeftProvider()
     *
     * @param $expected
     * @param $str
     * @param $substring
     * @param $encoding
     */
    public function testRemoveLeft($expected, $str, $substring, $encoding = '')
    {
        $result = UTF8::remove_left($str, $substring, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider removeRightProvider()
     *
     * @param $expected
     * @param $str
     * @param $substring
     * @param $encoding
     */
    public function testRemoveRight($expected, $str, $substring, $encoding = '')
    {
        $result = UTF8::remove_right($str, $substring, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider removeRightProvider()
     *
     * @param $expected
     * @param $str
     * @param $substring
     * @param $encoding
     */
    public function testRemoveiRight($expected, $str, $substring, $encoding = '')
    {
        $result = UTF8::remove_iright($str, strtoupper($substring), $encoding);
        static::assertSame($expected, $result);

        $result = UTF8::remove_iright($str, strtolower($substring), $encoding);
        static::assertSame($expected, $result);

        $result = UTF8::remove_iright(strtoupper($str), $substring, $encoding);
        static::assertSame(strtoupper($expected), $result);

        $result = UTF8::remove_iright(strtolower($str), $substring, $encoding);
        static::assertSame(strtolower($expected), $result);
    }

    /**
     * @dataProvider removeLeftProvider()
     *
     * @param $expected
     * @param $str
     * @param $substring
     * @param $encoding
     */
    public function testRemoveiLeft($expected, $str, $substring, $encoding = '')
    {
        $result = UTF8::remove_ileft($str, strtoupper($substring), $encoding);
        static::assertSame($expected, $result);

        $result = UTF8::remove_ileft($str, strtolower($substring), $encoding);
        static::assertSame($expected, $result);

        $result = UTF8::remove_ileft(strtoupper($str), $substring, $encoding);
        static::assertSame(strtoupper($expected), $result);

        $result = UTF8::remove_ileft(strtolower($str), $substring, $encoding);
        static::assertSame(strtolower($expected), $result);
    }

    /**
     * @dataProvider repeatProvider()
     *
     * @param $expected
     * @param $str
     * @param $multiplier
     */
    public function testRepeat($expected, $str, $multiplier)
    {
        $result = UTF8::str_repeat($str, $multiplier);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider replaceProvider()
     *
     * @param string $expected
     * @param string $str
     * @param string $search
     * @param string $replacement
     * @param        $encoding
     * @param bool   $caseSensitive
     */
    public function testReplace($expected, $str, $search, $replacement, $encoding = null, $caseSensitive = true)
    {
        $result = UTF8::replace($str, $search, $replacement, $caseSensitive);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider replaceAllProvider()
     *
     * @param string $expected
     * @param string $str
     * @param array  $search
     * @param string $replacement
     * @param        $encoding
     * @param bool   $caseSensitive
     */
    public function testReplaceAll($expected, $str, $search, $replacement, $encoding = null, $caseSensitive = true)
    {
        $result = UTF8::replace_all($str, $search, $replacement, $caseSensitive);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider replaceBeginningProvider()
     *
     * @param $expected
     * @param $str
     * @param $search
     * @param $replacement
     */
    public function testReplaceBeginning($expected, $str, $search, $replacement)
    {
        $result = UTF8::str_replace_beginning($str, $search, $replacement);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider ireplaceBeginningProvider()
     *
     * @param $expected
     * @param $str
     * @param $search
     * @param $replacement
     */
    public function testTestiReplaceBeginning($expected, $str, $search, $replacement)
    {
        $result = UTF8::str_ireplace_beginning($str, $search, $replacement);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider replaceEndingProvider()
     *
     * @param $expected
     * @param $str
     * @param $search
     * @param $replacement
     */
    public function testReplaceEnding($expected, $str, $search, $replacement)
    {
        $result = UTF8::str_replace_ending($str, $search, $replacement);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider ireplaceEndingProvider()
     *
     * @param $expected
     * @param $str
     * @param $search
     * @param $replacement
     */
    public function testTestiReplaceEnding($expected, $str, $search, $replacement)
    {
        $result = UTF8::str_ireplace_ending($str, $search, $replacement);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider reverseProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testReverse($expected, $str)
    {
        $result = UTF8::strrev($str);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider safeTruncateProvider()
     *
     * @param        $expected
     * @param        $str
     * @param        $length
     * @param string $substring
     * @param        $encoding
     */
    public function testSafeTruncate($expected, $str, $length, $substring = '', $encoding = '')
    {
        $result = UTF8::str_truncate_safe($str, $length, $substring, $encoding);

        static::assertSame($expected, $result, 'tested: ' . $str . ' | ' . $substring . ' (' . $length . ')');
    }

    /**
     * @dataProvider safeTruncateIgnoreWordsProvider()
     *
     * @param        $expected
     * @param        $str
     * @param        $length
     * @param string $substring
     * @param        $encoding
     */
    public function testSafeTruncateIgnoreWords($expected, $str, $length, $substring = '', $encoding = '')
    {
        $result = UTF8::str_truncate_safe($str, $length, $substring, $encoding, true);

        static::assertSame($expected, $result, 'tested: ' . $str . ' | ' . $substring . ' (' . $length . ')');
    }

    /**
     * @dataProvider shortenAfterWordProvider()
     *
     * @param        $expected
     * @param        $str
     * @param int    $length
     * @param string $strAddOn
     * @param        $encoding
     */
    public function testShortenAfterWord($expected, $str, $length, $strAddOn = '...', $encoding = '')
    {
        $result = UTF8::str_limit_after_word($str, $length, $strAddOn, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider shuffleProvider()
     *
     * @param $str
     * @param $encoding
     */
    public function testShuffle($str, $encoding = '')
    {
        $encoding = $encoding ?: \mb_internal_encoding();
        $result = UTF8::str_shuffle($str);

        static::assertSame(
            UTF8::strlen($str, $encoding),
            UTF8::strlen($result, $encoding)
        );

        // We'll make sure that the chars are present after shuffle
        $length = UTF8::strlen($str, $encoding);
        for ($i = 0; $i < $length; ++$i) {
            $char = UTF8::substr($str, $i, 1, $encoding);
            $countBefore = UTF8::substr_count($str, $char, 0, null, $encoding);
            $countAfter = UTF8::substr_count($result, $char, 0, null, $encoding);
            static::assertSame($countBefore, $countAfter);
        }
    }

    /**
     * @dataProvider sliceProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $start
     * @param null $end
     * @param      $encoding
     */
    public function testSlice($expected, $str, $start, $end = null, $encoding = '')
    {
        $result = UTF8::str_slice($str, $start, $end, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider snakeizeProvider()
     *
     * @param $expected
     * @param $str
     * @param $encoding
     */
    public function testSnakeize($expected, $str, $encoding = '')
    {
        $result = UTF8::str_snakeize($str, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider splitProvider()
     *
     * @param     $expected
     * @param     $str
     * @param     $pattern
     * @param int $limit
     */
    public function testSplit($expected, $str, $pattern, $limit = -1)
    {
        $result = UTF8::str_split_pattern($str, $pattern, $limit);

        static::assertTrue(\is_array($result));
        foreach ($result as $string) {
            self::assertUtf8String($string);
        }

        $counter = \count($expected);
        /** @noinspection ForeachInvariantsInspection */
        for ($i = 0; $i < $counter; ++$i) {
            static::assertSame($expected[$i], $result[$i]);
        }
    }

    /**
     * @dataProvider startsWithProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $substring
     * @param bool $caseSensitive
     */
    public function testStartsWith($expected, $str, $substring, $caseSensitive = true)
    {
        if ($caseSensitive) {
            $result = UTF8::str_starts_with($str, $substring);
        } else {
            $result = UTF8::str_istarts_with($str, $substring);
        }

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider startsWithProviderAny()
     *
     * @param      $expected
     * @param      $str
     * @param      $substring
     * @param bool $caseSensitive
     */
    public function testStartsWithAny($expected, $str, $substring, $caseSensitive = true)
    {
        if ($caseSensitive) {
            $result = UTF8::str_starts_with_any($str, $substring);
        } else {
            $result = UTF8::str_istarts_with_any($str, $substring);
        }

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider stripWhitespaceProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testStripWhitespace($expected, $str)
    {
        $result = UTF8::strip_whitespace($str);

        static::assertSame($expected, $result);
    }

    public function testStripeEmptyTags()
    {
        $testArray = [
            ''                            => '',
            '<h1>test</h1>'               => '<h1>test</h1>',
            '<😃>test</😃>'                 => '<😃>test</😃>',
            '<😃></😃>'                     => '',
            '<😃>' . "\t" . '</😃>'         => '',
            '<😃>😃</😃>' . "\n" . '<😃></😃>' => '<😃>😃</😃>' . "\n",
            'foo<h1></h1>bar'             => 'foobar',
            '<h1></h1> '                  => ' ',
            '</b></b>'                    => '</b></b>',
            'öäü<strong>lall</strong>'    => 'öäü<strong>lall</strong>',
            ' b<b></b>'                   => ' b',
            ' bc<b> </b>'                 => ' bc',
            ' bd<b>' . "\t" . '</b>'      => ' bd',
            '<b><b>lall</b>'              => '<b><b>lall</b>',
            '</b>lall</b>'                => '</b>lall</b>',
            '[b][/b]'                     => '[b][/b]',
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::html_stripe_empty_tags($testString));
        }
    }

    public function testStripeMediaQueries()
    {
        $testArray = [
            'test lall '                                                                         => 'test lall ',
            ''                                                                                   => '',
            ' '                                                                                  => ' ',
            'test @media (min-width:660px){ .des-cla #mv-tiles{width:480px} } test '             => 'test  test ',
            'test @media only screen and (max-width: 950px) { .des-cla #mv-tiles{width:480px} }' => 'test ',
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::css_stripe_media_queries($testString));
        }
    }

    public function testCssIdentifier()
    {
        $testArray = [
            'test lall '                    => 'test-lall',
            'chr(int $code_point) : string' => 'chrint-code_point--string',
            '123foo/bar!!!'                 => '_23foo-bar',
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::css_identifier($testString));
        }

        static::assertStringContainsString('auto-generated-css-', UTF8::css_identifier(UTF8::bom()));
        static::assertStringContainsString('foo', UTF8::css_identifier('<p>foo</p>', [], true));
        static::assertStringContainsString('auto-generated-css-', UTF8::css_identifier('<p></p>', [], true));

        if (\method_exists(__CLASS__, 'assertStringContainsString')) {
            static::assertStringContainsString('auto-generated-css-', UTF8::css_identifier());
            static::assertStringContainsString('auto-generated-css-', UTF8::css_identifier(' '));
        } else {
            static::assertContains('auto-generated-css-', UTF8::css_identifier());
            static::assertContains('auto-generated-css-', UTF8::css_identifier(' '));
        }
    }

    public function testSubStringOf()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>bar'          => 'bar',
            '<h1></h1> '               => '',
            '</b></b>'                 => 'b></b>',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => 'b<b></b>',
            '<b><b>lall</b>'           => 'b><b>lall</b>',
            '</b>lall</b>'             => 'b>lall</b>',
            '[B][/B]'                  => '',
            '[b][/b]'                  => 'b][/b]',
            'κόσμbε ¡-öäü'             => 'bε ¡-öäü',
            'bκόσμbε'                  => 'bκόσμbε',
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::str_substr_first($testString, 'b', false));
        }
    }

    /**
     * @dataProvider substrProvider()
     *
     * @param      $expected
     * @param      $str
     * @param      $start
     * @param null $length
     * @param      $encoding
     */
    public function testSubstr($expected, $str, $start, $length = null, $encoding = '')
    {
        $result = UTF8::substr($str, $start, $length, $encoding);

        static::assertSame($expected, $result);
    }

    public function testSubstringOfIgnoreCase()
    {
        $testArray = [
            ''                         => '',
            '<h1>test</h1>'            => '',
            'foo<h1></h1>Bar'          => 'Bar',
            'foo<h1></h1>bar'          => 'bar',
            '<h1></h1> '               => '',
            '</b></b>'                 => 'b></b>',
            'öäü<strong>lall</strong>' => '',
            ' b<b></b>'                => 'b<b></b>',
            '<b><b>lall</b>'           => 'b><b>lall</b>',
            '</b>lall</b>'             => 'b>lall</b>',
            '[B][/B]'                  => 'B][/B]',
            'κόσμbε ¡-öäü'             => 'bε ¡-öäü',
            'bκόσμbε'                  => 'bκόσμbε',
        ];

        foreach ($testArray as $testString => $testResult) {
            static::assertSame($testResult, UTF8::str_isubstr_first($testString, 'b', false));
        }
    }

    /**
     * @dataProvider surroundProvider()
     *
     * @param $expected
     * @param $str
     * @param $substring
     */
    public function testSurround($expected, $str, $substring)
    {
        $result = UTF8::str_surround($str, $substring);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider swapCaseProvider()
     *
     * @param $expected
     * @param $str
     * @param $encoding
     */
    public function testSwapCase($expected, $str, $encoding = '')
    {
        $result = UTF8::swapCase($str, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider tidyProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testTidy($expected, $str)
    {
        $result = UTF8::normalize_msword($str);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider titleizeProvider()
     *
     * @param string      $expected
     * @param string      $str
     * @param array|null  $ignore
     * @param string|null $word_define_chars
     * @param string      $encoding
     */
    public function testTitleize($expected, $str, $ignore = null, $word_define_chars = null, $encoding = '')
    {
        $result = UTF8::str_titleize(
            $str,
            $ignore,
            $encoding,
            false,
            null,
            false,
            true,
            $word_define_chars
        );

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider titleizeForHumansProvider()
     *
     * @param string      $expected
     * @param string      $str
     * @param array       $ignore
     * @param string|null $encoding
     */
    public function testTitleizeForHumans($str, $expected, $ignore = [], $encoding = '')
    {
        $result = UTF8::str_titleize_for_humans($str, $ignore, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @return \Iterator
     */
    public function titleizeForHumansProvider(): \Iterator
    {
        yield ['TITLE CASE', 'Title Case'];
        yield ['testing the method', 'Testing the Method'];
        yield ['i like to watch DVDs at home', 'I Like to watch DVDs at Home', ['watch']];
        yield ['  Θα ήθελα να φύγει  ', 'Θα Ήθελα Να Φύγει', [], 'UTF-8'];
        yield [
            'For step-by-step directions email someone@gmail.com',
            'For Step-by-Step Directions Email someone@gmail.com',
        ];
        yield [
            "2lmc Spool: 'Gruber on OmniFocus and Vapo(u)rware'",
            "2lmc Spool: 'Gruber on OmniFocus and Vapo(u)rware'",
        ];
        yield ['Have you read “The Lottery”?', 'Have You Read “The Lottery”?'];
        yield ['your hair[cut] looks (nice)', 'Your Hair[cut] Looks (Nice)'];
        yield [
            "People probably won't put http://foo.com/bar/ in titles",
            "People Probably Won't Put http://foo.com/bar/ in Titles",
        ];
        yield [
            'Scott Moritz and TheStreet.com’s million iPhone la‑la land',
            'Scott Moritz and TheStreet.com’s Million iPhone La‑La Land',
        ];
        yield ['BlackBerry vs. iPhone', 'BlackBerry vs. iPhone'];
        yield [
            'Notes and observations regarding Apple’s announcements from ‘The Beat Goes On’ special event',
            'Notes and Observations Regarding Apple’s Announcements From ‘The Beat Goes On’ Special Event',
        ];
        yield [
            'Read markdown_rules.txt to find out how _underscores around words_ will be interpretted',
            'Read markdown_rules.txt to Find Out How _Underscores Around Words_ Will Be Interpretted',
        ];
        yield [
            "Q&A with Steve Jobs: 'That's what happens in technology'",
            "Q&A With Steve Jobs: 'That's What Happens in Technology'",
        ];
        yield ["What is AT&T's problem?", "What Is AT&T's Problem?"];
        yield ['Apple deal with AT&T falls through', 'Apple Deal With AT&T Falls Through'];
        yield ['this v that', 'This v That'];
        yield ['this vs that', 'This vs That'];
        yield ['this v. that', 'This v. That'];
        yield ['this vs. that', 'This vs. That'];
        yield ["The SEC's Apple probe: what you need to know", "The SEC's Apple Probe: What You Need to Know"];
        yield [
            "'by the way, small word at the start but within quotes.'",
            "'By the Way, Small Word at the Start but Within Quotes.'",
        ];
        yield ['Small word at end is nothing to be afraid of', 'Small Word at End Is Nothing to Be Afraid Of'];
        yield [
            'Starting sub-phrase with a small word: a trick, perhaps?',
            'Starting Sub-Phrase With a Small Word: A Trick, Perhaps?',
        ];
        yield [
            "Sub-phrase with a small word in quotes: 'a trick, perhaps?'",
            "Sub-Phrase With a Small Word in Quotes: 'A Trick, Perhaps?'",
        ];
        yield [
            'Sub-phrase with a small word in quotes: "a trick, perhaps?"',
            'Sub-Phrase With a Small Word in Quotes: "A Trick, Perhaps?"',
        ];
        yield ['"Nothing to Be Afraid of?"', '"Nothing to Be Afraid Of?"'];
        yield ['a thing', 'A Thing'];
        yield [
            'Dr. Strangelove (or: how I Learned to Stop Worrying and Love the Bomb)',
            'Dr. Strangelove (Or: How I Learned to Stop Worrying and Love the Bomb)',
        ];
        yield ['  this is trimming', 'This Is Trimming'];
        yield ['this is trimming  ', 'This Is Trimming'];
        yield ['  this is trimming  ', 'This Is Trimming'];
        yield ['IF IT’S ALL CAPS, FIX IT', 'If It’s All Caps, Fix It'];
        yield ['What could/should be done about slashes?', 'What Could/Should Be Done About Slashes?'];
        yield [
            'Never touch paths like /var/run before/after /boot',
            'Never Touch Paths Like /var/run Before/After /boot',
        ];
    }

    /**
     * @dataProvider toAsciiProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testToAscii($expected, $str)
    {
        $result = UTF8::to_ascii($str);

        static::assertSame($expected, $result, 'tested:' . $str);
    }

    /**
     * @dataProvider toBooleanProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testToBoolean($expected, $str)
    {
        $result = UTF8::to_boolean($str);

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result, 'tested: ' . $str);
    }

    /**
     * @dataProvider toLowerCaseProvider()
     *
     * @param $expected
     * @param $str
     * @param $encoding
     */
    public function testToLowerCase($expected, $str, $encoding = '')
    {
        $result = UTF8::strtolower($str, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider toSpacesProvider()
     *
     * @param     $expected
     * @param     $str
     * @param int $tabLength
     */
    public function testToSpaces($expected, $str, $tabLength = 4)
    {
        $result = UTF8::tabs_to_spaces($str, $tabLength);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider toTabsProvider()
     *
     * @param     $expected
     * @param     $str
     * @param int $tabLength
     */
    public function testToTabs($expected, $str, $tabLength = 4)
    {
        $result = UTF8::spaces_to_tabs($str, $tabLength);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider toTitleCaseProvider()
     *
     * @param $expected
     * @param $str
     * @param $encoding
     */
    public function testToTitleCase($expected, $str, $encoding = '')
    {
        $result = UTF8::titlecase($str, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider toUpperCaseProvider()
     *
     * @param $expected
     * @param $str
     * @param $encoding
     */
    public function testToUpperCase($expected, $str, $encoding = '')
    {
        $result = UTF8::strtoupper($str, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider trimProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $chars
     */
    public function testTrim($expected, $str, $chars = null)
    {
        $result = UTF8::trim($str, $chars);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider trimLeftProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $chars
     */
    public function testTrimLeft($expected, $str, $chars = null)
    {
        $result = UTF8::ltrim($str, $chars);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider trimRightProvider()
     *
     * @param      $expected
     * @param      $str
     * @param null $chars
     */
    public function testTrimRight($expected, $str, $chars = null)
    {
        $result = UTF8::rtrim($str, $chars);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider truncateProvider()
     *
     * @param        $expected
     * @param        $str
     * @param        $length
     * @param string $substring
     * @param        $encoding
     */
    public function testTruncate($expected, $str, $length, $substring = '', $encoding = '')
    {
        $result = UTF8::str_truncate($str, $length, $substring, $encoding);

        static::assertSame($expected, $result);
    }

    public function testTruncateSafeUnicode()
    {
        $s = "\u{158}ekn\u{11B}te, jak se (dnes) m\u{E1}te?"; // Řekněte, jak se (dnes) máte?

        static::assertSame('…', UTF8::str_truncate_safe($s, -1, '…', 'UTF-8', true)); // length=-1
        static::assertSame('…', UTF8::str_truncate_safe($s, 0, '…', 'UTF-8', true)); // length=0
        static::assertSame('…', UTF8::str_truncate_safe($s, 1, '…', 'UTF-8', true)); // length=1
        static::assertSame('Ř…', UTF8::str_truncate_safe($s, 2, '…', 'UTF-8', true)); // length=2
        static::assertSame('Ře…', UTF8::str_truncate_safe($s, 3, '…', 'UTF-8', true)); // length=3
        static::assertSame('Řek…', UTF8::str_truncate_safe($s, 4, '…', 'UTF-8', true)); // length=4
        static::assertSame('Řekn…', UTF8::str_truncate_safe($s, 5, '…', 'UTF-8', true)); // length=5
        static::assertSame('Řekně…', UTF8::str_truncate_safe($s, 6, '…', 'UTF-8', true)); // length=6
        static::assertSame('Řeknět…', UTF8::str_truncate_safe($s, 7, '…', 'UTF-8', true)); // length=7
        static::assertSame('Řekněte…', UTF8::str_truncate_safe($s, 8, '…', 'UTF-8', true)); // length=8
        static::assertSame('Řekněte,…', UTF8::str_truncate_safe($s, 9, '…', 'UTF-8', true)); // length=9
        static::assertSame('Řekněte,…', UTF8::str_truncate_safe($s, 10, '…', 'UTF-8', true)); // length=10
        static::assertSame('Řekněte,…', UTF8::str_truncate_safe($s, 11, '…', 'UTF-8', true)); // length=11
        static::assertSame('Řekněte,…', UTF8::str_truncate_safe($s, 12, '…', 'UTF-8', true)); // length=12
        static::assertSame('Řekněte, jak…', UTF8::str_truncate_safe($s, 13, '…', 'UTF-8', true)); // length=13
        static::assertSame('Řekněte, jak…', UTF8::str_truncate_safe($s, 14, '…', 'UTF-8', true)); // length=14
        static::assertSame('Řekněte, jak…', UTF8::str_truncate_safe($s, 15, '…', 'UTF-8', true)); // length=15
        static::assertSame('Řekněte, jak se…', UTF8::str_truncate_safe($s, 16, '…', 'UTF-8', true)); // length=16
        static::assertSame('Řekněte, jak se…', UTF8::str_truncate_safe($s, 17, '…', 'UTF-8', true)); // length=17
        static::assertSame('Řekněte, jak se…', UTF8::str_truncate_safe($s, 18, '…', 'UTF-8', true)); // length=18
        static::assertSame('Řekněte, jak se…', UTF8::str_truncate_safe($s, 19, '…', 'UTF-8', true)); // length=19
        static::assertSame('Řekněte, jak se…', UTF8::str_truncate_safe($s, 20, '…', 'UTF-8', true)); // length=20
        static::assertSame('Řekněte, jak se…', UTF8::str_truncate_safe($s, 21, '…', 'UTF-8', true)); // length=21
        static::assertSame('Řekněte, jak se…', UTF8::str_truncate_safe($s, 22, '…', 'UTF-8', true)); // length=22
        static::assertSame('Řekněte, jak se (dnes)…', UTF8::str_truncate_safe($s, 23, '…', 'UTF-8', true)); // length=23
        static::assertSame('Řekněte, jak se (dnes)…', UTF8::str_truncate_safe($s, 24, '…', 'UTF-8', true)); // length=24
        static::assertSame('Řekněte, jak se (dnes)…', UTF8::str_truncate_safe($s, 25, '…', 'UTF-8', true)); // length=25
        static::assertSame('Řekněte, jak se (dnes)…', UTF8::str_truncate_safe($s, 26, '…', 'UTF-8', true)); // length=26
        static::assertSame('Řekněte, jak se (dnes)…', UTF8::str_truncate_safe($s, 27, '…', 'UTF-8', true)); // length=27
        static::assertSame('Řekněte, jak se (dnes) máte?', UTF8::str_truncate_safe($s, 28, '…', 'UTF-8', true)); // length=28
        static::assertSame('Řekněte, jak se (dnes) máte?', UTF8::str_truncate_safe($s, 29, '…', 'UTF-8', true)); // length=29
        static::assertSame('Řekněte, jak se (dnes) máte?', UTF8::str_truncate_safe($s, 30, '…', 'UTF-8', true)); // length=30
        static::assertSame('Řekněte, jak se (dnes) máte?', UTF8::str_truncate_safe($s, 31, '…', 'UTF-8', true)); // length=31
        static::assertSame('Řekněte, jak se (dnes) máte?', UTF8::str_truncate_safe($s, 32, '…', 'UTF-8', true)); // length=32

        // mañana, U+006E + U+0303 (combining character)
        static::assertSame("man\u{303}", UTF8::str_truncate_safe("man\u{303}ana", 4, '', 'UTF-8', true));
        static::assertSame('man', UTF8::str_truncate_safe("man\u{303}ana", 3, '', 'UTF-8', true));

        if (UTF8::mbstring_loaded()) { // only with "mbstring"
            if (Bootup::is_php('8.3')) { // https://github.com/php/php-src/issues/14703
                static::assertSame("κόσμε?", UTF8::str_truncate_safe("κόσμε\xa0\xa1", 6));
            } else {
                static::assertSame("κόσμε\xa0", UTF8::str_truncate_safe("κόσμε\xa0\xa1", 6));
            }
        }
    }

    /**
     * @dataProvider underscoredProvider()
     *
     * @param $expected
     * @param $str
     */
    public function testUnderscored($expected, $str)
    {
        $result = UTF8::str_underscored($str);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider upperCamelizeProvider()
     *
     * @param $expected
     * @param $str
     * @param $encoding
     */
    public function testUpperCamelize($expected, $str, $encoding = '')
    {
        $result = UTF8::str_upper_camelize($str, $encoding);

        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider upperCaseFirstProvider()
     *
     * @param $expected
     * @param $str
     * @param $encoding
     */
    public function testUpperCaseFirst($expected, $str, $encoding = '')
    {
        /** @noinspection ArgumentEqualsDefaultValueInspection */
        $result = UTF8::ucfirst($str, $encoding, false, null, false);

        static::assertSame($expected, $result);
    }

    public function testUtf8ify()
    {
        $examples = [
            '' => [''],
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
        <p>
          &nbsp;�&foo;❤&nbsp;
        </p>
        ' => [
                '' => '
        <h1>«Düsseldorf» &ndash; &lt;Köln&gt;</h1>
        <br /><br />
        <p>
          &nbsp;&foo;❤&nbsp;
        </p>
        ',
            ],
        ];

        foreach ($examples as $testString => $testResults) {
            foreach ($testResults as $before => $after) {
                static::assertSame($after, UTF8::cleanup($testString), 'tested: ' . $before . ' (' . \print_r($testResults, true) . ')');
            }
        }

        $examples = [
            // Valid UTF-8
            'κόσμε'    => ['κόσμε' => 'κόσμε'],
            '中'        => ['中' => '中'],
            '«foobar»' => ['«foobar»' => '«foobar»'],
            // Valid UTF-8 + Invalied Chars
            "κόσμε\xa0\xa1-öäü" => ['κόσμε-öäü' => 'κόσμε-öäü'],
            // Valid ASCII
            'a' => ['a' => 'a'],
            // Valid emoji (non-UTF-8)
            '😃' => ['😃' => '😃'],
            // Valid ASCII + Invalied Chars
            "a\xa0\xa1-öäü" => ['a-öäü' => 'a-öäü'],
            // Valid 2 Octet Sequence
            "\xc3\xb1" => ['ñ' => 'ñ'],
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
            "\xf0\x28\x8c\x28" => ['�(�(' => '(('],
            // Valid 5 Octet Sequence (but not Unicode!)
            "\xf8\xa1\xa1\xa1\xa1" => ['�' => ''],
            // Valid 6 Octet Sequence (but not Unicode!)
            "\xfc\xa1\xa1\xa1\xa1\xa1" => ['�' => ''],
        ];

        $counter = 0;
        foreach ($examples as $testString => $testResults) {
            foreach ($testResults as $before => $after) {
                static::assertSame($after, UTF8::cleanup($before), 'tested: ' . $counter);
            }
            ++$counter;
        }
    }

    /**
     * @dataProvider containsAnyProvider()
     *
     * @param      $expected
     * @param      $haystack
     * @param      $needles
     * @param bool $caseSensitive
     */
    public function testTestcontainsAny($expected, $haystack, $needles, $caseSensitive = true)
    {
        $result = UTF8::str_contains_any($haystack, $needles, $caseSensitive);

        static::assertTrue(\is_bool($result));
        static::assertSame($expected, $result);
    }

    /**
     * @dataProvider regexReplaceProvider()
     *
     * @param        $expected
     * @param        $str
     * @param        $pattern
     * @param        $replacement
     * @param string $options
     * @param string $delimiter
     * @param        $encoding
     */
    public function testTestregexReplace($expected, $str, $pattern, $replacement, $options = 'msr', $delimiter = '/', $encoding = '')
    {
        $result = UTF8::regex_replace($str, $pattern, $replacement, $options, $delimiter);

        static::assertSame($expected, $result);
    }

    /**
     * @return \Iterator
     */
    public function tidyProvider(): \Iterator
    {
        yield ['"I see..."', '“I see…”'];
        yield ["'This too'", '‘This too’'];
        yield ['test-dash', 'test—dash'];
        yield ['Ο συγγραφέας είπε...', 'Ο συγγραφέας είπε…'];
    }

    /**
     * @return \Iterator
     */
    public function titleizeProvider(): \Iterator
    {
        $ignore = ['at', 'by', 'for', 'in', 'of', 'on', 'out', 'to', 'the'];

        yield ['Title Case', 'TITLE CASE'];
        yield ['Up-to-Date', 'up-to-date', ['to'], '-'];
        yield ['Up-to-Date', 'up-to-date', ['to'], '-*'];
        yield ['Up-To-Date', 'up-to-date', [], '-*'];
        yield ['Up-To-D*A*T*E*', 'up-to-d*a*t*e*', [], '-*'];
        yield ['Title Case', "\n\t" . 'TITLE CASE '];
        yield ['Testing The Method', 'testing the method'];
        yield ['Testing the Method', 'testing the method', $ignore];
        yield [
            'I Like to Watch Dvds at Home',
            'i like to watch DVDs at home',
            $ignore,
        ];
        yield ['Θα Ήθελα Να Φύγει', '  Θα ήθελα να φύγει  ', null, null, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function toAsciiProvider(): \Iterator
    {
        yield ['foo bar', 'fòô bàř'];
        yield [' TEST ', ' ŤÉŚŢ '];
        yield ['ph = z = 3', 'φ = ź = 3'];
        yield ['perevirka', 'перевірка'];
        yield ['lysaia gora', 'лысая гора'];
        yield ['shchuka', 'щука'];
        yield ['Han Zi ', '漢字'];
        yield ['xin chao the gioi', 'xin chào thế giới'];
        yield ['XIN CHAO THE GIOI', 'XIN CHÀO THẾ GIỚI'];
        yield ['dam phat chet luon', 'đấm phát chết luôn'];
        yield [' ', ' '];
        // no-break space (U+00A0)
        yield ['           ', '           '];
        // spaces U+2000 to U+200A
        yield [' ', ' '];
        // narrow no-break space (U+202F)
        yield [' ', ' '];
        // medium mathematical space (U+205F)
        yield [' ', '　'];
        // ideographic space (U+3000)
        yield ['?', '𐍉'];
    }

    /**
     * @return \Iterator
     */
    public function toBooleanProvider(): \Iterator
    {
        yield [true, true];
        yield [true, 'true'];
        yield [true, '1'];
        yield [true, '1.0'];
        yield [true, 1.0];
        yield [true, '2.1'];
        yield [true, 2.1];
        yield [true, 1];
        yield [true, 'on'];
        yield [true, 'ON'];
        yield [true, 'yes'];
        yield [true, '999'];
        yield [false, false];
        yield [false, 'false'];
        yield [false, '0'];
        yield [false, 0];
        yield [false, '0.00'];
        yield [false, 0.00];
        yield [false, 'off'];
        yield [false, 'OFF'];
        yield [false, 'no'];
        yield [false, '-999'];
        yield [false, ''];
        yield [false, ' '];
        yield [false, '  ', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function toLowerCaseProvider(): \Iterator
    {
        yield ['foo bar', 'FOO BAR'];
        yield [' foo_bar ', ' FOO_bar '];
        yield ['fòô bàř', 'FÒÔ BÀŘ', 'UTF-8'];
        yield [' fòô_bàř ', ' FÒÔ_bàř ', 'UTF-8'];
        yield ['αυτοκίνητο', 'ΑΥΤΟΚΊΝΗΤΟ', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function toSpacesProvider(): \Iterator
    {
        yield ['    foo    bar    ', '	foo	bar	'];
        yield ['     foo     bar     ', '	foo	bar	', 5];
        yield ['    foo  bar  ', '		foo	bar	', 2];
        yield ['foobar', '	foo	bar	', 0];
        yield ["    foo\n    bar", "	foo\n	bar"];
        yield ["    fòô\n    bàř", "	fòô\n	bàř"];
    }

    /**
     * @return \Iterator
     */
    public function toStringProvider(): \Iterator
    {
        yield ['', null];
        yield ['', false];
        yield ['1', true];
        yield ['-9', -9];
        yield ['1.18', 1.18];
        yield [' string  ', ' string  '];
    }

    /**
     * @return \Iterator
     */
    public function toTabsProvider(): \Iterator
    {
        yield ['	foo	bar	', '    foo    bar    '];
        yield ['	foo	bar	', '     foo     bar     ', 5];
        yield ['		foo	bar	', '    foo  bar  ', 2];
        yield ["	foo\n	bar", "    foo\n    bar"];
        yield ["	fòô\n	bàř", "    fòô\n    bàř"];
    }

    /**
     * @return \Iterator
     */
    public function toTitleCaseProvider(): \Iterator
    {
        yield ['Foo Bar', 'foo bar'];
        yield [' Foo_Bar ', ' foo_bar '];
        yield ['Fòô Bàř', 'fòô bàř', 'UTF-8'];
        yield [' Fòô_Bàř ', ' fòô_bàř ', 'UTF-8'];
        yield ['Αυτοκίνητο Αυτοκίνητο', 'αυτοκίνητο αυτοκίνητο', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function toUpperCaseProvider(): \Iterator
    {
        yield ['FOO BAR', 'foo bar'];
        yield [' FOO_BAR ', ' FOO_bar '];
        yield ['FÒÔ BÀŘ', 'fòô bàř', 'UTF-8'];
        yield [' FÒÔ_BÀŘ ', ' FÒÔ_bàř ', 'UTF-8'];
        yield ['ΑΥΤΟΚΊΝΗΤΟ', 'αυτοκίνητο', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function trimLeftProvider(): \Iterator
    {
        yield ['foo   bar  ', '  foo   bar  '];
        yield ['foo bar', ' foo bar'];
        yield ['foo bar ', 'foo bar '];
        yield ["foo bar \n\t", "\n\t foo bar \n\t"];
        yield ['fòô   bàř  ', '  fòô   bàř  '];
        yield ['fòô bàř', ' fòô bàř'];
        yield ['fòô bàř ', 'fòô bàř '];
        yield ['foo bar', '--foo bar', '-'];
        yield ['fòô bàř', 'òòfòô bàř', 'ò', 'UTF-8'];
        yield ["fòô bàř \n\t", "\n\t fòô bàř \n\t", null, 'UTF-8'];
        yield ['fòô ', ' fòô ', null, 'UTF-8'];
        // narrow no-break space (U+202F)
        yield ['fòô  ', '  fòô  ', null, 'UTF-8'];
        // medium mathematical space (U+205F)
        yield ['fòô', '           fòô', null, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function trimProvider(): \Iterator
    {
        yield ['foo   bar', '  foo   bar  '];
        yield ['foo bar', ' foo bar'];
        yield ['foo bar', 'foo bar '];
        yield ['foo bar', "\n\t foo bar \n\t"];
        yield ['fòô   bàř', '  fòô   bàř  '];
        yield ['fòô bàř', ' fòô bàř'];
        yield ['fòô bàř', 'fòô bàř '];
        yield [' foo bar ', "\n\t foo bar \n\t", "\n\t"];
        yield ['fòô bàř', "\n\t fòô bàř \n\t", null, 'UTF-8'];
        yield ['fòô', ' fòô ', null, 'UTF-8'];
        // narrow no-break space (U+202F)
        yield ['fòô', '  fòô  ', null, 'UTF-8'];
        // medium mathematical space (U+205F)
        yield ['fòô', '           fòô', null, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function trimRightProvider(): \Iterator
    {
        yield ['  foo   bar', '  foo   bar  '];
        yield ['foo bar', 'foo bar '];
        yield [' foo bar', ' foo bar'];
        yield ["\n\t foo bar", "\n\t foo bar \n\t"];
        yield ['  fòô   bàř', '  fòô   bàř  '];
        yield ['fòô bàř', 'fòô bàř '];
        yield [' fòô bàř', ' fòô bàř'];
        yield ['foo bar', 'foo bar--', '-'];
        yield ['fòô bàř', 'fòô bàřòò', 'ò', 'UTF-8'];
        yield ["\n\t fòô bàř", "\n\t fòô bàř \n\t", null, 'UTF-8'];
        yield [' fòô', ' fòô ', null, 'UTF-8'];
        // narrow no-break space (U+202F)
        yield ['  fòô', '  fòô  ', null, 'UTF-8'];
        // medium mathematical space (U+205F)
        yield ['fòô', 'fòô           ', null, 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function truncateProvider(): \Iterator
    {
        yield ['Test foo bar', 'Test foo bar', 12];
        yield ['Test foo ba', 'Test foo bar', 11];
        yield ['Test foo', 'Test foo bar', 8];
        yield ['Test fo', 'Test foo bar', 7];
        yield ['Test', 'Test foo bar', 4];
        yield ['Test foo bar', 'Test foo bar', 12, '...'];
        yield ['Test foo...', 'Test foo bar', 11, '...'];
        yield ['Test ...', 'Test foo bar', 8, '...'];
        yield ['Test...', 'Test foo bar', 7, '...'];
        yield ['T...', 'Test foo bar', 4, '...'];
        yield ['Test fo....', 'Test foo bar', 11, '....'];
        yield ['Test fòô bàř', 'Test fòô bàř', 12, '', 'UTF-8'];
        yield ['Test fòô bà', 'Test fòô bàř', 11, '', 'UTF-8'];
        yield ['Test fòô', 'Test fòô bàř', 8, '', 'UTF-8'];
        yield ['Test fò', 'Test fòô bàř', 7, '', 'UTF-8'];
        yield ['Test', 'Test fòô bàř', 4, '', 'UTF-8'];
        yield ['Test fòô bàř', 'Test fòô bàř', 12, 'ϰϰ', 'UTF-8'];
        yield ['Test fòô ϰϰ', 'Test fòô bàř', 11, 'ϰϰ', 'UTF-8'];
        yield ['Test fϰϰ', 'Test fòô bàř', 8, 'ϰϰ', 'UTF-8'];
        yield ['Test ϰϰ', 'Test fòô bàř', 7, 'ϰϰ', 'UTF-8'];
        yield ['Teϰϰ', 'Test fòô bàř', 4, 'ϰϰ', 'UTF-8'];
        yield ['What are your pl...', 'What are your plans today?', 19, '...'];
    }

    /**
     * @return \Iterator
     */
    public function underscoredProvider(): \Iterator
    {
        yield ['test_case', 'testCase'];
        yield ['test_case', 'Test-Case'];
        yield ['test_case', 'test case'];
        yield ['test_case', 'test -case'];
        yield ['_test_case', '-test - case'];
        yield ['test_case', 'test_case'];
        yield ['test_c_test', '  test c test'];
        yield ['test_u_case', 'TestUCase'];
        yield ['test_c_c_test', 'TestCCTest'];
        yield ['string_with1number', 'string_with1number'];
        yield ['string_with_2_2_numbers', 'String-with_2_2 numbers'];
        yield ['1test2case', '1test2case'];
        yield ['yes_we_can', 'yesWeCan'];
        yield ['test_σase', 'test Σase', 'UTF-8'];
        yield ['στανιλ_case', 'Στανιλ case', 'UTF-8'];
        yield ['σash_case', 'Σash  Case', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function upperCamelizeProvider(): \Iterator
    {
        yield ['CamelCase', 'camelCase'];
        yield ['CamelCase', 'Camel-Case'];
        yield ['CamelCase', 'camel case'];
        yield ['CamelCase', 'camel -case'];
        yield ['CamelCase', 'camel - case'];
        yield ['CamelCase', 'camel_case'];
        yield ['CamelCTest', 'camel c test'];
        yield ['StringWith1Number', 'string_with1number'];
        yield ['StringWith22Numbers', 'string-with-2-2 numbers'];
        yield ['1Camel2Case', '1camel2case'];
        yield ['CamelΣase', 'camel σase', 'UTF-8'];
        yield ['ΣτανιλCase', 'στανιλ case', 'UTF-8'];
        yield ['ΣamelCase', 'Σamel  Case', 'UTF-8'];
    }

    /**
     * @return \Iterator
     */
    public function upperCaseFirstProvider(): \Iterator
    {
        yield ['Test', 'Test'];
        yield ['Test', 'test'];
        yield ['1a', '1a'];
        yield ['Σ test', 'σ test', 'UTF-8'];
        yield [' σ test', ' σ test', 'UTF-8'];
    }
}
