<?php

use voku\helper\UTF8 as u;

/**
 * Class Utf8SubstrReplaceTest
 */
class Utf8SubstrReplaceTest extends PHPUnit_Framework_TestCase
{
  public function test_replace_start()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $replaced = 'IñtërnâtX';
    self::assertEquals($replaced, u::substr_replace($str, 'X', 8));
  }

  public function test_empty_string()
  {
    $str = '';
    $replaced = 'X';
    self::assertEquals($replaced, u::substr_replace($str, 'X', 8));
  }

  public function test_negative()
  {
    $str = 'testing';
    $replaced = substr_replace($str, 'foo', 0, -2);
    self::assertEquals($replaced, u::substr_replace($str, 'foo', 0, -2));

    $str = 'testing';
    $replaced = substr_replace($str, 'foo', -2, 0);
    self::assertEquals($replaced, u::substr_replace($str, 'foo', -2, 0));

    $str = 'testing';
    $replaced = substr_replace($str, 'foo', -2, -2);
    self::assertEquals($replaced, u::substr_replace($str, 'foo', -2, -2));

    $str = array('testing');
    $replaced = substr_replace($str, 'foo', -2, -2);
    self::assertEquals($replaced, u::substr_replace($str, 'foo', -2, -2));

    $str = 'testing';
    $replaced = substr_replace($str, array('foo'), -2, -2);
    self::assertEquals($replaced, u::substr_replace($str, array('foo'), -2, -2));

    $str = array('testing', 'testingV2');
    $replaced = substr_replace($str, array('foo', 'fooV2'), -2, -2);
    self::assertEquals($replaced, u::substr_replace($str, array('foo', 'fooV2'), -2, -2));

    $str = array('testing', 'testingV2');
    $replaced = substr_replace($str, array('foo', 'fooV2'), array(1, 2), array(-1, 1));
    self::assertEquals($replaced, u::substr_replace($str, array('foo', 'fooV2'), array(1, 2), array(-1, 1)));

    $str = array('testing', 'testingV2');
    $replaced = substr_replace($str, array('foo', 'fooV2'), -2, array(-1, 1));
    self::assertEquals($replaced, u::substr_replace($str, array('foo', 'fooV2'), -2, array(-1, 1)));

    $str = array('testing', 'testingV2');
    $replaced = substr_replace($str, array('foo', 'fooV2'), array(1, 2), -1);
    self::assertEquals($replaced, u::substr_replace($str, array('foo', 'fooV2'), array(1, 2), -1));

    $str = 'testing';
    $replaced = substr_replace($str, array(), -2, -2);
    self::assertEquals($replaced, u::substr_replace($str, array(), -2, -2));

    $str = array('testing', 'lall');
    $replaced = substr_replace($str, 'foo', -2, -2);
    self::assertEquals($replaced, u::substr_replace($str, 'foo', -2, -2));

    $str = array('Iñtërnâtiônàlizætiøn', 'foo');
    //$replaced = substr_replace($str, 'foo', -2, -2); // INFO: this isn't multibyte ready
    self::assertEquals(array('Iñtërnâtiônàlizætifooøn', 'ffoooo'), u::substr_replace($str, 'foo', -2, -2));


    $str = array('Iñtërnâtiônàlizætiøn', 'foo');
    //$replaced = substr_replace($str, 'æ', 1); // INFO: this isn't multibyte ready
    self::assertEquals(array('Iæñtërnâtiônàlizætiøn', 'fæoo'), u::substr_replace($str, 'æ', 1));
  }

  public function test_zero()
  {
    $str = 'testing';
    $replaced = substr_replace($str, 'foo', 0, 0);
    self::assertEquals($replaced, u::substr_replace($str, 'foo', 0, 0));
  }

  public function test_linefeed()
  {
    $str = "Iñ\ntërnâtiônàlizætiøn";
    $replaced = "Iñ\ntërnâtX";
    self::assertEquals($replaced, u::substr_replace($str, 'X', 9));
  }

  public function test_linefeed_replace()
  {
    $str = "Iñ\ntërnâtiônàlizætiøn";
    $replaced = "Iñ\ntërnâtX\nY";
    self::assertEquals($replaced, u::substr_replace($str, "X\nY", 9));
  }
}
