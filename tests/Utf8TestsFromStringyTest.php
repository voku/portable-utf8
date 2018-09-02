<?php

use voku\helper\UTF8;

/**
 * Class Utf8TestsFromStringyTest
 */
class Utf8TestsFromStringyTest extends \PHPUnit\Framework\TestCase
{
  /**
   * @return array
   */
  public function appendProvider(): array
  {
    return [
        ['foobar', 'foo', 'bar'],
        ['fòôbàř', 'fòô', 'bàř', 'UTF-8'],
    ];
  }

  /**
   * Asserts that a variable is of a UTF8 instance.
   *
   * @param string $str
   */
  public static function assertUtf8String(string $str)
  {
    self::assertTrue(UTF8::is_utf8($str));
  }

  /**
   * @return array
   */
  public function atProvider(): array
  {
    return [
        ['f', 'foo bar', 0],
        ['o', 'foo bar', 1],
        ['r', 'foo bar', 6],
        ['', 'foo bar', 7],
        ['f', 'fòô bàř', 0, 'UTF-8'],
        ['ò', 'fòô bàř', 1, 'UTF-8'],
        ['ř', 'fòô bàř', 6, 'UTF-8'],
        ['', 'fòô bàř', 7, 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function betweenProvider(): array
  {
    return [
        ['', 'foo', '{', '}'],
        ['', '{foo', '{', '}'],
        ['foo', '{foo}', '{', '}'],
        ['{foo', '{{foo}', '{', '}'],
        ['', '{}foo}', '{', '}'],
        ['foo', '}{foo}', '{', '}'],
        ['foo', 'A description of {foo} goes here', '{', '}'],
        ['bar', '{foo} and {bar}', '{', '}', 1],
        ['', 'fòô', '{', '}', 0, 'UTF-8'],
        ['', '{fòô', '{', '}', 0, 'UTF-8'],
        ['fòô', '{fòô}', '{', '}', 0, 'UTF-8'],
        ['{fòô', '{{fòô}', '{', '}', 0, 'UTF-8'],
        ['', '{}fòô}', '{', '}', 0, 'UTF-8'],
        ['fòô', '}{fòô}', '{', '}', 0, 'UTF-8'],
        ['fòô', 'A description of {fòô} goes here', '{', '}', 0, 'UTF-8'],
        ['bàř', '{fòô} and {bàř}', '{', '}', 1, 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function camelizeProvider(): array
  {
    return [
        ['camelCase', 'CamelCase'],
        ['camelCase', 'Camel-Case'],
        ['camelCase', 'camel case'],
        ['camelCase', 'camel -case'],
        ['camelCase', 'camel - case'],
        ['camelCase', 'camel_case'],
        ['camelCTest', 'camel c test'],
        ['stringWith1Number', 'string_with1number'],
        ['stringWith22Numbers', 'string-with-2-2 numbers'],
        ['dataRate', 'data_rate'],
        ['backgroundColor', 'background-color'],
        ['yesWeCan', 'yes_we_can'],
        ['mozSomething', '-moz-something'],
        ['carSpeed', '_car_speed_'],
        ['serveHTTP', 'ServeHTTP'],
        ['1Camel2Case', '1camel2case'],
        ['camelΣase', 'camel σase', 'UTF-8'],
        ['στανιλCase', 'Στανιλ case', 'UTF-8'],
        ['σamelCase', 'σamel  Case', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function capitalizePersonalNameProvider(): array
  {
    return [
        ['Marcus Aurelius', 'marcus aurelius'],
        ['Torbjørn Færøvik', 'torbjørn færøvik'],
        ['Jaap de Hoop Scheffer', 'jaap de hoop scheffer'],
        ['K. Anders Ericsson', 'k. anders ericsson'],
        ['Per-Einar', 'per-einar'],
        [
            'Line Break',
            'line
             break',
        ],
        ['ab', 'ab'],
        ['af', 'af'],
        ['al', 'al'],
        ['and', 'and'],
        ['ap', 'ap'],
        ['bint', 'bint'],
        ['binte', 'binte'],
        ['da', 'da'],
        ['de', 'de'],
        ['del', 'del'],
        ['den', 'den'],
        ['der', 'der'],
        ['di', 'di'],
        ['dit', 'dit'],
        ['ibn', 'ibn'],
        ['la', 'la'],
        ['mac', 'mac'],
        ['nic', 'nic'],
        ['of', 'of'],
        ['ter', 'ter'],
        ['the', 'the'],
        ['und', 'und'],
        ['van', 'van'],
        ['von', 'von'],
        ['y', 'y'],
        ['zu', 'zu'],
        ['Bashar al-Assad', 'bashar al-assad'],
        ["d'Name", "d'Name"],
        ['ffName', 'ffName'],
        ["l'Name", "l'Name"],
        ['macDuck', 'macDuck'],
        ['mcDuck', 'mcDuck'],
        ['nickMick', 'nickMick'],
    ];
  }

  /**
   * @return array
   */
  public function charsProvider(): array
  {
    return [
        [[], ''],
        [['T', 'e', 's', 't'], 'Test'],
        [['F', 'ò', 'ô', ' ', 'B', 'à', 'ř'], 'Fòô Bàř'],
    ];
  }

  /**
   * @return array
   */
  public function collapseWhitespaceProvider(): array
  {
    return [
        ['foo bar', '  foo   bar  '],
        ['test string', 'test string'],
        ['Ο συγγραφέας', '   Ο     συγγραφέας  '],
        ['123', ' 123 '],
        ['', ' ', 'UTF-8'], // no-break space (U+00A0)
        ['', '           '], // spaces U+2000 to U+200A
        ['', ' ', 'UTF-8'], // narrow no-break space (U+202F)
        ['', ' ', 'UTF-8'], // medium mathematical space (U+205F)
        ['', '　', 'UTF-8'], // ideographic space (U+3000)
        ['1 2 3', '  1  2  3　　'],
        ['', ' '],
        ['', ''],
    ];
  }

  /**
   * @return array
   */
  public function containsAllProvider(): array
  {
    // One needle
    $singleNeedle = array_map(
        function ($array) {
          $array[2] = [$array[2]];

          return $array;
        }, $this->containsProvider()
    );

    $provider = [
      // One needle
      [false, 'Str contains foo bar', []],
      [false, 'Str contains foo bar', ['']],
      // Multiple needles
      [true, 'Str contains foo bar', ['foo', 'bar']],
      [true, '12398!@(*%!@# @!%#*&^%', [' @!%#*', '&^%']],
      [true, 'Ο συγγραφέας είπε', ['συγγρ', 'αφέας'], 'UTF-8'],
      [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['å´¥', '©'], true, 'UTF-8'],
      [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['å˚ ', '∆'], true, 'UTF-8'],
      [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['øœ', '¬'], true, 'UTF-8'],
      [false, 'Str contains foo bar', ['Foo', 'bar']],
      [false, 'Str contains foo bar', ['foobar', 'bar']],
      [false, 'Str contains foo bar', ['foo bar ', 'bar']],
      [false, 'Ο συγγραφέας είπε', ['  συγγραφέας ', '  συγγραφ '], true, 'UTF-8'],
      [false, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', [' ßå˚', ' ß '], true, 'UTF-8'],
      [true, 'Str contains foo bar', ['Foo bar', 'bar'], false],
      [true, '12398!@(*%!@# @!%#*&^%', [' @!%#*&^%', '*&^%'], false],
      [true, 'Ο συγγραφέας είπε', ['ΣΥΓΓΡΑΦΈΑΣ', 'ΑΦΈΑ'], false, 'UTF-8'],
      [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['Å´¥©', '¥©'], false, 'UTF-8'],
      [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['Å˚ ∆', ' ∆'], false, 'UTF-8'],
      [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['ØŒ¬', 'Œ'], false, 'UTF-8'],
      [false, 'Str contains foo bar', ['foobar', 'none'], false],
      [false, 'Str contains foo bar', ['foo bar ', ' ba'], false],
      [false, 'Ο συγγραφέας είπε', ['  συγγραφέας ', ' ραφέ '], false, 'UTF-8'],
      [false, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', [' ßÅ˚', ' Å˚ '], false, 'UTF-8'],
    ];

    return array_merge($singleNeedle, $provider);
  }

  /**
   * @return array
   */
  public function containsAnyProvider(): array
  {
    // One needle
    $singleNeedle = array_map(
        function ($array) {
          $array[2] = [$array[2]];

          return $array;
        }, $this->containsProvider()
    );

    $provider = [
      // No needles
      [false, 'Str contains foo bar', []],
      // Multiple needles
      [true, 'Str contains foo bar', ['foo', 'bar']],
      [true, '12398!@(*%!@# @!%#*&^%', [' @!%#*', '&^%']],
      [true, 'Ο συγγραφέας είπε', ['συγγρ', 'αφέας'], 'UTF-8'],
      [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['å´¥', '©'], true, 'UTF-8'],
      [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['å˚ ', '∆'], true, 'UTF-8'],
      [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['øœ', '¬'], true, 'UTF-8'],
      [false, 'Str contains foo bar', ['Foo', 'Bar']],
      [false, 'Str contains foo bar', ['foobar', 'bar ']],
      [false, 'Str contains foo bar', ['foo bar ', '  foo']],
      [false, 'Ο συγγραφέας είπε', ['  συγγραφέας ', '  συγγραφ '], true, 'UTF-8'],
      [false, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', [' ßå˚', ' ß '], true, 'UTF-8'],
      [true, 'Str contains foo bar', ['Foo bar', 'bar'], false],
      [true, '12398!@(*%!@# @!%#*&^%', [' @!%#*&^%', '*&^%'], false],
      [true, 'Ο συγγραφέας είπε', ['ΣΥΓΓΡΑΦΈΑΣ', 'ΑΦΈΑ'], false, 'UTF-8'],
      [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['Å´¥©', '¥©'], false, 'UTF-8'],
      [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['Å˚ ∆', ' ∆'], false, 'UTF-8'],
      [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ['ØŒ¬', 'Œ'], false, 'UTF-8'],
      [false, 'Str contains foo bar', ['foobar', 'none'], false],
      [false, 'Str contains foo bar', ['foo bar ', ' ba '], false],
      [false, 'Ο συγγραφέας είπε', ['  συγγραφέας ', ' ραφέ '], false, 'UTF-8'],
      [false, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', [' ßÅ˚', ' Å˚ '], false, 'UTF-8'],
    ];

    return array_merge($singleNeedle, $provider);
  }

  /**
   * @return array
   */
  public function containsProvider(): array
  {
    return [
        [true, 'Str contains foo bar', 'foo bar'],
        [true, '12398!@(*%!@# @!%#*&^%', ' @!%#*&^%'],
        [true, 'Ο συγγραφέας είπε', 'συγγραφέας', 'UTF-8'],
        [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'å´¥©', true, 'UTF-8'],
        [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'å˚ ∆', true, 'UTF-8'],
        [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'øœ¬', true, 'UTF-8'],
        [false, 'Str contains foo bar', 'Foo bar'],
        [false, 'Str contains foo bar', 'foobar'],
        [false, 'Str contains foo bar', 'foo bar '],
        [false, 'Ο συγγραφέας είπε', '  συγγραφέας ', true, 'UTF-8'],
        [false, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ' ßå˚', true, 'UTF-8'],
        [true, 'Str contains foo bar', 'Foo bar', false],
        [true, '12398!@(*%!@# @!%#*&^%', ' @!%#*&^%', false],
        [true, 'Ο συγγραφέας είπε', 'ΣΥΓΓΡΑΦΈΑΣ', false, 'UTF-8'],
        [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'Å´¥©', false, 'UTF-8'],
        [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'Å˚ ∆', false, 'UTF-8'],
        [true, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'ØŒ¬', false, 'UTF-8'],
        [false, 'Str contains foo bar', 'foobar', false],
        [false, 'Str contains foo bar', 'foo bar ', false],
        [false, 'Ο συγγραφέας είπε', '  συγγραφέας ', false, 'UTF-8'],
        [false, 'å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', ' ßÅ˚', false, 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function countSubstrProvider(): array
  {
    return [
        [0, '', 'foo'],
        [0, 'foo', 'bar'],
        [1, 'foo bar', 'foo'],
        [2, 'foo bar', 'o'],
        [0, '', 'fòô', 'UTF-8'],
        [0, 'fòô', 'bàř', 'UTF-8'],
        [1, 'fòô bàř', 'fòô', 'UTF-8'],
        [2, 'fôòô bàř', 'ô', 'UTF-8'],
        [0, 'fÔÒÔ bàř', 'ô', 'UTF-8'],
        [0, 'foo', 'BAR', false],
        [1, 'foo bar', 'FOo', false],
        [2, 'foo bar', 'O', false],
        [1, 'fòô bàř', 'fÒÔ', false, 'UTF-8'],
        [2, 'fôòô bàř', 'Ô', false, 'UTF-8'],
        [2, 'συγγραφέας', 'Σ', false, 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function dasherizeProvider(): array
  {
    return [
        ['test-case', 'testCase'],
        ['test-case', 'Test-Case'],
        ['test-case', 'test case'],
        ['-test-case', '-test -case'],
        ['test-case', 'test - case'],
        ['test-case', 'test_case'],
        ['test-c-test', 'test c test'],
        ['test-d-case', 'TestDCase'],
        ['test-c-c-test', 'TestCCTest'],
        ['string-with1number', 'string_with1number'],
        ['string-with-2-2-numbers', 'String-with_2_2 numbers'],
        ['1test2case', '1test2case'],
        ['data-rate', 'dataRate'],
        ['car-speed', 'CarSpeed'],
        ['yes-we-can', 'yesWeCan'],
        ['background-color', 'backgroundColor'],
        ['dash-σase', 'dash Σase', 'UTF-8'],
        ['στανιλ-case', 'Στανιλ case', 'UTF-8'],
        ['σash-case', 'Σash  Case', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function delimitProvider(): array
  {
    return [
        ['test*case', 'testCase', '*'],
        ['test&case', 'Test-Case', '&'],
        ['test#case', 'test case', '#'],
        ['test**case', 'test -case', '**'],
        ['~!~test~!~case', '-test - case', '~!~'],
        ['test*case', 'test_case', '*'],
        ['test%c%test', '  test c test', '%'],
        ['test+u+case', 'TestUCase', '+'],
        ['test=c=c=test', 'TestCCTest', '='],
        ['string#>with1number', 'string_with1number', '#>'],
        ['1test2case', '1test2case', '*'],
        ['test ύα σase', 'test Σase', ' ύα ', 'UTF-8',],
        ['στανιλαcase', 'Στανιλ case', 'α', 'UTF-8',],
        ['σashΘcase', 'Σash  Case', 'Θ', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function endsWithAnyProvider(): array
  {
    return [
        [true, 'foo bars', ['foo', 'o bars']],
        [true, 'FOO bars', ['foo', 'o bars'], false],
        [true, 'FOO bars', ['foo', 'o BARs'], false],
        [true, 'FÒÔ bàřs', ['foo', 'ô bàřs'], false, 'UTF-8'],
        [true, 'fòô bàřs', ['foo', 'ô BÀŘs'], false, 'UTF-8'],
        [false, 'foo bar', ['foo']],
        [false, 'foo bar', ['foo', 'foo bars']],
        [false, 'FOO bar', ['foo', 'foo bars']],
        [false, 'FOO bars', ['foo', 'foo BARS']],
        [false, 'FÒÔ bàřs', ['fòô', 'fòô bàřs'], true, 'UTF-8'],
        [false, 'fòô bàřs', ['fòô', 'fòô BÀŘS'], true, 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function endsWithProvider(): array
  {
    return [
        [true, 'foo bars', 'o bars'],
        [true, 'FOO bars', 'o bars', false],
        [true, 'FOO bars', 'o BARs', false],
        [true, 'FÒÔ bàřs', 'ô bàřs', false],
        [true, 'fòô bàřs', 'ô BÀŘs', false],
        [false, 'foo bar', 'foo'],
        [false, 'foo bar', 'foo bars'],
        [false, 'FOO bar', 'foo bars'],
        [false, 'FOO bars', 'foo BARS'],
        [false, 'FÒÔ bàřs', 'fòô bàřs', true],
        [false, 'fòô bàřs', 'fòô BÀŘS', true],
    ];
  }

  /**
   * @return array
   */
  public function ensureLeftProvider(): array
  {
    return [
        ['foobar', 'foobar', 'f'],
        ['foobar', 'foobar', 'foo'],
        ['foo/foobar', 'foobar', 'foo/'],
        ['http://foobar', 'foobar', 'http://'],
        ['http://foobar', 'http://foobar', 'http://'],
        ['fòôbàř', 'fòôbàř', 'f'],
        ['fòôbàř', 'fòôbàř', 'fòô'],
        ['fòô/fòôbàř', 'fòôbàř', 'fòô/'],
        ['http://fòôbàř', 'fòôbàř', 'http://'],
        ['http://fòôbàř', 'http://fòôbàř', 'http://'],
    ];
  }

  /**
   * @return array
   */
  public function ensureRightProvider(): array
  {
    return [
        ['foobar', 'foobar', 'r'],
        ['foobar', 'foobar', 'bar'],
        ['foobar/bar', 'foobar', '/bar'],
        ['foobar.com/', 'foobar', '.com/'],
        ['foobar.com/', 'foobar.com/', '.com/'],
        ['fòôbàř', 'fòôbàř', 'ř'],
        ['fòôbàř', 'fòôbàř', 'bàř'],
        ['fòôbàř/bàř', 'fòôbàř', '/bàř'],
        ['fòôbàř.com/', 'fòôbàř', '.com/'],
        ['fòôbàř.com/', 'fòôbàř.com/', '.com/'],
    ];
  }

  /**
   * @return array
   */
  public function escapeProvider(): array
  {
    return [
        ['', ''],
        ['raboof &lt;3', 'raboof <3'],
        ['řàbôòf&lt;foo&lt;lall&gt;&gt;&gt;', 'řàbôòf<foo<lall>>>'],
        ['řàb &lt;ô&gt;òf', 'řàb <ô>òf'],
        ['&lt;∂∆ onerro=&quot;alert(xss)&quot;&gt; ˚åß', '<∂∆ onerro="alert(xss)"> ˚åß'],
        ['&#039;œ … &#039;’)', '\'œ … \'’)'],
    ];
  }

  /**
   * @return array
   */
  public function firstProvider(): array
  {
    return [
        ['', 'foo bar', -5],
        ['', 'foo bar', 0],
        ['f', 'foo bar', 1],
        ['foo', 'foo bar', 3],
        ['foo bar', 'foo bar', 7],
        ['foo bar', 'foo bar', 8],
        ['', 'fòô bàř', -5, 'UTF-8'],
        ['', 'fòô bàř', 0, 'UTF-8'],
        ['f', 'fòô bàř', 1, 'UTF-8'],
        ['fòô', 'fòô bàř', 3, 'UTF-8'],
        ['fòô bàř', 'fòô bàř', 7, 'UTF-8'],
        ['fòô bàř', 'fòô bàř', 8, 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function hasLowerCaseProvider(): array
  {
    return [
        [false, ''],
        [true, 'foobar'],
        [false, 'FOO BAR'],
        [true, 'fOO BAR'],
        [true, 'foO BAR'],
        [true, 'FOO BAr'],
        [true, 'Foobar'],
        [false, 'FÒÔBÀŘ'],
        [true, 'fòôbàř'],
        [true, 'fòôbàř2'],
        [true, 'Fòô bàř'],
        [true, 'fòôbÀŘ'],
    ];
  }

  /**
   * @return array
   */
  public function hasUpperCaseProvider(): array
  {
    return [
        [false, ''],
        [true, 'FOOBAR'],
        [false, 'foo bar'],
        [true, 'Foo bar'],
        [true, 'FOo bar'],
        [true, 'foo baR'],
        [true, 'fOOBAR'],
        [false, 'fòôbàř'],
        [true, 'FÒÔBÀŘ'],
        [true, 'FÒÔBÀŘ2'],
        [true, 'fÒÔ BÀŘ'],
        [true, 'FÒÔBàř'],
    ];
  }

  /**
   * @return array
   */
  public function htmlDecodeProvider(): array
  {
    return [
        ['&', '&amp;'],
        ['"', '&quot;'],
        ["'", '&#039;', ENT_QUOTES],
        ['<', '&lt;'],
        ['>', '&gt;'],
    ];
  }

  /**
   * @return array
   */
  public function htmlEncodeProvider(): array
  {
    return [
        ['&amp;', '&'],
        ['&quot;', '"'],
        ['&#039;', "'", ENT_QUOTES],
        ['&lt;', '<'],
        ['&gt;', '>'],
    ];
  }

  /**
   * @return array
   */
  public function humanizeProvider(): array
  {
    return [
        ['Author', 'author_id'],
        ['Test user', ' _test_user_'],
        ['Συγγραφέας', ' συγγραφέας_id ', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function indexOfLastProvider(): array
  {
    return [
        [6, 'foo & bar', 'bar'],
        [6, 'foo & bar', 'bar', 0],
        [false, 'foo & bar', 'baz'],
        [false, 'foo & bar', 'baz', 0],
        [12, 'foo & bar & foo', 'foo', 0],
        [0, 'foo & bar & foo', 'foo', -5],
        [6, 'fòô & bàř', 'bàř', 0, 'UTF-8'],
        [false, 'fòô & bàř', 'baz', 0, 'UTF-8'],
        [12, 'fòô & bàř & fòô', 'fòô', 0, 'UTF-8'],
        [0, 'fòô & bàř & fòô', 'fòô', -5, 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function indexOfProvider(): array
  {
    return [
        [6, 'foo & bar', 'bar'],
        [6, 'foo & bar', 'bar', 0],
        [false, 'foo & bar', 'baz'],
        [false, 'foo & bar', 'baz', 0],
        [0, 'foo & bar & foo', 'foo', 0],
        [12, 'foo & bar & foo', 'foo', 5],
        [6, 'fòô & bàř', 'bàř', 0, 'UTF-8'],
        [false, 'fòô & bàř', 'baz', 0, 'UTF-8'],
        [0, 'fòô & bàř & fòô', 'fòô', 0, 'UTF-8'],
        [12, 'fòô & bàř & fòô', 'fòô', 5, 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function insertProvider(): array
  {
    return [
        ['foo bar', 'oo bar', 'f', 0],
        ['foo bar', 'f bar', 'oo', 1],
        ['f bar', 'f bar', 'oo', 20],
        ['foo bar', 'foo ba', 'r', 6],
        ['fòôbàř', 'fòôbř', 'à', 4, 'UTF-8'],
        ['fòô bàř', 'òô bàř', 'f', 0, 'UTF-8'],
        ['fòô bàř', 'f bàř', 'òô', 1, 'UTF-8'],
        ['fòô bàř', 'fòô bà', 'ř', 6, 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function isAlphaProvider(): array
  {
    return [
        [true, ''],
        [true, 'foobar'],
        [false, 'foo bar'],
        [false, 'foobar2'],
        [true, 'fòôbàř', 'UTF-8'],
        [false, 'fòô bàř', 'UTF-8'],
        [false, 'fòôbàř2', 'UTF-8'],
        [true, 'ҠѨњфгШ', 'UTF-8'],
        [false, 'ҠѨњ¨ˆфгШ', 'UTF-8'],
        [true, '丹尼爾', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function isAlphanumericProvider(): array
  {
    return [
        [true, ''],
        [true, 'foobar1'],
        [false, 'foo bar'],
        [false, 'foobar2"'],
        [false, "\nfoobar\n"],
        [true, 'fòôbàř1', 'UTF-8'],
        [false, 'fòô bàř', 'UTF-8'],
        [false, 'fòôbàř2"', 'UTF-8'],
        [true, 'ҠѨњфгШ', 'UTF-8'],
        [false, 'ҠѨњ¨ˆфгШ', 'UTF-8'],
        [true, '丹尼爾111', 'UTF-8'],
        [true, 'دانيال1', 'UTF-8'],
        [false, 'دانيال1 ', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function isBase64Provider(): array
  {
    return [
        [false, ' '],
        [false, ''],
        [true, base64_encode('FooBar')],
        [true, base64_encode(' ')],
        [true, base64_encode('FÒÔBÀŘ')],
        [true, base64_encode('συγγραφέας')],
        [false, 'Foobar'],
    ];
  }

  /**
   * @return array
   */
  public function isBlankProvider(): array
  {
    return [
        [true, ''],
        [true, ' '],
        [true, "\n\t "],
        [true, "\n\t  \v\f"],
        [false, "\n\t a \v\f"],
        [false, "\n\t ' \v\f"],
        [false, "\n\t 2 \v\f"],
        [true, '', 'UTF-8'],
        [true, ' ', 'UTF-8'], // no-break space (U+00A0)
        [true, '           ', 'UTF-8'], // spaces U+2000 to U+200A
        [true, ' ', 'UTF-8'], // narrow no-break space (U+202F)
        [true, ' ', 'UTF-8'], // medium mathematical space (U+205F)
        [true, '　', 'UTF-8'], // ideographic space (U+3000)
        [false, '　z', 'UTF-8'],
        [false, '　1', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function isHexadecimalProvider(): array
  {
    return [
        [true, ''],
        [true, 'abcdef'],
        [true, 'ABCDEF'],
        [true, '0123456789'],
        [true, '0123456789AbCdEf'],
        [false, '0123456789x'],
        [false, 'ABCDEFx'],
        [true, 'abcdef', 'UTF-8'],
        [true, 'ABCDEF', 'UTF-8'],
        [true, '0123456789', 'UTF-8'],
        [true, '0123456789AbCdEf', 'UTF-8'],
        [false, '0123456789x', 'UTF-8'],
        [false, 'ABCDEFx', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function isJsonProvider(): array
  {
    return [
        [false, ''],
        [false, '  '],
        [false, 'null'],
        [false, 'true'],
        [false, 'false'],
        [true, '[]'],
        [true, '{}'],
        [false, '123'],
        [true, '{"foo": "bar"}'],
        [false, '{"foo":"bar",}'],
        [false, '{"foo"}'],
        [true, '["foo"]'],
        [false, '{"foo": "bar"]'],
        [false, '123', 'UTF-8'],
        [true, '{"fòô": "bàř"}', 'UTF-8'],
        [false, '{"fòô":"bàř",}', 'UTF-8'],
        [false, '{"fòô"}', 'UTF-8'],
        [false, '["fòô": "bàř"]', 'UTF-8'],
        [true, '["fòô"]', 'UTF-8'],
        [false, '{"fòô": "bàř"]', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function isLowerCaseProvider(): array
  {
    return [
        [true, ''],
        [true, 'foobar'],
        [false, 'foo bar'],
        [false, 'Foobar'],
        [true, 'fòôbàř', 'UTF-8'],
        [false, 'fòôbàř2', 'UTF-8'],
        [false, 'fòô bàř', 'UTF-8'],
        [false, 'fòôbÀŘ', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function isProvider(): array
  {
    return [
        [true, 'Gears\\String\\Str', 'Gears\\String\\Str'],
        [true, 'Gears\\String\\Str', 'Gears\\*\\Str'],
        [true, 'Gears\\String\\Str', 'Gears\\*\\*'],
        [true, 'Gears\\String\\Str', '*\\*\\*'],
        [true, 'Gears\\String\\Str', '*\\String\\*'],
        [true, 'Gears\\String\\Str', '*\\*\\Str'],
        [true, 'Gears\\String\\Str', '*\\Str'],
        [true, 'Gears\\String\\Str', '*'],
        [true, 'Gears\\String\\Str', '**'],
        [true, 'Gears\\String\\Str', '****'],
        [true, 'Gears\\String\\Str', '*Str'],
        [false, 'Gears\\String\\Str', '*\\'],
        [false, 'Gears\\String\\Str', 'Gears-*-*'],
    ];
  }

  /**
   * @return array
   */
  public function isSerializedProvider(): array
  {
    return [
        [false, ''],
        [true, 'a:1:{s:3:"foo";s:3:"bar";}'],
        [false, 'a:1:{s:3:"foo";s:3:"bar"}'],
        [true, serialize(['foo' => 'bar'])],
        [true, 'a:1:{s:5:"fòô";s:5:"bàř";}', 'UTF-8'],
        [false, 'a:1:{s:5:"fòô";s:5:"bàř"}', 'UTF-8'],
        [true, serialize(['fòô' => 'bár']), 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function isUpperCaseProvider(): array
  {
    return [
        [true, ''],
        [true, 'FOOBAR'],
        [false, 'FOO BAR'],
        [false, 'fOOBAR'],
        [true, 'FÒÔBÀŘ', 'UTF-8'],
        [false, 'FÒÔBÀŘ2', 'UTF-8'],
        [false, 'FÒÔ BÀŘ', 'UTF-8'],
        [false, 'FÒÔBàř', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function lastProvider(): array
  {
    return [
        ['', 'foo bar', -5],
        ['', 'foo bar', 0],
        ['r', 'foo bar', 1],
        ['bar', 'foo bar', 3],
        ['foo bar', 'foo bar', 7],
        ['foo bar', 'foo bar', 8],
        ['', 'fòô bàř', -5, 'UTF-8'],
        ['', 'fòô bàř', 0, 'UTF-8'],
        ['ř', 'fòô bàř', 1, 'UTF-8'],
        ['bàř', 'fòô bàř', 3, 'UTF-8'],
        ['fòô bàř', 'fòô bàř', 7, 'UTF-8'],
        ['fòô bàř', 'fòô bàř', 8, 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function lengthProvider(): array
  {
    return [
        [11, '  foo bar  '],
        [1, 'f'],
        [0, ''],
        [7, 'fòô bàř', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function linesProvider(): array
  {
    return [
        [[], ''],
        [[''], "\r\n"],
        [['foo', 'bar'], "foo\nbar"],
        [['foo', 'bar'], "foo\rbar"],
        [['foo', 'bar'], "foo\r\nbar"],
        [['foo', '', 'bar'], "foo\r\n\r\nbar"],
        [['foo', 'bar', ''], "foo\r\nbar\r\n"],
        [['', 'foo', 'bar'], "\r\nfoo\r\nbar"],
        [['fòô', 'bàř'], "fòô\nbàř", 'UTF-8'],
        [['fòô', 'bàř'], "fòô\rbàř", 'UTF-8'],
        [['fòô', 'bàř'], "fòô\n\rbàř", 'UTF-8'],
        [['fòô', 'bàř'], "fòô\r\nbàř", 'UTF-8'],
        [['fòô', '', 'bàř'], "fòô\r\n\r\nbàř", 'UTF-8'],
        [['fòô', 'bàř', ''], "fòô\r\nbàř\r\n", 'UTF-8'],
        [['', 'fòô', 'bàř'], "\r\nfòô\r\nbàř", 'UTF-8'],
        [['1111111111111111111'], '1111111111111111111', 'UTF-8'],
        [['1111111111111111111111'], '1111111111111111111111', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function longestCommonPrefixProvider(): array
  {
    return [
        ['foo', 'foobar', 'foo bar'],
        ['foo bar', 'foo bar', 'foo bar'],
        ['f', 'foo bar', 'far boo'],
        ['', 'toy car', 'foo bar'],
        ['', 'foo bar', ''],
        ['fòô', 'fòôbar', 'fòô bar', 'UTF-8'],
        ['fòô bar', 'fòô bar', 'fòô bar', 'UTF-8'],
        ['fò', 'fòô bar', 'fòr bar', 'UTF-8'],
        ['', 'toy car', 'fòô bar', 'UTF-8'],
        ['', 'fòô bar', '', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function longestCommonSubstringProvider(): array
  {
    return [
        ['foo', 'foobar', 'foo bar'],
        ['foo bar', 'foo bar', 'foo bar'],
        ['oo ', 'foo bar', 'boo far'],
        ['foo ba', 'foo bad', 'foo bar'],
        ['', 'foo bar', ''],
        ['fòô', 'fòôbàř', 'fòô bàř', 'UTF-8'],
        ['fòô bàř', 'fòô bàř', 'fòô bàř', 'UTF-8'],
        [' bàř', 'fòô bàř', 'fòr bàř', 'UTF-8'],
        [' ', 'toy car', 'fòô bàř', 'UTF-8'],
        ['', 'fòô bàř', '', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function longestCommonSuffixProvider(): array
  {
    return [
        ['bar', 'foobar', 'foo bar'],
        ['foo bar', 'foo bar', 'foo bar'],
        ['ar', 'foo bar', 'boo far'],
        ['', 'foo bad', 'foo bar'],
        ['', 'foo bar', ''],
        ['bàř', 'fòôbàř', 'fòô bàř', 'UTF-8'],
        ['fòô bàř', 'fòô bàř', 'fòô bàř', 'UTF-8'],
        [' bàř', 'fòô bàř', 'fòr bàř', 'UTF-8'],
        ['', 'toy car', 'fòô bàř', 'UTF-8'],
        ['', 'fòô bàř', '', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function lowerCaseFirstProvider(): array
  {
    return [
        ['test', 'Test'],
        ['test', 'test'],
        ['1a', '1a'],
        ['σ test', 'Σ test', 'UTF-8'],
        [' Σ test', ' Σ test', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function offsetExistsProvider(): array
  {
    return [
        [true, 0],
        [true, 2],
        [false, 3],
        [true, -1],
        [true, -3],
        [false, -4],
    ];
  }

  /**
   * @return array
   */
  public function padBothProvider(): array
  {
    return [
        ['foo bar ', 'foo bar', 8],
        [' foo bar ', 'foo bar', 9, ' '],
        ['fòô bàř ', 'fòô bàř', 8, ' ', 'UTF-8'],
        [' fòô bàř ', 'fòô bàř', 9, ' ', 'UTF-8'],
        ['fòô bàř¬', 'fòô bàř', 8, '¬ø', 'UTF-8'],
        ['¬fòô bàř¬', 'fòô bàř', 9, '¬ø', 'UTF-8'],
        ['¬fòô bàř¬ø', 'fòô bàř', 10, '¬ø', 'UTF-8'],
        ['¬øfòô bàř¬ø', 'fòô bàř', 11, '¬ø', 'UTF-8'],
        ['¬fòô bàř¬ø', 'fòô bàř', 10, '¬øÿ', 'UTF-8'],
        ['¬øfòô bàř¬ø', 'fòô bàř', 11, '¬øÿ', 'UTF-8'],
        ['¬øfòô bàř¬øÿ', 'fòô bàř', 12, '¬øÿ', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function padLeftProvider(): array
  {
    return [
        ['  foo bar', 'foo bar', 9],
        ['_*foo bar', 'foo bar', 9, '_*'],
        ['_*_foo bar', 'foo bar', 10, '_*'],
        ['  fòô bàř', 'fòô bàř', 9, ' ', 'UTF-8'],
        ['¬øfòô bàř', 'fòô bàř', 9, '¬ø', 'UTF-8'],
        ['¬ø¬fòô bàř', 'fòô bàř', 10, '¬ø', 'UTF-8'],
        ['¬ø¬øfòô bàř', 'fòô bàř', 11, '¬ø', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function padProvider(): array
  {
    return [
      // length <= str
      ['foo bar', 'foo bar', -1],
      ['foo bar', 'foo bar', 7],
      ['fòô bàř', 'fòô bàř', 7, ' ', 'right', 'UTF-8'],

      // right
      ['foo bar  ', 'foo bar', 9],
      ['foo bar_*', 'foo bar', 9, '_*', 'right'],
      ['fòô bàř¬ø¬', 'fòô bàř', 10, '¬ø', 'right', 'UTF-8'],

      // left
      ['  foo bar', 'foo bar', 9, ' ', 'left'],
      ['_*foo bar', 'foo bar', 9, '_*', 'left'],
      ['¬ø¬fòô bàř', 'fòô bàř', 10, '¬ø', 'left', 'UTF-8'],

      // both
      ['foo bar ', 'foo bar', 8, ' ', 'both'],
      ['¬fòô bàř¬ø', 'fòô bàř', 10, '¬ø', 'both', 'UTF-8'],
      ['¬øfòô bàř¬øÿ', 'fòô bàř', 12, '¬øÿ', 'both', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function padRightProvider(): array
  {
    return [
        ['foo bar  ', 'foo bar', 9],
        ['foo bar_*', 'foo bar', 9, '_*'],
        ['foo bar_*_', 'foo bar', 10, '_*'],
        ['fòô bàř  ', 'fòô bàř', 9, ' ', 'UTF-8'],
        ['fòô bàř¬ø', 'fòô bàř', 9, '¬ø', 'UTF-8'],
        ['fòô bàř¬ø¬', 'fòô bàř', 10, '¬ø', 'UTF-8'],
        ['fòô bàř¬ø¬ø', 'fòô bàř', 11, '¬ø', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function prependProvider(): array
  {
    return [
        ['foobar', 'bar', 'foo'],
        ['fòôbàř', 'bàř', 'fòô', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function regexReplaceProvider(): array
  {
    return [
        ['', '', '', ''],
        ['bar', 'foo', 'f[o]+', 'bar'],
        ['//bar//', '/foo/', '/f[o]+/', '//bar//', 'msr', '#'],
        ['o bar', 'foo bar', 'f(o)o', '\1'],
        ['bar', 'foo bar', 'f[O]+\s', '', 'i'],
        ['foo', 'bar', '[[:alpha:]]{3}', 'foo'],
        ['', '', '', '', 'msr', '/', 'UTF-8'],
        ['bàř', 'fòô ', 'f[òô]+\s', 'bàř', 'msr', '/', 'UTF-8'],
        ['fòô', 'fò', '(ò)', '\\1ô', 'msr', '/', 'UTF-8'],
        ['fòô', 'bàř', '[[:alpha:]]{3}', 'fòô', 'msr', '/', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function removeHtmlBreakProvider(): array
  {
    return [
        ['', ''],
        ['raboof <3', 'raboof <3', '<ä>'],
        ['řàbôòf <foo<lall>>>', 'řàbôòf<br/><foo<lall>>>', ' '],
        [
            'řàb <ô>òf\', ô<br><br/>foo <a href="#">lall</a>',
            'řàb <ô>òf\', ô<br/>foo <a href="#">lall</a>',
            '<br><br/>',
        ],
        ['<∂∆ onerror="alert(xss)">˚åß', '<∂∆ onerror="alert(xss)">' . "\n" . '˚åß'],
        ['\'œ … \'’)', '\'œ … \'’)'],
    ];
  }

  /**
   * @return array
   */
  public function removeHtmlProvider(): array
  {
    return [
        ['', ''],
        ['raboof ', 'raboof <3', '<3>'],
        ['řàbôòf>', 'řàbôòf<foo<lall>>>', '<lall><lall/>'],
        ['řàb òf\', ô<br/>foo lall', 'řàb <ô>òf\', ô<br/>foo <a href="#">lall</a>', '<br><br/>'],
        [' ˚åß', '<∂∆ onerror="alert(xss)"> ˚åß'],
        ['\'œ … \'’)', '\'œ … \'’)'],
    ];
  }

  /**
   * @return array
   */
  public function removeLeftProvider(): array
  {
    return [
        ['foo bar', 'foo bar', ''],
        ['oo bar', 'foo bar', 'f'],
        ['bar', 'foo bar', 'foo '],
        ['foo bar', 'foo bar', 'oo'],
        ['foo bar', 'foo bar', 'oo bar'],
        ['oo bar', 'foo bar', UTF8::first_char('foo bar', 1), 'UTF-8'],
        ['oo bar', 'foo bar', UTF8::char_at('foo bar', 0), 'UTF-8'],
        ['fòô bàř', 'fòô bàř', '', 'UTF-8'],
        ['òô bàř', 'fòô bàř', 'f', 'UTF-8'],
        ['bàř', 'fòô bàř', 'fòô ', 'UTF-8'],
        ['fòô bàř', 'fòô bàř', 'òô', 'UTF-8'],
        ['fòô bàř', 'fòô bàř', 'òô bàř', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function removeRightProvider(): array
  {
    return [
        ['foo bar', 'foo bar', ''],
        ['foo ba', 'foo bar', 'r'],
        ['foo', 'foo bar', ' bar'],
        ['foo bar', 'foo bar', 'ba'],
        ['foo bar', 'foo bar', 'foo ba'],
        ['foo ba', 'foo bar', UTF8::str_last_char('foo bar', 1), 'UTF-8'],
        ['foo ba', 'foo bar', UTF8::char_at('foo bar', 6), 'UTF-8'],
        ['fòô bàř', 'fòô bàř', '', 'UTF-8'],
        ['fòô bà', 'fòô bàř', 'ř', 'UTF-8'],
        ['fòô', 'fòô bàř', ' bàř', 'UTF-8'],
        ['fòô bàř', 'fòô bàř', 'bà', 'UTF-8'],
        ['fòô bàř', 'fòô bàř', 'fòô bà', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function removeXssProvider(): array
  {
    return [
        ['', ''],
        [
            'Hello, i try to alert&#40;\'Hack\'&#41;; your site',
            'Hello, i try to <script>alert(\'Hack\');</script> your site',
        ],
        [
            '<IMG >',
            '<IMG SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29>',
        ],
        ['&lt;XSS &gt;', '<XSS STYLE="behavior: url(xss.htc);">'],
        ['<∂∆ > ˚åß', '<∂∆ onerror="alert(xss)"> ˚åß'],
        ['\'œ … <a href="#foo"> \'’)', '\'œ … <a href="#foo"> \'’)'],
    ];
  }

  /**
   * @return array
   */
  public function repeatProvider(): array
  {
    return [
        ['', 'foo', 0],
        ['foo', 'foo', 1],
        ['foofoo', 'foo', 2],
        ['foofoofoo', 'foo', 3],
        ['fòô', 'fòô', 1, 'UTF-8'],
        ['fòôfòô', 'fòô', 2, 'UTF-8'],
        ['fòôfòôfòô', 'fòô', 3, 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function replaceAllProvider(): array
  {
    return [
        ['', '', [], ''],
        ['', '', [''], ''],
        ['foo', ' ', [' ', ''], 'foo'],
        ['foo', '\s', ['\s', '\t'], 'foo'],
        ['foo bar', 'foo bar', [''], ''],
        ['\1 bar', 'foo bar', ['f(o)o', 'foo'], '\1'],
        ['\1 \1', 'foo bar', ['foo', 'föö', 'bar'], '\1'],
        ['bar', 'foo bar', ['foo '], ''],
        ['far bar', 'foo bar', ['foo'], 'far'],
        ['bar bar', 'foo bar foo bar', ['foo ', ' foo'], ''],
        ['bar bar bar bar', 'foo bar foo bar', ['foo ', ' foo'], ['bar ', ' bar']],
        ['', '', [''], '', 'UTF-8'],
        ['fòô', ' ', [' ', '', '  '], 'fòô', 'UTF-8'],
        ['fòôòô', '\s', ['\s', 'f'], 'fòô', 'UTF-8'],
        ['fòô bàř', 'fòô bàř', [''], '', 'UTF-8'],
        ['bàř', 'fòô bàř', ['fòô '], '', 'UTF-8'],
        ['far bàř', 'fòô bàř', ['fòô'], 'far', 'UTF-8'],
        ['bàř bàř', 'fòô bàř fòô bàř', ['fòô ', 'fòô'], '', 'UTF-8'],
        ['bàř bàř', 'fòô bàř fòô bàř', ['fòô '], ''],
        ['bàř bàř', 'fòô bàř fòô bàř', ['fòô '], ''],
        ['fòô bàř fòô bàř', 'fòô bàř fòô bàř', ['Fòô '], ''],
        ['fòô bàř fòô bàř', 'fòô bàř fòô bàř', ['fòÔ '], ''],
        ['fòô bàř bàř', 'fòô bàř [[fòô]] bàř', ['[[fòô]] ', '[]'], ''],
        ['', '', [''], '', 'UTF-8', false],
        ['fòô', ' ', [' ', '', '  '], 'fòô', 'UTF-8', false],
        ['fòôòô', '\s', ['\s', 'f'], 'fòô', 'UTF-8', false],
        ['fòô bàř', 'fòô bàř', [''], '', 'UTF-8', false],
        ['bàř', 'fòô bàř', ['fòÔ '], '', 'UTF-8', false],
        ['bàř', 'fòô bàř', ['fòÔ '], [''], 'UTF-8', false],
        ['far bàř', 'fòô bàř', ['Fòô'], 'far', 'UTF-8', false],
    ];
  }

  /**
   * @return array
   */
  public function replaceBeginningProvider(): array
  {
    return [
        ['', '', '', ''],
        ['foo', '', '', 'foo'],
        ['foo', '\s', '\s', 'foo'],
        ['foo bar', 'foo bar', '', ''],
        ['foo bar', 'foo bar', 'f(o)o', '\1'],
        ['\1 bar', 'foo bar', 'foo', '\1'],
        ['bar', 'foo bar', 'foo ', ''],
        ['far bar', 'foo bar', 'foo', 'far'],
        ['bar foo bar', 'foo bar foo bar', 'foo ', ''],
        ['', '', '', '', 'UTF-8'],
        ['fòô', '', '', 'fòô', 'UTF-8'],
        ['fòô', '\s', '\s', 'fòô', 'UTF-8'],
        ['fòô bàř', 'fòô bàř', '', '', 'UTF-8'],
        ['bàř', 'fòô bàř', 'fòô ', '', 'UTF-8'],
        ['far bàř', 'fòô bàř', 'fòô', 'far', 'UTF-8'],
        ['bàř fòô bàř', 'fòô bàř fòô bàř', 'fòô ', '', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function replaceEndingProvider(): array
  {
    return [
        ['', '', '', ''],
        ['foo', '', '', 'foo'],
        ['foo', '\s', '\s', 'foo'],
        ['foo bar', 'foo bar', '', ''],
        ['foo bar', 'foo bar', 'f(o)o', '\1'],
        ['foo bar', 'foo bar', 'foo', '\1'],
        ['foo bar', 'foo bar', 'foo ', ''],
        ['foo lall', 'foo bar', 'bar', 'lall'],
        ['foo bar foo ', 'foo bar foo bar', 'bar', ''],
        ['', '', '', '', 'UTF-8'],
        ['fòô', '', '', 'fòô', 'UTF-8'],
        ['fòô', '\s', '\s', 'fòô', 'UTF-8'],
        ['fòô bàř', 'fòô bàř', '', '', 'UTF-8'],
        ['fòô', 'fòô bàř', ' bàř', '', 'UTF-8'],
        ['fòôfar', 'fòô bàř', ' bàř', 'far', 'UTF-8'],
        ['fòô bàř fòô', 'fòô bàř fòô bàř', ' bàř', '', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function replaceProvider(): array
  {
    return [
        ['', '', '', ''],
        ['foo', ' ', ' ', 'foo'],
        ['foo', '\s', '\s', 'foo'],
        ['foo bar', 'foo bar', '', ''],
        ['foo bar', 'foo bar', 'f(o)o', '\1'],
        ['\1 bar', 'foo bar', 'foo', '\1'],
        ['bar', 'foo bar', 'foo ', ''],
        ['far bar', 'foo bar', 'foo', 'far'],
        ['bar bar', 'foo bar foo bar', 'foo ', ''],
        ['', '', '', '', 'UTF-8'],
        ['fòô', ' ', ' ', 'fòô', 'UTF-8'],
        ['fòô', '\s', '\s', 'fòô', 'UTF-8'],
        ['fòô bàř', 'fòô bàř', '', '', 'UTF-8'],
        ['bàř', 'fòô bàř', 'fòô ', '', 'UTF-8'],
        ['far bàř', 'fòô bàř', 'fòô', 'far', 'UTF-8'],
        ['bàř bàř', 'fòô bàř fòô bàř', 'fòô ', '', 'UTF-8'],
        ['bàř bàř', 'fòô bàř fòô bàř', 'fòô ', '',],
        ['bàř bàř', 'fòô bàř fòô bàř', 'fòô ', ''],
        ['fòô bàř fòô bàř', 'fòô bàř fòô bàř', 'Fòô ', ''],
        ['fòô bàř fòô bàř', 'fòô bàř fòô bàř', 'fòÔ ', ''],
        ['fòô bàř bàř', 'fòô bàř [[fòô]] bàř', '[[fòô]] ', ''],
        ['', '', '', '', 'UTF-8', false],
        ['òô', ' ', ' ', 'òô', 'UTF-8', false],
        ['fòô', '\s', '\s', 'fòô', 'UTF-8', false],
        ['fòô bàř', 'fòô bàř', '', '', 'UTF-8', false],
        ['bàř', 'fòô bàř', 'Fòô ', '', 'UTF-8', false],
        ['far bàř', 'fòô bàř', 'fòÔ', 'far', 'UTF-8', false],
        ['bàř bàř', 'fòô bàř fòô bàř', 'Fòô ', '', 'UTF-8', false],
    ];
  }

  /**
   * @return array
   */
  public function reverseProvider(): array
  {
    return [
        ['', ''],
        ['raboof', 'foobar'],
        ['řàbôòf', 'fòôbàř', 'UTF-8'],
        ['řàb ôòf', 'fòô bàř', 'UTF-8'],
        ['∂∆ ˚åß', 'ßå˚ ∆∂', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function safeTruncateProvider(): array
  {
    return [
        ['Test foo bar', 'Test foo bar', 12],
        ['Test foo', 'Test foo bar', 11],
        ['Test foo', 'Test foo bar', 8],
        ['Test', 'Test foo bar', 7],
        ['Test', 'Test foo bar', 4],
        ['Test', 'Testfoobar', 4],
        ['Test foo bar', 'Test foo bar', 12, '...'],
        ['Test foo...', 'Test foo bar', 11, '...'],
        ['Test...', 'Test foo bar', 8, '...'],
        ['Test...', 'Test foo bar', 7, '...'],
        ['...', 'Test foo bar', 4, '...'],
        ['Test....', 'Test foo bar', 11, '....'],
        ['Test fòô bàř', 'Test fòô bàř', 12, '', 'UTF-8'],
        ['Test fòô', 'Test fòô bàř', 11, '', 'UTF-8'],
        ['Test fòô', 'Test fòô bàř', 8, '', 'UTF-8'],
        ['Test', 'Test fòô bàř', 7, '', 'UTF-8'],
        ['Test', 'Test fòô bàř', 4, '', 'UTF-8'],
        ['Test fòô bàř', 'Test fòô bàř', 12, 'ϰϰ', 'UTF-8'],
        ['Test fòôϰϰ', 'Test fòô bàř', 11, 'ϰϰ', 'UTF-8'],
        ['Testϰϰ', 'Test fòô bàř', 8, 'ϰϰ', 'UTF-8'],
        ['Testϰϰ', 'Test fòô bàř', 7, 'ϰϰ', 'UTF-8'],
        ['ϰϰ', 'Test fòô bàř', 4, 'ϰϰ', 'UTF-8'],
        ['What are your plans...', 'What are your plans today?', 22, '...'],
    ];
  }

  /**
   * @return array
   */
  public function shortenAfterWordProvider(): array
  {
    return [
        ['this...', 'this is a test', 5, '...'],
        ['this is...', 'this is öäü-foo test', 8, '...'],
        ['fòô', 'fòô bàř fòô', 6, ''],
        ['fòô bàř', 'fòô bàř fòô', 8, ''],
    ];
  }

  /**
   * @return array
   */
  public function shuffleProvider(): array
  {
    return [
        ['foo bar'],
        ['∂∆ ˚åß', 'UTF-8'],
        ['å´¥©¨ˆßå˚ ∆∂˙©å∑¥øœ¬', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function sliceProvider(): array
  {
    return [
        ['foobar', 'foobar', 0],
        ['foobar', 'foobar', 0, null],
        ['foobar', 'foobar', 0, 6],
        ['fooba', 'foobar', 0, 5],
        ['', 'foobar', 3, 0],
        ['', 'foobar', 3, 2],
        ['ba', 'foobar', 3, 5],
        ['ba', 'foobar', 3, -1],
        ['fòôbàř', 'fòôbàř', 0, null, 'UTF-8'],
        ['fòôbàř', 'fòôbàř', 0, null],
        ['fòôbàř', 'fòôbàř', 0, 6, 'UTF-8'],
        ['fòôbà', 'fòôbàř', 0, 5, 'UTF-8'],
        ['', 'fòôbàř', 3, 0, 'UTF-8'],
        ['', 'fòôbàř', 3, 2, 'UTF-8'],
        ['bà', 'fòôbàř', 3, 5, 'UTF-8'],
        ['bà', 'fòôbàř', 3, -1, 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function slugifyProvider(): array
  {
    return [
        ['foo-bar', ' foo  bar '],
        ['foo-bar', 'foo -.-"-...bar'],
        ['another-und-foo-bar', 'another..& foo -.-"-...bar'],
        ['foo-dbar', " Foo d'Bar "],
        ['a-string-with-dashes', 'A string-with-dashes'],
        ['using-strings-like-foo-bar', 'Using strings like fòô bàř'],
        ['numbers-1234', 'numbers 1234'],
        ['perevirka-ryadka', 'перевірка рядка'],
        ['bukvar-s-bukvoj-y', 'букварь с буквой ы'],
        ['podehal-k-podezdu-moego-doma', 'подъехал к подъезду моего дома'],
        ['foo:bar:baz', 'Foo bar baz', ':'],
        ['a_string_with_underscores', 'A_string with_underscores', '_'],
        ['a_string_with_dashes', 'A string-with-dashes', '_'],
        ['a\string\with\dashes', 'A string-with-dashes', '\\'],
        ['an_odd_string', '--   An odd__   string-_', '_'],
    ];
  }

  /**
   * @return array
   */
  public function snakeizeProvider(): array
  {
    return [
        ['snake_case', 'SnakeCase'],
        ['snake_case', 'Snake-Case'],
        ['snake_case', 'snake case'],
        ['snake_case', 'snake -case'],
        ['snake_case', 'snake - case'],
        ['snake_case', 'snake_case'],
        ['camel_c_test', 'camel c test'],
        ['string_with_1_number', 'string_with 1 number'],
        ['string_with_1_number', 'string_with1number'],
        ['string_with_2_2_numbers', 'string-with-2-2 numbers'],
        ['data_rate', 'data_rate'],
        ['background_color', 'background-color'],
        ['yes_we_can', 'yes_we_can'],
        ['moz_something', '-moz-something'],
        ['car_speed', '_car_speed_'],
        ['serve_h_t_t_p', 'ServeHTTP'],
        ['1_camel_2_case', '1camel2case'],
        ['camel_σase', 'camel σase', 'UTF-8'],
        ['Στανιλ_case', 'Στανιλ case', 'UTF-8'],
        ['σamel_case', 'σamel  Case', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function splitProvider(): array
  {
    return [
        [['foo,bar,baz'], 'foo,bar,baz', ''],
        [['foo,bar,baz'], 'foo,bar,baz', '-'],
        [['foo', 'bar', 'baz'], 'foo,bar,baz', ','],
        [['foo', 'bar', 'baz'], 'foo,bar,baz', ',', -1],
        [[], 'foo,bar,baz', ',', 0],
        [['foo'], 'foo,bar,baz', ',', 1],
        [['foo', 'bar'], 'foo,bar,baz', ',', 2],
        [['foo', 'bar', 'baz'], 'foo,bar,baz', ',', 3],
        [['foo', 'bar', 'baz'], 'foo,bar,baz', ',', 10],
        [['fòô,bàř,baz'], 'fòô,bàř,baz', '-', -1, 'UTF-8'],
        [['fòô', 'bàř', 'baz'], 'fòô,bàř,baz', ',', -1, 'UTF-8'],
        [[], 'fòô,bàř,baz', ',', 0, 'UTF-8'],
        [['fòô'], 'fòô,bàř,baz', ',', 1, 'UTF-8'],
        [['fòô', 'bàř'], 'fòô,bàř,baz', ',', 2, 'UTF-8'],
        [['fòô', 'bàř', 'baz'], 'fòô,bàř,baz', ',', 3, 'UTF-8'],
        [['fòô', 'bàř', 'baz'], 'fòô,bàř,baz', ',', 10, 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function startsWithProvider(): array
  {
    return [
        [true, 'foo bars', 'foo bar'],
        [true, 'FOO bars', 'foo bar', false],
        [true, 'FOO bars', 'foo BAR', false],
        [true, 'FÒÔ bàřs', 'fòô bàř', false, 'UTF-8'],
        [true, 'fòô bàřs', 'fòô BÀŘ', false, 'UTF-8'],
        [false, 'foo bar', 'bar'],
        [false, 'foo bar', 'foo bars'],
        [false, 'FOO bar', 'foo bars'],
        [false, 'FOO bars', 'foo BAR'],
        [false, 'FÒÔ bàřs', 'fòô bàř', true, 'UTF-8'],
        [false, 'fòô bàřs', 'fòô BÀŘ', true, 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function startsWithProviderAny(): array
  {
    return [
        [true, 'foo bars', ['foo bar']],
        [true, 'foo bars', ['foo', 'bar']],
        [true, 'FOO bars', ['foo', 'bar'], false],
        [true, 'FOO bars', ['foo', 'BAR'], false],
        [true, 'FÒÔ bàřs', ['fòô', 'bàř'], false, 'UTF-8'],
        [true, 'fòô bàřs', ['fòô BÀŘ'], false, 'UTF-8'],
        [false, 'foo bar', ['bar']],
        [false, 'foo bar', ['foo bars']],
        [false, 'FOO bar', ['foo bars']],
        [false, 'FOO bars', ['foo BAR']],
        [false, 'FÒÔ bàřs', ['fòô bàř'], true, 'UTF-8'],
        [false, 'fòô bàřs', ['fòô BÀŘ'], true, 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function stripWhitespaceProvider(): array
  {
    return [
        ['foobar', '  foo   bar  '],
        ['teststring', 'test string'],
        ['Οσυγγραφέας', '   Ο     συγγραφέας  '],
        ['123', ' 123 '],
        ['', ' ', 'UTF-8'], // no-break space (U+00A0)
        ['', '           ', 'UTF-8'], // spaces U+2000 to U+200A
        ['', ' ', 'UTF-8'], // narrow no-break space (U+202F)
        ['', ' ', 'UTF-8'], // medium mathematical space (U+205F)
        ['', '　', 'UTF-8'], // ideographic space (U+3000)
        ['123', '  1  2  3　　', 'UTF-8'],
        ['', ' '],
        ['', ''],
    ];
  }

  /**
   * @return array
   */
  public function substrProvider(): array
  {
    return [
        ['foo bar', 'foo bar', 0],
        ['bar', 'foo bar', 4],
        ['bar', 'foo bar', 4, null],
        ['o b', 'foo bar', 2, 3],
        ['', 'foo bar', 4, 0],
        ['fòô bàř', 'fòô bàř', 0, null, 'UTF-8'],
        ['bàř', 'fòô bàř', 4, null, 'UTF-8'],
        ['ô b', 'fòô bàř', 2, 3, 'UTF-8'],
        ['', 'fòô bàř', 4, 0, 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function surroundProvider(): array
  {
    return [
        ['__foobar__', 'foobar', '__'],
        ['test', 'test', ''],
        ['**', '', '*'],
        ['¬fòô bàř¬', 'fòô bàř', '¬'],
        ['ßå∆˚ test ßå∆˚', ' test ', 'ßå∆˚'],
    ];
  }

  /**
   * @return array
   */
  public function swapCaseProvider(): array
  {
    return [
        ['TESTcASE', 'testCase'],
        ['tEST-cASE', 'Test-Case'],
        [' - σASH  cASE', ' - Σash  Case', 'UTF-8'],
        ['νΤΑΝΙΛ', 'Ντανιλ', 'UTF-8'],
    ];
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

      self::assertSame($testResult[1], UTF8::strlen($tmpString), 'tested: ' . $testString . ' | ' . $tmpString);
    }
  }

  public function testAddUniqueIdentifier()
  {
    $uniquIDs = [];
    for ($i = 0; $i <= 100; $i++) {
      $uniquIDs[] = UTF8::get_unique_string();
    }

    // detect duplicate values in the array
    foreach (array_count_values($uniquIDs) as $uniquID => $count) {
      self::assertSame(1, $count);
    }

    // check the string length
    foreach ($uniquIDs as $uniquID) {
      self::assertSame(32, strlen($uniquID));
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
        'κόσμbε ¡-öäü'             => 'ε ¡-öäü',
        'bκόσμbε'                  => 'κόσμbε',
    ];

    foreach ($testArray as $testString => $testResult) {
      self::assertSame($testResult, UTF8::str_substr_after_first_separator($testString, 'b'));
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
        'κόσμbε ¡-öäü'             => 'ε ¡-öäü',
        'bκόσμbε'                  => 'κόσμbε',
    ];

    foreach ($testArray as $testString => $testResult) {
      self::assertSame($testResult, UTF8::str_isubstr_after_first_separator($testString, 'b'));
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
        'κόσμbε ¡-öäü'             => 'ε ¡-öäü',
    ];

    foreach ($testArray as $testString => $testResult) {
      self::assertSame($testResult, UTF8::str_isubstr_after_last_separator($testString, 'b'));
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
        'κόσμbε ¡-öäü'             => 'ε ¡-öäü',
    ];

    foreach ($testArray as $testString => $testResult) {
      self::assertSame($testResult, UTF8::str_substr_after_last_separator($testString, 'b'));
    }
  }

  /**
   * @dataProvider atProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $index
   * @param      $encoding
   */
  public function testAt($expected, $str, $index, $encoding = '')
  {
    $result = UTF8::char_at($str, $index, $encoding);

    self::assertSame($expected, $result);
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
        'κόσμbε ¡-öäü'             => 'κόσμ',
    ];

    foreach ($testArray as $testString => $testResult) {
      self::assertSame($testResult, UTF8::str_substr_before_first_separator($testString, 'b'));
      self::assertSame($testResult, UTF8::str_substr_first($testString, 'b', true));
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
        'κόσμbε ¡-öäü'             => 'κόσμ',
        'Bκόσμbε'                  => '',
    ];

    foreach ($testArray as $testString => $testResult) {
      self::assertSame($testResult, UTF8::str_isubstr_before_first_separator($testString, 'b'));
      self::assertSame($testResult, UTF8::str_isubstr_first($testString, 'b', true));
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
        'κόσμbε ¡-öäü'             => 'κόσμ',
    ];

    foreach ($testArray as $testString => $testResult) {
      self::assertSame($testResult, UTF8::str_substr_before_last_separator($testString, 'b'));
      self::assertSame($testResult, UTF8::str_substr_last($testString, 'b', true));
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
        'κόσμbε ¡-öäü'             => 'κόσμ',
        'bκόσμbε'                  => 'bκόσμ',
    ];

    foreach ($testArray as $testString => $testResult) {
      self::assertSame($testResult, UTF8::str_isubstr_before_last_separator($testString, 'b'));
    }
  }

  /**
   * @dataProvider betweenProvider()
   *
   * @param          $expected
   * @param          $str
   * @param          $start
   * @param          $end
   * @param null|int $offset
   * @param null     $encoding
   */
  public function testBetween($expected, $str, $start, $end, $offset = 0, $encoding = '')
  {
    $result = UTF8::between($str, $start, $end, $offset, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider camelizeProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $encoding
   */
  public function testCamelize($expected, $str, $encoding = '')
  {
    $result = UTF8::str_camelize($str, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider charsProvider()
   *
   * @param      $expected
   * @param      $str
   */
  public function testChars($expected, $str)
  {
    $result = UTF8::chars($str);
    self::assertInternalType('array', $result);
    foreach ($result as $char) {
      self::assertInternalType('string', $char);
    }
    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider collapseWhitespaceProvider()
   *
   * @param      $expected
   * @param      $str
   */
  public function testCollapseWhitespace($expected, $str)
  {
    $result = UTF8::collapse_whitespace($str);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider containsProvider()
   *
   * @param      $expected
   * @param      $haystack
   * @param      $needle
   * @param bool $caseSensitive
   * @param      $encoding
   */
  public function testContains($expected, $haystack, $needle, $caseSensitive = true, $encoding = '')
  {
    $result = UTF8::str_contains($haystack, $needle, $caseSensitive, $encoding);
    self::assertInternalType('boolean', $result);
    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider containsAllProvider()
   *
   * @param bool     $expected
   * @param string   $haystack
   * @param string[] $needles
   * @param bool     $caseSensitive
   * @param string   $encoding
   */
  public function testContainsAll($expected, $haystack, $needles, $caseSensitive = true, $encoding = '')
  {
    $result = UTF8::str_contains_all($haystack, $needles, $caseSensitive, $encoding);
    self::assertInternalType('boolean', $result);
    self::assertSame($expected, $result, 'tested: ' . $haystack);
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
    self::assertSame($expected, $result, 'tested:' . $str);
  }

  /**
   * @dataProvider dasherizeProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $encoding
   */
  public function testDasherize($expected, $str, $encoding = '')
  {
    $result = UTF8::str_dasherize($str, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider delimitProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $delimiter
   * @param      $encoding
   */
  public function testDelimit($expected, $str, $delimiter, $encoding = '')
  {
    $result = UTF8::str_delimit($str, $delimiter, $encoding);

    self::assertSame($expected, $result);
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
    if ($caseSensitive === true) {
      $result = UTF8::str_ends_with($str, $substring);
    } else {
      $result = UTF8::str_iends_with($str, $substring);
    }

    self::assertInternalType('boolean', $result);
    self::assertSame($expected, $result);
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
    if ($caseSensitive === true) {
      $result = UTF8::str_ends_with_any($str, $substrings);
    } else {
      $result = UTF8::str_iends_with_any($str, $substrings);
    }

    self::assertInternalType('boolean', $result);
    self::assertEquals($expected, $result);
  }

  /**
   * @dataProvider ensureLeftProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $substring
   */
  public function testEnsureLeft($expected, $str, $substring)
  {
    $result = UTF8::str_ensure_left($str, $substring);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider ensureRightProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $substring
   */
  public function testEnsureRight($expected, $str, $substring)
  {
    $result = UTF8::str_ensure_right($str, $substring);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider escapeProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $encoding
   */
  public function testEscape($expected, $str, $encoding = '')
  {
    $result = UTF8::html_escape($str, $encoding);

    self::assertSame($expected, $result);
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
      self::assertSame($testExpected, UTF8::extract_text($testString, 'foobar'), 'tested: ' . $testString);
    }

    // ----------------

    $testString = 'this is only a Fork of UTF8';
    self::assertSame('…a Fork of UTF8', UTF8::extract_text($testString, 'Fork', 5), 'tested: ' . $testString);

    // ----------------

    $testString = 'This is only a Fork of UTF8, take a look at the new features.';
    self::assertSame('…Fork of UTF8…', UTF8::extract_text($testString, 'UTF8', 15), 'tested: ' . $testString);

    // ----------------

    $testString = 'This is only a Fork of UTF8, take a look at the new features.';
    self::assertSame('…only a Fork of UTF8, take a look…', UTF8::extract_text($testString, 'UTF8'), 'tested: ' . $testString);

    // ----------------

    $testString = 'This is only a Fork of UTF8, take a look at the new features.';
    self::assertSame('This is only a Fork of UTF8, take…', UTF8::extract_text($testString), 'tested: ' . $testString);

    // ----------------

    $testString = 'This is only a Fork of UTF8, take a look at the new features.';
    self::assertSame('This…', UTF8::extract_text($testString, '', 0), 'tested: ' . $testString);

    // ----------------

    $testString = 'This is only a Fork of UTF8, take a look at the new features.';
    self::assertSame('…UTF8, take a look at the new features.', UTF8::extract_text($testString, 'UTF8', 0), 'tested: ' . $testString);

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

      self::assertSame($testExpected, $result, 'tested: ' . $testString);
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

      self::assertSame($testExpected, $result, 'tested: ' . $testString);
    }
  }

  /**
   * @dataProvider firstProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $n
   * @param      $encoding
   */
  public function testFirst($expected, $str, $n, $encoding = '')
  {
    $result = UTF8::first_char($str, $n, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider hasLowerCaseProvider()
   *
   * @param      $expected
   * @param      $str
   */
  public function testHasLowerCase($expected, $str)
  {
    $result = UTF8::has_lowercase($str);

    self::assertInternalType('boolean', $result);
    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider hasUpperCaseProvider()
   *
   * @param      $expected
   * @param      $str
   */
  public function testHasUpperCase($expected, $str)
  {
    $result = UTF8::has_uppercase($str);

    self::assertInternalType('boolean', $result);
    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider htmlDecodeProvider()
   *
   * @param      $expected
   * @param      $str
   * @param int  $flags
   * @param      $encoding
   */
  public function testHtmlDecode($expected, $str, $flags = ENT_COMPAT, $encoding = '')
  {
    $result = UTF8::html_entity_decode($str, $flags, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider htmlEncodeProvider()
   *
   * @param      $expected
   * @param      $str
   * @param int  $flags
   * @param      $encoding
   */
  public function testHtmlEncode($expected, $str, $flags = ENT_COMPAT, $encoding = '')
  {
    $result = UTF8::htmlentities($str, $flags, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider humanizeProvider()
   *
   * @param      $expected
   * @param      $str
   */
  public function testHumanize($expected, $str)
  {
    $result = UTF8::str_humanize($str);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider indexOfProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $subStr
   * @param int  $offset
   * @param      $encoding
   */
  public function testIndexOf($expected, $str, $subStr, $offset = 0, $encoding = '')
  {
    $result = UTF8::str_index_first($str, $subStr, $offset, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider indexOfLastProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $subStr
   * @param int  $offset
   * @param      $encoding
   */
  public function testIndexOfLast($expected, $str, $subStr, $offset = 0, $encoding = '')
  {
    $result = UTF8::str_index_last($str, $subStr, $offset, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider insertProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $substring
   * @param      $index
   * @param      $encoding
   */
  public function testInsert($expected, $str, $substring, $index, $encoding = '')
  {
    $result = UTF8::str_insert($str, $substring, $index, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider isAlphaProvider()
   *
   * @param      $expected
   * @param      $str
   */
  public function testIsAlpha($expected, $str)
  {
    $result = UTF8::is_alpha($str);

    self::assertInternalType('boolean', $result);
    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider isAlphanumericProvider()
   *
   * @param      $expected
   * @param      $str
   */
  public function testIsAlphanumeric($expected, $str)
  {
    $result = UTF8::is_alphanumeric($str);

    self::assertInternalType('boolean', $result);
    self::assertSame($expected, $result);
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

    self::assertInternalType('boolean', $result);
    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider isBlankProvider()
   *
   * @param      $expected
   * @param      $str
   */
  public function testIsBlank($expected, $str)
  {
    $result = UTF8::is_blank($str);

    self::assertInternalType('boolean', $result);
    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider isHexadecimalProvider()
   *
   * @param      $expected
   * @param      $str
   */
  public function testIsHexadecimal($expected, $str)
  {
    $result = UTF8::is_hexadecimal($str);

    self::assertInternalType('boolean', $result);
    self::assertSame($expected, $result);
  }

  public function testIsHtml()
  {
    $testArray = [
        ''                         => false,
        '<h1>test</h1>'            => true,
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
      self::assertSame($testResult, UTF8::is_html($testString), 'tested: ' . $testString);
    }
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

    self::assertInternalType('boolean', $result);
    self::assertSame($expected, $result, 'tested: ' . $str);
  }

  /**
   * @dataProvider isLowerCaseProvider()
   *
   * @param      $expected
   * @param      $str
   */
  public function testIsLowerCase($expected, $str)
  {
    $result = UTF8::is_lowercase($str);

    self::assertInternalType('boolean', $result);
    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider isSerializedProvider()
   *
   * @param      $expected
   * @param      $str
   */
  public function testIsSerialized($expected, $str)
  {
    $result = UTF8::is_serialized($str);

    self::assertInternalType('boolean', $result);
    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider isUpperCaseProvider()
   *
   * @param      $expected
   * @param      $str
   */
  public function testIsUpperCase($expected, $str)
  {
    $result = UTF8::is_uppercase($str);

    self::assertInternalType('boolean', $result);
    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider lastProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $n
   * @param      $encoding
   */
  public function testLast($expected, $str, $n, $encoding = '')
  {
    $result = UTF8::str_last_char($str, $n, $encoding);

    self::assertSame($expected, $result);
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
        'κόσμbε ¡-öäü'             => 'bε ¡-öäü',
    ];

    foreach ($testArray as $testString => $testResult) {
      self::assertSame($testResult, UTF8::str_substr_last($testString, 'b', false));
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
        'κόσμbε ¡-öäü'             => 'bε ¡-öäü',
    ];

    foreach ($testArray as $testString => $testResult) {
      self::assertSame($testResult, UTF8::str_isubstr_last($testString, 'b', false));
    }
  }

  /**
   * @dataProvider lengthProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $encoding
   */
  public function testLength($expected, $str, $encoding = '')
  {
    $result = UTF8::strlen($str, $encoding);

    self::assertInternalType('int', $result);
    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider linesProvider()
   *
   * @param      $expected
   * @param      $str
   */
  public function testLines($expected, $str)
  {
    $result = UTF8::str_to_lines($str);

    self::assertInternalType('array', $result);
    foreach ($result as $line) {
      self::assertUtf8String($line);
    }

    $counter = count($expected);
    /** @noinspection ForeachInvariantsInspection */
    for ($i = 0; $i < $counter; $i++) {
      self::assertSame($expected[$i], $result[$i]);
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
      self::assertSame($testResult, UTF8::wordwrap_per_line($testString, 10));
    }
  }

  /**
   * @dataProvider longestCommonPrefixProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $otherStr
   * @param      $encoding
   */
  public function testLongestCommonPrefix($expected, $str, $otherStr, $encoding = '')
  {
    $result = UTF8::str_longest_common_prefix($str, $otherStr, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider longestCommonSubstringProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $otherStr
   * @param      $encoding
   */
  public function testLongestCommonSubstring($expected, $str, $otherStr, $encoding = '')
  {
    $result = UTF8::str_longest_common_substring($str, $otherStr, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider longestCommonSuffixProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $otherStr
   * @param      $encoding
   */
  public function testLongestCommonSuffix($expected, $str, $otherStr, $encoding = '')
  {
    $result = UTF8::str_longest_common_suffix($str, $otherStr, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider lowerCaseFirstProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $encoding
   */
  public function testLowerCaseFirst($expected, $str, $encoding = '')
  {
    $result = UTF8::lowerCaseFirst($str, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider offsetExistsProvider()
   *
   * @param $expected
   * @param $offset
   */
  public function testOffsetExists($expected, $offset)
  {
    self::assertSame($expected, UTF8::str_offset_exists('fòô', $offset));
  }

  public function testOffsetGet()
  {
    self::assertSame('f', UTF8::str_offset_get('fòô', 0));
    self::assertSame('ô', UTF8::str_offset_get('fòô', 2));
  }

  /**
   * @expectedException \OutOfBoundsException
   */
  public function testOffsetGetOutOfBoundsException()
  {
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

    self::assertSame($expected, $result);
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

    self::assertSame($expected, $result);
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testPadException()
  {
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

    self::assertSame($expected, $result);
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

    self::assertSame($expected, $result);
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
    $result = UTF8::str_remove_html($str, $allowableTags);

    self::assertSame($expected, $result);
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
    $result = UTF8::str_remove_html_breaks($str, $replacement);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider removeLeftProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $substring
   * @param      $encoding
   */
  public function testRemoveLeft($expected, $str, $substring, $encoding = '')
  {
    $result = UTF8::str_remove_left($str, $substring, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider removeRightProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $substring
   * @param      $encoding
   */
  public function testRemoveRight($expected, $str, $substring, $encoding = '')
  {
    $result = UTF8::str_remove_right($str, $substring, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider repeatProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $multiplier
   */
  public function testRepeat($expected, $str, $multiplier)
  {
    $result = UTF8::str_repeat($str, $multiplier);

    self::assertSame($expected, $result);
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

    self::assertSame($expected, $result);
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

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider replaceBeginningProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $search
   * @param      $replacement
   */
  public function testReplaceBeginning($expected, $str, $search, $replacement)
  {
    $result = UTF8::str_replace_beginning($str, $search, $replacement);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider replaceEndingProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $search
   * @param      $replacement
   */
  public function testReplaceEnding($expected, $str, $search, $replacement)
  {
    $result = UTF8::str_replace_ending($str, $search, $replacement);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider reverseProvider()
   *
   * @param      $expected
   * @param      $str
   */
  public function testReverse($expected, $str)
  {
    $result = UTF8::strrev($str);

    self::assertSame($expected, $result);
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

    self::assertSame($expected, $result, 'tested: ' . $str . ' | ' . $substring . ' (' . $length . ')');
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

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider shuffleProvider()
   *
   * @param      $str
   * @param      $encoding
   */
  public function testShuffle($str, $encoding = '')
  {
    $encoding = $encoding ?: mb_internal_encoding();
    $result = UTF8::str_shuffle($str);

    self::assertSame(
        UTF8::strlen($str, $encoding),
        UTF8::strlen($result, $encoding)
    );

    // We'll make sure that the chars are present after shuffle
    $length = UTF8::strlen($str, $encoding);
    for ($i = 0; $i < $length; $i++) {
      $char = UTF8::substr($str, $i, 1, $encoding);
      $countBefore = UTF8::substr_count($str, $char, 0, null, $encoding);
      $countAfter = UTF8::substr_count($result, $char, 0, null,  $encoding);
      self::assertSame($countBefore, $countAfter);
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

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider snakeizeProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $encoding
   */
  public function testSnakeize($expected, $str, $encoding = '')
  {
    $result = UTF8::str_snakeize($str, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider splitProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $pattern
   * @param int  $limit
   */
  public function testSplit($expected, $str, $pattern, $limit = -1)
  {
    $result = UTF8::str_split_pattern($str, $pattern, $limit);

    self::assertInternalType('array', $result);
    foreach ($result as $string) {
      self::assertUtf8String($string);
    }

    $counter = count($expected);
    /** @noinspection ForeachInvariantsInspection */
    for ($i = 0; $i < $counter; $i++) {
      self::assertSame($expected[$i], $result[$i]);
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

    self::assertInternalType('boolean', $result);
    self::assertSame($expected, $result);
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

    self::assertInternalType('boolean', $result);
    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider stripWhitespaceProvider()
   *
   * @param      $expected
   * @param      $str
   */
  public function testStripWhitespace($expected, $str)
  {
    $result = UTF8::strip_whitespace($str);

    self::assertEquals($expected, $result);
  }

  public function testStripeEmptyTags()
  {
    $testArray = [
        ''                         => '',
        '<h1>test</h1>'            => '<h1>test</h1>',
        'foo<h1></h1>bar'          => 'foobar',
        '<h1></h1> '               => ' ',
        '</b></b>'                 => '</b></b>',
        'öäü<strong>lall</strong>' => 'öäü<strong>lall</strong>',
        ' b<b></b>'                => ' b',
        '<b><b>lall</b>'           => '<b><b>lall</b>',
        '</b>lall</b>'             => '</b>lall</b>',
        '[b][/b]'                  => '[b][/b]',
    ];

    foreach ($testArray as $testString => $testResult) {
      self::assertSame($testResult, UTF8::html_stripe_empty_tags($testString));
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
      self::assertSame($testResult, UTF8::css_stripe_media_queries($testString));
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
        'κόσμbε ¡-öäü'             => 'bε ¡-öäü',
        'bκόσμbε'                  => 'bκόσμbε',
    ];

    foreach ($testArray as $testString => $testResult) {
      self::assertSame($testResult, UTF8::str_substr_first($testString, 'b', false));
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

    self::assertSame($expected, $result);
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
        'κόσμbε ¡-öäü'             => 'bε ¡-öäü',
        'bκόσμbε'                  => 'bκόσμbε',
    ];

    foreach ($testArray as $testString => $testResult) {
      self::assertSame($testResult, UTF8::str_isubstr_first($testString, 'b', false));
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

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider swapCaseProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $encoding
   */
  public function testSwapCase($expected, $str, $encoding = '')
  {
    $result = UTF8::swapCase($str, $encoding);

    self::assertSame($expected, $result);
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

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider titleizeProvider()
   *
   * @param      $expected
   * @param      $str
   * @param null $ignore
   * @param      $encoding
   */
  public function testTitleize($expected, $str, $ignore = null, $encoding = '')
  {
    $result = UTF8::str_titleize($str, $ignore, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider titleizeForHumansProvider()
   *
   * @param string      $expected
   * @param string      $str
   * @param array       $ignore
   * @param null|string $encoding
   */
  public function testTitleizeForHumans($str, $expected, $ignore = [], $encoding = '')
  {
    $result = UTF8::str_titleize_for_humans($str, $ignore, $encoding);

    self::assertSame($expected, $result);
  }

  public function titleizeForHumansProvider()
  {
    return [
        ['TITLE CASE', 'Title Case'],
        ['testing the method', 'Testing the Method'],
        ['i like to watch DVDs at home', 'I Like to watch DVDs at Home', ['watch']],
        ['  Θα ήθελα να φύγει  ', 'Θα Ήθελα Να Φύγει', [], 'UTF-8'],
        ['For step-by-step directions email someone@gmail.com', 'For Step-by-Step Directions Email someone@gmail.com'],
        ["2lmc Spool: 'Gruber on OmniFocus and Vapo(u)rware'", "2lmc Spool: 'Gruber on OmniFocus and Vapo(u)rware'"],
        ['Have you read “The Lottery”?', 'Have You Read “The Lottery”?'],
        ['your hair[cut] looks (nice)', 'Your Hair[cut] Looks (Nice)'],
        [
            "People probably won't put http://foo.com/bar/ in titles",
            "People Probably Won't Put http://foo.com/bar/ in Titles",
        ],
        [
            'Scott Moritz and TheStreet.com’s million iPhone la‑la land',
            'Scott Moritz and TheStreet.com’s Million iPhone La‑La Land',
        ],
        ['BlackBerry vs. iPhone', 'BlackBerry vs. iPhone'],
        [
            'Notes and observations regarding Apple’s announcements from ‘The Beat Goes On’ special event',
            'Notes and Observations Regarding Apple’s Announcements From ‘The Beat Goes On’ Special Event',
        ],
        [
            'Read markdown_rules.txt to find out how _underscores around words_ will be interpretted',
            'Read markdown_rules.txt to Find Out How _Underscores Around Words_ Will Be Interpretted',
        ],
        [
            "Q&A with Steve Jobs: 'That's what happens in technology'",
            "Q&A With Steve Jobs: 'That's What Happens in Technology'",
        ],
        ["What is AT&T's problem?", "What Is AT&T's Problem?"],
        ['Apple deal with AT&T falls through', 'Apple Deal With AT&T Falls Through'],
        ['this v that', 'This v That'],
        ['this vs that', 'This vs That'],
        ['this v. that', 'This v. That'],
        ['this vs. that', 'This vs. That'],
        ["The SEC's Apple probe: what you need to know", "The SEC's Apple Probe: What You Need to Know"],
        [
            "'by the way, small word at the start but within quotes.'",
            "'By the Way, Small Word at the Start but Within Quotes.'",
        ],
        ['Small word at end is nothing to be afraid of', 'Small Word at End Is Nothing to Be Afraid Of'],
        [
            'Starting sub-phrase with a small word: a trick, perhaps?',
            'Starting Sub-Phrase With a Small Word: A Trick, Perhaps?',
        ],
        [
            "Sub-phrase with a small word in quotes: 'a trick, perhaps?'",
            "Sub-Phrase With a Small Word in Quotes: 'A Trick, Perhaps?'",
        ],
        [
            'Sub-phrase with a small word in quotes: "a trick, perhaps?"',
            'Sub-Phrase With a Small Word in Quotes: "A Trick, Perhaps?"',
        ],
        ['"Nothing to Be Afraid of?"', '"Nothing to Be Afraid Of?"'],
        ['a thing', 'A Thing'],
        [
            'Dr. Strangelove (or: how I Learned to Stop Worrying and Love the Bomb)',
            'Dr. Strangelove (Or: How I Learned to Stop Worrying and Love the Bomb)',
        ],
        ['  this is trimming', 'This Is Trimming'],
        ['this is trimming  ', 'This Is Trimming'],
        ['  this is trimming  ', 'This Is Trimming'],
        ['IF IT’S ALL CAPS, FIX IT', 'If It’s All Caps, Fix It'],
        ['What could/should be done about slashes?', 'What Could/Should Be Done About Slashes?'],
        ['Never touch paths like /var/run before/after /boot', 'Never Touch Paths Like /var/run Before/After /boot'],
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

    self::assertSame($expected, $result, 'tested:' . $str);
  }

  /**
   * @dataProvider toBooleanProvider()
   *
   * @param      $expected
   * @param      $str
   */
  public function testToBoolean($expected, $str)
  {
    $result = UTF8::to_boolean($str);

    self::assertInternalType('boolean', $result);
    self::assertSame($expected, $result, 'tested: ' . $str);
  }

  /**
   * @dataProvider toLowerCaseProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $encoding
   */
  public function testToLowerCase($expected, $str, $encoding = '')
  {
    $result = UTF8::strtolower($str, $encoding);

    self::assertSame($expected, $result);
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

    self::assertSame($expected, $result);
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

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider toTitleCaseProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $encoding
   */
  public function testToTitleCase($expected, $str, $encoding = '')
  {
    $result = UTF8::titlecase($str, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider toUpperCaseProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $encoding
   */
  public function testToUpperCase($expected, $str, $encoding = '')
  {
    $result = UTF8::strtoupper($str, $encoding);

    self::assertSame($expected, $result);
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

    self::assertSame($expected, $result);
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

    self::assertSame($expected, $result);
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

    self::assertSame($expected, $result);
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

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider underscoredProvider()
   *
   * @param      $expected
   * @param      $str
   */
  public function testUnderscored($expected, $str)
  {
    $result = UTF8::str_underscored($str);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider upperCamelizeProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $encoding
   */
  public function testUpperCamelize($expected, $str, $encoding = '')
  {
    $result = UTF8::str_upper_camelize($str, $encoding);

    self::assertSame($expected, $result);
  }

  /**
   * @dataProvider upperCaseFirstProvider()
   *
   * @param      $expected
   * @param      $str
   * @param      $encoding
   */
  public function testUpperCaseFirst($expected, $str, $encoding = '')
  {
    $result = UTF8::str_upper_first($str, $encoding);

    self::assertSame($expected, $result);
  }

  public function testUtf8ify()
  {
    $examples = [
        ''                                     => [''],
        // Valid UTF-8 + UTF-8 NO-BREAK SPACE
        "κόσμε\xc2\xa0"                        => ['κόσμε' . "\xc2\xa0" => 'κόσμε' . "\xc2\xa0"],
        // Valid UTF-8
        '中'                                    => ['中' => '中'],
        // Valid UTF-8 + ISO-Error
        'DÃ¼sseldorf'                          => ['Düsseldorf' => 'Düsseldorf'],
        // Valid UTF-8 + Invalid Chars
        "κόσμε\xa0\xa1-öäü"                    => ['κόσμε-öäü' => 'κόσμε-öäü'],
        // Valid ASCII
        'a'                                    => ['a' => 'a'],
        // Valid ASCII + Invalid Chars
        "a\xa0\xa1-öäü"                        => ['a-öäü' => 'a-öäü'],
        // Valid 2 Octet Sequence
        "\xc3\xb1"                             => ['ñ' => 'ñ'],
        // Invalid 2 Octet Sequence
        "\xc3\x28"                             => ['�(' => '('],
        // Invalid Sequence Identifier
        "\xa0\xa1"                             => ['��' => ''],
        // Valid 3 Octet Sequence
        "\xe2\x82\xa1"                         => ['₡' => '₡'],
        // Invalid 3 Octet Sequence (in 2nd Octet)
        "\xe2\x28\xa1"                         => ['�(�' => '('],
        // Invalid 3 Octet Sequence (in 3rd Octet)
        "\xe2\x82\x28"                         => ['�(' => '('],
        // Valid 4 Octet Sequence
        "\xf0\x90\x8c\xbc"                     => ['𐌼' => '𐌼'],
        // Invalid 4 Octet Sequence (in 2nd Octet)
        "\xf0\x28\x8c\xbc"                     => ['�(��' => '('],
        // Invalid 4 Octet Sequence (in 3rd Octet)
        "\xf0\x90\x28\xbc"                     => ['�(�' => '('],
        // Invalid 4 Octet Sequence (in 4th Octet)
        " \xf0\x28\x8c\x28"                    => ['�(�(' => ' (('],
        // Valid 5 Octet Sequence (but not Unicode!)
        "\xf8\xa1\xa1\xa1\xa1"                 => ['�' => ''],
        // Valid 6 Octet Sequence (but not Unicode!) + UTF-8 EN SPACE
        "\xfc\xa1\xa1\xa1\xa1\xa1\xe2\x80\x82" => ['�' => ' '],
        // test for database-insert
        '
        <h1>«DÃ¼sseldorf» &ndash; &lt;Köln&gt;</h1>
        <br /><br />
        <p>
          &nbsp;�&foo;❤&nbsp;
        </p>
        '                              => [
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
        self::assertSame($after, UTF8::cleanup($testString), 'tested: ' . $before . ' (' . print_r($testResults, true) . ')');
      }
    }

    $examples = [
      // Valid UTF-8
      'κόσμε'                    => ['κόσμε' => 'κόσμε'],
      '中'                        => ['中' => '中'],
      '«foobar»'                 => ['«foobar»' => '«foobar»'],
      // Valid UTF-8 + Invalied Chars
      "κόσμε\xa0\xa1-öäü"        => ['κόσμε-öäü' => 'κόσμε-öäü'],
      // Valid ASCII
      'a'                        => ['a' => 'a'],
      // Valid emoji (non-UTF-8)
      '😃'                       => ['😃' => '😃'],
      // Valid ASCII + Invalied Chars
      "a\xa0\xa1-öäü"            => ['a-öäü' => 'a-öäü'],
      // Valid 2 Octet Sequence
      "\xc3\xb1"                 => ['ñ' => 'ñ'],
      // Invalid 2 Octet Sequence
      "\xc3\x28"                 => ['�(' => '('],
      // Invalid Sequence Identifier
      "\xa0\xa1"                 => ['��' => ''],
      // Valid 3 Octet Sequence
      "\xe2\x82\xa1"             => ['₡' => '₡'],
      // Invalid 3 Octet Sequence (in 2nd Octet)
      "\xe2\x28\xa1"             => ['�(�' => '('],
      // Invalid 3 Octet Sequence (in 3rd Octet)
      "\xe2\x82\x28"             => ['�(' => '('],
      // Valid 4 Octet Sequence
      "\xf0\x90\x8c\xbc"         => ['𐌼' => '𐌼'],
      // Invalid 4 Octet Sequence (in 2nd Octet)
      "\xf0\x28\x8c\xbc"         => ['�(��' => '('],
      // Invalid 4 Octet Sequence (in 3rd Octet)
      "\xf0\x90\x28\xbc"         => ['�(�' => '('],
      // Invalid 4 Octet Sequence (in 4th Octet)
      "\xf0\x28\x8c\x28"         => ['�(�(' => '(('],
      // Valid 5 Octet Sequence (but not Unicode!)
      "\xf8\xa1\xa1\xa1\xa1"     => ['�' => ''],
      // Valid 6 Octet Sequence (but not Unicode!)
      "\xfc\xa1\xa1\xa1\xa1\xa1" => ['�' => ''],
    ];

    $counter = 0;
    foreach ($examples as $testString => $testResults) {
      foreach ($testResults as $before => $after) {
        self::assertSame($after, UTF8::cleanup($before), $counter);
      }
      $counter++;
    }
  }

  /**
   * @dataProvider containsAnyProvider()
   *
   * @param      $expected
   * @param      $haystack
   * @param      $needles
   * @param bool $caseSensitive
   * @param      $encoding
   */
  public function testcontainsAny($expected, $haystack, $needles, $caseSensitive = true, $encoding = '')
  {
    $result = UTF8::str_contains_any($haystack, $needles, $caseSensitive, $encoding);

    self::assertInternalType('boolean', $result);
    self::assertSame($expected, $result);
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
  public function testregexReplace($expected, $str, $pattern, $replacement, $options = 'msr', $delimiter = '/', $encoding = '')
  {
    $result = UTF8::regexReplace($str, $pattern, $replacement, $options, $delimiter);

    self::assertSame($expected, $result);
  }

  /**
   * @return array
   */
  public function tidyProvider(): array
  {
    return [
        ['"I see..."', '“I see…”'],
        ["'This too'", '‘This too’'],
        ['test-dash', 'test—dash'],
        ['Ο συγγραφέας είπε...', 'Ο συγγραφέας είπε…'],
    ];
  }

  /**
   * @return array
   */
  public function titleizeProvider(): array
  {
    $ignore = ['at', 'by', 'for', 'in', 'of', 'on', 'out', 'to', 'the'];

    return [
        ['Title Case', 'TITLE CASE'],
        ['Testing The Method', 'testing the method'],
        ['Testing the Method', 'testing the method', $ignore],
        [
            'I Like to Watch Dvds at Home',
            'i like to watch DVDs at home',
            $ignore,
        ],
        ['Θα Ήθελα Να Φύγει', '  Θα ήθελα να φύγει  ', null, 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function toAsciiProvider(): array
  {
    return [
        ['foo bar', 'fòô bàř'],
        [' TEST ', ' ŤÉŚŢ '],
        ['ph = z = 3', 'φ = ź = 3'],
        ['perevirka', 'перевірка'],
        ['lysaia gora', 'лысая гора'],
        ['shchuka', 'щука'],
        ['Han Zi ', '漢字'],
        ['xin chao the gioi', 'xin chào thế giới'],
        ['XIN CHAO THE GIOI', 'XIN CHÀO THẾ GIỚI'],
        ['dam phat chet luon', 'đấm phát chết luôn'],
        [' ', ' '], // no-break space (U+00A0)
        ['           ', '           '], // spaces U+2000 to U+200A
        [' ', ' '], // narrow no-break space (U+202F)
        [' ', ' '], // medium mathematical space (U+205F)
        [' ', '　'], // ideographic space (U+3000)
        ['?', '𐍉'], // some uncommon, unsupported character (U+10349)
    ];
  }

  /**
   * @return array
   */
  public function toBooleanProvider(): array
  {
    return [
        [true, true],
        [true, 'true'],
        [true, '1'],
        [true, 1],
        [true, 'on'],
        [true, 'ON'],
        [true, 'yes'],
        [true, '999'],
        [false, false],
        [false, 'false'],
        [false, '0'],
        [false, 0],
        [false, 'off'],
        [false, 'OFF'],
        [false, 'no'],
        [false, '-999'],
        [false, ''],
        [false, ' '],
        [false, '  ', 'UTF-8'] // narrow no-break space (U+202F)
    ];
  }

  /**
   * @return array
   */
  public function toLowerCaseProvider(): array
  {
    return [
        ['foo bar', 'FOO BAR'],
        [' foo_bar ', ' FOO_bar '],
        ['fòô bàř', 'FÒÔ BÀŘ', 'UTF-8'],
        [' fòô_bàř ', ' FÒÔ_bàř ', 'UTF-8'],
        ['αυτοκίνητο', 'ΑΥΤΟΚΊΝΗΤΟ', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function toSpacesProvider(): array
  {
    return [
        ['    foo    bar    ', '	foo	bar	'],
        ['     foo     bar     ', '	foo	bar	', 5],
        ['    foo  bar  ', '		foo	bar	', 2],
        ['foobar', '	foo	bar	', 0],
        ["    foo\n    bar", "	foo\n	bar"],
        ["    fòô\n    bàř", "	fòô\n	bàř"],
    ];
  }

  /**
   * @return array
   */
  public function toStringProvider(): array
  {
    return [
        ['', null],
        ['', false],
        ['1', true],
        ['-9', -9],
        ['1.18', 1.18],
        [' string  ', ' string  '],
    ];
  }

  /**
   * @return array
   */
  public function toTabsProvider(): array
  {
    return [
        ['	foo	bar	', '    foo    bar    '],
        ['	foo	bar	', '     foo     bar     ', 5],
        ['		foo	bar	', '    foo  bar  ', 2],
        ["	foo\n	bar", "    foo\n    bar"],
        ["	fòô\n	bàř", "    fòô\n    bàř"],
    ];
  }

  /**
   * @return array
   */
  public function toTitleCaseProvider(): array
  {
    return [
        ['Foo Bar', 'foo bar'],
        [' Foo_Bar ', ' foo_bar '],
        ['Fòô Bàř', 'fòô bàř', 'UTF-8'],
        [' Fòô_Bàř ', ' fòô_bàř ', 'UTF-8'],
        ['Αυτοκίνητο Αυτοκίνητο', 'αυτοκίνητο αυτοκίνητο', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function toUpperCaseProvider(): array
  {
    return [
        ['FOO BAR', 'foo bar'],
        [' FOO_BAR ', ' FOO_bar '],
        ['FÒÔ BÀŘ', 'fòô bàř', 'UTF-8'],
        [' FÒÔ_BÀŘ ', ' FÒÔ_bàř ', 'UTF-8'],
        ['ΑΥΤΟΚΊΝΗΤΟ', 'αυτοκίνητο', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function trimLeftProvider(): array
  {
    return [
        ['foo   bar  ', '  foo   bar  '],
        ['foo bar', ' foo bar'],
        ['foo bar ', 'foo bar '],
        ["foo bar \n\t", "\n\t foo bar \n\t"],
        ['fòô   bàř  ', '  fòô   bàř  '],
        ['fòô bàř', ' fòô bàř'],
        ['fòô bàř ', 'fòô bàř '],
        ['foo bar', '--foo bar', '-'],
        ['fòô bàř', 'òòfòô bàř', 'ò', 'UTF-8'],
        ["fòô bàř \n\t", "\n\t fòô bàř \n\t", null, 'UTF-8'],
        ['fòô ', ' fòô ', null, 'UTF-8'], // narrow no-break space (U+202F)
        ['fòô  ', '  fòô  ', null, 'UTF-8'], // medium mathematical space (U+205F)
        ['fòô', '           fòô', null, 'UTF-8'] // spaces U+2000 to U+200A
    ];
  }

  /**
   * @return array
   */
  public function trimProvider(): array
  {
    return [
        ['foo   bar', '  foo   bar  '],
        ['foo bar', ' foo bar'],
        ['foo bar', 'foo bar '],
        ['foo bar', "\n\t foo bar \n\t"],
        ['fòô   bàř', '  fòô   bàř  '],
        ['fòô bàř', ' fòô bàř'],
        ['fòô bàř', 'fòô bàř '],
        [' foo bar ', "\n\t foo bar \n\t", "\n\t"],
        ['fòô bàř', "\n\t fòô bàř \n\t", null, 'UTF-8'],
        ['fòô', ' fòô ', null, 'UTF-8'], // narrow no-break space (U+202F)
        ['fòô', '  fòô  ', null, 'UTF-8'], // medium mathematical space (U+205F)
        ['fòô', '           fòô', null, 'UTF-8'] // spaces U+2000 to U+200A
    ];
  }

  /**
   * @return array
   */
  public function trimRightProvider(): array
  {
    return [
        ['  foo   bar', '  foo   bar  '],
        ['foo bar', 'foo bar '],
        [' foo bar', ' foo bar'],
        ["\n\t foo bar", "\n\t foo bar \n\t"],
        ['  fòô   bàř', '  fòô   bàř  '],
        ['fòô bàř', 'fòô bàř '],
        [' fòô bàř', ' fòô bàř'],
        ['foo bar', 'foo bar--', '-'],
        ['fòô bàř', 'fòô bàřòò', 'ò', 'UTF-8'],
        ["\n\t fòô bàř", "\n\t fòô bàř \n\t", null, 'UTF-8'],
        [' fòô', ' fòô ', null, 'UTF-8'], // narrow no-break space (U+202F)
        ['  fòô', '  fòô  ', null, 'UTF-8'], // medium mathematical space (U+205F)
        ['fòô', 'fòô           ', null, 'UTF-8'] // spaces U+2000 to U+200A
    ];
  }

  /**
   * @return array
   */
  public function truncateProvider(): array
  {
    return [
        ['Test foo bar', 'Test foo bar', 12],
        ['Test foo ba', 'Test foo bar', 11],
        ['Test foo', 'Test foo bar', 8],
        ['Test fo', 'Test foo bar', 7],
        ['Test', 'Test foo bar', 4],
        ['Test foo bar', 'Test foo bar', 12, '...'],
        ['Test foo...', 'Test foo bar', 11, '...'],
        ['Test ...', 'Test foo bar', 8, '...'],
        ['Test...', 'Test foo bar', 7, '...'],
        ['T...', 'Test foo bar', 4, '...'],
        ['Test fo....', 'Test foo bar', 11, '....'],
        ['Test fòô bàř', 'Test fòô bàř', 12, '', 'UTF-8'],
        ['Test fòô bà', 'Test fòô bàř', 11, '', 'UTF-8'],
        ['Test fòô', 'Test fòô bàř', 8, '', 'UTF-8'],
        ['Test fò', 'Test fòô bàř', 7, '', 'UTF-8'],
        ['Test', 'Test fòô bàř', 4, '', 'UTF-8'],
        ['Test fòô bàř', 'Test fòô bàř', 12, 'ϰϰ', 'UTF-8'],
        ['Test fòô ϰϰ', 'Test fòô bàř', 11, 'ϰϰ', 'UTF-8'],
        ['Test fϰϰ', 'Test fòô bàř', 8, 'ϰϰ', 'UTF-8'],
        ['Test ϰϰ', 'Test fòô bàř', 7, 'ϰϰ', 'UTF-8'],
        ['Teϰϰ', 'Test fòô bàř', 4, 'ϰϰ', 'UTF-8'],
        ['What are your pl...', 'What are your plans today?', 19, '...'],
    ];
  }

  /**
   * @return array
   */
  public function underscoredProvider(): array
  {
    return [
        ['test_case', 'testCase'],
        ['test_case', 'Test-Case'],
        ['test_case', 'test case'],
        ['test_case', 'test -case'],
        ['_test_case', '-test - case'],
        ['test_case', 'test_case'],
        ['test_c_test', '  test c test'],
        ['test_u_case', 'TestUCase'],
        ['test_c_c_test', 'TestCCTest'],
        ['string_with1number', 'string_with1number'],
        ['string_with_2_2_numbers', 'String-with_2_2 numbers'],
        ['1test2case', '1test2case'],
        ['yes_we_can', 'yesWeCan'],
        ['test_σase', 'test Σase', 'UTF-8'],
        ['στανιλ_case', 'Στανιλ case', 'UTF-8'],
        ['σash_case', 'Σash  Case', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function upperCamelizeProvider(): array
  {
    return [
        ['CamelCase', 'camelCase'],
        ['CamelCase', 'Camel-Case'],
        ['CamelCase', 'camel case'],
        ['CamelCase', 'camel -case'],
        ['CamelCase', 'camel - case'],
        ['CamelCase', 'camel_case'],
        ['CamelCTest', 'camel c test'],
        ['StringWith1Number', 'string_with1number'],
        ['StringWith22Numbers', 'string-with-2-2 numbers'],
        ['1Camel2Case', '1camel2case'],
        ['CamelΣase', 'camel σase', 'UTF-8'],
        ['ΣτανιλCase', 'στανιλ case', 'UTF-8'],
        ['ΣamelCase', 'Σamel  Case', 'UTF-8'],
    ];
  }

  /**
   * @return array
   */
  public function upperCaseFirstProvider(): array
  {
    return [
        ['Test', 'Test'],
        ['Test', 'test'],
        ['1a', '1a'],
        ['Σ test', 'σ test', 'UTF-8'],
        [' σ test', ' σ test', 'UTF-8'],
    ];
  }
}
