<?php

declare(strict_types=1);

use voku\helper\UTF8;

/**
 * Class Utf8IsUtf8Test
 */
class Utf8IsUtf8Test extends \PHPUnit\Framework\TestCase
{

  public function testValidUtf8()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertTrue(UTF8::is_utf8($str));
  }

  public function testValidUtf8Ascii()
  {
    $str = 'testing';
    self::assertTrue(UTF8::is_utf8($str));
  }

  public function testInvalidUtf8()
  {
    $str = "Iñtërnâtiôn\xe9àlizætiøn";
    self::assertFalse(UTF8::is_utf8($str));
  }

  public function testInvalidUtf8Ascii()
  {
    $str = "this is an invalid char '\xe9' here";
    self::assertFalse(UTF8::is_utf8($str));
  }

  public function testInvalidUtf8Start()
  {
    $str = "\xe9Iñtërnâtiônàlizætiøn";
    self::assertFalse(UTF8::is_utf8($str));
  }

  public function testValidUtf8End()
  {
    $str = "Iñtërnâtiônàlizætiøn\xe9";
    self::assertTrue(UTF8::is_utf8($str));
  }

  public function testValidTwoOctetId()
  {
    $str = "abc\xc3\xb1";
    self::assertTrue(UTF8::is_utf8($str));
  }

  public function testInvalidTwoOctetSequence()
  {
    $str = "Iñtërnâtiônàlizætiøn \xc3\x28 Iñtërnâtiônàlizætiøn";
    self::assertFalse(UTF8::is_utf8($str));
  }

  public function testInvalidIdBetweenTwoAndThree()
  {
    $str = "Iñtërnâtiônàlizætiøn\xa0\xa1Iñtërnâtiônàlizætiøn";
    self::assertFalse(UTF8::is_utf8($str));
  }


  public function testValidThreeOctetId()
  {
    $str = "Iñtërnâtiônàlizætiøn\xe2\x82\xa1Iñtërnâtiônàlizætiøn";
    self::assertTrue(UTF8::is_utf8($str));
  }


  public function testInvalidThreeOctetSequenceSecond()
  {
    $str = "Iñtërnâtiônàlizætiøn\xe2\x28\xa1Iñtërnâtiônàlizætiøn";
    self::assertFalse(UTF8::is_utf8($str));
  }

  public function testInvalidThreeOctetSequenceThird()
  {
    $str = "Iñtërnâtiônàlizætiøn\xe2\x82\x28Iñtërnâtiônàlizætiøn";
    self::assertFalse(UTF8::is_utf8($str));
  }

  public function testValidFourOctetId()
  {
    $str = "Iñtërnâtiônàlizætiøn\xf0\x90\x8c\xbcIñtërnâtiônàlizætiøn";
    self::assertTrue(UTF8::is_utf8($str));
  }

  public function testInvalidFourOctetSequence()
  {
    $str = "Iñtërnâtiônàlizætiøn\xf0\x28\x8c\xbcIñtërnâtiônàlizætiøn";
    self::assertFalse(UTF8::is_utf8($str));
  }

  public function testInvalidFiveOctetSequence()
  {
    $str = "Iñtërnâtiônàlizætiøn\xf8\xa1\xa1\xa1\xa1Iñtërnâtiônàlizætiøn";
    self::assertFalse(UTF8::is_utf8($str));
  }

  public function testInvalidSixOctetSequence()
  {
    $str = "Iñtërnâtiônàlizætiøn\xfc\xa1\xa1\xa1\xa1\xa1Iñtërnâtiônàlizætiøn";
    self::assertFalse(UTF8::is_utf8($str));
  }

  public function testValidUtf8CleanUp()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    self::assertSame('Iñtërnâtiônàlizætiøn', UTF8::cleanup($str));
  }

  public function testValidUtf8AsciiCleanUp()
  {
    $str = 'testing';
    self::assertSame('testing', UTF8::cleanup($str));
  }

  public function testInvalidUtf8CleanUp()
  {
    $str = "Iñtërnâtiôn\xe9àlizætiøn";
    self::assertSame('Iñtërnâtiônàlizætiøn', UTF8::cleanup($str));
  }

  public function testInvalidUtf8AsciiCleanUp()
  {
    $str = "this is an invalid char '\xe9' here";
    self::assertSame("this is an invalid char '' here", UTF8::cleanup($str));
  }

  public function testInvalidUtf8MultipleCleanUp()
  {
    $str = "\xe9Iñtërnâtiôn\xe9àlizætiøn\xe9";
    self::assertSame('Iñtërnâtiônàlizætiøn', UTF8::cleanup($str));
  }

  public function testValidTwoOctetIdCleanUp()
  {
    $str = "abc\xc3\xb1";
    self::assertSame($str, UTF8::cleanup($str));
  }

  public function testInvalidTwoOctetSequenceCleanUp()
  {
    $str = "Iñtërnâtiônàlizætiøn \xc3\x28 Iñtërnâtiônàlizætiøn";
    $stripped = "Iñtërnâtiônàlizætiøn \x28 Iñtërnâtiônàlizætiøn";
    self::assertSame($stripped, UTF8::cleanup($str));
  }

  public function testInvalidIdBetweenTwoAndThreeCleanUp()
  {
    $str = "Iñtërnâtiônàlizætiøn\xa0\xa1Iñtërnâtiônàlizætiøn";
    $stripped = 'IñtërnâtiônàlizætiønIñtërnâtiônàlizætiøn';
    self::assertSame($stripped, UTF8::cleanup($str));
  }

  public function testValidThreeOctetIdCleanUp()
  {
    $str = "Iñtërnâtiônàlizætiøn\xe2\x82\xa1Iñtërnâtiônàlizætiøn";
    self::assertSame($str, UTF8::cleanup($str));
  }

  public function testInvalidThreeOctetSequenceSecondCleanUp()
  {
    $str = "Iñtërnâtiônàlizætiøn\xe2\x28\xa1Iñtërnâtiônàlizætiøn";
    $stripped = 'Iñtërnâtiônàlizætiøn(Iñtërnâtiônàlizætiøn';
    self::assertSame($stripped, UTF8::cleanup($str));
  }

  public function testInvalidThreeOctetSequenceThirdCleanUp()
  {
    $str = "Iñtërnâtiônàlizætiøn\xe2\x82\x28Iñtërnâtiônàlizætiøn";
    $stripped = 'Iñtërnâtiônàlizætiøn(Iñtërnâtiônàlizætiøn';
    self::assertSame($stripped, UTF8::cleanup($str));
  }

  public function testValidFourOctetIdCleanUp()
  {
    $str = "Iñtërnâtiônàlizætiøn\xf0\x90\x8c\xbcIñtërnâtiônàlizætiøn";
    self::assertSame($str, UTF8::cleanup($str));
  }

  public function testInvalidFourOctetSequenceCleanUp()
  {
    $str = "Iñtërnâtiônàlizætiøn\xf0\x28\x8c\xbcIñtërnâtiônàlizætiøn";
    $stripped = 'Iñtërnâtiônàlizætiøn(Iñtërnâtiônàlizætiøn';
    self::assertSame($stripped, UTF8::cleanup($str));
  }

  public function testInvalidFiveOctetSequenceCleanUp()
  {
    $str = "Iñtërnâtiônàlizætiøn\xf8\xa1\xa1\xa1\xa1Iñtërnâtiônàlizætiøn";
    $stripped = 'IñtërnâtiônàlizætiønIñtërnâtiônàlizætiøn';
    self::assertSame($stripped, UTF8::cleanup($str));
  }

  public function testInvalidSixOctetSequenceCleanUp()
  {
    $str = "Iñtërnâtiônàlizætiøn\xfc\xa1\xa1\xa1\xa1\xa1Iñtërnâtiônàlizætiøn";
    $stripped = 'IñtërnâtiônàlizætiønIñtërnâtiônàlizætiøn';
    self::assertSame($stripped, UTF8::cleanup($str));
  }
}
