<?php

declare(strict_types=0);

namespace voku\helper;

use Symfony\Polyfill\Intl\Grapheme\Grapheme;
use Symfony\Polyfill\Xml\Xml;

/**
 * UTF8-Helper-Class
 *
 * @package voku\helper
 */
final class UTF8NonStrict
{
  /**
   * @param int $int
   *
   * @return string
   */
  public static function chr($int)
  {
    return chr($int);
  }
}
