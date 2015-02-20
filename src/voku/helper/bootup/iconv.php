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

/**
 * @param $from
 * @param $to
 * @param $s
 *
 * @return bool|string
 */
function iconv($from, $to, $s)
{
  return Iconv::iconv($from, $to, $s);
}

/**
 * @param string $type
 *
 * @return array|string
 */
function iconv_get_encoding($type = 'all')
{
  return Iconv::iconv_get_encoding($type);
}

/**
 * @param $type
 * @param $charset
 *
 * @return bool
 */
function iconv_set_encoding($type, $charset)
{
  return Iconv::iconv_set_encoding($type, $charset);
}

/**
 * @param $name
 * @param $value
 * @param $pref
 *
 * @return string
 */
function iconv_mime_encode($name, $value, $pref = INF)
{
  return Iconv::iconv_mime_encode($name, $value, $pref);
}

/**
 * @param $buffer
 * @param $mode
 *
 * @return bool|string
 */
function ob_iconv_handler($buffer, $mode)
{
  return Iconv::ob_iconv_handler($buffer, $mode);
}

/**
 * @param     $encoded_headers
 * @param int $mode
 * @param     $enc
 *
 * @return array|bool
 */
function iconv_mime_decode_headers($encoded_headers, $mode = 0, $enc = INF)
{
  return Iconv::iconv_mime_decode_headers($encoded_headers, $mode, $enc);
}

if (extension_loaded('mbstring')) {

  /**
   * @param $s
   * @param $enc
   *
   * @return bool|int
   */
  function iconv_strlen($s, $enc = INF)
  {
    INF === $enc && $enc = Iconv::$internal_encoding;
    return mb_strlen($s, $enc);
  }

  /**
   * @param     $s
   * @param     $needle
   * @param int $offset
   * @param     $enc
   *
   * @return bool|int
   */
  function iconv_strpos($s, $needle, $offset = 0, $enc = INF)
  {
    INF === $enc && $enc = Iconv::$internal_encoding;
    return mb_strpos($s, $needle, $offset, $enc);
  }

  /**
   * @param $s
   * @param $needle
   * @param $enc
   *
   * @return bool|int
   */
  function iconv_strrpos($s, $needle, $enc = INF)
  {
    INF === $enc && $enc = Iconv::$internal_encoding;
    return mb_strrpos($s, $needle, $enc);
  }

  /**
   * @param     $s
   * @param     $start
   * @param int $length
   * @param     $enc
   *
   * @return string
   */
  function iconv_substr($s, $start, $length = 2147483647, $enc = INF)
  {
    INF === $enc && $enc = Iconv::$internal_encoding;
    return mb_substr($s, $start, $length, $enc);
  }

  /**
   * @param     $encoded_headers
   * @param int $mode
   * @param     $enc
   *
   * @return bool|string
   */
  function iconv_mime_decode($encoded_headers, $mode = 0, $enc = INF)
  {
    INF === $enc && $enc = Iconv::$internal_encoding;
    return mb_decode_mimeheader($encoded_headers, $mode, $enc);
  }

} else {
  if (extension_loaded('xml')) {

    /**
     * @param $s
     * @param $enc
     *
     * @return bool|int
     */
    function iconv_strlen($s, $enc = INF)
    {
      return Iconv::strlen1($s, $enc);
    }

  } else {

    /**
     * @param $s
     * @param $enc
     *
     * @return bool|int
     */
    function iconv_strlen($s, $enc = INF)
    {
      return Iconv::strlen2($s, $enc);
    }
  }

  /**
   * @param     $s
   * @param     $needle
   * @param int $offset
   * @param     $enc
   *
   * @return bool|int
   */
  function iconv_strpos($s, $needle, $offset = 0, $enc = INF)
  {
    return Iconv::iconv_strpos($s, $needle, $offset, $enc);
  }

  /**
   * @param $s
   * @param $needle
   * @param $enc
   *
   * @return bool|int
   */
  function iconv_strrpos($s, $needle, $enc = INF)
  {
    return Iconv::iconv_strrpos($s, $needle, $enc);
  }

  /**
   * @param     $s
   * @param     $start
   * @param int $length
   * @param     $enc
   *
   * @return bool|string
   */
  function iconv_substr($s, $start, $length = 2147483647, $enc = INF)
  {
    return Iconv::iconv_substr($s, $start, $length, $enc);
  }

  /**
   * @param     $encoded_headers
   * @param int $mode
   * @param     $enc
   *
   * @return bool|string
   */
  function iconv_mime_decode($encoded_headers, $mode = 0, $enc = INF)
  {
    return Iconv::iconv_mime_decode($encoded_headers, $mode, $enc);
  }
}
