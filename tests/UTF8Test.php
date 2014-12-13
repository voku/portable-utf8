<?php

use voku\helper\UTF8;

class UTF8Test extends PHPUnit_Framework_TestCase
{

  function test_strlen()
  {
    $string = 'string <strong>with utf-8 chars åèä</strong> - doo-bee doo-bee dooh';

    $this->assertEquals(70, strlen($string));
    $this->assertEquals(67, UTF8::strlen($string));

    $string_test1 = strip_tags($string);
    $string_test2 = UTF8::strip_tags($string);

    $this->assertEquals(53, strlen($string_test1));
    $this->assertEquals(50, UTF8::strlen($string_test2));
  }

  /**
   * @dataProvider trimProvider
   */
  public function testTrim($input, $output)
  {
    $this->assertSame($output, UTF8::trim($input));
  }

  /**
   * @return array
   */
  public function trimProvider()
  {
    return array(
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

  public function testCleanup()
  {
    $examples = array(
      // Valud UTF-8
      "κόσμε"                    => array("κόσμε" => "κόσμε"),
      // Valid ASCII
      "a"                        => array("a" => "a"),
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
      "\xf0\x90\x8c\xbc"         => array("𐌼" => "𐌼"),
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

    foreach ($examples as $testString => $testResults) {
      foreach ($testResults as $before => $after) {
        //$this->assertEquals($before, $testString);
        $this->assertEquals($after, UTF8::cleanup($testString));
      }
    }

  }

}
