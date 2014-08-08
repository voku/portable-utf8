<?php

use voku\helper\UTF8;

class ReplaceTest extends PHPUnit_Framework_TestCase {

  function test_strlen() {

    $string = 'Привет мир';

    $this->assertEquals(
      'Пока мир', UTF8::str_replace('Привет', 'Пока', $string)
    );

  }

}
