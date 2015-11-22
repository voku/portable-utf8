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

/** @noinspection PhpUsageOfSilenceOperatorInspection */
@trigger_error('You are using a fallback implementation of the mbstring extension. Installing the native one is highly recommended instead. | http://php.net/manual/en/mbstring.installation.php', E_USER_DEPRECATED);

/**
 * @param string $str
 * @param string $to
 * @param string $from
 *
 * @return string|false
 */
function mb_convert_encoding($str, $to, $from = INF)
{
  return Mbstring::mb_convert_encoding($str, $to, $from);
}

/**
 * @param string $str
 *
 * @return bool|string
 */
function mb_decode_mimeheader($str)
{
  return Mbstring::mb_decode_mimeheader($str);
}

/**
 * @param $str
 * @param $charset
 * @param $transfer_enc
 * @param $lf
 * @param $indent
 */
function mb_encode_mimeheader($str, $charset = INF, $transfer_enc = INF, $lf = INF, $indent = INF)
{
  Mbstring::mb_encode_mimeheader($str, $charset, $transfer_enc, $lf, $indent);
}

/**
 * @param string $str
 * @param int    $mode
 * @param string $encoding
 *
 * @return bool|mixed|string
 */
function mb_convert_case($str, $mode, $encoding = INF)
{
  return Mbstring::mb_convert_case($str, $mode, $encoding);
}

/**
 * @param string $encoding
 *
 * @return bool|string
 */
function mb_internal_encoding($encoding = INF)
{
  return Mbstring::mb_internal_encoding($encoding);
}

/**
 * @param string $lang
 *
 * @return bool|string
 */
function mb_language($lang = INF)
{
  return Mbstring::mb_language($lang);
}

/**
 * @return string[]
 */
function mb_list_encodings()
{
  return Mbstring::mb_list_encodings();
}

/**
 * @param string $encoding
 *
 * @return array|bool
 */
function mb_encoding_aliases($encoding)
{
  return Mbstring::mb_encoding_aliases($encoding);
}

/**
 * @param string $var
 * @param string $encoding
 *
 * @return bool
 */
function mb_check_encoding($var = INF, $encoding = INF)
{
  return Mbstring::mb_check_encoding($var, $encoding);
}

/**
 * @param string       $str
 * @param string[] $encoding_list
 * @param bool         $strict
 *
 * @return bool
 */
function mb_detect_encoding($str, $encoding_list = INF, $strict = false)
{
  return Mbstring::mb_detect_encoding($str, $encoding_list, $strict);
}

/**
 * @param string|array $encoding_list
 *
 * @return bool
 */
function mb_detect_order($encoding_list = INF)
{
  return Mbstring::mb_detect_order($encoding_list);
}

/**
 * @param string $str
 * @param array  $result
 */
function mb_parse_str($str, &$result = array())
{
  parse_str($str, $result);
}

/**
 * @param string $str
 * @param string $encoding
 *
 * @return bool|int
 */
function mb_strlen($str, $encoding = INF)
{
  return Mbstring::mb_strlen($str, $encoding);
}

/**
 * @param string $haystack
 * @param string $needle
 * @param int    $offset
 * @param string $encoding
 *
 * @return bool|int
 */
function mb_strpos($haystack, $needle, $offset = 0, $encoding = INF)
{
  return Mbstring::mb_strpos($haystack, $needle, $offset, $encoding);
}

/**
 * @param string $str
 * @param string $enc
 *
 * @return bool|mixed|string
 */
function mb_strtolower($str, $enc = INF)
{
  return Mbstring::mb_strtolower($str, $enc);
}

/**
 * @param string $str
 * @param string $enc
 *
 * @return bool|mixed|string
 */
function mb_strtoupper($str, $enc = INF)
{
  return Mbstring::mb_strtoupper($str, $enc);
}

/**
 * @param string $char
 *
 * @return false|string
 */
function mb_substitute_character($char = INF)
{
  return Mbstring::mb_substitute_character($char);
}

/**
 * @param string   $str
 * @param int      $start
 * @param integer $length
 * @param string   $enc
 *
 * @return string
 */
function mb_substr($str, $start, $length = 2147483647, $enc = INF)
{
  return Mbstring::mb_substr($str, $start, $length, $enc);
}

/**
 * @param string $haystack
 * @param string $needle
 * @param int    $offset
 * @param string $encoding
 *
 * @return string
 */
function mb_stripos($haystack, $needle, $offset = 0, $encoding = INF)
{
  return Mbstring::mb_stripos($haystack, $needle, $offset, $encoding);
}

/**
 * @param string $haystack
 * @param string $needle
 * @param bool   $part
 * @param string $encoding
 *
 * @return false|string
 */
function mb_stristr($haystack, $needle, $part = false, $encoding = INF)
{
  return Mbstring::mb_stristr($haystack, $needle, $part, $encoding);
}

/**
 * @param string $haystack
 * @param string $needle
 * @param bool   $part
 * @param string $encoding
 *
 * @return false|string
 */
function mb_strrchr($haystack, $needle, $part = false, $encoding = INF)
{
  return Mbstring::mb_strrchr($haystack, $needle, $part, $encoding);
}

/**
 * @param string $haystack
 * @param string $needle
 * @param bool   $part
 * @param string $encoding
 *
 * @return false|string
 */
function mb_strrichr($haystack, $needle, $part = false, $encoding = INF)
{
  return Mbstring::mb_strrichr($haystack, $needle, $part, $encoding);
}

/**
 * @param string $haystack
 * @param string $needle
 * @param int    $offset
 * @param string $encoding
 *
 * @return string
 */
function mb_strripos($haystack, $needle, $offset = 0, $encoding = INF)
{
  return Mbstring::mb_strripos($haystack, $needle, $offset, $encoding);
}

/**
 * @param string $haystack
 * @param string $needle
 * @param int    $offset
 * @param string $encoding
 *
 * @return bool|int
 */
function mb_strrpos($haystack, $needle, $offset = 0, $encoding = INF)
{
  return Mbstring::mb_strrpos($haystack, $needle, $offset, $encoding);
}

/**
 * @param string $haystack
 * @param string $needle
 * @param bool   $part
 * @param string $encoding
 *
 * @return false|string
 */
function mb_strstr($haystack, $needle, $part = false, $encoding = INF)
{
  return Mbstring::mb_strstr($haystack, $needle, $part, $encoding);
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
 * @param string $enc
 *
 * @return bool|string
 */
function mb_http_output($enc = INF)
{
  return Mbstring::mb_http_output($enc);
}

/**
 * @param string $str
 * @param string $encoding
 *
 * @return int
 */
function mb_strwidth($str, $encoding = INF)
{
  return Mbstring::mb_strwidth($str, $encoding);
}

/**
 * @param string $haystack
 * @param string $needle
 * @param string $encoding
 *
 * @return int
 */
function mb_substr_count($haystack, $needle, $encoding = INF)
{
  return Mbstring::mb_substr_count($haystack, $needle, $encoding);
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