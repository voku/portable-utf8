<?php

namespace Patchwork\Tests\Utf8;

use Normalizer as n;
use voku\helper\Bootup;

class BootupTest extends \PHPUnit_Framework_TestCase
{
  function testFilterRequestInputs()
  {
    $c = "à";
    $d = n::normalize($c, n::NFD);

    $bak = array(
        $_GET,
        $_POST,
        $_COOKIE,
        $_REQUEST,
        $_ENV,
        $_FILES
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
                ''
            ),
            'type'     => array(
                '',
                ''
            ),
            'tmp_name' => array(
                '',
                ''
            ),
            'error'    => array(
                4,
                4
            ),
            'size'     => array(
                0,
                0
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

    $this->assertSame($expect, $_GET);

    list($_GET, $_POST, $_COOKIE, $_REQUEST, $_ENV, $_FILES) = $bak;
  }

  function testFilterRequestUri()
  {
    $uriA = '/' . urlencode("bàr");
    $uriB = '/' . urlencode(utf8_decode("bàr"));
    $uriC = '/' . utf8_decode("bàr");
    $uriD = '/' . "bàr";

    $u = Bootup::filterRequestUri($uriA, false);
    $this->assertSame($uriA, $u);

    $u = Bootup::filterRequestUri($uriB, false);
    $this->assertSame($uriA, $u);

    $u = Bootup::filterRequestUri($uriC, false);
    $this->assertSame($uriA, $u);

    $u = Bootup::filterRequestUri($uriD, false);
    $this->assertSame($uriD, $u);
  }
}
