<?php

use voku\helper\Bootup;
use voku\helper\UTF8;

/**
 * Class BootupTest
 */
class BootupTest extends PHPUnit_Framework_TestCase
{
  public function testInitAll()
  {
    Bootup::initAll();

    self::assertSame('UTF-8', ini_get('default_charset'));
  }

  public function testFilterRequestInputs()
  {
    UTF8::checkForSupport();

    $c = 'à';
    $d = Normalizer::normalize($c, Normalizer::NFD);

    $bak = array(
        $_GET,
        $_POST,
        $_COOKIE,
        $_REQUEST,
        $_ENV,
        $_FILES,
    );

    $_GET = array(
        'n' => 4,
        'a' => "\xE9",
        'b' => substr($d, 1),
        'c' => $c,
        'd' => $d,
        'e' => "\n\r\n\r",
    );

    $_GET['f'] = $_GET;

    $_FILES = array(
        'a' => array(
            'name'     => '',
            'type'     => '',
            'tmp_name' => '',
            'error'    => 4,
            'size'     => 0,
        ),
        'b' => array(
            'name'     => array(
                '',
                '',
            ),
            'type'     => array(
                '',
                '',
            ),
            'tmp_name' => array(
                '',
                '',
            ),
            'error'    => array(
                4,
                4,
            ),
            'size'     => array(
                0,
                0,
            ),
        ),
    );

    Bootup::filterRequestInputs();

    $expect = array(
        'n' => 4,
        'a' => 'é',
        'b' => '◌' . substr($d, 1),
        'c' => $c,
        'd' => $c,
        'e' => "\n\n\n",
    );

    $expect['f'] = $expect;

    self::assertSame($expect, $_GET);

    list($_GET, $_POST, $_COOKIE, $_REQUEST, $_ENV, $_FILES) = $bak;
  }

  public function testFilterRequestUri()
  {
    $uriA = '/' . urlencode('bàr');
    $uriB = '/' . urlencode(utf8_decode('bàr'));
    $uriC = '/' . utf8_decode('bàr');
    $uriD = '/' . 'bàr';

    // ---

    $u = Bootup::filterRequestUri(null, false);
    self::assertSame(false, $u);

    $_SERVER['REQUEST_URI'] = $uriA;

    $u = Bootup::filterRequestUri(null, false);
    self::assertSame('/b%C3%A0r', $u);

    // ---

    $u = Bootup::filterRequestUri($uriA, false);
    self::assertSame($uriA, $u);

    $u = Bootup::filterRequestUri($uriB, false);
    self::assertSame($uriA, $u);

    $u = Bootup::filterRequestUri($uriC, false);
    self::assertSame($uriA, $u);

    $u = Bootup::filterRequestUri($uriD, false);
    self::assertSame($uriD, $u);
  }

  public function testGetRandomBytes()
  {
    $rand_false = Bootup::get_random_bytes(0);
    self::assertSame(false, $rand_false);

    $rand_false = Bootup::get_random_bytes('test');
    self::assertSame(false, $rand_false);

    $rand = Bootup::get_random_bytes(32);
    self::assertSame(32, strlen($rand));

    $rand = Bootup::get_random_bytes(0);
    self::assertSame(0, strlen($rand));

    $bytes = array(
        Bootup::get_random_bytes(12),
        Bootup::get_random_bytes(16),
        Bootup::get_random_bytes(16)
    );

    self::assertTrue(
        strlen(bin2hex($bytes[0])) === 24
    );

    self::assertFalse(
        $bytes[1] === $bytes[2]
    );
  }

  public function testIsPhp()
  {
    $isPHP = Bootup::is_php('0.1');
    self::assertSame(true, $isPHP);

    $isPHP = Bootup::is_php('999');
    self::assertSame(false, $isPHP);

    if (defined('PHP_MAJOR_VERSION') && PHP_MAJOR_VERSION <= 5) {
      $isPHP = Bootup::is_php('7');
      self::assertSame(false, $isPHP);
    }

    if (defined('PHP_MAJOR_VERSION') && PHP_MAJOR_VERSION >= 5) {
      $isPHP = Bootup::is_php('5.0');
      self::assertSame(true, $isPHP);
    }

    if (defined('PHP_MAJOR_VERSION') && PHP_MAJOR_VERSION >= 7) {
      $isPHP = Bootup::is_php('7');
      self::assertSame(true, $isPHP);
    }
  }
}
