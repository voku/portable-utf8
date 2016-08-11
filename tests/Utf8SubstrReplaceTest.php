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
    self::assertSame($replaced, u::substr_replace($str, 'X', 8));
  }

  public function test_empty_string()
  {
    $str = '';
    $replaced = 'X';
    self::assertSame($replaced, u::substr_replace($str, 'X', 8));
  }

  public function test_negative()
  {
    $str = 'testing';
    $replaced = substr_replace($str, 'foo', 0, -2);
    self::assertSame($replaced, u::substr_replace($str, 'foo', 0, -2));

    $str = 'testing';
    $replaced = substr_replace($str, 'foo', -2, 0);
    self::assertSame($replaced, u::substr_replace($str, 'foo', -2, 0));

    $str = 'testing';
    $replaced = substr_replace($str, 'foo', -2, -2);
    self::assertSame($replaced, u::substr_replace($str, 'foo', -2, -2));

    $str = array('testing');
    $replaced = substr_replace($str, 'foo', -2, -2);
    self::assertSame($replaced, u::substr_replace($str, 'foo', -2, -2));

    $str = 'testing';
    $replaced = substr_replace($str, array('foo'), -2, -2);
    self::assertSame($replaced, u::substr_replace($str, array('foo'), -2, -2));

    $str = array('testing', 'testingV2');
    $replaced = substr_replace($str, array('foo', 'fooV2'), -2, -2);
    self::assertSame($replaced, u::substr_replace($str, array('foo', 'fooV2'), -2, -2));

    $str = array('testing', 'testingV2');
    $replaced = substr_replace($str, array('foo', 'fooV2'), array(1, 2), array(-1, 1));
    self::assertSame($replaced, u::substr_replace($str, array('foo', 'fooV2'), array(1, 2), array(-1, 1)));

    $str = array('testing', 'testingV2');
    $replaced = substr_replace($str, array('foo', 'fooV2'), -2, array(-1, 1));
    self::assertSame($replaced, u::substr_replace($str, array('foo', 'fooV2'), -2, array(-1, 1)));

    $str = array('testing', 'testingV2');
    $replaced = substr_replace($str, array('foo', 'fooV2'), array(1, 2), -1);
    self::assertSame($replaced, u::substr_replace($str, array('foo', 'fooV2'), array(1, 2), -1));

    $str = 'testing';
    $replaced = substr_replace($str, array(), -2, -2);
    self::assertSame($replaced, u::substr_replace($str, array(), -2, -2));

    $str = array('testing', 'lall');
    $replaced = substr_replace($str, 'foo', -2, -2);
    self::assertSame($replaced, u::substr_replace($str, 'foo', -2, -2));

    $str = array('Iñtërnâtiônàlizætiøn', 'foo');
    //$replaced = substr_replace($str, 'foo', -2, -2); // INFO: this isn't multibyte ready
    self::assertSame(array('Iñtërnâtiônàlizætifooøn', 'ffoooo'), u::substr_replace($str, 'foo', -2, -2));


    $str = array('Iñtërnâtiônàlizætiøn', 'foo');
    //$replaced = substr_replace($str, 'æ', 1); // INFO: this isn't multibyte ready

    self::assertSame(array('XIñtërnâtiônàlizætiøn', 'Xfoo'), u::substr_replace($str, 'X', 0));
    self::assertSame(array('IXñtërnâtiônàlizætiøn', 'fXoo'), u::substr_replace($str, 'X', 1));
    self::assertSame(array('IñtërnâtiôXnàlizætiøn', 'fooX'), u::substr_replace($str, 'X', 10));

    self::assertSame(array('XIñtërnâtiônàlizætiøn', 'Xfoo'), u::substr_replace($str, 'X', array(0, 0)));
    self::assertSame(array('IXñtërnâtiônàlizætiøn', 'fXoo'), u::substr_replace($str, 'X', array(1, 1)));
    self::assertSame(array('IñtërnâtiôXnàlizætiøn', 'fooX'), u::substr_replace($str, 'X', array(10, 10)));

    self::assertSame(array('æIñtërnâtiônàlizætiøn', 'æfoo'), u::substr_replace($str, 'æ', 0));
    self::assertSame(array('Iæñtërnâtiônàlizætiøn', 'fæoo'), u::substr_replace($str, 'æ', 1));
    self::assertSame(array('Iñtërnâtiôænàlizætiøn', 'fooæ'), u::substr_replace($str, 'æ', 10));

    self::assertSame(array('Iñtërnâtiôænàlizætiøn', 'fooæ'), u::substr_replace($str, 'æ', 10, 0));
    self::assertSame(array('Iñtërnâtiôæàlizætiøn', 'fooæ'), u::substr_replace($str, 'æ', 10, 1));
    self::assertSame(array('Iñtërnâtiôæ', 'fooæ'), u::substr_replace($str, 'æ', 10, 10));
  }

  public function test_zero()
  {
    $str = 'testing';
    $replaced = substr_replace($str, 'foo', 0, 0);
    self::assertSame($replaced, u::substr_replace($str, 'foo', 0, 0));
  }

  public function test_linefeed()
  {
    $str = "Iñ\ntërnâtiônàlizætiøn";
    $replaced = "Iñ\ntërnâtX";
    self::assertSame($replaced, u::substr_replace($str, 'X', 9));

    // ---

    $str = "Iñ\ntërnâtiônàlizætiøn";
    $replaced = "Iñ\ntërnâtà";
    self::assertSame($replaced, u::substr_replace($str, 'à', 9));
  }

  public function test_linefeed_replace()
  {
    $str = "Iñ\ntërnâtiônàlizætiøn";
    $replaced = "Iñ\ntërnâtX\nY";
    self::assertSame($replaced, u::substr_replace($str, "X\nY", 9));
  }
}
