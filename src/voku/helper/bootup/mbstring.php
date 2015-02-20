<?php

/*
 * Copyright (C) 2013 Nicolas Grekas - p@tchwork.com
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the (at your option):
 * Apache License v2.0 (http://apache.org/licenses/LICENSE-2.0.txt), or
 * GNU General Public License v2.0 (http://gnu.org/licenses/gpl-2.0.txt).
 */

use voku\helper\shim\Mbstring;

const MB_OVERLOAD_MAIL = 1;
const MB_OVERLOAD_STRING = 2;
const MB_OVERLOAD_REGEX = 4;
const MB_CASE_UPPER = 0;
const MB_CASE_LOWER = 1;
const MB_CASE_TITLE = 2;

/**
 * @param $s
 * @param $to
 * @param $from
 *
 * @return bool|mixed|string
 */
function mb_convert_encoding($s, $to, $from = INF)
{
  return Mbstring::mb_convert_encoding($s, $to, $from);
}

/**
 * @param $s
 *
 * @return bool|string
 */
function mb_decode_mimeheader($s)
{
  return Mbstring::mb_decode_mimeheader($s);
}

/**
 * @param $s
 * @param $charset
 * @param $transfer_enc
 * @param $lf
 * @param $indent
 */
function mb_encode_mimeheader($s, $charset = INF, $transfer_enc = INF, $lf = INF, $indent = INF)
{
  Mbstring::mb_encode_mimeheader($s, $charset, $transfer_enc, $lf, $indent);
}

/**
 * @param $s
 * @param $mode
 * @param $enc
 *
 * @return bool|mixed|string
 */
function mb_convert_case($s, $mode, $enc = INF)
{
  return Mbstring::mb_convert_case($s, $mode, $enc);
}

/**
 * @param $enc
 *
 * @return bool|string
 */
function mb_internal_encoding($enc = INF)
{
  return Mbstring::mb_internal_encoding($enc);
}

/**
 * @param $lang
 *
 * @return bool|string
 */
function mb_language($lang = INF)
{
  return Mbstring::mb_language($lang);
}

/**
 * @return array
 */
function mb_list_encodings()
{
  return Mbstring::mb_list_encodings();
}

/**
 * @param $encoding
 *
 * @return array|bool
 */
function mb_encoding_aliases($encoding)
{
  return Mbstring::mb_encoding_aliases($encoding);
}

/**
 * @param $var
 * @param $encoding
 *
 * @return bool
 */
function mb_check_encoding($var = INF, $encoding = INF)
{
  return Mbstring::mb_check_encoding($var, $encoding);
}

/**
 * @param      $str
 * @param      $encoding_list
 * @param bool $strict
 *
 * @return bool
 */
function mb_detect_encoding($str, $encoding_list = INF, $strict = false)
{
  return Mbstring::mb_detect_encoding($str, $encoding_list, $strict);
}

/**
 * @param $encoding_list
 *
 * @return bool
 */
function mb_detect_order($encoding_list = INF)
{
  return Mbstring::mb_detect_order($encoding_list);
}

/**
 * @param       $s
 * @param array $result
 */
function mb_parse_str($s, &$result = array())
{
  parse_str($s, $result);
}

/**
 * @param $s
 * @param $enc
 *
 * @return bool|int
 */
function mb_strlen($s, $enc = INF)
{
  return Mbstring::mb_strlen($s, $enc);
}

/**
 * @param     $s
 * @param     $needle
 * @param int $offset
 * @param     $enc
 *
 * @return bool|int
 */
function mb_strpos($s, $needle, $offset = 0, $enc = INF)
{
  return Mbstring::mb_strpos($s, $needle, $offset, $enc);
}

/**
 * @param $s
 * @param $enc
 *
 * @return bool|mixed|string
 */
function mb_strtolower($s, $enc = INF)
{
  return Mbstring::mb_strtolower($s, $enc);
}

/**
 * @param $s
 * @param $enc
 *
 * @return bool|mixed|string
 */
function mb_strtoupper($s, $enc = INF)
{
  return Mbstring::mb_strtoupper($s, $enc);
}

/**
 * @param $char
 *
 * @return bool|string
 */
function mb_substitute_character($char = INF)
{
  return Mbstring::mb_substitute_character($char);
}

/**
 * @param     $s
 * @param     $start
 * @param int $length
 * @param     $enc
 *
 * @return string
 */
function mb_substr($s, $start, $length = 2147483647, $enc = INF)
{
  return Mbstring::mb_substr($s, $start, $length, $enc);
}

/**
 * @param     $s
 * @param     $needle
 * @param int $offset
 * @param     $enc
 *
 * @return bool|int
 */
function mb_stripos($s, $needle, $offset = 0, $enc = INF)
{
  return Mbstring::mb_stripos($s, $needle, $offset, $enc);
}

/**
 * @param      $s
 * @param      $needle
 * @param bool $part
 * @param      $enc
 *
 * @return bool|string
 */
function mb_stristr($s, $needle, $part = false, $enc = INF)
{
  return Mbstring::mb_stristr($s, $needle, $part, $enc);
}

/**
 * @param      $s
 * @param      $needle
 * @param bool $part
 * @param      $enc
 *
 * @return bool|string
 */
function mb_strrchr($s, $needle, $part = false, $enc = INF)
{
  return Mbstring::mb_strrchr($s, $needle, $part, $enc);
}

/**
 * @param      $s
 * @param      $needle
 * @param bool $part
 * @param      $enc
 *
 * @return bool|string
 */
function mb_strrichr($s, $needle, $part = false, $enc = INF)
{
  return Mbstring::mb_strrichr($s, $needle, $part, $enc);
}

/**
 * @param     $s
 * @param     $needle
 * @param int $offset
 * @param     $enc
 *
 * @return bool|int
 */
function mb_strripos($s, $needle, $offset = 0, $enc = INF)
{
  return Mbstring::mb_strripos($s, $needle, $offset, $enc);
}

/**
 * @param     $s
 * @param     $needle
 * @param int $offset
 * @param     $enc
 *
 * @return bool|int
 */
function mb_strrpos($s, $needle, $offset = 0, $enc = INF)
{
  return Mbstring::mb_strrpos($s, $needle, $offset, $enc);
}

/**
 * @param      $s
 * @param      $needle
 * @param bool $part
 * @param      $enc
 *
 * @return bool|string
 */
function mb_strstr($s, $needle, $part = false, $enc = INF)
{
  return Mbstring::mb_strstr($s, $needle, $part, $enc);
}

/**
 * @param string $type
 *
 * @return array|bool
 */
function mb_get_info($type = 'all')
{
  return Mbstring::mb_get_info($type);
}

/**
 * @param $enc
 *
 * @return bool|string
 */
function mb_http_output($enc = INF)
{
  return Mbstring::mb_http_output($enc);
}

/**
 * @param $s
 * @param $enc
 *
 * @return int
 */
function mb_strwidth($s, $enc = INF)
{
  return Mbstring::mb_strwidth($s, $enc);
}

/**
 * @param $haystack
 * @param $needle
 * @param $enc
 *
 * @return int
 */
function mb_substr_count($haystack, $needle, $enc = INF)
{
  return Mbstring::mb_substr_count($haystack, $needle, $enc);
}

/**
 * @param $contents
 * @param $status
 *
 * @return mixed
 */
function mb_output_handler($contents, $status)
{
  return Mbstring::mb_output_handler($contents, $status);
}

/**
 * @param string $type
 *
 * @return bool
 */
function mb_http_input($type = '')
{
  return Mbstring::mb_http_input($type);
}