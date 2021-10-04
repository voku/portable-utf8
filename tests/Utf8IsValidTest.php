<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8 as u;

/**
 * Class Utf8IsValidTest
 *
 * @internal
 */
final class Utf8IsValidTest extends \PHPUnit\Framework\TestCase
{
    public function testValidUtf8()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        static::assertTrue(u::isUtf8($str));
    }

    public function testValidUtf8Ascii()
    {
        $str = 'ABC 123';
        static::assertTrue(u::isUtf8($str));
    }

    public function testInvalidUtf8()
    {
        $str = "Iñtërnâtiôn\xE9àlizætiøn";
        static::assertFalse(u::isUtf8($str));
    }

    public function testInvalidUtf8Ascii()
    {
        $str = "this is an invalid char '\xE9' here";
        static::assertFalse(u::isUtf8($str));
    }

    public function testEmptyString()
    {
        $str = '';
        static::assertTrue(u::isUtf8($str));
    }

    public function testValidTwoOctetId()
    {
        $str = "\xC3\xB1";
        static::assertTrue(u::isUtf8($str));
    }

    public function testValidCancel()
    {
        $str = '';
        static::assertTrue(u::isUtf8($str));
    }

    public function testInvalidTwoOctetSequence()
    {
        $str = "Iñtërnâtiônàlizætiøn \xC3\x28 Iñtërnâtiônàlizætiøn";
        static::assertFalse(u::isUtf8($str));
    }

    public function testInvalidIdBetweenTwoAndThree()
    {
        $str = "Iñtërnâtiônàlizætiøn\xA0\xA1Iñtërnâtiônàlizætiøn";
        static::assertFalse(u::isUtf8($str));
    }

    public function testValidThreeOctetId()
    {
        $str = "Iñtërnâtiônàlizætiøn\xE2\x82\xA1Iñtërnâtiônàlizætiøn";
        static::assertTrue(u::isUtf8($str));
    }

    public function testInvalidThreeOctetSequenceSecond()
    {
        $str = "Iñtërnâtiônàlizætiøn\xE2\x28\xA1Iñtërnâtiônàlizætiøn";
        static::assertFalse(u::isUtf8($str));
    }

    public function testInvalidThreeOctetSequenceThird()
    {
        $str = "Iñtërnâtiônàlizætiøn\xE2\x82\x28Iñtërnâtiônàlizætiøn";
        static::assertFalse(u::isUtf8($str));
    }

    public function testValidFourOctetId()
    {
        $str = "Iñtërnâtiônàlizætiøn\xF0\x90\x8C\xBCIñtërnâtiônàlizætiøn";
        static::assertTrue(u::isUtf8($str));
    }

    public function testInvalidFourOctetSequence()
    {
        $str = "Iñtërnâtiônàlizætiøn\xF0\x28\x8C\xBCIñtërnâtiônàlizætiøn";
        static::assertFalse(u::isUtf8($str));
    }

    public function testInvalidFiveOctetSequence()
    {
        $str = "Iñtërnâtiônàlizætiøn\xF8\xA1\xA1\xA1\xA1Iñtërnâtiônàlizætiøn";
        static::assertFalse(u::isUtf8($str));
    }

    public function testInvalidSixOctetSequence()
    {
        $str = "Iñtërnâtiônàlizætiøn\xFC\xA1\xA1\xA1\xA1\xA1Iñtërnâtiônàlizætiøn";
        static::assertFalse(u::isUtf8($str));
    }
}
