<?php

use voku\helper\UTF8;

class UTF8Test extends PHPUnit_Framework_TestCase {

  function test_strlen() {
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
    public function testTrim($input, $output) {
        $this->assertSame($output, UTF8::trim($input));
    }

    /**
     * @return array
     */
    public function trimProvider() {
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

}
