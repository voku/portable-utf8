<?php

declare(strict_types=0);

namespace voku\helper;

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
