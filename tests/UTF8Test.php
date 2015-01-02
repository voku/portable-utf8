<?php

use voku\helper\UTF8;

class UTF8Test extends PHPUnit_Framework_TestCase
{

  public function testStrlen()
  {
    $string = 'string <strong>with utf-8 chars åèä</strong> - doo-bee doo-bee dooh';

    $this->assertEquals(70, strlen($string));
    $this->assertEquals(67, UTF8::strlen($string));

    $string_test1 = strip_tags($string);
    $string_test2 = UTF8::strip_tags($string);

    $this->assertEquals(53, strlen($string_test1));
    $this->assertEquals(50, UTF8::strlen($string_test2));
  }

  public function testHtmlspecialchars()
  {
    $testArray = array(
        "<a href='κόσμε'>κόσμε</a>" => "&lt;a href='κόσμε'&gt;κόσμε&lt;/a&gt;",
        "<白>"                       => "&lt;白&gt;",
        "öäü"                       => "öäü",
        " "                         => " ",
        ""                          => ""
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::htmlspecialchars($actual));
    }
  }

  public function testHtmlentities()
  {
    $testArray = array(
        "<白>" => "&lt;白&gt;",
        "öäü" => "&ouml;&auml;&uuml;",
        " "   => " ",
        ""    => ""
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::htmlentities($actual));
    }
  }

  public function testFitsInside()
  {
    $testArray = array(
        'κόσμε'  => array(5 => true),
        'test'   => array(4 => true),
        ''       => array(0 => true),
        ' '      => array(0 => false),
        'abcöäü' => array(2 => false)
    );

    foreach ($testArray as $actual => $data) {
      foreach ($data as $size => $expected) {
        $this->assertEquals($expected, UTF8::fits_inside($actual, $size), 'error by ' . $actual);
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
        "FÃÂ©dération Camerounaise de Football"           => "Fédération Camerounaise de Football",
        "FÃÂ©dération Camerounaise de Football\n"         => "Fédération Camerounaise de Football\n",
        "FÃ©dÃ©ration Camerounaise de Football"           => "Fédération Camerounaise de Football",
        "FÃ©dÃ©ration Camerounaise de Football\n"         => "Fédération Camerounaise de Football\n",
        "FÃÂ©dÃÂ©ration Camerounaise de Football"         => "Fédération Camerounaise de Football",
        "FÃÂ©dÃÂ©ration Camerounaise de Football\n"       => "Fédération Camerounaise de Football\n",
        "FÃÂÂÂÂ©dÃÂÂÂÂ©ration Camerounaise de Football"   => "Fédération Camerounaise de Football",
        "FÃÂÂÂÂ©dÃÂÂÂÂ©ration Camerounaise de Football\n" => "Fédération Camerounaise de Football\n",
    );

    foreach ($testArray as $before => $after) {
      $this->assertEquals($after, UTF8::fix_utf8($before));
    }
  }

  public function testIsUtf8()
  {
    $testArray = array(
        'κ'                                                                => true,
        ''                                                                 => true,
        ' '                                                                => true,
        "\n"                                                               => true,
        'abc'                                                              => true,
        'abcöäü'                                                           => true,
        '白'                                                                => true,
        "សាកល្បង!"                                                         => true,
        "דיעס איז אַ פּרובירן!"                                            => true,
        "Штампи іст Ейн тест!"                                             => true,
        "Штампы гіст Эйн тэст!"                                            => true,
        "測試！"                                                              => true,
        "ການທົດສອບ!"                                                       => true,
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
        "Iñtërnâtiônàlizætiøn\xFC\xA1\xA1\xA1\xA1\xA1Iñtërnâtiônàlizætiøn" => false
    );

    $conter = 0;
    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::is_utf8($actual), 'error by - ' . $conter . ' :' . $actual);
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
            'κ' => 3
        ),
        'cba'    => array(
            'a' => 1,
            'b' => 1,
            'c' => 1
        ),
        'abcöäü' => array(
            'a' => 1,
            'b' => 1,
            'c' => 1,
            'ä' => 1,
            'ö' => 1,
            'ü' => 1
        ),
        '白白'     => array('白' => 2),
        ''       => array()
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::count_chars($actual), 'error by ' . $actual);
    }
  }

  public function testStringHasBom()
  {
    $testArray = array(
        UTF8::bom() . 'κ'      => true,
        'abc'                  => false,
        UTF8::bom() . 'abcöäü' => true,
        '白'                    => false,
        UTF8::bom()            => true
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::string_has_bom($actual), 'error by ' . $actual);
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
        ' '      => ' '
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::strrev($actual), 'error by ' . $actual);
    }
  }

  public function testIsAscii()
  {
    $testArray = array(
        'κ'      => false,
        'abc'    => true,
        'abcöäü' => false,
        '白'      => false,
        ''       => true
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::is_ascii($actual), 'error by ' . $actual);
    }
  }

  public function testRemoveDuplicates()
  {
    $testArray = array(
        "öäü-κόσμεκόσμε-äöü"   => array(
            "öäü-κόσμε-äöü" => "κόσμε"
        ),
        "äöüäöüäöü-κόσμεκόσμε" => array(
            "äöü-κόσμε" => array(
                "äöü",
                "κόσμε"
            )
        )
    );

    foreach ($testArray as $actual => $data) {
      foreach ($data as $expected => $filter) {
        $this->assertEquals($expected, UTF8::remove_duplicates($actual, $filter));
      }
    }
  }

  public function testRange()
  {
    $expected = array(
        "κ",
        "ι",
        "θ",
        "η",
        "ζ"
    );

    $this->assertEquals($expected, UTF8::range("κ", "ζ"));
    $this->assertEquals(0, count(UTF8::range("κ", "")));

  }

  public function testHash()
  {
    $testArray = array(
        2,
        8,
        0,
        100,
        1234
    );

    foreach ($testArray as $testValue) {
      $this->assertEquals($testValue, UTF8::strlen(UTF8::hash($testValue)));
    }
  }

  public function testCallback()
  {
    $actual = UTF8::callback(
        array(
            'voku\helper\UTF8',
            'strtolower'
        ), "Κόσμε-ÖÄÜ"
    );
    $expected = array(
        "κ",
        "ό",
        "σ",
        "μ",
        "ε",
        "-",
        "ö",
        "ä",
        "ü"
    );
    $this->assertEquals($expected, $actual);
  }

  public function testAccess()
  {
    $testArray = array(
        ""          => array(1 => ""),
        "中文空白"      => array(2 => "空"),
        "中文空白-test" => array(3 => "白"),
    );

    foreach ($testArray as $actualString => $testDataArray) {
      foreach ($testDataArray as $stringPos => $expectedString) {
        $this->assertEquals($expectedString, UTF8::access($actualString, $stringPos));
      }
    }
  }

  public function testStrSort()
  {
    $tests = array(
        ""               => "",
        "  -ABC-中文空白-  " => "    ---ABC中文白空",
        "      - ÖÄÜ- "  => "        --ÄÖÜ",
        "öäü"            => "äöü"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::str_sort($before));
    }

    $tests = array(
        "  -ABC-中文空白-  " => "空白文中CBA---    ",
        "      - ÖÄÜ- "  => "ÜÖÄ--        ",
        "öäü"            => "üöä"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::str_sort($before, false, true));
    }

    $tests = array(
        "    "           => " ",
        "  -ABC-中文空白-  " => " -ABC中文白空",
        "      - ÖÄÜ- "  => " -ÄÖÜ",
        "öäü"            => "äöü"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::str_sort($before, true));
    }

    $tests = array(
        "  -ABC-中文空白-  " => "空白文中CBA- ",
        "      - ÖÄÜ- "  => "ÜÖÄ- ",
        "öäü"            => "üöä"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::str_sort($before, true, true));
    }
  }

  public function testUrlSlug()
  {
    $tests = array(
        "  -ABC-中文空白-  " => "abc",
        "      - ÖÄÜ- "  => "oau",
        "öäü"            => "oau"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::url_slug($before));
    }

    $tests = array(
        "  -ABC-中文空白-  " => "abc",
        "      - ÖÄÜ- "  => "oau",
        "  öäüabc"       => "oaua"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::url_slug($before, 4));
    }

    $tests = array(
        "Facebook bekämpft erstmals Durchsuchungsbefehle" => "facebook-bekaempft-erstmals-durchsuchungsbefehle",
        "  -ABC-中文空白-  "                                  => "abc",
        "      - ÖÄÜ- "                                   => "oeaeue",
        "öäü"                                             => "oeaeue"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::url_slug($before, -1, 'de'));
    }
  }

  public function testString()
  {
    $this->assertEquals("", UTF8::string(array()));
    $this->assertEquals(
        "öäü", UTF8::string(
        array(
            246,
            228,
            252
        )
    )
    );
    $this->assertEquals(
        "ㅡㅡ", UTF8::string(
        array(
            12641,
            12641
        )
    )
    );
  }

  public function testStripTags()
  {
    $this->assertEquals("", UTF8::strip_tags(""));
    $this->assertEquals("中文空白 ", UTF8::strip_tags("<nav>中文空白 </nav>"));
    $this->assertEquals("wtf", UTF8::strip_tags("<ㅡㅡ></ㅡㅡ><div></div><input type='email' name='user[email]' /><a>wtf</a>"));
  }

  public function testStrPad()
  {
    $firstString = "Though wise men at their end know dark is right,\nBecause their words had forked no lightning they\n";
    $secondString = "Do not go gentle into that good night.";
    $expectedString = $firstString . $secondString;
    $actualString = UTF8::str_pad($firstString, UTF8::strlen($firstString) + UTF8::strlen($secondString), $secondString);

    $this->assertEquals($expectedString, $actualString);

    $this->assertEquals("中文空白______", UTF8::str_pad("中文空白", 10, "_", STR_PAD_RIGHT));
    $this->assertEquals("______中文空白", UTF8::str_pad("中文空白", 10, "_", STR_PAD_LEFT));
    $this->assertEquals("___中文空白___", UTF8::str_pad("中文空白", 10, "_", STR_PAD_BOTH));

    $toPad = '<IñtërnëT>'; // 10 characters
    $padding = 'ø__'; // 4 characters

    $this->assertEquals($toPad . '          ', UTF8::str_pad($toPad, 20));
    $this->assertEquals('          ' . $toPad, UTF8::str_pad($toPad, 20, ' ', STR_PAD_LEFT));
    $this->assertEquals('     ' . $toPad . '     ', UTF8::str_pad($toPad, 20, ' ', STR_PAD_BOTH));

    $this->assertEquals($toPad, UTF8::str_pad($toPad, 10));
    $this->assertEquals('5char', str_pad('5char', 4)); // str_pos won't truncate input string
    $this->assertEquals($toPad, UTF8::str_pad($toPad, 8));

    $this->assertEquals($toPad . 'ø__ø__ø__ø', UTF8::str_pad($toPad, 20, $padding, STR_PAD_RIGHT));
    $this->assertEquals('ø__ø__ø__ø' . $toPad, UTF8::str_pad($toPad, 20, $padding, STR_PAD_LEFT));
    $this->assertEquals('ø__ø_' . $toPad . 'ø__ø_', UTF8::str_pad($toPad, 20, $padding, STR_PAD_BOTH));
  }

  /**
   * @dataProvider trimProvider
   *
   * @param $input
   * @param $output
   */
  public function testTrim($input, $output)
  {
    $this->assertEquals($output, UTF8::trim($input));
  }

  /**
   * @return array
   */
  public function trimProvider()
  {
    return array(
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

  public function testClean()
  {
    $examples = array(
      // Valid UTF-8
      "κόσμε"                    => array("κόσμε" => "κόσμε"),
      "中"                        => array("中" => "中"),
      // Valid UTF-8 + Invalied Chars
      "κόσμε\xa0\xa1-öäü"        => array("κόσμε-öäü" => "κόσμε-öäü"),
      // Valid ASCII
      "a"                        => array("a" => "a"),
      // Valid ASCII + Invalied Chars
      "a\xa0\xa1-öäü"            => array("a-öäü" => "a-öäü"),
      // Valid 2 Octet Sequence
      "\xc3\xb1"                 => array("ñ" => "ñ"),
      // Invalid 2 Octet Sequence
      "\xc3\x28"                 => array("�(" => "("),
      // Invalid Sequence Identifier
      "\xa0\xa1"                 => array("��" => ""),
      // Valid 3 Octet Sequence
      "\xe2\x82\xa1"             => array("₡" => "₡"),
      // Invalid 3 Octet Sequence (in 2nd Octet)
      "\xe2\x28\xa1"             => array("�(�" => "("),
      // Invalid 3 Octet Sequence (in 3rd Octet)
      "\xe2\x82\x28"             => array("�(" => "("),
      // Valid 4 Octet Sequence
      "\xf0\x90\x8c\xbc"         => array("𐌼" => ""),
      // Invalid 4 Octet Sequence (in 2nd Octet)
      "\xf0\x28\x8c\xbc"         => array("�(��" => "("),
      // Invalid 4 Octet Sequence (in 3rd Octet)
      "\xf0\x90\x28\xbc"         => array("�(�" => "("),
      // Invalid 4 Octet Sequence (in 4th Octet)
      "\xf0\x28\x8c\x28"         => array("�(�(" => "(("),
      // Valid 5 Octet Sequence (but not Unicode!)
      "\xf8\xa1\xa1\xa1\xa1"     => array("�" => ""),
      // Valid 6 Octet Sequence (but not Unicode!)
      "\xfc\xa1\xa1\xa1\xa1\xa1" => array("�" => ""),
    );

    $counter = 0;
    foreach ($examples as $testString => $testResults) {
      foreach ($testResults as $before => $after) {
        $this->assertEquals($after, UTF8::cleanup($testString), $counter);
      }
      $counter++;
    }
  }

  public function testCleanup()
  {
    $examples = array(
      // Valid UTF-8 + UTF-8 NO-BREAK SPACE
      "κόσμε\xc2\xa0"                        => array("κόσμε" => "κόσμε "),
      // Valid UTF-8
      "中"                                    => array("中" => "中"),
      // Valid UTF-8 + ISO-Erros
      "DÃ¼sseldorf"                          => array("Düsseldorf" => "Düsseldorf"),
      // Valid UTF-8 + Invalied Chars
      "κόσμε\xa0\xa1-öäü"                    => array("κόσμε-öäü" => "κόσμε-öäü"),
      // Valid ASCII
      "a"                                    => array("a" => "a"),
      // Valid ASCII + Invalied Chars
      "a\xa0\xa1-öäü"                        => array("a-öäü" => "a-öäü"),
      // Valid 2 Octet Sequence
      "\xc3\xb1"                             => array("ñ" => "ñ"),
      // Invalid 2 Octet Sequence
      "\xc3\x28"                             => array("�(" => "("),
      // Invalid Sequence Identifier
      "\xa0\xa1"                             => array("��" => ""),
      // Valid 3 Octet Sequence
      "\xe2\x82\xa1"                         => array("₡" => "₡"),
      // Invalid 3 Octet Sequence (in 2nd Octet)
      "\xe2\x28\xa1"                         => array("�(�" => "("),
      // Invalid 3 Octet Sequence (in 3rd Octet)
      "\xe2\x82\x28"                         => array("�(" => "("),
      // Valid 4 Octet Sequence
      "\xf0\x90\x8c\xbc"                     => array("𐌼" => ""),
      // Invalid 4 Octet Sequence (in 2nd Octet)
      "\xf0\x28\x8c\xbc"                     => array("�(��" => "("),
      // Invalid 4 Octet Sequence (in 3rd Octet)
      "\xf0\x90\x28\xbc"                     => array("�(�" => "("),
      // Invalid 4 Octet Sequence (in 4th Octet)
      " \xf0\x28\x8c\x28"                    => array("�(�(" => " (("),
      // Valid 5 Octet Sequence (but not Unicode!)
      "\xf8\xa1\xa1\xa1\xa1"                 => array("�" => ""),
      // Valid 6 Octet Sequence (but not Unicode!) + UTF-8 EN SPACE
      "\xfc\xa1\xa1\xa1\xa1\xa1\xe2\x80\x82" => array("�" => " "),
    );

    foreach ($examples as $testString => $testResults) {
      foreach ($testResults as $before => $after) {
        $this->assertEquals($after, UTF8::cleanup($testString));
      }
    }

  }

  public function testWhitespace()
  {
    $whitespaces = UTF8::whitespace_table();
    foreach ($whitespaces as $whitespace) {
      $this->assertEquals(" ", UTF8::clean($whitespace, false, true));
    }
  }

  public function testLtrim()
  {
    $tests = array(
        "  -ABC-中文空白-  " => "-ABC-中文空白-  ",
        "      - ÖÄÜ- "  => "- ÖÄÜ- ",
        "öäü"            => "öäü"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::ltrim($before));
    }

    $this->assertEquals("tërnâtiônàlizætiøn", UTF8::ltrim("ñtërnâtiônàlizætiøn", "ñ"));
    $this->assertEquals("Iñtërnâtiônàlizætiøn", UTF8::ltrim("Iñtërnâtiônàlizætiøn", "ñ"));
    $this->assertEquals("", UTF8::ltrim(""));
    $this->assertEquals("", UTF8::ltrim(" "));
    $this->assertEquals("Iñtërnâtiônàlizætiøn", UTF8::ltrim("/Iñtërnâtiônàlizætiøn", "/"));
    $this->assertEquals("Iñtërnâtiônàlizætiøn", UTF8::ltrim("Iñtërnâtiônàlizætiøn", "^s"));
    $this->assertEquals("\nñtërnâtiônàlizætiøn", UTF8::ltrim("ñ\nñtërnâtiônàlizætiøn", "ñ"));
    $this->assertEquals("tërnâtiônàlizætiøn", UTF8::ltrim("ñ\nñtërnâtiônàlizætiøn", "ñ\n"));
  }

  function testStr_split()
  {
    $this->assertEquals(
        array(
            'd',
            'é',
            'j',
            'à'
        ), UTF8::str_split('déjà', 1)
    );
    $this->assertEquals(
        array(
            'dé',
            'jà'
        ), UTF8::str_split('déjà', 2)
    );
  }

  public function testRtrim()
  {
    $tests = array(
        "-ABC-中文空白-  "        => "-ABC-中文空白-",
        "- ÖÄÜ-             " => "- ÖÄÜ-",
        "öäü"                 => "öäü"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::rtrim($before));
    }

    $this->assertEquals("Iñtërnâtiônàlizæti", UTF8::rtrim("Iñtërnâtiônàlizætiø", "ø"));
    $this->assertEquals("Iñtërnâtiônàlizætiøn ", UTF8::rtrim("Iñtërnâtiônàlizætiøn ", "ø"));
    $this->assertEquals("", UTF8::rtrim(""));
    $this->assertEquals("Iñtërnâtiônàlizætiø\n", UTF8::rtrim("Iñtërnâtiônàlizætiø\nø", "ø"));
    $this->assertEquals("Iñtërnâtiônàlizæti", UTF8::rtrim("Iñtërnâtiônàlizætiø\nø", "\nø"));
  }

  public function testStrtolower()
  {
    $tests = array(
        "ABC-中文空白"    => "abc-中文空白",
        "ÖÄÜ"         => "öäü",
        "öäü"         => "öäü",
        "κόσμε"       => "κόσμε",
        "Κόσμε"       => "κόσμε",
        "ㅋㅋ-Lol"      => "ㅋㅋ-lol",
        "ㅎㄹ..-Daebak" => "ㅎㄹ..-daebak",
        "ㅈㅅ-Sorry"    => "ㅈㅅ-sorry",
        "ㅡㅡ-WTF"      => "ㅡㅡ-wtf"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::strtolower($before));
    }
  }

  public function testStrtoupper()
  {
    $tests = array(
        "abc-中文空白"     => "ABC-中文空白",
        "öäü"          => "ÖÄÜ",
        "öäü test öäü" => "ÖÄÜ TEST ÖÄÜ",
        "ÖÄÜ"          => "ÖÄÜ",
        "中文空白"         => "中文空白"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::strtoupper($before));
    }
  }

  public function testMin()
  {
    $tests = array(
        "abc-中文空白"     => "-",
        "öäü"          => "ä",
        "öäü test öäü" => " ",
        "ÖÄÜ"          => 'Ä',
        "中文空白"         => "中"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::min($before));
    }
  }

  public function testMax()
  {
    $tests = array(
        "abc-中文空白"     => "空",
        "öäü"          => "ü",
        "öäü test öäü" => "ü",
        "ÖÄÜ"          => 'Ü',
        "中文空白"         => "空"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::max($before));
    }
  }

  public function testUcfirst()
  {
    $this->assertEquals("Öäü", UTF8::ucfirst("Öäü"));
    $this->assertEquals("Öäü", UTF8::ucfirst("öäü"));
    $this->assertEquals("Κόσμε", UTF8::ucfirst("κόσμε"));
    $this->assertEquals("ABC-ÖÄÜ-中文空白", UTF8::ucfirst("aBC-ÖÄÜ-中文空白"));
    $this->assertEquals("Iñtërnâtiônàlizætiøn", UTF8::ucfirst("iñtërnâtiônàlizætiøn"));
    $this->assertEquals("Ñtërnâtiônàlizætiøn", UTF8::ucfirst("ñtërnâtiônàlizætiøn"));
    $this->assertEquals(" iñtërnâtiônàlizætiøn", UTF8::ucfirst(" iñtërnâtiônàlizætiøn"));
    $this->assertEquals("Ñtërnâtiônàlizætiøn", UTF8::ucfirst("Ñtërnâtiônàlizætiøn"));
    $this->assertEquals("", UTF8::ucfirst(""));
    $this->assertEquals("Ñ", UTF8::ucfirst("ñ"));
    $this->assertEquals("Ñtërn\nâtiônàlizætiøn", UTF8::ucfirst("ñtërn\nâtiônàlizætiøn"));
  }

  public function testUcWords()
  {
    $this->assertEquals("Iñt Ërn Âti Ônà Liz Æti Øn", UTF8::ucwords("iñt ërn âti ônà liz æti øn"));
    $this->assertEquals("Iñt Ërn Âti\n Ônà Liz Æti  Øn", UTF8::ucwords("iñt ërn âti\n ônà liz æti  øn"));
    $this->assertEquals("", UTF8::ucwords(""));
    $this->assertEquals("Ñ", UTF8::ucwords("ñ"));
    $this->assertEquals("Iñt Ërn Âti\n Ônà Liz Æti Øn", UTF8::ucwords("iñt ërn âti\n ônà liz æti øn"));
  }

  public function testLcfirst()
  {
    $this->assertEquals("öäü", UTF8::lcfirst("Öäü"));
    $this->assertEquals("κόσμε", UTF8::lcfirst("Κόσμε"));
    $this->assertEquals("aBC-ÖÄÜ-中文空白", UTF8::lcfirst("ABC-ÖÄÜ-中文空白"));
    $this->assertEquals("ñTËRNÂTIÔNÀLIZÆTIØN", UTF8::lcfirst("ÑTËRNÂTIÔNÀLIZÆTIØN"));
    $this->assertEquals("ñTËRNÂTIÔNÀLIZÆTIØN", UTF8::lcfirst("ñTËRNÂTIÔNÀLIZÆTIØN"));
    $this->assertEquals("", UTF8::lcfirst(""));
    $this->assertEquals(" ", UTF8::lcfirst(" "));
    $this->assertEquals("\t test", UTF8::lcfirst("\t test"));
    $this->assertEquals("ñ", UTF8::lcfirst("Ñ"));
    $this->assertEquals("ñTËRN\nâtiônàlizætiøn", UTF8::lcfirst("ÑTËRN\nâtiônàlizætiøn"));
  }

  public function testStrirpos()
  {
    $this->assertEquals(3, UTF8::strripos('DÉJÀ', 'à'));
    $this->assertEquals(false, UTF8::strripos('aςσb', 'ΣΣ'));
    $this->assertEquals(6, UTF8::strripos("κόσμε-κόσμε", "Κ"));
    $this->assertEquals(11, UTF8::strripos("test κόσμε κόσμε test", "Κ"));
    $this->assertEquals(7, UTF8::strripos("中文空白-ÖÄÜ-中文空白", "ü"));
  }

  public function testStrrpos()
  {
    $this->assertEquals(false, UTF8::strrpos('한국어', ''));
    $this->assertEquals(1, UTF8::strrpos('한국어', '국'));
    $this->assertEquals(6, UTF8::strrpos("κόσμε-κόσμε", "κ"));
    $this->assertEquals(13, UTF8::strrpos("test κόσμε κόσμε test", "σ"));
    $this->assertEquals(9, UTF8::strrpos("中文空白-ÖÄÜ-中文空白", "中"));
  }

  public function testStrpos()
  {
    $this->assertEquals(false, UTF8::strpos('abc', ''));
    $this->assertEquals(false, UTF8::strpos('abc', 'd'));
    $this->assertEquals(false, UTF8::strpos('abc', 'a', 3));
    //$this->assertEquals(0, UTF8::strpos('abc', 'a', -1));
    $this->assertEquals(1, UTF8::strpos('한국어', '국'));
    $this->assertEquals(0, UTF8::strpos("κόσμε-κόσμε-κόσμε", "κ"));
    $this->assertEquals(7, UTF8::strpos("test κόσμε test κόσμε", "σ"));
    $this->assertEquals(8, UTF8::strpos("ABC-ÖÄÜ-中文空白-中文空白", "中"));
  }

  public function testStripos()
  {
    $this->assertEquals(3, UTF8::stripos('DÉJÀ', 'à'));
    $this->assertEquals(1, UTF8::stripos('aςσb', 'ΣΣ'));
    $this->assertEquals(16, UTF8::stripos('der Straße nach Paris', 'Paris'));
    $this->assertEquals(4, UTF8::stripos("öäü-κόσμε-κόσμε-κόσμε", "Κ"));
    $this->assertEquals(5, UTF8::stripos("Test κόσμε test κόσμε", "Κ"));
    $this->assertEquals(4, UTF8::stripos("ABC-ÖÄÜ-中文空白-中文空白", "ö"));
  }

  public function testOrd()
  {
    $testArray = array(
        "\xF0\x90\x8C\xBC" => 66364,
        "中"                => 20013,
        "₧"                => 8359,
        "κ"                => 954,
        "ö"                => 246,
        "ñ"                => 241,
        "{"                => 123,
        "a"                => 97,
        " "                => 32,
        ""                 => 0,
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::ord($actual));
    }
  }

  public function testHtmlEncode()
  {
    $testArray = array(
        "{-test" => "&#123;&#45;&#116;&#101;&#115;&#116;",
        "中文空白"   => "&#20013;&#25991;&#31354;&#30333;",
        "κόσμε"  => "&#954;&#8057;&#963;&#956;&#949;",
        "öäü"    => "&#246;&#228;&#252;",
        " "      => "&#32;",
        ""       => "",
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::html_encode($actual));
    }
  }

  public function testSingleChrHtmlEncode()
  {
    $testArray = array(
        "{" => "&#123;",
        "中" => "&#20013;",
        "κ" => "&#954;",
        "ö" => "&#246;",
        ""  => ""
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::single_chr_html_encode($actual));
    }
  }

  public function testChrSizeList()
  {
    $testArray = array(
        "中文空白"      => array(
            3,
            3,
            3,
            3
        ),
        "öäü"       => array(
            2,
            2,
            2
        ),
        "abc"       => array(
            1,
            1,
            1
        ),
        ""          => array(),
        "中文空白-test" => array(
            3,
            3,
            3,
            3,
            1,
            1,
            1,
            1,
            1
        ),
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::chr_size_list($actual));
    }
  }

  public function testWordCount()
  {
    $testArray = array(
        "中文空白"        => 1,
        "öäü öäü öäü" => 3,
        "abc"         => 1,
        ""            => 0,
        " "           => 0
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::str_word_count($actual));
    }
  }

  public function testMaxChrWidth()
  {
    $testArray = array(
        "中文空白" => 3,
        "öäü"  => 2,
        "abc"  => 1,
        ""     => 0
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::max_chr_width($actual));
    }
  }

  public function testSplit()
  {
    $this->assertEquals(
        array(
            "中",
            "文",
            "空",
            "白"
        ), UTF8::split("中文空白")
    );
    $this->assertEquals(
        array(
            "中文",
            "空白"
        ), UTF8::split("中文空白", 2)
    );
    $this->assertEquals(array("中文空白"), UTF8::split("中文空白", 4));
    $this->assertEquals(array("中文空白"), UTF8::split("中文空白", 8));
  }

  public function testChunkSplit()
  {
    $result = UTF8::chunk_split("ABC-ÖÄÜ-中文空白-κόσμε", 3);
    $expected = "ABC\r\n-ÖÄ\r\nÜ-中\r\n文空白\r\n-κό\r\nσμε";

    $this->assertEquals($expected, $result);
  }
}
