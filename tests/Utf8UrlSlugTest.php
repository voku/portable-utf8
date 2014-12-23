<?php

use voku\helper\UTF8 as u;

class Utf8UrlSlugTest extends PHPUnit_Framework_TestCase
{
  public function test_utf8()
  {
    $str = 'testiñg test.';
    $this->assertEquals('testing-test', u::url_slug($str));
  }

  public function test_ascii()
  {
    $str = 'testing - test';
    $this->assertEquals('testing-test', u::url_slug($str));
  }

  public function test_invalid_char()
  {
    $str = "tes\xE9ting";
    $this->assertEquals("testing", u::url_slug($str));
  }

  public function test_empty_str()
  {
    $str = '';
    $this->assertEmpty(u::url_slug($str));
  }

  public function test_nul_and_non_7_bit()
  {
    $str = "a\x00ñ\x00c";
    $this->assertEquals("anc", u::url_slug($str));
  }

  public function test_nul()
  {
    $str = "a\x00b\x00c";
    $this->assertEquals("abc", u::url_slug($str));
  }
}
