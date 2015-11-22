<?php

/*
 * Copyright (C) 2013 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */

use voku\helper\shim\Iconv;

const ICONV_IMPL = 'Patchwork';
const ICONV_VERSION = '1.0';
const ICONV_MIME_DECODE_STRICT = 1;
const ICONV_MIME_DECODE_CONTINUE_ON_ERROR = 2;

/** @noinspection PhpUsageOfSilenceOperatorInspection */
@trigger_error('You are using a fallback implementation of the iconv extension. Installing the native one is highly recommended instead. | http://php.net/manual/en/iconv.installation.php', E_USER_DEPRECATED);

/**
 * @param string $from
 * @param string $to
 * @param string $str
 *
 * @return string|false
 */
function iconv($from, $to, $str)
{
  return Iconv::iconv($from, $to, $str);
}

/**
 * @param string $type
 *
 * @return string
 */
function iconv_get_encoding($type = 'all')
{
  return Iconv::iconv_get_encoding($type);
}

/**
 * @param string $type
 * @param string $charset
 *
 * @return bool
 */
function iconv_set_encoding($type, $charset)
{
  return Iconv::iconv_set_encoding($type, $charset);
}

/**
 * @param string $name
 * @param string $value
 * @param array  $pref
 *
 * @return string
 */
function iconv_mime_encode($name, $value, $pref = INF)
{
  return Iconv::iconv_mime_encode($name, $value, $pref);
}

/**
 * @param string $buffer
 * @param mixed  $mode
 *
 * @return string|false
 */
function ob_iconv_handler($buffer, $mode)
{
  return Iconv::ob_iconv_handler($buffer, $mode);
}

/**
 * @param string $encoded_headers
 * @param int    $mode
 * @param string $enc
 *
 * @return array|bool
 */
function iconv_mime_decode_headers($encoded_headers, $mode = 0, $enc = INF)
{
  return Iconv::iconv_mime_decode_headers($encoded_headers, $mode, $enc);
}

if (extension_loaded('mbstring')) {

  /**
   * @param string $str
   * @param string $encoding
   *
   * @return bool|int
   */
  function iconv_strlen($str, $encoding = INF)
  {
    INF === $encoding && $encoding = Iconv::$internal_encoding;

    return mb_strlen($str, $encoding);
  }

  /**
   * @param string $haystack
   * @param string $needle
   * @param int    $offset
   * @param string $encoding
   *
   * @return string
   */
  function iconv_strpos($haystack, $needle, $offset = 0, $encoding = INF)
  {
    INF === $encoding && $encoding = Iconv::$internal_encoding;

    return mb_strpos($haystack, $needle, $offset, $encoding);
  }

  /**
   * @param string $haystack
   * @param string $needle
   * @param string $encoding
   *
   * @return bool|int
   */
  function iconv_strrpos($haystack, $needle, $encoding = INF)
  {
    INF === $encoding && $encoding = Iconv::$internal_encoding;

    return mb_strrpos($haystack, $needle, 0, $encoding);
  }

  /**
   * @param string   $str
   * @param int      $start
   * @param integer $length
   * @param string   $encoding
   *
   * @return string
   */
  function iconv_substr($str, $start, $length = 2147483647, $encoding = INF)
  {
    INF === $encoding && $enc = Iconv::$internal_encoding;

    return mb_substr($str, $start, $length, $encoding);
  }

  /**
   * @param string $encoded_headers
   * @param int    $mode
   * @param string $encoding
   *
   * @return bool|string
   */
  function iconv_mime_decode($encoded_headers, $mode = 0, $encoding = INF)
  {
    INF === $encoding && $encoding = Iconv::$internal_encoding;

    return mb_decode_mimeheader($encoded_headers, $mode, $encoding);
  }

} else {
  if (extension_loaded('xml')) {

    /**
     * @param string $str
     * @param string $encoding
     *
     * @return bool|int
     */
    function iconv_strlen($str, $encoding = INF)
    {
      return Iconv::strlen1($str, $encoding);
    }

  } else {

    /**
     * @param string $str
     * @param string $encoding
     *
     * @return bool|int
     */
    function iconv_strlen($str, $encoding = INF)
    {
      return Iconv::strlen2($str, $encoding);
    }
  }

  /**
   * @param string $str
   * @param string $needle
   * @param int    $offset
   * @param string $encoding
   *
   * @return string
   */
  function iconv_strpos($str, $needle, $offset = 0, $encoding = INF)
  {
    return Iconv::iconv_strpos($str, $needle, $offset, $encoding);
  }

  /**
   * @param string $str
   * @param string $needle
   * @param string $encoding
   *
   * @return bool|int
   */
  function iconv_strrpos($str, $needle, $encoding = INF)
  {
    return Iconv::iconv_strrpos($str, $needle, $encoding);
  }

  /**
   * @param string $str
   * @param int    $start
   * @param int    $length
   * @param string $encoding
   *
   * @return string
   */
  function iconv_substr($str, $start, $length = 2147483647, $encoding = INF)
  {
    return Iconv::iconv_substr($str, $start, $length, $encoding);
  }

  /**
   * @param string $encoded_headers
   * @param int    $mode
   * @param string $encoding
   *
   * @return bool|string
   */
  function iconv_mime_decode($encoded_headers, $mode = 0, $encoding = INF)
  {
    return Iconv::iconv_mime_decode($encoded_headers, $mode, $encoding);
  }
}
