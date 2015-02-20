<?php

/*
 * Copyright (C) 2013 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */

use voku\helper\shim as s;

const GRAPHEME_EXTR_COUNT = 0;
const GRAPHEME_EXTR_MAXBYTES = 1;
const GRAPHEME_EXTR_MAXCHARS = 2;

/**
 * @param     $s
 * @param int $form
 *
 * @return bool
 */
function normalizer_is_normalized($s, $form = s\Normalizer::NFC)
{
  return s\Normalizer::isNormalized($s, $form);
}

/**
 * @param     $s
 * @param int $form
 *
 * @return string
 */
function normalizer_normalize($s, $form = s\Normalizer::NFC)
{
  return s\Normalizer::normalize($s, $form);
}

/**
 * @param     $s
 * @param     $size
 * @param int $type
 * @param int $start
 * @param int $next
 *
 * @return string
 */
function grapheme_extract($s, $size, $type = 0, $start = 0, &$next = 0)
{
  return s\Intl::grapheme_extract($s, $size, $type, $start, $next);
}

/**
 * @param     $s
 * @param     $needle
 * @param int $offset
 *
 * @return bool|int|null
 */
function grapheme_stripos($s, $needle, $offset = 0)
{
  return s\Intl::grapheme_stripos($s, $needle, $offset);
}

/**
 * @param      $s
 * @param      $needle
 * @param bool $before_needle
 *
 * @return bool|string
 */
function grapheme_stristr($s, $needle, $before_needle = false)
{
  return s\Intl::grapheme_stristr($s, $needle, $before_needle);
}

/**
 * @param $s
 *
 * @return null
 */
function grapheme_strlen($s)
{
  return s\Intl::grapheme_strlen($s);
}

/**
 * @param     $s
 * @param     $needle
 * @param int $offset
 *
 * @return bool|int|null
 */
function grapheme_strpos($s, $needle, $offset = 0)
{
  return s\Intl::grapheme_strpos($s, $needle, $offset);
}

/**
 * @param     $s
 * @param     $needle
 * @param int $offset
 *
 * @return bool|int|null
 */
function grapheme_strripos($s, $needle, $offset = 0)
{
  return s\Intl::grapheme_strripos($s, $needle, $offset);
}

/**
 * @param     $s
 * @param     $needle
 * @param int $offset
 *
 * @return bool|int|null
 */
function grapheme_strrpos($s, $needle, $offset = 0)
{
  return s\Intl::grapheme_strrpos($s, $needle, $offset);
}

/**
 * @param      $s
 * @param      $needle
 * @param bool $before_needle
 *
 * @return bool|string
 */
function grapheme_strstr($s, $needle, $before_needle = false)
{
  return s\Intl::grapheme_strstr($s, $needle, $before_needle);
}

/**
 * @param     $s
 * @param     $start
 * @param int $len
 *
 * @return bool|string
 */
function grapheme_substr($s, $start, $len = 2147483647)
{
  return s\Intl::grapheme_substr($s, $start, $len);
}
