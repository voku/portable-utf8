<?php

namespace voku\helper;

/**
 * UTF8-Helper-Class
 *
 * used all functions from "http://pageconfig.com/post/portable-utf8" and added changed from Dethnull@github
 *
 * @author      Hamid Sarfraz
 * @author      Lars Moelleken
 *
 * @package     voku\helper
 */
class UTF8
{
  private static $support = array();

  public function __construct()
  {
  }

  /**
   * check for UTF8-Support
   */
  static public function checkForSupport()
  {
    if (count(self::$support) === 0) {
      self::$support['mbstring'] = self::mbstring_loaded();
      self::$support['iconv'] = self::iconv_loaded();
      self::$support['pcre_utf8'] = self::pcre_utf8_support();

      // DEBUG
      //dump(self::$support, false);
    }
  }

  /**
   * checks whether mbstring is available on the server
   *
   * @return   bool True if available, False otherwise
   */
  static public function mbstring_loaded()
  {
    return extension_loaded('mbstring') ? true : false;
  }

  /**
   * checks whether iconv is available on the server
   *
   * @return   bool True if available, False otherwise
   */
  static public function iconv_loaded()
  {
    return extension_loaded('iconv') ? true : false;
  }

  /**
   * checks if \u modifier is available that enables Unicode support in PCRE.
   *
   * @return   bool True if support is available, false otherwise
   */
  static public function pcre_utf8_support()
  {
    return @preg_match('//u', '');
  }

  /**
   * UTF-8 version of htmlentities()
   *
   * Convert all applicable characters to HTML entities
   *
   * @link http://php.net/manual/en/function.htmlentities.php
   *
   * @param string $string        <p>
   *                              The input string.
   *                              </p>
   * @param int    $flags         [optional] <p>
   *                              A bitmask of one or more of the following flags, which specify how to handle quotes,
   *                              invalid code unit sequences and the used document type. The default is
   *                              ENT_COMPAT | ENT_HTML401.
   *                              <table>
   *                              Available <i>flags</i> constants
   *                              <tr valign="top">
   *                              <td>Constant Name</td>
   *                              <td>Description</td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_COMPAT</b></td>
   *                              <td>Will convert double-quotes and leave single-quotes alone.</td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_QUOTES</b></td>
   *                              <td>Will convert both double and single quotes.</td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_NOQUOTES</b></td>
   *                              <td>Will leave both double and single quotes unconverted.</td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_IGNORE</b></td>
   *                              <td>
   *                              Silently discard invalid code unit sequences instead of returning
   *                              an empty string. Using this flag is discouraged as it
   *                              may have security implications.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_SUBSTITUTE</b></td>
   *                              <td>
   *                              Replace invalid code unit sequences with a Unicode Replacement Character
   *                              U+FFFD (UTF-8) or &#38;#38;#FFFD; (otherwise) instead of returning an empty string.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_DISALLOWED</b></td>
   *                              <td>
   *                              Replace invalid code points for the given document type with a
   *                              Unicode Replacement Character U+FFFD (UTF-8) or &#38;#38;#FFFD;
   *                              (otherwise) instead of leaving them as is. This may be useful, for
   *                              instance, to ensure the well-formedness of XML documents with
   *                              embedded external content.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_HTML401</b></td>
   *                              <td>
   *                              Handle code as HTML 4.01.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_XML1</b></td>
   *                              <td>
   *                              Handle code as XML 1.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_XHTML</b></td>
   *                              <td>
   *                              Handle code as XHTML.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_HTML5</b></td>
   *                              <td>
   *                              Handle code as HTML 5.
   *                              </td>
   *                              </tr>
   *                              </table>
   *                              </p>
   * @param string $encoding      [optional] <p>
   *                              Like <b>htmlspecialchars</b>,
   *                              <b>htmlentities</b> takes an optional third argument
   *                              <i>encoding</i> which defines encoding used in
   *                              conversion.
   *                              Although this argument is technically optional, you are highly
   *                              encouraged to specify the correct value for your code.
   *                              </p>
   * @param bool   $double_encode [optional] <p>
   *                              When <i>double_encode</i> is turned off PHP will not
   *                              encode existing html entities. The default is to convert everything.
   *                              </p>
   *
   * @return string the encoded string.
   * </p>
   * <p>
   * If the input <i>string</i> contains an invalid code unit
   * sequence within the given <i>encoding</i> an empty string
   * will be returned, unless either the <b>ENT_IGNORE</b> or
   * <b>ENT_SUBSTITUTE</b> flags are set.
   */
  static public function htmlentities($string, $flags = ENT_COMPAT, $encoding = 'UTF-8', $double_encode = true)
  {
    return htmlentities($string, $flags, $encoding, $double_encode);
  }

  /**
   * UTF-8 version of html_entity_decode()
   *
   * Convert all HTML entities to their applicable characters
   *
   * @link http://php.net/manual/en/function.html-entity-decode.php
   *
   * @param string $string   <p>
   *                         The input string.
   *                         </p>
   * @param int    $flags    [optional] <p>
   *                         A bitmask of one or more of the following flags, which specify how to handle quotes and
   *                         which document type to use. The default is ENT_COMPAT | ENT_HTML401.
   *                         <table>
   *                         Available <i>flags</i> constants
   *                         <tr valign="top">
   *                         <td>Constant Name</td>
   *                         <td>Description</td>
   *                         </tr>
   *                         <tr valign="top">
   *                         <td><b>ENT_COMPAT</b></td>
   *                         <td>Will convert double-quotes and leave single-quotes alone.</td>
   *                         </tr>
   *                         <tr valign="top">
   *                         <td><b>ENT_QUOTES</b></td>
   *                         <td>Will convert both double and single quotes.</td>
   *                         </tr>
   *                         <tr valign="top">
   *                         <td><b>ENT_NOQUOTES</b></td>
   *                         <td>Will leave both double and single quotes unconverted.</td>
   *                         </tr>
   *                         <tr valign="top">
   *                         <td><b>ENT_HTML401</b></td>
   *                         <td>
   *                         Handle code as HTML 4.01.
   *                         </td>
   *                         </tr>
   *                         <tr valign="top">
   *                         <td><b>ENT_XML1</b></td>
   *                         <td>
   *                         Handle code as XML 1.
   *                         </td>
   *                         </tr>
   *                         <tr valign="top">
   *                         <td><b>ENT_XHTML</b></td>
   *                         <td>
   *                         Handle code as XHTML.
   *                         </td>
   *                         </tr>
   *                         <tr valign="top">
   *                         <td><b>ENT_HTML5</b></td>
   *                         <td>
   *                         Handle code as HTML 5.
   *                         </td>
   *                         </tr>
   *                         </table>
   *                         </p>
   * @param string $encoding [optional] <p>
   *                         Encoding to use.
   *                         </p>
   *
   * @return string the decoded string.
   */
  static public function html_entity_decode($string, $flags = ENT_COMPAT, $encoding = 'UTF-8')
  {
    return html_entity_decode($string, $flags, $encoding);
  }

  /**
   * UTF-8 version of htmlspecialchars()
   *
   * Convert special characters to HTML entities
   *
   * @link http://php.net/manual/en/function.htmlspecialchars.php
   *
   * @param string $string        <p>
   *                              The string being converted.
   *                              </p>
   * @param int    $flags         [optional] <p>
   *                              A bitmask of one or more of the following flags, which specify how to handle quotes,
   *                              invalid code unit sequences and the used document type. The default is
   *                              ENT_COMPAT | ENT_HTML401.
   *                              <table>
   *                              Available <i>flags</i> constants
   *                              <tr valign="top">
   *                              <td>Constant Name</td>
   *                              <td>Description</td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_COMPAT</b></td>
   *                              <td>Will convert double-quotes and leave single-quotes alone.</td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_QUOTES</b></td>
   *                              <td>Will convert both double and single quotes.</td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_NOQUOTES</b></td>
   *                              <td>Will leave both double and single quotes unconverted.</td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_IGNORE</b></td>
   *                              <td>
   *                              Silently discard invalid code unit sequences instead of returning
   *                              an empty string. Using this flag is discouraged as it
   *                              may have security implications.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_SUBSTITUTE</b></td>
   *                              <td>
   *                              Replace invalid code unit sequences with a Unicode Replacement Character
   *                              U+FFFD (UTF-8) or &#38;#38;#FFFD; (otherwise) instead of returning an empty string.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_DISALLOWED</b></td>
   *                              <td>
   *                              Replace invalid code points for the given document type with a
   *                              Unicode Replacement Character U+FFFD (UTF-8) or &#38;#38;#FFFD;
   *                              (otherwise) instead of leaving them as is. This may be useful, for
   *                              instance, to ensure the well-formedness of XML documents with
   *                              embedded external content.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_HTML401</b></td>
   *                              <td>
   *                              Handle code as HTML 4.01.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_XML1</b></td>
   *                              <td>
   *                              Handle code as XML 1.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_XHTML</b></td>
   *                              <td>
   *                              Handle code as XHTML.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_HTML5</b></td>
   *                              <td>
   *                              Handle code as HTML 5.
   *                              </td>
   *                              </tr>
   *                              </table>
   *                              </p>
   * @param string $encoding      [optional] <p>
   *                              Defines encoding used in conversion.
   *                              </p>
   *                              <p>
   *                              For the purposes of this function, the encodings
   *                              ISO-8859-1, ISO-8859-15,
   *                              UTF-8, cp866,
   *                              cp1251, cp1252, and
   *                              KOI8-R are effectively equivalent, provided the
   *                              <i>string</i> itself is valid for the encoding, as
   *                              the characters affected by <b>htmlspecialchars</b> occupy
   *                              the same positions in all of these encodings.
   *                              </p>
   * @param bool   $double_encode [optional] <p>
   *                              When <i>double_encode</i> is turned off PHP will not
   *                              encode existing html entities, the default is to convert everything.
   *                              </p>
   *
   * @return string The converted string.
   * </p>
   * <p>
   * If the input <i>string</i> contains an invalid code unit
   * sequence within the given <i>encoding</i> an empty string
   * will be returned, unless either the <b>ENT_IGNORE</b> or
   * <b>ENT_SUBSTITUTE</b> flags are set.
   */
  static public function htmlspecialchars($string, $flags = ENT_COMPAT, $encoding = 'UTF-8', $double_encode = true)
  {
    return htmlspecialchars($string, $flags, $encoding, $double_encode);
  }

  /**
   * checks whether the passed string contains only byte sequances that
   * appear valid UTF-8 characters.
   *
   * @since 1.0
   *
   * @param    string $str The string to be checked
   *
   * @return   bool True if the check succeeds, False Otherwise
   */
  static public function is_utf8($str)
  {
    self::checkForSupport();

    if (self::$support['pcre_utf8'] === true) {
      return ( bool )preg_match('//u', $str);
    }

    if (self::$support['mbstring'] === true) {
      return mb_check_encoding($str, 'UTF-8');
    }

    // fallback

    $len = strlen($str);

    for ($i = 0; $i < $len; $i++) {
      if (($str[$i] & "\x80") === "\x00") {
        continue;
      } else if ((($str[$i] & "\xE0") === "\xC0") && (isset($str[$i + 1]))) {
        if (($str[$i + 1] & "\xC0") === "\x80") {
          $i++;
          continue;
        }

        return false;
      } else if ((($str[$i] & "\xF0") === "\xE0") && (isset($str[$i + 2]))) {
        if ((($str[$i + 1] & "\xC0") === "\x80") && (($str[$i + 2] & "\xC0") === "\x80")) {
          $i = $i + 2;
          continue;
        }

        return false;
      } else if ((($str[$i] & "\xF8") === "\xF0") && (isset($str[$i + 3]))) {
        if ((($str[$i + 1] & "\xC0") === "\x80") && (($str[$i + 2] & "\xC0") === "\x80") && (($str[$i + 3] & "\xC0") === "\x80")) {
          $i = $i + 3;
          continue;
        }

        return false;
      } else {
        return false;
      }
    }

    return true;
  }

  /**
   * checks if the number of Unicode characters in a string are not
   * more than the specified integer.
   *
   * @param    string $str      The original string to be checked.
   * @param    int    $box_size The size in number of chars to be checked against string.
   *
   * @return   bool true if string is less than or equal to $box_size The
   *           false otherwise
   */
  static public function fits_inside($str, $box_size)
  {
    return (self::strlen($str) <= $box_size);
  }

  /**
   * Get string length
   *
   * @link     http://php.net/manual/en/function.mb-strlen.php
   *
   * @param $string
   *
   * @internal param string $str <p>
   *           The string being checked for length.
   *           </p>
   * @return int the number of characters in
   *           string str having character encoding
   *           encoding. A multi-byte character is
   *           counted as 1.
   */
  static public function strlen($string)
  {
    self::checkForSupport();

    if (self::$support['mbstring'] === true) {
      $str = self::clean($string);

      return mb_strlen($str, 'UTF-8');
    }

    if (self::$support['iconv'] === true) {
      $str = self::clean($string);

      return iconv_strlen($str, 'UTF-8');
    }

    //PCRE \X is buggy in many recent versions of PHP
    //See the original post.

    //if( self::$support['pcre_utf8'] === true )
    //{
    //	$str	= self::clean( $str );
    //
    //	preg_match_all( '/\X/u' , $str , $matches  );
    //
    //	return count( $matches[0] );
    //}

    return count(self::split($string));
  }

  /**
   * accepts a string and removes all non-UTF-8 characters from it.
   *
   * @param    string  $str The string to be sanitized.
   * @param    Boolean $remove_bom
   *
   * @return   string Clean UTF-8 encoded string
   */
  static public function clean($str, $remove_bom = false)
  {
    // http://stackoverflow.com/questions/1401317/remove-non-utf8-characters-from-string
    // caused connection reset problem on larger strings
    // $regx = '/((?:[\x00-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3}){1,})|./';

    $regx = '/([\x00-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3})|./s';

    $str = preg_replace($regx, '$1', $str);

    if ($remove_bom) {
      $str = self::removeBOM($str);
    }

    return $str;
  }

  /**
   * removing UTF-8 BOM
   *
   * @param String $string
   *
   * @return String
   */
  public static function removeBOM($string = "")
  {
    if (substr($string, 0, 3) == pack("CCC", 0xef, 0xbb, 0xbf)) {
      $string = substr($string, 3);
    }

    return $string;
  }

  /**
   * Get part of string
   *
   * @link http://php.net/manual/en/function.mb-substr.php
   *
   * @param string $str    <p>
   *                       The string being checked.
   *                       </p>
   * @param int    $start  <p>
   *                       The first position used in str.
   *                       </p>
   * @param int    $length [optional] <p>
   *                       The maximum length of the returned string.
   *                       </p>
   *
   * @return string mb_substr returns the portion of
   * str specified by the
   * start and
   * length parameters.
   */
  static public function substr($str, $start = 0, $length = null)
  {
    self::checkForSupport();

    //iconv and mbstring are not tolerant to invalid encoding
    //further, their behaviour is inconsistent with that of PHP's substr

    if (self::$support['iconv'] === true) {
      $str = self::clean($str);

      if ($length === null) {
        $length = PHP_INT_MAX;
      }

      return iconv_substr($str, $start, $length, 'UTF-8');
    }

    if (self::$support['mbstring'] === true) {
      $str = self::clean($str);

      if ($length === null) {
        $length = PHP_INT_MAX;
      }

      return mb_substr($str, $start, $length, 'UTF-8');
    }

    //Fallback

    //Split to array, and remove invalid characters
    $array = self::split($str);

    //Extract relevant part, and join to make sting again
    return implode(array_slice($array, $start, $length));
  }

  /**
   * convert a string to an array of Unicode characters.
   *
   * @param    string $str          The string to split into array.
   * @param    int    $split_length Max character length of each array element
   *
   * @return   array An array containing chunks of the string
   */
  static public function split($str, $split_length = 1)
  {
    self::checkForSupport();

    $str = ( string )$str;

    $ret = array();

    if (self::$support['pcre_utf8'] === true) {
      $str = self::clean($str);

      //	http://stackoverflow.com/a/8780076/369005
      $ret = preg_split('/(?<!^)(?!$)/u', $str);

      // \X is buggy in many recent versions of PHP
      //preg_match_all( '/\X/u' , $str , $ret );
      //$ret	= $ret[0];
    } else {
      //Fallback

      $len = strlen($str);

      for ($i = 0; $i < $len; $i++) {
        if (($str[$i] & "\x80") === "\x00") {
          $ret[] = $str[$i];
        } else if ((($str[$i] & "\xE0") === "\xC0") && (isset($str[$i + 1]))) {
          if (($str[$i + 1] & "\xC0") === "\x80") {
            $ret[] = $str[$i] . $str[$i + 1];

            $i++;
          }
        } else if ((($str[$i] & "\xF0") === "\xE0") && (isset($str[$i + 2]))) {
          if ((($str[$i + 1] & "\xC0") === "\x80") && (($str[$i + 2] & "\xC0") === "\x80")) {
            $ret[] = $str[$i] . $str[$i + 1] . $str[$i + 2];

            $i = $i + 2;
          }
        } else if ((($str[$i] & "\xF8") === "\xF0") && (isset($str[$i + 3]))) {
          if ((($str[$i + 1] & "\xC0") === "\x80") && (($str[$i + 2] & "\xC0") === "\x80") && (($str[$i + 3] & "\xC0") === "\x80")) {
            $ret[] = $str[$i] . $str[$i + 1] . $str[$i + 2] . $str[$i + 3];

            $i = $i + 3;
          }
        }
      }
    }


    if ($split_length > 1) {
      $ret = array_chunk($ret, $split_length);

      $ret = array_map('implode', $ret);
    }

    if ($ret[0] === '') {
      return array();
    }

    return $ret;
  }

  /**
   * calculates and returns the maximum number of bytes taken by any
   * UTF-8 encoded character in the given string
   *
   * @param    string $str The original Unicode string
   *
   * @return   array An array of byte lengths of each character.
   */
  static public function max_chr_width($str)
  {
    return max(self::chr_size_list($str));
  }

  /**
   * generates an array of byte length of each character of a Unicode string.
   *
   * @param    string $str The original Unicode string
   *
   * @return   array An array of byte lengths of each character.
   */

  static public function chr_size_list($str)
  {
    return array_map('strlen', self::split($str));
  }

  /**
   * converts a UTF-8 character to HTML Numbered Entity like &#123;
   *
   * @param    string $chr The Unicode character to be encoded as numbered entity
   *
   * @return   string HTML numbered entity
   */
  static public function single_chr_html_encode($chr)
  {
    return '&#' . self::ord($chr) . ';';
  }

  /**
   * calculates Unicode Code Point of the given UTF-8 encoded character
   *
   * @param    string $chr The character of which to calculate Code Point
   *
   * @return   int Unicode Code Point of the given character
   *           0 on invalid UTF-8 byte sequence
   */
  static public function ord($chr)
  {
    $chr = self::split($chr);

    $chr = $chr[0];

    switch (strlen($chr)) {
      case 1:
        return
            ord($chr);

      case 2:
        return
            ((ord($chr[0]) & 0x1F) << 6)
            | (ord($chr[1]) & 0x3F);

      case 3:
        return
            ((ord($chr[0]) & 0x0F) << 12)
            | ((ord($chr[1]) & 0x3F) << 6)
            | (ord($chr[2]) & 0x3F);

      case 4:
        return
            ((ord($chr[0]) & 0x07) << 18)
            | ((ord($chr[1]) & 0x3F) << 12)
            | ((ord($chr[2]) & 0x3F) << 6)
            | (ord($chr[3]) & 0x3F);
    }

    return 0;
  }

  /**
   * converts a UTF-8 string to a series of
   *
   * INFO: HTML Numbered Entities like &#123;&#39;&#1740;...
   *
   * @param    string $str The Unicode string to be encoded as numbered entities
   *
   * @return   string HTML numbered entities
   */
  static public function html_encode($str)
  {
    return implode(
        array_map(
            array(
                'UTF8',
                'single_chr_html_encode'
            ), self::split($str)
        )
    );
  }

  /**
   * checks if a file starts with BOM character
   *
   * @param    string $file_path Path to a valid file
   *
   * @return   bool True if the file has BOM at the start, False otherwise
   */
  static public function file_has_bom($file_path)
  {
    return self::is_bom(file_get_contents($file_path, 0, null, -1, 3));
  }

  /**
   * checks if the given string is a Byte Order Mark
   *
   * @param    string $utf8_chr The input string
   *
   * @return   bool True if the $utf8_chr is Byte Order Mark, False otherwise
   */
  static public function is_bom($utf8_chr)
  {
    return ($utf8_chr === self::bom());
  }

  /**
   * returns the Byte Order Mark Character
   *
   * @return   string Byte Order Mark
   */
  static public function bom()
  {
    return "\xef\xbb\xbf";
  }

  /**
   * checks if string starts with BOM character
   *
   * @param    string $str The input string
   *
   * @return   bool True if the string has BOM at the start, False otherwise
   */
  static public function string_has_bom($str)
  {
    return self::is_bom(substr($str, 0, 3));
  }

  /**
   * prepends BOM character to the string and returns the whole string.
   *
   * INFO: If BOM already existed there, the Input string is returned.
   *
   * @param    string $str The input string
   *
   * @return   string The output string that contains BOM
   */
  static public function add_bom_to_string($str)
  {
    if (!self::is_bom(substr($str, 0, 3))) {
      $str = self::bom() . $str;
    }

    return $str;
  }

  /**
   * shuffles all the characters in the string.
   *
   * @param    string $str The input string
   *
   * @return   string The shuffled string
   */

  static public function str_shuffle($str)
  {
    $array = self::split($str);

    shuffle($array);

    return implode('', $array);
  }

  /**
   * returns count of characters used in a string
   *
   * @param    string $str The input string
   *
   * @return   array An associative array of Character as keys and
   *           their count as values
   */
  static public function count_chars($str) //there is no $mode parameters
  {
    $array = array_count_values(self::split($str));

    ksort($array);

    return $array;
  }

  /**
   * reverses characters order in the string
   *
   * @param    string $str The input string
   *
   * @return   string The string with characters in the reverse sequence
   */
  static public function strrev($str)
  {
    return implode(array_reverse(self::split($str)));
  }

  /**
   * returns the UTF-8 character with the maximum code point in the given data
   *
   * @param    mixed $arg A UTF-8 encoded string or an array of such strings
   *
   * @return   string The character with the highest code point than others
   */
  static public function max($arg)
  {
    if (is_array($arg)) {
      $arg = implode($arg);
    }

    return self::chr(max(self::codepoints($arg)));
  }

  /**
   * generates a UTF-8 encoded character from the given Code Point
   *
   * @param    int $code_point The code point for which to generate a character
   *
   * @return   string Milti-Byte character
   *           returns empty string on failure to encode
   */
  static public function chr($code_point)
  {
    self::checkForSupport();

    if (($i = ( int )$code_point) !== $code_point) {
      //$code_point is a string, lets extract int code point from it

      if (!($i = ( int )self::hex_to_int($code_point))) {
        return '';
      }
    }

    //json not working properly for larger code points
    //See the original post.

    //if( extension_loaded( 'json' ) )
    //{
    //	$hex	= dechex( $i );
    //
    //	return json_decode('"\u'. ( strlen( $hex ) < 4 ? substr( '000' . $hex , -4 ) : $hex ) .'"');
    //}
    //else

    if (self::$support['mbstring'] === true) {
      return mb_convert_encoding("&#$i;", 'UTF-8', 'HTML-ENTITIES');
    } else if (version_compare(phpversion(), '5.0.0') === 1) {
      //html_entity_decode did not support Multi-Byte before PHP 5.0.0
      return html_entity_decode("&#{$i};", ENT_QUOTES, 'UTF-8');
    }

    // fallback

    $bits = ( int )(log($i, 2) + 1);

    if ($bits <= 7) //Single Byte
    {
      return chr($i);
    } else if ($bits <= 11) //Two Bytes
    {
      return chr((($i >> 6) & 0x1F) | 0xC0) . chr(($i & 0x3F) | 0x80);
    } else if ($bits <= 16) //Three Bytes
    {
      return chr((($i >> 12) & 0x0F) | 0xE0) . chr((($i >> 6) & 0x3F) | 0x80) . chr(($i & 0x3F) | 0x80);
    } else if ($bits <= 21) //Four Bytes
    {
      return chr((($i >> 18) & 0x07) | 0xF0) . chr((($i >> 12) & 0x3F) | 0x80) . chr((($i >> 6) & 0x3F) | 0x80) . chr(($i & 0x3F) | 0x80);
    } else {
      return ''; //Cannot be encoded as Valid UTF-8
    }
  }

  /**
   * converts hexadecimal U+xxxx code point representation to Integer
   *
   * INFO: opposite to UTF8::int_to_hex( )
   *
   * @param    string $str The Hexadecimal Code Point representation
   *
   * @return   int The Code Point, or 0 on failure
   */
  static public function hex_to_int($str)
  {
    if (preg_match('/^(?:\\\u|U\+|)([a-z0-9]{4,6})$/i', $str, $match)) {
      return ( int )hexdec($match[1]);
    }

    return 0;
  }

  /**
   * accepts a string and returns an array of Unicode Code Points
   *
   * @since 1.0
   *
   * @param    mixed $arg     A UTF-8 encoded string or an array of such strings
   * @param    bool  $u_style If True, will return Code Points in U+xxxx format,
   *                          default, Code Points will be returned as integers
   *
   * @return   array The array of code points
   */
  static public function codepoints($arg, $u_style = false)
  {
    if (is_string($arg)) {
      $arg = self::split($arg);
    }

    $arg = array_map(
        array(
            'UTF8',
            'ord'
        ), $arg
    );

    if ($u_style) {
      $arg = array_map(
          array(
              'UTF8',
              'int_to_hex'
          ), $arg
      );
    }

    return $arg;
  }

  /**
   * returns the UTF-8 character with the minimum code point in the given data
   *
   * @param    mixed $arg A UTF-8 encoded string or an array of such strings
   *
   * @return   string The character with the lowest code point than others
   */
  static public function min($arg)
  {
    if (is_array($arg)) {
      $arg = implode($arg);
    }

    return self::chr(min(self::codepoints($arg)));
  }

  /**
   * get hexadecimal code point (U+xxxx) of a UTF-8 encoded character
   *
   * @param    string $chr The input character
   * @param string    $pfix
   *
   * @return   string The Code Point encoded as U+xxxx
   */
  static public function chr_to_hex($chr, $pfix = 'U+')
  {
    return self::int_to_hex(self::ord($chr), $pfix);
  }

  /**
   * converts Integer to hexadecimal U+xxxx code point representation
   *
   * @param    int    $int The integer to be converted to hexadecimal code point
   * @param    string $pfix
   *
   * @return   string The Code Point, or empty string on failure
   */
  static public function int_to_hex($int, $pfix = 'U+')
  {
    if (ctype_digit(( string )$int)) {
      $hex = dechex(( int )$int);

      $hex = (strlen($hex) < 4 ? substr('0000' . $hex, -4) : $hex);

      return $pfix . $hex;
    }

    return '';
  }

  /**
   * counts number of words in the UTF-8 string
   *
   * @param    string $str The input string
   *
   * @return   int The number of words in the string
   */
  static public function word_count($str)
  {
    return count(explode('-', self::url_slug($str)));
  }

  /**
   * creates SEO Friendly URL Slugs with UTF-8 support
   *
   * INFO: Optionally can do transliteration
   *
   * @param    string $str  The text which is to be converted Slug
   * @param    int    $maxl Optional. Sets the maximum number of characters
   *                        to be allowed in the slug. Default is UNLIMITED.
   * @param    bool   $trns
   *
   * @return   string The UTF-8 encoded URL Slug.
   */
  static public function url_slug($str = '', $maxl = -1, $trns = false)
  {
    self::checkForSupport();

    $str = self::clean($str, true); // INFO: true == $remove_bom

    $str = self::strtolower($str);

    if ($trns && self::$support['iconv'] === true) {
      $str = iconv('UTF-8', 'US-ASCII//TRANSLIT//IGNORE', $str);
    }

    if (self::$support['pcre_utf8'] === true) {
      $str = preg_replace('/[^\\p{L}\\p{Nd}\-_]+/u', '-', $str);
    } else {
      $str = preg_replace('/[\>\<\+\?\&\"\'\/\\\:\s\-\#\%\=]+/', '-', $str);
    }

    if ($maxl > 0) {
      $maxl = ( int )$maxl;

      $str = self::substr($str, 0, $maxl);
    }

    $str = trim($str, '_-');

    if (!strlen($str)) {
      $str = substr(md5(microtime(true)), 0, ($maxl == -1 ? 32 : $maxl));
    }

    return $str;
  }

  /**
   * (PHP 4 &gt;= 4.3.0, PHP 5)<br/>
   * Make a string lowercase
   *
   * @link http://php.net/manual/en/function.mb-strtolower.php
   *
   * @param string $str <p>
   *                    The string being lowercased.
   *                    </p>
   *
   * @return string str with all alphabetic characters converted to lowercase.
   */
  static public function strtolower($str)
  {
    self::checkForSupport();

    if (self::$support['mbstring'] === true) {
      return mb_strtolower($str, 'UTF-8');
    } else {

      // fallback
      if (empty($table)) {
        $table = array_flip(self::case_table());
      }

      $str = self::clean($str);

      return strtr($str, $table);
    }
  }

  /**
   * returns an array of all lower and upper case UTF-8 encoded characters
   *
   * @return   array An array with lower case chars as keys and upper chars as values
   */
  function case_table()
  {
    static $case = array(

      //lower => upper
      "\xf0\x90\x91\x8f" => "\xf0\x90\x90\xa7",
      "\xf0\x90\x91\x8e" => "\xf0\x90\x90\xa6",
      "\xf0\x90\x91\x8d" => "\xf0\x90\x90\xa5",
      "\xf0\x90\x91\x8c" => "\xf0\x90\x90\xa4",
      "\xf0\x90\x91\x8b" => "\xf0\x90\x90\xa3",
      "\xf0\x90\x91\x8a" => "\xf0\x90\x90\xa2",
      "\xf0\x90\x91\x89" => "\xf0\x90\x90\xa1",
      "\xf0\x90\x91\x88" => "\xf0\x90\x90\xa0",
      "\xf0\x90\x91\x87" => "\xf0\x90\x90\x9f",
      "\xf0\x90\x91\x86" => "\xf0\x90\x90\x9e",
      "\xf0\x90\x91\x85" => "\xf0\x90\x90\x9d",
      "\xf0\x90\x91\x84" => "\xf0\x90\x90\x9c",
      "\xf0\x90\x91\x83" => "\xf0\x90\x90\x9b",
      "\xf0\x90\x91\x82" => "\xf0\x90\x90\x9a",
      "\xf0\x90\x91\x81" => "\xf0\x90\x90\x99",
      "\xf0\x90\x91\x80" => "\xf0\x90\x90\x98",
      "\xf0\x90\x90\xbf" => "\xf0\x90\x90\x97",
      "\xf0\x90\x90\xbe" => "\xf0\x90\x90\x96",
      "\xf0\x90\x90\xbd" => "\xf0\x90\x90\x95",
      "\xf0\x90\x90\xbc" => "\xf0\x90\x90\x94",
      "\xf0\x90\x90\xbb" => "\xf0\x90\x90\x93",
      "\xf0\x90\x90\xba" => "\xf0\x90\x90\x92",
      "\xf0\x90\x90\xb9" => "\xf0\x90\x90\x91",
      "\xf0\x90\x90\xb8" => "\xf0\x90\x90\x90",
      "\xf0\x90\x90\xb7" => "\xf0\x90\x90\x8f",
      "\xf0\x90\x90\xb6" => "\xf0\x90\x90\x8e",
      "\xf0\x90\x90\xb5" => "\xf0\x90\x90\x8d",
      "\xf0\x90\x90\xb4" => "\xf0\x90\x90\x8c",
      "\xf0\x90\x90\xb3" => "\xf0\x90\x90\x8b",
      "\xf0\x90\x90\xb2" => "\xf0\x90\x90\x8a",
      "\xf0\x90\x90\xb1" => "\xf0\x90\x90\x89",
      "\xf0\x90\x90\xb0" => "\xf0\x90\x90\x88",
      "\xf0\x90\x90\xaf" => "\xf0\x90\x90\x87",
      "\xf0\x90\x90\xae" => "\xf0\x90\x90\x86",
      "\xf0\x90\x90\xad" => "\xf0\x90\x90\x85",
      "\xf0\x90\x90\xac" => "\xf0\x90\x90\x84",
      "\xf0\x90\x90\xab" => "\xf0\x90\x90\x83",
      "\xf0\x90\x90\xaa" => "\xf0\x90\x90\x82",
      "\xf0\x90\x90\xa9" => "\xf0\x90\x90\x81",
      "\xf0\x90\x90\xa8" => "\xf0\x90\x90\x80",
      "\xef\xbd\x9a"     => "\xef\xbc\xba",
      "\xef\xbd\x99"     => "\xef\xbc\xb9",
      "\xef\xbd\x98"     => "\xef\xbc\xb8",
      "\xef\xbd\x97"     => "\xef\xbc\xb7",
      "\xef\xbd\x96"     => "\xef\xbc\xb6",
      "\xef\xbd\x95"     => "\xef\xbc\xb5",
      "\xef\xbd\x94"     => "\xef\xbc\xb4",
      "\xef\xbd\x93"     => "\xef\xbc\xb3",
      "\xef\xbd\x92"     => "\xef\xbc\xb2",
      "\xef\xbd\x91"     => "\xef\xbc\xb1",
      "\xef\xbd\x90"     => "\xef\xbc\xb0",
      "\xef\xbd\x8f"     => "\xef\xbc\xaf",
      "\xef\xbd\x8e"     => "\xef\xbc\xae",
      "\xef\xbd\x8d"     => "\xef\xbc\xad",
      "\xef\xbd\x8c"     => "\xef\xbc\xac",
      "\xef\xbd\x8b"     => "\xef\xbc\xab",
      "\xef\xbd\x8a"     => "\xef\xbc\xaa",
      "\xef\xbd\x89"     => "\xef\xbc\xa9",
      "\xef\xbd\x88"     => "\xef\xbc\xa8",
      "\xef\xbd\x87"     => "\xef\xbc\xa7",
      "\xef\xbd\x86"     => "\xef\xbc\xa6",
      "\xef\xbd\x85"     => "\xef\xbc\xa5",
      "\xef\xbd\x84"     => "\xef\xbc\xa4",
      "\xef\xbd\x83"     => "\xef\xbc\xa3",
      "\xef\xbd\x82"     => "\xef\xbc\xa2",
      "\xef\xbd\x81"     => "\xef\xbc\xa1",
      "\xea\x9e\x8c"     => "\xea\x9e\x8b",
      "\xea\x9e\x87"     => "\xea\x9e\x86",
      "\xea\x9e\x85"     => "\xea\x9e\x84",
      "\xea\x9e\x83"     => "\xea\x9e\x82",
      "\xea\x9e\x81"     => "\xea\x9e\x80",
      "\xea\x9d\xbf"     => "\xea\x9d\xbe",
      "\xea\x9d\xbc"     => "\xea\x9d\xbb",
      "\xea\x9d\xba"     => "\xea\x9d\xb9",
      "\xea\x9d\xaf"     => "\xea\x9d\xae",
      "\xea\x9d\xad"     => "\xea\x9d\xac",
      "\xea\x9d\xab"     => "\xea\x9d\xaa",
      "\xea\x9d\xa9"     => "\xea\x9d\xa8",
      "\xea\x9d\xa7"     => "\xea\x9d\xa6",
      "\xea\x9d\xa5"     => "\xea\x9d\xa4",
      "\xea\x9d\xa3"     => "\xea\x9d\xa2",
      "\xea\x9d\xa1"     => "\xea\x9d\xa0",
      "\xea\x9d\x9f"     => "\xea\x9d\x9e",
      "\xea\x9d\x9d"     => "\xea\x9d\x9c",
      "\xea\x9d\x9b"     => "\xea\x9d\x9a",
      "\xea\x9d\x99"     => "\xea\x9d\x98",
      "\xea\x9d\x97"     => "\xea\x9d\x96",
      "\xea\x9d\x95"     => "\xea\x9d\x94",
      "\xea\x9d\x93"     => "\xea\x9d\x92",
      "\xea\x9d\x91"     => "\xea\x9d\x90",
      "\xea\x9d\x8f"     => "\xea\x9d\x8e",
      "\xea\x9d\x8d"     => "\xea\x9d\x8c",
      "\xea\x9d\x8b"     => "\xea\x9d\x8a",
      "\xea\x9d\x89"     => "\xea\x9d\x88",
      "\xea\x9d\x87"     => "\xea\x9d\x86",
      "\xea\x9d\x85"     => "\xea\x9d\x84",
      "\xea\x9d\x83"     => "\xea\x9d\x82",
      "\xea\x9d\x81"     => "\xea\x9d\x80",
      "\xea\x9c\xbf"     => "\xea\x9c\xbe",
      "\xea\x9c\xbd"     => "\xea\x9c\xbc",
      "\xea\x9c\xbb"     => "\xea\x9c\xba",
      "\xea\x9c\xb9"     => "\xea\x9c\xb8",
      "\xea\x9c\xb7"     => "\xea\x9c\xb6",
      "\xea\x9c\xb5"     => "\xea\x9c\xb4",
      "\xea\x9c\xb3"     => "\xea\x9c\xb2",
      "\xea\x9c\xaf"     => "\xea\x9c\xae",
      "\xea\x9c\xad"     => "\xea\x9c\xac",
      "\xea\x9c\xab"     => "\xea\x9c\xaa",
      "\xea\x9c\xa9"     => "\xea\x9c\xa8",
      "\xea\x9c\xa7"     => "\xea\x9c\xa6",
      "\xea\x9c\xa5"     => "\xea\x9c\xa4",
      "\xea\x9c\xa3"     => "\xea\x9c\xa2",
      "\xea\x9a\x97"     => "\xea\x9a\x96",
      "\xea\x9a\x95"     => "\xea\x9a\x94",
      "\xea\x9a\x93"     => "\xea\x9a\x92",
      "\xea\x9a\x91"     => "\xea\x9a\x90",
      "\xea\x9a\x8f"     => "\xea\x9a\x8e",
      "\xea\x9a\x8d"     => "\xea\x9a\x8c",
      "\xea\x9a\x8b"     => "\xea\x9a\x8a",
      "\xea\x9a\x89"     => "\xea\x9a\x88",
      "\xea\x9a\x87"     => "\xea\x9a\x86",
      "\xea\x9a\x85"     => "\xea\x9a\x84",
      "\xea\x9a\x83"     => "\xea\x9a\x82",
      "\xea\x9a\x81"     => "\xea\x9a\x80",
      "\xea\x99\xad"     => "\xea\x99\xac",
      "\xea\x99\xab"     => "\xea\x99\xaa",
      "\xea\x99\xa9"     => "\xea\x99\xa8",
      "\xea\x99\xa7"     => "\xea\x99\xa6",
      "\xea\x99\xa5"     => "\xea\x99\xa4",
      "\xea\x99\xa3"     => "\xea\x99\xa2",
      "\xea\x99\x9f"     => "\xea\x99\x9e",
      "\xea\x99\x9d"     => "\xea\x99\x9c",
      "\xea\x99\x9b"     => "\xea\x99\x9a",
      "\xea\x99\x99"     => "\xea\x99\x98",
      "\xea\x99\x97"     => "\xea\x99\x96",
      "\xea\x99\x95"     => "\xea\x99\x94",
      "\xea\x99\x93"     => "\xea\x99\x92",
      "\xea\x99\x91"     => "\xea\x99\x90",
      "\xea\x99\x8f"     => "\xea\x99\x8e",
      "\xea\x99\x8d"     => "\xea\x99\x8c",
      "\xea\x99\x8b"     => "\xea\x99\x8a",
      "\xea\x99\x89"     => "\xea\x99\x88",
      "\xea\x99\x87"     => "\xea\x99\x86",
      "\xea\x99\x85"     => "\xea\x99\x84",
      "\xea\x99\x83"     => "\xea\x99\x82",
      "\xea\x99\x81"     => "\xea\x99\x80",
      "\xe2\xb4\xa5"     => "\xe1\x83\x85",
      "\xe2\xb4\xa4"     => "\xe1\x83\x84",
      "\xe2\xb4\xa3"     => "\xe1\x83\x83",
      "\xe2\xb4\xa2"     => "\xe1\x83\x82",
      "\xe2\xb4\xa1"     => "\xe1\x83\x81",
      "\xe2\xb4\xa0"     => "\xe1\x83\x80",
      "\xe2\xb4\x9f"     => "\xe1\x82\xbf",
      "\xe2\xb4\x9e"     => "\xe1\x82\xbe",
      "\xe2\xb4\x9d"     => "\xe1\x82\xbd",
      "\xe2\xb4\x9c"     => "\xe1\x82\xbc",
      "\xe2\xb4\x9b"     => "\xe1\x82\xbb",
      "\xe2\xb4\x9a"     => "\xe1\x82\xba",
      "\xe2\xb4\x99"     => "\xe1\x82\xb9",
      "\xe2\xb4\x98"     => "\xe1\x82\xb8",
      "\xe2\xb4\x97"     => "\xe1\x82\xb7",
      "\xe2\xb4\x96"     => "\xe1\x82\xb6",
      "\xe2\xb4\x95"     => "\xe1\x82\xb5",
      "\xe2\xb4\x94"     => "\xe1\x82\xb4",
      "\xe2\xb4\x93"     => "\xe1\x82\xb3",
      "\xe2\xb4\x92"     => "\xe1\x82\xb2",
      "\xe2\xb4\x91"     => "\xe1\x82\xb1",
      "\xe2\xb4\x90"     => "\xe1\x82\xb0",
      "\xe2\xb4\x8f"     => "\xe1\x82\xaf",
      "\xe2\xb4\x8e"     => "\xe1\x82\xae",
      "\xe2\xb4\x8d"     => "\xe1\x82\xad",
      "\xe2\xb4\x8c"     => "\xe1\x82\xac",
      "\xe2\xb4\x8b"     => "\xe1\x82\xab",
      "\xe2\xb4\x8a"     => "\xe1\x82\xaa",
      "\xe2\xb4\x89"     => "\xe1\x82\xa9",
      "\xe2\xb4\x88"     => "\xe1\x82\xa8",
      "\xe2\xb4\x87"     => "\xe1\x82\xa7",
      "\xe2\xb4\x86"     => "\xe1\x82\xa6",
      "\xe2\xb4\x85"     => "\xe1\x82\xa5",
      "\xe2\xb4\x84"     => "\xe1\x82\xa4",
      "\xe2\xb4\x83"     => "\xe1\x82\xa3",
      "\xe2\xb4\x82"     => "\xe1\x82\xa2",
      "\xe2\xb4\x81"     => "\xe1\x82\xa1",
      "\xe2\xb4\x80"     => "\xe1\x82\xa0",
      "\xe2\xb3\xae"     => "\xe2\xb3\xad",
      "\xe2\xb3\xac"     => "\xe2\xb3\xab",
      "\xe2\xb3\xa3"     => "\xe2\xb3\xa2",
      "\xe2\xb3\xa1"     => "\xe2\xb3\xa0",
      "\xe2\xb3\x9f"     => "\xe2\xb3\x9e",
      "\xe2\xb3\x9d"     => "\xe2\xb3\x9c",
      "\xe2\xb3\x9b"     => "\xe2\xb3\x9a",
      "\xe2\xb3\x99"     => "\xe2\xb3\x98",
      "\xe2\xb3\x97"     => "\xe2\xb3\x96",
      "\xe2\xb3\x95"     => "\xe2\xb3\x94",
      "\xe2\xb3\x93"     => "\xe2\xb3\x92",
      "\xe2\xb3\x91"     => "\xe2\xb3\x90",
      "\xe2\xb3\x8f"     => "\xe2\xb3\x8e",
      "\xe2\xb3\x8d"     => "\xe2\xb3\x8c",
      "\xe2\xb3\x8b"     => "\xe2\xb3\x8a",
      "\xe2\xb3\x89"     => "\xe2\xb3\x88",
      "\xe2\xb3\x87"     => "\xe2\xb3\x86",
      "\xe2\xb3\x85"     => "\xe2\xb3\x84",
      "\xe2\xb3\x83"     => "\xe2\xb3\x82",
      "\xe2\xb3\x81"     => "\xe2\xb3\x80",
      "\xe2\xb2\xbf"     => "\xe2\xb2\xbe",
      "\xe2\xb2\xbd"     => "\xe2\xb2\xbc",
      "\xe2\xb2\xbb"     => "\xe2\xb2\xba",
      "\xe2\xb2\xb9"     => "\xe2\xb2\xb8",
      "\xe2\xb2\xb7"     => "\xe2\xb2\xb6",
      "\xe2\xb2\xb5"     => "\xe2\xb2\xb4",
      "\xe2\xb2\xb3"     => "\xe2\xb2\xb2",
      "\xe2\xb2\xb1"     => "\xe2\xb2\xb0",
      "\xe2\xb2\xaf"     => "\xe2\xb2\xae",
      "\xe2\xb2\xad"     => "\xe2\xb2\xac",
      "\xe2\xb2\xab"     => "\xe2\xb2\xaa",
      "\xe2\xb2\xa9"     => "\xe2\xb2\xa8",
      "\xe2\xb2\xa7"     => "\xe2\xb2\xa6",
      "\xe2\xb2\xa5"     => "\xe2\xb2\xa4",
      "\xe2\xb2\xa3"     => "\xe2\xb2\xa2",
      "\xe2\xb2\xa1"     => "\xe2\xb2\xa0",
      "\xe2\xb2\x9f"     => "\xe2\xb2\x9e",
      "\xe2\xb2\x9d"     => "\xe2\xb2\x9c",
      "\xe2\xb2\x9b"     => "\xe2\xb2\x9a",
      "\xe2\xb2\x99"     => "\xe2\xb2\x98",
      "\xe2\xb2\x97"     => "\xe2\xb2\x96",
      "\xe2\xb2\x95"     => "\xe2\xb2\x94",
      "\xe2\xb2\x93"     => "\xe2\xb2\x92",
      "\xe2\xb2\x91"     => "\xe2\xb2\x90",
      "\xe2\xb2\x8f"     => "\xe2\xb2\x8e",
      "\xe2\xb2\x8d"     => "\xe2\xb2\x8c",
      "\xe2\xb2\x8b"     => "\xe2\xb2\x8a",
      "\xe2\xb2\x89"     => "\xe2\xb2\x88",
      "\xe2\xb2\x87"     => "\xe2\xb2\x86",
      "\xe2\xb2\x85"     => "\xe2\xb2\x84",
      "\xe2\xb2\x83"     => "\xe2\xb2\x82",
      "\xe2\xb2\x81"     => "\xe2\xb2\x80",
      "\xe2\xb1\xb6"     => "\xe2\xb1\xb5",
      "\xe2\xb1\xb3"     => "\xe2\xb1\xb2",
      "\xe2\xb1\xac"     => "\xe2\xb1\xab",
      "\xe2\xb1\xaa"     => "\xe2\xb1\xa9",
      "\xe2\xb1\xa8"     => "\xe2\xb1\xa7",
      "\xe2\xb1\xa6"     => "\xc8\xbe",
      "\xe2\xb1\xa5"     => "\xc8\xba",
      "\xe2\xb1\xa1"     => "\xe2\xb1\xa0",
      "\xe2\xb1\x9e"     => "\xe2\xb0\xae",
      "\xe2\xb1\x9d"     => "\xe2\xb0\xad",
      "\xe2\xb1\x9c"     => "\xe2\xb0\xac",
      "\xe2\xb1\x9b"     => "\xe2\xb0\xab",
      "\xe2\xb1\x9a"     => "\xe2\xb0\xaa",
      "\xe2\xb1\x99"     => "\xe2\xb0\xa9",
      "\xe2\xb1\x98"     => "\xe2\xb0\xa8",
      "\xe2\xb1\x97"     => "\xe2\xb0\xa7",
      "\xe2\xb1\x96"     => "\xe2\xb0\xa6",
      "\xe2\xb1\x95"     => "\xe2\xb0\xa5",
      "\xe2\xb1\x94"     => "\xe2\xb0\xa4",
      "\xe2\xb1\x93"     => "\xe2\xb0\xa3",
      "\xe2\xb1\x92"     => "\xe2\xb0\xa2",
      "\xe2\xb1\x91"     => "\xe2\xb0\xa1",
      "\xe2\xb1\x90"     => "\xe2\xb0\xa0",
      "\xe2\xb1\x8f"     => "\xe2\xb0\x9f",
      "\xe2\xb1\x8e"     => "\xe2\xb0\x9e",
      "\xe2\xb1\x8d"     => "\xe2\xb0\x9d",
      "\xe2\xb1\x8c"     => "\xe2\xb0\x9c",
      "\xe2\xb1\x8b"     => "\xe2\xb0\x9b",
      "\xe2\xb1\x8a"     => "\xe2\xb0\x9a",
      "\xe2\xb1\x89"     => "\xe2\xb0\x99",
      "\xe2\xb1\x88"     => "\xe2\xb0\x98",
      "\xe2\xb1\x87"     => "\xe2\xb0\x97",
      "\xe2\xb1\x86"     => "\xe2\xb0\x96",
      "\xe2\xb1\x85"     => "\xe2\xb0\x95",
      "\xe2\xb1\x84"     => "\xe2\xb0\x94",
      "\xe2\xb1\x83"     => "\xe2\xb0\x93",
      "\xe2\xb1\x82"     => "\xe2\xb0\x92",
      "\xe2\xb1\x81"     => "\xe2\xb0\x91",
      "\xe2\xb1\x80"     => "\xe2\xb0\x90",
      "\xe2\xb0\xbf"     => "\xe2\xb0\x8f",
      "\xe2\xb0\xbe"     => "\xe2\xb0\x8e",
      "\xe2\xb0\xbd"     => "\xe2\xb0\x8d",
      "\xe2\xb0\xbc"     => "\xe2\xb0\x8c",
      "\xe2\xb0\xbb"     => "\xe2\xb0\x8b",
      "\xe2\xb0\xba"     => "\xe2\xb0\x8a",
      "\xe2\xb0\xb9"     => "\xe2\xb0\x89",
      "\xe2\xb0\xb8"     => "\xe2\xb0\x88",
      "\xe2\xb0\xb7"     => "\xe2\xb0\x87",
      "\xe2\xb0\xb6"     => "\xe2\xb0\x86",
      "\xe2\xb0\xb5"     => "\xe2\xb0\x85",
      "\xe2\xb0\xb4"     => "\xe2\xb0\x84",
      "\xe2\xb0\xb3"     => "\xe2\xb0\x83",
      "\xe2\xb0\xb2"     => "\xe2\xb0\x82",
      "\xe2\xb0\xb1"     => "\xe2\xb0\x81",
      "\xe2\xb0\xb0"     => "\xe2\xb0\x80",
      "\xe2\x86\x84"     => "\xe2\x86\x83",
      "\xe2\x85\x8e"     => "\xe2\x84\xb2",
      "\xe1\xbf\xb3"     => "\xe1\xbf\xbc",
      "\xe1\xbf\xa5"     => "\xe1\xbf\xac",
      "\xe1\xbf\xa1"     => "\xe1\xbf\xa9",
      "\xe1\xbf\xa0"     => "\xe1\xbf\xa8",
      "\xe1\xbf\x91"     => "\xe1\xbf\x99",
      "\xe1\xbf\x90"     => "\xe1\xbf\x98",
      "\xe1\xbf\x83"     => "\xe1\xbf\x8c",
      "\xe1\xbe\xbe"     => "\xce\x99",
      "\xe1\xbe\xb3"     => "\xe1\xbe\xbc",
      "\xe1\xbe\xb1"     => "\xe1\xbe\xb9",
      "\xe1\xbe\xb0"     => "\xe1\xbe\xb8",
      "\xe1\xbe\xa7"     => "\xe1\xbe\xaf",
      "\xe1\xbe\xa6"     => "\xe1\xbe\xae",
      "\xe1\xbe\xa5"     => "\xe1\xbe\xad",
      "\xe1\xbe\xa4"     => "\xe1\xbe\xac",
      "\xe1\xbe\xa3"     => "\xe1\xbe\xab",
      "\xe1\xbe\xa2"     => "\xe1\xbe\xaa",
      "\xe1\xbe\xa1"     => "\xe1\xbe\xa9",
      "\xe1\xbe\xa0"     => "\xe1\xbe\xa8",
      "\xe1\xbe\x97"     => "\xe1\xbe\x9f",
      "\xe1\xbe\x96"     => "\xe1\xbe\x9e",
      "\xe1\xbe\x95"     => "\xe1\xbe\x9d",
      "\xe1\xbe\x94"     => "\xe1\xbe\x9c",
      "\xe1\xbe\x93"     => "\xe1\xbe\x9b",
      "\xe1\xbe\x92"     => "\xe1\xbe\x9a",
      "\xe1\xbe\x91"     => "\xe1\xbe\x99",
      "\xe1\xbe\x90"     => "\xe1\xbe\x98",
      "\xe1\xbe\x87"     => "\xe1\xbe\x8f",
      "\xe1\xbe\x86"     => "\xe1\xbe\x8e",
      "\xe1\xbe\x85"     => "\xe1\xbe\x8d",
      "\xe1\xbe\x84"     => "\xe1\xbe\x8c",
      "\xe1\xbe\x83"     => "\xe1\xbe\x8b",
      "\xe1\xbe\x82"     => "\xe1\xbe\x8a",
      "\xe1\xbe\x81"     => "\xe1\xbe\x89",
      "\xe1\xbe\x80"     => "\xe1\xbe\x88",
      "\xe1\xbd\xbd"     => "\xe1\xbf\xbb",
      "\xe1\xbd\xbc"     => "\xe1\xbf\xba",
      "\xe1\xbd\xbb"     => "\xe1\xbf\xab",
      "\xe1\xbd\xba"     => "\xe1\xbf\xaa",
      "\xe1\xbd\xb9"     => "\xe1\xbf\xb9",
      "\xe1\xbd\xb8"     => "\xe1\xbf\xb8",
      "\xe1\xbd\xb7"     => "\xe1\xbf\x9b",
      "\xe1\xbd\xb6"     => "\xe1\xbf\x9a",
      "\xe1\xbd\xb5"     => "\xe1\xbf\x8b",
      "\xe1\xbd\xb4"     => "\xe1\xbf\x8a",
      "\xe1\xbd\xb3"     => "\xe1\xbf\x89",
      "\xe1\xbd\xb2"     => "\xe1\xbf\x88",
      "\xe1\xbd\xb1"     => "\xe1\xbe\xbb",
      "\xe1\xbd\xb0"     => "\xe1\xbe\xba",
      "\xe1\xbd\xa7"     => "\xe1\xbd\xaf",
      "\xe1\xbd\xa6"     => "\xe1\xbd\xae",
      "\xe1\xbd\xa5"     => "\xe1\xbd\xad",
      "\xe1\xbd\xa4"     => "\xe1\xbd\xac",
      "\xe1\xbd\xa3"     => "\xe1\xbd\xab",
      "\xe1\xbd\xa2"     => "\xe1\xbd\xaa",
      "\xe1\xbd\xa1"     => "\xe1\xbd\xa9",
      "\xe1\xbd\xa0"     => "\xe1\xbd\xa8",
      "\xe1\xbd\x97"     => "\xe1\xbd\x9f",
      "\xe1\xbd\x95"     => "\xe1\xbd\x9d",
      "\xe1\xbd\x93"     => "\xe1\xbd\x9b",
      "\xe1\xbd\x91"     => "\xe1\xbd\x99",
      "\xe1\xbd\x85"     => "\xe1\xbd\x8d",
      "\xe1\xbd\x84"     => "\xe1\xbd\x8c",
      "\xe1\xbd\x83"     => "\xe1\xbd\x8b",
      "\xe1\xbd\x82"     => "\xe1\xbd\x8a",
      "\xe1\xbd\x81"     => "\xe1\xbd\x89",
      "\xe1\xbd\x80"     => "\xe1\xbd\x88",
      "\xe1\xbc\xb7"     => "\xe1\xbc\xbf",
      "\xe1\xbc\xb6"     => "\xe1\xbc\xbe",
      "\xe1\xbc\xb5"     => "\xe1\xbc\xbd",
      "\xe1\xbc\xb4"     => "\xe1\xbc\xbc",
      "\xe1\xbc\xb3"     => "\xe1\xbc\xbb",
      "\xe1\xbc\xb2"     => "\xe1\xbc\xba",
      "\xe1\xbc\xb1"     => "\xe1\xbc\xb9",
      "\xe1\xbc\xb0"     => "\xe1\xbc\xb8",
      "\xe1\xbc\xa7"     => "\xe1\xbc\xaf",
      "\xe1\xbc\xa6"     => "\xe1\xbc\xae",
      "\xe1\xbc\xa5"     => "\xe1\xbc\xad",
      "\xe1\xbc\xa4"     => "\xe1\xbc\xac",
      "\xe1\xbc\xa3"     => "\xe1\xbc\xab",
      "\xe1\xbc\xa2"     => "\xe1\xbc\xaa",
      "\xe1\xbc\xa1"     => "\xe1\xbc\xa9",
      "\xe1\xbc\xa0"     => "\xe1\xbc\xa8",
      "\xe1\xbc\x95"     => "\xe1\xbc\x9d",
      "\xe1\xbc\x94"     => "\xe1\xbc\x9c",
      "\xe1\xbc\x93"     => "\xe1\xbc\x9b",
      "\xe1\xbc\x92"     => "\xe1\xbc\x9a",
      "\xe1\xbc\x91"     => "\xe1\xbc\x99",
      "\xe1\xbc\x90"     => "\xe1\xbc\x98",
      "\xe1\xbc\x87"     => "\xe1\xbc\x8f",
      "\xe1\xbc\x86"     => "\xe1\xbc\x8e",
      "\xe1\xbc\x85"     => "\xe1\xbc\x8d",
      "\xe1\xbc\x84"     => "\xe1\xbc\x8c",
      "\xe1\xbc\x83"     => "\xe1\xbc\x8b",
      "\xe1\xbc\x82"     => "\xe1\xbc\x8a",
      "\xe1\xbc\x81"     => "\xe1\xbc\x89",
      "\xe1\xbc\x80"     => "\xe1\xbc\x88",
      "\xe1\xbb\xbf"     => "\xe1\xbb\xbe",
      "\xe1\xbb\xbd"     => "\xe1\xbb\xbc",
      "\xe1\xbb\xbb"     => "\xe1\xbb\xba",
      "\xe1\xbb\xb9"     => "\xe1\xbb\xb8",
      "\xe1\xbb\xb7"     => "\xe1\xbb\xb6",
      "\xe1\xbb\xb5"     => "\xe1\xbb\xb4",
      "\xe1\xbb\xb3"     => "\xe1\xbb\xb2",
      "\xe1\xbb\xb1"     => "\xe1\xbb\xb0",
      "\xe1\xbb\xaf"     => "\xe1\xbb\xae",
      "\xe1\xbb\xad"     => "\xe1\xbb\xac",
      "\xe1\xbb\xab"     => "\xe1\xbb\xaa",
      "\xe1\xbb\xa9"     => "\xe1\xbb\xa8",
      "\xe1\xbb\xa7"     => "\xe1\xbb\xa6",
      "\xe1\xbb\xa5"     => "\xe1\xbb\xa4",
      "\xe1\xbb\xa3"     => "\xe1\xbb\xa2",
      "\xe1\xbb\xa1"     => "\xe1\xbb\xa0",
      "\xe1\xbb\x9f"     => "\xe1\xbb\x9e",
      "\xe1\xbb\x9d"     => "\xe1\xbb\x9c",
      "\xe1\xbb\x9b"     => "\xe1\xbb\x9a",
      "\xe1\xbb\x99"     => "\xe1\xbb\x98",
      "\xe1\xbb\x97"     => "\xe1\xbb\x96",
      "\xe1\xbb\x95"     => "\xe1\xbb\x94",
      "\xe1\xbb\x93"     => "\xe1\xbb\x92",
      "\xe1\xbb\x91"     => "\xe1\xbb\x90",
      "\xe1\xbb\x8f"     => "\xe1\xbb\x8e",
      "\xe1\xbb\x8d"     => "\xe1\xbb\x8c",
      "\xe1\xbb\x8b"     => "\xe1\xbb\x8a",
      "\xe1\xbb\x89"     => "\xe1\xbb\x88",
      "\xe1\xbb\x87"     => "\xe1\xbb\x86",
      "\xe1\xbb\x85"     => "\xe1\xbb\x84",
      "\xe1\xbb\x83"     => "\xe1\xbb\x82",
      "\xe1\xbb\x81"     => "\xe1\xbb\x80",
      "\xe1\xba\xbf"     => "\xe1\xba\xbe",
      "\xe1\xba\xbd"     => "\xe1\xba\xbc",
      "\xe1\xba\xbb"     => "\xe1\xba\xba",
      "\xe1\xba\xb9"     => "\xe1\xba\xb8",
      "\xe1\xba\xb7"     => "\xe1\xba\xb6",
      "\xe1\xba\xb5"     => "\xe1\xba\xb4",
      "\xe1\xba\xb3"     => "\xe1\xba\xb2",
      "\xe1\xba\xb1"     => "\xe1\xba\xb0",
      "\xe1\xba\xaf"     => "\xe1\xba\xae",
      "\xe1\xba\xad"     => "\xe1\xba\xac",
      "\xe1\xba\xab"     => "\xe1\xba\xaa",
      "\xe1\xba\xa9"     => "\xe1\xba\xa8",
      "\xe1\xba\xa7"     => "\xe1\xba\xa6",
      "\xe1\xba\xa5"     => "\xe1\xba\xa4",
      "\xe1\xba\xa3"     => "\xe1\xba\xa2",
      "\xe1\xba\xa1"     => "\xe1\xba\xa0",
      "\xe1\xba\x9b"     => "\xe1\xb9\xa0",
      "\xe1\xba\x95"     => "\xe1\xba\x94",
      "\xe1\xba\x93"     => "\xe1\xba\x92",
      "\xe1\xba\x91"     => "\xe1\xba\x90",
      "\xe1\xba\x8f"     => "\xe1\xba\x8e",
      "\xe1\xba\x8d"     => "\xe1\xba\x8c",
      "\xe1\xba\x8b"     => "\xe1\xba\x8a",
      "\xe1\xba\x89"     => "\xe1\xba\x88",
      "\xe1\xba\x87"     => "\xe1\xba\x86",
      "\xe1\xba\x85"     => "\xe1\xba\x84",
      "\xe1\xba\x83"     => "\xe1\xba\x82",
      "\xe1\xba\x81"     => "\xe1\xba\x80",
      "\xe1\xb9\xbf"     => "\xe1\xb9\xbe",
      "\xe1\xb9\xbd"     => "\xe1\xb9\xbc",
      "\xe1\xb9\xbb"     => "\xe1\xb9\xba",
      "\xe1\xb9\xb9"     => "\xe1\xb9\xb8",
      "\xe1\xb9\xb7"     => "\xe1\xb9\xb6",
      "\xe1\xb9\xb5"     => "\xe1\xb9\xb4",
      "\xe1\xb9\xb3"     => "\xe1\xb9\xb2",
      "\xe1\xb9\xb1"     => "\xe1\xb9\xb0",
      "\xe1\xb9\xaf"     => "\xe1\xb9\xae",
      "\xe1\xb9\xad"     => "\xe1\xb9\xac",
      "\xe1\xb9\xab"     => "\xe1\xb9\xaa",
      "\xe1\xb9\xa9"     => "\xe1\xb9\xa8",
      "\xe1\xb9\xa7"     => "\xe1\xb9\xa6",
      "\xe1\xb9\xa5"     => "\xe1\xb9\xa4",
      "\xe1\xb9\xa3"     => "\xe1\xb9\xa2",
      "\xe1\xb9\xa1"     => "\xe1\xb9\xa0",
      "\xe1\xb9\x9f"     => "\xe1\xb9\x9e",
      "\xe1\xb9\x9d"     => "\xe1\xb9\x9c",
      "\xe1\xb9\x9b"     => "\xe1\xb9\x9a",
      "\xe1\xb9\x99"     => "\xe1\xb9\x98",
      "\xe1\xb9\x97"     => "\xe1\xb9\x96",
      "\xe1\xb9\x95"     => "\xe1\xb9\x94",
      "\xe1\xb9\x93"     => "\xe1\xb9\x92",
      "\xe1\xb9\x91"     => "\xe1\xb9\x90",
      "\xe1\xb9\x8f"     => "\xe1\xb9\x8e",
      "\xe1\xb9\x8d"     => "\xe1\xb9\x8c",
      "\xe1\xb9\x8b"     => "\xe1\xb9\x8a",
      "\xe1\xb9\x89"     => "\xe1\xb9\x88",
      "\xe1\xb9\x87"     => "\xe1\xb9\x86",
      "\xe1\xb9\x85"     => "\xe1\xb9\x84",
      "\xe1\xb9\x83"     => "\xe1\xb9\x82",
      "\xe1\xb9\x81"     => "\xe1\xb9\x80",
      "\xe1\xb8\xbf"     => "\xe1\xb8\xbe",
      "\xe1\xb8\xbd"     => "\xe1\xb8\xbc",
      "\xe1\xb8\xbb"     => "\xe1\xb8\xba",
      "\xe1\xb8\xb9"     => "\xe1\xb8\xb8",
      "\xe1\xb8\xb7"     => "\xe1\xb8\xb6",
      "\xe1\xb8\xb5"     => "\xe1\xb8\xb4",
      "\xe1\xb8\xb3"     => "\xe1\xb8\xb2",
      "\xe1\xb8\xb1"     => "\xe1\xb8\xb0",
      "\xe1\xb8\xaf"     => "\xe1\xb8\xae",
      "\xe1\xb8\xad"     => "\xe1\xb8\xac",
      "\xe1\xb8\xab"     => "\xe1\xb8\xaa",
      "\xe1\xb8\xa9"     => "\xe1\xb8\xa8",
      "\xe1\xb8\xa7"     => "\xe1\xb8\xa6",
      "\xe1\xb8\xa5"     => "\xe1\xb8\xa4",
      "\xe1\xb8\xa3"     => "\xe1\xb8\xa2",
      "\xe1\xb8\xa1"     => "\xe1\xb8\xa0",
      "\xe1\xb8\x9f"     => "\xe1\xb8\x9e",
      "\xe1\xb8\x9d"     => "\xe1\xb8\x9c",
      "\xe1\xb8\x9b"     => "\xe1\xb8\x9a",
      "\xe1\xb8\x99"     => "\xe1\xb8\x98",
      "\xe1\xb8\x97"     => "\xe1\xb8\x96",
      "\xe1\xb8\x95"     => "\xe1\xb8\x94",
      "\xe1\xb8\x93"     => "\xe1\xb8\x92",
      "\xe1\xb8\x91"     => "\xe1\xb8\x90",
      "\xe1\xb8\x8f"     => "\xe1\xb8\x8e",
      "\xe1\xb8\x8d"     => "\xe1\xb8\x8c",
      "\xe1\xb8\x8b"     => "\xe1\xb8\x8a",
      "\xe1\xb8\x89"     => "\xe1\xb8\x88",
      "\xe1\xb8\x87"     => "\xe1\xb8\x86",
      "\xe1\xb8\x85"     => "\xe1\xb8\x84",
      "\xe1\xb8\x83"     => "\xe1\xb8\x82",
      "\xe1\xb8\x81"     => "\xe1\xb8\x80",
      "\xe1\xb5\xbd"     => "\xe2\xb1\xa3",
      "\xe1\xb5\xb9"     => "\xea\x9d\xbd",
      "\xd6\x86"         => "\xd5\x96",
      "\xd6\x85"         => "\xd5\x95",
      "\xd6\x84"         => "\xd5\x94",
      "\xd6\x83"         => "\xd5\x93",
      "\xd6\x82"         => "\xd5\x92",
      "\xd6\x81"         => "\xd5\x91",
      "\xd6\x80"         => "\xd5\x90",
      "\xd5\xbf"         => "\xd5\x8f",
      "\xd5\xbe"         => "\xd5\x8e",
      "\xd5\xbd"         => "\xd5\x8d",
      "\xd5\xbc"         => "\xd5\x8c",
      "\xd5\xbb"         => "\xd5\x8b",
      "\xd5\xba"         => "\xd5\x8a",
      "\xd5\xb9"         => "\xd5\x89",
      "\xd5\xb8"         => "\xd5\x88",
      "\xd5\xb7"         => "\xd5\x87",
      "\xd5\xb6"         => "\xd5\x86",
      "\xd5\xb5"         => "\xd5\x85",
      "\xd5\xb4"         => "\xd5\x84",
      "\xd5\xb3"         => "\xd5\x83",
      "\xd5\xb2"         => "\xd5\x82",
      "\xd5\xb1"         => "\xd5\x81",
      "\xd5\xb0"         => "\xd5\x80",
      "\xd5\xaf"         => "\xd4\xbf",
      "\xd5\xae"         => "\xd4\xbe",
      "\xd5\xad"         => "\xd4\xbd",
      "\xd5\xac"         => "\xd4\xbc",
      "\xd5\xab"         => "\xd4\xbb",
      "\xd5\xaa"         => "\xd4\xba",
      "\xd5\xa9"         => "\xd4\xb9",
      "\xd5\xa8"         => "\xd4\xb8",
      "\xd5\xa7"         => "\xd4\xb7",
      "\xd5\xa6"         => "\xd4\xb6",
      "\xd5\xa5"         => "\xd4\xb5",
      "\xd5\xa4"         => "\xd4\xb4",
      "\xd5\xa3"         => "\xd4\xb3",
      "\xd5\xa2"         => "\xd4\xb2",
      "\xd5\xa1"         => "\xd4\xb1",
      "\xd4\xa5"         => "\xd4\xa4",
      "\xd4\xa3"         => "\xd4\xa2",
      "\xd4\xa1"         => "\xd4\xa0",
      "\xd4\x9f"         => "\xd4\x9e",
      "\xd4\x9d"         => "\xd4\x9c",
      "\xd4\x9b"         => "\xd4\x9a",
      "\xd4\x99"         => "\xd4\x98",
      "\xd4\x97"         => "\xd4\x96",
      "\xd4\x95"         => "\xd4\x94",
      "\xd4\x93"         => "\xd4\x92",
      "\xd4\x91"         => "\xd4\x90",
      "\xd4\x8f"         => "\xd4\x8e",
      "\xd4\x8d"         => "\xd4\x8c",
      "\xd4\x8b"         => "\xd4\x8a",
      "\xd4\x89"         => "\xd4\x88",
      "\xd4\x87"         => "\xd4\x86",
      "\xd4\x85"         => "\xd4\x84",
      "\xd4\x83"         => "\xd4\x82",
      "\xd4\x81"         => "\xd4\x80",
      "\xd3\xbf"         => "\xd3\xbe",
      "\xd3\xbd"         => "\xd3\xbc",
      "\xd3\xbb"         => "\xd3\xba",
      "\xd3\xb9"         => "\xd3\xb8",
      "\xd3\xb7"         => "\xd3\xb6",
      "\xd3\xb5"         => "\xd3\xb4",
      "\xd3\xb3"         => "\xd3\xb2",
      "\xd3\xb1"         => "\xd3\xb0",
      "\xd3\xaf"         => "\xd3\xae",
      "\xd3\xad"         => "\xd3\xac",
      "\xd3\xab"         => "\xd3\xaa",
      "\xd3\xa9"         => "\xd3\xa8",
      "\xd3\xa7"         => "\xd3\xa6",
      "\xd3\xa5"         => "\xd3\xa4",
      "\xd3\xa3"         => "\xd3\xa2",
      "\xd3\xa1"         => "\xd3\xa0",
      "\xd3\x9f"         => "\xd3\x9e",
      "\xd3\x9d"         => "\xd3\x9c",
      "\xd3\x9b"         => "\xd3\x9a",
      "\xd3\x99"         => "\xd3\x98",
      "\xd3\x97"         => "\xd3\x96",
      "\xd3\x95"         => "\xd3\x94",
      "\xd3\x93"         => "\xd3\x92",
      "\xd3\x91"         => "\xd3\x90",
      "\xd3\x8f"         => "\xd3\x80",
      "\xd3\x8e"         => "\xd3\x8d",
      "\xd3\x8c"         => "\xd3\x8b",
      "\xd3\x8a"         => "\xd3\x89",
      "\xd3\x88"         => "\xd3\x87",
      "\xd3\x86"         => "\xd3\x85",
      "\xd3\x84"         => "\xd3\x83",
      "\xd3\x82"         => "\xd3\x81",
      "\xd2\xbf"         => "\xd2\xbe",
      "\xd2\xbd"         => "\xd2\xbc",
      "\xd2\xbb"         => "\xd2\xba",
      "\xd2\xb9"         => "\xd2\xb8",
      "\xd2\xb7"         => "\xd2\xb6",
      "\xd2\xb5"         => "\xd2\xb4",
      "\xd2\xb3"         => "\xd2\xb2",
      "\xd2\xb1"         => "\xd2\xb0",
      "\xd2\xaf"         => "\xd2\xae",
      "\xd2\xad"         => "\xd2\xac",
      "\xd2\xab"         => "\xd2\xaa",
      "\xd2\xa9"         => "\xd2\xa8",
      "\xd2\xa7"         => "\xd2\xa6",
      "\xd2\xa5"         => "\xd2\xa4",
      "\xd2\xa3"         => "\xd2\xa2",
      "\xd2\xa1"         => "\xd2\xa0",
      "\xd2\x9f"         => "\xd2\x9e",
      "\xd2\x9d"         => "\xd2\x9c",
      "\xd2\x9b"         => "\xd2\x9a",
      "\xd2\x99"         => "\xd2\x98",
      "\xd2\x97"         => "\xd2\x96",
      "\xd2\x95"         => "\xd2\x94",
      "\xd2\x93"         => "\xd2\x92",
      "\xd2\x91"         => "\xd2\x90",
      "\xd2\x8f"         => "\xd2\x8e",
      "\xd2\x8d"         => "\xd2\x8c",
      "\xd2\x8b"         => "\xd2\x8a",
      "\xd2\x81"         => "\xd2\x80",
      "\xd1\xbf"         => "\xd1\xbe",
      "\xd1\xbd"         => "\xd1\xbc",
      "\xd1\xbb"         => "\xd1\xba",
      "\xd1\xb9"         => "\xd1\xb8",
      "\xd1\xb7"         => "\xd1\xb6",
      "\xd1\xb5"         => "\xd1\xb4",
      "\xd1\xb3"         => "\xd1\xb2",
      "\xd1\xb1"         => "\xd1\xb0",
      "\xd1\xaf"         => "\xd1\xae",
      "\xd1\xad"         => "\xd1\xac",
      "\xd1\xab"         => "\xd1\xaa",
      "\xd1\xa9"         => "\xd1\xa8",
      "\xd1\xa7"         => "\xd1\xa6",
      "\xd1\xa5"         => "\xd1\xa4",
      "\xd1\xa3"         => "\xd1\xa2",
      "\xd1\xa1"         => "\xd1\xa0",
      "\xd1\x9f"         => "\xd0\x8f",
      "\xd1\x9e"         => "\xd0\x8e",
      "\xd1\x9d"         => "\xd0\x8d",
      "\xd1\x9c"         => "\xd0\x8c",
      "\xd1\x9b"         => "\xd0\x8b",
      "\xd1\x9a"         => "\xd0\x8a",
      "\xd1\x99"         => "\xd0\x89",
      "\xd1\x98"         => "\xd0\x88",
      "\xd1\x97"         => "\xd0\x87",
      "\xd1\x96"         => "\xd0\x86",
      "\xd1\x95"         => "\xd0\x85",
      "\xd1\x94"         => "\xd0\x84",
      "\xd1\x93"         => "\xd0\x83",
      "\xd1\x92"         => "\xd0\x82",
      "\xd1\x91"         => "\xd0\x81",
      "\xd1\x90"         => "\xd0\x80",
      "\xd1\x8f"         => "\xd0\xaf",
      "\xd1\x8e"         => "\xd0\xae",
      "\xd1\x8d"         => "\xd0\xad",
      "\xd1\x8c"         => "\xd0\xac",
      "\xd1\x8b"         => "\xd0\xab",
      "\xd1\x8a"         => "\xd0\xaa",
      "\xd1\x89"         => "\xd0\xa9",
      "\xd1\x88"         => "\xd0\xa8",
      "\xd1\x87"         => "\xd0\xa7",
      "\xd1\x86"         => "\xd0\xa6",
      "\xd1\x85"         => "\xd0\xa5",
      "\xd1\x84"         => "\xd0\xa4",
      "\xd1\x83"         => "\xd0\xa3",
      "\xd1\x82"         => "\xd0\xa2",
      "\xd1\x81"         => "\xd0\xa1",
      "\xd1\x80"         => "\xd0\xa0",
      "\xd0\xbf"         => "\xd0\x9f",
      "\xd0\xbe"         => "\xd0\x9e",
      "\xd0\xbd"         => "\xd0\x9d",
      "\xd0\xbc"         => "\xd0\x9c",
      "\xd0\xbb"         => "\xd0\x9b",
      "\xd0\xba"         => "\xd0\x9a",
      "\xd0\xb9"         => "\xd0\x99",
      "\xd0\xb8"         => "\xd0\x98",
      "\xd0\xb7"         => "\xd0\x97",
      "\xd0\xb6"         => "\xd0\x96",
      "\xd0\xb5"         => "\xd0\x95",
      "\xd0\xb4"         => "\xd0\x94",
      "\xd0\xb3"         => "\xd0\x93",
      "\xd0\xb2"         => "\xd0\x92",
      "\xd0\xb1"         => "\xd0\x91",
      "\xd0\xb0"         => "\xd0\x90",
      "\xcf\xbb"         => "\xcf\xba",
      "\xcf\xb8"         => "\xcf\xb7",
      "\xcf\xb5"         => "\xce\x95",
      "\xcf\xb2"         => "\xcf\xb9",
      "\xcf\xb1"         => "\xce\xa1",
      "\xcf\xb0"         => "\xce\x9a",
      "\xcf\xaf"         => "\xcf\xae",
      "\xcf\xad"         => "\xcf\xac",
      "\xcf\xab"         => "\xcf\xaa",
      "\xcf\xa9"         => "\xcf\xa8",
      "\xcf\xa7"         => "\xcf\xa6",
      "\xcf\xa5"         => "\xcf\xa4",
      "\xcf\xa3"         => "\xcf\xa2",
      "\xcf\xa1"         => "\xcf\xa0",
      "\xcf\x9f"         => "\xcf\x9e",
      "\xcf\x9d"         => "\xcf\x9c",
      "\xcf\x9b"         => "\xcf\x9a",
      "\xcf\x99"         => "\xcf\x98",
      "\xcf\x97"         => "\xcf\x8f",
      "\xcf\x96"         => "\xce\xa0",
      "\xcf\x95"         => "\xce\xa6",
      "\xcf\x91"         => "\xce\x98",
      "\xcf\x90"         => "\xce\x92",
      "\xcf\x8e"         => "\xce\x8f",
      "\xcf\x8d"         => "\xce\x8e",
      "\xcf\x8c"         => "\xce\x8c",
      "\xcf\x8b"         => "\xce\xab",
      "\xcf\x8a"         => "\xce\xaa",
      "\xcf\x89"         => "\xce\xa9",
      "\xcf\x88"         => "\xce\xa8",
      "\xcf\x87"         => "\xce\xa7",
      "\xcf\x86"         => "\xce\xa6",
      "\xcf\x85"         => "\xce\xa5",
      "\xcf\x84"         => "\xce\xa4",
      "\xcf\x83"         => "\xce\xa3",
      "\xcf\x82"         => "\xce\xa3",
      "\xcf\x81"         => "\xce\xa1",
      "\xcf\x80"         => "\xce\xa0",
      "\xce\xbf"         => "\xce\x9f",
      "\xce\xbe"         => "\xce\x9e",
      "\xce\xbd"         => "\xce\x9d",
      "\xce\xbc"         => "\xce\x9c",
      "\xce\xbb"         => "\xce\x9b",
      "\xce\xba"         => "\xce\x9a",
      "\xce\xb9"         => "\xce\x99",
      "\xce\xb8"         => "\xce\x98",
      "\xce\xb7"         => "\xce\x97",
      "\xce\xb6"         => "\xce\x96",
      "\xce\xb5"         => "\xce\x95",
      "\xce\xb4"         => "\xce\x94",
      "\xce\xb3"         => "\xce\x93",
      "\xce\xb2"         => "\xce\x92",
      "\xce\xb1"         => "\xce\x91",
      "\xce\xaf"         => "\xce\x8a",
      "\xce\xae"         => "\xce\x89",
      "\xce\xad"         => "\xce\x88",
      "\xce\xac"         => "\xce\x86",
      "\xcd\xbd"         => "\xcf\xbf",
      "\xcd\xbc"         => "\xcf\xbe",
      "\xcd\xbb"         => "\xcf\xbd",
      "\xcd\xb7"         => "\xcd\xb6",
      "\xcd\xb3"         => "\xcd\xb2",
      "\xcd\xb1"         => "\xcd\xb0",
      "\xca\x92"         => "\xc6\xb7",
      "\xca\x8c"         => "\xc9\x85",
      "\xca\x8b"         => "\xc6\xb2",
      "\xca\x8a"         => "\xc6\xb1",
      "\xca\x89"         => "\xc9\x84",
      "\xca\x88"         => "\xc6\xae",
      "\xca\x83"         => "\xc6\xa9",
      "\xca\x80"         => "\xc6\xa6",
      "\xc9\xbd"         => "\xe2\xb1\xa4",
      "\xc9\xb5"         => "\xc6\x9f",
      "\xc9\xb2"         => "\xc6\x9d",
      "\xc9\xb1"         => "\xe2\xb1\xae",
      "\xc9\xaf"         => "\xc6\x9c",
      "\xc9\xab"         => "\xe2\xb1\xa2",
      "\xc9\xa9"         => "\xc6\x96",
      "\xc9\xa8"         => "\xc6\x97",
      "\xc9\xa5"         => "\xea\x9e\x8d",
      "\xc9\xa3"         => "\xc6\x94",
      "\xc9\xa0"         => "\xc6\x93",
      "\xc9\x9b"         => "\xc6\x90",
      "\xc9\x99"         => "\xc6\x8f",
      "\xc9\x97"         => "\xc6\x8a",
      "\xc9\x96"         => "\xc6\x89",
      "\xc9\x94"         => "\xc6\x86",
      "\xc9\x93"         => "\xc6\x81",
      "\xc9\x92"         => "\xe2\xb1\xb0",
      "\xc9\x91"         => "\xe2\xb1\xad",
      "\xc9\x90"         => "\xe2\xb1\xaf",
      "\xc9\x8f"         => "\xc9\x8e",
      "\xc9\x8d"         => "\xc9\x8c",
      "\xc9\x8b"         => "\xc9\x8a",
      "\xc9\x89"         => "\xc9\x88",
      "\xc9\x87"         => "\xc9\x86",
      "\xc9\x82"         => "\xc9\x81",
      "\xc9\x80"         => "\xe2\xb1\xbf",
      "\xc8\xbf"         => "\xe2\xb1\xbe",
      "\xc8\xbc"         => "\xc8\xbb",
      "\xc8\xb3"         => "\xc8\xb2",
      "\xc8\xb1"         => "\xc8\xb0",
      "\xc8\xaf"         => "\xc8\xae",
      "\xc8\xad"         => "\xc8\xac",
      "\xc8\xab"         => "\xc8\xaa",
      "\xc8\xa9"         => "\xc8\xa8",
      "\xc8\xa7"         => "\xc8\xa6",
      "\xc8\xa5"         => "\xc8\xa4",
      "\xc8\xa3"         => "\xc8\xa2",
      "\xc8\x9f"         => "\xc8\x9e",
      "\xc8\x9d"         => "\xc8\x9c",
      "\xc8\x9b"         => "\xc8\x9a",
      "\xc8\x99"         => "\xc8\x98",
      "\xc8\x97"         => "\xc8\x96",
      "\xc8\x95"         => "\xc8\x94",
      "\xc8\x93"         => "\xc8\x92",
      "\xc8\x91"         => "\xc8\x90",
      "\xc8\x8f"         => "\xc8\x8e",
      "\xc8\x8d"         => "\xc8\x8c",
      "\xc8\x8b"         => "\xc8\x8a",
      "\xc8\x89"         => "\xc8\x88",
      "\xc8\x87"         => "\xc8\x86",
      "\xc8\x85"         => "\xc8\x84",
      "\xc8\x83"         => "\xc8\x82",
      "\xc8\x81"         => "\xc8\x80",
      "\xc7\xbf"         => "\xc7\xbe",
      "\xc7\xbd"         => "\xc7\xbc",
      "\xc7\xbb"         => "\xc7\xba",
      "\xc7\xb9"         => "\xc7\xb8",
      "\xc7\xb5"         => "\xc7\xb4",
      "\xc7\xb3"         => "\xc7\xb2",
      "\xc7\xaf"         => "\xc7\xae",
      "\xc7\xad"         => "\xc7\xac",
      "\xc7\xab"         => "\xc7\xaa",
      "\xc7\xa9"         => "\xc7\xa8",
      "\xc7\xa7"         => "\xc7\xa6",
      "\xc7\xa5"         => "\xc7\xa4",
      "\xc7\xa3"         => "\xc7\xa2",
      "\xc7\xa1"         => "\xc7\xa0",
      "\xc7\x9f"         => "\xc7\x9e",
      "\xc7\x9d"         => "\xc6\x8e",
      "\xc7\x9c"         => "\xc7\x9b",
      "\xc7\x9a"         => "\xc7\x99",
      "\xc7\x98"         => "\xc7\x97",
      "\xc7\x96"         => "\xc7\x95",
      "\xc7\x94"         => "\xc7\x93",
      "\xc7\x92"         => "\xc7\x91",
      "\xc7\x90"         => "\xc7\x8f",
      "\xc7\x8e"         => "\xc7\x8d",
      "\xc7\x8c"         => "\xc7\x8b",
      "\xc7\x89"         => "\xc7\x88",
      "\xc7\x86"         => "\xc7\x85",
      "\xc6\xbf"         => "\xc7\xb7",
      "\xc6\xbd"         => "\xc6\xbc",
      "\xc6\xb9"         => "\xc6\xb8",
      "\xc6\xb6"         => "\xc6\xb5",
      "\xc6\xb4"         => "\xc6\xb3",
      "\xc6\xb0"         => "\xc6\xaf",
      "\xc6\xad"         => "\xc6\xac",
      "\xc6\xa8"         => "\xc6\xa7",
      "\xc6\xa5"         => "\xc6\xa4",
      "\xc6\xa3"         => "\xc6\xa2",
      "\xc6\xa1"         => "\xc6\xa0",
      "\xc6\x9e"         => "\xc8\xa0",
      "\xc6\x9a"         => "\xc8\xbd",
      "\xc6\x99"         => "\xc6\x98",
      "\xc6\x95"         => "\xc7\xb6",
      "\xc6\x92"         => "\xc6\x91",
      "\xc6\x8c"         => "\xc6\x8b",
      "\xc6\x88"         => "\xc6\x87",
      "\xc6\x85"         => "\xc6\x84",
      "\xc6\x83"         => "\xc6\x82",
      "\xc6\x80"         => "\xc9\x83",
      "\xc5\xbf"         => "\x53",
      "\xc5\xbe"         => "\xc5\xbd",
      "\xc5\xbc"         => "\xc5\xbb",
      "\xc5\xba"         => "\xc5\xb9",
      "\xc5\xb7"         => "\xc5\xb6",
      "\xc5\xb5"         => "\xc5\xb4",
      "\xc5\xb3"         => "\xc5\xb2",
      "\xc5\xb1"         => "\xc5\xb0",
      "\xc5\xaf"         => "\xc5\xae",
      "\xc5\xad"         => "\xc5\xac",
      "\xc5\xab"         => "\xc5\xaa",
      "\xc5\xa9"         => "\xc5\xa8",
      "\xc5\xa7"         => "\xc5\xa6",
      "\xc5\xa5"         => "\xc5\xa4",
      "\xc5\xa3"         => "\xc5\xa2",
      "\xc5\xa1"         => "\xc5\xa0",
      "\xc5\x9f"         => "\xc5\x9e",
      "\xc5\x9d"         => "\xc5\x9c",
      "\xc5\x9b"         => "\xc5\x9a",
      "\xc5\x99"         => "\xc5\x98",
      "\xc5\x97"         => "\xc5\x96",
      "\xc5\x95"         => "\xc5\x94",
      "\xc5\x93"         => "\xc5\x92",
      "\xc5\x91"         => "\xc5\x90",
      "\xc5\x8f"         => "\xc5\x8e",
      "\xc5\x8d"         => "\xc5\x8c",
      "\xc5\x8b"         => "\xc5\x8a",
      "\xc5\x88"         => "\xc5\x87",
      "\xc5\x86"         => "\xc5\x85",
      "\xc5\x84"         => "\xc5\x83",
      "\xc5\x82"         => "\xc5\x81",
      "\xc5\x80"         => "\xc4\xbf",
      "\xc4\xbe"         => "\xc4\xbd",
      "\xc4\xbc"         => "\xc4\xbb",
      "\xc4\xba"         => "\xc4\xb9",
      "\xc4\xb7"         => "\xc4\xb6",
      "\xc4\xb5"         => "\xc4\xb4",
      "\xc4\xb3"         => "\xc4\xb2",
      "\xc4\xb1"         => "\x49",
      "\xc4\xaf"         => "\xc4\xae",
      "\xc4\xad"         => "\xc4\xac",
      "\xc4\xab"         => "\xc4\xaa",
      "\xc4\xa9"         => "\xc4\xa8",
      "\xc4\xa7"         => "\xc4\xa6",
      "\xc4\xa5"         => "\xc4\xa4",
      "\xc4\xa3"         => "\xc4\xa2",
      "\xc4\xa1"         => "\xc4\xa0",
      "\xc4\x9f"         => "\xc4\x9e",
      "\xc4\x9d"         => "\xc4\x9c",
      "\xc4\x9b"         => "\xc4\x9a",
      "\xc4\x99"         => "\xc4\x98",
      "\xc4\x97"         => "\xc4\x96",
      "\xc4\x95"         => "\xc4\x94",
      "\xc4\x93"         => "\xc4\x92",
      "\xc4\x91"         => "\xc4\x90",
      "\xc4\x8f"         => "\xc4\x8e",
      "\xc4\x8d"         => "\xc4\x8c",
      "\xc4\x8b"         => "\xc4\x8a",
      "\xc4\x89"         => "\xc4\x88",
      "\xc4\x87"         => "\xc4\x86",
      "\xc4\x85"         => "\xc4\x84",
      "\xc4\x83"         => "\xc4\x82",
      "\xc4\x81"         => "\xc4\x80",
      "\xc3\xbf"         => "\xc5\xb8",
      "\xc3\xbe"         => "\xc3\x9e",
      "\xc3\xbd"         => "\xc3\x9d",
      "\xc3\xbc"         => "\xc3\x9c",
      "\xc3\xbb"         => "\xc3\x9b",
      "\xc3\xba"         => "\xc3\x9a",
      "\xc3\xb9"         => "\xc3\x99",
      "\xc3\xb8"         => "\xc3\x98",
      "\xc3\xb6"         => "\xc3\x96",
      "\xc3\xb5"         => "\xc3\x95",
      "\xc3\xb4"         => "\xc3\x94",
      "\xc3\xb3"         => "\xc3\x93",
      "\xc3\xb2"         => "\xc3\x92",
      "\xc3\xb1"         => "\xc3\x91",
      "\xc3\xb0"         => "\xc3\x90",
      "\xc3\xaf"         => "\xc3\x8f",
      "\xc3\xae"         => "\xc3\x8e",
      "\xc3\xad"         => "\xc3\x8d",
      "\xc3\xac"         => "\xc3\x8c",
      "\xc3\xab"         => "\xc3\x8b",
      "\xc3\xaa"         => "\xc3\x8a",
      "\xc3\xa9"         => "\xc3\x89",
      "\xc3\xa8"         => "\xc3\x88",
      "\xc3\xa7"         => "\xc3\x87",
      "\xc3\xa6"         => "\xc3\x86",
      "\xc3\xa5"         => "\xc3\x85",
      "\xc3\xa4"         => "\xc3\x84",
      "\xc3\xa3"         => "\xc3\x83",
      "\xc3\xa2"         => "\xc3\x82",
      "\xc3\xa1"         => "\xc3\x81",
      "\xc3\xa0"         => "\xc3\x80",
      "\xc2\xb5"         => "\xce\x9c",
      "\x7a"             => "\x5a",
      "\x79"             => "\x59",
      "\x78"             => "\x58",
      "\x77"             => "\x57",
      "\x76"             => "\x56",
      "\x75"             => "\x55",
      "\x74"             => "\x54",
      "\x73"             => "\x53",
      "\x72"             => "\x52",
      "\x71"             => "\x51",
      "\x70"             => "\x50",
      "\x6f"             => "\x4f",
      "\x6e"             => "\x4e",
      "\x6d"             => "\x4d",
      "\x6c"             => "\x4c",
      "\x6b"             => "\x4b",
      "\x6a"             => "\x4a",
      "\x69"             => "\x49",
      "\x68"             => "\x48",
      "\x67"             => "\x47",
      "\x66"             => "\x46",
      "\x65"             => "\x45",
      "\x64"             => "\x44",
      "\x63"             => "\x43",
      "\x62"             => "\x42",
      "\x61"             => "\x41"

    );

    return $case;
  }

  /**
   * count the number of sub string occurrences
   *
   * @param    string $haystack The string to search in
   * @param    string $needle   The string to search for
   * @param    int    $offset   The offset where to start counting
   * @param    int    $length   The maximum length after the specified offset to search for the substring.
   *
   * @return   int number of occurrences of $needle
   */
  static public function substr_count($haystack, $needle, $offset = 0, $length = null)
  {
    if ($offset || $length) {
      $haystack = self::substr($haystack, $offset, $length);
    }

    return ($length === null ? substr_count($haystack, $needle, $offset) : substr_count($haystack, $needle, $offset, $length));
  }

  /**
   * checks if a string is 7 bit ASCII
   *
   * @param    string $str The string to check
   *
   * @return   bool True if ASCII, False otherwise
   */
  static public function is_ascii($str)
  {
    return ( bool )!preg_match('/[\x80-\xFF]/', $str);
  }

  /**
   * create an array containing a range of UTF-8 characters
   *
   * @param    mixed $var1 Numeric or hexadecimal code points, or a UTF-8 character to start from
   * @param    mixed $var2 Numeric or hexadecimal code points, or a UTF-8 character to end at
   *
   * @return   array Array of UTF-8 characters
   */
  static public function range($var1, $var2)
  {
    if (ctype_digit(( string )$var1)) {
      $start = ( int )$var1;
    } else if (!($start = ( int )self::hex_to_int($var1))) {
      //if not u+0000 style codepoint

      if (!($start = self::ord($var1))) {
        //if not a valid utf8 character

        return array();
      }
    }

    if (ctype_digit(( string )$var2)) {
      $end = ( int )$var2;
    } else if (!($end = ( int )self::hex_to_int($var2))) {
      //if not u+0000 style codepoint

      if (!($end = self::ord($var1))) {
        //if not a valid utf8 character

        return array();
      }
    }

    return array_map(
        array(
            'UTF8',
            'chr'
        ), range($start, $end)
    );
  }

  /**
   * creates a random string of UTF-8 characters
   *
   * @param    int $len The length of string in characters
   *
   * @return   string String consisting of random characters
   */
  static public function hash($len = 8)
  {
    static $chars = array();
    static $chars_len = null;

    self::checkForSupport();

    if (!$chars) {
      if (self::$support['pcre_utf8'] === true) {
        $chars = array_map(
            array(
                'UTF8',
                'chr'
            ), range(48, 0xffff)
        );

        $chars = preg_replace('/[^\p{N}\p{Lu}\p{Ll}]/u', '', $chars);

        $chars = array_values(array_filter($chars));
      } else {
        $chars = array_merge(range('0', '9'), range('A', 'Z'), range('a', 'z'));
      }

      $chars_len = count($chars);
    }

    $hash = '';

    for (; $len; --$len) {
      $hash .= $chars[mt_rand() % $chars_len];
    }

    return $hash;
  }

  /**
   * callback( )
   *
   * @alias of UTF8::chr_map( )
   */
  static public function callback($callback, $str)
  {
    return self::chr_map($callback, $str);
  }

  /**
   * applies callback to all characters of a string
   *
   * @param    string $callback The callback function
   * @param    string $str      UTF-8 string to run callback on
   *
   * @return   array The outcome of callback
   */

  static public function chr_map($callback, $str)
  {
    $chars = self::split($str);

    return array_map($callback, $chars);
  }

  /**
   * returns a single UTF-8 character from string.
   *
   * @param    string $string UTF-8 string
   * @param    int    $pos    The position of character to return.
   *
   * @return   string Single Multi-Byte character
   */
  static public function access($string, $pos)
  {
    //return the character at the specified position: $str[1] like functionality

    return self::substr($string, $pos, 1);
  }

  /**
   * sort all characters according to code points
   *
   * @param    string $str    UTF-8 string
   * @param    bool   $unique Sort unique. If true, repeated characters are ignored
   * @param    bool   $desc   If true, will sort characters in reverse code point order.
   *
   * @return   string String of sorted characters
   */
  static public function str_sort($str, $unique = false, $desc = false)
  {
    $array = self::codepoints($str);

    if ($unique) {
      $array = array_flip(array_flip($array));
    }

    if ($desc) {
      arsort($array);
    } else {
      asort($array);
    }

    return self::string($array);
  }

  /**
   * makes a UTF-8 string from Code  points
   *
   * @param    array $array Integer or Hexadecimal codepoints
   *
   * @return   string UTF-8 encoded string
   */
  static public function string($array)
  {
    return implode(
        array_map(
            array(
                'UTF8',
                'chr'
            ), $array
        )
    );
  }

  /**
   * strip HTML and PHP tags from a string
   *
   * @since 1.2
   *
   * @param    string $string         UTF-8 string
   * @param    string $allowable_tags The tags to allow in the string.
   *
   * @return   string The stripped string.
   */
  static public function strip_tags($string, $allowable_tags = '')
  {
    //clean broken utf8
    $string = self::clean($string);

    return strip_tags($string, $allowable_tags);
  }

  /**
   * pad a UTF-8 string to given length with another string
   *
   * @param    string $input      The input string
   * @param    int    $pad_length The length of return string
   * @param    string $pad_string String to use for padding the input string
   * @param    int    $pad_type   can be STR_PAD_RIGHT, STR_PAD_LEFT or STR_PAD_BOTH
   *
   * @return   string Returns the padded string
   */
  static public function str_pad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT)
  {
    $input_length = self::strlen($input);

    if (is_int($pad_length) && ($pad_length > 0) && ($pad_length >= $input_length)) {
      $ps_length = self::strlen($pad_string);

      $diff = $pad_length - $input_length;

      switch ($pad_type) {
        case STR_PAD_LEFT:
          $pre = str_repeat($pad_string, ( int )ceil($diff / $ps_length));
          $pre = self::substr($pre, 0, $diff);
          $post = '';
          break;

        case STR_PAD_BOTH:
          $pre = str_repeat($pad_string, ( int )ceil($diff / $ps_length / 2));
          $pre = self::substr($pre, 0, ( int )$diff / 2);
          $post = str_repeat($pad_string, ( int )ceil($diff / $ps_length / 2));
          $post = self::substr($post, 0, ( int )ceil($diff / 2));
          break;

        case STR_PAD_RIGHT:
        default:
          $post = str_repeat($pad_string, ( int )ceil($diff / $ps_length));
          $post = self::substr($post, 0, $diff);
          $pre = '';
      }

      return $pre . $input . $post;
    }

    return $input;
  }

  /**
   * removes duplicate occurrences of a string in another string
   *
   * @param    string $str  The base string
   * @param    string $what String to search for in the base string
   *
   * @return   string The result string with removed duplicates
   */
  static public function remove_duplicates($str, $what = ' ')
  {
    if (is_string($what)) {
      $what = array($what);
    }

    if (is_array($what)) {
      foreach ($what as $item) {
        $str = preg_replace('/(' . preg_quote($item, '/') . ')+/', $item, $str);
      }
    }

    return $str;
  }

  /**
   * strip whitespace or other characters from beginning or end of a UTF-8 string
   *
   * @param    string $string The string to be trimmed
   * @param    string $chars  Optional characters to be stripped
   *
   * @return   string The trimmed string
   */
  static public function trim($string = '', $chars = '')
  {
    $string = self::ltrim($string, $chars);

    return self::rtrim($string, $chars);
  }

  /**
   * strip whitespace or other characters from beginning of a UTF-8 string
   *
   * @param    string $string The string to be trimmed
   * @param    string $chars  Optional characters to be stripped
   *
   * @return   string The string with unwanted characters stripped from the left
   */
  static public function ltrim($string = '', $chars = '')
  {
    self::trim_util($string, $chars);

    $s_look = self::split($string);

    $count = 0;

    while (isset($s_look[$count]) && isset($chars[$s_look[$count]])) {
      ++$count;
    }

    return self::substr($string, $count);
  }

  /**
   * for internal use - Prepares a string and given chars for trim operations
   *
   * @param    string $string The string to be trimmed
   * @param    string $chars  Optional characters to be stripped
   */
  static public function trim_util(&$string, &$chars)
  {
    if (empty($chars)) {
      $chars = self::ws();
    } else if (is_string($chars)) {
      $chars = self::split($chars);
    }

    $chars = array_flip($chars);

    $string = self::clean($string); //Cleanup is necessary here, or trim functions may break
  }

  /**
   * returns an array of Unicode White Space characters
   *
   * @return   array An array with numeric code point as key and White Space Character as value
   */
  static public function ws()
  {
    Static $white = array(

      //    Numeric Code Point    => UTF-8 Character

      0     => "\x0",
      //NUL Byte
      9     => "\x9",
      //Tab
      10    => "\xa",
      //New Line
      11    => "\xb",
      //Vertical Tab
      13    => "\xd",
      //Carriage Return
      32    => "\x20",
      //Ordinary Space
      160   => "\xc2\xa0",
      //NO-BREAK SPACE
      5760  => "\xe1\x9a\x80",
      //OGHAM SPACE MARK
      6158  => "\xe1\xa0\x8e",
      //MONGOLIAN VOWEL SEPARATOR
      8192  => "\xe2\x80\x80",
      //EN QUAD
      8193  => "\xe2\x80\x81",
      //EM QUAD
      8194  => "\xe2\x80\x82",
      //EN SPACE
      8195  => "\xe2\x80\x83",
      //EM SPACE
      8196  => "\xe2\x80\x84",
      //THREE-PER-EM SPACE
      8197  => "\xe2\x80\x85",
      //FOUR-PER-EM SPACE
      8198  => "\xe2\x80\x86",
      //SIX-PER-EM SPACE
      8199  => "\xe2\x80\x87",
      //FIGURE SPACE
      8200  => "\xe2\x80\x88",
      //PUNCTUATION SPACE
      8201  => "\xe2\x80\x89",
      //THIN SPACE
      8202  => "\xe2\x80\x8a",
      //HAIR SPACE
      8232  => "\xe2\x80\xa8",
      //LINE SEPARATOR
      8233  => "\xe2\x80\xa9",
      //PARAGRAPH SEPARATOR
      8239  => "\xe2\x80\xaf",
      //NARROW NO-BREAK SPACE
      8287  => "\xe2\x81\x9f",
      //MEDIUM MATHEMATICAL SPACE
      12288 => "\xe3\x80\x80"
      //IDEOGRAPHIC SPACE
    );

    return $white;
  }

  /**
   * strip whitespace or other characters from end of a UTF-8 string
   *
   * @param    string $string The string to be trimmed
   * @param    string $chars  Optional characters to be stripped
   *
   * @return   string The string with unwanted characters stripped from the right
   */
  static public function rtrim($string = '', $chars = '')
  {
    self::trim_util($string, $chars);

    $s_look = self::split($string);

    $len = count($s_look);

    while ($len && isset($chars[$s_look[$len - 1]])) {
      --$len;
    }

    return self::substr($string, 0, $len);
  }

  /**
   * Finds position of first occurrence of a string within another, case insensitive
   *
   * @link http://php.net/manual/en/function.mb-stripos.php
   *
   * @param string $haystack <p>
   *                         The string from which to get the position of the first occurrence
   *                         of needle
   *                         </p>
   * @param string $needle   <p>
   *                         The string to find in haystack
   *                         </p>
   * @param int    $offset   [optional] <p>
   *                         The position in haystack
   *                         to start searching
   *                         </p>
   *
   * @return int Return the numeric position of the first occurrence of
   * needle in the haystack
   * string, or false if needle is not found.
   */
  static public function stripos($haystack, $needle, $offset = null)
  {
    self::checkForSupport();

    if (self::$support['mbstring'] === true) {
      return mb_stripos($haystack, $needle, $offset);
    } else {
      return self::strpos(self::strtolower($haystack), self::strtolower($needle), $offset);
    }
  }

  /**
   * Find position of first occurrence of string in a string
   *
   * @link http://php.net/manual/en/function.mb-strpos.php
   *
   * @param string $haystack <p>
   *                         The string being checked.
   *                         </p>
   * @param string $needle   <p>
   *                         The position counted from the beginning of haystack.
   *                         </p>
   * @param int    $offset   [optional] <p>
   *                         The search offset. If it is not specified, 0 is used.
   *                         </p>
   *
   * @return int the numeric position of
   * the first occurrence of needle in the
   * haystack string. If
   * needle is not found, it returns false.
   */
  static public function strpos($haystack, $needle, $offset = null)
  {
    self::checkForSupport();
    //iconv and mbstring do not support integer $needle

    if ((( int )$needle) === $needle && ($needle >= 0)) {
      $needle = self::chr($needle);
    }

    $needle = self::clean(( string )$needle);

    $offset = ( int )$offset;

    //mb_strpos returns wrong position if invalid characters are found in $haystack before $needle
    //iconv_strpos is not tolerant to invalid characters

    $haystack = self::clean($haystack);

    if (self::$support['mbstring'] === true) {
      return mb_strpos($haystack, $needle, $offset, 'UTF-8');
    }

    if (self::$support['iconv'] === true) {
      return iconv_strpos($haystack, $needle, $offset, 'UTF-8');
    }

    if ($offset > 0) {
      $haystack = self::substr($haystack, $offset);
    }

    //Negative Offset not supported in PHP strpos(), ignoring

    if (($pos = strpos($haystack, $needle)) !== false) {
      $left = substr($haystack, 0, $pos);

      return ($offset > 0 ? $offset : 0) + self::strlen($left);
    }

    return false;
  }

  /**
   * clean-up a UTF-8 string and show only printable chars at the end
   *
   * @param $text
   *
   * @return string
   */
  static public function cleanup($text)
  {
    self::checkForSupport();

    // strip invalid characters from UTF-8 strings (so that we can insert it into the db)
    if (self::$support['mbstring'] === true) {
      $text = mb_convert_encoding($text, 'UTF-8');
    } else if (function_exists('iconv')) {
      $text = iconv('UTF-8', 'UTF-8//TRANSLIT//IGNORE', $text);
    }

    // remove all none UTF-8 symbols
    $text = self::clean($text);

    // remove non-breaking spaces and other non-standard spaces
    //$text = preg_replace('/\s+/u', ' ', $text);

    // replace controls symbols with nothing
    //$text = preg_replace('/\p{C}+/u', '', $text);

    return (string)$text;
  }

  /**
   * makes string's first char Uppercase
   *
   * @param    string $str The input string
   *
   * @return   string The resulting string
   */
  static public function ucfirst($str)
  {
    return self::strtoupper(self::substr($str, 0, 1)) . self::substr($str, 1);
  }

  /**
   * Make a string uppercase
   *
   * @link http://php.net/manual/en/function.mb-strtoupper.php
   *
   * @param string $str <p>
   *                    The string being uppercased.
   *                    </p>
   *
   * @return string str with all alphabetic characters converted to uppercase.
   */
  static public function strtoupper($str)
  {
    self::checkForSupport();

    if (self::$support['mbstring'] === true) {
      return mb_strtoupper($str, 'UTF-8');
    } else {

      // fallback
      if (empty($table)) {
        $table = self::case_table();
      }

      $str = self::clean($str);

      return strtr($str, $table);
    }
  }

  /**
   * makes string's first char Lowercase
   *
   * @param    string $str The input string
   *
   * @return   string The resulting string
   */
  static public function lcfirst($str)
  {
    return self::strtolower(self::substr($str, 0, 1)) . self::substr($str, 1);
  }

  /**
   * find position of last occurrence of a case-insensitive string
   *
   * @param    string $haystack The string to look in
   * @param    string $needle   The string to look for
   * @param    int    $offset   (Optional) Number of characters to ignore in the begining or end
   *
   * @return   int The position of offset
   */
  static public function strripos($haystack, $needle, $offset = 0)
  {
    return self::strrpos(self::strtolower($haystack), self::strtolower($needle), $offset);
  }

  /**
   * Find position of last occurrence of a string in a string
   *
   * @link http://php.net/manual/en/function.mb-strrpos.php
   *
   * @param string $haystack <p>
   *                         The string being checked, for the last occurrence
   *                         of needle
   *                         </p>
   * @param string $needle   <p>
   *                         The string to find in haystack.
   *                         </p>
   * @param int    $offset   [optional] May be specified to begin searching an arbitrary number of characters into
   *                         the string. Negative values will stop searching at an arbitrary point
   *                         prior to the end of the string.
   *
   * @return int the numeric position of
   * the last occurrence of needle in the
   * haystack string. If
   * needle is not found, it returns false.
   */
  static public function strrpos($haystack, $needle, $offset = null)
  {
    self::checkForSupport();

    if ((( int )$needle) === $needle && ($needle >= 0)) {
      $needle = self::chr($needle);
    }

    $needle = self::clean(( string )$needle);

    $offset = ( int )$offset;

    $haystack = self::clean($haystack);

    if (self::$support['mbstring'] === true) {
      // mb_strrpos returns wrong position if invalid characters are found in $haystack before $needle
      return mb_strrpos($haystack, $needle, $offset, 'UTF-8');
    }

    if (self::$support['iconv'] === true && $offset === 0) {
      //iconv_strrpos is not tolerant to invalid characters
      //iconv_strrpos does not accept $offset

      return iconv_strrpos($haystack, $needle, 'UTF-8');
    }

    if ($offset > 0) {
      $haystack = self::substr($haystack, $offset);
    } else if ($offset < 0) {
      $haystack = self::substr($haystack, 0, $offset);
    }

    if (($pos = strrpos($haystack, $needle)) !== false) {
      $left = substr($haystack, 0, $pos);

      return ($offset > 0 ? $offset : 0) + self::strlen($left);
    }

    return false;
  }

  /**
   * splits a string into smaller chunks and multiple lines, using the specified
   * line ending character
   *
   * @param    string $body     The original string to be split.
   * @param    int    $chunklen The maximum character length of a chunk
   * @param    string $end      The character(s) to be inserted at the end of each chunk
   *
   * @return   string The chunked string
   */

  static public function chunk_split($body, $chunklen = 76, $end = "\r\n")
  {
    return implode($end, self::split($body, $chunklen));
  }

  /**
   * returns an array with all utf8 whitespace characters as per
   * http://www.bogofilter.org/pipermail/bogofilter/2003-March/001889.html
   *
   * @author: Derek E. derek.isname@gmail.com
   *
   * @return array an array with all known whitespace characters as values and the type of whitespace as keys
   *         as defined in above URL
   */
  static public function whitespace_table() {
    $whitespace = array(
        "SPACE"                     => "\x20",
        "NO-BREAK SPACE"            => "\xc2\xa0",
        "OGHAM SPACE MARK"          => "\xe1\x9a\x80",
        "EN QUAD"                   => "\xe2\x80\x80",
        "EM QUAD"                   => "\xe2\x80\x81",
        "EN SPACE"                  => "\xe2\x80\x82",
        "EM SPACE"                  => "\xe2\x80\x83",
        "THREE-PER-EM SPACE"        => "\xe2\x80\x84",
        "FOUR-PER-EM SPACE"         => "\xe2\x80\x85",
        "SIX-PER-EM SPACE"          => "\xe2\x80\x86",
        "FIGURE SPACE"              => "\xe2\x80\x87",
        "PUNCTUATION SPACE"         => "\xe2\x80\x88",
        "THIN SPACE"                => "\xe2\x80\x89",
        "HAIR SPACE"                => "\xe2\x80\x8a",
        "ZERO WIDTH SPACE"          => "\xe2\x80\x8b",
        "NARROW NO-BREAK SPACE"     => "\xe2\x80\xaf",
        "MEDIUM MATHEMATICAL SPACE" => "\xe2\x81\x9f",
        "IDEOGRAPHIC SPACE"         => "\xe3\x80\x80",
    );

    return $whitespace;
  }

} 
