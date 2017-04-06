<?php

use voku\helper\Bootup;
use voku\helper\UTF8;

/**
 * Class Utf8GlobalTest
 */
class Utf8GlobalTest extends PHPUnit_Framework_TestCase
{

  /**
   * helper-function for test -> "testCombineSomeUtf8Functions()"
   *
   * @param $comment
   *
   * @return string
   */
  public function cleanString($comment)
  {
    foreach (array('fuck', 'foo', 'bar') as $value) {
      $value = UTF8::trim($value);

      if (UTF8::stripos($comment, $value) !== false) {

        $comment = UTF8::str_ireplace($value, '*****', $comment);
      }
    }

    $comment = UTF8::trim(strip_tags($comment));

    return (string)$comment;
  }

  /**
   * Call protected/private method of a class.
   *
   * @param object &$object    Instantiated object that we will run method on.
   * @param string $methodName Method name to call
   * @param array  $parameters Array of parameters to pass into method.
   *
   * @return mixed Method return.
   */
  public function invokeMethod(&$object, $methodName, array $parameters = array())
  {
    $reflection = new \ReflectionClass(get_class($object));
    $method = $reflection->getMethod($methodName);
    $method->setAccessible(true);

    return $method->invokeArgs($object, $parameters);
  }

  public function setUp()
  {
    error_reporting(E_STRICT);
  }

  public function testAccess()
  {
    $testArray = array(
        ''          => array(1 => ''),
        '中文空白'      => array(2 => '空'),
        '中文空白-test' => array(3 => '白'),
        'fòô'       => array(1 => 'ò'),
    );

    foreach ($testArray as $actualString => $testDataArray) {
      foreach ($testDataArray as $stringPos => $expectedString) {
        self::assertSame($expectedString, UTF8::access($actualString, $stringPos));
      }
    }
  }

  public function testCallback()
  {
    $actual = UTF8::callback(
        array(
            'voku\helper\UTF8',
            'strtolower',
        ),
        'Κόσμε-ÖÄÜ'
    );
    $expected = array(
        'κ',
        'ό',
        'σ',
        'μ',
        'ε',
        '-',
        'ö',
        'ä',
        'ü',
    );
    self::assertSame($expected, $actual);
  }

  public function testChar()
  {
    $testArray = array(
        '39'   => '\'',
        '40'   => '(',
        '41'   => ')',
        '42'   => '*',
        '160'  => ' ',
        0x666  => '٦',
        0x165  => 'ť',
        0x8469 => '葩',
        0x2603 => '☃',
    );

    foreach ($testArray as $before => $after) {
      self::assertSame($after, UTF8::chr($before), 'tested: ' . $before);
    }

    for ($i = 0; $i < 20000; $i++) { // keep this loop for simple performance tests
      foreach ($testArray as $before => $after) {
        self::assertSame($after, UTF8::chr(UTF8::ord(UTF8::chr($before))), 'tested: ' . $before);
      }
    }

    // -- with encoding

    self::assertSame(97, UTF8::ord('a', 'ISO'));
    self::assertSame('a', UTF8::chr(97, 'ISO'));

    // --

    $testArrayFail = array(
        null  => null, // fail
        ''    => null, // fail
        'foo' => null, // fail
        'fòô' => null, // fail
    );

    foreach ($testArrayFail as $before => $after) {
      self::assertSame($after, UTF8::chr($before), 'tested: ' . $before);
    }
  }

  public function testChrSizeList()
  {
    $testArray = array(
        "中文空白\xF0\x90\x8C\xBC" => array(
            3,
            3,
            3,
            3,
            4,
        ),
        'öäü'                  => array(
            2,
            2,
            2,
        ),
        'abc'                  => array(
            1,
            1,
            1,
        ),
        ''                     => array(),
        '中文空白-test'            => array(
            3,
            3,
            3,
            3,
            1,
            1,
            1,
            1,
            1,
        ),
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::chr_size_list($actual));
    }
  }

  public function testChrToDecimal()
  {
    $tests = array(
        '~' => 0x7e,
        '§' => 0xa7,
        'ሇ' => 0x1207,
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::chr_to_decimal($before));
      self::assertSame($after, UTF8::chr_to_int(UTF8::int_to_chr(UTF8::chr_to_int($before))));
    }
  }

  public function testChrToHex()
  {
    $tests = array(
        ''  => 'U+0000',
        ' ' => 'U+0020',
        0   => 'U+0030',
        'a' => 'U+0061',
        'ä' => 'U+00e4',
        'ό' => 'U+1f79',
        '❤' => 'U+2764',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::chr_to_hex(UTF8::hex_to_chr(UTF8::chr_to_hex($before))), 'tested: ' . $before);
    }

    // ---

    self::assertSame('U+2764', UTF8::chr_to_hex('❤'));
    self::assertSame('U+00a7', UTF8::chr_to_hex('§'));

    // ---

    self::assertSame('U+0000', UTF8::chr_to_hex(UTF8::hex_to_chr(UTF8::chr_to_hex(''))));

  }

  public function testChunkSplit()
  {
    $result = UTF8::chunk_split('ABC-ÖÄÜ-中文空白-κόσμε', 3);
    $expected = "ABC\r\n-ÖÄ\r\nÜ-中\r\n文空白\r\n-κό\r\nσμε";

    self::assertSame($expected, $result);
  }

  public function testClean()
  {
    $examples = array(
      // Valid defaults
      ''                                                                                     => array('' => ''),
      ' '                                                                                    => array(' ' => ' '),
      null                                                                                   => array(null => ''),
      1                                                                                      => array(1 => '1'),
      '2'                                                                                    => array('2' => '2'),
      '+1'                                                                                   => array('+1' => '+1'),
      // Valid UTF-8
      '纳达尔绝境下大反击拒绝冷门逆转晋级中网四强'                                                                => array('纳达尔绝境下大反击拒绝冷门逆转晋级中网四强' => '纳达尔绝境下大反击拒绝冷门逆转晋级中网四强'),
      'κόσμε'                                                                                => array('κόσμε' => 'κόσμε'),
      '中'                                                                                    => array('中' => '中'),
      '«foobar»'                                                                             => array('«foobar»' => '«foobar»'),
      // Valid UTF-8 + UTF-8 NO-BREAK SPACE
      "κόσμε\xc2\xa0"                                                                        => array("κόσμε\xc2\xa0" => "κόσμε\xc2\xa0"),
      // Valid UTF-8 + Invalid Chars
      "κόσμε\xa0\xa1-öäü"                                                                    => array('κόσμε-öäü' => 'κόσμε-öäü'),
      // Valid UTF-8 + ISO-Errors
      'DÃ¼sseldorf'                                                                          => array('Düsseldorf' => 'Düsseldorf'),
      // Valid ASCII
      'a'                                                                                    => array('a' => 'a'),
      // Valid emoji (non-UTF-8)
      '😃'                                                                                   => array('😃' => '😃'),
      '🐵 🙈 🙉 🙊 | ❤️ 💔 💌 💕 💞 💓 💗 💖 💘 💝 💟 💜 💛 💚 💙 | 🚾 🆒 🆓 🆕 🆖 🆗 🆙 🏧' => array('🐵 🙈 🙉 🙊 | ❤️ 💔 💌 💕 💞 💓 💗 💖 💘 💝 💟 💜 💛 💚 💙 | 🚾 🆒 🆓 🆕 🆖 🆗 🆙 🏧' => '🐵 🙈 🙉 🙊 | ❤️ 💔 💌 💕 💞 💓 💗 💖 💘 💝 💟 💜 💛 💚 💙 | 🚾 🆒 🆓 🆕 🆖 🆗 🆙 🏧'),
      // Valid ASCII + Invalid Chars
      "a\xa0\xa1-öäü"                                                                        => array('a-öäü' => 'a-öäü'),
      // Valid 2 Octet Sequence
      "\xc3\xb1"                                                                             => array('ñ' => 'ñ'),
      // Invalid 2 Octet Sequence
      "\xc3\x28"                                                                             => array('�(' => '('),
      // Invalid
      "\x00"                                                                                 => array('�' => ''),
      // Invalid Sequence Identifier
      "\xa0\xa1"                                                                             => array('��' => ''),
      // Valid 3 Octet Sequence
      "\xe2\x82\xa1"                                                                         => array('₡' => '₡'),
      // Invalid 3 Octet Sequence (in 2nd Octet)
      "\xe2\x28\xa1"                                                                         => array('�(�' => '('),
      // Invalid 3 Octet Sequence (in 3rd Octet)
      "\xe2\x82\x28"                                                                         => array('�(' => '('),
      // Valid 4 Octet Sequence
      "\xf0\x90\x8c\xbc"                                                                     => array('𐌼' => '𐌼'),
      // Invalid 4 Octet Sequence (in 2nd Invalid 4 Octet Sequence (in 2ndOctet)
      "\xf0\x28\x8c\xbc"                                                                     => array('�(��' => '('),
      // Invalid 4 Octet Sequence (in 3rd Octet)
      "\xf0\x90\x28\xbc"                                                                     => array('�(�' => '('),
      // Invalid 4 Octet Sequence (in 4th Octet)
      "\xf0\x28\x8c\x28"                                                                     => array('�(�(' => '(('),
      // Valid 5 Octet Sequence (but not Unicode!)
      "\xf8\xa1\xa1\xa1\xa1"                                                                 => array('�' => ''),
      // Valid 6 Octet Sequence (but not Unicode!)
      "\xfc\xa1\xa1\xa1\xa1\xa1"                                                             => array('�' => ''),
      // Valid 6 Octet Sequence (but not Unicode!) + UTF-8 EN SPACE
      "\xfc\xa1\xa1\xa1\xa1\xa1\xe2\x80\x82"                                                 => array('�' => ' '),
    );

    // <<<<--- \"this comment is only a helper for PHPStorm and non UTF-8 chars

    $counter = 0;
    foreach ($examples as $testString => $testResults) {
      foreach ($testResults as $before => $after) {
        self::assertSame($after, UTF8::cleanup($testString), $counter);
      }
      $counter++;
    }
  }

  public function testStrReplaceFirst()
  {
    $testArray = array(
        '' => array('', '', ''),
        ' lall lall' => array('lall', '', 'lall lall lall'),
        'ö a l l ' => array('l', 'ö', 'l a l l '),
        'κöäüσμε ό' => array('ό', 'öäü', "κόσμε\xc2\xa0ό", ),
    );

    foreach ($testArray as $after => $test) {
      self::assertSame($after, UTF8::str_replace_first($test[0], $test[1], $test[2]));
    }
  }

  public function testCleanup()
  {
    $examples = array(
      // Valid defaults
      ''                                     => array('' => ''),
      ' '                                    => array(' ' => ' '),
      null                                   => array(null => ''),
      1                                      => array(1 => '1'),
      '2'                                    => array('2' => '2'),
      '+1'                                   => array('+1' => '+1'),
      // Valid UTF-8 + UTF-8 NO-BREAK SPACE
      "κόσμε\xc2\xa0"                        => array('κόσμε' . "\xc2\xa0" => 'κόσμε' . "\xc2\xa0"),
      // Valid UTF-8
      '中'                                    => array('中' => '中'),
      // Valid UTF-8 + ISO-Error
      'DÃ¼sseldorf'                          => array('Düsseldorf' => 'Düsseldorf'),
      // Valid UTF-8 + Invalid Chars
      "κόσμε\xa0\xa1-öäü"                    => array('κόσμε-öäü' => 'κόσμε-öäü'),
      // Valid ASCII
      'a'                                    => array('a' => 'a'),
      // Valid ASCII + Invalid Chars
      "a\xa0\xa1-öäü"                        => array('a-öäü' => 'a-öäü'),
      // Valid 2 Octet Sequence
      "\xc3\xb1"                             => array('ñ' => 'ñ'),
      // Invalid
      "\x00"                                 => array('�' => ''),
      // Invalid 2 Octet Sequence
      "\xc3\x28"                             => array('�(' => '('),
      // Invalid Sequence Identifier
      "\xa0\xa1"                             => array('��' => ''),
      // Valid 3 Octet Sequence
      "\xe2\x82\xa1"                         => array('₡' => '₡'),
      // Invalid 3 Octet Sequence (in 2nd Octet)
      "\xe2\x28\xa1"                         => array('�(�' => '('),
      // Invalid 3 Octet Sequence (in 3rd Octet)
      "\xe2\x82\x28"                         => array('�(' => '('),
      // Valid 4 Octet Sequence
      "\xf0\x90\x8c\xbc"                     => array('𐌼' => '𐌼'),
      // Invalid 4 Octet Sequence (in 2nd Octet)
      "\xf0\x28\x8c\xbc"                     => array('�(��' => '('),
      // Invalid 4 Octet Sequence (in 3rd Octet)
      "\xf0\x90\x28\xbc"                     => array('�(�' => '('),
      // Invalid 4 Octet Sequence (in 4th Octet)
      " \xf0\x28\x8c\x28"                    => array('�(�(' => ' (('),
      // Valid 5 Octet Sequence (but not Unicode!)
      "\xf8\xa1\xa1\xa1\xa1"                 => array('�' => ''),
      // Valid 6 Octet Sequence (but not Unicode!) + UTF-8 EN SPACE
      "\xfc\xa1\xa1\xa1\xa1\xa1\xe2\x80\x82" => array('�' => ' '),
      // test for database-insert
      '
        <h1>«DÃ¼sseldorf» &ndash; &lt;Köln&gt;</h1>
        <br /><br />
        <!--suppress CheckDtdRefs -->
<p>
          &nbsp;�&foo;❤&nbsp;
        </p>
        '                              => array(
          '' => '
        <h1>«Düsseldorf» &ndash; &lt;Köln&gt;</h1>
        <br /><br />
        <!--suppress CheckDtdRefs -->
<p>
          &nbsp;&foo;❤&nbsp;
        </p>
        ',
      ),
    );

    foreach ($examples as $testString => $testResults) {
      foreach ($testResults as $before => $after) {
        self::assertSame($after, UTF8::cleanup($testString));
      }
    }

  }

  public function testCodepoints()
  {
    $testArray = array(
        "\xF0\x90\x8C\xBC---" => array(
            0 => 66364,
            1 => 45,
            2 => 45,
            3 => 45,
        ),
        '中-abc'               => array(
            0 => 20013,
            1 => 45,
            2 => 97,
            3 => 98,
            4 => 99,
        ),
        '₧{abc}'              => array(
            0 => 8359,
            1 => 123,
            2 => 97,
            3 => 98,
            4 => 99,
            5 => 125,
        ),
        'κöñ'                 => array(
            0 => 954,
            1 => 246,
            2 => 241,
        ),
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::codepoints($actual));
    }

    // --- U+xxxx format

    self::assertSame(array(0 => 'U+03ba', 1 => 'U+00f6', 2 => 'U+00f1'), UTF8::codepoints('κöñ', true));
    self::assertSame(
        array(0 => 'U+03ba', 1 => 'U+00f6', 2 => 'U+00f1'), UTF8::codepoints(
        array(
            'κ',
            'ö',
            'ñ',
        ), true
    )
    );
  }

  public function testCombineSomeUtf8Functions()
  {
    $testArray = array(
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
    );

    foreach ($testArray as $testString => $testResult) {
      self::assertSame($testResult, $this->cleanString($testString));
    }
  }

  public function testCountChars()
  {
    $testArray = array(
        'κaκbκc' => array(
            'κ' => 3,
            'a' => 1,
            'b' => 1,
            'c' => 1,
        ),
        'cba'    => array(
            'c' => 1,
            'b' => 1,
            'a' => 1,
        ),
        'abcöäü' => array(
            'a' => 1,
            'b' => 1,
            'c' => 1,
            'ö' => 1,
            'ä' => 1,
            'ü' => 1,
        ),
        '白白'     => array('白' => 2),
        ''       => array(),
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame(true, $expected === UTF8::count_chars($actual), 'error by ' . $actual);
    }

    // added invalid UTF-8
    $testArray['白' . "\xa0\xa1" . '白'] = array('白' => 2);

    foreach ($testArray as $actual => $expected) {
      self::assertSame(true, $expected === UTF8::count_chars($actual, true), 'error by ' . $actual);
    }
  }

  public function testDecimalToChr()
  {
    $tests = array(
        0x7e   => '~',
        0xa7   => '§',
        0x1207 => 'ሇ',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::decimal_to_chr($before));
    }
  }

  public function testEncode()
  {
    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-中文空白-  ',
        '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
        'öäü'            => 'öäü',
        ''               => '',
        'abc'            => 'abc',
        'Berbée'         => 'Berbée',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::encode('', $before), 'tested: ' . $before); // do nothing
    }

    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-中文空白-  ',
        '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
        'öäü'            => 'öäü',
        ''               => '',
        'abc'            => 'abc',
        'Berbée'         => 'Berbée',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::encode('UTF8', $before), 'tested: ' . $before); // UTF-8
    }

    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-????-  ',
        '      - ÖÄÜ- '  => '      - ???- ',
        'öäü'            => '???',
        ''               => '',
        'abc'            => 'abc',
        'Berbée'         => 'Berb?e',
    );

    if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
      foreach ($tests as $before => $after) {
        self::assertSame($after, UTF8::encode('CP367', $before), 'tested: ' . $before); // CP367
      }
    }

    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-????-  ',
        '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
        'öäü'            => 'öäü',
        ''               => '',
        'abc'            => 'abc',
        'Berbée'         => 'Berbée',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::filter(UTF8::encode('ISo88591', $before)), 'tested: ' . $before); // ISO-8859-1
    }

    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-????-  ',
        '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
        'öäü'            => 'öäü',
        ''               => '',
        'abc'            => 'abc',
        'Berbée'         => 'Berbée',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::filter(UTF8::encode('IsO-8859-15', UTF8::encode('iso-8859-1', $before)))); // ISO-8859-15
    }

    // ---

    self::assertSame('éàa', UTF8::encode('UTF-8', UTF8::encode('ISO-8859-1', 'éàa')));
  }

  public function testEncodeUtf8EncodeUtf8()
  {
    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-中文空白-  ',
        '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
        'öäü'            => 'öäü',
        ''               => '',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::encode('UTF-8', UTF8::encode('UTF-8', $before)));
    }
  }

  public function testEncodeUtf8Utf8Encode()
  {
    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-ä¸­æ–‡ç©ºç™½-  ',
        '      - ÖÄÜ- '  => '      - Ã–Ã„Ãœ- ',
        'öäü'            => 'Ã¶Ã¤Ã¼',
        ''               => '',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::utf8_encode(UTF8::encode('UTF-8', $before)));
    }
  }

  public function testFileGetContents()
  {
    $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/sample-unicode-chart.txt');
    self::assertContains('M	𝐌	𝑀	𝑴	𝖬	𝗠	𝘔	𝙈	ℳ	𝓜	𝔐	𝕸	𝙼	𝕄', $testString);

    $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/sample-html.txt');
    self::assertContains('վṩ鼦Ѷ鼦ַ鼦ٷվݡ', $testString);

    $testString = file_get_contents(__DIR__ . '/fixtures/sample-html.txt');
    self::assertContains('վṩ鼦Ѷ鼦ַ鼦ٷվݡ', $testString);

    $testString = file_get_contents(__DIR__ . '/fixtures/sample-html.txt');
    $testStringUtf8 = UTF8::clean($testString, true, true, true);
    self::assertContains('վṩ鼦Ѷ鼦ַ鼦ٷվݡ', $testStringUtf8);
    self::assertContains('<p>鼦</p>', $testStringUtf8);
    self::assertContains('<li><a href="/">鼦վͼ</a></li>', $testStringUtf8);
    self::assertContains('<B><a href="http://www.baidu.com/" >ٶ</a></B>', $testStringUtf8);

    // ---

    $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-16-be.txt');
    self::assertContains(
        '<p>Today’s Internet users are not the same users who were online a decade ago. There are better connections.',
        $testString
    );

    $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-16-le.txt');
    self::assertContains(
        '<p>Today’s Internet users are not the same users who were online a decade ago. There are better connections.',
        $testString
    );

    $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-8.txt');
    self::assertContains('Iñtërnâtiônàlizætiøn', $testString);

    $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/latin.txt');
    self::assertContains('Iñtërnâtiônàlizætiøn', $testString);

    $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/iso-8859-7.txt');
    self::assertContains('Iñtërnâtiônàlizætiøn', $testString);

    $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-16-be.txt', FILE_TEXT);
    self::assertContains(
        '<p>Today’s Internet users are not the same users who were online a decade ago. There are better connections.',
        $testString
    );

    $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-16-le.txt', null, null, 0);
    self::assertContains(
        '<p>Today’s Internet users are not the same users who were online a decade ago. There are better connections.',
        $testString
    );

    // text: with offset
    $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-16-le.txt', null, null, 5);
    self::assertContains('There are better connections.', $testString);

    // text: with offset & max-length
    $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-8.txt', null, null, 7, 11);
    self::assertContains('Iñtërnât', $testString);

    // text: with offset & max-length + timeout
    $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/latin.txt', null, null, 7, 10, 15);
    self::assertContains('ñtërnâtiôn', $testString);

    // text: with timeout
    $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/iso-8859-7.txt', null, null, 7, null, 10);
    self::assertContains('Iñtërnâtiônàlizætiøn', $testString);

    // text: with max-length + timeout
    $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/iso-8859-7.txt', null, null, null, 10, 10);
    self::assertContains('Hírek', $testString);

    $context = stream_context_create(
        array(
            'http' =>
                array(
                    'timeout' => 10,
                ),
        )
    );

    // text: with max-length + timeout
    $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/iso-8859-7.txt', null, $context, null, 10, 10);
    self::assertContains('Hírek', $testString);

    // text: do not convert to utf-8 + timeout
    $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/iso-8859-7.txt', null, $context, null, 10, 10, false);
    self::assertRegExp('#H.*rek#', $testString);

    // text: do not convert to utf-8 + timeout
    $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/utf-8.txt', null, $context, null, 10, 10, false);
    self::assertContains('Hírek', $testString);
  }

  public function testFileGetContentsBinary()
  {
    $context = stream_context_create(
        array(
            'http' =>
                array(
                    'timeout' => 10,
                ),
        )
    );

    // image: do not convert to utf-8 + timeout
    $image = UTF8::file_get_contents(__DIR__ . '/fixtures/image.png', null, $context, null, null, 10, false);
    self::assertSame(true, UTF8::is_binary($image));

    // image: convert to utf-8 + timeout (ERROR)
    $image2 = UTF8::file_get_contents(__DIR__ . '/fixtures/image.png', null, $context, null, null, 10, true);
    self::assertSame(false, UTF8::is_binary($image2));

    self::assertNotEquals($image2, $image);
  }

  public function testFilter()
  {
    self::assertSame('é', UTF8::filter("\xE9"));

    // ---

    $c = 'à';
    $d = \Normalizer::normalize($c, \Normalizer::NFD);
    $a = array(
        'n' => 4,
        'a' => "\xE9",
        'b' => substr($d, 1),
        'c' => $c,
        'd' => $d,
        'e' => "\n\r\n\r",
    );
    $a['f'] = (object)$a;
    $b = UTF8::filter($a);
    $b['f'] = (array)$a['f'];

    $expect = array(
        'n' => 4,
        'a' => 'é',
        'b' => '◌' . substr($d, 1),
        'c' => $c,
        'd' => $c,
        'e' => "\n\n\n",
    );
    $expect['f'] = $expect;

    self::assertSame($expect, $b);

    // -----

    $result = UTF8::filter(array("\xE9", 'à', 'a', "\xe2\x80\xa8"), \Normalizer::FORM_D);

    self::assertSame(array(0 => 'é', 1 => 'à', 2 => 'a', 3 => "\xe2\x80\xa8"), $result);
  }

  public function testFilterVar()
  {
    $options = array(
        'options' => array(
            'default'   => -1,
            // value to return if the filter fails
            'min_range' => 90,
            'max_range' => 99,
        ),
    );

    self::assertSame('  -ABC-中文空白-  ', UTF8::filter_var('  -ABC-中文空白-  ', FILTER_DEFAULT));
    self::assertSame(false, UTF8::filter_var('  -ABC-中文空白-  ', FILTER_VALIDATE_URL));
    self::assertSame(false, UTF8::filter_var('  -ABC-中文空白-  ', FILTER_VALIDATE_EMAIL));
    self::assertSame(-1, UTF8::filter_var('中文空白 ', FILTER_VALIDATE_INT, $options));
    self::assertSame(99, UTF8::filter_var(99, FILTER_VALIDATE_INT, $options));
    self::assertSame(-1, UTF8::filter_var(100, FILTER_VALIDATE_INT, $options));
  }

  public function testFilterVarArray()
  {
    $filters = array(
        'name'  => array(
            'filter'  => FILTER_CALLBACK,
            'options' => array('voku\helper\UTF8', 'ucwords'),
        ),
        'age'   => array(
            'filter'  => FILTER_VALIDATE_INT,
            'options' => array(
                'min_range' => 1,
                'max_range' => 120,
            ),
        ),
        'email' => FILTER_VALIDATE_EMAIL,
    );

    $data['name'] = 'κόσμε';
    $data['age'] = '18';
    $data['email'] = 'foo@bar.de';

    self::assertSame(
        array(
            'name'  => 'Κόσμε',
            'age'   => 18,
            'email' => 'foo@bar.de',
        ),
        UTF8::filter_var_array($data, $filters, true)
    );

    self::assertSame(
        array(
            'name'  => 'κόσμε',
            'age'   => '18',
            'email' => 'foo@bar.de',
        ),
        UTF8::filter_var_array($data)
    );
  }

  public function testFitsInside()
  {
    $testArray = array(
        'κόσμε'  => array(5 => true),
        'test'   => array(4 => true),
        ''       => array(0 => true),
        ' '      => array(0 => false),
        'abcöäü' => array(2 => false),
    );

    foreach ($testArray as $actual => $data) {
      foreach ($data as $size => $expected) {
        self::assertSame($expected, UTF8::fits_inside($actual, $size), 'error by ' . $actual);
      }
    }
  }

  public function testFixBrokenUtf8()
  {
    $testArray = array(
        'Düsseldorf'                                      => 'Düsseldorf',
        'Ã'                                               => 'Ã',
        ' '                                               => ' ',
        ''                                                => '',
        "\n"                                              => "\n",
        "test\xc2\x88"                                    => 'testˆ',
        'DÃ¼sseldorf'                                     => 'Düsseldorf',
        'Ã¤'                                              => 'ä',
        'test'                                            => 'test',
        'FÃÂ©dération Camerounaise de Football'           => 'Fédération Camerounaise de Football',
        "FÃÂ©dération Camerounaise de Football\n"         => "Fédération Camerounaise de Football\n",
        'FÃ©dÃ©ration Camerounaise de Football'           => 'Fédération Camerounaise de Football',
        "FÃ©dÃ©ration Camerounaise de Football\n"         => "Fédération Camerounaise de Football\n",
        'FÃÂ©dÃÂ©ration Camerounaise de Football'         => 'Fédération Camerounaise de Football',
        "FÃÂ©dÃÂ©ration Camerounaise de Football\n"       => "Fédération Camerounaise de Football\n",
        'FÃÂÂÂÂ©dÃÂÂÂÂ©ration Camerounaise de Football'   => 'Fédération Camerounaise de Football',
        "FÃÂÂÂÂ©dÃÂÂÂÂ©ration Camerounaise de Football\n" => "Fédération Camerounaise de Football\n",
    );

    foreach ($testArray as $before => $after) {
      self::assertSame($after, UTF8::fix_utf8($before));
    }

    self::assertSame(array('Düsseldorf', 'Fédération'), UTF8::fix_utf8(array('DÃ¼sseldorf', 'FÃÂÂÂÂ©dÃÂÂÂÂ©ration')));
  }

  public function testFixSimpleUtf8()
  {
    $testArray = array(
        'Düsseldorf'   => 'Düsseldorf',
        'Ã'            => 'Ã',
        ' '            => ' ',
        ''             => '',
        "\n"           => "\n",
        "test\xc2\x88" => 'testˆ',
        'DÃ¼sseldorf'  => 'Düsseldorf',
        'Ã¤'           => 'ä',
        'test'         => 'test',
    );

    for ($i = 0; $i < 2; $i++) { // keep this loop for simple performance tests
      foreach ($testArray as $before => $after) {
        self::assertSame($after, UTF8::fix_simple_utf8($before), 'tested: ' . $before);
      }
    }
  }

  public function testGetCharDirection()
  {
    $testArray = array(
        'ا'                                                                                => 'RTL',
        'أحبك'                                                                             => 'RTL',
        'זאת השפה העברית.א'                                                                => 'RTL',
        // http://dotancohen.com/howto/rtl_right_to_left.html
        'זאת השפה העברית.‏'                                                                => 'RTL',
        'abc'                                                                              => 'LTR',
        'öäü'                                                                              => 'LTR',
        '?'                                                                                => 'LTR',
        '💩'                                                                               => 'LTR',
        '中文空白'                                                                             => 'LTR',
        'मोनिच'                                                                            => 'LTR',
        'क्षȸ'                                                                             => 'LTR',
        'ࡘ'                                                                                => 'RTL',
        '𐤹'                                                                               => 'RTL',
        // https://www.compart.com/de/unicode/U+10939
        '𐠅'                                                                               => 'RTL',
        // https://www.compart.com/de/unicode/U+10805
        'ますだ, よしひこ'                                                                        => 'LTR',
        '𐭠 𐭡 𐭢 𐭣 𐭤 𐭥 𐭦 𐭧 𐭨 𐭩 𐭪 𐭫 𐭬 𐭭 𐭮 𐭯 𐭰 𐭱 𐭲 𐭸 𐭹 𐭺 𐭻 𐭼 𐭽 𐭾 𐭿' => 'RTL',
        // http://www.sonderzeichen.de/Inscriptional_Pahlavi/Unicode-10B7F.html

    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::getCharDirection($actual), 'error by ' . $actual);
    }
  }

  public function testHexToIntAndIntToHex()
  {
    $tests = array(
        'U+2026' => 8230,
        'U+03ba' => 954,
        'U+00f6' => 246,
        'U+00f1' => 241,
        'U+0000' => 0,
    );

    $testsForHexToInt = array(
        '\u2026' => 8230,
        '\u03ba' => 954,
        '\u00f6' => 246,
        '\u00f1' => 241,
        '\u0000' => 0,
        //
        '2026'   => 8230,
        '03ba'   => 954,
        '00f6'   => 246,
        '00f1'   => 241,
        '0000'   => 0,
    );

    foreach (array_replace($testsForHexToInt, $tests) as $before => $after) {
      self::assertSame($after, UTF8::hex_to_int($before), 'tested: ' . $before);
    }

    foreach ($tests as $after => $before) {
      self::assertSame($after, UTF8::int_to_hex($before), 'tested: ' . $before);
    }

    // --- fail (hex_to_int)

    self::assertSame(false, UTF8::hex_to_int(''));
    self::assertSame(false, UTF8::hex_to_int('abc-öäü'));

    // --- fail (int_to_hex)

    self::assertSame('', UTF8::int_to_hex(''));
    self::assertSame('', UTF8::int_to_hex('abc-öäü'));

  }

  public function testHtmlEncode()
  {
    $testArray = array(
        '{-test'                  => '&#123;&#45;&#116;&#101;&#115;&#116;',
        '中文空白'                    => '&#20013;&#25991;&#31354;&#30333;',
        'Dänisch (Å/å, Æ/æ, Ø/ø)' => '&#68;&#228;&#110;&#105;&#115;&#99;&#104;&#32;&#40;&#197;&#47;&#229;&#44;&#32;&#198;&#47;&#230;&#44;&#32;&#216;&#47;&#248;&#41;',
        '👍 💩 😄 ❤ 👍 💩 😄 ❤'   => '&#128077;&#32;&#128169;&#32;&#128516;&#32;&#10084;&#32;&#128077;&#32;&#128169;&#32;&#128516;&#32;&#10084;',
        'κόσμε'                   => '&#954;&#8057;&#963;&#956;&#949;',
        'öäü'                     => '&#246;&#228;&#252;',
        ' '                       => '&#32;',
        ''                        => '',
        '�'                       => '&#65533;',
        'Test-,;:'                => '&#84;&#101;&#115;&#116;&#45;&#44;&#59;&#58;',
        '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ ♿ ⛎' => '&#128077;&#32;&#128169;&#32;&#128516;&#32;&#10084;&#32;&#128077;&#32;&#128169;&#32;&#128516;&#32;&#10084;&#32;&#128054;&#32;&#128169;&#32;&#128049;&#32;&#128056;&#32;&#127744;&#32;&#10084;&#32;&#9855;&#32;&#9934;',
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::html_encode($actual), 'tested:' . $actual);
    }

    foreach ($testArray as $actual => $expected) {
      self::assertSame($actual, UTF8::html_decode(UTF8::html_encode($actual)), 'tested:' . $actual);
    }

    foreach ($testArray as $actual => $expected) {
      self::assertSame($actual, UTF8::html_entity_decode(UTF8::html_encode($actual)), 'tested:' . $actual);
    }

    // ---

    $testArray = array(
        '{-test'                  => '{-test',
        '中文空白'                    => '&#20013;&#25991;&#31354;&#30333;',
        'Dänisch (Å/å, Æ/æ, Ø/ø)' => 'D&#228;nisch (&#197;/&#229;, &#198;/&#230;, &#216;/&#248;)',
        '👍 💩 😄 ❤ 👍 💩 😄 ❤'   => '&#128077; &#128169; &#128516; &#10084; &#128077; &#128169; &#128516; &#10084;',
        'κόσμε'                   => '&#954;&#8057;&#963;&#956;&#949;',
        'öäü'                     => '&#246;&#228;&#252;',
        ' '                       => ' ',
        ''                        => '',
        '�'                       => '&#65533;',
        'Test-,;:'                => 'Test-,;:',
        '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ ♿ ⛎' => '&#128077; &#128169; &#128516; &#10084; &#128077; &#128169; &#128516; &#10084; &#128054; &#128169; &#128049; &#128056; &#127744; &#10084; &#9855; &#9934;',
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::html_encode($actual, true), 'tested:' . $actual);
      self::assertSame($actual, UTF8::html_decode(UTF8::html_encode($actual, true)), 'tested:' . $actual);
    }

    // ---

    $testArray = array(
        '{-test'                  => '{-test',
        '中文空白'                    => '中文空白',
        'κόσμε'                   => 'κόσμε',
        'öäü'                     => 'öäü',
        'Dänisch (Å/å, Æ/æ, Ø/ø)' => 'Dänisch (Å/å, Æ/æ, Ø/ø)',
        '👍 💩 😄 ❤ 👍 💩 😄 ❤'   => '👍 💩 😄 ❤ 👍 💩 😄 ❤',
        ' '                       => ' ',
        ''                        => '',
        '&#d;'                    => '&#d;',
        '&d;'                     => '&d;',
        '&gt;'                    => '>',
        '%ABREPRESENT%C9%BB'      => '%ABREPRESENT%C9%BB',
        'Test-,;:'                => 'Test-,;:',
        '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ ♿ ⛎' => '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ ♿ ⛎',
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::html_decode(UTF8::html_encode($actual, true)), 'tested:' . $actual);
    }

    // --- ISO

    $testArray = array(
        '中文空白'                  => '中文空白',
        'κόσμε'                 => 'κόσμε',
        'öäü'                   => 'öäü',
        '(Å/å, Æ/æ, Ø/ø, Σ/σ)'  => '(Å/å, Æ/æ, Ø/ø, Σ/σ)',
        '👍 💩 😄 ❤ 👍 💩 😄 ❤' => '👍 💩 😄 ❤ 👍 💩 😄 ❤',
    );

    foreach ($testArray as $actual => $expected) {
      self::assertNotSame($expected, UTF8::html_decode(UTF8::html_encode($actual, true, 'ISO')), 'tested:' . $actual);
    }

    $testArray = array(
        '{-test' => '{-test',
        'abc'    => 'abc',
        ' '      => ' ',
        ''       => '',
        '&#d;'   => '&#d;',
        '&d;'    => '&d;',
        '&gt;'   => '>',
        '&#39;'  => '\'',
        'Test-,;:' => 'Test-,;:',
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::html_decode(UTF8::html_encode($actual, true, 'ISO'), ENT_QUOTES), 'tested:' . $actual);
    }

    // ---

    // bug is reported: https://github.com/facebook/hhvm/issues/6303#issuecomment-234739899
    if (defined('HHVM_VERSION') === false) {
      $testArray = array(
          '&#d;'  => '&#d;',
          '&d;'   => '&d;',
          '&gt;'  => '>',
          '&#39;' => '&#39;',
      );

      foreach ($testArray as $actual => $expected) {
        self::assertSame($expected, UTF8::html_decode(UTF8::html_encode($actual, true, 'ISO'), ENT_COMPAT), 'tested:' . $actual);
      }
    }
  }

  public function testHtmlEntityDecode()
  {
    $testArray = array(
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
    );

    // bug is reported: https://github.com/facebook/hhvm/issues/6303#issuecomment-234739899
    if (defined('HHVM_VERSION') === false) {
      $tmpTestArray = array(
          'who&#039;s online'                  => 'who&#039;s online',
          'who&amp;#039;s online'              => 'who&#039;s online',
          'who&#039;s online-'                 => 'who&#039;s online-',
          'Who&#039;s Online'                  => 'Who&#039;s Online',
          'Who&amp;#039;s Online'              => 'Who&#039;s Online',
          'Who&amp;amp;#039;s Online &#20013;' => 'Who&#039;s Online 中',
          'who\'s online&colon;'               => 'who\'s online&colon;',
      );

      $testArray = array_merge($testArray, $tmpTestArray);
    }

    foreach ($testArray as $before => $after) {
      self::assertSame($after, UTF8::html_entity_decode($before, ENT_COMPAT), 'error by ' . $before);
    }
  }

  public function testHtmlEntityDecodeWithEntNoQuotes()
  {
    $testArray = array(
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
    );

    // bug is reported: https://github.com/facebook/hhvm/issues/6303#issuecomment-234739899
    if (defined('HHVM_VERSION') === false) {
      $tmpTestArray = array(
          'who&#039;s online'                  => 'who&#039;s online',
          'who&amp;#039;s online'              => 'who&#039;s online',
          'who&#039;s online-'                 => 'who&#039;s online-',
          'Who&#039;s Online'                  => 'Who&#039;s Online',
          'Who&amp;#039;s Online'              => 'Who&#039;s Online',
          'Who&amp;amp;#039;s Online &#20013;' => 'Who&#039;s Online 中',
          'who\'s online&colon;'               => 'who\'s online&colon;',
      );

      $testArray = array_merge($testArray, $tmpTestArray);
    }

    foreach ($testArray as $before => $after) {
      self::assertSame($after, UTF8::html_entity_decode($before, ENT_NOQUOTES, 'UTF-8'), 'error by ' . $before);
    }
  }

  public function testHtmlEntityDecodeWithEntQuotes()
  {
    $testArray = array(
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
    );

    // bug is reported: https://github.com/facebook/hhvm/issues/6303#issuecomment-234739899
    if (defined('HHVM_VERSION') === false) {
      $tmpTestArray = array(
          'who\'s online&colon;' => 'who\'s online&colon;',
      );

      $testArray = array_merge($testArray, $tmpTestArray);
    }

    foreach ($testArray as $before => $after) {
      self::assertSame($after, UTF8::html_entity_decode($before, ENT_QUOTES, 'UTF-8'), 'error by ' . $before);
    }

    // ---

    $testArray = array(
        'κόσμε'                     => 'κόσμε',
        'who&#039;s online'         => 'who\'s online',
        'who&amp;#039;s online'     => 'who\'s online',
        'who&#039;s online-'        => 'who\'s online-',
        'Who&#039;s Online'         => 'Who\'s Online',
        'Who&amp;amp;#039;s Online' => 'Who\'s Online',
        "Who\'s Online&#x0003A;"    => 'Who\\\'s Online:',
    );

    foreach ($testArray as $before => $after) {
      self::assertSame($after, UTF8::html_entity_decode($before, ENT_QUOTES, 'ISO'), 'error by ' . $before); // 'ISO-8859-1'
    }

    if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
      self::assertSame('Who\'s Online ?', UTF8::html_entity_decode('Who&amp;#039;s Online &#20013;', ENT_QUOTES, 'ISO'));
    } else {
      self::assertSame('Who\'s Online ', UTF8::html_entity_decode('Who&amp;#039;s Online &#20013;', ENT_QUOTES, 'ISO'));
    }
  }

  public function testHtmlEntityDecodeWithHtml5()
  {
    $testArray = array(
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
    );

    // bug is reported: https://github.com/facebook/hhvm/issues/6303#issuecomment-234739899
    if (defined('HHVM_VERSION') === false) {
      $tmpTestArray = array(
          'who\'s online&colon;' => 'who\'s online:',
      );

      $testArray = array_merge($testArray, $tmpTestArray);
    }

    if (Bootup::is_php('5.4') === true) {
      foreach ($testArray as $before => $after) {
        self::assertSame($after, UTF8::html_entity_decode($before, ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'error by ' . $before);
      }
    }
  }

  public function testHtmlentities()
  {
    $testArray = array(
        '<白>'                                                                                                         => '&lt;&#30333;&gt;',
        '<白-öäü>'                                                                                                     => '&lt;&#30333;-&ouml;&auml;&uuml;&gt;',
        'dies ist ein test „Goldenen Regeln und Checklisten“.<br /><br /><br />' . UTF8::html_entity_decode('&nbsp;') => 'dies ist ein test &bdquo;Goldenen Regeln und Checklisten&ldquo;.&lt;br /&gt;&lt;br /&gt;&lt;br /&gt;&nbsp;',
        'öäü'                                                                                                         => '&ouml;&auml;&uuml;',
        ' '                                                                                                           => ' ',
        ''                                                                                                            => '',
        'Test-,;:'                                                                                                    => 'Test-,;:',
        '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ ♿ ⛎'                                                     => '&#128077; &#128169; &#128516; &#10084; &#128077; &#128169; &#128516; &#10084; &#128054; &#128169; &#128049; &#128056; &#127744; &#10084; &#9855; &#9934;',
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::htmlentities($actual));

      self::assertSame(
          $actual,
          UTF8::html_entity_decode(
              UTF8::htmlentities($actual)
          )
      );
    }

    // ---

    $testArray = array(
        'abc' => 'abc',
        'öäü' => '&Atilde;&para;&Atilde;&curren;&Atilde;&frac14;',
        ' '   => ' ',
        ''    => '',
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::htmlentities($actual, ENT_COMPAT, 'ISO-8859-1', false));

      self::assertSame(
          $actual,
          UTF8::html_entity_decode(
              UTF8::htmlentities($actual, ENT_COMPAT, 'ISO-8859-1', false),
              ENT_COMPAT,
              'ISO-8859-1'
          )
      );
    }
  }

  public function testHtmlspecialchars()
  {
    $testArray = array(
        "<a href='κόσμε'>κόσμε</a>" => "&lt;a href='κόσμε'&gt;κόσμε&lt;/a&gt;",
        '<白>'                       => '&lt;白&gt;',
        'öäü'                       => 'öäü',
        ' '                         => ' ',
        ''                          => '',
        'Test-,;:'                  => 'Test-,;:',
        '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ &#x267F; &#x26CE;' => '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ &amp;#x267F; &amp;#x26CE;',
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::htmlspecialchars($actual));
      self::assertSame($expected, UTF8::htmlspecialchars($actual, ENT_COMPAT, 'UTF8'));
    }

    // ---

    $testArray = array(
        "<a href='κόσμε'>κόσμε</a>" => '&lt;a href=&#039;κόσμε&#039;&gt;κόσμε&lt;/a&gt;',
        '<白>'                       => '&lt;白&gt;',
        'öäü'                       => 'öäü',
        ' '                         => ' ',
        ''                          => '',
        'Test-,;:'                  => 'Test-,;:',
        '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ &#x267F; &#x26CE;' => '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ &amp;#x267F; &amp;#x26CE;',
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::htmlspecialchars($actual, ENT_QUOTES, 'UTF8'));
    }
  }

  public function testIsAscii()
  {
    $testArray = array(
        'κ'      => false,
        'abc'    => true,
        'abcöäü' => false,
        '白'      => false,
        ' '      => true,
        ''       => true,
        '!!!'    => true,
        '§§§'    => false,
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::is_ascii($actual), 'error by ' . $actual);
    }

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::isAscii($actual), 'error by ' . $actual);
    }
  }

  public function testIsBase64()
  {
    $tests = array(
        0                                          => false,
        1                                          => false,
        -1                                         => false,
        ' '                                        => false,
        ''                                         => false,
        'أبز'                                      => false,
        "\xe2\x80\x99"                             => false,
        'Ɓtest'                                    => false,
        base64_encode('true')                      => true,
        base64_encode('  -ABC-中文空白-  ')            => true,
        'キャンパス'                                    => false,
        'биологическом'                            => false,
        '정, 병호'                                    => false,
        'on'                                       => false,
        'ますだ, よしひこ'                                => false,
        'मोनिच'                                    => false,
        'क्षȸ'                                     => false,
        base64_encode('👍 💩 😄 ❤ 👍 💩 😄 ❤أحبك') => true,
        '👍 💩 😄 ❤ 👍 💩 😄 ❤أحبك'                => false,
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::isBase64($before), $before);
    }
  }

  public function testIsBinary()
  {
    $tests = array(
        'öäü'          => false,
        ''             => false,
        '1'            => false,
        '01010101'     => true,
        decbin(324546) => true,
        01             => true,
        1020304        => false,
        01020304       => false,
        11020304       => false,
        '1010101'      => true,
        11111111       => true,
        00000000       => true,
        "\x00\x01"     => true,
        "\x01\x00"     => true,
        "\x01\x02"     => false,
        "\x01\x01ab"   => false,
        "\x01\x01b"    => false,
        "\x01\x00a"    => true, // >= 30% binary
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::isBinary($before), 'value: ' . $before);
      self::assertSame($after, UTF8::is_binary($before), 'value: ' . $before);
    }
  }

  public function testIsBom()
  {
    $testArray = array(
        "\xef\xbb\xbf"    => true,
        '  þÿ'            => true,
        "foo\xef\xbb\xbf" => false,
        '   þÿ'           => false,
        'foo'             => false,
        ''                => false,
        ' '               => false,
    );

    foreach ($testArray as $test => $expected) {
      self::assertSame($expected, UTF8::isBom($test), 'tested: ' . $test);
      self::assertSame($expected, UTF8::is_bom($test), 'tested: ' . $test);
    }
  }

  public function testIsHtml()
  {
    $testArray = array(
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
    );

    foreach ($testArray as $testString => $testResult) {
      self::assertSame($testResult, UTF8::is_html($testString), 'tested: ' . $testString);
      self::assertSame($testResult, UTF8::isHtml($testString), 'tested: ' . $testString);
    }
  }

  public function testIsUtf16()
  {
    $testArray = array(
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
    );

    $conter = 0;
    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::is_utf16($actual), 'error by - ' . $conter . ' :' . $actual);
      $conter++;
    }

    $conter = 0;
    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::isUtf16($actual), 'error by - ' . $conter . ' :' . $actual);
      $conter++;
    }

    self::assertSame(false, UTF8::isUtf16(file_get_contents(__DIR__ . '/fixtures/utf-8.txt')));
    self::assertSame(false, UTF8::isUtf16(file_get_contents(__DIR__ . '/fixtures/utf-8-bom.txt')));

    self::assertSame(2, UTF8::isUtf16(file_get_contents(__DIR__ . '/fixtures/utf-16-be.txt')));
    self::assertSame(2, UTF8::isUtf16(file_get_contents(__DIR__ . '/fixtures/utf-16-be-bom.txt')));

    self::assertSame(1, UTF8::isUtf16(file_get_contents(__DIR__ . '/fixtures/utf-16-le.txt')));
    self::assertSame(1, UTF8::isUtf16(file_get_contents(__DIR__ . '/fixtures/utf-16-le-bom.txt')));

    self::assertSame(1, UTF8::isUtf16(file_get_contents(__DIR__ . '/fixtures/sample-utf-16-le-bom.txt')));
    self::assertSame(2, UTF8::isUtf16(file_get_contents(__DIR__ . '/fixtures/sample-utf-16-be-bom.txt')));
  }

  public function testIsUtf32()
  {
    $testArray = array(
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
    );

    $conter = 0;
    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::is_utf32($actual), 'error by - ' . $conter . ' :' . $actual);
      $conter++;
    }

    $conter = 0;
    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::isUtf32($actual), 'error by - ' . $conter . ' :' . $actual);
      $conter++;
    }

    self::assertSame(false, UTF8::isUtf32(file_get_contents(__DIR__ . '/fixtures/utf-8.txt')));
    self::assertSame(false, UTF8::isUtf32(file_get_contents(__DIR__ . '/fixtures/utf-8-bom.txt')));

    self::assertSame(1, UTF8::isUtf32(file_get_contents(__DIR__ . '/fixtures/sample-utf-32-le-bom.txt')));
    self::assertSame(2, UTF8::isUtf32(file_get_contents(__DIR__ . '/fixtures/sample-utf-32-be-bom.txt')));
  }

  public function testIsUtf8()
  {
    $testArray = array(
        1                                                                                  => true,
        -1                                                                                 => true,
        'κ'                                                                                => true,
        ''                                                                                 => true,
        ' '                                                                                => true,
        "\n"                                                                               => true,
        'abc'                                                                              => true,
        'abcöäü'                                                                           => true,
        '白'                                                                                => true,
        'សាកល្បង!'                                                                         => true,
        'דיעס איז אַ פּרובירן!'                                                            => true,
        'Штампи іст Ейн тест!'                                                             => true,
        'Штампы гіст Эйн тэст!'                                                            => true,
        '測試！'                                                                              => true,
        'ການທົດສອບ!'                                                                       => true,
        'Iñtërnâtiônàlizætiøn'                                                             => true,
        'ABC 123'                                                                          => true,
        "Iñtërnâtiôn\xE9àlizætiøn"                                                         => false,
        '𐤹'                                                                               => true,
        // https://www.compart.com/de/unicode/U+10939
        '𐠅'                                                                               => true,
        // https://www.compart.com/de/unicode/U+10805
        'ますだ, よしひこ'                                                                        => true,
        '𐭠 𐭡 𐭢 𐭣 𐭤 𐭥 𐭦 𐭧 𐭨 𐭩 𐭪 𐭫 𐭬 𐭭 𐭮 𐭯 𐭰 𐭱 𐭲 𐭸 𐭹 𐭺 𐭻 𐭼 𐭽 𐭾 𐭿' => true,
        // http://www.sonderzeichen.de/Inscriptional_Pahlavi/Unicode-10B7F.html
        "\xf0\x28\x8c\x28"                                                                 => false,
        "this is an invalid char '\xE9' here"                                              => false,
        "\xC3\xB1"                                                                         => true,
        "Iñtërnâtiônàlizætiøn \xC3\x28 Iñtërnâtiônàlizætiøn"                               => false,
        "Iñtërnâtiônàlizætiøn\xA0\xA1Iñtërnâtiônàlizætiøn"                                 => false,
        "Iñtërnâtiônàlizætiøn\xE2\x82\xA1Iñtërnâtiônàlizætiøn"                             => true,
        "Iñtërnâtiônàlizætiøn\xE2\x28\xA1Iñtërnâtiônàlizætiøn"                             => false,
        "Iñtërnâtiônàlizætiøn\xE2\x82\x28Iñtërnâtiônàlizætiøn"                             => false,
        "Iñtërnâtiônàlizætiøn\xF0\x90\x8C\xBCIñtërnâtiônàlizætiøn"                         => true,
        "Iñtërnâtiônàlizætiøn\xF0\x28\x8C\xBCIñtërnâtiônàlizætiøn"                         => false,
        "Iñtërnâtiônàlizætiøn\xf8\xa1\xa1\xa1\xa1Iñtërnâtiônàlizætiøn"                     => false,
        "Iñtërnâtiônàlizætiøn\xFC\xA1\xA1\xA1\xA1\xA1Iñtërnâtiônàlizætiøn"                 => false,
        "\xC3\x28"                                                                         => false,
        "\xA0\xA1"                                                                         => false,
        "\xE2\x82\xA1"                                                                     => true,
        "\xE2\x28\xA1"                                                                     => false,
        "\xE2\x82\x28"                                                                     => false,
        "\xF0\x90\x8C\xBC"                                                                 => true,
        "\xF0\x28\x8C\xBC"                                                                 => false,
        "\xF0\x90\x28\xBC"                                                                 => false,
        "\xF0\x28\x8C\x28"                                                                 => false,
        "\xF8\xA1\xA1\xA1\xA1"                                                             => false,
        "\xFC\xA1\xA1\xA1\xA1\xA1"                                                         => false,
    );

    $conter = 0;
    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::is_utf8($actual), 'error by - ' . $conter . ' :' . $actual);
      $conter++;
    }

    $conter = 0;
    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::isUtf8($actual), 'error by - ' . $conter . ' :' . $actual);
      $conter++;
    }

    self::assertSame(false, UTF8::is_utf8(file_get_contents(__DIR__ . '/fixtures/utf-16-be.txt'), true));
    self::assertSame(false, UTF8::is_utf8(file_get_contents(__DIR__ . '/fixtures/utf-16-be-bom.txt'), true));
  }

  public function testJsonDecode()
  {
    $testArray = array(
        '{"recipe_id":-1,"recipe_created":"","recipe_title":"FSDFSDF","recipe_description":"","recipe_yield":0,"recipe_prepare_time":"fast","recipe_image":"","recipe_legal":0,"recipe_license":0,"recipe_category_id":[],"recipe_category_name":[],"recipe_variety_id":[],"recipe_variety_name":[],"recipe_tag_id":[],"recipe_tag_name":[],"recipe_instruction_id":[],"recipe_instruction_text":[],"recipe_ingredient_id":[],"recipe_ingredient_name":[],"recipe_ingredient_amount":[],"recipe_ingredient_unit":[],"errorArray":{"recipe_legal":"error","recipe_license":"error","recipe_description":"error","recipe_yield":"error","recipe_category_name":"error","recipe_tag_name":"error","recipe_instruction_text":"error","recipe_ingredient_amount":"error","recipe_ingredient_unit":"error"},"errorMessage":"[[Bitte f\u00fclle die rot markierten Felder korrekt aus.]]","db":{"query_count":15}}'                            => '{"recipe_id":-1,"recipe_created":"","recipe_title":"FSDFSDF","recipe_description":"","recipe_yield":0,"recipe_prepare_time":"fast","recipe_image":"","recipe_legal":0,"recipe_license":0,"recipe_category_id":[],"recipe_category_name":[],"recipe_variety_id":[],"recipe_variety_name":[],"recipe_tag_id":[],"recipe_tag_name":[],"recipe_instruction_id":[],"recipe_instruction_text":[],"recipe_ingredient_id":[],"recipe_ingredient_name":[],"recipe_ingredient_amount":[],"recipe_ingredient_unit":[],"errorArray":{"recipe_legal":"error","recipe_license":"error","recipe_description":"error","recipe_yield":"error","recipe_category_name":"error","recipe_tag_name":"error","recipe_instruction_text":"error","recipe_ingredient_amount":"error","recipe_ingredient_unit":"error"},"errorMessage":"[[Bitte f\u00fclle die rot markierten Felder korrekt aus.]]","db":{"query_count":15}}',
        '{"recipe_id":-1,"recipe_created":"","recipe_title":"FSDFSκόσμε' . "\xa0\xa1" . '-öäüDF","recipe_description":"","recipe_yield":0,"recipe_prepare_time":"fast","recipe_image":"","recipe_legal":0,"recipe_license":0,"recipe_category_id":[],"recipe_category_name":[],"recipe_variety_id":[],"recipe_variety_name":[],"recipe_tag_id":[],"recipe_tag_name":[],"recipe_instruction_id":[],"recipe_instruction_text":[],"recipe_ingredient_id":[],"recipe_ingredient_name":[],"recipe_ingredient_amount":[],"recipe_ingredient_unit":[],"errorArray":{"recipe_legal":"error","recipe_license":"error","recipe_description":"error","recipe_yield":"error","recipe_category_name":"error","recipe_tag_name":"error","recipe_instruction_text":"error","recipe_ingredient_amount":"error","recipe_ingredient_unit":"error"},"errorMessage":"[[Bitte f\u00fclle die rot markierten Felder korrekt aus.]]","db":{"query_count":15}}' => '{"recipe_id":-1,"recipe_created":"","recipe_title":"FSDFSκόσμε ¡-öäüDF","recipe_description":"","recipe_yield":0,"recipe_prepare_time":"fast","recipe_image":"","recipe_legal":0,"recipe_license":0,"recipe_category_id":[],"recipe_category_name":[],"recipe_variety_id":[],"recipe_variety_name":[],"recipe_tag_id":[],"recipe_tag_name":[],"recipe_instruction_id":[],"recipe_instruction_text":[],"recipe_ingredient_id":[],"recipe_ingredient_name":[],"recipe_ingredient_amount":[],"recipe_ingredient_unit":[],"errorArray":{"recipe_legal":"error","recipe_license":"error","recipe_description":"error","recipe_yield":"error","recipe_category_name":"error","recipe_tag_name":"error","recipe_instruction_text":"error","recipe_ingredient_amount":"error","recipe_ingredient_unit":"error"},"errorMessage":"[[Bitte fülle die rot markierten Felder korrekt aus.]]","db":{"query_count":15}}',
        '{"array":[1,2,3],"boolean":true,"null":null,"number":123,"object":{"a":"b","c":"d","e":"f"},"string":"Hello World | öäü"}'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     => '{"array":[1,2,3],"boolean":true,"null":null,"number":123,"object":{"a":"b","c":"d","e":"f"},"string":"Hello World | öäü"}',
        '{"array":[1,"¥","ä"]}'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         => '{"array":[1,"¥","ä"]}',
    );

    foreach ($testArray as $before => $after) {
      self::assertSame($after, UTF8::json_decode(UTF8::json_encode($before)));
    }

    // ---

    // add more tests
    $testArray['{"array":[1,2,3],,...}}'] = false;
    $testArray['{"test": 123}'] = true;
    $testArray['[{"test": 123}]'] = true;

    foreach ($testArray as $before => $after) {
      self::assertSame(
          ($after === false ? false : true),
          UTF8::is_json($before),
          'tested: ' . $before
      );
    }

    // ----

    $expected = new stdClass();
    $expected->array = array(1, '¥', 'ä');
    self::assertEquals($expected, UTF8::json_decode('{"array":[1,"¥","ä"]}'));

    // ----

    self::assertEquals(array(1, '¥', 'ä'), UTF8::json_decode('[1,"\u00a5","\u00e4"]'));
  }

  public function testJsonEncode()
  {
    $test = new stdClass();
    $test->array = array(1, '¥', 'ä');
    self::assertEquals('{"array":[1,"\u00a5","\u00e4"]}', UTF8::json_encode($test));

    // ----

    self::assertEquals('[1,"\u00a5","\u00e4"]', UTF8::json_encode(array(1, '¥', 'ä')));
  }

  public function testLcfirst()
  {
    self::assertSame('öäü', UTF8::lcfirst('Öäü'));
    self::assertSame('κόσμε', UTF8::lcfirst('Κόσμε'));
    self::assertSame('aBC-ÖÄÜ-中文空白', UTF8::lcfirst('ABC-ÖÄÜ-中文空白'));
    self::assertSame('ñTËRNÂTIÔNÀLIZÆTIØN', UTF8::lcfirst('ÑTËRNÂTIÔNÀLIZÆTIØN'));
    self::assertSame('ñTËRNÂTIÔNÀLIZÆTIØN', UTF8::lcfirst('ñTËRNÂTIÔNÀLIZÆTIØN'));
    self::assertSame('', UTF8::lcfirst(''));
    self::assertSame(' ', UTF8::lcfirst(' '));
    self::assertSame("\t test", UTF8::lcfirst("\t test"));
    self::assertSame('ñ', UTF8::lcfirst('Ñ'));
    self::assertSame("ñTËRN\nâtiônàlizætiøn", UTF8::lcfirst("ÑTËRN\nâtiônàlizætiøn"));
    self::assertSame('deja', UTF8::lcfirst('Deja'));
    self::assertSame('σσς', UTF8::lcfirst('Σσς'));
    self::assertSame('dEJa', UTF8::lcfirst('dEJa'));
    self::assertSame('σσΣ', UTF8::lcfirst('σσΣ'));
  }

  public function testLtrim()
  {
    $tests = array(
        '  -ABC-中文空白-  ' => '-ABC-中文空白-  ',
        '      - ÖÄÜ- '  => '- ÖÄÜ- ',
        'öäü'            => 'öäü',
        1                => '1',
        ''               => '',
        null             => '',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::ltrim($before));
      self::assertSame($after, ltrim($before));
    }

    self::assertSame('tërnâtiônàlizætiøn', UTF8::ltrim('ñtërnâtiônàlizætiøn', 'ñ'));
    self::assertSame('tërnâtiônàlizætiøn', ltrim('ñtërnâtiônàlizætiøn', 'ñ'));

    self::assertSame('Iñtërnâtiônàlizætiøn', UTF8::ltrim('Iñtërnâtiônàlizætiøn', 'ñ'));
    self::assertSame('Iñtërnâtiônàlizætiøn', ltrim('Iñtërnâtiônàlizætiøn', 'ñ'));

    self::assertSame('', UTF8::ltrim(''));
    self::assertSame('', ltrim(''));

    self::assertSame('', UTF8::ltrim(' '));
    self::assertSame('', ltrim(' '));

    self::assertSame('Iñtërnâtiônàlizætiøn', UTF8::ltrim('/Iñtërnâtiônàlizætiøn', '/'));
    self::assertSame('Iñtërnâtiônàlizætiøn', ltrim('/Iñtërnâtiônàlizætiøn', '/'));

    self::assertSame('Iñtërnâtiônàlizætiøn', UTF8::ltrim('Iñtërnâtiônàlizætiøn', '^s'));
    self::assertSame('Iñtërnâtiônàlizætiøn', ltrim('Iñtërnâtiônàlizætiøn', '^s'));

    self::assertSame("\nñtërnâtiônàlizætiøn", UTF8::ltrim("ñ\nñtërnâtiônàlizætiøn", 'ñ'));
    self::assertSame("\nñtërnâtiônàlizætiøn", ltrim("ñ\nñtërnâtiônàlizætiøn", 'ñ'));

    self::assertSame('tërnâtiônàlizætiøn', UTF8::ltrim("ñ\nñtërnâtiônàlizætiøn", "ñ\n"));
    self::assertSame('tërnâtiônàlizætiøn', ltrim("ñ\nñtërnâtiônàlizætiøn", "ñ\n"));

    // UTF-8

    self::assertSame("#string#\xc2\xa0\xe1\x9a\x80", UTF8::ltrim("\xe2\x80\x83\x20#string#\xc2\xa0\xe1\x9a\x80"));
  }

  public function testMax()
  {
    $tests = array(
        'abc-äöü-中文空白'         => '空',
        'öäü'                  => 'ü',
        'öäü test öäü'         => 'ü',
        'ÖÄÜ'                  => 'Ü',
        '中文空白'                 => '空',
        'Intërnâtiônàlizætiøn' => 'ø',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::max($before));
    }

    self::assertSame('ü', UTF8::max(array('öäü', 'test', 'abc')));
  }

  public function testMaxChrWidth()
  {
    $testArray = array(
        '中文空白'                 => 3,
        'Intërnâtiônàlizætiøn' => 2,
        'öäü'                  => 2,
        'abc'                  => 1,
        ''                     => 0,
        null                   => 0,
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::max_chr_width($actual));
    }
  }

  public function testMin()
  {
    $tests = array(
        'abc-äöü-中文空白' => '-',
        'öäü'          => 'ä',
        'öäü test öäü' => ' ',
        'ÖÄÜ'          => 'Ä',
        '中文空白'         => '中',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::min($before));
    }

    self::assertSame('a', UTF8::min(array('öäü', 'test', 'abc')));
  }

  public function testNormalizeEncoding()
  {
    $tests = array(
        'ISO'          => 'ISO-8859-1',
        'UTF8'         => 'UTF-8',
        'WINDOWS-1251' => 'WINDOWS-1251',
        ''             => false,
        'Utf-8'        => 'UTF-8',
        'UTF-8'        => 'UTF-8',
        'ISO-8859-5'   => 'ISO-8859-5',
        false          => false,
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::normalizeEncoding($before), 'tested: ' . $before);
    }
  }

  public function testNormalizeMsword()
  {
    $tests = array(
        ''                                                                         => '',
        ' '                                                                        => ' ',
        '«foobar»'                                                                 => '"foobar"',
        '中文空白 ‟'                                                                   => '中文空白 "',
        "<ㅡㅡ></ㅡㅡ><div>…</div><input type='email' name='user[email]' /><a>wtf</a>" => "<ㅡㅡ></ㅡㅡ><div>...</div><input type='email' name='user[email]' /><a>wtf</a>",
        '– DÃ¼sseldorf —'                                                          => '- DÃ¼sseldorf -',
        '„Abcdef…”'                                                                => '"Abcdef..."',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::normalize_msword($before));
    }
  }

  public function testNormalizeWhitespace()
  {
    $tests = array(
        ''                                                                                    => '',
        ' '                                                                                   => ' ',
        ' foo ' . "\xe2\x80\xa8" . ' öäü' . "\xe2\x80\xa9"                                    => ' foo   öäü ',
        "«\xe2\x80\x80foobar\xe2\x80\x80»"                                                    => '« foobar »',
        '中文空白 ‟'                                                                              => '中文空白 ‟',
        "<ㅡㅡ></ㅡㅡ><div>\xe2\x80\x85</div><input type='email' name='user[email]' /><a>wtf</a>" => "<ㅡㅡ></ㅡㅡ><div> </div><input type='email' name='user[email]' /><a>wtf</a>",
        "–\xe2\x80\x8bDÃ¼sseldorf\xe2\x80\x8b—"                                               => '– DÃ¼sseldorf —',
        "„Abcdef\xe2\x81\x9f”"                                                                => '„Abcdef ”',
        " foo\t foo "                                                                         => ' foo	 foo ',
    );

    for ($i = 0; $i < 2; $i++) { // keep this loop for simple performance tests
      foreach ($tests as $before => $after) {
        self::assertSame($after, UTF8::normalize_whitespace($before));
      }
    }

    // replace "non breaking space"
    self::assertSame('abc- -öäü- -', UTF8::normalize_whitespace("abc-\xc2\xa0-öäü-\xe2\x80\xaf-\xE2\x80\xAC"));

    // keep "non breaking space"
    self::assertSame("abc-\xc2\xa0-öäü- -", UTF8::normalize_whitespace("abc-\xc2\xa0-öäü-\xe2\x80\xaf-\xE2\x80\xAC", true));

    // ... and keep "bidirectional text chars"
    self::assertSame("abc-\xc2\xa0-öäü- -\xE2\x80\xAC", UTF8::normalize_whitespace("abc-\xc2\xa0-öäü-\xe2\x80\xaf-\xE2\x80\xAC", true, true));
  }

  public function testNumberFormat()
  {
    self::assertSame('1.23', UTF8::number_format('1.234567', 2, '.', ''));
    self::assertSame('1,3', UTF8::number_format('1.298765', 1, ',', ''));
    self::assertSame('1,0', UTF8::number_format('1', 1, ',', ''));
    self::assertSame('0,0', UTF8::number_format('foo', 1, ',', ''));
    self::assertSame('0', UTF8::number_format(''));
  }

  public function testOrd()
  {
    $nbsp = UTF8::html_entity_decode('&nbsp;');

    $testArray = array(
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
    );

    for ($i = 0; $i < 2; $i++) { // keep this loop for simple performance tests
      foreach ($testArray as $actual => $expected) {
        self::assertSame($expected, UTF8::ord($actual));
      }
    }
  }

  public function testParseStr()
  {
    // test-string
    $str = "Iñtërnâtiôn\xE9àlizætiøn=測試&arr[]=foo+測試&arr[]=ການທົດສອບ";

    $result = UTF8::parse_str($str, $array, true);

    self::assertSame(true, $result);

    // bug is already reported: https://github.com/facebook/hhvm/issues/6340
    if (defined('HHVM_VERSION') === false) {
      self::assertSame('foo 測試', $array['arr'][0]);
      self::assertSame('ການທົດສອບ', $array['arr'][1]);
    }

    // bug is already reported: https://github.com/facebook/hhvm/issues/6340
    // -> mb_parse_str not parsing multidimensional array
    if (defined('HHVM_VERSION') === false) {
      self::assertSame('測試', $array['Iñtërnâtiônàlizætiøn']);
    }

    // ---

    // test-string
    $str = 'Iñtërnâtiônàlizætiøn=測試&arr[]=foo+測試&arr[]=ການທົດສອບ';

    $result = UTF8::parse_str($str, $array, false);

    self::assertSame(true, $result);

    // bug is already reported: https://github.com/facebook/hhvm/issues/6340
    if (defined('HHVM_VERSION') === false) {
      self::assertSame('foo 測試', $array['arr'][0]);
      self::assertSame('ການທົດສອບ', $array['arr'][1]);
    }

    // bug is already reported: https://github.com/facebook/hhvm/issues/6340
    // -> mb_parse_str not parsing multidimensional array
    if (defined('HHVM_VERSION') === false) {
      self::assertSame('測試', $array['Iñtërnâtiônàlizætiøn']);
    }

    // ---

    $str = 'foo[]=bar&test=lall';

    $foo = '123';
    $test = '';

    /** @noinspection NonSecureParseStrUsageInspection */
    parse_str($str); // <- you don't need to use the second parameter, but it is more then recommended!!!

    self::assertSame($foo, array(0 => 'bar'));
    self::assertSame($test, 'lall');
    self::assertSame($str, 'foo[]=bar&test=lall');

    $foo = '123';
    $test = '';

    if (Bootup::is_php('7.1') === false) {
      /** @noinspection NonSecureParseStrUsageInspection */
      /** @noinspection PhpParamsInspection */
      UTF8::parse_str($str); // <- you need to use the second parameter!!!

      self::assertSame($foo, '123');
      self::assertSame($test, '');
      self::assertSame($str, 'foo[]=bar&test=lall');
    }

    // ---

    $str = '[]';

    $result = UTF8::parse_str($str, $array);

    // bug reported (hhvm (3.6.6~precise)): https://github.com/facebook/hhvm/issues/7247
    if (defined('HHVM_VERSION') === false) {
      self::assertSame(false, $result);
    }
  }

  public function testRange()
  {
    // --- UTF-8 chars

    $expected = array('κ', 'ι', 'θ', 'η', 'ζ',);
    self::assertSame($expected, UTF8::range('κ', 'ζ'));
    self::assertSame(0, count(UTF8::range('κ', '')));

    // --- code points

    $expected = array('₧', '₨', '₩');
    self::assertSame($expected, UTF8::range(8359, 8361));

    // --- HEX

    $expected = array(' ', '!', '"', '#');
    self::assertSame($expected, UTF8::range("\x20", "\x23"));
  }

  public function testRawurldecode()
  {
    $testArray = array(
        'W%F6bse' => 'Wöbse',
        'Ã' => 'Ã',
        'Ã¤' => 'ä',
        ' ' => ' ',
        '' => '',
        "\n" => "\n",
        "\u00ed" => 'í',
        'tes%20öäü%20\u00edtest+test' => 'tes öäü ítest+test',
        'test+test@foo.bar' => 'test+test@foo.bar',
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
        'Hello%2BWorld%2B%253E%2Bhow%2Bare%2Byou%253F' => 'Hello+World+>+how+are+you?',
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
        'https://foo.bar/tpl_preview.php?pid=122&json=%7B%22recipe_id%22%3A-1%2C%22recipe_created%22%3A%22%22%2C%22recipe_title%22%3A%22vxcvxc%22%2C%22recipe_description%22%3A%22%22%2C%22recipe_yield%22%3A0%2C%22recipe_prepare_time%22%3A0%2C%22recipe_image%22%3A%22%22%2C%22recipe_legal%22%3A0%2C%22recipe_live%22%3A0%2C%22recipe_user_guid%22%3A%22%22%2C%22recipe_category_id%22%3A%5B%5D%2C%22recipe_category_name%22%3A%5B%5D%2C%22recipe_variety_id%22%3A%5B%5D%2C%22recipe_variety_name%22%3A%5B%5D%2C%22recipe_tag_id%22%3A%5B%5D%2C%22recipe_tag_name%22%3A%5B%5D%2C%22recipe_instruction_id%22%3A%5B%5D%2C%22recipe_instruction_text%22%3A%5B%5D%2C%22recipe_ingredient_id%22%3A%5B%5D%2C%22recipe_ingredient_name%22%3A%5B%5D%2C%22recipe_ingredient_amount%22%3A%5B%5D%2C%22recipe_ingredient_unit%22%3A%5B%5D%2C%22formMatchingArray%22%3A%7B%22unites%22%3A%5B%22Becher%22%2C%22Beete%22%2C%22Beutel%22%2C%22Blatt%22%2C%22Bl%5Cu00e4tter%22%2C%22Bund%22%2C%22B%5Cu00fcndel%22%2C%22cl%22%2C%22cm%22%2C%22dicke%22%2C%22dl%22%2C%22Dose%22%2C%22Dose%5C%2Fn%22%2C%22d%5Cu00fcnne%22%2C%22Ecke%28n%29%22%2C%22Eimer%22%2C%22einige%22%2C%22einige+Stiele%22%2C%22EL%22%2C%22EL%2C+geh%5Cu00e4uft%22%2C%22EL%2C+gestr.%22%2C%22etwas%22%2C%22evtl.%22%2C%22extra%22%2C%22Fl%5Cu00e4schchen%22%2C%22Flasche%22%2C%22Flaschen%22%2C%22g%22%2C%22Glas%22%2C%22Gl%5Cu00e4ser%22%2C%22gr.+Dose%5C%2Fn%22%2C%22gr.+Fl.%22%2C%22gro%5Cu00dfe%22%2C%22gro%5Cu00dfen%22%2C%22gro%5Cu00dfer%22%2C%22gro%5Cu00dfes%22%2C%22halbe%22%2C%22Halm%28e%29%22%2C%22Handvoll%22%2C%22K%5Cu00e4stchen%22%2C%22kg%22%2C%22kl.+Bund%22%2C%22kl.+Dose%5C%2Fn%22%2C%22kl.+Glas%22%2C%22kl.+Kopf%22%2C%22kl.+Scheibe%28n%29%22%2C%22kl.+St%5Cu00fcck%28e%29%22%2C%22kl.Flasche%5C%2Fn%22%2C%22kleine%22%2C%22kleinen%22%2C%22kleiner%22%2C%22kleines%22%2C%22Knolle%5C%2Fn%22%2C%22Kopf%22%2C%22K%5Cu00f6pfe%22%2C%22K%5Cu00f6rner%22%2C%22Kugel%22%2C%22Kugel%5C%2Fn%22%2C%22Kugeln%22%2C%22Liter%22%2C%22m.-gro%5Cu00dfe%22%2C%22m.-gro%5Cu00dfer%22%2C%22m.-gro%5Cu00dfes%22%2C%22mehr%22%2C%22mg%22%2C%22ml%22%2C%22Msp.%22%2C%22n.+B.%22%2C%22Paar%22%2C%22Paket%22%2C%22Pck.%22%2C%22Pkt.%22%2C%22Platte%5C%2Fn%22%2C%22Port.%22%2C%22Prise%28n%29%22%2C%22Prisen%22%2C%22Prozent+%25%22%2C%22Riegel%22%2C%22Ring%5C%2Fe%22%2C%22Rippe%5C%2Fn%22%2C%22Rolle%28n%29%22%2C%22Sch%5Cu00e4lchen%22%2C%22Scheibe%5C%2Fn%22%2C%22Schuss%22%2C%22Spritzer%22%2C%22Stange%5C%2Fn%22%2C%22St%5Cu00e4ngel%22%2C%22Stiel%5C%2Fe%22%2C%22Stiele%22%2C%22St%5Cu00fcck%28e%29%22%2C%22Tafel%22%2C%22Tafeln%22%2C%22Tasse%22%2C%22Tasse%5C%2Fn%22%2C%22Teil%5C%2Fe%22%2C%22TL%22%2C%22TL+%28geh%5Cu00e4uft%29%22%2C%22TL+%28gestr.%29%22%2C%22Topf%22%2C%22Tropfen%22%2C%22Tube%5C%2Fn%22%2C%22T%5Cu00fcte%5C%2Fn%22%2C%22viel%22%2C%22wenig%22%2C%22W%5Cu00fcrfel%22%2C%22Wurzel%22%2C%22Wurzel%5C%2Fn%22%2C%22Zehe%5C%2Fn%22%2C%22Zweig%5C%2Fe%22%5D%2C%22yield%22%3A%7B%221%22%3A%221+Portion%22%2C%222%22%3A%222+Portionen%22%2C%223%22%3A%223+Portionen%22%2C%224%22%3A%224+Portionen%22%2C%225%22%3A%225+Portionen%22%2C%226%22%3A%226+Portionen%22%2C%227%22%3A%227+Portionen%22%2C%228%22%3A%228+Portionen%22%2C%229%22%3A%229+Portionen%22%2C%2210%22%3A%2210+Portionen%22%2C%2211%22%3A%2211+Portionen%22%2C%2212%22%3A%2212+Portionen%22%7D%2C%22prepare_time%22%3A%7B%221%22%3A%22schnell%22%2C%222%22%3A%22mittel%22%2C%223%22%3A%22aufwendig%22%7D%2C%22category%22%3A%7B%221%22%3A%22Vorspeise%22%2C%222%22%3A%22Suppe%22%2C%223%22%3A%22Salat%22%2C%224%22%3A%22Hauptspeise%22%2C%225%22%3A%22Beilage%22%2C%226%22%3A%22Nachtisch%5C%2FDessert%22%2C%227%22%3A%22Getr%5Cu00e4nke%22%2C%228%22%3A%22B%5Cu00fcffet%22%2C%229%22%3A%22Fr%5Cu00fchst%5Cu00fcck%5C%2FBrunch%22%7D%2C%22variety%22%3A%7B%221%22%3A%22Basmati+Reis%22%2C%222%22%3A%22Basmati+%26amp%3B+Wild+Reis%22%2C%223%22%3A%22R%5Cu00e4ucherreis%22%2C%224%22%3A%22Jasmin+Reis%22%2C%225%22%3A%221121+Basmati+Wunderreis%22%2C%226%22%3A%22Spitzen+Langkorn+Reis%22%2C%227%22%3A%22Wildreis%22%2C%228%22%3A%22Naturreis%22%2C%229%22%3A%22Sushi+Reis%22%7D%2C%22tag--ingredient%22%3A%7B%221%22%3A%22Eier%22%2C%222%22%3A%22Gem%5Cu00fcse%22%2C%223%22%3A%22Getreide%22%2C%224%22%3A%22Fisch%22%2C%225%22%3A%22Fleisch%22%2C%226%22%3A%22Meeresfr%5Cu00fcchte%22%2C%227%22%3A%22Milchprodukte%22%2C%228%22%3A%22Obst%22%2C%229%22%3A%22Salat%22%7D%2C%22tag--preparation%22%3A%7B%2210%22%3A%22Backen%22%2C%2211%22%3A%22Blanchieren%22%2C%2212%22%3A%22Braten%5C%2FSchmoren%22%2C%2213%22%3A%22D%5Cu00e4mpfen%5C%2FD%5Cu00fcnsten%22%2C%2214%22%3A%22Einmachen%22%2C%2215%22%3A%22Frittieren%22%2C%2216%22%3A%22Gratinieren%5C%2F%5Cu00dcberbacken%22%2C%2217%22%3A%22Grillen%22%2C%2218%22%3A%22Kochen%22%7D%2C%22tag--kitchen%22%3A%7B%2219%22%3A%22Afrikanisch%22%2C%2220%22%3A%22Alpenk%5Cu00fcche%22%2C%2221%22%3A%22Asiatisch%22%2C%2222%22%3A%22Deutsch+%28regional%29%22%2C%2223%22%3A%22Franz%5Cu00f6sisch%22%2C%2224%22%3A%22Mediterran%22%2C%2225%22%3A%22Orientalisch%22%2C%2226%22%3A%22Osteurop%5Cu00e4isch%22%2C%2227%22%3A%22Skandinavisch%22%2C%2228%22%3A%22S%5Cu00fcdamerikanisch%22%2C%2229%22%3A%22US-Amerikanisch%22%2C%2230%22%3A%22%22%7D%2C%22tag--difficulty%22%3A%7B%2231%22%3A%22Einfach%22%2C%2232%22%3A%22Mittelschwer%22%2C%2233%22%3A%22Anspruchsvoll%22%7D%2C%22tag--feature%22%3A%7B%2234%22%3A%22Gut+vorzubereiten%22%2C%2235%22%3A%22Kalorienarm+%5C%2F+leicht%22%2C%2236%22%3A%22Klassiker%22%2C%2237%22%3A%22Preiswert%22%2C%2238%22%3A%22Raffiniert%22%2C%2239%22%3A%22Vegetarisch+%5C%2F+Vegan%22%2C%2240%22%3A%22Vitaminreich%22%2C%2241%22%3A%22Vollwert%22%2C%2242%22%3A%22%22%7D%2C%22tag%22%3A%7B%221%22%3A%22Eier%22%2C%222%22%3A%22Gem%5Cu00fcse%22%2C%223%22%3A%22Getreide%22%2C%224%22%3A%22Fisch%22%2C%225%22%3A%22Fleisch%22%2C%226%22%3A%22Meeresfr%5Cu00fcchte%22%2C%227%22%3A%22Milchprodukte%22%2C%228%22%3A%22Obst%22%2C%229%22%3A%22Salat%22%2C%2210%22%3A%22Backen%22%2C%2211%22%3A%22Blanchieren%22%2C%2212%22%3A%22Braten%5C%2FSchmoren%22%2C%2213%22%3A%22D%5Cu00e4mpfen%5C%2FD%5Cu00fcnsten%22%2C%2214%22%3A%22Einmachen%22%2C%2215%22%3A%22Frittieren%22%2C%2216%22%3A%22Gratinieren%5C%2F%5Cu00dcberbacken%22%2C%2217%22%3A%22Grillen%22%2C%2218%22%3A%22Kochen%22%2C%2219%22%3A%22Afrikanisch%22%2C%2220%22%3A%22Alpenk%5Cu00fcche%22%2C%2221%22%3A%22Asiatisch%22%2C%2222%22%3A%22Deutsch+%28regional%29%22%2C%2223%22%3A%22Franz%5Cu00f6sisch%22%2C%2224%22%3A%22Mediterran%22%2C%2225%22%3A%22Orientalisch%22%2C%2226%22%3A%22Osteurop%5Cu00e4isch%22%2C%2227%22%3A%22Skandinavisch%22%2C%2228%22%3A%22S%5Cu00fcdamerikanisch%22%2C%2229%22%3A%22US-Amerikanisch%22%2C%2230%22%3A%22%22%2C%2231%22%3A%22Einfach%22%2C%2232%22%3A%22Mittelschwer%22%2C%2233%22%3A%22Anspruchsvoll%22%2C%2234%22%3A%22Gut+vorzubereiten%22%2C%2235%22%3A%22Kalorienarm+%5C%2F+leicht%22%2C%2236%22%3A%22Klassiker%22%2C%2237%22%3A%22Preiswert%22%2C%2238%22%3A%22Raffiniert%22%2C%2239%22%3A%22Vegetarisch+%5C%2F+Vegan%22%2C%2240%22%3A%22Vitaminreich%22%2C%2241%22%3A%22Vollwert%22%2C%2242%22%3A%22%22%7D%7D%2C%22errorArray%22%3A%7B%22recipe_prepare_time%22%3A%22error%22%2C%22recipe_yield%22%3A%22error%22%2C%22recipe_category_name%22%3A%22error%22%2C%22recipe_tag_name%22%3A%22error%22%2C%22recipe_instruction_text%22%3A%22error%22%2C%22recipe_ingredient_name%22%3A%22error%22%7D%2C%22errorMessage%22%3A%22Bitte+f%5Cu00fclle+die+rot+markierten+Felder+korrekt+aus.%22%2C%22db%22%3A%7B%22query_count%22%3A20%7D%7D' => 'https://foo.bar/tpl_preview.php?pid=122&json={"recipe_id":-1,"recipe_created":"","recipe_title":"vxcvxc","recipe_description":"","recipe_yield":0,"recipe_prepare_time":0,"recipe_image":"","recipe_legal":0,"recipe_live":0,"recipe_user_guid":"","recipe_category_id":[],"recipe_category_name":[],"recipe_variety_id":[],"recipe_variety_name":[],"recipe_tag_id":[],"recipe_tag_name":[],"recipe_instruction_id":[],"recipe_instruction_text":[],"recipe_ingredient_id":[],"recipe_ingredient_name":[],"recipe_ingredient_amount":[],"recipe_ingredient_unit":[],"formMatchingArray":{"unites":["Becher","Beete","Beutel","Blatt","Blätter","Bund","Bündel","cl","cm","dicke","dl","Dose","Dose\/n","dünne","Ecke(n)","Eimer","einige","einige+Stiele","EL","EL,+gehäuft","EL,+gestr.","etwas","evtl.","extra","Fläschchen","Flasche","Flaschen","g","Glas","Gläser","gr.+Dose\/n","gr.+Fl.","große","großen","großer","großes","halbe","Halm(e)","Handvoll","Kästchen","kg","kl.+Bund","kl.+Dose\/n","kl.+Glas","kl.+Kopf","kl.+Scheibe(n)","kl.+Stück(e)","kl.Flasche\/n","kleine","kleinen","kleiner","kleines","Knolle\/n","Kopf","Köpfe","Körner","Kugel","Kugel\/n","Kugeln","Liter","m.-große","m.-großer","m.-großes","mehr","mg","ml","Msp.","n.+B.","Paar","Paket","Pck.","Pkt.","Platte\/n","Port.","Prise(n)","Prisen","Prozent+%","Riegel","Ring\/e","Rippe\/n","Rolle(n)","Schälchen","Scheibe\/n","Schuss","Spritzer","Stange\/n","Stängel","Stiel\/e","Stiele","Stück(e)","Tafel","Tafeln","Tasse","Tasse\/n","Teil\/e","TL","TL+(gehäuft)","TL+(gestr.)","Topf","Tropfen","Tube\/n","Tüte\/n","viel","wenig","Würfel","Wurzel","Wurzel\/n","Zehe\/n","Zweig\/e"],"yield":{"1":"1+Portion","2":"2+Portionen","3":"3+Portionen","4":"4+Portionen","5":"5+Portionen","6":"6+Portionen","7":"7+Portionen","8":"8+Portionen","9":"9+Portionen","10":"10+Portionen","11":"11+Portionen","12":"12+Portionen"},"prepare_time":{"1":"schnell","2":"mittel","3":"aufwendig"},"category":{"1":"Vorspeise","2":"Suppe","3":"Salat","4":"Hauptspeise","5":"Beilage","6":"Nachtisch\/Dessert","7":"Getränke","8":"Büffet","9":"Frühstück\/Brunch"},"variety":{"1":"Basmati+Reis","2":"Basmati+&+Wild+Reis","3":"Räucherreis","4":"Jasmin+Reis","5":"1121+Basmati+Wunderreis","6":"Spitzen+Langkorn+Reis","7":"Wildreis","8":"Naturreis","9":"Sushi+Reis"},"tag--ingredient":{"1":"Eier","2":"Gemüse","3":"Getreide","4":"Fisch","5":"Fleisch","6":"Meeresfrüchte","7":"Milchprodukte","8":"Obst","9":"Salat"},"tag--preparation":{"10":"Backen","11":"Blanchieren","12":"Braten\/Schmoren","13":"Dämpfen\/Dünsten","14":"Einmachen","15":"Frittieren","16":"Gratinieren\/Überbacken","17":"Grillen","18":"Kochen"},"tag--kitchen":{"19":"Afrikanisch","20":"Alpenküche","21":"Asiatisch","22":"Deutsch+(regional)","23":"Französisch","24":"Mediterran","25":"Orientalisch","26":"Osteuropäisch","27":"Skandinavisch","28":"Südamerikanisch","29":"US-Amerikanisch","30":""},"tag--difficulty":{"31":"Einfach","32":"Mittelschwer","33":"Anspruchsvoll"},"tag--feature":{"34":"Gut+vorzubereiten","35":"Kalorienarm+\/+leicht","36":"Klassiker","37":"Preiswert","38":"Raffiniert","39":"Vegetarisch+\/+Vegan","40":"Vitaminreich","41":"Vollwert","42":""},"tag":{"1":"Eier","2":"Gemüse","3":"Getreide","4":"Fisch","5":"Fleisch","6":"Meeresfrüchte","7":"Milchprodukte","8":"Obst","9":"Salat","10":"Backen","11":"Blanchieren","12":"Braten\/Schmoren","13":"Dämpfen\/Dünsten","14":"Einmachen","15":"Frittieren","16":"Gratinieren\/Überbacken","17":"Grillen","18":"Kochen","19":"Afrikanisch","20":"Alpenküche","21":"Asiatisch","22":"Deutsch+(regional)","23":"Französisch","24":"Mediterran","25":"Orientalisch","26":"Osteuropäisch","27":"Skandinavisch","28":"Südamerikanisch","29":"US-Amerikanisch","30":"","31":"Einfach","32":"Mittelschwer","33":"Anspruchsvoll","34":"Gut+vorzubereiten","35":"Kalorienarm+\/+leicht","36":"Klassiker","37":"Preiswert","38":"Raffiniert","39":"Vegetarisch+\/+Vegan","40":"Vitaminreich","41":"Vollwert","42":""}},"errorArray":{"recipe_prepare_time":"error","recipe_yield":"error","recipe_category_name":"error","recipe_tag_name":"error","recipe_instruction_text":"error","recipe_ingredient_name":"error"},"errorMessage":"Bitte+fülle+die+rot+markierten+Felder+korrekt+aus.","db":{"query_count":20}}',
        '<a href="&#38&#35&#49&#48&#54&#38&#35&#57&#55&#38&#35&#49&#49&#56&#38&#35&#57&#55&#38&#35&#49&#49&#53&#38&#35&#57&#57&#38&#35&#49&#49&#52&#38&#35&#49&#48&#53&#38&#35&#49&#49&#50&#38&#35&#49&#49&#54&#38&#35&#53&#56&#38&#35&#57&#57&#38&#35&#49&#49&#49&#38&#35&#49&#49&#48&#38&#35&#49&#48&#50&#38&#35&#49&#48&#53&#38&#35&#49&#49&#52&#38&#35&#49&#48&#57&#38&#35&#52&#48&#38&#35&#52&#57&#38&#35&#52&#49">Clickhere</a>' => '<a href="javascript:confirm(1)">Clickhere</a>',
        '🐶 | 💩 | 🐱 | 🐸 | 🌀 | ❤ | &#x267F; | &#x26CE; | ' => '🐶 | 💩 | 🐱 | 🐸 | 🌀 | ❤ | ♿ | ⛎ | ', // view-source:https://twitter.github.io/twemoji/preview.html
    );

    foreach ($testArray as $before => $after) {
      self::assertSame($after, UTF8::rawurldecode($before), 'testing: ' . $before);
    }
  }

  public function testRemoveBom()
  {
    $testBom = array(
        "\xEF\xBB\xBFΜπορώ να φάω σπασμένα γυαλιά χωρίς να πάθω τίποτα",
        "\xFE\xFFΜπορώ να φάω σπασμένα γυαλιά χωρίς να πάθω τίποτα",
        "\xFF\xFEΜπορώ να φάω σπασμένα γυαλιά χωρίς να πάθω τίποτα",
        "\x00\x00\xFE\xFFΜπορώ να φάω σπασμένα γυαλιά χωρίς να πάθω τίποτα",
        "\xFF\xFE\x00\x00Μπορώ να φάω σπασμένα γυαλιά χωρίς να πάθω τίποτα",
    );

    foreach ($testBom as $count => &$test) {

      $test = UTF8::remove_bom($test);

      self::assertSame(
          'Μπορώ να φάω σπασμένα γυαλιά χωρίς να πάθω τίποτα',
          $test,
          'error by ' . $count
      );

      $test = UTF8::add_bom_to_string($test);
      self::assertSame(true, UTF8::string_has_bom($test));
      self::assertSame(true, UTF8::hasBom($test)); // alias
    }
  }

  public function testRemoveDuplicates()
  {
    $testArray = array(
        'öäü-κόσμεκόσμε-äöü'   => array(
            'öäü-κόσμε-äöü' => 'κόσμε',
        ),
        'äöüäöüäöü-κόσμεκόσμε' => array(
            'äöü-κόσμε' => array(
                'äöü',
                'κόσμε',
            ),
        ),
    );

    foreach ($testArray as $actual => $data) {
      foreach ($data as $expected => $filter) {
        self::assertSame($expected, UTF8::remove_duplicates($actual, $filter));
      }
    }
  }

  public function testRemoveInvisibleCharacters()
  {
    $testArray = array(
        "κόσ\0με"                                                                          => 'κόσμε',
        "Κόσμε\x20"                                                                        => 'Κόσμε ',
        "öäü-κόσμ\x0εκόσμε-äöü"                                                            => 'öäü-κόσμεκόσμε-äöü',
        'öäü-κόσμεκόσμε-äöüöäü-κόσμεκόσμε-äöü'                                             => 'öäü-κόσμεκόσμε-äöüöäü-κόσμεκόσμε-äöü',
        "äöüäöüäöü-κόσμεκόσμεäöüäöüäöü\xe1\x9a\x80κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε"          => 'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε',
        'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-Κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε' => 'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-Κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε',
        '  '                                                                               => '  ',
        ''                                                                                 => '',
    );

    foreach ($testArray as $before => $after) {
      self::assertSame($after, UTF8::remove_invisible_characters($before), 'error by ' . $before);
    }

    self::assertSame('κόσ?με 	%00 | tes%20öäü%20\u00edtest', UTF8::remove_invisible_characters("κόσ\0με 	%00 | tes%20öäü%20\u00edtest", false, '?'));
    self::assertSame('κόσμε 	 | tes%20öäü%20\u00edtest', UTF8::remove_invisible_characters("κόσ\0με 	%00 | tes%20öäü%20\u00edtest", true, ''));
  }

  public function testReplaceDiamondQuestionMark()
  {
    $tests = array(
        ''                                                                         => '',
        ' '                                                                        => ' ',
        '�'                                                                        => '',
        '中文空白 �'                                                                   => '中文空白 ',
        "<ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a>" => "<ㅡㅡ></ㅡㅡ><div></div><input type='email' name='user[email]' /><a>wtf</a>",
        'DÃ¼�sseldorf'                                                             => 'DÃ¼sseldorf',
        'Abcdef'                                                                   => 'Abcdef',
        "\xC0\x80foo|&#65533;"                                                     => 'foo|&#65533;',
    );

    $counter = 0;
    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::replace_diamond_question_mark($before, ''), 'tested: ' . $before . ' | counter: ' . $counter);
      ++$counter;
    }

    // ---

    $tests = array(
        "Iñtërnâtiôn\xe9àlizætiøn"                                         => 'Iñtërnâtiônàlizætiøn',
        // invalid UTF-8 string
        "Iñtërnâtiônàlizætiøn\xfc\xa1\xa1\xa1\xa1\xa1Iñtërnâtiônàlizætiøn" => 'IñtërnâtiônàlizætiønIñtërnâtiônàlizætiøn',
        // invalid six octet sequence
        "Iñtërnâtiônàlizætiøn\xf0\x28\x8c\xbcIñtërnâtiônàlizætiøn"         => 'Iñtërnâtiônàlizætiøn(Iñtërnâtiônàlizætiøn',
        // invalid four octet sequence
        "Iñtërnâtiônàlizætiøn \xc3\x28 Iñtërnâtiônàlizætiøn"               => 'Iñtërnâtiônàlizætiøn ( Iñtërnâtiônàlizætiøn',
        // invalid two octet sequence
        "this is an invalid char '\xe9' here"                              => "this is an invalid char '' here",
        // invalid ASCII string
        "Iñtërnâtiônàlizætiøn\xa0\xa1Iñtërnâtiônàlizætiøn"                 => 'IñtërnâtiônàlizætiønIñtërnâtiônàlizætiøn',
        // invalid id between two and three
        "Iñtërnâtiônàlizætiøn\xf8\xa1\xa1\xa1\xa1Iñtërnâtiônàlizætiøn"     => 'IñtërnâtiônàlizætiønIñtërnâtiônàlizætiøn',
        //  invalid five octet sequence
        "Iñtërnâtiônàlizætiøn\xe2\x82\x28Iñtërnâtiônàlizætiøn"             => 'Iñtërnâtiônàlizætiøn(Iñtërnâtiônàlizætiøn',
        // invalid three octet sequence third
        "Iñtërnâtiônàlizætiøn\xe2\x28\xa1Iñtërnâtiônàlizætiøn"             => 'Iñtërnâtiônàlizætiøn(Iñtërnâtiônàlizætiøn',
        // invalid three octet sequence second
    );

    $counter = 0;

    if (Bootup::is_php('5.4')) { // invalid UTF-8 + PHP 5.3 => error
      foreach ($tests as $before => $after) {
        self::assertSame($after, UTF8::replace_diamond_question_mark($before, ''), 'tested: ' . $before . ' | counter: ' . $counter);
        ++$counter;
      }
    }

    // ---

    if (Bootup::is_php('5.4')) { // invalid UTF-8 + PHP 5.3 => error
      self::assertSame('Iñtërnâtiônàlizætiøn??Iñtërnâtiônàlizætiøn', UTF8::replace_diamond_question_mark("Iñtërnâtiônàlizætiøn\xa0\xa1Iñtërnâtiônàlizætiøn", '?', true));
    }

    // ---

    self::assertSame("Iñtërnâtiônàlizætiøn\xa0\xa1Iñtërnâtiônàlizætiøn", UTF8::replace_diamond_question_mark("Iñtërnâtiônàlizætiøn\xa0\xa1Iñtërnâtiônàlizætiøn", '?', false));
  }

  public function testRtrim()
  {
    $tests = array(
        '-ABC-中文空白-  '        => '-ABC-中文空白-',
        '- ÖÄÜ-             ' => '- ÖÄÜ-',
        'öäü'                 => 'öäü',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::rtrim($before));
    }

    self::assertSame('Iñtërnâtiônàlizæti', UTF8::rtrim('Iñtërnâtiônàlizætiø', 'ø'));
    self::assertSame('Iñtërnâtiônàlizætiøn ', UTF8::rtrim('Iñtërnâtiônàlizætiøn ', 'ø'));
    self::assertSame('', UTF8::rtrim(''));
    self::assertSame("Iñtërnâtiônàlizætiø\n", UTF8::rtrim("Iñtërnâtiônàlizætiø\nø", 'ø'));
    self::assertSame('Iñtërnâtiônàlizæti', UTF8::rtrim("Iñtërnâtiônàlizætiø\nø", "\nø"));
    self::assertSame("\xe2\x80\x83\x20#string#", UTF8::rtrim("\xe2\x80\x83\x20#string#\xc2\xa0\xe1\x9a\x80"));
  }

  public function testSingleChrHtmlEncode()
  {
    $testArray = array(
        '{' => '&#123;',
        '中' => '&#20013;',
        'κ' => '&#954;',
        'ö' => '&#246;',
        ''  => '',
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::single_chr_html_encode($actual));
    }

    self::assertSame('a', UTF8::single_chr_html_encode('a', true));
  }

  public function testSplit()
  {
    for ($i = 0; $i <= 2; $i++) { // keep this loop for simple performance tests
      self::assertSame(
          array(
              '中',
              '文',
              '空',
              '白',
          ),
          UTF8::split('中文空白')
      );
      self::assertSame(
          array(
              '中文',
              '空白',
          ),
          UTF8::split('中文空白', 2)
      );
      self::assertSame(array('中文空白'), UTF8::split('中文空白', 4));
      self::assertSame(array('中文空白'), UTF8::split('中文空白', 8));

      self::assertSame(array('1234'), UTF8::split(1234, 8));
    }
  }

  public function testStrDetectEncoding()
  {
    $tests = array(
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
        '1'                             => 'ASCII', // ASCII
        decbin(324546)                  => 'ASCII', // ASCII
        01                              => 'ASCII', // ASCII
    );

    for ($i = 0; $i <= 2; $i++) { // keep this loop for simple performance tests
      foreach ($tests as $before => $after) {
        self::assertSame($after, UTF8::str_detect_encoding($before), 'value: ' . $before);
      }
    }

    $testString = file_get_contents(__DIR__ . '/fixtures/sample-html.txt');
    self::assertContains('UTF-8', UTF8::str_detect_encoding($testString));

    $testString = file_get_contents(__DIR__ . '/fixtures/latin.txt');
    self::assertContains('ISO-8859-1', UTF8::str_detect_encoding($testString));

    $testString = file_get_contents(__DIR__ . '/fixtures/iso-8859-7.txt');
    self::assertContains('ISO-8859-1', UTF8::str_detect_encoding($testString)); // ?
  }

  public function testStrEndsWith()
  {
    $str = 'BeginMiddleΚόσμε';

    $tests = array(
        'Κόσμε' => true,
        'κόσμε' => false,
        ''      => false,
        ' '     => false,
        false   => false,
        'ε'     => true,
        'End'   => false,
        'end'   => false,
    );

    foreach ($tests as $test => $result) {
      self::assertSame($result, UTF8::str_ends_with($str, $test), 'tested: ' . $test);
    }
  }

  public function testStrIEndsWith()
  {
    $str = 'BeginMiddleΚόσμε';

    $tests = array(
        'Κόσμε' => true,
        'κόσμε' => true,
        ''      => false,
        ' '     => false,
        false   => false,
        'ε'     => true,
        'End'   => false,
        'end'   => false,
    );

    foreach ($tests as $test => $result) {
      self::assertSame($result, UTF8::str_iends_with($str, $test), 'tested: ' . $test);
    }
  }

  public function testStrIStartsWith()
  {
    $str = 'ΚόσμεMiddleEnd';

    $tests = array(
        'Κόσμε' => true,
        'κόσμε' => true,
        ''      => false,
        ' '     => false,
        false   => false,
        'Κ'     => true,
        'End'   => false,
        'end'   => false,
    );

    foreach ($tests as $test => $result) {
      self::assertSame($result, UTF8::str_istarts_with($str, $test), 'tested: ' . $test);
    }
  }

  public function testStrLimit()
  {
    $testArray = array(
        array('this...', 'this is a test', 5, '...'),
        array('this is...', 'this is öäü-foo test', 8, '...'),
        array('fòô', 'fòô bàř fòô', 6, ''),
        array('fòô bàř', 'fòô bàř fòô', 8, ''),
        array('fòô bàř', "fòô bàř fòô \x00", 8, ''),
        array('', "fòô bàř \x00fòô", 0, ''),
        array('fòô bàř', "fòô bàř \x00fòô", -1, ''),
        array('fòô bàř白', "fòô bàř \x00fòô", 8, '白'),
        array('白白', '白白 白白', 3),
        array('白白白', '白白白', 100),
        array('', '', 1),
        array('', null, 1),
        array('', false, 1),
    );

    foreach ($testArray as $test) {
      self::assertSame($test[0], UTF8::str_limit_after_word($test[1], $test[2], $test[3]), 'tested: ' . $test[1]);
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

    self::assertSame($expectedString, $actualString);

    self::assertSame('中文空白______', UTF8::str_pad('中文空白', 10, '_', STR_PAD_RIGHT));
    self::assertSame('______中文空白', UTF8::str_pad('中文空白', 10, '_', STR_PAD_LEFT));
    self::assertSame('___中文空白___', UTF8::str_pad('中文空白', 10, '_', STR_PAD_BOTH));

    $toPad = '<IñtërnëT>'; // 10 characters
    $padding = 'ø__'; // 4 characters

    self::assertSame($toPad . '          ', UTF8::str_pad($toPad, 20));
    self::assertSame('          ' . $toPad, UTF8::str_pad($toPad, 20, ' ', STR_PAD_LEFT));
    self::assertSame('     ' . $toPad . '     ', UTF8::str_pad($toPad, 20, ' ', STR_PAD_BOTH));

    self::assertSame($toPad, UTF8::str_pad($toPad, 10));
    self::assertSame('5char', str_pad('5char', 4)); // str_pos won't truncate input string
    self::assertSame($toPad, UTF8::str_pad($toPad, 8));

    self::assertSame($toPad . 'ø__ø__ø__ø', UTF8::str_pad($toPad, 20, $padding, STR_PAD_RIGHT));
    self::assertSame('ø__ø__ø__ø' . $toPad, UTF8::str_pad($toPad, 20, $padding, STR_PAD_LEFT));
    self::assertSame('ø__ø_' . $toPad . 'ø__ø_', UTF8::str_pad($toPad, 20, $padding, STR_PAD_BOTH));
  }

  public function testStrRepeat()
  {
    $tests = array(
        ''                                                                         => '',
        ' '                                                                        => '                 ',
        '�'                                                                        => '�����������������',
        '中文空白 �'                                                                   => '中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �中文空白 �',
        "<ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a>" => "<ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a><ㅡㅡ></ㅡㅡ><div>�</div><input type='email' name='user[email]' /><a>wtf</a>",
        'DÃ¼�sseldorf'                                                             => 'DÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorfDÃ¼�sseldorf',
        'Abcdef'                                                                   => 'AbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdefAbcdef',
        "°~\xf0\x90\x28\xbc"                                                       => '°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼°~ð(¼',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::str_repeat($before, 17));
    }
  }

  public function testStrShuffle()
  {
    $testArray = array(
        'this is a test',
        'this is öäü-foo test',
        'fòô bàř fòô',
    );

    foreach ($testArray as $test) {
      self::assertEquals(
          array(),
          array_diff(
              UTF8::str_split($test),
              UTF8::str_split(UTF8::str_shuffle($test))
          ), 'tested: ' . $test
      );
    }
  }

  public function testStrSort()
  {
    $tests = array(
        ''               => '',
        '  -ABC-中文空白-  ' => '    ---ABC中文白空',
        '      - ÖÄÜ- '  => '        --ÄÖÜ',
        'öäü'            => 'äöü',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::str_sort($before));
    }

    $tests = array(
        '  -ABC-中文空白-  ' => '空白文中CBA---    ',
        '      - ÖÄÜ- '  => 'ÜÖÄ--        ',
        'öäü'            => 'üöä',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::str_sort($before, false, true));
    }

    $tests = array(
        '    '           => ' ',
        '  -ABC-中文空白-  ' => ' -ABC中文白空',
        '      - ÖÄÜ- '  => ' -ÄÖÜ',
        'öäü'            => 'äöü',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::str_sort($before, true));
    }

    $tests = array(
        '  -ABC-中文空白-  ' => '空白文中CBA- ',
        '      - ÖÄÜ- '  => 'ÜÖÄ- ',
        'öäü'            => 'üöä',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::str_sort($before, true, true));
    }
  }

  public function testStrStartsWith()
  {
    $str = 'ΚόσμεMiddleEnd';

    $tests = array(
        'Κόσμε' => true,
        'κόσμε' => false,
        ''      => false,
        ' '     => false,
        false   => false,
        'Κ'     => true,
        'End'   => false,
        'end'   => false,
    );

    foreach ($tests as $test => $result) {
      self::assertSame($result, UTF8::str_starts_with($str, $test), 'tested: ' . $test);
    }
  }

  public function testStrToBinary()
  {
    $tests = array(
        0    => '110000',
        '1'  => '110001',
        '~'  => '1111110',
        '§'  => '1100001010100111',
        'ሇ'  => '111000011000100010000111',
        '😃' => '11110000100111111001100010000011',

    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::str_to_binary($before), 'tested: ' . $before);
    }

    foreach ($tests as $before => $after) {
      self::assertSame((string)$before, UTF8::binary_to_str(UTF8::str_to_binary($before)), 'tested: ' . $before);
    }
  }

  public function testStrToWords()
  {
    self::assertSame(array('iñt', 'ërn'), UTF8::str_to_words('iñt ërn I', '', false, 1));
    self::assertSame(array('iñt', 'ërn', 'I'), UTF8::str_to_words('iñt ërn I', '', true));
    self::assertSame(array('', 'iñt', ' ', 'ërn', '',), UTF8::str_to_words('iñt ërn'));
    self::assertSame(array('', 'âti', "\n ", 'ônà', ''), UTF8::str_to_words("âti\n ônà"));
    self::assertSame(array('', '中文空白', ' ', 'oöäü#s', ''), UTF8::str_to_words('中文空白 oöäü#s', '#'));
    self::assertSame(array('', 'foo', ' ', 'oo', ' ', 'oöäü', '#', 's', ''), UTF8::str_to_words('foo oo oöäü#s', ''));
    self::assertSame(array(''), UTF8::str_to_words(''));
  }

  public function testStr_split()
  {
    self::assertSame(
        array(
            'd',
            'é',
            'j',
            'à',
        ),
        UTF8::str_split('déjà', 1)
    );
    self::assertSame(
        array(
            'dé',
            'jà',
        ),
        UTF8::str_split('déjà', 2)
    );
  }

  public function testString()
  {
    self::assertSame('', UTF8::string(array()));
    self::assertSame(
        'öäü',
        UTF8::string(
            array(
                246,
                228,
                252,
            )
        )
    );
    self::assertSame(
        'ㅡㅡ',
        UTF8::string(
            array(
                12641,
                12641,
            )
        )
    );
    self::assertSame('中文空白', UTF8::string(UTF8::codepoints('中文空白')));
  }

  public function testStringHasBom()
  {
    $testArray = array(
        ' '                    => false,
        ''                     => false,
        UTF8::bom() . 'κ'      => true,
        'abc'                  => false,
        UTF8::bom() . 'abcöäü' => true,
        '白'                    => false,
        UTF8::bom()            => true,
    );

    $utf8_bom = file_get_contents(__DIR__ . '/fixtures/sample-utf-8-bom.txt');
    $utf8_bom_only = file_get_contents(__DIR__ . '/fixtures/sample-utf-8-bom-only.txt');
    $utf16_be_bom = file_get_contents(__DIR__ . '/fixtures/sample-utf-16-be-bom.txt');
    $utf16_be_bom_only = file_get_contents(__DIR__ . '/fixtures/sample-utf-16-be-bom-only.txt');
    $utf16_le_bom = file_get_contents(__DIR__ . '/fixtures/sample-utf-16-le-bom.txt');
    $utf16_le_bom_only = file_get_contents(__DIR__ . '/fixtures/sample-utf-16-le-bom-only.txt');
    $utf32_be_bom = file_get_contents(__DIR__ . '/fixtures/sample-utf-32-be-bom.txt');
    $utf32_be_bom_only = file_get_contents(__DIR__ . '/fixtures/sample-utf-32-be-bom-only.txt');
    $utf32_le_bom = file_get_contents(__DIR__ . '/fixtures/sample-utf-32-le-bom.txt');
    $utf32_le_bom_only = file_get_contents(__DIR__ . '/fixtures/sample-utf-32-le-bom-only.txt');

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
      self::assertSame($expected, UTF8::string_has_bom($actual), 'error by ' . $actual);
    }
  }

  public function testStripTags()
  {
    $tests = array(
        null                                                                      => '',
        ''                                                                        => '',
        ' '                                                                       => ' ',
        1                                                                         => '1',
        '2'                                                                       => '2',
        'Abcdef'                                                                  => 'Abcdef',
        '<nav>DÃ¼sseldorf</nav>'                                                  => 'DÃ¼sseldorf',
        "<ㅡㅡ></ㅡㅡ><div></div><input type='email' name='user[email]' /><a>wtf</a>" => 'wtf',
        '<nav>中文空白 </nav>'                                                        => '中文空白 ',
        "<span>κόσμε\xa0\xa1</span>-<span>öäü</span>öäü"                          => 'κόσμε-öäüöäü',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::strip_tags($before, null, true));
    }

    // ---

    $tests = array(
        null                                                                      => '',
        ''                                                                        => '',
        ' '                                                                       => ' ',
        1                                                                         => '1',
        '2'                                                                       => '2',
        'Abcdef'                                                                  => 'Abcdef',
        '<nav>DÃ¼sseldorf</nav>'                                                  => 'DÃ¼sseldorf',
        "<ㅡㅡ></ㅡㅡ><div></div><input type='email' name='user[email]' /><a>wtf</a>" => 'wtf',
        '<nav>中文空白 </nav>'                                                        => '中文空白 ',
        '<span>κόσμε</span>-<span>öäü</span>öäü'                                  => '<span>κόσμε</span>-<span>öäü</span>öäü',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::strip_tags($before, '<span>', false));
    }
  }

  public function testStripos()
  {
    for ($i = 0; $i <= 2; $i++) { // keep this loop for simple performance tests
      self::assertSame(false, UTF8::stripos('DÉJÀ', 'ä'));
      self::assertSame(false, UTF8::stripos('DÉJÀ', ' '));
      self::assertSame(false, UTF8::stripos('DÉJÀ', ''));
      self::assertSame(false, UTF8::stripos('', 'ä'));
      self::assertSame(false, UTF8::stripos('', ' '));
      self::assertSame(false, UTF8::stripos('', ''));
      self::assertSame(1, UTF8::stripos('aςσb', 'ΣΣ'));
      self::assertSame(3, UTF8::stripos('DÉJÀ', 'à'));
      self::assertSame(4, UTF8::stripos('öäü-κόσμε-κόσμε-κόσμε', 'Κ'));
      self::assertSame(4, UTF8::stripos('ABC-ÖÄÜ-中文空白-中文空白', 'ö'));
      self::assertSame(5, UTF8::stripos('Test κόσμε test κόσμε', 'Κ'));
      self::assertSame(16, UTF8::stripos('der Straße nach Paris', 'Paris'));

      // ---

      self::assertSame(3, UTF8::stripos('DÉJÀ', 'à'));
      self::assertSame(3, UTF8::stripos('DÉJÀ', 'à', 1));
      self::assertSame(3, UTF8::stripos('DÉJÀ', 'à', 1, 'UTF-8'));
      self::assertSame(false, UTF8::stripos('DÉJÀ', 'à', 1, 'ISO'));
    }
  }

  public function testStrirpos()
  {
    self::assertSame(false, strripos('', ''));
    self::assertSame(false, strripos(' ', ''));
    self::assertSame(false, strripos('', ' '));
    self::assertSame(false, strripos('DJ', ''));
    self::assertSame(false, strripos('', 'J'));

    self::assertSame(1, UTF8::strripos('aσσb', 'ΣΣ'));
    self::assertSame(1, UTF8::strripos('aςσb', 'ΣΣ'));

    self::assertSame(1, strripos('DJ', 'J'));
    self::assertSame(1, UTF8::strripos('DJ', 'J'));
    self::assertSame(3, UTF8::strripos('DÉJÀ', 'à'));
    self::assertSame(4, UTF8::strripos('ÀDÉJÀ', 'à'));
    self::assertSame(6, UTF8::strripos('κόσμε-κόσμε', 'Κ'));
    self::assertSame(7, UTF8::strripos('中文空白-ÖÄÜ-中文空白', 'ü'));
    self::assertSame(11, UTF8::strripos('test κόσμε κόσμε test', 'Κ'));
    self::assertSame(13, UTF8::strripos('ABC-ÖÄÜ-中文空白-中文空白', '中'));
  }

  public function testStrlen()
  {
    // string with UTF-16 (LE) BOM + valid UTF-8 && invalid UTF-8
    $string = "\xFF\xFE" . 'string <strong>with utf-8 chars åèä</strong>' . "\xa0\xa1" . ' - doo-bee doo-bee dooh';

    if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
      self::assertSame(71, strlen($string));
    } else {
      self::assertSame(74, strlen($string));
    }

    self::assertSame(74, UTF8::strlen($string, '8bit'));
    self::assertSame(67, UTF8::strlen($string, 'UTF-8', true));

    if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
      self::assertSame(71, UTF8::strlen($string));
      self::assertSame(71, UTF8::strlen($string, 'UTF-8', false));
    }

    $string_test1 = strip_tags($string);
    $string_test2 = UTF8::strip_tags($string);

    if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
      self::assertSame(54, strlen($string_test1));
    } else {
      self::assertSame(57, strlen($string_test1)); // not correct
    }

    if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
      self::assertSame(54, UTF8::strlen($string_test2, 'UTF-8', false));
    }

    self::assertSame(50, UTF8::strlen($string_test2, 'UTF-8', true));

    $testArray = array(
        '⠊⠀⠉⠁⠝⠀⠑⠁⠞⠀⠛⠇⠁⠎⠎⠀⠁⠝⠙⠀⠊⠞'    => 22,
        "<a href='κόσμε'>κόσμε</a>" => 25,
        '<白>'                       => 3,
        'öäü'                       => 3,
        ' '                         => 1,
        ''                          => 0,
        1                           => 1,
        -1                          => 2,
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::strlen($actual), $actual);
    }

    $testArray = array(
        "<a href='test'>tester</a>" => 25,
        '<a>'                       => 3,
        'abc'                       => 3,
        ' '                         => 1,
        ''                          => 0,
        1                           => 1,
        -1                          => 2,
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, strlen($actual), $actual);
    }
  }

  public function testStrnatcasecmp()
  {
    self::assertSame(0, UTF8::strnatcasecmp('Hello world 中文空白!', 'Hello WORLD 中文空白!'));
    self::assertSame(1, UTF8::strnatcasecmp('Hello world 中文空白!', 'Hello WORLD 中文空白'));
    self::assertSame(-1, UTF8::strnatcasecmp('Hello world 中文空白', 'Hello WORLD 中文空白!'));
    self::assertSame(-1, UTF8::strnatcasecmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!'));
    self::assertSame(1, UTF8::strcasecmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!')); // strcasecmp
    self::assertSame(1, UTF8::strnatcasecmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'));
    self::assertSame(-1, UTF8::strcasecmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!')); // strcasecmp
    self::assertSame(0, UTF8::strnatcasecmp('10Hello world 中文空白!', '10Hello world 中文空白!'));
    self::assertSame(0, UTF8::strnatcasecmp('Hello world 中文空白!', 'Hello WORLD 中文空白!'));
  }

  public function testStrnatcmp()
  {
    self::assertSame(1, UTF8::strnatcmp('Hello world 中文空白!', 'Hello WORLD 中文空白!'));
    self::assertSame(1, UTF8::strnatcmp('Hello world 中文空白!', 'Hello WORLD 中文空白'));
    self::assertSame(1, UTF8::strnatcmp('Hello world 中文空白', 'Hello WORLD 中文空白!'));
    self::assertSame(-1, UTF8::strnatcmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!'));
    self::assertSame(1, UTF8::strcmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!')); // strcmp
    self::assertSame(1, UTF8::strnatcmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'));
    self::assertSame(-1, UTF8::strcmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!')); // strcmp
    self::assertSame(0, UTF8::strnatcmp('10Hello world 中文空白!', '10Hello world 中文空白!'));
    self::assertSame(1, UTF8::strnatcmp('Hello world 中文空白!', 'Hello WORLD 中文空白!'));
  }

  public function testStrncasecmp()
  {
    $tests = array(
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
    );

    foreach ($tests as $before => $after) {
      if ($after < 0) {
        self::assertSame(true, UTF8::strncasecmp($before, 'ü', 10) < 0, 'tested: ' . $before);
      } elseif ($after > 0) {
        self::assertSame(true, UTF8::strncasecmp($before, 'ü', 10) > 0, 'tested: ' . $before);
      } else {
        self::assertSame(true, UTF8::strncasecmp($before, 'ü', 10) === 0, 'tested: ' . $before);
      }

    }
  }

  public function testStrncmp()
  {
    $tests = array(
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
    );

    foreach ($tests as $before => $after) {
      if ($after < 0) {
        self::assertSame(true, UTF8::strncmp($before, 'ü', 10) < 0, 'tested: ' . $before);
      } elseif ($after > 0) {
        self::assertSame(true, UTF8::strncmp($before, 'ü', 10) > 0, 'tested: ' . $before);
      } else {
        self::assertSame(true, UTF8::strncmp($before, 'ü', 10) === 0, 'tested: ' . $before);
      }
    }
  }

  public function testStrpbrk()
  {
    // php compatible tests

    $text = 'This is a Simple text.';

    self::assertSame(false, strpbrk($text, ''));
    self::assertSame(strpbrk($text, ''), UTF8::strpbrk($text, ''));

    self::assertSame(false, strpbrk('', 'mi'));
    self::assertSame(strpbrk('', 'mi'), UTF8::strpbrk('', 'mi'));

    // this echoes "is is a Simple text." because 'i' is matched first
    self::assertSame('is is a Simple text.', strpbrk($text, 'mi'));
    self::assertSame(strpbrk($text, 'mi'), UTF8::strpbrk($text, 'mi'));

    // this echoes "Simple text." because chars are case sensitive
    self::assertSame('Simple text.', strpbrk($text, 'S'));
    self::assertSame('Simple text.', UTF8::strpbrk($text, 'S'));

    // ---

    // UTF-8
    $text = 'Hello -中文空白-';
    self::assertSame('白-', UTF8::strpbrk($text, '白'));

    // empty input
    self::assertSame(false, UTF8::strpbrk('', 'z'));

    // empty char-list
    self::assertSame(false, UTF8::strpbrk($text, ''));

    // not matching char-list
    $text = 'Hello -中文空白-';
    self::assertSame(false, UTF8::strpbrk($text, 'z'));
  }

  public function testStrpos()
  {
    for ($i = 0; $i <= 2; $i++) { // keep this loop for simple performance tests

      // php compatible tests

      self::assertSame(false, strpos('abc', ''));
      self::assertSame(false, UTF8::strpos('abc', ''));

      self::assertSame(false, strpos('abc', 'd'));
      self::assertSame(false, UTF8::strpos('abc', 'd'));

      self::assertSame(false, strpos('abc', 'a', 3));
      self::assertSame(false, UTF8::strpos('abc', 'a', 3));

      self::assertSame(false, strpos('abc', 'a', 1));
      self::assertSame(false, UTF8::strpos('abc', 'a', 1));

      self::assertSame(1, strpos('abc', 'b', 1));
      self::assertSame(1, UTF8::strpos('abc', 'b', 1));

      self::assertSame(false, strpos('abc', 'b', -1));
      self::assertSame(false, UTF8::strpos('abc', 'b', -1));

      self::assertSame(1, strpos('abc', 'b', 0));
      self::assertSame(1, UTF8::strpos('abc', 'b', 0));

      // UTF-8 tests

      if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
        self::assertSame(16, strpos('der Straße nach Paris', 'Paris'));
      } else {
        self::assertSame(17, strpos('der Straße nach Paris', 'Paris')); // not correct
      }

      self::assertSame(17, UTF8::strpos('der Straße nach Paris', 'Paris', 0, '8bit')); // not correct
      self::assertSame(16, UTF8::strpos('der Straße nach Paris', 'Paris'));

      if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
        self::assertSame(1, strpos('한국어', '국'));
      } else {
        self::assertSame(3, strpos('한국어', '국')); // not correct
      }

      self::assertSame(1, UTF8::strpos('한국어', '국'));

      self::assertSame(0, UTF8::strpos('κόσμε-κόσμε-κόσμε', 'κ'));
      self::assertSame(7, UTF8::strpos('test κόσμε test κόσμε', 'σ'));
      self::assertSame(8, UTF8::strpos('ABC-ÖÄÜ-中文空白-中文空白', '中'));

      // --- invalid UTF-8

      if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
        self::assertSame(15, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白'));

        if (Bootup::is_php('7.1') === false) {
          self::assertSame(false, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', -8));
        } else {
          self::assertSame(20, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', -8));
        }

        self::assertSame(false, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', -4));
        self::assertSame(false, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', -1));
        self::assertSame(15, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', 0));
        self::assertSame(15, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', 4));
        self::assertSame(15, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', 8));
        self::assertSame(14, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', 0, 'UTF-8', true));
        self::assertSame(15, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', 0, 'UTF-8', false));
        self::assertSame(26, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', 0, 'ISO', true));
        self::assertSame(27, UTF8::strpos('ABC-ÖÄÜ-💩-' . "\xc3\x28" . '中文空白-中文空白' . "\xf0\x28\x8c\x28" . 'abc', '白', 0, 'ISO', false));

        // ISO

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
          self::assertSame(16, strpos('der Straße nach Paris', 'Paris', 0));
        } else {
          self::assertSame(17, strpos('der Straße nach Paris', 'Paris', 0)); // not correct
        }

        self::assertSame(17, UTF8::strpos('der Straße nach Paris', 'Paris', 0, 'ISO')); // not correct

        if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
          self::assertSame(1, strpos('한국어', '국', 0));
        } else {
          self::assertSame(3, strpos('한국어', '국', 0)); // not correct
        }

        self::assertSame(3, UTF8::strpos('한국어', '국', 0, 'ISO')); // not correct
      }
    }
  }

  public function testStrrchr()
  {
    $testArray = array(
        'κόσμε'                                                                            => 'κόσμε',
        'Κόσμε'                                                                            => false,
        'öäü-κόσμεκόσμε-äöü'                                                               => 'κόσμε-äöü',
        'öäü-κόσμεκόσμε-äöüöäü-κόσμεκόσμε-äöü'                                             => 'κόσμε-äöü',
        'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε'                     => 'κόσμε',
        'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-Κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε' => 'κόσμε',
        '  '                                                                               => false,
        ''                                                                                 => false,
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::strrchr($actual, 'κόσμε'), 'error by ' . $actual);
    }

    // --- UTF-8

    self::assertSame('κόσμε-äöü', UTF8::strrchr('κόσμεκόσμε-äöü', 'κόσμε', false, 'UTF-8'));
    self::assertSame(false, UTF8::strrchr('Aκόσμεκόσμε-äöü', 'aκόσμε', false, 'UTF-8'));

    self::assertSame('κόσμε', UTF8::strrchr('κόσμεκόσμε-äöü', 'κόσμε', true, 'UTF-8'));
    self::assertSame(false, UTF8::strrchr('Aκόσμεκόσμε-äöü', 'aκόσμε', true, 'UTF-8'));

    // --- ISO

    if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
      self::assertSame('κόσμε-äöü', UTF8::strrchr('κόσμεκόσμε-äöü', 'κόσμε', false, 'ISO'));
      self::assertSame(false, UTF8::strrchr('Aκόσμεκόσμε-äöü', 'aκόσμε', false, 'ISO'));

      self::assertSame('κόσμε', UTF8::strrchr('κόσμεκόσμε-äöü', 'κόσμε', true, 'ISO'));
      self::assertSame(false, UTF8::strrchr('Aκόσμεκόσμε-äöü', 'aκόσμε', true, 'ISO'));
    }
  }

  public function testStrrev()
  {
    $testArray = array(
        'κ-öäü'  => 'üäö-κ',
        'abc'    => 'cba',
        'abcöäü' => 'üäöcba',
        '-白-'    => '-白-',
        ''       => '',
        ' '      => ' ',
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::strrev($actual), 'error by ' . $actual);
    }
  }

  public function testStrrichr()
  {
    $testArray = array(
        'κόσμε'                                                                            => 'κόσμε',
        'Κόσμε'                                                                            => 'Κόσμε',
        'öäü-κόσμεκόσμε-äöü'                                                               => 'κόσμε-äöü',
        'öäü-κόσμεκόσμε-äöüöäü-κόσμεκόσμε-äöü'                                             => 'κόσμε-äöü',
        'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε'                     => 'κόσμε',
        'äöüäöüäöü-κόσμεκόσμεäöüäöüäöü-Κόσμεκόσμεäöüäöüäöü-κόσμεκόσμεäöüäöüäöü-κόσμεκόσμε' => 'κόσμε',
        '  '                                                                               => false,
        ''                                                                                 => false,
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::strrichr($actual, 'κόσμε'), 'error by ' . $actual);
    }

    // --- UTF-8

    self::assertSame('Aκόσμεκόσμε-äöü', UTF8::strrichr('Aκόσμεκόσμε-äöü', 'aκόσμε', false, 'UTF-8'));
    self::assertSame('ü-abc', UTF8::strrichr('äöü-abc', 'ü', false, 'UTF-8'));

    self::assertSame('', UTF8::strrichr('Aκόσμεκόσμε-äöü', 'aκόσμε', true, 'UTF-8'));
    self::assertSame('äö', UTF8::strrichr('äöü-abc', 'ü', true, 'UTF-8'));

    // --- ISO

    if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
      self::assertSame('Aκόσμεκόσμε-äöü', UTF8::strrichr('Aκόσμεκόσμε-äöü', 'aκόσμε', false, 'ISO'));
      self::assertSame('ü-abc', UTF8::strrichr('äöü-abc', 'ü', false, 'ISO'));

      self::assertSame('', UTF8::strrichr('Aκόσμεκόσμε-äöü', 'aκόσμε', true, 'ISO'));
      self::assertSame('äö', UTF8::strrichr('äöü-abc', 'ü', true, 'ISO'));
    }
  }

  public function testStrrpos()
  {
    if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
      self::assertSame(1, strrpos('한국어', '국'));
    } else {
      self::assertSame(3, strrpos('한국어', '국')); // not correct
    }

    // bug is reported: https://github.com/facebook/hhvm/issues/7318
    if (defined('HHVM_VERSION') === true) {
      self::assertSame(1, UTF8::strrpos('한국어', '국', 0, '8bit', false));
      self::assertSame(1, UTF8::strrpos('한국어', '국', 0, 'ISO', false));
      self::assertSame(1, UTF8::strrpos('한국어', '국', 0, '', true));
    } else {

      if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
        self::assertSame(3, UTF8::strrpos('한국어', '국', 0, '8bit', false));
        self::assertSame(3, UTF8::strrpos('한국어', '국', 0, 'ISO', false));
      }

      self::assertSame(1, UTF8::strrpos('한국어', '국', 0, '', true));
    }

    self::assertSame(1, UTF8::strrpos('한국어', '국', 0, 'UTF-8', false));

    // --- invalid UTF-8

    if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
      self::assertSame(11, UTF8::strrpos("Iñtërnâtiôn\xE9àlizætiøn", 'à', 0, 'UTF-8', true));
      self::assertSame(12, UTF8::strrpos("Iñtërnâtiôn\xE9àlizætiøn", 'à', 0, 'UTF-8', false));
    }

    // ---

    self::assertSame(1, UTF8::strrpos('11--', '1-', 0, 'UTF-8', false));
    self::assertSame(2, UTF8::strrpos('-11--', '1-', 0, 'UTF-8', false));
    self::assertSame(false, UTF8::strrpos('한국어', '', 0, 'UTF-8', false));
    self::assertSame(1, UTF8::strrpos('한국어', '국', 0, 'UTF8', true));
    self::assertSame(false, UTF8::strrpos('한국어', ''));
    self::assertSame(1, UTF8::strrpos('한국어', '국'));
    self::assertSame(6, UTF8::strrpos('κόσμε-κόσμε', 'κ'));
    self::assertSame(13, UTF8::strrpos('test κόσμε κόσμε test', 'σ'));
    self::assertSame(9, UTF8::strrpos('中文空白-ÖÄÜ-中文空白', '中'));
    self::assertSame(13, UTF8::strrpos('ABC-ÖÄÜ-中文空白-中文空白', '中'));
  }

  public function testStrtocasefold()
  {
    self::assertSame('ǰ◌̱', UTF8::strtocasefold('ǰ◌̱', true)); // Original (NFC)
    self::assertSame('j◌̌◌', UTF8::strtocasefold('J◌̌◌')); // Uppercased
    self::assertSame('j◌̱◌̌', UTF8::strtocasefold('J◌̱◌̌')); // Uppercased NFC

    // valid utf-8
    self::assertSame('hello world 中文空白', UTF8::strtocasefold('Hello world 中文空白'));

    // invalid utf-8

    if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
      if (Bootup::is_php('5.4')) { // invalid UTF-8 + PHP 5.3 = 20 => error
        self::assertSame('iñtërnâtiôn?àlizætiøn', UTF8::strtocasefold("Iñtërnâtiôn\xE9àlizætiøn"));
        self::assertSame('iñtërnâtiôn?àlizætiøn', UTF8::strtocasefold("Iñtërnâtiôn\xE9àlizætiøn", true));
      }
    }

    self::assertSame('iñtërnâtiônàlizætiøn', UTF8::strtocasefold("Iñtërnâtiôn\xE9àlizætiøn", true, true));
  }

  public function testStrtolower()
  {
    $tests = array(
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
        'DÉJÀ Σσς Iıİi' => 'déjà σσς iıii', // result for language === "tr" --> "déjà σσς ııii"
        'ABC-ΣΣ'        => 'abc-σσ', // result for language === "tr" --> "abc-σς"
        'Å/å, Æ/æ, Ø/ø' => 'å/å, æ/æ, ø/ø',
        'ΣΣΣ'           => 'σσσ', // result for language === "tr" --> "σσς"
        'DİNÇ'          => 'dinç',
        'DINÇ'          => 'dinç', // result for language === "tr" --> "dınç"
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::strtolower($before), 'tested: ' . $before);
    }

    // ---

    // ISO (non utf-8 encoding)
    self::assertNotSame('déjà σσς iıii', UTF8::strtolower('DÉJÀ Σσς Iıİi', 'ISO'));
    self::assertNotSame('öäü', UTF8::strtolower('ÖÄÜ', 'ISO'));

    // ---

    // invalid utf-8
    if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
      if (Bootup::is_php('5.4')) { // invalid UTF-8 + PHP 5.3 = 20 => error
        self::assertSame('iñtërnâtiôn?àlizætiøn', UTF8::strtolower("Iñtërnâtiôn\xE9àlizætiøn"));
        self::assertSame('iñtërnâtiôn?àlizætiøn', UTF8::strtolower("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', false));
      }
    }

    self::assertSame('iñtërnâtiônàlizætiøn', UTF8::strtolower("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', true));

    // ---

    UTF8::checkForSupport();
    $support = UTF8::getSupportInfo();

    // language === "tr"
    if (
        UTF8::intl_loaded() === true
        &&
        Bootup::is_php('5.4')
        &&
        in_array('tr-Lower', $support['intl__transliterator_list_ids'], true)
    ) {
      $tests = array(
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
      );

      // DEBUG (for travis ci)
      /** @noinspection ForgottenDebugOutputInspection */
      //var_dump(transliterator_list_ids());

      foreach ($tests as $before => $after) {
        self::assertSame($after, UTF8::strtolower($before, 'UTF8', false, 'tr'), 'tested: ' . $before);
      }
    }
  }

  public function testStrtonatfold()
  {
    $utf8 = new UTF8();

    // valid utf-8
    $string = $this->invokeMethod($utf8, 'strtonatfold', array('Hello world 中文空白'));
    self::assertSame('Hello world 中文空白', $string);

    // invalid utf-8
    $string = $this->invokeMethod($utf8, 'strtonatfold', array("Iñtërnâtiôn\xE9àlizætiøn"));
    self::assertSame('', $string);
  }

  public function testStrtoupper()
  {
    $tests = array(
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
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::strtoupper($before), 'tested: ' . $before);
    }

    // ---

    // ISO (non utf-8 encoding)
    self::assertNotSame('DÉJÀ ΣΣΣ IIİI', UTF8::strtoupper('Déjà Σσς Iıİi', 'ISO'));
    self::assertSame('ABC TEST', UTF8::strtoupper('abc test', 'ISO'));

    // ---

    // invalid utf-8

    if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
      if (Bootup::is_php('5.4')) { // invalid UTF-8 + PHP 5.3 = 20 => error
        self::assertSame('IÑTËRNÂTIÔN?ÀLIZÆTIØN', UTF8::strtoupper("Iñtërnâtiôn\xE9àlizætiøn"));
        self::assertSame('IÑTËRNÂTIÔN?ÀLIZÆTIØN', UTF8::strtoupper("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', false));
      }
    }

    self::assertSame('IÑTËRNÂTIÔNÀLIZÆTIØN', UTF8::strtoupper("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', true));

    // ---

    UTF8::checkForSupport();
    $support = UTF8::getSupportInfo();

    // language === "tr"
    if (
        UTF8::intl_loaded() === true
        &&
        Bootup::is_php('5.4')
        &&
        in_array('tr-Upper', $support['intl__transliterator_list_ids'], true)
    ) {
      $tests = array(
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
      );

      foreach ($tests as $before => $after) {
        self::assertSame($after, UTF8::strtoupper($before, 'UTF8', false, 'tr'), 'tested: ' . $before);
      }
    }
  }

  public function testStrtr()
  {
    // php compatible tests

    $arr = array(
        'Hello' => 'Hi',
        'world' => 'earth',
    );
    self::assertSame('Hi earth', strtr('Hello world', $arr));
    self::assertSame('Hi earth', UTF8::strtr('Hello world', $arr));

    // UTF-8 tests

    $arr = array(
        'Hello' => '○●◎',
        '中文空白'  => 'earth',
    );
    self::assertSame('○●◎ earth', UTF8::strtr('Hello 中文空白', $arr));

    self::assertSame('○●◎◎o wor◎d', UTF8::strtr('Hello world', 'Hello', '○●◎'));
    self::assertSame('Hello world H●◎', UTF8::strtr('Hello world ○●◎', '○', 'Hello'));
  }

  public function testStrwidth()
  {
    $testArray = array(
        'testtest' => 8,
        'Ã'        => 1,
        ' '        => 1,
        ''         => 0,
        "\n"       => 1,
        'test'     => 4,
        "ひらがな\r"   => 9,
        "○●◎\r"    => 4,
    );

    foreach ($testArray as $before => $after) {
      self::assertSame($after, UTF8::strwidth($before));
    }

    // test + Invalid Chars

    if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
      if (Bootup::is_php('5.4')) { // invalid UTF-8 + PHP 5.3 = 20 => error
        self::assertSame(21, UTF8::strwidth("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', false));
      }
    }

    self::assertSame(20, UTF8::strwidth("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', true));

    if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
      self::assertSame(20, UTF8::strlen("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', false));
    }

    self::assertSame(20, UTF8::strlen("Iñtërnâtiôn\xE9àlizætiøn", 'UTF8', true));

    // ISO

    if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
      self::assertSame(28, UTF8::strlen("Iñtërnâtiôn\xE9àlizætiøn", 'ISO', false));
      self::assertSame(27, UTF8::strlen("Iñtërnâtiôn\xE9àlizætiøn", 'ISO', true));
    }
  }

  public function testSubstr()
  {
    self::assertSame('23', substr(1234, 1, 2));
    self::assertSame('bc', substr('abcde', 1, 2));
    self::assertSame('de', substr('abcde', -2, 2));
    self::assertSame('bc', substr('abcde', 1, 2));
    self::assertSame('bc', substr('abcde', 1, 2));
    self::assertSame('bcd', substr('abcde', 1, 3));
    self::assertSame('bc', substr('abcde', 1, 2));

    self::assertSame('23', UTF8::substr(1234, 1, 2));
    self::assertSame('bc', UTF8::substr('abcde', 1, 2));
    self::assertSame('de', UTF8::substr('abcde', -2, 2));
    self::assertSame('bc', UTF8::substr('abcde', 1, 2));
    self::assertSame('bc', UTF8::substr('abcde', 1, 2, true));
    self::assertSame('bc', UTF8::substr('abcde', 1, 2, 'UTF-8', true));
    self::assertSame('bcd', UTF8::substr('abcde', 1, 3));
    self::assertSame('bc', UTF8::substr('abcde', 1, 2));

    // UTF-8
    self::assertSame('文空', UTF8::substr('中文空白', 1, 2));
    self::assertSame('Я можу', UTF8::substr('Я можу їсти скло', 0, 6));
  }

  public function testSubstrCompare()
  {
    // php compatible tests

    self::assertSame(0, substr_compare(12345, 23, 1, 2));
    self::assertSame(0, UTF8::substr_compare(12345, 23, 1, 2));

    self::assertSame(0, substr_compare('abcde', 'bc', 1, 2));
    self::assertSame(0, UTF8::substr_compare('abcde', 'bc', 1, 2));

    self::assertSame(0, substr_compare('abcde', 'de', -2, 2));
    self::assertSame(0, UTF8::substr_compare('abcde', 'de', -2, 2));

    self::assertSame(0, substr_compare('abcde', 'bcg', 1, 2));
    self::assertSame(0, UTF8::substr_compare('abcde', 'bcg', 1, 2));

    self::assertSame(0, substr_compare('abcde', 'BC', 1, 2, true));
    self::assertSame(0, UTF8::substr_compare('abcde', 'BC', 1, 2, true));

    self::assertSame(1, substr_compare('abcde', 'bc', 1, 3));
    self::assertSame(1, UTF8::substr_compare('abcde', 'bc', 1, 3));

    self::assertSame(-1, substr_compare('abcde', 'cd', 1, 2));
    self::assertSame(-1, UTF8::substr_compare('abcde', 'cd', 1, 2));

    // UTF-8 tests

    self::assertTrue(UTF8::substr_compare("○●◎\r", '●◎') < 0);
    self::assertTrue(UTF8::substr_compare("○●◎\r", '●◎', -1) < 0);
    self::assertTrue(UTF8::substr_compare("○●◎\r", '●◎', -1, 2) < 0);
    self::assertTrue(UTF8::substr_compare("○●◎\r", '●◎', 0, 2) < 0);

    self::assertSame(1, UTF8::substr_compare("○●◎\r", '◎●', 1, 2));

    self::assertSame(0, UTF8::substr_compare("○●◎\r", '●◎', 1, 2, false));
    self::assertSame(0, UTF8::substr_compare("○●◎\r", '●◎', 1, 2));
    self::assertSame(0, UTF8::substr_compare('中文空白', '文空', 1, 2, true));
    self::assertSame(0, UTF8::substr_compare('中文空白', '文空', 1, 2));

  }

  public function testSubstrCount()
  {
    // php compatible tests

    self::assertSame(false, substr_count('', ''));
    self::assertSame(false, UTF8::substr_count('', ''));

    self::assertSame(false, substr_count('', '', 1));
    self::assertSame(false, UTF8::substr_count('', '', 1));

    if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
      self::assertSame(null, substr_count('', '', 1, 1));
    } else {
      self::assertSame(false, substr_count('', '', 1, 1));
    }

    self::assertSame(false, UTF8::substr_count('', '', 1, 1));

    if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
      self::assertSame(null, substr_count('', 'test', 1, 1));
    } else {
      self::assertSame(false, substr_count('', 'test', 1, 1));
    }

    self::assertSame(false, UTF8::substr_count('', 'test', 1, 1));

    if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
      self::assertSame(null, substr_count('test', '', 1, 1));
    } else {
      self::assertSame(false, substr_count('test', '', 1, 1));
    }

    self::assertSame(false, UTF8::substr_count('test', '', 1, 1));

    if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
      self::assertSame(null, substr_count('test', 'test', 1, 1));
    } else {
      self::assertSame(0, substr_count('test', 'test', 1, 1));
    }

    self::assertSame(0, UTF8::substr_count('test', 'test', 1, 1));

    if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
      self::assertSame(null, substr_count(12345, 23, 1, 2));
    } else {
      self::assertSame(1, substr_count(12345, 23, 1, 2));
    }

    self::assertSame(1, UTF8::substr_count(12345, 23, 1, 2));

    self::assertSame(2, substr_count('abcdebc', 'bc'));
    self::assertSame(2, UTF8::substr_count('abcdebc', 'bc'));

    if (Bootup::is_php('7.1') === false) {

      if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
        self::assertSame(null, substr_count('abcde', 'de', -2, 2));
      } else {
        self::assertSame(false, substr_count('abcde', 'de', -2, 2));
      }

      self::assertSame(false, UTF8::substr_count('abcde', 'de', -2, 2));
    } else {
      self::assertSame(1, substr_count('abcde', 'de', -2, 2));
      self::assertSame(1, UTF8::substr_count('abcde', 'de', -2, 2));
    }

    if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
      self::assertSame(null, substr_count('abcde', 'bcg', 1, 2));
    } else {
      self::assertSame(0, substr_count('abcde', 'bcg', 1, 2));
    }

    self::assertSame(0, UTF8::substr_count('abcde', 'bcg', 1, 2));

    if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
      self::assertSame(null, substr_count('abcde', 'BC', 1, 2));
    } else {
      self::assertSame(0, substr_count('abcde', 'BC', 1, 2));
    }

    self::assertSame(0, UTF8::substr_count('abcde', 'BC', 1, 2));

    if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
      self::assertSame(null, substr_count('abcde', 'bc', 1, 3));
    } else {
      self::assertSame(1, substr_count('abcde', 'bc', 1, 3));
    }

    self::assertSame(1, UTF8::substr_count('abcde', 'bc', 1, 3));

    if (UTF8::getSupportInfo('mbstring_func_overload') === true) {
      self::assertSame(null, substr_count('abcde', 'cd', 1, 2));
    } else {
      self::assertSame(0, substr_count('abcde', 'cd', 1, 2));
    }

    self::assertSame(0, UTF8::substr_count('abcde', 'cd', 1, 2));

    // UTF-8 tests

    self::assertSame(2, UTF8::substr_count('Можам да јадам стакло, а не ме штета.', 'д'));
    self::assertSame(2, UTF8::substr_count("○●◎\r◎", '◎'));
    self::assertSame(1, UTF8::substr_count("○●◎\r", '●◎', 1, 2));
    self::assertSame(1, UTF8::substr_count('中文空白', '文空', 1, 2));

    // ISO

    if (UTF8::mbstring_loaded() === true) { // only with "mbstring"
      self::assertSame(0, UTF8::substr_count('中文空白', '文空', 1, 2, 'ISO'));
      self::assertSame(1, UTF8::substr_count('abcde', 'bc', 1, 2, 'ISO'));
    }
  }

  public function testSubstrILeft()
  {
    $str = 'ΚόσμεMiddleEnd';

    $tests = array(
        'Κόσμε' => 'MiddleEnd',
        'κόσμε' => 'MiddleEnd',
        ''      => 'ΚόσμεMiddleEnd',
        ' '     => 'ΚόσμεMiddleEnd',
        false   => 'ΚόσμεMiddleEnd',
        'Κ'     => 'όσμεMiddleEnd',
        'End'   => 'ΚόσμεMiddleEnd',
        'end'   => 'ΚόσμεMiddleEnd',
    );

    foreach ($tests as $test => $result) {
      self::assertSame($result, UTF8::substr_ileft($str, $test), 'tested: ' . $test);
    }

    // ---

    self::assertSame('MiddleEndΚόσμε', UTF8::substr_ileft('ΚόσμεMiddleEndΚόσμε', 'Κόσμε'));

    // ---

    self::assertSame('ΚόσμεMiddleEndΚόσμε', UTF8::substr_ileft('ΚόσμεMiddleEndΚόσμε', ''));

    // ---

    self::assertSame('', UTF8::substr_ileft('', 'Κόσμε'));
  }

  public function testSubstrIRight()
  {
    $str = 'BeginMiddleΚόσμε';

    $tests = array(
        'Κόσμε' => 'BeginMiddle',
        'κόσμε' => 'BeginMiddle',
        ''      => 'BeginMiddleΚόσμε',
        ' '     => 'BeginMiddleΚόσμε',
        false   => 'BeginMiddleΚόσμε',
        'ε'     => 'BeginMiddleΚόσμ',
        'End'   => 'BeginMiddleΚόσμε',
        'end'   => 'BeginMiddleΚόσμε',
    );

    foreach ($tests as $test => $result) {
      self::assertSame($result, UTF8::substr_iright($str, $test), 'tested: ' . $test);
    }

    // ---

    self::assertSame('ΚόσμεMiddleEnd', UTF8::substr_iright('ΚόσμεMiddleEndΚόσμε', 'Κόσμε'));

    // ---

    self::assertSame('ΚόσμεMiddleEndΚόσμε', UTF8::substr_iright('ΚόσμεMiddleEndΚόσμε', ''));

    // ---

    self::assertSame('', UTF8::substr_iright('', 'Κόσμε'));
  }

  public function testSubstrLeft()
  {
    $str = 'ΚόσμεMiddleEnd';

    $tests = array(
        'Κόσμε' => 'MiddleEnd',
        'κόσμε' => 'ΚόσμεMiddleEnd',
        ''      => 'ΚόσμεMiddleEnd',
        ' '     => 'ΚόσμεMiddleEnd',
        false   => 'ΚόσμεMiddleEnd',
        'Κ'     => 'όσμεMiddleEnd',
        'End'   => 'ΚόσμεMiddleEnd',
        'end'   => 'ΚόσμεMiddleEnd',
    );

    foreach ($tests as $test => $result) {
      self::assertSame($result, UTF8::substr_left($str, $test), 'tested: ' . $test);
    }

    // ---

    self::assertSame('MiddleEndΚόσμε', UTF8::substr_left('ΚόσμεMiddleEndΚόσμε', 'Κόσμε'));

    // ---

    self::assertSame('ΚόσμεMiddleEndΚόσμε', UTF8::substr_left('ΚόσμεMiddleEndΚόσμε', ''));

    // ---

    self::assertSame('', UTF8::substr_left('', 'Κόσμε'));
  }

  public function testSubstrRight()
  {
    $str = 'BeginMiddleΚόσμε';

    $tests = array(
        'Κόσμε' => 'BeginMiddle',
        'κόσμε' => 'BeginMiddleΚόσμε',
        ''      => 'BeginMiddleΚόσμε',
        ' '     => 'BeginMiddleΚόσμε',
        false   => 'BeginMiddleΚόσμε',
        'ε'     => 'BeginMiddleΚόσμ',
        'End'   => 'BeginMiddleΚόσμε',
        'end'   => 'BeginMiddleΚόσμε',
    );

    foreach ($tests as $test => $result) {
      self::assertSame($result, UTF8::substr_right($str, $test), 'tested: ' . $test);
    }

    // ---

    self::assertSame('ΚόσμεMiddleEnd', UTF8::substr_right('ΚόσμεMiddleEndΚόσμε', 'Κόσμε'));

    // ---

    self::assertSame('ΚόσμεMiddleEndΚόσμε', UTF8::substr_right('ΚόσμεMiddleEndΚόσμε', ''));

    // ---

    self::assertSame('', UTF8::substr_right('', 'Κόσμε'));
  }

  public function testSwapCase()
  {
    $tests = array(
        1                               => '1',
        -1                              => '-1',
        ' '                             => ' ',
        ''                              => '',
        'أبز'                           => 'أبز',
        "\xe2\x80\x99"                  => '’',
        'Ɓtest'                         => 'ɓTEST',
        '  -ABC-中文空白-  '                => '  -abc-中文空白-  ',
        "      - abc- \xc2\x87"         => '      - ABC- ',
        'abc'                           => 'ABC',
        'deja vu'                       => 'DEJA VU',
        'déjà vu'                       => 'DÉJÀ VU',
        'déJÀ σσς iıII'                 => 'DÉjà ΣΣΣ IIii',
        "test\x80-\xBFöäü"              => 'TEST-ÖÄÜ',
        'Internationalizaetion'         => 'iNTERNATIONALIZAETION',
        "中 - &#20013; - %&? - \xc2\x80" => '中 - &#20013; - %&? - ',
        'BonJour'                       => 'bONjOUR',
        'BonJour & au revoir'           => 'bONjOUR & AU REVOIR',
        'Déjà'                          => 'dÉJÀ',
        'това е тестово заглавие'       => 'ТОВА Е ТЕСТОВО ЗАГЛАВИЕ',
        'это тестовый заголовок'        => 'ЭТО ТЕСТОВЫЙ ЗАГОЛОВОК',
        'führen Aktivitäten Haglöfs'    => 'FÜHREN aKTIVITÄTEN hAGLÖFS',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::swapCase($before, 'UTF-8', true), $before);
    }

    // ---

    self::assertNotSame('это тестовый заголовок', UTF8::swapCase('ЭТО ТЕСТОВЫЙ ЗАГОЛОВОК', 'ISO'));
    self::assertSame('BonJour & au revoir', UTF8::swapCase('bONjOUR & AU REVOIR', 'ISO'));
  }

  public function testToASCII()
  {
    $testsStrict = array();
    if (UTF8::intl_loaded() === true && Bootup::is_php('5.4')) {

      // ---

      $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/sample-unicode-chart.txt');
      $resultString = UTF8::file_get_contents(__DIR__ . '/fixtures/sample-ascii-chart.txt');

      self::assertSame($resultString, UTF8::to_ascii($testString, '?', true));

      // ---

      $testsStrict = array(
          1                               => '1',
          -1                              => '-1',
          ' '                             => ' ',
          ''                              => '',
          'أبز'                           => 'abz',
          "\xe2\x80\x99"                  => '\'',
          'Ɓtest'                         => 'Btest',
          '  -ABC-中文空白-  '                => '  -ABC-zhong wen kong bai-  ',
          "      - abc- \xc2\x87"         => '      - abc- ++',
          'abc'                           => 'abc',
          'deja vu'                       => 'deja vu',
          'déjà vu'                       => 'deja vu',
          'déjà σσς iıii'                 => 'deja sss iiii',
          "test\x80-\xBFöäü"              => 'test-oau',
          'Internationalizaetion'         => 'Internationalizaetion',
          "中 - &#20013; - %&? - \xc2\x80" => 'zhong - &#20013; - %&? - EUR',
          'Un été brûlant sur la côte'    => 'Un ete brulant sur la cote',
          'Αυτή είναι μια δοκιμή'         => 'Aute einai mia dokime',
          'أحبك'                          => 'ahbk',
          'キャンパス'                         => 'kyanhasu',
          'биологическом'                 => 'biologiceskom',
          '정, 병호'                         => 'jeong, byeongho',
          'ますだ, よしひこ'                     => 'masuta, yoshihiko',
          'मोनिच'                         => 'monica',
          'क्षȸ'                          => 'kasadb',
          'أحبك 😀'                       => 'ahbk ?',
          'ذرزسشصضطظعغػؼؽؾؿ 5.99€'       => 'dhrzsshsdtz\'gh[?][?][?][?][?] 5.99EUR',
          'ذرزسشصضطظعغػؼؽؾؿ £5.99'       => 'dhrzsshsdtz\'gh[?][?][?][?][?] PS5.99',
          '׆אבגדהוזחטיךכלםמן $5.99'      => '[?]\'bgdhwzhtykklmmn $5.99',
          '日一国会人年大十二本中長出三同 ¥5990' => 'ri yi guo hui ren nian da shi er ben zhong zhang chu san tong Y=5990',
          '5.99€ 日一国会人年大十 $5.99'    => '5.99EUR ri yi guo hui ren nian da shi $5.99',
          'בגדה@ضطظعغػ.com'              => 'bgdh@dtz\'gh[?].com',
          '年大十@ضطظعغػ'                 => 'nian da shi@dtz\'gh[?]',
          'בגדה & 年大十'                 => 'bgdh & nian da shi',
          '国&ם at ضطظعغػ.הוז'           => 'guo&m at dtz\'gh[?].hwz',
          'my username is @בגדה'         => 'my username is @bgdh',
          'The review gave 5* to ظعغػ'   => 'The review gave 5* to z\'gh[?]',
          'use 年大十@ضطظعغػ.הוז to get a 10% discount' => 'use nian da shi@dtz\'gh[?].hwz to get a 10% discount',
          '日 = הط^2'                    => 'ri = ht^2',
          'ךכלם 国会 غػؼؽ 9.81 m/s2'     => 'kklm guo hui gh[?][?][?] 9.81 m/s2',
          'The #会 comment at @בגדה = 10% of *&*' => 'The #hui comment at @bgdh = 10% of *&*',
          '∀ i ∈ ℕ'                       => '[?] i [?] N',
          '👍 💩 😄 ❤ 👍 💩 😄 ❤أحبك'     => '? ? ?  ? ? ? ahbk',
      );
    }

    $tests = array(
        1                               => '1',
        -1                              => '-1',
        ' '                             => ' ',
        ''                              => '',
        'أبز'                           => 'abz',
        "\xe2\x80\x99"                  => '\'',
        'Ɓtest'                         => 'Btest',
        '  -ABC-中文空白-  '                => '  -ABC-Zhong Wen Kong Bai -  ',
        "      - abc- \xc2\x87"         => '      - abc- ++',
        'abc'                           => 'abc',
        'deja vu'                       => 'deja vu',
        'déjà vu '                       => 'deja vu ',
        'déjà σσς iıii'                 => 'deja sss iiii',
        'κόσμε'                         => 'kosme',
        "test\x80-\xBFöäü"              => 'test-oau',
        'Internationalizaetion'         => 'Internationalizaetion',
        "中 - &#20013; - %&? - \xc2\x80" => 'Zhong  - &#20013; - %&? - EUR',
        'Un été brûlant sur la côte'    => 'Un ete brulant sur la cote',
        'Αυτή είναι μια δοκιμή'         => 'Aute einai mia dokime',
        'أحبك'                          => 'aHbk',
        'キャンパス'                         => 'kiyanpasu',
        'биологическом'                 => 'biologicheskom',
        '정, 병호'                         => 'jeong, byeongho',
        'ますだ, よしひこ'                     => 'masuda, yosihiko',
        'मोनिच'                         => 'monic',
        'क्षȸ'                          => 'kssdb',
        'أحبك 😀'                       => 'aHbk ?',
        '∀ i ∈ ℕ'                       => '[?] i [?] N',
        '👍 💩 😄 ❤ 👍 💩 😄 ❤أحبك'     => '? ? ?  ? ? ? aHbk',
    );

    for ($i = 0; $i <= 2; $i++) { // keep this loop for simple performance tests
      foreach ($tests as $before => $after) {
        self::assertSame($after, UTF8::to_ascii($before), 'tested: ' . $before);
        self::assertSame($after, UTF8::str_transliterate($before), 'tested: ' . $before);
      }
    }

    foreach ($testsStrict as $before => $after) {
      self::assertSame($after, UTF8::to_ascii($before, '?', true), 'tested: ' . $before);
      self::assertSame($after, UTF8::toAscii($before, '?', true), 'tested: ' . $before);
      self::assertSame($after, UTF8::str_transliterate($before, '?', true), 'tested: ' . $before);
    }
  }

  public function testToLatin1Utf8()
  {
    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-????-  ',
        '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
        'öäü'            => 'öäü',
        ''               => '',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::to_utf8(UTF8::to_latin1($before)));
    }

    // alias
    self::assertSame($tests, UTF8::to_utf8(UTF8::toIso8859($tests)));
    self::assertSame($tests, UTF8::to_utf8(UTF8::to_latin1($tests)));
    self::assertSame($tests, UTF8::toUTF8(UTF8::toLatin1($tests)));
  }

  public function testToUtf8()
  {
    $examples = array(
      // Valid UTF-8
      'κόσμε'                                                                => array('κόσμε' => 'κόσμε'),
      '中'                                                                    => array('中' => '中'),
      // Valid UTF-8 + "win1252"-encoding
      'Dänisch (Å/å, Æ/æ, Ø/ø) + ' . "\xe2\x82\xac"                          => array('Dänisch (Å/å, Æ/æ, Ø/ø) + €' => 'Dänisch (Å/å, Æ/æ, Ø/ø) + €'),
      // Valid UTF-8 + Invalid Chars
      "κόσμε\xa0\xa1-öäü-‽‽‽"                                                => array('κόσμε-öäü-‽‽‽' => 'κόσμε-öäü-‽‽‽'),
      // Valid emoji (non-UTF-8)
      '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ &#x267F; &#x26CE;'              => array('👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ &#x267F; &#x26CE;' => '👍 💩 😄 ❤ 👍 💩 😄 ❤ 🐶 💩 🐱 🐸 🌀 ❤ &#x267F; &#x26CE;'),
      // Valid ASCII
      'a'                                                                    => array('a' => 'a'),
      // Valid ASCII + Invalid Chars
      "a\xa0\xa1-öäü"                                                        => array('a-öäü' => 'a-öäü'),
      // Valid 2 Octet Sequence
      "\xc3\xb1"                                                             => array('ñ' => 'ñ'),
      // Invalid 2 Octet Sequence
      "\xc3\x28"                                                             => array('�(' => '('),
      // Invalid Sequence Identifier
      "\xa0\xa1"                                                             => array('��' => ''),
      // Valid 3 Octet Sequence
      "\xe2\x82\xa1"                                                         => array('₡' => '₡'),
      // Invalid 3 Octet Sequence (in 2nd Octet)
      "\xe2\x28\xa1"                                                         => array('�(�' => '('),
      // Invalid 3 Octet Sequence (in 3rd Octet)
      "\xe2\x82\x28"                                                         => array('�(' => '('),
      // Valid 4 Octet Sequence
      "\xf0\x90\x8c\xbc"                                                     => array('𐌼' => '𐌼'),
      // Invalid 4 Octet Sequence (in 2nd Octet)
      "\xf0\x28\x8c\xbc"                                                     => array('�(��' => '('),
      // Invalid 4 Octet Sequence (in 3rd Octet)
      "\xf0\x90\x28\xbc"                                                     => array('�(�' => '('),
      // Invalid 4 Octet Sequence (in 4th Octet)
      "\xf0\x28\x8c\x28"                                                     => array('�(�(' => '(('),
      // Valid 5 Octet Sequence (but not Unicode!)
      "\xf8\xa1\xa1\xa1\xa1"                                                 => array('�' => ''),
      // Valid 6 Octet Sequence (but not Unicode!)
      "\xfc\xa1\xa1\xa1\xa1\xa1"                                             => array('�' => ''),
      // Valid UTF-8 string with null characters
      "\0\0\0\0中\0 -\0\0 &#20013; - &#128077; - %&? - \xc2\x80"              => array('中 - &#20013; - &#128077; - %&? - €' => '中 - &#20013; - &#128077; - %&? - €'),
      // InValid UTF-8 string with null characters + HMTL
      "\0\0\0\0中\0 -\0\0 &#20013; - &shy; - &nbsp; - %&? - \xc2\x80\x80\x80" => array('中 - &#20013; - &shy; - &nbsp; - %&? - €' => '中 - &#20013; - &shy; - &nbsp; - %&? - €'),
    );

    $counter = 0;
    foreach ($examples as $testString => $testResults) {
      foreach ($testResults as $before => $after) {
        self::assertSame($after, UTF8::to_utf8(UTF8::cleanup($testString)), $counter . ' - ' . $before);
      }
      $counter++;
    }

    $testString = 'test' . UTF8::html_entity_decode('&nbsp;') . 'test';
    self::assertSame('test' . "\xc2\xa0" . 'test', $testString);
    self::assertSame('test&nbsp;test', UTF8::htmlentities($testString));
    self::assertSame('test' . "\xc2\xa0" . 'test', UTF8::cleanup($testString));
  }

  public function testToUtf8ByLanguage()
  {
    // http://www.columbia.edu/~fdc/utf8/

    $testArray = array(
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
    );

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

    $result = array();
    $i = 0;
    foreach ($testArray as $test) {

      $result[$i] = UTF8::to_utf8($test);

      self::assertSame($test, $result[$i]);

      $i++;
    }

    // test with array
    self::assertSame($result, UTF8::to_utf8($testArray));

    foreach ($testArray as $test) {
      self::assertSame($test, UTF8::to_utf8(UTF8::to_utf8($test)));
    }
  }

  public function testToUtf8_v2()
  {
    $testArray = array(
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
    );

    foreach ($testArray as $before => $after) {
      self::assertSame($after, UTF8::to_utf8($before));
    }

    // ---

    $testArray = array(
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
    );

    foreach ($testArray as $before => $after) {
      self::assertSame($after, UTF8::to_utf8($before, true));
    }

    // ---

    $invalidTest = array(
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
    );

    foreach ($invalidTest as $test => $note) {
      self::assertSame('a', UTF8::cleanup($test), $note);
    }
  }

  public function testToUtf8_v3()
  {
    $utf8File = file_get_contents(__DIR__ . '/fixtures/utf-8.txt');
    $latinFile = file_get_contents(__DIR__ . '/fixtures/latin.txt');

    $utf8File = explode("\n", str_replace(array("\r\n", "\r", '<br>', '<br />'), "\n", $utf8File));
    $latinFile = explode("\n", str_replace(array("\r\n", "\r", '<br>', '<br />'), "\n", $latinFile));

    $testArray = array_combine($latinFile, $utf8File);

    self::assertTrue(count($testArray) > 0);
    foreach ($testArray as $before => $after) {
      self::assertSame($after, UTF8::to_utf8($before), 'tested: ' . $before);
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
    for ($i = 0; $i <= 2; $i++) { // keep this loop for simple performance tests
      self::assertSame($output, UTF8::trim($input));
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
    self::assertSame($output, UTF8::trim($input, ' '));
  }

  /**
   * @dataProvider trimProviderAdvancedWithMoreThenTwoBytes
   *
   * @param $input
   * @param $output
   */
  public function testTrimAdvancedWithMoreThenTwoBytes($input, $output)
  {
    self::assertSame($output, UTF8::trim($input, '白'));
  }

  public function testUcWords()
  {
    self::assertSame('Iñt Ërn ÂTi Ônà Liz Æti Øn', UTF8::ucwords('iñt ërn âTi ônà liz æti øn'));
    self::assertSame("Iñt Ërn Âti\n Ônà Liz Æti  Øn", UTF8::ucwords("iñt ërn âti\n ônà liz æti  øn"));
    self::assertSame('中文空白 foo Oo Oöäü#s', UTF8::ucwords('中文空白 foo oo oöäü#s', array('foo'), '#'));
    self::assertSame('中文空白 foo Oo Oöäü#S', UTF8::ucwords('中文空白 foo oo oöäü#s', array('foo'), ''));
    self::assertSame('', UTF8::ucwords(''));
    self::assertSame('Ñ', UTF8::ucwords('ñ'));
    self::assertSame("Iñt ËrN Âti\n Ônà Liz Æti Øn", UTF8::ucwords("iñt ërN âti\n ônà liz æti øn"));
    self::assertSame('ÑtërnâtiônàlizætIøN', UTF8::ucwords('ñtërnâtiônàlizætIøN'));
    self::assertSame('ÑtërnâtiônàlizætIøN Test câse', UTF8::ucwords('ñtërnâtiônàlizætIøN test câse', array('câse')));
    self::assertSame('Deja Σσς DEJa ΣσΣ', UTF8::ucwords('deja σσς dEJa σσΣ'));

    self::assertSame('Deja Σσς DEJa ΣσΣ', UTF8::ucwords('deja σσς dEJa σσΣ', array('de')));
    self::assertSame('Deja Σσς DEJa ΣσΣ', UTF8::ucwords('deja σσς dEJa σσΣ', array('d', 'e')));

    self::assertSame('deja Σσς DEJa ΣσΣ', UTF8::ucwords('deja σσς dEJa σσΣ', array('deja')));
    self::assertSame('deja Σσς DEJa σσΣ', UTF8::ucwords('deja σσς dEJa σσΣ', array('deja', 'σσΣ')));
  }

  public function testUcfirst()
  {
    self::assertSame('Öäü', UTF8::ucfirst('Öäü'));
    self::assertSame('Öäü', UTF8::ucfirst('öäü'));
    self::assertSame('Κόσμε', UTF8::ucfirst('κόσμε'));
    self::assertSame('ABC-ÖÄÜ-中文空白', UTF8::ucfirst('aBC-ÖÄÜ-中文空白'));
    self::assertSame('Iñtërnâtiônàlizætiøn', UTF8::ucfirst('iñtërnâtiônàlizætiøn'));
    self::assertSame('Ñtërnâtiônàlizætiøn', UTF8::ucfirst('ñtërnâtiônàlizætiøn'));
    self::assertSame(' iñtërnâtiônàlizætiøn', UTF8::ucfirst(' iñtërnâtiônàlizætiøn'));
    self::assertSame('Ñtërnâtiônàlizætiøn', UTF8::ucfirst('Ñtërnâtiônàlizætiøn'));
    self::assertSame('ÑtërnâtiônàlizætIøN', UTF8::ucfirst('ñtërnâtiônàlizætIøN'));
    self::assertSame('ÑtërnâtiônàlizætIøN test câse', UTF8::ucfirst('ñtërnâtiônàlizætIøN test câse'));
    self::assertSame('', UTF8::ucfirst(''));
    self::assertSame('Ñ', UTF8::ucfirst('ñ'));
    self::assertSame("Ñtërn\nâtiônàlizætiøn", UTF8::ucfirst("ñtërn\nâtiônàlizætiøn"));
    self::assertSame('Deja', UTF8::ucfirst('deja'));
    self::assertSame('Σσς', UTF8::ucfirst('σσς'));
    self::assertSame('DEJa', UTF8::ucfirst('dEJa'));
    self::assertSame('ΣσΣ', UTF8::ucfirst('σσΣ'));

    // alias
    self::assertSame('Öäü', UTF8::ucword('öäü'));
  }

  public function testUrldecode()
  {
    $testArray = array(
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
    );

    foreach ($testArray as $before => $after) {
      self::assertSame($after, UTF8::urldecode($before), 'testing: ' . $before);
    }
  }

  public function testUrldecodeFixWin1252Chars()
  {
    $urldecode_fix_win1252_chars = UTF8::urldecode_fix_win1252_chars();

    self::assertSame(true, is_array($urldecode_fix_win1252_chars));
    self::assertSame(true, count($urldecode_fix_win1252_chars) > 0);
  }

  public function testUtf8DecodeEncodeUtf8()
  {

    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-中文空白-  ',
        '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
        'öäü'            => 'öäü',
        ''               => '',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::encode('UTF-8', $before));
    }

    // ---

    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-????-  ',
        '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
        'öäü'            => 'öäü',
        ''               => '',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::encode('UTF-8', UTF8::utf8_decode($before)));
    }

    // ---

    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-????-  ',
        '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
        'öäü'            => 'öäü',
        ''               => '',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::utf8_encode(UTF8::encode('ISO-8859-1', $before, false)));
    }
  }

  public function testUtf8DecodeUtf8Encode()
  {
    $tests = array(
        '  -ABC-中文空白-  '  => '  -ABC-????-  ',
        '      - ÖÄÜ- '     => '      - ÖÄÜ- ',
        'öäü'               => 'öäü',
        ''                  => '',
        false               => '0',
        null                => '',
        "\xe2\x28\xa1"      => 'â(¡',
        "\xa0\xa1"          => ' ¡',
        "κόσμε\xa0\xa1-öäü" => '????? ¡-öäü',
        'foobar'            => 'foobar',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::utf8_encode((UTF8::utf8_decode($before))));
    }
  }

  public function testUtf8Encode()
  {
    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-ä¸­æ–‡ç©ºç™½-  ',
        '      - ÖÄÜ- '  => '      - Ã–Ã„Ãœ- ',
        'öäü'            => 'Ã¶Ã¤Ã¼',
        ''               => '',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::utf8_encode($before));
    }
  }

  public function testUtf8EncodeEncodeUtf8()
  {
    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-ä¸­æ–‡ç©ºç™½-  ',
        '      - ÖÄÜ- '  => '      - Ã–Ã„Ãœ- ',
        'öäü'            => 'Ã¶Ã¤Ã¼',
        ''               => '',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::encode('UTF-8', UTF8::utf8_encode($before)));
    }
  }

  public function testUtf8EncodeUtf8Decode()
  {
    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-中文空白-  ',
        '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
        'öäü'            => 'öäü',
        ''               => '',
        'foobar'         => 'foobar',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::utf8_decode(UTF8::utf8_encode($before)));
    }
  }

  public function testUtf8EncodeUtf8Encode()
  {
    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-Ã¤Â¸Â­Ã¦â€“â€¡Ã§Â©ÂºÃ§â„¢Â½-  ',
        '      - ÖÄÜ- '  => '      - Ãƒâ€“Ãƒâ€žÃƒÅ“- ',
        'öäü'            => 'ÃƒÂ¶ÃƒÂ¤ÃƒÂ¼',
        ''               => '',
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after, UTF8::utf8_encode(UTF8::utf8_encode($before)));
    }
  }

  public function testUtf8FileWithBom()
  {
    $bom = UTF8::file_has_bom(__DIR__ . '/fixtures/utf-8-bom.txt');
    self::assertSame(true, $bom);

    $bom = UTF8::file_has_bom(__DIR__ . '/fixtures/utf-8.txt');
    self::assertSame(false, $bom);
  }

  public function testUtf8FixWin1252Chars()
  {
    $testArray = array(
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
    );

    foreach ($testArray as $before => $after) {
      self::assertSame($after, UTF8::utf8_fix_win1252_chars($before));
    }
  }

  public function testUtf8Strstr()
  {
    $tests = array(
        'ABC@中文空白.com' => array(
            'ABC',
            '@中文空白.com',
        ),
        ' @ - ÖÄÜ- '   => array(
            ' ',
            '@ - ÖÄÜ- ',
        ),
        'öä@ü'         => array(
            'öä',
            '@ü',
        ),
        ''             => array(
            false,
            false,
        ),
        '  '           => array(
            false,
            false,
        ),
    );

    foreach ($tests as $before => $after) {
      self::assertSame($after[0], UTF8::strstr($before, '@', true), 'tested: ' . $before);
      // alias
      self::assertSame($after[0], UTF8::strchr($before, '@', true), 'tested: ' . $before);
    }

    // ---

    foreach ($tests as $before => $after) {
      self::assertSame($after[1], UTF8::strstr($before, '@'), 'tested: ' . $before);
    }

    // --- UTF-8

    self::assertSame('ABC', UTF8::strstr('ABC@中文空白.com', '@', true, 'UTF-8'));
    self::assertSame('@中文空白.com', UTF8::strstr('ABC@中文空白.com', '@', false, 'UTF-8'));

    self::assertSame('ABC@', UTF8::strstr('ABC@中文空白.com', '中文空白', true, 'UTF-8'));
    self::assertSame('中文空白.com', UTF8::strstr('ABC@中文空白.com', '中文空白', false, 'UTF-8'));

    // --- ISO

    self::assertSame('ABC', UTF8::strstr('ABC@中文空白.com', '@', true, 'ISO'));
    self::assertSame('@中文空白.com', UTF8::strstr('ABC@中文空白.com', '@', false, 'ISO'));

    self::assertSame('ABC@', UTF8::strstr('ABC@中文空白.com', '中文空白', true, 'ISO'));
    self::assertSame('中文空白.com', UTF8::strstr('ABC@中文空白.com', '中文空白', false, 'ISO'));

    // --- false

    self::assertSame(false, UTF8::strstr('ABC@中文空白.com', 'z', true, 'UTF-8'));
    self::assertSame(false, UTF8::strstr('ABC@中文空白.com', 'z', false, 'UTF-8'));
    self::assertSame(false, UTF8::strstr('', 'z', true, 'UTF-8'));
    self::assertSame(false, UTF8::strstr('', 'z', false, 'UTF-8'));
  }

  public function testValidCharsViaUtf8Encode()
  {
    $tests = UTF8::json_decode(UTF8::file_get_contents(__DIR__ . '/valid.json'), true);

    foreach ($tests as $test) {
      self::assertSame($test, UTF8::encode('UTF-8', $test));
    }
  }

  public function testWhitespace()
  {
    $whitespaces = UTF8::whitespace_table();
    foreach ($whitespaces as $whitespace) {
      self::assertSame(' ', UTF8::clean($whitespace, false, true));
    }
  }

  public function testWordCount()
  {
    $testArray = array(
        '中文空白 öäü abc' => 3,
        'öäü öäü öäü'  => 3,
        'abc'          => 1,
        ''             => 0,
        ' '            => 0,
    );

    foreach ($testArray as $actual => $expected) {
      self::assertSame($expected, UTF8::str_word_count($actual));
    }

    self::assertSame(3, UTF8::str_word_count('中文空白 foo öäü'));
    self::assertSame(3, UTF8::str_word_count('中文空白 foo öäü', 0));
    self::assertSame(
        array(
            0 => '中文空白',
            1 => 'foo',
            2 => 'öäü',
        ),
        UTF8::str_word_count('中文空白 foo öäü', 1)
    );
    self::assertSame(3, UTF8::str_word_count('中文空白 foo öäü#s', 0, '#'));
    self::assertSame(4, UTF8::str_word_count('中文空白 foo öäü#s', 0, ''));
    self::assertSame(
        array(
            '中文空白',
            'foo',
            'öäü#s',
        ),
        UTF8::str_word_count('中文空白 foo öäü#s', 1, '#')
    );
    self::assertSame(
        array(
            0 => '中文空白',
            5 => 'foo',
            9 => 'öäü#s',
        ),
        UTF8::str_word_count('中文空白 foo öäü#s', 2, '#')
    );
    self::assertSame(
        array(
            0 => '中文空白',
            5 => 'foo',
            9 => 'öäü',
        ),
        UTF8::str_word_count('中文空白 foo öäü', 2)
    );
    self::assertSame(
        array(
            'test',
            'foo',
            'test',
            'test-test',
            'test',
            'test',
            'test\'s',
            'test’s',
            'test#s',
        ),
        UTF8::str_word_count('test,foo test test-test test_test test\'s test’s test#s', 1, '#')
    );
    self::assertSame(
        array(
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
        ),
        UTF8::str_word_count('test,foo test test-test test_test test\'s test’s test#s', 1)
    );
  }

  public function testWordsLimit()
  {
    $testArray = array(
        array('this is a test', 'this is a test', 5, '...'),
        array('this is öäü-foo test', 'this is öäü-foo test', 8, '...'),
        array('fòô...öäü', 'fòô bàř fòô', 1, '...öäü'),
        array('fòô', 'fòô bàř fòô', 1, ''),
        array('fòô bàř', 'fòô bàř fòô', 2, ''),
        array('fòô', 'fòô', 1, ''),
        array('', 'fòô', 0, ''),
        array('', '', 1, '...'),
        array('', '', 0, '...'),
    );

    foreach ($testArray as $test) {
      self::assertSame($test[0], UTF8::words_limit($test[1], $test[2], $test[3]), 'tested: ' . $test[1]);
    }
  }

  public function testWs()
  {
    $whitespace = UTF8::ws();

    self::assertSame(true, is_array($whitespace));
    self::assertSame(true, count($whitespace) > 0);
  }

  public function testcleanParameter()
  {
    $dirtyTestString = "\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃";

    self::assertSame("\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃", UTF8::clean($dirtyTestString));
    self::assertSame("\xEF\xBB\xBF„Abcdef \x20…” — 😃", UTF8::clean($dirtyTestString, false, true, false, false));
    self::assertSame("\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃", UTF8::clean($dirtyTestString, false, false, false, true));
    self::assertSame("\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃", UTF8::clean($dirtyTestString, false, false, false, false));
    self::assertSame("\xEF\xBB\xBF\"Abcdef\xc2\xa0\x20...\" - 😃", UTF8::clean($dirtyTestString, false, false, true, true));
    self::assertSame("\xEF\xBB\xBF\"Abcdef\xc2\xa0\x20...\" - 😃", UTF8::clean($dirtyTestString, false, false, true, false));
    self::assertSame("\xEF\xBB\xBF\"Abcdef  ...\" - 😃", UTF8::clean($dirtyTestString, false, true, true, false));
    self::assertSame("\xEF\xBB\xBF\"Abcdef\xc2\xa0\x20...\" - 😃", UTF8::clean($dirtyTestString, false, true, true, true));
    self::assertSame("„Abcdef\xc2\xa0\x20…” — 😃", UTF8::clean($dirtyTestString, true, false, false, false));
    self::assertSame("„Abcdef\xc2\xa0\x20…” — 😃", UTF8::clean($dirtyTestString, true, false, false, true));
    self::assertSame("\"Abcdef\xc2\xa0\x20...\" - 😃", UTF8::clean($dirtyTestString, true, false, true, false));
    self::assertSame("\"Abcdef\xc2\xa0\x20...\" - 😃", UTF8::clean($dirtyTestString, true, false, true, true));
    self::assertSame('„Abcdef  …” — 😃', UTF8::clean($dirtyTestString, true, true, false, false));
    self::assertSame('„Abcdef  …” — 😃', UTF8::clean($dirtyTestString, true, true, false, true));
    self::assertSame('"Abcdef  ..." - 😃', UTF8::clean($dirtyTestString, true, true, true, false));
    self::assertSame("\"Abcdef\xc2\xa0 ...\" - 😃", UTF8::clean($dirtyTestString, true, true, true, true));
  }

  public function testhex_to_chr()
  {
    self::assertEquals('<', UTF8::hex_to_chr('3c'));
    self::assertEquals('<', UTF8::hex_to_chr('003c'));
    self::assertEquals('&', UTF8::hex_to_chr('26'));
    self::assertEquals('}', UTF8::hex_to_chr('7d'));
    self::assertEquals('Σ', UTF8::hex_to_chr('3A3'));
    self::assertEquals('Σ', UTF8::hex_to_chr('03A3'));
    self::assertEquals('Σ', UTF8::hex_to_chr('3a3'));
    self::assertEquals('Σ', UTF8::hex_to_chr('03a3'));
  }

  public function testhtml_encode_chr()
  {
    self::assertEquals('\'', UTF8::decimal_to_chr(39));
    self::assertEquals('\'', UTF8::decimal_to_chr('39'));
    self::assertEquals('&', UTF8::decimal_to_chr(38));
    self::assertEquals('&', UTF8::decimal_to_chr('38'));
    self::assertEquals('<', UTF8::decimal_to_chr(60));
    self::assertEquals('Σ', UTF8::decimal_to_chr(931));
    self::assertEquals('Σ', UTF8::decimal_to_chr('0931'));
    // alias
    self::assertEquals('Σ', UTF8::int_to_chr('0931'));
  }

  /**
   * @return array
   */
  public function trimProvider()
  {
    return array(
        array(
            1,
            '1',
        ),
        array(
            -1,
            '-1',
        ),
        array(
            '  ',
            '',
        ),
        array(
            '',
            '',
        ),
        array(
            '　中文空白　 ',
            '中文空白',
        ),
        array(
            'do not go gentle into that good night',
            'do not go gentle into that good night',
        ),
    );
  }

  /**
   * @return array
   */
  public function trimProviderAdvanced()
  {
    return array(
        array(
            1,
            '1',
        ),
        array(
            -1,
            '-1',
        ),
        array(
            '  ',
            '',
        ),
        array(
            '',
            '',
        ),
        array(
            ' 白 ',
            '白',
        ),
        array(
            '   白白 ',
            '白白',
        ),
        array(
            '　中文空白',
            '　中文空白',
        ),
        array(
            'do not go gentle into that good night',
            'do not go gentle into that good night',
        ),
    );
  }

  /**
   * @return array
   */
  public function trimProviderAdvancedWithMoreThenTwoBytes()
  {
    return array(
        array(
            1,
            '1',
        ),
        array(
            -1,
            '-1',
        ),
        array(
            '  ',
            '  ',
        ),
        array(
            '',
            '',
        ),
        array(
            '白',
            '',
        ),
        array(
            '白白',
            '',
        ),
        array(
            '　中文空白',
            '　中文空',
        ),
        array(
            'do not go gentle into that good night',
            'do not go gentle into that good night',
        ),
    );
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

    self::assertSame($expected, $result);
  }

  /**
   * @return array
   */
  public function stripWhitespaceProvider()
  {
    return array(
        array('foobar', '  foo   bar  '),
        array('teststring', 'test string'),
        array('Οσυγγραφέας', '   Ο     συγγραφέας  '),
        array('123', ' 123 '),
        array('', ' ', 'UTF-8'), // no-break space (U+00A0)
        array('', '           ', 'UTF-8'), // spaces U+2000 to U+200A
        array('', ' ', 'UTF-8'), // narrow no-break space (U+202F)
        array('', ' ', 'UTF-8'), // medium mathematical space (U+205F)
        array('', '　', 'UTF-8'), // ideographic space (U+3000)
        array('123', '  1  2  3　　', 'UTF-8'),
        array('', ' '),
        array('', ''),
    );
  }
}
