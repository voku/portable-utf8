<?php

namespace Patchwork\Tests\PHP\Shim;

use voku\helper\shim\Xml as p;

class XmlTest extends \PHPUnit_Framework_TestCase
{
  function testUtf8Encode()
  {
    $s = array_map('chr', range(0, 255));
    $s = implode('', $s);
    $e = p::utf8_encode($s) . 'Σ어';

    $this->assertSame(utf8_encode($s), p::utf8_encode($s));
    $this->assertSame(utf8_decode($e), p::utf8_decode($e));

    $s = 444;

    $this->assertSame(utf8_encode($s), p::utf8_encode($s));
    $this->assertSame(utf8_decode($s), p::utf8_decode($s));
  }
}
