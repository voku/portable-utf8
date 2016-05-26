<?php

use voku\helper\Bootup;
use voku\helper\UTF8;

/**
 * Class Utf8GlobalTest
 */
class Utf8GlobalTest extends PHPUnit_Framework_TestCase
{

  public function testStrlen()
  {
    $string = 'string <strong>with utf-8 chars åèä</strong> - doo-bee doo-bee dooh';

    self::assertEquals(70, strlen($string));
    self::assertEquals(67, UTF8::strlen($string));

    $string_test1 = strip_tags($string);
    $string_test2 = UTF8::strip_tags($string);

    self::assertEquals(53, strlen($string_test1));
    self::assertEquals(50, UTF8::strlen($string_test2));

    $testArray = array(
        "<a href='κόσμε'>κόσμε</a>" => 25,
        '<白>'                       => 3,
        'öäü'                       => 3,
        ' '                         => 1,
        ''                          => 0,
        1                           => 1,
        -1                          => 2,
    );

    foreach ($testArray as $actual => $expected) {
      self::assertEquals($expected, UTF8::strlen($actual), $actual);
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
      self::assertEquals($expected, strlen($actual), $actual);
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
    );

    foreach ($testArray as $actual => $expected) {
      self::assertEquals($expected, UTF8::htmlspecialchars($actual));
    }
  }

  public function testHtmlentities()
  {
    $testArray = array(
        '<白>'                                                                                                         => '&lt;白&gt;',
        'dies ist ein test „Goldenen Regeln und Checklisten“.<br /><br /><br />' . UTF8::html_entity_decode('&nbsp;') => 'dies ist ein test &bdquo;Goldenen Regeln und Checklisten&ldquo;.&lt;br /&gt;&lt;br /&gt;&lt;br /&gt;&nbsp;',
        'öäü'                                                                                                         => '&ouml;&auml;&uuml;',
        ' '                                                                                                           => ' ',
        ''                                                                                                            => '',
    );

    foreach ($testArray as $actual => $expected) {
      self::assertEquals($expected, UTF8::htmlentities($actual));
    }
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
        self::assertEquals($expected, UTF8::fits_inside($actual, $size), 'error by ' . $actual);
      }
    }
  }

  public function testFixBrokenUtf8()
  {
    $testArray = array(
        'DÃ¼sseldorf'                                     => 'Düsseldorf',
        'Ã¤'                                              => 'ä',
        ' '                                               => ' ',
        ''                                                => '',
        "\n"                                              => "\n",
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
      self::assertEquals($after, UTF8::fix_utf8($before));
    }

    self::assertEquals(array('Düsseldorf', 'Fédération'), UTF8::fix_utf8(array('DÃ¼sseldorf', 'FÃÂÂÂÂ©dÃÂÂÂÂ©ration')));
  }

  public function testParseStr()
  {
    // test-string
    $str = "Iñtërnâtiôn\xE9àlizætiøn=測試&arr[]=foo+測試&arr[]=ການທົດສອບ";

    UTF8::parse_str($str, $array);

    // WARNING: HipHop VM 3.5.0 error via travis-ci // "Undefined index: arr"
    if (!defined('HHVM_VERSION')) {
      self::assertEquals('foo 測試', $array['arr'][0]);
      self::assertEquals('ການທົດສອບ', $array['arr'][1]);
    }

    self::assertEquals('測試', $array['Iñtërnâtiônéàlizætiøn']);
  }

  public function testIsUtf8()
  {
    $testArray = array(
        1                                                                  => true,
        -1                                                                 => true,
        'κ'                                                                => true,
        ''                                                                 => true,
        ' '                                                                => true,
        "\n"                                                               => true,
        'abc'                                                              => true,
        'abcöäü'                                                           => true,
        '白'                                                                => true,
        'សាកល្បង!'                                                         => true,
        'דיעס איז אַ פּרובירן!'                                            => true,
        'Штампи іст Ейн тест!'                                             => true,
        'Штампы гіст Эйн тэст!'                                            => true,
        '測試！'                                                              => true,
        'ການທົດສອບ!'                                                       => true,
        'Iñtërnâtiônàlizætiøn'                                             => true,
        'ABC 123'                                                          => true,
        "Iñtërnâtiôn\xE9àlizætiøn"                                         => false,
        "\xf0\x28\x8c\x28"                                                 => false,
        "this is an invalid char '\xE9' here"                              => false,
        "\xC3\xB1"                                                         => true,
        "Iñtërnâtiônàlizætiøn \xC3\x28 Iñtërnâtiônàlizætiøn"               => false,
        "Iñtërnâtiônàlizætiøn\xA0\xA1Iñtërnâtiônàlizætiøn"                 => false,
        "Iñtërnâtiônàlizætiøn\xE2\x82\xA1Iñtërnâtiônàlizætiøn"             => true,
        "Iñtërnâtiônàlizætiøn\xE2\x28\xA1Iñtërnâtiônàlizætiøn"             => false,
        "Iñtërnâtiônàlizætiøn\xE2\x82\x28Iñtërnâtiônàlizætiøn"             => false,
        "Iñtërnâtiônàlizætiøn\xF0\x90\x8C\xBCIñtërnâtiônàlizætiøn"         => true,
        "Iñtërnâtiônàlizætiøn\xF0\x28\x8C\xBCIñtërnâtiônàlizætiøn"         => false,
        "Iñtërnâtiônàlizætiøn\xf8\xa1\xa1\xa1\xa1Iñtërnâtiônàlizætiøn"     => false,
        "Iñtërnâtiônàlizætiøn\xFC\xA1\xA1\xA1\xA1\xA1Iñtërnâtiônàlizætiøn" => false,
        "\xC3\x28"                                                         => false,
        "\xA0\xA1"                                                         => false,
        "\xE2\x82\xA1"                                                     => true,
        "\xE2\x28\xA1"                                                     => false,
        "\xE2\x82\x28"                                                     => false,
        "\xF0\x90\x8C\xBC"                                                 => true,
        "\xF0\x28\x8C\xBC"                                                 => false,
        "\xF0\x90\x28\xBC"                                                 => false,
        "\xF0\x28\x8C\x28"                                                 => false,
        "\xF8\xA1\xA1\xA1\xA1"                                             => false,
        "\xFC\xA1\xA1\xA1\xA1\xA1"                                         => false,
    );

    $conter = 0;
    foreach ($testArray as $actual => $expected) {
      self::assertEquals($expected, UTF8::is_utf8($actual), 'error by - ' . $conter . ' :' . $actual);
      $conter++;
    }

    $conter = 0;
    foreach ($testArray as $actual => $expected) {
      self::assertEquals($expected, UTF8::isUtf8($actual), 'error by - ' . $conter . ' :' . $actual);
      $conter++;
    }
  }

  public function testCountChars()
  {
    $testArray = array(
        'κaκbκc' => array(
            'a' => 1,
            'b' => 1,
            'c' => 1,
            'κ' => 3,
        ),
        'cba'    => array(
            'a' => 1,
            'b' => 1,
            'c' => 1,
        ),
        'abcöäü' => array(
            'a' => 1,
            'b' => 1,
            'c' => 1,
            'ä' => 1,
            'ö' => 1,
            'ü' => 1,
        ),
        '白白'     => array('白' => 2),
        ''       => array(),
    );

    foreach ($testArray as $actual => $expected) {
      self::assertEquals($expected, UTF8::count_chars($actual), 'error by ' . $actual);
    }
  }

  public function testStringHasBom()
  {
    $testArray = array(
        UTF8::bom() . 'κ'      => true,
        'abc'                  => false,
        UTF8::bom() . 'abcöäü' => true,
        '白'                    => false,
        UTF8::bom()            => true,
    );

    foreach ($testArray as $actual => $expected) {
      self::assertEquals($expected, UTF8::string_has_bom($actual), 'error by ' . $actual);
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
      self::assertEquals($expected, UTF8::strrev($actual), 'error by ' . $actual);
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
    );

    foreach ($testArray as $actual => $expected) {
      self::assertEquals($expected, UTF8::is_ascii($actual), 'error by ' . $actual);
    }

    foreach ($testArray as $actual => $expected) {
      self::assertEquals($expected, UTF8::isAscii($actual), 'error by ' . $actual);
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
      self::assertEquals($expected, UTF8::strrichr($actual, 'κόσμε'), 'error by ' . $actual);
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
      self::assertEquals($expected, UTF8::strrchr($actual, 'κόσμε'), 'error by ' . $actual);
    }
  }

  public function testGetCharDirection()
  {
    $testArray = array(
        'ا'   => 'RTL',
        'abc' => 'LTR',
        '?'   => 'LTR',
    );

    foreach ($testArray as $actual => $expected) {
      self::assertEquals($expected, UTF8::getCharDirection($actual), 'error by ' . $actual);
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
        'who&#039;s online'                                                                         => 'who&#x27;s online',
        'who&amp;#039;s online'                                                                     => 'who&#x27;s online',
        'who&#039;s online-'                                                                        => 'who&#x27;s online-',
        'Who&#039;s Online'                                                                         => 'Who&#x27;s Online',
        'Who&amp;#039;s Online'                                                                     => 'Who&#x27;s Online',
        'Who&amp;amp;#039;s Online &#20013;'                                                        => 'Who&#x27;s Online 中',
        'who\'s online&colon;'                                                                      => 'who\'s online&colon;',
        "Who\'s Online&#x0003A;"                                                                    => 'Who\\\'s Online:',
        '&lt;&copy; W3S&ccedil;h&deg;&deg;&brvbar;&sect;&gt;'                                       => '<© W3Sçh°°¦§>',
    );

    // WARNING: HipHop error // "ENT_COMPAT" isn't working
    if (defined('HHVM_VERSION') === false) {
      foreach ($testArray as $before => $after) {
        self::assertEquals($after, UTF8::html_entity_decode($before, ENT_COMPAT), 'error by ' . $before);
      }
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
        'who\'s online&colon;'                                                                      => 'who\'s online&colon;',
        "Who\'s Online&#x0003A;"                                                                    => 'Who\\\'s Online:',
        '&lt;&copy; W3S&ccedil;h&deg;&deg;&brvbar;&sect;&gt;'                                       => '<© W3Sçh°°¦§>',
    );

    foreach ($testArray as $before => $after) {
      self::assertEquals($after, UTF8::html_entity_decode($before, ENT_QUOTES, 'UTF-8'), 'error by ' . $before);
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
        'who\'s online&colon;'                                                                      => 'who\'s online:',
        "Who\'s Online&#x0003A;"                                                                    => 'Who\\\'s Online:',
        '&lt;&copy; W3S&ccedil;h&deg;&deg;&brvbar;&sect;&gt;'                                       => '<© W3Sçh°°¦§>',
    );

    if (Bootup::is_php('5.4') === true && defined('HHVM_VERSION') !== true) {
      foreach ($testArray as $before => $after) {
        self::assertEquals($after, UTF8::html_entity_decode($before, ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'error by ' . $before);
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
      self::assertEquals($after, UTF8::remove_invisible_characters($before), 'error by ' . $before);
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

    foreach ($testBom as $count => $test) {
      self::assertEquals(
          'Μπορώ να φάω σπασμένα γυαλιά χωρίς να πάθω τίποτα',
          UTF8::removeBOM($test),
          'error by ' . $count
      );
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
        self::assertEquals($expected, UTF8::remove_duplicates($actual, $filter));
      }
    }
  }

  public function testRange()
  {
    $expected = array(
        'κ',
        'ι',
        'θ',
        'η',
        'ζ',
    );

    self::assertEquals($expected, UTF8::range('κ', 'ζ'));
    self::assertEquals(0, count(UTF8::range('κ', '')));

  }

  public function testHash()
  {
    $testArray = array(
        2,
        8,
        0,
        100,
        1234,
    );

    foreach ($testArray as $testValue) {
      self::assertEquals($testValue, UTF8::strlen(UTF8::hash($testValue)));
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
    self::assertEquals($expected, $actual);
  }

  public function testAccess()
  {
    $testArray = array(
        ''          => array(1 => ''),
        '中文空白'      => array(2 => '空'),
        '中文空白-test' => array(3 => '白'),
    );

    foreach ($testArray as $actualString => $testDataArray) {
      foreach ($testDataArray as $stringPos => $expectedString) {
        self::assertEquals($expectedString, UTF8::access($actualString, $stringPos));
      }
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
      self::assertEquals($after, UTF8::str_sort($before));
    }

    $tests = array(
        '  -ABC-中文空白-  ' => '空白文中CBA---    ',
        '      - ÖÄÜ- '  => 'ÜÖÄ--        ',
        'öäü'            => 'üöä',
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::str_sort($before, false, true));
    }

    $tests = array(
        '    '           => ' ',
        '  -ABC-中文空白-  ' => ' -ABC中文白空',
        '      - ÖÄÜ- '  => ' -ÄÖÜ',
        'öäü'            => 'äöü',
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::str_sort($before, true));
    }

    $tests = array(
        '  -ABC-中文空白-  ' => '空白文中CBA- ',
        '      - ÖÄÜ- '  => 'ÜÖÄ- ',
        'öäü'            => 'üöä',
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::str_sort($before, true, true));
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
            '',
            '',
        ),
        '  '           => array(
            '',
            '',
        ),
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after[0], UTF8::strstr($before, '@', true), $before);
    }

    foreach ($tests as $before => $after) {
      self::assertEquals($after[1], UTF8::strstr($before, '@'), $before);
    }
  }

  public function testValidCharsViaUtf8Encode()
  {
    $tests = UTF8::json_decode(UTF8::file_get_contents(__DIR__ . '/test-valid-chars.json'), true);

    foreach ($tests as $test) {
      self::assertEquals($test, UTF8::encode('UTF-8', $test));
    }
  }

  public function testUtf8DecodeUtf8Encode()
  {
    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-????-  ',
        '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
        'öäü'            => 'öäü',
        ''               => '',
        'foobar'         => 'foobar',
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::utf8_encode((UTF8::utf8_decode($before))));
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
      self::assertEquals($after, UTF8::utf8_decode(UTF8::utf8_encode($before)));
    }
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

    $result = array();
    $i = 0;
    foreach ($testArray as $test) {

      $result[$i] = UTF8::to_utf8($test);

      self::assertEquals($test, $result[$i]);

      $i++;
    }

    // test with array
    self::assertEquals($result, UTF8::to_utf8($testArray));

    foreach ($testArray as $test) {
      self::assertEquals($test, UTF8::to_utf8(UTF8::to_utf8($test)));
    }
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
      self::assertEquals($after, UTF8::encode('UTF-8', UTF8::encode('UTF-8', $before)));
    }
  }

  public function testEncodeUtf8()
  {
    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-中文空白-  ',
        '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
        'öäü'            => 'öäü',
        ''               => '',
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::encode('UTF-8', $before));
    }

    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-????-  ',
        '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
        'öäü'            => 'öäü',
        ''               => '',
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::filter(UTF8::encode('ISO-8859-1', $before)));
    }
  }

  public function testNormalizeEncoding()
  {
    $tests = array(
        'ISO'          => 'ISO-8859-1',
        'UTF8'         => 'UTF-8',
        'WINDOWS-1251' => 'ISO-8859-5',
        ''             => '',
        'Utf-8'        => 'UTF-8',
        'UTF-8'        => 'UTF-8',
        'ISO-8859-5'   => 'ISO-8859-5',
        false          => false,
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::normalizeEncoding($before));
    }
  }

  public function testUtf8DecodeEncodeUtf8()
  {
    $tests = array(
        '  -ABC-中文空白-  ' => '  -ABC-????-  ',
        '      - ÖÄÜ- '  => '      - ÖÄÜ- ',
        'öäü'            => 'öäü',
        ''               => '',
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::encode('UTF-8', UTF8::utf8_decode($before)));
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
      self::assertEquals($after, UTF8::utf8_encode(UTF8::encode('UTF-8', $before)));
    }
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
    );

    foreach ($testArray as $before => $after) {
      self::assertEquals($after, UTF8::fix_simple_utf8($before));
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
      self::assertEquals($after, UTF8::encode('UTF-8', UTF8::utf8_encode($before)));
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
      self::assertEquals($after, UTF8::utf8_encode(UTF8::utf8_encode($before)));
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
      self::assertEquals($after, UTF8::utf8_encode($before));
    }
  }

  public function testUtf8FileWithBom()
  {
    $bom = UTF8::file_has_bom(__DIR__ . '/fixtures/test1Utf8Bom.txt');
    self::assertEquals(true, $bom);

    $bom = UTF8::file_has_bom(__DIR__ . '/fixtures/test1Utf8.txt');
    self::assertEquals(false, $bom);
  }

  public function testIsBinary()
  {
    $tests = array(
        'öäü'          => false,
        ''             => false,
        '1'            => false,
        decbin(324546) => true,
        01             => true,
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::is_binary($before), 'value: ' . $before);
    }
  }

  public function testStrDetectEncoding()
  {
    $tests = array(
        'に対するパッチです' => 'UTF-8', // ISO-2022-JP, but PHP can't detect it ...
        'ASCII'    => 'ASCII', // ASCII
        'Iñtërnâtiônàlizætiøn' => 'UTF-8', // UTF-8
        '亜 唖 娃 阿 哀 愛 挨 姶 逢 葵 茜 穐 悪 握 渥' => 'UTF-8', // EUC-JP
        'áéóú' => 'UTF-8', // ISO-8859-1
        '☺' => 'UTF-8',
        '☃' => 'UTF-8',
        '○●◎' => 'UTF-8',
        'öäü'          => 'UTF-8', // ISO-8859-1
        ''             => 'ASCII', // ASCII
        '1'            => 'ASCII', // ASCII
        decbin(324546) => 'ASCII', // ASCII
        01             => 'ASCII', // ASCII
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::str_detect_encoding($before), 'value: ' . $before);
    }
  }

  public function testFileGetContents()
  {
    // INFO: UTF-8 shim only works for UTF-8
    if (UTF8::mbstring_loaded() === true) {

      $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/test1Utf16pe.txt');
      self::assertContains(
          '<p>Today’s Internet users are not the same users who were online a decade ago. There are better connections.',
          $testString
      );

      $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/test1Utf16le.txt');
      self::assertContains(
          '<p>Today’s Internet users are not the same users who were online a decade ago. There are better connections.',
          $testString
      );

      $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/test1Utf8.txt');
      self::assertContains('Iñtërnâtiônàlizætiøn', $testString);

      $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/test1Latin.txt');
      self::assertContains('Iñtërnâtiônàlizætiøn', $testString);

      $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/test1Iso8859-7.txt');
      self::assertContains('Iñtërnâtiônàlizætiøn', $testString);

      $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/test1Utf16pe.txt', FILE_TEXT);
      self::assertContains(
          '<p>Today’s Internet users are not the same users who were online a decade ago. There are better connections.',
          $testString
      );

      $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/test1Utf16le.txt', null, null, 0);
      self::assertContains(
          '<p>Today’s Internet users are not the same users who were online a decade ago. There are better connections.',
          $testString
      );

      // text: with offset
      $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/test1Utf16le.txt', null, null, 5);
      self::assertContains('There are better connections.', $testString);

      // text: with offset & max-length
      $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/test1Utf8.txt', null, null, 7, 11);
      self::assertContains('Iñtërnât', $testString);

      // text: with offset & max-length + timeout
      $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/test1Latin.txt', null, null, 7, 10, 15);
      self::assertContains('ñtërnâtiôn', $testString);

      // text: with timeout
      $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/test1Iso8859-7.txt', null, null, 7, null, 10);
      self::assertContains('Iñtërnâtiônàlizætiøn', $testString);

      // text: with max-length + timeout
      $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/test1Iso8859-7.txt', null, null, null, 10, 10);
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
      $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/test1Iso8859-7.txt', null, $context, null, 10, 10);
      self::assertContains('Hírek', $testString);

      // text: do not convert to utf-8 + timeout
      $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/test1Iso8859-7.txt', null, $context, null, 10, 10, false);
      self::assertRegExp('#H.*rek#', $testString);

      // text: do not convert to utf-8 + timeout
      $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/test1Utf8.txt', null, $context, null, 10, 10, false);
      self::assertContains('Hírek', $testString);

      // image: do not convert to utf-8 + timeout
      $image = UTF8::file_get_contents(__DIR__ . '/fixtures/test-image.png', null, $context, null, null, 10, false);
      self::assertEquals(true, UTF8::is_binary($image));

      // image: convert to utf-8 + timeout (ERROR)
      $image2 = UTF8::file_get_contents(__DIR__ . '/fixtures/test-image.png', null, $context, null, null, 10, true);
      self::assertEquals(false, UTF8::is_binary($image2));

      self::assertNotEquals($image2, $image);
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
      self::assertEquals($after, UTF8::to_utf8(UTF8::to_latin1($before)));
    }

    self::assertEquals($tests, UTF8::to_utf8(UTF8::to_latin1($tests)));
  }

  public function testNumberFormat()
  {
    self::assertEquals('1.23', UTF8::number_format('1.234567', 2, '.', ''));
    self::assertEquals('1,3', UTF8::number_format('1.298765', 1, ',', ''));
  }

  public function testSubstr()
  {
    self::assertEquals(23, substr(1234, 1, 2));
    self::assertEquals('bc', substr('abcde', 1, 2));
    self::assertEquals('de', substr('abcde', -2, 2));
    self::assertEquals('bc', substr('abcde', 1, 2));
    self::assertEquals('bc', substr('abcde', 1, 2));
    self::assertEquals('bcd', substr('abcde', 1, 3));
    self::assertEquals('bc', substr('abcde', 1, 2));

    self::assertEquals(23, UTF8::substr(1234, 1, 2));
    self::assertEquals('bc', UTF8::substr('abcde', 1, 2));
    self::assertEquals('de', UTF8::substr('abcde', -2, 2));
    self::assertEquals('bc', UTF8::substr('abcde', 1, 2));
    self::assertEquals('bc', UTF8::substr('abcde', 1, 2, true));
    self::assertEquals('bc', UTF8::substr('abcde', 1, 2, 'UTF-8', true));
    self::assertEquals('bcd', UTF8::substr('abcde', 1, 3));
    self::assertEquals('bc', UTF8::substr('abcde', 1, 2));

    // UTF-8
    self::assertEquals('文空', UTF8::substr('中文空白', 1, 2));
  }

  public function testSubstrCount()
  {
    self::assertEquals(false, substr_count('', ''));
    self::assertEquals(false, substr_count('', '', 1));
    self::assertEquals(false, substr_count('', '', 1, 1));
    self::assertEquals(false, substr_count('', 'test', 1, 1));
    self::assertEquals(false, substr_count('test', '', 1, 1));
    self::assertEquals(0, substr_count('test', 'test', 1, 1));
    self::assertEquals(1, substr_count(12345, 23, 1, 2));
    self::assertEquals(2, substr_count('abcdebc', 'bc'));
    self::assertEquals(0, substr_count('abcde', 'de', -2, 2));
    self::assertEquals(0, substr_count('abcde', 'bcg', 1, 2));
    self::assertEquals(0, substr_count('abcde', 'BC', 1, 2));
    self::assertEquals(1, substr_count('abcde', 'bc', 1, 3));
    self::assertEquals(0, substr_count('abcde', 'cd', 1, 2));

    self::assertEquals(false, UTF8::substr_count('', ''));
    self::assertEquals(false, UTF8::substr_count('', '', 1));
    self::assertEquals(false, UTF8::substr_count('', '', 1, 1));
    self::assertEquals(false, UTF8::substr_count('', 'test', 1, 1));
    self::assertEquals(false, UTF8::substr_count('test', '', 1, 1));
    self::assertEquals(1, UTF8::substr_count(12345, 23, 1, 2));
    self::assertEquals(2, UTF8::substr_count('abcdebc', 'bc'));
    self::assertEquals(1, UTF8::substr_count('abcde', 'de', -2, 2));
    self::assertEquals(0, UTF8::substr_count('abcde', 'bcg', 1, 2));
    self::assertEquals(0, UTF8::substr_count('abcde', 'BC', 1, 2));
    self::assertEquals(1, UTF8::substr_count('abcde', 'bc', 1, 3));
    self::assertEquals(0, UTF8::substr_count('abcde', 'cd', 1, 2));

    // UTF-8
    self::assertEquals(2, UTF8::substr_count("○●◎\r◎", '◎'));
    self::assertEquals(1, UTF8::substr_count("○●◎\r", '●◎', 1, 2));
    self::assertEquals(1, UTF8::substr_count('中文空白', '文空', 1, 2));
  }

  public function testSubstrCompare()
  {
    self::assertEquals(0, substr_compare(12345, 23, 1, 2));
    self::assertEquals(0, substr_compare('abcde', 'bc', 1, 2));
    self::assertEquals(0, substr_compare('abcde', 'de', -2, 2));
    self::assertEquals(0, substr_compare('abcde', 'bcg', 1, 2));
    self::assertEquals(0, substr_compare('abcde', 'BC', 1, 2, true));
    self::assertEquals(1, substr_compare('abcde', 'bc', 1, 3));
    self::assertEquals(-1, substr_compare('abcde', 'cd', 1, 2));

    self::assertEquals(0, UTF8::substr_compare(12345, 23, 1, 2));
    self::assertEquals(0, UTF8::substr_compare('abcde', 'bc', 1, 2));
    self::assertEquals(0, UTF8::substr_compare('abcde', 'de', -2, 2));
    self::assertEquals(0, UTF8::substr_compare('abcde', 'bcg', 1, 2));
    self::assertEquals(0, UTF8::substr_compare('abcde', 'BC', 1, 2, true));
    self::assertEquals(1, UTF8::substr_compare('abcde', 'bc', 1, 3));
    self::assertEquals(-1, UTF8::substr_compare('abcde', 'cd', 1, 2));

    // UTF-8
    self::assertEquals(0, UTF8::substr_compare("○●◎\r", '●◎', 1, 2, false));
    self::assertEquals(0, UTF8::substr_compare('中文空白', '文空', 1, 2, true));
  }

  public function testStrtr()
  {
    $arr = array(
        'Hello' => 'Hi',
        'world' => 'earth',
    );
    self::assertEquals('Hi earth', strtr('Hello world', $arr));
    self::assertEquals('Hi earth', UTF8::strtr('Hello world', $arr));

    // UTF-8
    $arr = array(
        'Hello' => '○●◎',
        '中文空白'  => 'earth',
    );
    self::assertEquals('○●◎ earth', UTF8::strtr('Hello 中文空白', $arr));

    // extra
    self::assertEquals('○●◎◎o wor◎d', UTF8::strtr('Hello world', 'Hello', '○●◎'));
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
      self::assertEquals($after, UTF8::str_repeat($before, 17));
    }
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
      self::assertEquals($testResult, $this->cleanString($testString));
    }
  }

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

  public function testFilterInput()
  {
    $options = array(
        'options' => array(
            'default'   => -1,
            // value to return if the filter fails
            'min_range' => 90,
            'max_range' => 99,
        ),
    );

    self::assertEquals('  -ABC-中文空白-  ', UTF8::filter_var('  -ABC-中文空白-  ', FILTER_DEFAULT));
    self::assertEquals(false, UTF8::filter_var('  -ABC-中文空白-  ', FILTER_VALIDATE_URL));
    self::assertEquals(false, UTF8::filter_var('  -ABC-中文空白-  ', FILTER_VALIDATE_EMAIL));
    self::assertEquals(-1, UTF8::filter_var('中文空白 ', FILTER_VALIDATE_INT, $options));
    self::assertEquals('99', UTF8::filter_var(99, FILTER_VALIDATE_INT, $options));
    self::assertEquals(-1, UTF8::filter_var(100, FILTER_VALIDATE_INT, $options));
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

    self::assertEquals(
        array(
            'name'  => 'Κόσμε',
            'age'   => 18,
            'email' => 'foo@bar.de',
        ),
        UTF8::filter_var_array($data, $filters, true)
    );

    self::assertEquals(
        array(
            'name'  => 'κόσμε',
            'age'   => '18',
            'email' => 'foo@bar.de',
        ),
        UTF8::filter_var_array($data)
    );
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
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::replace_diamond_question_mark($before, ''));
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
      self::assertEquals($after, UTF8::normalize_msword($before));
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

    for ($i = 0; $i < 10; $i++) {
      foreach ($tests as $before => $after) {
        self::assertEquals($after, UTF8::normalize_whitespace($before));
      }
    }

    // replace "non breaking space"
    self::assertEquals('abc- -öäü- -', UTF8::normalize_whitespace("abc-\xc2\xa0-öäü-\xe2\x80\xaf-\xE2\x80\xAC"));
    // keep "non breaking space"
    self::assertEquals("abc-\xc2\xa0-öäü- -", UTF8::normalize_whitespace("abc-\xc2\xa0-öäü-\xe2\x80\xaf-\xE2\x80\xAC", true));
    // ... and keep "bidirectional text chars"
    self::assertEquals("abc-\xc2\xa0-öäü- -\xE2\x80\xAC", UTF8::normalize_whitespace("abc-\xc2\xa0-öäü-\xe2\x80\xaf-\xE2\x80\xAC", true, true));
  }

  public function testString()
  {
    self::assertEquals('', UTF8::string(array()));
    self::assertEquals(
        'öäü',
        UTF8::string(
            array(
                246,
                228,
                252,
            )
        )
    );
    self::assertEquals(
        'ㅡㅡ',
        UTF8::string(
            array(
                12641,
                12641,
            )
        )
    );
  }

  public function testStripTags()
  {
    $tests = array(
        ''                                                                        => '',
        ' '                                                                       => ' ',
        '<nav>中文空白 </nav>'                                                        => '中文空白 ',
        "<ㅡㅡ></ㅡㅡ><div></div><input type='email' name='user[email]' /><a>wtf</a>" => 'wtf',
        '<nav>DÃ¼sseldorf</nav>'                                                  => 'DÃ¼sseldorf',
        'Abcdef'                                                                  => 'Abcdef',
        "<span>κόσμε\xa0\xa1</span>-<span>öäü</span>öäü"                          => 'κόσμε-öäüöäü',
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::strip_tags($before));
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

    self::assertEquals($expectedString, $actualString);

    self::assertEquals('中文空白______', UTF8::str_pad('中文空白', 10, '_', STR_PAD_RIGHT));
    self::assertEquals('______中文空白', UTF8::str_pad('中文空白', 10, '_', STR_PAD_LEFT));
    self::assertEquals('___中文空白___', UTF8::str_pad('中文空白', 10, '_', STR_PAD_BOTH));

    $toPad = '<IñtërnëT>'; // 10 characters
    $padding = 'ø__'; // 4 characters

    self::assertEquals($toPad . '          ', UTF8::str_pad($toPad, 20));
    self::assertEquals('          ' . $toPad, UTF8::str_pad($toPad, 20, ' ', STR_PAD_LEFT));
    self::assertEquals('     ' . $toPad . '     ', UTF8::str_pad($toPad, 20, ' ', STR_PAD_BOTH));

    self::assertEquals($toPad, UTF8::str_pad($toPad, 10));
    self::assertEquals('5char', str_pad('5char', 4)); // str_pos won't truncate input string
    self::assertEquals($toPad, UTF8::str_pad($toPad, 8));

    self::assertEquals($toPad . 'ø__ø__ø__ø', UTF8::str_pad($toPad, 20, $padding, STR_PAD_RIGHT));
    self::assertEquals('ø__ø__ø__ø' . $toPad, UTF8::str_pad($toPad, 20, $padding, STR_PAD_LEFT));
    self::assertEquals('ø__ø_' . $toPad . 'ø__ø_', UTF8::str_pad($toPad, 20, $padding, STR_PAD_BOTH));
  }

  /**
   * @dataProvider trimProvider
   *
   * @param $input
   * @param $output
   */
  public function testTrim($input, $output)
  {
    for ($i = 0; $i <= 100; $i++) {
      self::assertEquals($output, UTF8::trim($input));
    }
  }

  /**
   * @dataProvider trimProviderAdvancedWithMoreThenTwoBytes
   *
   * @param $input
   * @param $output
   */
  public function testTrimAdvancedWithMoreThenTwoBytes($input, $output)
  {
    self::assertEquals($output, UTF8::trim($input, '白'));
  }

  /**
   * @return array
   */
  public function trimProviderAdvancedWithMoreThenTwoBytes()
  {
    return array(
        array(
            1,
            1,
        ),
        array(
            -1,
            -1,
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
   * @dataProvider trimProviderAdvanced
   *
   * @param $input
   * @param $output
   */
  public function testTrimAdvanced($input, $output)
  {
    self::assertEquals($output, UTF8::trim($input, ' '));
  }

  /**
   * @return array
   */
  public function trimProviderAdvanced()
  {
    return array(
        array(
            1,
            1,
        ),
        array(
            -1,
            -1,
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
  public function trimProvider()
  {
    return array(
        array(
            1,
            1,
        ),
        array(
            -1,
            -1,
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

  public function testToUtf8()
  {
    $examples = array(
      // Valid UTF-8
      'κόσμε'                                       => array('κόσμε' => 'κόσμε'),
      '中'                                           => array('中' => '中'),
      // Valid UTF-8 + Invalied Chars
      "κόσμε\xa0\xa1-öäü"                           => array('κόσμε-öäü' => 'κόσμε-öäü'),
      // Valid emoji (non-UTF-8)
      '👍 💩 😄 ❤ 👍 💩 😄 ❤'                        => array('👍 💩 😄 ❤ 👍 💩 😄 ❤' => '👍 💩 😄 ❤ 👍 💩 😄 ❤'),
      // Valid ASCII
      'a'                                           => array('a' => 'a'),
      // Valid ASCII + Invalied Chars
      "a\xa0\xa1-öäü"                               => array('a-öäü' => 'a-öäü'),
      // Valid 2 Octet Sequence
      "\xc3\xb1"                                    => array('ñ' => 'ñ'),
      // Invalid 2 Octet Sequence
      "\xc3\x28"                                    => array('�(' => '('),
      // Invalid Sequence Identifier
      "\xa0\xa1"                                    => array('��' => ''),
      // Valid 3 Octet Sequence
      "\xe2\x82\xa1"                                => array('₡' => '₡'),
      // Invalid 3 Octet Sequence (in 2nd Octet)
      "\xe2\x28\xa1"                                => array('�(�' => '('),
      // Invalid 3 Octet Sequence (in 3rd Octet)
      "\xe2\x82\x28"                                => array('�(' => '('),
      // Valid 4 Octet Sequence
      "\xf0\x90\x8c\xbc"                            => array('𐌼' => '𐌼'),
      // Invalid 4 Octet Sequence (in 2nd Octet)
      "\xf0\x28\x8c\xbc"                            => array('�(��' => '('),
      // Invalid 4 Octet Sequence (in 3rd Octet)
      "\xf0\x90\x28\xbc"                            => array('�(�' => '('),
      // Invalid 4 Octet Sequence (in 4th Octet)
      "\xf0\x28\x8c\x28"                            => array('�(�(' => '(('),
      // Valid 5 Octet Sequence (but not Unicode!)
      "\xf8\xa1\xa1\xa1\xa1"                        => array('�' => ''),
      // Valid 6 Octet Sequence (but not Unicode!)
      "\xfc\xa1\xa1\xa1\xa1\xa1"                    => array('�' => ''),
      // Valid UTF-8 string with null characters
      "\0\0\0\0中\0 -\0\0 &#20013; - %&? - \xc2\x80" => array('中 - &#20013; - %&? - ' => '中 - &#20013; - %&? - €'),
    );

    $counter = 0;
    foreach ($examples as $testString => $testResults) {
      foreach ($testResults as $before => $after) {
        self::assertEquals($after, UTF8::to_utf8(UTF8::cleanup($testString)), $counter . ' - ' . $before);
      }
      $counter++;
    }

    $testString = 'test' . UTF8::html_entity_decode('&nbsp;') . 'test';
    self::assertEquals('test' . "\xc2\xa0" . 'test', $testString);
    self::assertEquals('test&nbsp;test', UTF8::htmlentities($testString));
    self::assertEquals('test' . "\xc2\xa0" . 'test', UTF8::cleanup($testString));
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
      self::assertEquals($after, UTF8::strwidth($before));
    }
  }

  public function testToUtf8_v2()
  {
    $testArray = array(
        'Düsseldorf'                   => 'Düsseldorf',
        'Ã'                            => 'Ã',
        'foobar  || 😃'                => 'foobar  || 😃',
        ' '                            => ' ',
        ''                             => '',
        "\n"                           => "\n",
        'test'                         => 'test',
        'Here&#39;s some quoted text.' => 'Here\'s some quoted text.',
        '&#39;'                        => '\'',
        "\u0063\u0061\u0074"           => 'cat',
        "\u0039&#39;\u0039"            => '9\'9',
    );

    foreach ($testArray as $before => $after) {
      self::assertEquals($after, UTF8::to_utf8($before));
    }
  }

  public function testUtf8FixWin1252Chars()
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
    );

    foreach ($testArray as $before => $after) {
      self::assertEquals($after, UTF8::utf8_fix_win1252_chars($before));
    }
  }

  public function testUrlDecode()
  {
    $testArray = array(
        'W%F6bse' => 'Wöbse',
        'Ã' => 'Ã',
        'Ã¤' => 'ä',
        ' ' => ' ',
        '' => '',
        "\n" => "\n",
        "\u00ed" => 'í',
        'con%5cu00%366irm' => 'confirm',
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
    );

    foreach ($testArray as $before => $after) {
      self::assertEquals($after, UTF8::urldecode($before), 'testing: ' . $before);
    }
  }

  public function testJsonDecode()
  {
    $testArray = array(
        '{"recipe_id":-1,"recipe_created":"","recipe_title":"FSDFSDF","recipe_description":"","recipe_yield":0,"recipe_prepare_time":"fast","recipe_image":"","recipe_legal":0,"recipe_license":0,"recipe_category_id":[],"recipe_category_name":[],"recipe_variety_id":[],"recipe_variety_name":[],"recipe_tag_id":[],"recipe_tag_name":[],"recipe_instruction_id":[],"recipe_instruction_text":[],"recipe_ingredient_id":[],"recipe_ingredient_name":[],"recipe_ingredient_amount":[],"recipe_ingredient_unit":[],"errorArray":{"recipe_legal":"error","recipe_license":"error","recipe_description":"error","recipe_yield":"error","recipe_category_name":"error","recipe_tag_name":"error","recipe_instruction_text":"error","recipe_ingredient_amount":"error","recipe_ingredient_unit":"error"},"errorMessage":"[[Bitte f\u00fclle die rot markierten Felder korrekt aus.]]","db":{"query_count":15}}'                            => '{"recipe_id":-1,"recipe_created":"","recipe_title":"FSDFSDF","recipe_description":"","recipe_yield":0,"recipe_prepare_time":"fast","recipe_image":"","recipe_legal":0,"recipe_license":0,"recipe_category_id":[],"recipe_category_name":[],"recipe_variety_id":[],"recipe_variety_name":[],"recipe_tag_id":[],"recipe_tag_name":[],"recipe_instruction_id":[],"recipe_instruction_text":[],"recipe_ingredient_id":[],"recipe_ingredient_name":[],"recipe_ingredient_amount":[],"recipe_ingredient_unit":[],"errorArray":{"recipe_legal":"error","recipe_license":"error","recipe_description":"error","recipe_yield":"error","recipe_category_name":"error","recipe_tag_name":"error","recipe_instruction_text":"error","recipe_ingredient_amount":"error","recipe_ingredient_unit":"error"},"errorMessage":"[[Bitte f\u00fclle die rot markierten Felder korrekt aus.]]","db":{"query_count":15}}',
        '{"recipe_id":-1,"recipe_created":"","recipe_title":"FSDFSκόσμε' . "\xa0\xa1" . '-öäüDF","recipe_description":"","recipe_yield":0,"recipe_prepare_time":"fast","recipe_image":"","recipe_legal":0,"recipe_license":0,"recipe_category_id":[],"recipe_category_name":[],"recipe_variety_id":[],"recipe_variety_name":[],"recipe_tag_id":[],"recipe_tag_name":[],"recipe_instruction_id":[],"recipe_instruction_text":[],"recipe_ingredient_id":[],"recipe_ingredient_name":[],"recipe_ingredient_amount":[],"recipe_ingredient_unit":[],"errorArray":{"recipe_legal":"error","recipe_license":"error","recipe_description":"error","recipe_yield":"error","recipe_category_name":"error","recipe_tag_name":"error","recipe_instruction_text":"error","recipe_ingredient_amount":"error","recipe_ingredient_unit":"error"},"errorMessage":"[[Bitte f\u00fclle die rot markierten Felder korrekt aus.]]","db":{"query_count":15}}' => '{"recipe_id":-1,"recipe_created":"","recipe_title":"FSDFS\u03ba\u03cc\u03c3\u03bc\u03b5\u00a0\u00a1-\u00f6\u00e4\u00fcDF","recipe_description":"","recipe_yield":0,"recipe_prepare_time":"fast","recipe_image":"","recipe_legal":0,"recipe_license":0,"recipe_category_id":[],"recipe_category_name":[],"recipe_variety_id":[],"recipe_variety_name":[],"recipe_tag_id":[],"recipe_tag_name":[],"recipe_instruction_id":[],"recipe_instruction_text":[],"recipe_ingredient_id":[],"recipe_ingredient_name":[],"recipe_ingredient_amount":[],"recipe_ingredient_unit":[],"errorArray":{"recipe_legal":"error","recipe_license":"error","recipe_description":"error","recipe_yield":"error","recipe_category_name":"error","recipe_tag_name":"error","recipe_instruction_text":"error","recipe_ingredient_amount":"error","recipe_ingredient_unit":"error"},"errorMessage":"[[Bitte f\u00fclle die rot markierten Felder korrekt aus.]]","db":{"query_count":15}}',
    );

    foreach ($testArray as $before => $after) {
      self::assertEquals($after, UTF8::json_encode(UTF8::json_decode($before)));
    }
  }

  public function testToUtf8_v3()
  {
    $utf8File = file_get_contents(__DIR__ . '/test1Utf8.txt');
    $latinFile = file_get_contents(__DIR__ . '/test1Latin.txt');

    $utf8File = explode("\n", $utf8File);
    $latinFile = explode("\n", $latinFile);

    $testArray = array_combine($latinFile, $utf8File);

    foreach ($testArray as $before => $after) {
      self::assertEquals($after, UTF8::to_utf8($before));
    }
  }

  public function testChar()
  {
    $testArray = array(
        '39'  => '\'',
        '40'  => '(',
        '41'  => ')',
        '42'  => '*',
        '160' => ' ',
    );

    foreach ($testArray as $before => $after) {
      self::assertEquals($after, UTF8::chr($before));
    }
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
        array('', '', 1, '...'),
        array('', '', 0, '...'),
    );

    foreach ($testArray as $test) {
      self::assertEquals($test[0], UTF8::words_limit($test[1], $test[2], $test[3]), 'tested: ' . $test[1]);
    }
  }

  public function testStrLimit()
  {
    $testArray = array(
        array('this...', 'this is a test', 5, '...'),
        array('this is...', 'this is öäü-foo test', 8, '...'),
        array('fòô', 'fòô bàř fòô', 6, ''),
        array('fòô bàř', 'fòô bàř fòô', 8, ''),
    );

    foreach ($testArray as $test) {
      self::assertEquals($test[0], UTF8::str_limit_after_word($test[1], $test[2], $test[3]), 'tested: ' . $test[1]);
    }
  }

  public function testClean()
  {
    $examples = array(
      // Valid UTF-8
      'κόσμε'                    => array('κόσμε' => 'κόσμε'),
      '中'                        => array('中' => '中'),
      '«foobar»'                 => array('«foobar»' => '«foobar»'),
      // Valid UTF-8 + Invalied Chars
      "κόσμε\xa0\xa1-öäü"        => array('κόσμε-öäü' => 'κόσμε-öäü'),
      // Valid ASCII
      'a'                        => array('a' => 'a'),
      // Valid emoji (non-UTF-8)
      '😃' => array('😃' => '😃'),
      // Valid ASCII + Invalied Chars
      "a\xa0\xa1-öäü"            => array('a-öäü' => 'a-öäü'),
      // Valid 2 Octet Sequence
      "\xc3\xb1"                 => array('ñ' => 'ñ'),
      // Invalid 2 Octet Sequence
      "\xc3\x28"                 => array('�(' => '('),
      // Invalid Sequence Identifier
      "\xa0\xa1"                 => array('��' => ''),
      // Valid 3 Octet Sequence
      "\xe2\x82\xa1"             => array('₡' => '₡'),
      // Invalid 3 Octet Sequence (in 2nd Octet)
      "\xe2\x28\xa1"             => array('�(�' => '('),
      // Invalid 3 Octet Sequence (in 3rd Octet)
      "\xe2\x82\x28"             => array('�(' => '('),
      // Valid 4 Octet Sequence
      "\xf0\x90\x8c\xbc"         => array('𐌼' => '𐌼'),
      // Invalid 4 Octet Sequence (in 2nd Octet)
      "\xf0\x28\x8c\xbc"         => array('�(��' => '('),
      // Invalid 4 Octet Sequence (in 3rd Octet)
      "\xf0\x90\x28\xbc"         => array('�(�' => '('),
      // Invalid 4 Octet Sequence (in 4th Octet)
      "\xf0\x28\x8c\x28"         => array('�(�(' => '(('),
      // Valid 5 Octet Sequence (but not Unicode!)
      "\xf8\xa1\xa1\xa1\xa1"     => array('�' => ''),
      // Valid 6 Octet Sequence (but not Unicode!)
      "\xfc\xa1\xa1\xa1\xa1\xa1" => array('�' => ''),
    );

    $counter = 0;
    foreach ($examples as $testString => $testResults) {
      foreach ($testResults as $before => $after) {
        self::assertEquals($after, UTF8::cleanup($testString), $counter);
      }
      $counter++;
    }
  }

  public function testCleanup()
  {
    $examples = array(
        ''                                     => array(''),
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
        <p>
          &nbsp;�&foo;❤&nbsp;
        </p>
        '                              => array(
            '' => '
        <h1>«Düsseldorf» &ndash; &lt;Köln&gt;</h1>
        <br /><br />
        <p>
          &nbsp;&foo;❤&nbsp;
        </p>
        ',
        ),
    );

    foreach ($examples as $testString => $testResults) {
      foreach ($testResults as $before => $after) {
        self::assertEquals($after, UTF8::cleanup($testString));
      }
    }

  }

  public function testToASCII()
  {
    $tests = array(
        1                               => 1,
        -1                              => -1,
        ' '                             => ' ',
        ''                              => '',
        'أبز'                           => '\'bz',
        "\xe2\x80\x99"                  => '\'',
        'Ɓtest'                         => 'Btest',
        '  -ABC-中文空白-  '                => '  -ABC-Zhong Wen Kong Bai -  ',
        "      - abc- \xc2\x87"         => '      - abc- ++',
        'abc'                           => 'abc',
        'deja vu'                       => 'deja vu',
        'déjà vu'                       => 'deja vu',
        'déjà σσς iıii'                 => 'deja sss iiii',
        "test\x80-\xBFöäü"              => 'test-oau',
        'Internationalizaetion'         => 'Internationalizaetion',
        "中 - &#20013; - %&? - \xc2\x80" => 'Zhong  - &#20013; - %&? - EUR',
        'Un été brûlant sur la côte'    => 'Un ete brulant sur la cote',
        'Αυτή είναι μια δοκιμή'         => 'Aute einai mia dokime',
        'أحبك'                          => '\'Hbk',
        'キャンパス'                         => 'kiyanpasu',
        'биологическом'                 => 'biologicheskom',
        '정, 병호'                         => 'jeong, byeongho',
        'ますだ, よしひこ'                     => 'masuda, yosihiko',
        'मोनिच'                         => 'monic',
        'क्षȸ'                          => 'kssdb',
        'أحبك 😀'                       => '\'Hbk ?',
        '∀ i ∈ ℕ'                       => '[?] i [?] N',
        '👍 💩 😄 ❤ 👍 💩 😄 ❤أحبك'     => '? ? ?  ? ? ? \'Hbk',
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::to_ascii($before), $before);
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
      self::assertEquals($after, UTF8::isBase64($before), $before);
    }
  }

  public function testSwapCase()
  {
    $tests = array(
        1                               => 1,
        -1                              => -1,
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
        'déjà σσς iıii'                 => 'DÉJÀ ΣΣΣ IIII',
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
      self::assertEquals($after, UTF8::swapCase($before), $before);
    }
  }

  public function testStrTransliterate()
  {
    $tests = array(
        1                               => 1,
        -1                              => -1,
        ' '                             => ' ',
        ''                              => '',
        'أبز'                           => '\'bz',
        "\xe2\x80\x99"                  => '\'',
        'Ɓtest'                         => 'Btest',
        '  -ABC-中文空白-  '                => '  -ABC-Zhong Wen Kong Bai -  ',
        "      - abc- \xc2\x87"         => '      - abc- ++',
        'abc'                           => 'abc',
        'deja vu'                       => 'deja vu',
        'déjà vu'                       => 'deja vu',
        'déjà σσς iıii'                 => 'deja sss iiii',
        "test\x80-\xBFöäü"              => 'test-oau',
        'Internationalizaetion'         => 'Internationalizaetion',
        "中 - &#20013; - %&? - \xc2\x80" => 'Zhong  - &#20013; - %&? - EUR',
        'BonJour'                       => 'BonJour',
        'BonJour & au revoir'           => 'BonJour & au revoir',
        'Déjà'                          => 'Deja',
        'trąnslįteration tėst ųsąge ūž' => 'transliteration test usage uz',
        'това е тестово заглавие'       => 'tova e testovo zaglavie',
        'это тестовый заголовок'        => 'eto testovyi zagolovok',
        'führen Aktivitäten Haglöfs'    => 'fuhren Aktivitaten Haglofs',
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::str_transliterate($before), $before);
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
      self::assertEquals($after, UTF8::decimal_to_chr($before));
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
      self::assertEquals($after, UTF8::chr_to_decimal($before));
    }
  }

  public function testChrToBinary()
  {
    $tests = array(
        '~' => '01111110',
        '§' => '1100001010100111',
        'ሇ' => '111000011000100010000111',

    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::str_to_binary($before));
    }
  }

  public function testcleanParameter()
  {
    $dirtyTestString = "\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃";

    self::assertEquals("\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃", UTF8::clean($dirtyTestString));
    self::assertEquals("\xEF\xBB\xBF„Abcdef \x20…” — 😃", UTF8::clean($dirtyTestString, false, true, false, false));
    self::assertEquals("\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃", UTF8::clean($dirtyTestString, false, false, false, true));
    self::assertEquals("\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃", UTF8::clean($dirtyTestString, false, false, false, false));
    self::assertEquals("\xEF\xBB\xBF\"Abcdef\xc2\xa0\x20...\" - 😃", UTF8::clean($dirtyTestString, false, false, true, true));
    self::assertEquals("\xEF\xBB\xBF\"Abcdef\xc2\xa0\x20...\" - 😃", UTF8::clean($dirtyTestString, false, false, true, false));
    self::assertEquals("\xEF\xBB\xBF\"Abcdef  ...\" - 😃", UTF8::clean($dirtyTestString, false, true, true, false));
    self::assertEquals("\xEF\xBB\xBF\"Abcdef\xc2\xa0\x20...\" - 😃", UTF8::clean($dirtyTestString, false, true, true, true));
    self::assertEquals("„Abcdef\xc2\xa0\x20…” — 😃", UTF8::clean($dirtyTestString, true, false, false, false));
    self::assertEquals("„Abcdef\xc2\xa0\x20…” — 😃", UTF8::clean($dirtyTestString, true, false, false, true));
    self::assertEquals("\"Abcdef\xc2\xa0\x20...\" - 😃", UTF8::clean($dirtyTestString, true, false, true, false));
    self::assertEquals("\"Abcdef\xc2\xa0\x20...\" - 😃", UTF8::clean($dirtyTestString, true, false, true, true));
    self::assertEquals('„Abcdef  …” — 😃', UTF8::clean($dirtyTestString, true, true, false, false));
    self::assertEquals('„Abcdef  …” — 😃', UTF8::clean($dirtyTestString, true, true, false, true));
    self::assertEquals('"Abcdef  ..." - 😃', UTF8::clean($dirtyTestString, true, true, true, false));
    self::assertEquals("\"Abcdef\xc2\xa0 ...\" - 😃", UTF8::clean($dirtyTestString, true, true, true, true));
  }

  public function testWhitespace()
  {
    $whitespaces = UTF8::whitespace_table();
    foreach ($whitespaces as $whitespace) {
      self::assertEquals(' ', UTF8::clean($whitespace, false, true));
    }
  }

  public function testLtrim()
  {
    $tests = array(
        '  -ABC-中文空白-  ' => '-ABC-中文空白-  ',
        '      - ÖÄÜ- '  => '- ÖÄÜ- ',
        'öäü'            => 'öäü',
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::ltrim($before));
    }

    self::assertEquals('tërnâtiônàlizætiøn', UTF8::ltrim('ñtërnâtiônàlizætiøn', 'ñ'));
    self::assertEquals('Iñtërnâtiônàlizætiøn', UTF8::ltrim('Iñtërnâtiônàlizætiøn', 'ñ'));
    self::assertEquals('', UTF8::ltrim(''));
    self::assertEquals('', UTF8::ltrim(' '));
    self::assertEquals('Iñtërnâtiônàlizætiøn', UTF8::ltrim('/Iñtërnâtiônàlizætiøn', '/'));
    self::assertEquals('Iñtërnâtiônàlizætiøn', UTF8::ltrim('Iñtërnâtiônàlizætiøn', '^s'));
    self::assertEquals("\nñtërnâtiônàlizætiøn", UTF8::ltrim("ñ\nñtërnâtiônàlizætiøn", 'ñ'));
    self::assertEquals('tërnâtiônàlizætiøn', UTF8::ltrim("ñ\nñtërnâtiônàlizætiøn", "ñ\n"));
  }

  public function testStr_split()
  {
    self::assertEquals(
        array(
            'd',
            'é',
            'j',
            'à',
        ),
        UTF8::str_split('déjà', 1)
    );
    self::assertEquals(
        array(
            'dé',
            'jà',
        ),
        UTF8::str_split('déjà', 2)
    );
  }

  public function testRtrim()
  {
    $tests = array(
        '-ABC-中文空白-  '        => '-ABC-中文空白-',
        '- ÖÄÜ-             ' => '- ÖÄÜ-',
        'öäü'                 => 'öäü',
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::rtrim($before));
    }

    self::assertEquals('Iñtërnâtiônàlizæti', UTF8::rtrim('Iñtërnâtiônàlizætiø', 'ø'));
    self::assertEquals('Iñtërnâtiônàlizætiøn ', UTF8::rtrim('Iñtërnâtiônàlizætiøn ', 'ø'));
    self::assertEquals('', UTF8::rtrim(''));
    self::assertEquals("Iñtërnâtiônàlizætiø\n", UTF8::rtrim("Iñtërnâtiônàlizætiø\nø", 'ø'));
    self::assertEquals('Iñtërnâtiônàlizæti', UTF8::rtrim("Iñtërnâtiônàlizætiø\nø", "\nø"));
  }

  public function testStrtolower()
  {
    $tests = array(
        1               => 1,
        -1              => -1,
        'ABC-中文空白'      => 'abc-中文空白',
        'ÖÄÜ'           => 'öäü',
        'öäü'           => 'öäü',
        'κόσμε'         => 'κόσμε',
        'Κόσμε'         => 'κόσμε',
        'ㅋㅋ-Lol'        => 'ㅋㅋ-lol',
        'ㅎㄹ..-Daebak'   => 'ㅎㄹ..-daebak',
        'ㅈㅅ-Sorry'      => 'ㅈㅅ-sorry',
        'ㅡㅡ-WTF'        => 'ㅡㅡ-wtf',
        'DÉJÀ Σσς Iıİi' => 'déjà σσς iıii',
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::strtolower($before));
    }
  }

  public function testStrtoupper()
  {
    $tests = array(
        1               => 1,
        -1              => -1,
        'abc-中文空白'      => 'ABC-中文空白',
        'öäü'           => 'ÖÄÜ',
        'öäü test öäü'  => 'ÖÄÜ TEST ÖÄÜ',
        'ÖÄÜ'           => 'ÖÄÜ',
        '中文空白'          => '中文空白',
        'Déjà Σσς Iıİi' => 'DÉJÀ ΣΣΣ IIİI',
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::strtoupper($before));
    }
  }

  public function testMin()
  {
    $tests = array(
        'abc-中文空白'     => '-',
        'öäü'          => 'ä',
        'öäü test öäü' => ' ',
        'ÖÄÜ'          => 'Ä',
        '中文空白'         => '中',
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::min($before));
    }
  }

  public function testMax()
  {
    $tests = array(
        'abc-中文空白'     => '空',
        'öäü'          => 'ü',
        'öäü test öäü' => 'ü',
        'ÖÄÜ'          => 'Ü',
        '中文空白'         => '空',
    );

    foreach ($tests as $before => $after) {
      self::assertEquals($after, UTF8::max($before));
    }
  }

  public function testUcfirst()
  {
    self::assertEquals('Öäü', UTF8::ucfirst('Öäü'));
    self::assertEquals('Öäü', UTF8::ucfirst('öäü'));
    self::assertEquals('Κόσμε', UTF8::ucfirst('κόσμε'));
    self::assertEquals('ABC-ÖÄÜ-中文空白', UTF8::ucfirst('aBC-ÖÄÜ-中文空白'));
    self::assertEquals('Iñtërnâtiônàlizætiøn', UTF8::ucfirst('iñtërnâtiônàlizætiøn'));
    self::assertEquals('Ñtërnâtiônàlizætiøn', UTF8::ucfirst('ñtërnâtiônàlizætiøn'));
    self::assertEquals(' iñtërnâtiônàlizætiøn', UTF8::ucfirst(' iñtërnâtiônàlizætiøn'));
    self::assertEquals('Ñtërnâtiônàlizætiøn', UTF8::ucfirst('Ñtërnâtiônàlizætiøn'));
    self::assertEquals('ÑtërnâtiônàlizætIøN', UTF8::ucfirst('ñtërnâtiônàlizætIøN'));
    self::assertEquals('ÑtërnâtiônàlizætIøN test câse', UTF8::ucfirst('ñtërnâtiônàlizætIøN test câse'));
    self::assertEquals('', UTF8::ucfirst(''));
    self::assertEquals('Ñ', UTF8::ucfirst('ñ'));
    self::assertEquals("Ñtërn\nâtiônàlizætiøn", UTF8::ucfirst("ñtërn\nâtiônàlizætiøn"));
    self::assertSame('Deja', UTF8::ucfirst('deja'));
    self::assertSame('Σσς', UTF8::ucfirst('σσς'));
    self::assertSame('DEJa', UTF8::ucfirst('dEJa'));
    self::assertSame('ΣσΣ', UTF8::ucfirst('σσΣ'));
  }

  public function testUcWords()
  {
    self::assertEquals('Iñt Ërn ÂTi Ônà Liz Æti Øn', UTF8::ucwords('iñt ërn âTi ônà liz æti øn'));
    self::assertEquals("Iñt Ërn Âti\n Ônà Liz Æti  Øn", UTF8::ucwords("iñt ërn âti\n ônà liz æti  øn"));
    self::assertEquals('', UTF8::ucwords(''));
    self::assertEquals('Ñ', UTF8::ucwords('ñ'));
    self::assertEquals("Iñt ËrN Âti\n Ônà Liz Æti Øn", UTF8::ucwords("iñt ërN âti\n ônà liz æti øn"));
    self::assertEquals('ÑtërnâtiônàlizætIøN', UTF8::ucwords('ñtërnâtiônàlizætIøN'));
    self::assertEquals('ÑtërnâtiônàlizætIøN Test câse', UTF8::ucwords('ñtërnâtiônàlizætIøN test câse', array('câse')));
    self::assertSame('Deja Σσς DEJa ΣσΣ', UTF8::ucwords('deja σσς dEJa σσΣ'));
  }

  public function testLcfirst()
  {
    self::assertEquals('öäü', UTF8::lcfirst('Öäü'));
    self::assertEquals('κόσμε', UTF8::lcfirst('Κόσμε'));
    self::assertEquals('aBC-ÖÄÜ-中文空白', UTF8::lcfirst('ABC-ÖÄÜ-中文空白'));
    self::assertEquals('ñTËRNÂTIÔNÀLIZÆTIØN', UTF8::lcfirst('ÑTËRNÂTIÔNÀLIZÆTIØN'));
    self::assertEquals('ñTËRNÂTIÔNÀLIZÆTIØN', UTF8::lcfirst('ñTËRNÂTIÔNÀLIZÆTIØN'));
    self::assertEquals('', UTF8::lcfirst(''));
    self::assertEquals(' ', UTF8::lcfirst(' '));
    self::assertEquals("\t test", UTF8::lcfirst("\t test"));
    self::assertEquals('ñ', UTF8::lcfirst('Ñ'));
    self::assertEquals("ñTËRN\nâtiônàlizætiøn", UTF8::lcfirst("ÑTËRN\nâtiônàlizætiøn"));
    self::assertSame('deja', UTF8::lcfirst('Deja'));
    self::assertSame('σσς', UTF8::lcfirst('Σσς'));
    self::assertSame('dEJa', UTF8::lcfirst('dEJa'));
    self::assertSame('σσΣ', UTF8::lcfirst('σσΣ'));
  }

  public function testStrirpos()
  {
    self::assertEquals(3, UTF8::strripos('DÉJÀ', 'à'));
    self::assertEquals(false, UTF8::strripos('aςσb', 'ΣΣ'));
    self::assertEquals(6, UTF8::strripos('κόσμε-κόσμε', 'Κ'));
    self::assertEquals(11, UTF8::strripos('test κόσμε κόσμε test', 'Κ'));
    self::assertEquals(7, UTF8::strripos('中文空白-ÖÄÜ-中文空白', 'ü'));
  }

  public function testStrrpos()
  {
    self::assertSame(1, UTF8::strrpos('11--', '1-', 0, 'UTF-8'));
    self::assertSame(2, UTF8::strrpos('-11--', '1-', 0, 'UTF-8'));
    self::assertSame(false, UTF8::strrpos('한국어', '', 'UTF-8'));
    self::assertSame(1, UTF8::strrpos('한국어', '국', 'UTF-8'));
    self::assertEquals(false, UTF8::strrpos('한국어', ''));
    self::assertEquals(1, UTF8::strrpos('한국어', '국'));
    self::assertEquals(6, UTF8::strrpos('κόσμε-κόσμε', 'κ'));
    self::assertEquals(13, UTF8::strrpos('test κόσμε κόσμε test', 'σ'));
    self::assertEquals(9, UTF8::strrpos('中文空白-ÖÄÜ-中文空白', '中'));
  }

  public function testStrpos()
  {
    for ($i = 0; $i <= 5; $i++) {
      self::assertEquals(false, UTF8::strpos('abc', ''));
      self::assertEquals(false, UTF8::strpos('abc', 'd'));
      self::assertEquals(false, UTF8::strpos('abc', 'a', 3));
      self::assertEquals(16, UTF8::strpos('der Straße nach Paris', 'Paris'));
      self::assertEquals(0, UTF8::strpos('abc', 'a', 1));
      self::assertEquals(1, UTF8::strpos('abc', 'b', 1));
      self::assertEquals(1, UTF8::strpos('abc', 'b', 0));
      //self::assertEquals(1, UTF8::strpos('abc', 'c', -1));
      self::assertEquals(1, UTF8::strpos('한국어', '국'));
      self::assertEquals(0, UTF8::strpos('κόσμε-κόσμε-κόσμε', 'κ'));
      self::assertEquals(7, UTF8::strpos('test κόσμε test κόσμε', 'σ'));
      self::assertEquals(8, UTF8::strpos('ABC-ÖÄÜ-中文空白-中文空白', '中'));
    }
  }

  public function testStripos()
  {
    for ($i = 0; $i <= 5; $i++) {
      self::assertEquals(3, UTF8::stripos('DÉJÀ', 'à'));
      self::assertEquals(1, UTF8::stripos('aςσb', 'ΣΣ'));
      self::assertEquals(16, UTF8::stripos('der Straße nach Paris', 'Paris'));
      self::assertEquals(4, UTF8::stripos('öäü-κόσμε-κόσμε-κόσμε', 'Κ'));
      self::assertEquals(5, UTF8::stripos('Test κόσμε test κόσμε', 'Κ'));
      self::assertEquals(4, UTF8::stripos('ABC-ÖÄÜ-中文空白-中文空白', 'ö'));
    }
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

    foreach ($testArray as $actual => $expected) {
      self::assertEquals($expected, UTF8::ord($actual));
    }
  }

  public function testHtmlEncode()
  {
    $testArray = array(
        '{-test' => '&#123;&#45;&#116;&#101;&#115;&#116;',
        '中文空白'   => '&#20013;&#25991;&#31354;&#30333;',
        'κόσμε'  => '&#954;&#8057;&#963;&#956;&#949;',
        'öäü'    => '&#246;&#228;&#252;',
        ' '      => '&#32;',
        ''       => '',
    );

    foreach ($testArray as $actual => $expected) {
      self::assertEquals($expected, UTF8::html_encode($actual));
    }
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
      self::assertEquals($expected, UTF8::single_chr_html_encode($actual));
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
      self::assertEquals($expected, UTF8::chr_size_list($actual));
    }
  }

  public function testStrnatcasecmp()
  {
    self::assertEquals(0, UTF8::strnatcasecmp('Hello world 中文空白!', 'Hello WORLD 中文空白!'));
    self::assertEquals(1, UTF8::strnatcasecmp('Hello world 中文空白!', 'Hello WORLD 中文空白'));
    self::assertEquals(-1, UTF8::strnatcasecmp('Hello world 中文空白', 'Hello WORLD 中文空白!'));
    self::assertEquals(-1, UTF8::strnatcasecmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!'));
    self::assertEquals(1, UTF8::strnatcasecmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'));
    self::assertEquals(0, UTF8::strnatcasecmp('10Hello world 中文空白!', '10Hello world 中文空白!'));
    self::assertEquals(0, UTF8::strnatcasecmp('Hello world 中文空白!', 'Hello WORLD 中文空白!'));
  }

  public function testStrnatcmp()
  {
    self::assertEquals(1, UTF8::strnatcmp('Hello world 中文空白!', 'Hello WORLD 中文空白!'));
    self::assertEquals(1, UTF8::strnatcmp('Hello world 中文空白!', 'Hello WORLD 中文空白'));
    self::assertEquals(1, UTF8::strnatcmp('Hello world 中文空白', 'Hello WORLD 中文空白!'));
    self::assertEquals(-1, UTF8::strnatcmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!'));
    self::assertEquals(1, UTF8::strnatcmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'));
    self::assertEquals(0, UTF8::strnatcmp('10Hello world 中文空白!', '10Hello world 中文空白!'));
    self::assertEquals(1, UTF8::strnatcmp('Hello world 中文空白!', 'Hello WORLD 中文空白!'));
  }

  public function testStrtocasefold()
  {
    self::assertSame('ǰ◌̱', UTF8::strtocasefold('ǰ◌̱'));    // Original (NFC)
    self::assertSame('j◌̌◌', UTF8::strtocasefold('J◌̌◌'));   // Uppercased
    self::assertSame('j◌̱◌̌', UTF8::strtocasefold('J◌̱◌̌')); // Uppercased NFC

    // valid utf-8
    self::assertEquals('hello world 中文空白', UTF8::strtocasefold('Hello world 中文空白'));

    // invalid utf-8
    self::assertEquals('iñtërnâtiônàlizætiøn', UTF8::strtocasefold("Iñtërnâtiôn\xE9àlizætiøn"));
  }

  public function testStrtonatfold()
  {
    $utf8 = new UTF8();

    // valid utf-8
    $string = $this->invokeMethod($utf8, 'strtonatfold', array('Hello world 中文空白'));
    self::assertEquals('Hello world 中文空白', $string);

    // invalid utf-8
    $string = $this->invokeMethod($utf8, 'strtonatfold', array("Iñtërnâtiôn\xE9àlizætiøn"));
    self::assertEquals('', $string);
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

  public function testWordCount()
  {
    $testArray = array(
        '中文空白'        => 1,
        'öäü öäü öäü' => 3,
        'abc'         => 1,
        ''            => 0,
        ' '           => 0,
    );

    foreach ($testArray as $actual => $expected) {
      self::assertEquals($expected, UTF8::str_word_count($actual));
    }
  }

  public function testMaxChrWidth()
  {
    $testArray = array(
        '中文空白' => 3,
        'öäü'  => 2,
        'abc'  => 1,
        ''     => 0,
    );

    foreach ($testArray as $actual => $expected) {
      self::assertEquals($expected, UTF8::max_chr_width($actual));
    }
  }

  public function testSplit()
  {
    self::assertEquals(
        array(
            '中',
            '文',
            '空',
            '白',
        ),
        UTF8::split('中文空白')
    );
    self::assertEquals(
        array(
            '中文',
            '空白',
        ),
        UTF8::split('中文空白', 2)
    );
    self::assertEquals(array('中文空白'), UTF8::split('中文空白', 4));
    self::assertEquals(array('中文空白'), UTF8::split('中文空白', 8));

    self::assertEquals(array(1234), UTF8::split(1234, 8));
  }

  public function testChunkSplit()
  {
    $result = UTF8::chunk_split('ABC-ÖÄÜ-中文空白-κόσμε', 3);
    $expected = "ABC\r\n-ÖÄ\r\nÜ-中\r\n文空白\r\n-κό\r\nσμε";

    self::assertEquals($expected, $result);
  }

  public function setUp()
  {
    error_reporting(E_STRICT);
  }
}
