<?php

use Symfony\Polyfill\Xml\Xml as p;

/**
 * Class ShimXmlTest
 */
class ShimXmlTest extends PHPUnit_Framework_TestCase
{
  public function testUtf8Encode()
  {
    $s = array_map('chr', range(0, 255));
    $s = implode('', $s);
    $e = p::utf8_encode($s);

    self::assertSame(utf8_encode($s), p::utf8_encode($s));
    self::assertSame(utf8_decode($e), p::utf8_decode($e));

    self::assertSame('??', p::utf8_decode('Σ어'));

    $s = 444;

    self::assertSame(utf8_encode($s), p::utf8_encode($s));
    self::assertSame(utf8_decode($s), p::utf8_decode($s));
  }
}
