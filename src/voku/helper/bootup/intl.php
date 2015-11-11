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

/** @noinspection PhpUsageOfSilenceOperatorInspection */
@trigger_error('You are using a fallback implementation of the intl extension. Installing the native one is highly recommended instead. | http://php.net/manual/en/intl.installation.php', E_USER_DEPRECATED);

/**
 * @param string $str
 * @param int    $form
 *
 * @return bool
 */
function normalizer_is_normalized($str, $form = s\Normalizer::NFC)
{
  return s\Normalizer::isNormalized($str, $form);
}

/**
 * @param string $str
 * @param int    $form
 *
 * @return string
 */
function normalizer_normalize($str, $form = s\Normalizer::NFC)
{
  return s\Normalizer::normalize($str, $form);
}

/**
 * @param string $str
 * @param int    $size
 * @param int    $type
 * @param int    $start
 * @param int    $next
 *
 * @return string
 */
function grapheme_extract($str, $size, $type = 0, $start = 0, &$next = 0)
{
  return s\Intl::grapheme_extract($str, $size, $type, $start, $next);
}

/**
 * @param string $str
 * @param string $needle
 * @param int    $offset
 *
 * @return bool|int|null
 */
function grapheme_stripos($str, $needle, $offset = 0)
{
  return s\Intl::grapheme_stripos($str, $needle, $offset);
}

/**
 * @param string $str
 * @param string $needle
 * @param bool   $before_needle
 *
 * @return false|string
 */
function grapheme_stristr($str, $needle, $before_needle = false)
{
  return s\Intl::grapheme_stristr($str, $needle, $before_needle);
}

/**
 * @param string $str
 *
 * @return integer|null
 */
function grapheme_strlen($str)
{
  return s\Intl::grapheme_strlen($str);
}

/**
 * @param string $str
 * @param string $needle
 * @param int    $offset
 *
 * @return bool|int|null
 */
function grapheme_strpos($str, $needle, $offset = 0)
{
  return s\Intl::grapheme_strpos($str, $needle, $offset);
}

/**
 * @param string $str
 * @param string $needle
 * @param int    $offset
 *
 * @return bool|int|null
 */
function grapheme_strripos($str, $needle, $offset = 0)
{
  return s\Intl::grapheme_strripos($str, $needle, $offset);
}

/**
 * @param string $str
 * @param string $needle
 * @param int    $offset
 *
 * @return bool|int|null
 */
function grapheme_strrpos($str, $needle, $offset = 0)
{
  return s\Intl::grapheme_strrpos($str, $needle, $offset);
}

/**
 * @param string $str
 * @param string $needle
 * @param bool   $before_needle
 *
 * @return false|string
 */
function grapheme_strstr($str, $needle, $before_needle = false)
{
  return s\Intl::grapheme_strstr($str, $needle, $before_needle);
}

/**
 * @param string $str
 * @param int    $start
 * @param int    $len
 *
 * @return false|string
 */
function grapheme_substr($str, $start, $len = 2147483647)
{
  return s\Intl::grapheme_substr($str, $start, $len);
}
