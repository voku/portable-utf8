<?php

declare(strict_types=1);

namespace voku\helper;

/**
 * UTF8-Helper-Class
 *
 * @package voku\helper
 */
final class UTF8
{
  // (CRLF|([ZWNJ-ZWJ]|T+|L*(LV?V+|LV|LVT)T*|L+|[^Control])[Extend]*|[Control])
  // This regular expression is a work around for http://bugs.exim.org/1279
  const GRAPHEME_CLUSTER_RX = "(?:\r\n|(?:[ -~\x{200C}\x{200D}]|[ᆨ-ᇹ]+|[ᄀ-ᅟ]*(?:[가개갸걔거게겨계고과괘괴교구궈궤귀규그긔기까깨꺄꺠꺼께껴꼐꼬꽈꽤꾀꾜꾸꿔꿰뀌뀨끄끠끼나내냐냬너네녀녜노놔놰뇌뇨누눠눼뉘뉴느늬니다대댜댸더데뎌뎨도돠돼되됴두둬뒈뒤듀드듸디따때땨떄떠떼뗘뗴또똬뙈뙤뚀뚜뚸뛔뛰뜌뜨띄띠라래랴럐러레려례로롸뢔뢰료루뤄뤠뤼류르릐리마매먀먜머메며몌모뫄뫠뫼묘무뭐뭬뮈뮤므믜미바배뱌뱨버베벼볘보봐봬뵈뵤부붜붸뷔뷰브븨비빠빼뺘뺴뻐뻬뼈뼤뽀뽜뽸뾔뾰뿌뿨쀄쀠쀼쁘쁴삐사새샤섀서세셔셰소솨쇄쇠쇼수숴쉐쉬슈스싀시싸쌔쌰썌써쎄쎠쎼쏘쏴쐐쐬쑈쑤쒀쒜쒸쓔쓰씌씨아애야얘어에여예오와왜외요우워웨위유으의이자재쟈쟤저제져졔조좌좨죄죠주줘줴쥐쥬즈즤지짜째쨔쨰쩌쩨쪄쪠쪼쫘쫴쬐쬬쭈쭤쮀쮜쮸쯔쯰찌차채챠챼처체쳐쳬초촤쵀최쵸추춰췌취츄츠츼치카캐캬컈커케켜켸코콰쾌쾨쿄쿠쿼퀘퀴큐크킈키타태탸턔터테텨톄토톼퇘퇴툐투퉈퉤튀튜트틔티파패퍄퍠퍼페펴폐포퐈퐤푀표푸풔풰퓌퓨프픠피하해햐햬허헤혀혜호화홰회효후훠훼휘휴흐희히]?[ᅠ-ᆢ]+|[가-힣])[ᆨ-ᇹ]*|[ᄀ-ᅟ]+|[^\p{Cc}\p{Cf}\p{Zl}\p{Zp}])[\p{Mn}\p{Me}\x{09BE}\x{09D7}\x{0B3E}\x{0B57}\x{0BBE}\x{0BD7}\x{0CC2}\x{0CD5}\x{0CD6}\x{0D3E}\x{0D57}\x{0DCF}\x{0DDF}\x{200C}\x{200D}\x{1D165}\x{1D16E}-\x{1D172}]*|[\p{Cc}\p{Cf}\p{Zl}\p{Zp}])";

  /**
   * Bom => Byte-Length
   *
   * INFO: https://en.wikipedia.org/wiki/Byte_order_mark
   *
   * @var array
   */
  private static $BOM = [
      "\xef\xbb\xbf"     => 3, // UTF-8 BOM
      'ï»¿'              => 6, // UTF-8 BOM as "WINDOWS-1252" (one char has [maybe] more then one byte ...)
      "\x00\x00\xfe\xff" => 4, // UTF-32 (BE) BOM
      '  þÿ'             => 6, // UTF-32 (BE) BOM as "WINDOWS-1252"
      "\xff\xfe\x00\x00" => 4, // UTF-32 (LE) BOM
      'ÿþ  '             => 6, // UTF-32 (LE) BOM as "WINDOWS-1252"
      "\xfe\xff"         => 2, // UTF-16 (BE) BOM
      'þÿ'               => 4, // UTF-16 (BE) BOM as "WINDOWS-1252"
      "\xff\xfe"         => 2, // UTF-16 (LE) BOM
      'ÿþ'               => 4, // UTF-16 (LE) BOM as "WINDOWS-1252"
  ];

  /**
   * Numeric code point => UTF-8 Character
   *
   * url: http://www.w3schools.com/charsets/ref_utf_punctuation.asp
   *
   * @var array
   */
  private static $WHITESPACE = [
    // NUL Byte
    0     => "\x0",
    // Tab
    9     => "\x9",
    // New Line
    10    => "\xa",
    // Vertical Tab
    11    => "\xb",
    // Carriage Return
    13    => "\xd",
    // Ordinary Space
    32    => "\x20",
    // NO-BREAK SPACE
    160   => "\xc2\xa0",
    // OGHAM SPACE MARK
    5760  => "\xe1\x9a\x80",
    // MONGOLIAN VOWEL SEPARATOR
    6158  => "\xe1\xa0\x8e",
    // EN QUAD
    8192  => "\xe2\x80\x80",
    // EM QUAD
    8193  => "\xe2\x80\x81",
    // EN SPACE
    8194  => "\xe2\x80\x82",
    // EM SPACE
    8195  => "\xe2\x80\x83",
    // THREE-PER-EM SPACE
    8196  => "\xe2\x80\x84",
    // FOUR-PER-EM SPACE
    8197  => "\xe2\x80\x85",
    // SIX-PER-EM SPACE
    8198  => "\xe2\x80\x86",
    // FIGURE SPACE
    8199  => "\xe2\x80\x87",
    // PUNCTUATION SPACE
    8200  => "\xe2\x80\x88",
    // THIN SPACE
    8201  => "\xe2\x80\x89",
    //HAIR SPACE
    8202  => "\xe2\x80\x8a",
    // LINE SEPARATOR
    8232  => "\xe2\x80\xa8",
    // PARAGRAPH SEPARATOR
    8233  => "\xe2\x80\xa9",
    // NARROW NO-BREAK SPACE
    8239  => "\xe2\x80\xaf",
    // MEDIUM MATHEMATICAL SPACE
    8287  => "\xe2\x81\x9f",
    // IDEOGRAPHIC SPACE
    12288 => "\xe3\x80\x80",
  ];

  /**
   * @var array
   */
  private static $WHITESPACE_TABLE = [
      'SPACE'                     => "\x20",
      'NO-BREAK SPACE'            => "\xc2\xa0",
      'OGHAM SPACE MARK'          => "\xe1\x9a\x80",
      'EN QUAD'                   => "\xe2\x80\x80",
      'EM QUAD'                   => "\xe2\x80\x81",
      'EN SPACE'                  => "\xe2\x80\x82",
      'EM SPACE'                  => "\xe2\x80\x83",
      'THREE-PER-EM SPACE'        => "\xe2\x80\x84",
      'FOUR-PER-EM SPACE'         => "\xe2\x80\x85",
      'SIX-PER-EM SPACE'          => "\xe2\x80\x86",
      'FIGURE SPACE'              => "\xe2\x80\x87",
      'PUNCTUATION SPACE'         => "\xe2\x80\x88",
      'THIN SPACE'                => "\xe2\x80\x89",
      'HAIR SPACE'                => "\xe2\x80\x8a",
      'LINE SEPARATOR'            => "\xe2\x80\xa8",
      'PARAGRAPH SEPARATOR'       => "\xe2\x80\xa9",
      'ZERO WIDTH SPACE'          => "\xe2\x80\x8b",
      'NARROW NO-BREAK SPACE'     => "\xe2\x80\xaf",
      'MEDIUM MATHEMATICAL SPACE' => "\xe2\x81\x9f",
      'IDEOGRAPHIC SPACE'         => "\xe3\x80\x80",
  ];

  /**
   * bidirectional text chars
   *
   * url: https://www.w3.org/International/questions/qa-bidi-unicode-controls
   *
   * @var array
   */
  private static $BIDI_UNI_CODE_CONTROLS_TABLE = [
    // LEFT-TO-RIGHT EMBEDDING (use -> dir = "ltr")
    8234 => "\xE2\x80\xAA",
    // RIGHT-TO-LEFT EMBEDDING (use -> dir = "rtl")
    8235 => "\xE2\x80\xAB",
    // POP DIRECTIONAL FORMATTING // (use -> </bdo>)
    8236 => "\xE2\x80\xAC",
    // LEFT-TO-RIGHT OVERRIDE // (use -> <bdo dir = "ltr">)
    8237 => "\xE2\x80\xAD",
    // RIGHT-TO-LEFT OVERRIDE // (use -> <bdo dir = "rtl">)
    8238 => "\xE2\x80\xAE",
    // LEFT-TO-RIGHT ISOLATE // (use -> dir = "ltr")
    8294 => "\xE2\x81\xA6",
    // RIGHT-TO-LEFT ISOLATE // (use -> dir = "rtl")
    8295 => "\xE2\x81\xA7",
    // FIRST STRONG ISOLATE // (use -> dir = "auto")
    8296 => "\xE2\x81\xA8",
    // POP DIRECTIONAL ISOLATE
    8297 => "\xE2\x81\xA9",
  ];

  /**
   * @var array
   */
  private static $COMMON_CASE_FOLD = [
      'upper' => [
          'µ',
          'ſ',
          "\xCD\x85",
          'ς',
          'ẞ',
          "\xCF\x90",
          "\xCF\x91",
          "\xCF\x95",
          "\xCF\x96",
          "\xCF\xB0",
          "\xCF\xB1",
          "\xCF\xB5",
          "\xE1\xBA\x9B",
          "\xE1\xBE\xBE",
      ],
      'lower' => [
          'μ',
          's',
          'ι',
          'σ',
          'ß',
          'β',
          'θ',
          'φ',
          'π',
          'κ',
          'ρ',
          'ε',
          "\xE1\xB9\xA1",
          'ι',
      ],
  ];


  /**
   * @var array
   */
  private static $SUPPORT = [];

  /**
   * @var null|array
   */
  private static $UTF8_MSWORD;

  /**
   * @var null|array
   */
  private static $BROKEN_UTF8_FIX;

  /**
   * @var null|array
   */
  private static $WIN1252_TO_UTF8;

  /**
   * @var null|array
   */
  private static $ENCODINGS;

  /**
   * @var null|array
   */
  private static $ORD;

  /**
   * @var null|array
   */
  private static $CHR;

  /**
   * __construct()
   */
  public function __construct()
  {
    self::checkForSupport();
  }

  /**
   * Return the character at the specified position: $str[1] like functionality.
   *
   * @param string $str <p>A UTF-8 string.</p>
   * @param int    $pos <p>The position of character to return.</p>
   *
   * @return string Single Multi-Byte character.
   */
  public static function access(string $str, int $pos): string
  {
    if ('' === $str) {
      return '';
    }

    if ($pos < 0) {
      return '';
    }

    return (string)self::substr($str, $pos, 1);
  }

  /**
   * Prepends UTF-8 BOM character to the string and returns the whole string.
   *
   * INFO: If BOM already existed there, the Input string is returned.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return string The output string that contains BOM.
   */
  public static function add_bom_to_string(string $str): string
  {
    if (self::string_has_bom($str) === false) {
      $str = self::bom() . $str;
    }

    return $str;
  }

  /**
   * Adds the specified amount of left and right padding to the given string.
   * The default character used is a space.
   *
   * @param string $str
   * @param int    $left     [optional] <p>Length of left padding. Default: 0</p>
   * @param int    $right    [optional] <p>Length of right padding. Default: 0</p>
   * @param string $padStr   [optional] <p>String used to pad. Default: ' '</p>
   * @param string $encoding [optional] <p>Default: UTF-8</p>
   *
   * @return string String with padding applied.
   */
  private static function apply_padding(string $str, int $left = 0, int $right = 0, string $padStr = ' ', string $encoding): string
  {
    $strlen = self::strlen($str, $encoding);

    if ($left && $right) {
      $length = ($left + $right) + $strlen;
      $type = STR_PAD_BOTH;
    } elseif ($left) {
      $length = $left + $strlen;
      $type = STR_PAD_LEFT;
    } elseif ($right) {
      $length = $right + $strlen;
      $type = STR_PAD_RIGHT;
    } else {
      $length = ($left + $right) + $strlen;
      $type = STR_PAD_BOTH;
    }

    return self::str_pad($str, $length, $padStr, $type, $encoding);
  }

  /**
   * Changes all keys in an array.
   *
   * @param array $array <p>The array to work on</p>
   * @param int   $case  [optional] <p> Either <strong>CASE_UPPER</strong><br>
   *                     or <strong>CASE_LOWER</strong> (default)</p>
   *
   * @return string[] An array with its keys lower or uppercased.
   */
  public static function array_change_key_case(array $array, int $case = CASE_LOWER): array
  {
    if (
        $case !== CASE_LOWER
        &&
        $case !== CASE_UPPER
    ) {
      $case = CASE_LOWER;
    }

    $return = [];
    foreach ($array as $key => $value) {
      if ($case === CASE_LOWER) {
        $key = self::strtolower($key);
      } else {
        $key = self::strtoupper($key);
      }

      $return[$key] = $value;
    }

    return $return;
  }

  /**
   * Returns the substring between $start and $end, if found, or an empty
   * string. An optional offset may be supplied from which to begin the
   * search for the start string.
   *
   * @param string $str
   * @param string $start    <p>Delimiter marking the start of the substring.</p>
   * @param string $end      <p>Delimiter marking the end of the substring.</p>
   * @param int    $offset   [optional] <p>Index from which to begin the search. Default: 0</p>
   * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return string
   */
  public static function between(string $str, string $start, string $end, int $offset = 0, string $encoding = 'UTF-8'): string
  {
    $posStart = self::strpos($str, $start, $offset, $encoding);
    if ($posStart === false) {
      return '';
    }

    $substrIndex = $posStart + self::strlen($start, $encoding);
    $posEnd = self::strpos($str, $end, $substrIndex, $encoding);
    if (
        $posEnd === false
        ||
        $posEnd === $substrIndex
    ) {
      return '';
    }

    $return = self::substr($str, $substrIndex, $posEnd - $substrIndex, $encoding);

    if ($return === false) {
      return '';
    }

    return $return;
  }

  /**
   * Convert binary into an string.
   *
   * @param mixed $bin 1|0
   *
   * @return string
   */
  public static function binary_to_str($bin): string
  {
    if (!isset($bin[0])) {
      return '';
    }

    $convert = \base_convert($bin, 2, 16);
    if ($convert === '0') {
      return '';
    }

    return \pack('H*', $convert);
  }

  /**
   * Returns the UTF-8 Byte Order Mark Character.
   *
   * INFO: take a look at UTF8::$bom for e.g. UTF-16 and UTF-32 BOM values
   *
   * @return string UTF-8 Byte Order Mark
   */
  public static function bom(): string
  {
    return "\xef\xbb\xbf";
  }

  /**
   * @alias of UTF8::chr_map()
   *
   * @see   UTF8::chr_map()
   *
   * @param string|array $callback
   * @param string       $str
   *
   * @return string[]
   */
  public static function callback($callback, string $str): array
  {
    return self::chr_map($callback, $str);
  }

  /**
   * Returns the character at $index, with indexes starting at 0.
   *
   * @param string $str
   * @param int    $index    <p>Position of the character.</p>
   * @param string $encoding [optional] <p>Default is UTF-8</p>
   *
   * @return string The character at $index.
   */
  public static function char_at(string $str, int $index, string $encoding = 'UTF-8'): string
  {
    return (string)self::substr($str, $index, 1, $encoding);
  }

  /**
   * Returns an array consisting of the characters in the string.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return string[] An array of chars.
   */
  public static function chars(string $str): array
  {
    return self::str_split($str, 1);
  }

  /**
   * This method will auto-detect your server environment for UTF-8 support.
   *
   * INFO: You don't need to run it manually, it will be triggered if it's needed.
   */
  public static function checkForSupport()
  {
    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {

      self::$SUPPORT['already_checked_via_portable_utf8'] = true;

      // http://php.net/manual/en/book.mbstring.php
      self::$SUPPORT['mbstring'] = self::mbstring_loaded();
      self::$SUPPORT['mbstring_func_overload'] = self::mbstring_overloaded();

      // http://php.net/manual/en/book.iconv.php
      self::$SUPPORT['iconv'] = self::iconv_loaded();

      // http://php.net/manual/en/book.intl.php
      self::$SUPPORT['intl'] = self::intl_loaded();
      self::$SUPPORT['intl__transliterator_list_ids'] = [];

      self::$SUPPORT['symfony_polyfill_used'] = self::symfony_polyfill_used();

      if (
          self::$SUPPORT['intl'] === true
          &&
          \function_exists('transliterator_list_ids') === true
      ) {
        /** @noinspection PhpComposerExtensionStubsInspection */
        self::$SUPPORT['intl__transliterator_list_ids'] = \transliterator_list_ids();
      }

      // http://php.net/manual/en/class.intlchar.php
      self::$SUPPORT['intlChar'] = self::intlChar_loaded();

      // http://php.net/manual/en/book.ctype.php
      self::$SUPPORT['ctype'] = self::ctype_loaded();

      // http://php.net/manual/en/class.finfo.php
      self::$SUPPORT['finfo'] = self::finfo_loaded();

      // http://php.net/manual/en/book.json.php
      self::$SUPPORT['json'] = self::json_loaded();

      // http://php.net/manual/en/book.pcre.php
      self::$SUPPORT['pcre_utf8'] = self::pcre_utf8_support();
    }
  }

  /**
   * Generates a UTF-8 encoded character from the given code point.
   *
   * INFO: opposite to UTF8::ord()
   *
   * @param int|string $code_point <p>The code point for which to generate a character.</p>
   * @param string     $encoding   [optional] <p>Default is UTF-8</p>
   *
   * @return string|null Multi-Byte character, returns null on failure or empty input.
   */
  public static function chr($code_point, string $encoding = 'UTF-8')
  {
    // init
    static $CHAR_CACHE = [];

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if (
        $encoding !== 'UTF-8'
        &&
        $encoding !== 'ISO-8859-1'
        &&
        $encoding !== 'WINDOWS-1252'
        &&
        self::$SUPPORT['mbstring'] === false
    ) {
      \trigger_error('UTF8::chr() without mbstring cannot handle "' . $encoding . '" encoding', E_USER_WARNING);
    }

    $cacheKey = $code_point . $encoding;
    if (isset($CHAR_CACHE[$cacheKey]) === true) {
      return $CHAR_CACHE[$cacheKey];
    }

    if ($code_point <= 127) { // use "simple"-char only until "\x80"

      if (self::$CHR === null) {
        $chrTmp = self::getData('chr');
        if ($chrTmp) {
          self::$CHR = (array)$chrTmp;
        }
      }

      $chr = self::$CHR[$code_point];

      if ($encoding !== 'UTF-8') {
        $chr = self::encode($encoding, $chr);
      }

      return $CHAR_CACHE[$cacheKey] = $chr;
    }

    if (self::$SUPPORT['intlChar'] === true) {
      /** @noinspection PhpComposerExtensionStubsInspection */
      $chr = \IntlChar::chr($code_point);

      if ($encoding !== 'UTF-8') {
        $chr = self::encode($encoding, $chr);
      }

      return $CHAR_CACHE[$cacheKey] = $chr;
    }

    if (self::$CHR === null) {
      $chrTmp = self::getData('chr');
      if ($chrTmp) {
        self::$CHR = (array)$chrTmp;
      }
    }

    $code_point = (int)$code_point;
    if ($code_point <= 0x7F) {
      $chr = self::$CHR[$code_point];
    } elseif ($code_point <= 0x7FF) {
      $chr = self::$CHR[($code_point >> 6) + 0xC0] .
             self::$CHR[($code_point & 0x3F) + 0x80];
    } elseif ($code_point <= 0xFFFF) {
      $chr = self::$CHR[($code_point >> 12) + 0xE0] .
             self::$CHR[(($code_point >> 6) & 0x3F) + 0x80] .
             self::$CHR[($code_point & 0x3F) + 0x80];
    } else {
      $chr = self::$CHR[($code_point >> 18) + 0xF0] .
             self::$CHR[(($code_point >> 12) & 0x3F) + 0x80] .
             self::$CHR[(($code_point >> 6) & 0x3F) + 0x80] .
             self::$CHR[($code_point & 0x3F) + 0x80];
    }

    if ($encoding !== 'UTF-8') {
      $chr = self::encode($encoding, $chr);
    }

    return $CHAR_CACHE[$cacheKey] = $chr;
  }

  /**
   * Applies callback to all characters of a string.
   *
   * @param string|array $callback <p>The callback function.</p>
   * @param string       $str      <p>UTF-8 string to run callback on.</p>
   *
   * @return string[] The outcome of callback.
   */
  public static function chr_map($callback, string $str): array
  {
    $chars = self::split($str);

    return \array_map($callback, $chars);
  }

  /**
   * Generates an array of byte length of each character of a Unicode string.
   *
   * 1 byte => U+0000  - U+007F
   * 2 byte => U+0080  - U+07FF
   * 3 byte => U+0800  - U+FFFF
   * 4 byte => U+10000 - U+10FFFF
   *
   * @param string $str <p>The original unicode string.</p>
   *
   * @return int[] An array of byte lengths of each character.
   */
  public static function chr_size_list(string $str): array
  {
    if ('' === $str) {
      return [];
    }

    $strSplit = self::split($str);

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['mbstring_func_overload'] === true) {
      return \array_map(
          function ($data) {
            return UTF8::strlen_in_byte($data);
          },
          $strSplit
      );
    }

    return \array_map('\strlen', $strSplit);
  }

  /**
   * Get a decimal code representation of a specific character.
   *
   * @param string $char <p>The input character.</p>
   *
   * @return int
   */
  public static function chr_to_decimal(string $char): int
  {
    $code = self::ord($char[0]);
    $bytes = 1;

    if (!($code & 0x80)) {
      // 0xxxxxxx
      return $code;
    }

    if (($code & 0xe0) === 0xc0) {
      // 110xxxxx
      $bytes = 2;
      $code &= ~0xc0;
    } elseif (($code & 0xf0) === 0xe0) {
      // 1110xxxx
      $bytes = 3;
      $code &= ~0xe0;
    } elseif (($code & 0xf8) === 0xf0) {
      // 11110xxx
      $bytes = 4;
      $code &= ~0xf0;
    }

    for ($i = 2; $i <= $bytes; $i++) {
      // 10xxxxxx
      $code = ($code << 6) + (self::ord($char[$i - 1]) & ~0x80);
    }

    return $code;
  }

  /**
   * Get hexadecimal code point (U+xxxx) of a UTF-8 encoded character.
   *
   * @param string|int $char <p>The input character</p>
   * @param string     $pfix [optional]
   *
   * @return string The code point encoded as U+xxxx
   */
  public static function chr_to_hex($char, string $pfix = 'U+'): string
  {
    if ('' === $char) {
      return '';
    }

    if ($char === '&#0;') {
      $char = '';
    }

    return self::int_to_hex(self::ord($char), $pfix);
  }

  /**
   * alias for "UTF8::chr_to_decimal()"
   *
   * @see UTF8::chr_to_decimal()
   *
   * @param string $chr
   *
   * @return int
   */
  public static function chr_to_int(string $chr): int
  {
    return self::chr_to_decimal($chr);
  }

  /**
   * Splits a string into smaller chunks and multiple lines, using the specified line ending character.
   *
   * @param string $body     <p>The original string to be split.</p>
   * @param int    $chunklen [optional] <p>The maximum character length of a chunk.</p>
   * @param string $end      [optional] <p>The character(s) to be inserted at the end of each chunk.</p>
   *
   * @return string The chunked string.
   */
  public static function chunk_split(string $body, int $chunklen = 76, string $end = "\r\n"): string
  {
    return \implode($end, self::split($body, $chunklen));
  }

  /**
   * Accepts a string and removes all non-UTF-8 characters from it + extras if needed.
   *
   * @param string $str                           <p>The string to be sanitized.</p>
   * @param bool   $remove_bom                    [optional] <p>Set to true, if you need to remove UTF-BOM.</p>
   * @param bool   $normalize_whitespace          [optional] <p>Set to true, if you need to normalize the
   *                                              whitespace.</p>
   * @param bool   $normalize_msword              [optional] <p>Set to true, if you need to normalize MS Word chars
   *                                              e.g.: "…"
   *                                              => "..."</p>
   * @param bool   $keep_non_breaking_space       [optional] <p>Set to true, to keep non-breaking-spaces, in
   *                                              combination with
   *                                              $normalize_whitespace</p>
   * @param bool   $replace_diamond_question_mark [optional] <p>Set to true, if you need to remove diamond question
   *                                              mark e.g.: "�"</p>
   * @param bool   $remove_invisible_characters   [optional] <p>Set to false, if you not want to remove invisible
   *                                              characters e.g.: "\0"</p>
   *
   * @return string Clean UTF-8 encoded string.
   */
  public static function clean(
      string $str,
      bool $remove_bom = false,
      bool $normalize_whitespace = false,
      bool $normalize_msword = false,
      bool $keep_non_breaking_space = false,
      bool $replace_diamond_question_mark = false,
      bool $remove_invisible_characters = true
  ): string
  {
    // http://stackoverflow.com/questions/1401317/remove-non-utf8-characters-from-string
    // caused connection reset problem on larger strings

    $regx = '/
      (
        (?: [\x00-\x7F]               # single-byte sequences   0xxxxxxx
        |   [\xC0-\xDF][\x80-\xBF]    # double-byte sequences   110xxxxx 10xxxxxx
        |   [\xE0-\xEF][\x80-\xBF]{2} # triple-byte sequences   1110xxxx 10xxxxxx * 2
        |   [\xF0-\xF7][\x80-\xBF]{3} # quadruple-byte sequence 11110xxx 10xxxxxx * 3
        ){1,100}                      # ...one or more times
      )
    | ( [\x80-\xBF] )                 # invalid byte in range 10000000 - 10111111
    | ( [\xC0-\xFF] )                 # invalid byte in range 11000000 - 11111111
    /x';
    $str = (string)\preg_replace($regx, '$1', $str);

    if ($replace_diamond_question_mark === true) {
      $str = self::replace_diamond_question_mark($str, '');
    }

    if ($remove_invisible_characters === true) {
      $str = self::remove_invisible_characters($str);
    }

    if ($normalize_whitespace === true) {
      $str = self::normalize_whitespace($str, $keep_non_breaking_space);
    }

    if ($normalize_msword === true) {
      $str = self::normalize_msword($str);
    }

    if ($remove_bom === true) {
      $str = self::remove_bom($str);
    }

    return $str;
  }

  /**
   * Clean-up a and show only printable UTF-8 chars at the end  + fix UTF-8 encoding.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return string
   */
  public static function cleanup($str): string
  {
    // init
    $str = (string)$str;

    if ('' === $str) {
      return '';
    }

    // fixed ISO <-> UTF-8 Errors
    $str = self::fix_simple_utf8($str);

    // remove all none UTF-8 symbols
    // && remove diamond question mark (�)
    // && remove remove invisible characters (e.g. "\0")
    // && remove BOM
    // && normalize whitespace chars (but keep non-breaking-spaces)
    $str = self::clean(
        $str,
        true,
        true,
        false,
        true,
        true,
        true
    );

    return $str;
  }

  /**
   * Accepts a string or a array of strings and returns an array of Unicode code points.
   *
   * INFO: opposite to UTF8::string()
   *
   * @param string|string[] $arg        <p>A UTF-8 encoded string or an array of such strings.</p>
   * @param bool            $u_style    <p>If True, will return code points in U+xxxx format,
   *                                    default, code points will be returned as integers.</p>
   *
   * @return array<int|string>
   *                           The array of code points:<br>
   *                           array<int> for $u_style === false<br>
   *                           array<string> for $u_style === true<br>
   */
  public static function codepoints($arg, bool $u_style = false): array
  {
    if (\is_string($arg) === true) {
      $arg = self::split($arg);
    }

    $arg = \array_map(
        [
            self::class,
            'ord',
        ],
        $arg
    );

    if (\count($arg) === 0) {
      return [];
    }

    if ($u_style) {
      $arg = \array_map(
          [
              self::class,
              'int_to_hex',
          ],
          $arg
      );
    }

    return $arg;
  }

  /**
   * Trims the string and replaces consecutive whitespace characters with a
   * single space. This includes tabs and newline characters, as well as
   * multibyte whitespace such as the thin space and ideographic space.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return string String with a trimmed $str and condensed whitespace.
   */
  public static function collapse_whitespace(string $str): string
  {
    return self::trim(
        self::regex_replace($str, '[[:space:]]+', ' ')
    );
  }

  /**
   * Returns count of characters used in a string.
   *
   * @param string $str       <p>The input string.</p>
   * @param bool   $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return int[] An associative array of Character as keys and
   *               their count as values.
   */
  public static function count_chars(string $str, bool $cleanUtf8 = false): array
  {
    return \array_count_values(self::split($str, 1, $cleanUtf8));
  }

  /**
   * Remove css media-queries.
   *
   * @param string $str
   *
   * @return string
   */
  public static function css_stripe_media_queries(string $str): string
  {
    return (string)\preg_replace(
        '#@media\\s+(?:only\\s)?(?:[\\s{\\(]|screen|all)\\s?[^{]+{.*}\\s*}\\s*#misU',
        '',
        $str
    );
  }

  /**
   * Checks whether ctype is available on the server.
   *
   * @return bool
   *              <strong>true</strong> if available, <strong>false</strong> otherwise.
   */
  public static function ctype_loaded(): bool
  {
    return \extension_loaded('ctype');
  }

  /**
   * Converts a int-value into an UTF-8 character.
   *
   * @param mixed $int
   *
   * @return string
   */
  public static function decimal_to_chr($int): string
  {
    return self::html_entity_decode('&#' . $int . ';', ENT_QUOTES | ENT_HTML5);
  }

  /**
   * Decodes a MIME header field
   *
   * @param string $str
   * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return string|false
   *                      A decoded MIME field on success,
   *                      or false if an error occurs during the decoding.
   */
  public static function decode_mimeheader($str, $encoding = 'UTF-8')
  {
    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['iconv'] === true) {
      return \iconv_mime_decode($str, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, $encoding);
    }

    if ($encoding != 'UTF-8') {
      $str = self::encode($encoding, $str);
    }

    return \mb_decode_mimeheader($str);
  }

  /**
   * Encode a string with a new charset-encoding.
   *
   * INFO:  The different to "UTF8::utf8_encode()" is that this function, try to fix also broken / double encoding,
   *        so you can call this function also on a UTF-8 String and you don't mess the string.
   *
   * @param string $toEncoding                  <p>e.g. 'UTF-16', 'UTF-8', 'ISO-8859-1', etc.</p>
   * @param string $str                         <p>The input string</p>
   * @param bool   $autodetectFromEncoding      [optional] <p>Force the new encoding (we try to fix broken / double
   *                                            encoding for UTF-8)<br> otherwise we auto-detect the current
   *                                            string-encoding</p>
   * @param string $fromEncoding                [optional] <p>e.g. 'UTF-16', 'UTF-8', 'ISO-8859-1', etc.<br>
   *                                            A empty string will trigger the autodetect anyway.</p>
   *
   * @return string
   */
  public static function encode(string $toEncoding, string $str, bool $autodetectFromEncoding = true, string $fromEncoding = ''): string
  {
    if ('' === $str || '' === $toEncoding) {
      return $str;
    }

    if ($toEncoding !== 'UTF-8' && $toEncoding !== 'CP850') {
      $toEncoding = self::normalize_encoding($toEncoding, 'UTF-8');
    }

    if ($fromEncoding && $fromEncoding !== 'UTF-8' && $fromEncoding !== 'CP850') {
      $fromEncoding = self::normalize_encoding($fromEncoding, null);
    }

    if ($toEncoding && $fromEncoding && $fromEncoding === $toEncoding) {
      return $str;
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if ($toEncoding === 'JSON') {
      return self::json_encode($str);
    }
    if ($fromEncoding === 'JSON') {
      $str = self::json_decode($str);
      $fromEncoding = '';
    }

    if ($toEncoding === 'BASE64') {
      return base64_encode($str);
    }
    if ($fromEncoding === 'BASE64') {
      $str = base64_decode($str);
      $fromEncoding = '';
    }

    if ($toEncoding === 'HTML-ENTITIES') {
      return self::html_encode($str, true, 'UTF-8');
    }
    if ($fromEncoding === 'HTML-ENTITIES') {
      $str = self::html_decode($str, ENT_COMPAT, 'UTF-8');
      $fromEncoding = '';
    }

    $fromEncodingDetected = false;
    if (
        $autodetectFromEncoding === true
        ||
        !$fromEncoding
    ) {
      $fromEncodingDetected = self::str_detect_encoding($str);
    }

    // DEBUG
    //var_dump($toEncoding, $fromEncoding, $fromEncodingDetected, $str, "\n\n");

    if ($fromEncodingDetected !== false) {
      $fromEncoding = $fromEncodingDetected;
    } elseif ($fromEncodingDetected === false && $autodetectFromEncoding === true) {
      // fallback for the "autodetect"-mode
      return self::to_utf8($str);
    }

    if (
        !$fromEncoding
        ||
        $fromEncoding === $toEncoding
    ) {
      return $str;
    }

    if (
        $toEncoding === 'UTF-8'
        &&
        (
            $fromEncoding === 'WINDOWS-1252'
            ||
            $fromEncoding === 'ISO-8859-1'
        )
    ) {
      return self::to_utf8($str);
    }

    if (
        $toEncoding === 'ISO-8859-1'
        &&
        (
            $fromEncoding === 'WINDOWS-1252'
            ||
            $fromEncoding === 'UTF-8'
        )
    ) {
      return self::to_iso8859($str);
    }

    if (
        $toEncoding !== 'UTF-8'
        &&
        $toEncoding !== 'ISO-8859-1'
        &&
        $toEncoding !== 'WINDOWS-1252'
        &&
        self::$SUPPORT['mbstring'] === false
    ) {
      \trigger_error('UTF8::encode() without mbstring cannot handle "' . $toEncoding . '" encoding', E_USER_WARNING);
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['mbstring'] === true) {
      // info: do not use the symfony polyfill here
      $strEncoded = \mb_convert_encoding(
          $str,
          $toEncoding,
          $fromEncoding
      );

      if ($strEncoded) {
        return $strEncoded;
      }
    }

    $return = \iconv($fromEncoding, $toEncoding, $str);
    if ($return !== false) {
      return $return;
    }

    return $str;
  }

  /**
   * @param string $str
   * @param string $fromCharset      [optional] <p>Set the input charset.</p>
   * @param string $toCharset        [optional] <p>Set the output charset.</p>
   * @param string $transferEncoding [optional] <p>Set the transfer encoding.</p>
   * @param string $linefeed         [optional] <p>Set the used linefeed.</p>
   * @param int    $indent           [optional] <p>Set the max length indent.</p>
   *
   * @return string|false
   *                      An encoded MIME field on success,
   *                      or false if an error occurs during the encoding.
   */
  public static function encode_mimeheader(
      $str,
      $fromCharset = 'UTF-8',
      $toCharset = 'UTF-8',
      $transferEncoding = 'Q',
      $linefeed = "\r\n",
      $indent = 76
  )
  {
    if ($fromCharset !== 'UTF-8' && $fromCharset !== 'CP850') {
      $fromCharset = self::normalize_encoding($fromCharset, 'UTF-8');
    }

    if ($toCharset !== 'UTF-8' && $toCharset !== 'CP850') {
      $toCharset = self::normalize_encoding($toCharset, 'UTF-8');
    }

    $output = \iconv_mime_encode(
        '',
        $str,
        [
            'scheme'           => $transferEncoding,
            'line-length'      => $indent,
            'input-charset'    => $fromCharset,
            'output-charset'   => $toCharset,
            'line-break-chars' => $linefeed,
        ]
    );

    return $output;
  }

  /**
   * Create an extract from a sentence, so if the search-string was found, it try to centered in the output.
   *
   * @param string   $str                    <p>The input string.</p>
   * @param string   $search                 <p>The searched string.</p>
   * @param int|null $length                 [optional] <p>Default: null === text->length / 2</p>
   * @param string   $replacerForSkippedText [optional] <p>Default: …</p>
   * @param string   $encoding               [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return string
   */
  public static function extract_text(string $str, string $search = '', int $length = null, string $replacerForSkippedText = '…', string $encoding = 'UTF-8'): string
  {
    if ('' === $str) {
      return '';
    }

    $trimChars = "\t\r\n -_()!~?=+/*\\,.:;\"'[]{}`&";

    if ($length === null) {
      $length = (int)\round(self::strlen($str, $encoding) / 2, 0);
    }

    if (empty($search)) {

      $stringLength = self::strlen($str, $encoding);

      if ($length > 0) {
        $end = ($length - 1) > $stringLength ? $stringLength : ($length - 1);
      } else {
        $end = 0;
      }

      $pos = (int)\min(
          self::strpos($str, ' ', $end, $encoding),
          self::strpos($str, '.', $end, $encoding)
      );

      if ($pos) {
        $strSub = self::substr($str, 0, $pos, $encoding);
        if ($strSub === false) {
          return '';
        }

        return \rtrim($strSub, $trimChars) . $replacerForSkippedText;
      }

      return $str;
    }

    $wordPos = self::stripos($str, $search, 0, $encoding);
    $halfSide = (int)($wordPos - $length / 2 + self::strlen($search, $encoding) / 2);

    $pos_start = 0;
    if ($halfSide > 0) {
      $halfText = self::substr($str, 0, $halfSide, $encoding);
      if ($halfText !== false) {
        $pos_start = (int)\max(
            self::strrpos($halfText, ' ', 0, $encoding),
            self::strrpos($halfText, '.', 0, $encoding)
        );
      }
    }

    if ($wordPos && $halfSide > 0) {
      $l = $pos_start + $length - 1;
      $realLength = self::strlen($str, $encoding);

      if ($l > $realLength) {
        $l = $realLength;
      }

      $pos_end = (int)\min(
              self::strpos($str, ' ', $l, $encoding),
              self::strpos($str, '.', $l, $encoding)
          ) - $pos_start;

      if (!$pos_end || $pos_end <= 0) {
        $strSub = self::substr($str, $pos_start, self::strlen($str), $encoding);
        if ($strSub !== false) {
          $extract = $replacerForSkippedText . \ltrim($strSub, $trimChars);
        } else {
          $extract = '';
        }
      } else {
        $strSub = self::substr($str, $pos_start, $pos_end, $encoding);
        if ($strSub !== false) {
          $extract = $replacerForSkippedText . \trim($strSub, $trimChars) . $replacerForSkippedText;
        } else {
          $extract = '';
        }
      }

    } else {

      $l = $length - 1;
      $trueLength = self::strlen($str, $encoding);

      if ($l > $trueLength) {
        $l = $trueLength;
      }

      $pos_end = \min(
          self::strpos($str, ' ', $l, $encoding),
          self::strpos($str, '.', $l, $encoding)
      );

      if ($pos_end) {
        $strSub = self::substr($str, 0, $pos_end, $encoding);
        if ($strSub !== false) {
          $extract = \rtrim($strSub, $trimChars) . $replacerForSkippedText;
        } else {
          $extract = '';
        }
      } else {
        $extract = $str;
      }
    }

    return $extract;
  }

  /**
   * Reads entire file into a string.
   *
   * WARNING: do not use UTF-8 Option ($convertToUtf8) for binary-files (e.g.: images) !!!
   *
   * @link http://php.net/manual/en/function.file-get-contents.php
   *
   * @param string        $filename             <p>
   *                                            Name of the file to read.
   *                                            </p>
   * @param bool          $use_include_path     [optional] <p>
   *                                            Prior to PHP 5, this parameter is called
   *                                            use_include_path and is a bool.
   *                                            As of PHP 5 the FILE_USE_INCLUDE_PATH can be used
   *                                            to trigger include path
   *                                            search.
   *                                            </p>
   * @param resource|null $context              [optional] <p>
   *                                            A valid context resource created with
   *                                            stream_context_create. If you don't need to use a
   *                                            custom context, you can skip this parameter by &null;.
   *                                            </p>
   * @param int|null      $offset               [optional] <p>
   *                                            The offset where the reading starts.
   *                                            </p>
   * @param int|null      $maxLength            [optional] <p>
   *                                            Maximum length of data read. The default is to read until end
   *                                            of file is reached.
   *                                            </p>
   * @param int           $timeout              <p>The time in seconds for the timeout.</p>
   *
   * @param bool          $convertToUtf8        <strong>WARNING!!!</strong> <p>Maybe you can't use this option for some
   *                                            files, because they used non default utf-8 chars. Binary files like
   *                                            images or pdf will not be converted.</p>
   * @param string        $fromEncoding         [optional] <p>e.g. 'UTF-16', 'UTF-8', 'ISO-8859-1', etc.<br>
   *                                            A empty string will trigger the autodetect anyway.</p>
   *
   * @return string|false The function returns the read data or false on failure.
   */
  public static function file_get_contents(
      string $filename,
      bool $use_include_path = false,
      $context = null,
      int $offset = null,
      int $maxLength = null,
      int $timeout = 10,
      bool $convertToUtf8 = true,
      string $fromEncoding = ''
  )
  {
    // init
    $filename = \filter_var($filename, FILTER_SANITIZE_STRING);

    if ($timeout && $context === null) {
      $context = \stream_context_create(
          [
              'http' =>
                  [
                      'timeout' => $timeout,
                  ],
          ]
      );
    }

    if ($offset === null) {
      $offset = 0;
    }

    if (\is_int($maxLength) === true) {
      $data = \file_get_contents($filename, $use_include_path, $context, $offset, $maxLength);
    } else {
      $data = \file_get_contents($filename, $use_include_path, $context, $offset);
    }

    // return false on error
    if ($data === false) {
      return false;
    }

    if ($convertToUtf8 === true) {
      if (
          self::is_binary($data, true) === true
          &&
          self::is_utf16($data, false) === false
          &&
          self::is_utf32($data, false) === false
      ) {
        // do nothing, it's binary and not UTF16 or UTF32
      } else {

        $data = self::encode('UTF-8', $data, false, $fromEncoding);
        $data = self::cleanup($data);

      }
    }

    return $data;
  }

  /**
   * Checks if a file starts with BOM (Byte Order Mark) character.
   *
   * @param string $file_path <p>Path to a valid file.</p>
   *
   * @throws \RuntimeException if file_get_contents() returned false
   *
   * @return bool
   *              <strong>true</strong> if the file has BOM at the start, <strong>false</strong> otherwise.
   */
  public static function file_has_bom(string $file_path): bool
  {
    $file_content = \file_get_contents($file_path);
    if ($file_content === false) {
      throw new \RuntimeException('file_get_contents() returned false for:' . $file_path);
    }

    return self::string_has_bom($file_content);
  }

  /**
   * Normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.
   *
   * @param mixed  $var
   * @param int    $normalization_form
   * @param string $leading_combining
   *
   * @return mixed
   */
  public static function filter($var, int $normalization_form = 4 /* n::NFC */, string $leading_combining = '◌')
  {
    switch (\gettype($var)) {
      case 'array':
        foreach ($var as $k => $v) {
          /** @noinspection AlterInForeachInspection */
          $var[$k] = self::filter($v, $normalization_form, $leading_combining);
        }
        break;
      case 'object':
        foreach ($var as $k => $v) {
          $var->{$k} = self::filter($v, $normalization_form, $leading_combining);
        }
        break;
      case 'string':

        if (false !== \strpos($var, "\r")) {
          // Workaround https://bugs.php.net/65732
          $var = self::normalize_line_ending($var);
        }

        if (self::is_ascii($var) === false) {
          /** @noinspection PhpUndefinedClassInspection */
          if (\Normalizer::isNormalized($var, $normalization_form)) {
            $n = '-';
          } else {
            /** @noinspection PhpUndefinedClassInspection */
            $n = \Normalizer::normalize($var, $normalization_form);

            if (isset($n[0])) {
              $var = $n;
            } else {
              $var = self::encode('UTF-8', $var, true);
            }
          }

          if (
              $var[0] >= "\x80"
              &&
              isset($n[0], $leading_combining[0])
              &&
              \preg_match('/^\p{Mn}/u', $var)
          ) {
            // Prevent leading combining chars
            // for NFC-safe concatenations.
            $var = $leading_combining . $var;
          }
        }

        break;
    }

    return $var;
  }

  /**
   * "filter_input()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.
   *
   * Gets a specific external variable by name and optionally filters it
   *
   * @link  http://php.net/manual/en/function.filter-input.php
   *
   * @param int    $type          <p>
   *                              One of <b>INPUT_GET</b>, <b>INPUT_POST</b>,
   *                              <b>INPUT_COOKIE</b>, <b>INPUT_SERVER</b>, or
   *                              <b>INPUT_ENV</b>.
   *                              </p>
   * @param string $variable_name <p>
   *                              Name of a variable to get.
   *                              </p>
   * @param int    $filter        [optional] <p>
   *                              The ID of the filter to apply. The
   *                              manual page lists the available filters.
   *                              </p>
   * @param mixed  $options       [optional] <p>
   *                              Associative array of options or bitwise disjunction of flags. If filter
   *                              accepts options, flags can be provided in "flags" field of array.
   *                              </p>
   *
   * @return mixed Value of the requested variable on success, <b>FALSE</b> if the filter fails, or <b>NULL</b> if the
   *               <i>variable_name</i> variable is not set. If the flag <b>FILTER_NULL_ON_FAILURE</b> is used, it
   *               returns <b>FALSE</b> if the variable is not set and <b>NULL</b> if the filter fails.
   */
  public static function filter_input(int $type, string $variable_name, int $filter = FILTER_DEFAULT, $options = null)
  {
    if (4 > \func_num_args()) {
      $var = \filter_input($type, $variable_name, $filter);
    } else {
      $var = \filter_input($type, $variable_name, $filter, $options);
    }

    return self::filter($var);
  }

  /**
   * "filter_input_array()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.
   *
   * Gets external variables and optionally filters them
   *
   * @link  http://php.net/manual/en/function.filter-input-array.php
   *
   * @param int   $type       <p>
   *                          One of <b>INPUT_GET</b>, <b>INPUT_POST</b>,
   *                          <b>INPUT_COOKIE</b>, <b>INPUT_SERVER</b>, or
   *                          <b>INPUT_ENV</b>.
   *                          </p>
   * @param mixed $definition [optional] <p>
   *                          An array defining the arguments. A valid key is a string
   *                          containing a variable name and a valid value is either a filter type, or an array
   *                          optionally specifying the filter, flags and options. If the value is an
   *                          array, valid keys are filter which specifies the
   *                          filter type,
   *                          flags which specifies any flags that apply to the
   *                          filter, and options which specifies any options that
   *                          apply to the filter. See the example below for a better understanding.
   *                          </p>
   *                          <p>
   *                          This parameter can be also an integer holding a filter constant. Then all values in the
   *                          input array are filtered by this filter.
   *                          </p>
   * @param bool  $add_empty  [optional] <p>
   *                          Add missing keys as <b>NULL</b> to the return value.
   *                          </p>
   *
   * @return mixed An array containing the values of the requested variables on success, or <b>FALSE</b> on failure. An
   *               array value will be <b>FALSE</b> if the filter fails, or <b>NULL</b> if the variable is not set. Or
   *               if the flag <b>FILTER_NULL_ON_FAILURE</b> is used, it returns <b>FALSE</b> if the variable is not
   *               set and <b>NULL</b> if the filter fails.
   */
  public static function filter_input_array(int $type, $definition = null, bool $add_empty = true)
  {
    if (2 > \func_num_args()) {
      $a = \filter_input_array($type);
    } else {
      $a = \filter_input_array($type, $definition, $add_empty);
    }

    return self::filter($a);
  }

  /**
   * "filter_var()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.
   *
   * Filters a variable with a specified filter
   *
   * @link  http://php.net/manual/en/function.filter-var.php
   *
   * @param mixed $variable <p>
   *                        Value to filter.
   *                        </p>
   * @param int   $filter   [optional] <p>
   *                        The ID of the filter to apply. The
   *                        manual page lists the available filters.
   *                        </p>
   * @param mixed $options  [optional] <p>
   *                        Associative array of options or bitwise disjunction of flags. If filter
   *                        accepts options, flags can be provided in "flags" field of array. For
   *                        the "callback" filter, callable type should be passed. The
   *                        callback must accept one argument, the value to be filtered, and return
   *                        the value after filtering/sanitizing it.
   *                        </p>
   *                        <p>
   *                        <code>
   *                        // for filters that accept options, use this format
   *                        $options = array(
   *                        'options' => array(
   *                        'default' => 3, // value to return if the filter fails
   *                        // other options here
   *                        'min_range' => 0
   *                        ),
   *                        'flags' => FILTER_FLAG_ALLOW_OCTAL,
   *                        );
   *                        $var = filter_var('0755', FILTER_VALIDATE_INT, $options);
   *                        // for filter that only accept flags, you can pass them directly
   *                        $var = filter_var('oops', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
   *                        // for filter that only accept flags, you can also pass as an array
   *                        $var = filter_var('oops', FILTER_VALIDATE_BOOLEAN,
   *                        array('flags' => FILTER_NULL_ON_FAILURE));
   *                        // callback validate filter
   *                        function foo($value)
   *                        {
   *                        // Expected format: Surname, GivenNames
   *                        if (strpos($value, ", ") === false) return false;
   *                        list($surname, $givennames) = explode(", ", $value, 2);
   *                        $empty = (empty($surname) || empty($givennames));
   *                        $notstrings = (!is_string($surname) || !is_string($givennames));
   *                        if ($empty || $notstrings) {
   *                        return false;
   *                        } else {
   *                        return $value;
   *                        }
   *                        }
   *                        $var = filter_var('Doe, Jane Sue', FILTER_CALLBACK, array('options' => 'foo'));
   *                        </code>
   *                        </p>
   *
   * @return mixed the filtered data, or <b>FALSE</b> if the filter fails.
   */
  public static function filter_var($variable, int $filter = FILTER_DEFAULT, $options = null)
  {
    if (3 > \func_num_args()) {
      $variable = \filter_var($variable, $filter);
    } else {
      $variable = \filter_var($variable, $filter, $options);
    }

    return self::filter($variable);
  }

  /**
   * "filter_var_array()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.
   *
   * Gets multiple variables and optionally filters them
   *
   * @link  http://php.net/manual/en/function.filter-var-array.php
   *
   * @param array $data       <p>
   *                          An array with string keys containing the data to filter.
   *                          </p>
   * @param mixed $definition [optional] <p>
   *                          An array defining the arguments. A valid key is a string
   *                          containing a variable name and a valid value is either a
   *                          filter type, or an
   *                          array optionally specifying the filter, flags and options.
   *                          If the value is an array, valid keys are filter
   *                          which specifies the filter type,
   *                          flags which specifies any flags that apply to the
   *                          filter, and options which specifies any options that
   *                          apply to the filter. See the example below for a better understanding.
   *                          </p>
   *                          <p>
   *                          This parameter can be also an integer holding a filter constant. Then all values in the
   *                          input array are filtered by this filter.
   *                          </p>
   * @param bool  $add_empty  [optional] <p>
   *                          Add missing keys as <b>NULL</b> to the return value.
   *                          </p>
   *
   * @return mixed An array containing the values of the requested variables on success, or <b>FALSE</b> on failure. An
   *               array value will be <b>FALSE</b> if the filter fails, or <b>NULL</b> if the variable is not set.
   */
  public static function filter_var_array(array $data, $definition = null, bool $add_empty = true)
  {
    if (2 > \func_num_args()) {
      $a = \filter_var_array($data);
    } else {
      $a = \filter_var_array($data, $definition, $add_empty);
    }

    return self::filter($a);
  }

  /**
   * Checks whether finfo is available on the server.
   *
   * @return bool
   *              <strong>true</strong> if available, <strong>false</strong> otherwise.
   */
  public static function finfo_loaded(): bool
  {
    return \class_exists('finfo');
  }

  /**
   * Returns the first $n characters of the string.
   *
   * @param string $str      <p>The input string.</p>
   * @param int    $n        <p>Number of characters to retrieve from the start.</p>
   * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return string
   */
  public static function first_char(string $str, int $n = 1, string $encoding = 'UTF-8'): string
  {
    if ($n <= 0) {
      return '';
    }

    $strSub = self::substr($str, 0, $n, $encoding);
    if ($strSub === false) {
      return '';
    }

    return $strSub;
  }

  /**
   * Check if the number of unicode characters are not more than the specified integer.
   *
   * @param string $str      The original string to be checked.
   * @param int    $box_size The size in number of chars to be checked against string.
   *
   * @return bool true if string is less than or equal to $box_size, false otherwise.
   */
  public static function fits_inside(string $str, int $box_size): bool
  {
    return (self::strlen($str) <= $box_size);
  }

  /**
   * @param string $str
   * @param bool   $useLower     <p>Use uppercase by default, otherwise use lowecase.</p>
   * @param bool   $fullCaseFold <p>Convert not only common cases.</p>
   *
   * @return string
   */
  private static function fixStrCaseHelper(string $str, $useLower = false, $fullCaseFold = false): string
  {
    $upper = self::$COMMON_CASE_FOLD['upper'];
    $lower = self::$COMMON_CASE_FOLD['lower'];

    if ($useLower === true) {
      $str = (string)\str_replace(
          $upper,
          $lower,
          $str
      );
    } else {
      $str = (string)\str_replace(
          $lower,
          $upper,
          $str
      );
    }

    if ($fullCaseFold) {

      static $FULL_CASE_FOLD = null;
      if ($FULL_CASE_FOLD === null) {
        $FULL_CASE_FOLD = self::getData('caseFolding_full');
      }

      if ($useLower === true) {
        $str = (string)\str_replace($FULL_CASE_FOLD[0], $FULL_CASE_FOLD[1], $str);
      } else {
        $str = (string)\str_replace($FULL_CASE_FOLD[1], $FULL_CASE_FOLD[0], $str);
      }
    }

    return $str;
  }

  /**
   * Try to fix simple broken UTF-8 strings.
   *
   * INFO: Take a look at "UTF8::fix_utf8()" if you need a more advanced fix for broken UTF-8 strings.
   *
   * If you received an UTF-8 string that was converted from Windows-1252 as it was ISO-8859-1
   * (ignoring Windows-1252 chars from 80 to 9F) use this function to fix it.
   * See: http://en.wikipedia.org/wiki/Windows-1252
   *
   * @param string $str <p>The input string</p>
   *
   * @return string
   */
  public static function fix_simple_utf8(string $str): string
  {
    if ('' === $str) {
      return '';
    }

    static $BROKEN_UTF8_TO_UTF8_KEYS_CACHE = null;
    static $BROKEN_UTF8_TO_UTF8_VALUES_CACHE = null;

    if ($BROKEN_UTF8_TO_UTF8_KEYS_CACHE === null) {

      if (self::$BROKEN_UTF8_FIX === null) {
        self::$BROKEN_UTF8_FIX = self::getData('utf8_fix');
      }

      $BROKEN_UTF8_TO_UTF8_KEYS_CACHE = \array_keys(self::$BROKEN_UTF8_FIX);
      $BROKEN_UTF8_TO_UTF8_VALUES_CACHE = \array_values(self::$BROKEN_UTF8_FIX);
    }

    return \str_replace($BROKEN_UTF8_TO_UTF8_KEYS_CACHE, $BROKEN_UTF8_TO_UTF8_VALUES_CACHE, $str);
  }

  /**
   * Fix a double (or multiple) encoded UTF8 string.
   *
   * @param string[]|string $str You can use a string or an array of strings.
   *
   * @return string[]|string
   *                          Will return the fixed input-"array" or
   *                          the fixed input-"string".
   */
  public static function fix_utf8($str)
  {
    if (\is_array($str) === true) {
      foreach ($str as $k => $v) {
        $str[$k] = self::fix_utf8($v);
      }

      return $str;
    }

    $str = (string)$str;
    $last = '';
    while ($last !== $str) {
      $last = $str;
      $str = self::to_utf8(
          self::utf8_decode($str, true)
      );
    }

    return $str;
  }

  /**
   * Get character of a specific character.
   *
   * @param string $char
   *
   * @return string 'RTL' or 'LTR'
   */
  public static function getCharDirection(string $char): string
  {
    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['intlChar'] === true) {
      /** @noinspection PhpComposerExtensionStubsInspection */
      $tmpReturn = \IntlChar::charDirection($char);

      // from "IntlChar"-Class
      $charDirection = [
          'RTL' => [1, 13, 14, 15, 21],
          'LTR' => [0, 11, 12, 20],
      ];

      if (\in_array($tmpReturn, $charDirection['LTR'], true)) {
        return 'LTR';
      }

      if (\in_array($tmpReturn, $charDirection['RTL'], true)) {
        return 'RTL';
      }
    }

    $c = static::chr_to_decimal($char);

    if (!(0x5be <= $c && 0x10b7f >= $c)) {
      return 'LTR';
    }

    if (0x85e >= $c) {

      if (0x5be === $c ||
          0x5c0 === $c ||
          0x5c3 === $c ||
          0x5c6 === $c ||
          (0x5d0 <= $c && 0x5ea >= $c) ||
          (0x5f0 <= $c && 0x5f4 >= $c) ||
          0x608 === $c ||
          0x60b === $c ||
          0x60d === $c ||
          0x61b === $c ||
          (0x61e <= $c && 0x64a >= $c) ||
          (0x66d <= $c && 0x66f >= $c) ||
          (0x671 <= $c && 0x6d5 >= $c) ||
          (0x6e5 <= $c && 0x6e6 >= $c) ||
          (0x6ee <= $c && 0x6ef >= $c) ||
          (0x6fa <= $c && 0x70d >= $c) ||
          0x710 === $c ||
          (0x712 <= $c && 0x72f >= $c) ||
          (0x74d <= $c && 0x7a5 >= $c) ||
          0x7b1 === $c ||
          (0x7c0 <= $c && 0x7ea >= $c) ||
          (0x7f4 <= $c && 0x7f5 >= $c) ||
          0x7fa === $c ||
          (0x800 <= $c && 0x815 >= $c) ||
          0x81a === $c ||
          0x824 === $c ||
          0x828 === $c ||
          (0x830 <= $c && 0x83e >= $c) ||
          (0x840 <= $c && 0x858 >= $c) ||
          0x85e === $c
      ) {
        return 'RTL';
      }

    } elseif (0x200f === $c) {

      return 'RTL';

    } elseif (0xfb1d <= $c) {

      if (0xfb1d === $c ||
          (0xfb1f <= $c && 0xfb28 >= $c) ||
          (0xfb2a <= $c && 0xfb36 >= $c) ||
          (0xfb38 <= $c && 0xfb3c >= $c) ||
          0xfb3e === $c ||
          (0xfb40 <= $c && 0xfb41 >= $c) ||
          (0xfb43 <= $c && 0xfb44 >= $c) ||
          (0xfb46 <= $c && 0xfbc1 >= $c) ||
          (0xfbd3 <= $c && 0xfd3d >= $c) ||
          (0xfd50 <= $c && 0xfd8f >= $c) ||
          (0xfd92 <= $c && 0xfdc7 >= $c) ||
          (0xfdf0 <= $c && 0xfdfc >= $c) ||
          (0xfe70 <= $c && 0xfe74 >= $c) ||
          (0xfe76 <= $c && 0xfefc >= $c) ||
          (0x10800 <= $c && 0x10805 >= $c) ||
          0x10808 === $c ||
          (0x1080a <= $c && 0x10835 >= $c) ||
          (0x10837 <= $c && 0x10838 >= $c) ||
          0x1083c === $c ||
          (0x1083f <= $c && 0x10855 >= $c) ||
          (0x10857 <= $c && 0x1085f >= $c) ||
          (0x10900 <= $c && 0x1091b >= $c) ||
          (0x10920 <= $c && 0x10939 >= $c) ||
          0x1093f === $c ||
          0x10a00 === $c ||
          (0x10a10 <= $c && 0x10a13 >= $c) ||
          (0x10a15 <= $c && 0x10a17 >= $c) ||
          (0x10a19 <= $c && 0x10a33 >= $c) ||
          (0x10a40 <= $c && 0x10a47 >= $c) ||
          (0x10a50 <= $c && 0x10a58 >= $c) ||
          (0x10a60 <= $c && 0x10a7f >= $c) ||
          (0x10b00 <= $c && 0x10b35 >= $c) ||
          (0x10b40 <= $c && 0x10b55 >= $c) ||
          (0x10b58 <= $c && 0x10b72 >= $c) ||
          (0x10b78 <= $c && 0x10b7f >= $c)
      ) {
        return 'RTL';
      }
    }

    return 'LTR';
  }

  /**
   * get data from "/data/*.ser"
   *
   * @param string $file
   *
   * @return mixed|false Will return false on error.
   */
  private static function getData(string $file)
  {
    $file = __DIR__ . '/data/' . $file . '.php';
    if (\file_exists($file)) {
      /** @noinspection PhpIncludeInspection */
      return require $file;
    }

    return false;
  }

  /**
   * Check for php-support.
   *
   * @param string|null $key
   *
   * @return mixed
   *               Return the full support-"array", if $key === null<br>
   *               return bool-value, if $key is used and available<br>
   *               otherwise return <strong>null</strong>.
   */
  public static function getSupportInfo(string $key = null)
  {
    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if ($key === null) {
      return self::$SUPPORT;
    }

    if (!isset(self::$SUPPORT[$key])) {
      return null;
    }

    return self::$SUPPORT[$key];
  }

  /**
   * @param string $str
   * @param array  $fallback with this keys: 'ext', 'mime', 'type'
   *
   * @return array
   *               with this keys: 'ext', 'mime', 'type'
   */
  private static function get_file_type(
      string $str,
      array $fallback = [
          'ext'  => null,
          'mime' => 'application/octet-stream',
          'type' => null,
      ]
  ): array
  {
    if ($str === '') {
      return $fallback;
    }

    $str_info = self::substr_in_byte($str, 0, 2);
    if (self::strlen_in_byte($str_info) !== 2) {
      return $fallback;
    }

    $str_info = \unpack('C2chars', $str_info);
    $type_code = (int)($str_info['chars1'] . $str_info['chars2']);

    // DEBUG
    //var_dump($type_code);

    switch ($type_code) {
      case 3780:
        $ext = 'pdf';
        $mime = 'application/pdf';
        $type = 'binary';

        break;
      case 7790:
        $ext = 'exe';
        $mime = 'application/octet-stream';
        $type = 'binary';

        break;
      case 7784:
        $ext = 'midi';
        $mime = 'audio/x-midi';
        $type = 'binary';

        break;
      case 8075:
        $ext = 'zip';
        $mime = 'application/zip';
        $type = 'binary';

        break;
      case 8297:
        $ext = 'rar';
        $mime = 'application/rar';
        $type = 'binary';

        break;
      case 255216:
        $ext = 'jpg';
        $mime = 'image/jpeg';
        $type = 'binary';

        break;
      case 7173:
        $ext = 'gif';
        $mime = 'image/gif';
        $type = 'binary';

        break;
      case 6677:
        $ext = 'bmp';
        $mime = 'image/bmp';
        $type = 'binary';

        break;
      case 13780:
        $ext = 'png';
        $mime = 'image/png';
        $type = 'binary';

        break;
      default:
        return $fallback;
    }

    return [
        'ext'  => $ext,
        'mime' => $mime,
        'type' => $type,
    ];
  }

  /**
   * @param int    $length        <p>Length of the random string.</p>
   * @param string $possibleChars [optional] <p>Characters string for the random selection.</p>
   * @param string $encoding      [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return string
   */
  public static function get_random_string(int $length, string $possibleChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', string $encoding = 'UTF-8'): string
  {
    // init
    $i = 0;
    $str = '';
    $maxlength = self::strlen($possibleChars, $encoding);

    if ($maxlength === 0) {
      return '';
    }

    // add random chars
    while ($i < $length) {
      try {
        $randInt = \random_int(0, $maxlength - 1);
      } catch (\Exception $e) {
        /** @noinspection RandomApiMigrationInspection */
        $randInt = \mt_rand(0, $maxlength - 1);
      }
      $char = self::substr($possibleChars, $randInt, 1, $encoding);
      $str .= $char;
      $i++;
    }

    return $str;
  }

  /**
   * @param string|int $entropyExtra [optional] <p>Extra entropy via a string or int value.</p>
   * @param bool       $md5          [optional] <p>Return the unique identifier as md5-hash? Default: true</p>
   *
   * @return string
   */
  public static function get_unique_string($entropyExtra = '', bool $md5 = true): string
  {
    $uniqueHelper = \mt_rand() .
                    \session_id() .
                    ($_SERVER['REMOTE_ADDR'] ?? '') .
                    ($_SERVER['SERVER_ADDR'] ?? '') .
                    $entropyExtra;

    $uniqueString = \uniqid($uniqueHelper, true);

    if ($md5) {
      $uniqueString = \md5($uniqueString . $uniqueHelper);
    }

    return $uniqueString;
  }

  /**
   * alias for "UTF8::string_has_bom()"
   *
   * @see        UTF8::string_has_bom()
   *
   * @param string $str
   *
   * @return bool
   *
   * @deprecated <p>use "UTF8::string_has_bom()"</p>
   */
  public static function hasBom(string $str): bool
  {
    return self::string_has_bom($str);
  }

  /**
   * Returns true if the string contains a lower case char, false otherwise.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return bool Whether or not the string contains a lower case character.
   */
  public static function has_lowercase(string $str): bool
  {
    return self::str_matches_pattern($str, '.*[[:lower:]]');
  }

  /**
   * Returns true if the string contains an upper case char, false otherwise.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return bool Whether or not the string contains an upper case character.
   */
  public static function has_uppercase(string $str): bool
  {
    return self::str_matches_pattern($str, '.*[[:upper:]]');
  }

  /**
   * Converts a hexadecimal-value into an UTF-8 character.
   *
   * @param string $hexdec <p>The hexadecimal value.</p>
   *
   * @return string|false One single UTF-8 character.
   */
  public static function hex_to_chr(string $hexdec)
  {
    return self::decimal_to_chr(\hexdec($hexdec));
  }

  /**
   * Converts hexadecimal U+xxxx code point representation to integer.
   *
   * INFO: opposite to UTF8::int_to_hex()
   *
   * @param string $hexDec <p>The hexadecimal code point representation.</p>
   *
   * @return int|false The code point, or false on failure.
   */
  public static function hex_to_int($hexDec)
  {
    // init
    $hexDec = (string)$hexDec;

    if ('' === $hexDec) {
      return false;
    }

    if (\preg_match('/^(?:\\\u|U\+|)([a-z0-9]{4,6})$/i', $hexDec, $match)) {
      return \intval($match[1], 16);
    }

    return false;
  }

  /**
   * alias for "UTF8::html_entity_decode()"
   *
   * @see UTF8::html_entity_decode()
   *
   * @param string $str
   * @param int    $flags
   * @param string $encoding
   *
   * @return string
   */
  public static function html_decode(string $str, int $flags = null, string $encoding = 'UTF-8'): string
  {
    return self::html_entity_decode($str, $flags, $encoding);
  }

  /**
   * Converts a UTF-8 string to a series of HTML numbered entities.
   *
   * INFO: opposite to UTF8::html_decode()
   *
   * @param string $str            <p>The Unicode string to be encoded as numbered entities.</p>
   * @param bool   $keepAsciiChars [optional] <p>Keep ASCII chars.</p>
   * @param string $encoding       [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return string HTML numbered entities.
   */
  public static function html_encode(string $str, bool $keepAsciiChars = false, string $encoding = 'UTF-8'): string
  {
    if ('' === $str) {
      return '';
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    # INFO: http://stackoverflow.com/questions/35854535/better-explanation-of-convmap-in-mb-encode-numericentity
    if (self::$SUPPORT['mbstring'] === true) {
      $startCode = 0x00;
      if ($keepAsciiChars === true) {
        $startCode = 0x80;
      }

      return \mb_encode_numericentity(
          $str,
          [$startCode, 0xfffff, 0, 0xfffff, 0],
          $encoding
      );
    }

    //
    // fallback via vanilla php
    //

    return \implode(
        '',
        \array_map(
            function ($chr) use ($keepAsciiChars, $encoding) {
              return UTF8::single_chr_html_encode($chr, $keepAsciiChars, $encoding);
            },
            self::split($str)
        )
    );
  }

  /**
   * UTF-8 version of html_entity_decode()
   *
   * The reason we are not using html_entity_decode() by itself is because
   * while it is not technically correct to leave out the semicolon
   * at the end of an entity most browsers will still interpret the entity
   * correctly. html_entity_decode() does not convert entities without
   * semicolons, so we are left with our own little solution here. Bummer.
   *
   * Convert all HTML entities to their applicable characters
   *
   * INFO: opposite to UTF8::html_encode()
   *
   * @link http://php.net/manual/en/function.html-entity-decode.php
   *
   * @param string $str      <p>
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
   * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return string The decoded string.
   */
  public static function html_entity_decode(string $str, int $flags = null, string $encoding = 'UTF-8'): string
  {
    if ('' === $str) {
      return '';
    }

    if (!isset($str[3])) { // examples: &; || &x;
      return $str;
    }

    if (
        \strpos($str, '&') === false
        ||
        (
            \strpos($str, '&#') === false
            &&
            \strpos($str, ';') === false
        )
    ) {
      return $str;
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if ($flags === null) {
      $flags = ENT_QUOTES | ENT_HTML5;
    }

    if (
        $encoding !== 'UTF-8'
        &&
        $encoding !== 'ISO-8859-1'
        &&
        $encoding !== 'WINDOWS-1252'
        &&
        self::$SUPPORT['mbstring'] === false
    ) {
      \trigger_error('UTF8::html_entity_decode() without mbstring cannot handle "' . $encoding . '" encoding', E_USER_WARNING);
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    do {
      $str_compare = $str;

      # INFO: http://stackoverflow.com/questions/35854535/better-explanation-of-convmap-in-mb-encode-numericentity
      if (self::$SUPPORT['mbstring'] === true) {

        $str = \mb_decode_numericentity(
            $str,
            [0x80, 0xfffff, 0, 0xfffff, 0],
            $encoding
        );

      } else {

        $str = (string)\preg_replace_callback(
            "/&#\d{2,6};/",
            function ($matches) use ($encoding) {
              // always fallback via symfony polyfill
              $returnTmp = \mb_convert_encoding($matches[0], $encoding, 'HTML-ENTITIES');

              if ($returnTmp !== '"' && $returnTmp !== "'") {
                return $returnTmp;
              }

              return $matches[0];
            },
            $str
        );

      }

      // decode numeric & UTF16 two byte entities
      $str = \html_entity_decode(
          \preg_replace('/(&#(?:x0*[0-9a-f]{2,6}(?![0-9a-f;])|(?:0*\d{2,6}(?![0-9;]))))/iS', '$1;', $str),
          $flags,
          $encoding
      );

    } while ($str_compare !== $str);

    return $str;
  }

  /**
   * Create a escape html version of the string via "UTF8::htmlspecialchars()".
   *
   * @param string $str
   * @param string $encoding [optional] <p>Default: UTF-8</p>
   *
   * @return string
   */
  public static function html_escape(string $str, string $encoding = 'UTF-8'): string
  {
    return self::htmlspecialchars(
        $str,
        ENT_QUOTES | ENT_SUBSTITUTE,
        $encoding
    );
  }

  /**
   * Remove empty html-tag.
   *
   * e.g.: <tag></tag>
   *
   * @param string $str
   *
   * @return string
   */
  public static function html_stripe_empty_tags(string $str): string
  {
    return (string)\preg_replace(
        "/<[^\/>]*>(([\s]?)*|)<\/[^>]*>/iu",
        '',
        $str
    );
  }

  /**
   * Convert all applicable characters to HTML entities: UTF-8 version of htmlentities()
   *
   * @link http://php.net/manual/en/function.htmlentities.php
   *
   * @param string $str           <p>
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
   *
   * @return string The encoded string.
   * </p>
   * <p>
   * If the input <i>string</i> contains an invalid code unit
   * sequence within the given <i>encoding</i> an empty string
   * will be returned, unless either the <b>ENT_IGNORE</b> or
   * <b>ENT_SUBSTITUTE</b> flags are set.
   */
  public static function htmlentities(string $str, int $flags = ENT_COMPAT, string $encoding = 'UTF-8', bool $double_encode = true): string
  {
    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    $str = \htmlentities($str, $flags, $encoding, $double_encode);

    /**
     * PHP doesn't replace a backslash to its html entity since this is something
     * that's mostly used to escape characters when inserting in a database. Since
     * we're using a decent database layer, we don't need this shit and we're replacing
     * the double backslashes by its' html entity equivalent.
     *
     * https://github.com/forkcms/library/blob/master/spoon/filter/filter.php#L303
     */
    $str = \str_replace('\\', '&#92;', $str);

    return self::html_encode($str, true, $encoding);
  }

  /**
   * Convert only special characters to HTML entities: UTF-8 version of htmlspecialchars()
   *
   * INFO: Take a look at "UTF8::htmlentities()"
   *
   * @link http://php.net/manual/en/function.htmlspecialchars.php
   *
   * @param string $str           <p>
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
  public static function htmlspecialchars(string $str, int $flags = ENT_COMPAT, string $encoding = 'UTF-8', bool $double_encode = true): string
  {
    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    return \htmlspecialchars($str, $flags, $encoding, $double_encode);
  }

  /**
   * Checks whether iconv is available on the server.
   *
   * @return bool
   *              <strong>true</strong> if available, <strong>false</strong> otherwise.
   */
  public static function iconv_loaded(): bool
  {
    return \extension_loaded('iconv') ? true : false;
  }

  /**
   * alias for "UTF8::decimal_to_chr()"
   *
   * @see UTF8::decimal_to_chr()
   *
   * @param mixed $int
   *
   * @return string
   */
  public static function int_to_chr($int): string
  {
    return self::decimal_to_chr($int);
  }

  /**
   * Converts Integer to hexadecimal U+xxxx code point representation.
   *
   * INFO: opposite to UTF8::hex_to_int()
   *
   * @param int    $int  <p>The integer to be converted to hexadecimal code point.</p>
   * @param string $pfix [optional]
   *
   * @return string The code point, or empty string on failure.
   */
  public static function int_to_hex(int $int, string $pfix = 'U+'): string
  {
    $hex = \dechex($int);

    $hex = (\strlen($hex) < 4 ? \substr('0000' . $hex, -4) : $hex);

    return $pfix . $hex . '';
  }

  /**
   * Checks whether intl-char is available on the server.
   *
   * @return bool
   *              <strong>true</strong> if available, <strong>false</strong> otherwise.
   */
  public static function intlChar_loaded(): bool
  {
    return \class_exists('IntlChar');
  }

  /**
   * Checks whether intl is available on the server.
   *
   * @return bool
   *              <strong>true</strong> if available, <strong>false</strong> otherwise.
   */
  public static function intl_loaded(): bool
  {
    return \extension_loaded('intl');
  }

  /**
   * alias for "UTF8::is_ascii()"
   *
   * @see        UTF8::is_ascii()
   *
   * @param string $str
   *
   * @return bool
   *
   * @deprecated <p>use "UTF8::is_ascii()"</p>
   */
  public static function isAscii(string $str): bool
  {
    return self::is_ascii($str);
  }

  /**
   * alias for "UTF8::is_base64()"
   *
   * @see        UTF8::is_base64()
   *
   * @param string $str
   *
   * @return bool
   *
   * @deprecated <p>use "UTF8::is_base64()"</p>
   */
  public static function isBase64($str): bool
  {
    return self::is_base64($str);
  }

  /**
   * alias for "UTF8::is_binary()"
   *
   * @see        UTF8::is_binary()
   *
   * @param mixed $str
   * @param bool  $strict
   *
   * @return bool
   *
   * @deprecated <p>use "UTF8::is_binary()"</p>
   */
  public static function isBinary($str, $strict = false): bool
  {
    return self::is_binary($str, $strict);
  }

  /**
   * alias for "UTF8::is_bom()"
   *
   * @see        UTF8::is_bom()
   *
   * @param string $utf8_chr
   *
   * @return bool
   *
   * @deprecated <p>use "UTF8::is_bom()"</p>
   */
  public static function isBom(string $utf8_chr): bool
  {
    return self::is_bom($utf8_chr);
  }

  /**
   * alias for "UTF8::is_html()"
   *
   * @see        UTF8::is_html()
   *
   * @param string $str
   *
   * @return bool
   *
   * @deprecated <p>use "UTF8::is_html()"</p>
   */
  public static function isHtml(string $str): bool
  {
    return self::is_html($str);
  }

  /**
   * alias for "UTF8::is_json()"
   *
   * @see        UTF8::is_json()
   *
   * @param string $str
   *
   * @return bool
   *
   * @deprecated <p>use "UTF8::is_json()"</p>
   */
  public static function isJson(string $str): bool
  {
    return self::is_json($str);
  }

  /**
   * alias for "UTF8::is_utf16()"
   *
   * @see        UTF8::is_utf16()
   *
   * @param mixed $str
   *
   * @return int|false
   *                    <strong>false</strong> if is't not UTF16,<br>
   *                    <strong>1</strong> for UTF-16LE,<br>
   *                    <strong>2</strong> for UTF-16BE.
   *
   * @deprecated <p>use "UTF8::is_utf16()"</p>
   */
  public static function isUtf16($str)
  {
    return self::is_utf16($str);
  }

  /**
   * alias for "UTF8::is_utf32()"
   *
   * @see        UTF8::is_utf32()
   *
   * @param mixed $str
   *
   * @return int|false
   *                   <strong>false</strong> if is't not UTF16,
   *                   <strong>1</strong> for UTF-32LE,
   *                   <strong>2</strong> for UTF-32BE.
   *
   * @deprecated <p>use "UTF8::is_utf32()"</p>
   */
  public static function isUtf32($str)
  {
    return self::is_utf32($str);
  }

  /**
   * alias for "UTF8::is_utf8()"
   *
   * @see        UTF8::is_utf8()
   *
   * @param string $str
   * @param bool   $strict
   *
   * @return bool
   *
   * @deprecated <p>use "UTF8::is_utf8()"</p>
   */
  public static function isUtf8($str, $strict = false): bool
  {
    return self::is_utf8($str, $strict);
  }

  /**
   * Returns true if the string contains only alphabetic chars, false otherwise.
   *
   * @param string $str
   *
   * @return bool
   *               Whether or not $str contains only alphabetic chars.
   */
  public static function is_alpha(string $str): bool
  {
    return self::str_matches_pattern($str, '^[[:alpha:]]*$');
  }

  /**
   * Returns true if the string contains only alphabetic and numeric chars, false otherwise.
   *
   * @param string $str
   *
   * @return bool
   *               Whether or not $str contains only alphanumeric chars.
   */
  public static function is_alphanumeric(string $str): bool
  {
    return self::str_matches_pattern($str, '^[[:alnum:]]*$');
  }

  /**
   * Checks if a string is 7 bit ASCII.
   *
   * @param string $str <p>The string to check.</p>
   *
   * @return bool
   *              <strong>true</strong> if it is ASCII<br>
   *              <strong>false</strong> otherwise
   *
   */
  public static function is_ascii(string $str): bool
  {
    if ('' === $str) {
      return true;
    }

    return !\preg_match('/[^\x09\x10\x13\x0A\x0D\x20-\x7E]/', $str);
  }

  /**
   * Returns true if the string is base64 encoded, false otherwise.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return bool Whether or not $str is base64 encoded.
   */
  public static function is_base64($str): bool
  {
    if ('' === $str) {
      return false;
    }

    if (\is_string($str) === false) {
      return false;
    }

    $base64String = (string)\base64_decode($str, true);

    return $base64String && \base64_encode($base64String) === $str;
  }

  /**
   * Check if the input is binary... (is look like a hack).
   *
   * @param mixed $input
   * @param bool  $strict
   *
   * @return bool
   */
  public static function is_binary($input, bool $strict = false): bool
  {
    $input = (string)$input;
    if ('' === $input) {
      return false;
    }

    if (\preg_match('~^[01]+$~', $input)) {
      return true;
    }

    $ext = self::get_file_type($input);
    if ($ext['type'] === 'binary') {
      return true;
    }

    $testLength = self::strlen_in_byte($input);
    if ($testLength) {
      if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
        self::checkForSupport();
      }

      $testNull = self::substr_count_in_byte($input, "\x0", 0, $testLength);
      if (($testNull / $testLength) > 0.256) {
        return true;
      }
    }

    if ($strict === true) {

      if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
        self::checkForSupport();
      }

      if (self::$SUPPORT['finfo'] === false) {
        throw new \RuntimeException('ext-fileinfo: is not installed');
      }

      /** @noinspection PhpComposerExtensionStubsInspection */
      $finfo = new \finfo(FILEINFO_MIME_ENCODING);
      $finfo_encoding = $finfo->buffer($input);
      if ($finfo_encoding && $finfo_encoding === 'binary') {
        return true;
      }

    }

    return false;
  }

  /**
   * Check if the file is binary.
   *
   * @param string $file
   *
   * @return bool
   */
  public static function is_binary_file($file): bool
  {
    // init
    $block = '';

    $fp = \fopen($file, 'rb');
    if (\is_resource($fp)) {
      $block = \fread($fp, 512);
      \fclose($fp);
    }

    if ($block === '') {
      return false;
    }

    return self::is_binary($block, true);
  }

  /**
   * Returns true if the string contains only whitespace chars, false otherwise.
   *
   * @param string $str
   *
   * @return bool
   *               Whether or not $str contains only whitespace characters.
   */
  public static function is_blank(string $str): bool
  {
    return self::str_matches_pattern($str, '^[[:space:]]*$');
  }

  /**
   * Checks if the given string is equal to any "Byte Order Mark".
   *
   * WARNING: Use "UTF8::string_has_bom()" if you will check BOM in a string.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return bool
   *              <strong>true</strong> if the $utf8_chr is Byte Order Mark, <strong>false</strong> otherwise.
   */
  public static function is_bom($str): bool
  {
    foreach (self::$BOM as $bomString => $bomByteLength) {
      if ($str === $bomString) {
        return true;
      }
    }

    return false;
  }

  /**
   * Determine whether the string is considered to be empty.
   *
   * A variable is considered empty if it does not exist or if its value equals FALSE.
   * empty() does not generate a warning if the variable does not exist.
   *
   * @param mixed $str
   *
   * @return bool Whether or not $str is empty().
   */
  public static function is_empty($str): bool
  {
    return empty($str);
  }

  /**
   * Returns true if the string contains only hexadecimal chars, false otherwise.
   *
   * @param string $str
   *
   * @return bool
   *               Whether or not $str contains only hexadecimal chars.
   */
  public static function is_hexadecimal(string $str): bool
  {
    return self::str_matches_pattern($str, '^[[:xdigit:]]*$');
  }

  /**
   * Check if the string contains any html-tags <lall>.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return bool
   */
  public static function is_html(string $str): bool
  {
    if ('' === $str) {
      return false;
    }

    // init
    $matches = [];

    \preg_match("/<\/?\w+(?:(?:\s+\w+(?:\s*=\s*(?:\".*?\"|'.*?'|[^'\">\s]+))?)*+\s*|\s*)\/?>/", $str, $matches);

    return !(\count($matches) === 0);
  }

  /**
   * Try to check if "$str" is an json-string.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return bool
   */
  public static function is_json(string $str): bool
  {
    if ('' === $str) {
      return false;
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['json'] === false) {
      throw new \RuntimeException('ext-json: is not installed');
    }

    $json = self::json_decode($str);

    /** @noinspection PhpComposerExtensionStubsInspection */
    return (
               \is_object($json) === true
               ||
               \is_array($json) === true
           )
           &&
           \json_last_error() === JSON_ERROR_NONE;
  }

  /**
   * @param string $str
   *
   * @return bool
   */
  public static function is_lowercase(string $str): bool
  {
    if (self::str_matches_pattern($str, '^[[:lower:]]*$')) {
      return true;
    }

    return false;
  }

  /**
   * Returns true if the string is serialized, false otherwise.
   *
   * @param string $str
   *
   * @return bool Whether or not $str is serialized.
   */
  public static function is_serialized(string $str): bool
  {
    if ('' === $str) {
      return false;
    }

    /** @noinspection PhpUsageOfSilenceOperatorInspection */
    /** @noinspection UnserializeExploitsInspection */
    return $str === 'b:0;'
           ||
           @\unserialize($str) !== false;
  }

  /**
   * Returns true if the string contains only lower case chars, false
   * otherwise.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return bool
   *               Whether or not $str contains only lower case characters.
   */
  public static function is_uppercase(string $str): bool
  {
    return self::str_matches_pattern($str, '^[[:upper:]]*$');
  }

  /**
   * Check if the string is UTF-16.
   *
   * @param mixed $str <p>The input string.</p>
   * @param bool  $checkIfStringIsBinary
   *
   * @return int|false
   *                   <strong>false</strong> if is't not UTF-16,<br>
   *                   <strong>1</strong> for UTF-16LE,<br>
   *                   <strong>2</strong> for UTF-16BE.
   */
  public static function is_utf16($str, $checkIfStringIsBinary = true)
  {
    // init
    $str = (string)$str;
    $strChars = [];

    if (
        $checkIfStringIsBinary === true
        &&
        self::is_binary($str, true) === false
    ) {
      return false;
    }

    if (self::$SUPPORT['mbstring'] === false) {
      \trigger_error('UTF8::is_utf16() without mbstring may did not work correctly', E_USER_WARNING);
    }

    $str = self::remove_bom($str);

    $maybeUTF16LE = 0;
    $test = \mb_convert_encoding($str, 'UTF-8', 'UTF-16LE');
    if ($test) {
      $test2 = \mb_convert_encoding($test, 'UTF-16LE', 'UTF-8');
      $test3 = \mb_convert_encoding($test2, 'UTF-8', 'UTF-16LE');
      if ($test3 === $test) {
        if (\count($strChars) === 0) {
          $strChars = self::count_chars($str, true);
        }
        foreach (self::count_chars($test3, true) as $test3char => $test3charEmpty) {
          if (\in_array($test3char, $strChars, true) === true) {
            $maybeUTF16LE++;
          }
        }
      }
    }

    $maybeUTF16BE = 0;
    $test = \mb_convert_encoding($str, 'UTF-8', 'UTF-16BE');
    if ($test) {
      $test2 = \mb_convert_encoding($test, 'UTF-16BE', 'UTF-8');
      $test3 = \mb_convert_encoding($test2, 'UTF-8', 'UTF-16BE');
      if ($test3 === $test) {
        if (\count($strChars) === 0) {
          $strChars = self::count_chars($str, true);
        }
        foreach (self::count_chars($test3, true) as $test3char => $test3charEmpty) {
          if (\in_array($test3char, $strChars, true) === true) {
            $maybeUTF16BE++;
          }
        }
      }
    }

    if ($maybeUTF16BE !== $maybeUTF16LE) {
      if ($maybeUTF16LE > $maybeUTF16BE) {
        return 1;
      }

      return 2;
    }

    return false;
  }

  /**
   * Check if the string is UTF-32.
   *
   * @param mixed $str <p>The input string.</p>
   * @param bool  $checkIfStringIsBinary
   *
   * @return int|false
   *                   <strong>false</strong> if is't not UTF-32,<br>
   *                   <strong>1</strong> for UTF-32LE,<br>
   *                   <strong>2</strong> for UTF-32BE.
   */
  public static function is_utf32($str, $checkIfStringIsBinary = true)
  {
    // init
    $str = (string)$str;
    $strChars = [];

    if (
        $checkIfStringIsBinary === true
        &&
        self::is_binary($str, true) === false
    ) {
      return false;
    }

    if (self::$SUPPORT['mbstring'] === false) {
      \trigger_error('UTF8::is_utf32() without mbstring may did not work correctly', E_USER_WARNING);
    }

    $str = self::remove_bom($str);

    $maybeUTF32LE = 0;
    $test = \mb_convert_encoding($str, 'UTF-8', 'UTF-32LE');
    if ($test) {
      $test2 = \mb_convert_encoding($test, 'UTF-32LE', 'UTF-8');
      $test3 = \mb_convert_encoding($test2, 'UTF-8', 'UTF-32LE');
      if ($test3 === $test) {
        if (\count($strChars) === 0) {
          $strChars = self::count_chars($str, true);
        }
        foreach (self::count_chars($test3, true) as $test3char => $test3charEmpty) {
          if (\in_array($test3char, $strChars, true) === true) {
            $maybeUTF32LE++;
          }
        }
      }
    }

    $maybeUTF32BE = 0;
    $test = \mb_convert_encoding($str, 'UTF-8', 'UTF-32BE');
    if ($test) {
      $test2 = \mb_convert_encoding($test, 'UTF-32BE', 'UTF-8');
      $test3 = \mb_convert_encoding($test2, 'UTF-8', 'UTF-32BE');
      if ($test3 === $test) {
        if (\count($strChars) === 0) {
          $strChars = self::count_chars($str, true);
        }
        foreach (self::count_chars($test3, true) as $test3char => $test3charEmpty) {
          if (\in_array($test3char, $strChars, true) === true) {
            $maybeUTF32BE++;
          }
        }
      }
    }

    if ($maybeUTF32BE !== $maybeUTF32LE) {
      if ($maybeUTF32LE > $maybeUTF32BE) {
        return 1;
      }

      return 2;
    }

    return false;
  }

  /**
   * Checks whether the passed string contains only byte sequences that appear valid UTF-8 characters.
   *
   * @see    http://hsivonen.iki.fi/php-utf8/
   *
   * @param string|string[] $str    <p>The string to be checked.</p>
   * @param bool            $strict <p>Check also if the string is not UTF-16 or UTF-32.</p>
   *
   * @return bool
   */
  public static function is_utf8($str, bool $strict = false): bool
  {
    if (\is_array($str) === true) {
      foreach ($str as $k => $v) {
        if (false === self::is_utf8($v, $strict)) {
          return false;
        }
      }

      return true;
    }

    if ('' === $str) {
      return true;
    }

    if ($strict === true) {
      $isBinary = self::is_binary($str, true);

      if ($isBinary && self::is_utf16($str, false) !== false) {
        return false;
      }

      if ($isBinary && self::is_utf32($str, false) !== false) {
        return false;
      }
    }

    if (self::pcre_utf8_support() !== true) {

      // If even just the first character can be matched, when the /u
      // modifier is used, then it's valid UTF-8. If the UTF-8 is somehow
      // invalid, nothing at all will match, even if the string contains
      // some valid sequences
      return (\preg_match('/^.{1}/us', $str, $ar) === 1);
    }

    $mState = 0; // cached expected number of octets after the current octet
    // until the beginning of the next UTF8 character sequence
    $mUcs4 = 0; // cached Unicode character
    $mBytes = 1; // cached expected number of octets in the current sequence

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$ORD === null) {
      self::$ORD = self::getData('ord');
    }

    $len = self::strlen_in_byte((string)$str);
    /** @noinspection ForeachInvariantsInspection */
    for ($i = 0; $i < $len; $i++) {
      $in = self::$ORD[$str[$i]];
      if ($mState === 0) {
        // When mState is zero we expect either a US-ASCII character or a
        // multi-octet sequence.
        if (0 === (0x80 & $in)) {
          // US-ASCII, pass straight through.
          $mBytes = 1;
        } elseif (0xC0 === (0xE0 & $in)) {
          // First octet of 2 octet sequence.
          $mUcs4 = $in;
          $mUcs4 = ($mUcs4 & 0x1F) << 6;
          $mState = 1;
          $mBytes = 2;
        } elseif (0xE0 === (0xF0 & $in)) {
          // First octet of 3 octet sequence.
          $mUcs4 = $in;
          $mUcs4 = ($mUcs4 & 0x0F) << 12;
          $mState = 2;
          $mBytes = 3;
        } elseif (0xF0 === (0xF8 & $in)) {
          // First octet of 4 octet sequence.
          $mUcs4 = $in;
          $mUcs4 = ($mUcs4 & 0x07) << 18;
          $mState = 3;
          $mBytes = 4;
        } elseif (0xF8 === (0xFC & $in)) {
          /* First octet of 5 octet sequence.
          *
          * This is illegal because the encoded codepoint must be either
          * (a) not the shortest form or
          * (b) outside the Unicode range of 0-0x10FFFF.
          * Rather than trying to resynchronize, we will carry on until the end
          * of the sequence and let the later error handling code catch it.
          */
          $mUcs4 = $in;
          $mUcs4 = ($mUcs4 & 0x03) << 24;
          $mState = 4;
          $mBytes = 5;
        } elseif (0xFC === (0xFE & $in)) {
          // First octet of 6 octet sequence, see comments for 5 octet sequence.
          $mUcs4 = $in;
          $mUcs4 = ($mUcs4 & 1) << 30;
          $mState = 5;
          $mBytes = 6;
        } else {
          // Current octet is neither in the US-ASCII range nor a legal first
          // octet of a multi-octet sequence.
          return false;
        }
      } else {
        // When mState is non-zero, we expect a continuation of the multi-octet
        // sequence
        if (0x80 === (0xC0 & $in)) {
          // Legal continuation.
          $shift = ($mState - 1) * 6;
          $tmp = $in;
          $tmp = ($tmp & 0x0000003F) << $shift;
          $mUcs4 |= $tmp;
          // Prefix: End of the multi-octet sequence. mUcs4 now contains the final
          // Unicode code point to be output.
          if (0 === --$mState) {
            // Check for illegal sequences and code points.
            //
            // From Unicode 3.1, non-shortest form is illegal
            if (
                (2 === $mBytes && $mUcs4 < 0x0080)
                ||
                (3 === $mBytes && $mUcs4 < 0x0800)
                ||
                (4 === $mBytes && $mUcs4 < 0x10000)
                ||
                (4 < $mBytes)
                ||
                // From Unicode 3.2, surrogate characters are illegal.
                (($mUcs4 & 0xFFFFF800) === 0xD800)
                ||
                // Code points outside the Unicode range are illegal.
                ($mUcs4 > 0x10FFFF)
            ) {
              return false;
            }
            // initialize UTF8 cache
            $mState = 0;
            $mUcs4 = 0;
            $mBytes = 1;
          }
        } else {
          // ((0xC0 & (*in) != 0x80) && (mState != 0))
          // Incomplete multi-octet sequence.
          return false;
        }
      }
    }

    return true;
  }

  /**
   * (PHP 5 &gt;= 5.2.0, PECL json &gt;= 1.2.0)<br/>
   * Decodes a JSON string
   *
   * @link http://php.net/manual/en/function.json-decode.php
   *
   * @param string $json    <p>
   *                        The <i>json</i> string being decoded.
   *                        </p>
   *                        <p>
   *                        This function only works with UTF-8 encoded strings.
   *                        </p>
   *                        <p>PHP implements a superset of
   *                        JSON - it will also encode and decode scalar types and <b>NULL</b>. The JSON standard
   *                        only supports these values when they are nested inside an array or an object.
   *                        </p>
   * @param bool   $assoc   [optional] <p>
   *                        When <b>TRUE</b>, returned objects will be converted into
   *                        associative arrays.
   *                        </p>
   * @param int    $depth   [optional] <p>
   *                        User specified recursion depth.
   *                        </p>
   * @param int    $options [optional] <p>
   *                        Bitmask of JSON decode options. Currently only
   *                        <b>JSON_BIGINT_AS_STRING</b>
   *                        is supported (default is to cast large integers as floats)
   *                        </p>
   *
   * @return mixed
   *                The value encoded in <i>json</i> in appropriate PHP type. Values true, false and
   *                null (case-insensitive) are returned as <b>TRUE</b>, <b>FALSE</b> and <b>NULL</b> respectively.
   *                <b>NULL</b> is returned if the <i>json</i> cannot be decoded or if the encoded data
   *                is deeper than the recursion limit.
   */
  public static function json_decode(string $json, bool $assoc = false, int $depth = 512, int $options = 0)
  {
    $json = self::filter($json);

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['json'] === false) {
      throw new \RuntimeException('ext-json: is not installed');
    }

    /** @noinspection PhpComposerExtensionStubsInspection */
    $json = \json_decode($json, $assoc, $depth, $options);

    return $json;
  }

  /**
   * (PHP 5 &gt;= 5.2.0, PECL json &gt;= 1.2.0)<br/>
   * Returns the JSON representation of a value.
   *
   * @link http://php.net/manual/en/function.json-encode.php
   *
   * @param mixed $value   <p>
   *                       The <i>value</i> being encoded. Can be any type except
   *                       a resource.
   *                       </p>
   *                       <p>
   *                       All string data must be UTF-8 encoded.
   *                       </p>
   *                       <p>PHP implements a superset of
   *                       JSON - it will also encode and decode scalar types and <b>NULL</b>. The JSON standard
   *                       only supports these values when they are nested inside an array or an object.
   *                       </p>
   * @param int   $options [optional] <p>
   *                       Bitmask consisting of <b>JSON_HEX_QUOT</b>,
   *                       <b>JSON_HEX_TAG</b>,
   *                       <b>JSON_HEX_AMP</b>,
   *                       <b>JSON_HEX_APOS</b>,
   *                       <b>JSON_NUMERIC_CHECK</b>,
   *                       <b>JSON_PRETTY_PRINT</b>,
   *                       <b>JSON_UNESCAPED_SLASHES</b>,
   *                       <b>JSON_FORCE_OBJECT</b>,
   *                       <b>JSON_UNESCAPED_UNICODE</b>. The behaviour of these
   *                       constants is described on
   *                       the JSON constants page.
   *                       </p>
   * @param int   $depth   [optional] <p>
   *                       Set the maximum depth. Must be greater than zero.
   *                       </p>
   *
   * @return string|false
   *                      A JSON encoded <strong>string</strong> on success or<br>
   *                      <strong>FALSE</strong> on failure.
   */
  public static function json_encode($value, int $options = 0, int $depth = 512)
  {
    $value = self::filter($value);

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['json'] === false) {
      throw new \RuntimeException('ext-json: is not installed');
    }

    /** @noinspection PhpComposerExtensionStubsInspection */
    $json = \json_encode($value, $options, $depth);

    return $json;
  }

  /**
   * Checks whether JSON is available on the server.
   *
   * @return bool
   *              <strong>true</strong> if available, <strong>false</strong> otherwise
   */
  public static function json_loaded(): bool
  {
    return \function_exists('json_decode');
  }

  /**
   * Makes string's first char lowercase.
   *
   * @param string      $str                   <p>The input string</p>
   * @param string      $encoding              [optional] <p>Set the charset for e.g. "mb_" function</p>
   * @param bool        $cleanUtf8             [optional] <p>Remove non UTF-8 chars from the string.</p>
   * @param string|null $lang                  [optional] <p>Set the language for special cases: az, el, lt, tr</p>
   * @param bool        $tryToKeepStringLength [optional] <p>true === try to keep the string length: e.g. ẞ -> ß</p>
   *
   * @return string The resulting string.
   */
  public static function lcfirst(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false, string $lang = null, bool $tryToKeepStringLength = false): string
  {
    $strPartTwo = self::substr($str, 1, null, $encoding, $cleanUtf8);
    if ($strPartTwo === false) {
      $strPartTwo = '';
    }

    $strPartOne = self::strtolower(
        (string)self::substr($str, 0, 1, $encoding, $cleanUtf8),
        $encoding,
        $cleanUtf8,
        $lang,
        $tryToKeepStringLength
    );

    return $strPartOne . $strPartTwo;
  }

  /**
   * alias for "UTF8::lcfirst()"
   *
   * @see UTF8::lcfirst()
   *
   * @param string      $str
   * @param string      $encoding
   * @param bool        $cleanUtf8
   * @param string|null $lang
   * @param bool        $tryToKeepStringLength
   *
   * @return string
   */
  public static function lcword(
      string $str,
      string $encoding = 'UTF-8',
      bool $cleanUtf8 = false,
      string $lang = null,
      bool $tryToKeepStringLength = false
  ): string
  {
    return self::lcfirst($str, $encoding, $cleanUtf8, $lang, $tryToKeepStringLength);
  }

  /**
   * Lowercase for all words in the string.
   *
   * @param string      $str                   <p>The input string.</p>
   * @param string[]    $exceptions            [optional] <p>Exclusion for some words.</p>
   * @param string      $charlist              [optional] <p>Additional chars that contains to words and do not start a
   *                                           new word.</p>
   * @param string      $encoding              [optional] <p>Set the charset.</p>
   * @param bool        $cleanUtf8             [optional] <p>Remove non UTF-8 chars from the string.</p>
   * @param string|null $lang                  [optional] <p>Set the language for special cases: az, el, lt, tr</p>
   * @param bool        $tryToKeepStringLength [optional] <p>true === try to keep the string length: e.g. ẞ -> ß</p>
   *
   * @return string
   */
  public static function lcwords(
      string $str,
      array $exceptions = [],
      string $charlist = '',
      string $encoding = 'UTF-8',
      bool $cleanUtf8 = false,
      string $lang = null,
      bool $tryToKeepStringLength = false
  ): string
  {
    if (!$str) {
      return '';
    }

    $words = self::str_to_words($str, $charlist);
    $newWords = [];

    if (\count($exceptions) > 0) {
      $useExceptions = true;
    } else {
      $useExceptions = false;
    }

    foreach ($words as $word) {

      if (!$word) {
        continue;
      }

      if (
          $useExceptions === false
          ||
          (
              $useExceptions === true
              &&
              !\in_array($word, $exceptions, true)
          )
      ) {
        $word = self::lcfirst($word, $encoding, $cleanUtf8, $lang, $tryToKeepStringLength);
      }

      $newWords[] = $word;
    }

    return \implode('', $newWords);
  }

  /**
   * alias for "UTF8::lcfirst()"
   *
   * @see UTF8::lcfirst()
   *
   * @param string      $str
   * @param string      $encoding
   * @param bool        $cleanUtf8
   * @param string|null $lang
   * @param bool        $tryToKeepStringLength
   *
   * @return string
   */
  public static function lowerCaseFirst(
      string $str,
      string $encoding = 'UTF-8',
      bool $cleanUtf8 = false,
      string $lang = null,
      bool $tryToKeepStringLength = false
  ): string
  {
    return self::lcfirst($str, $encoding, $cleanUtf8, $lang, $tryToKeepStringLength);
  }

  /**
   * Strip whitespace or other characters from beginning of a UTF-8 string.
   *
   * @param string $str   <p>The string to be trimmed</p>
   * @param mixed  $chars <p>Optional characters to be stripped</p>
   *
   * @return string The string with unwanted characters stripped from the left.
   */
  public static function ltrim(string $str = '', $chars = INF): string
  {
    if ('' === $str) {
      return '';
    }

    // Info: http://nadeausoftware.com/articles/2007/9/php_tip_how_strip_punctuation_characters_web_page#Unicodecharactercategories
    if ($chars === INF || !$chars) {
      $pattern = "^[\pZ\pC]+";
    } else {
      $chars = \preg_quote($chars, '/');
      $pattern = "^[$chars]+";
    }

    return self::regex_replace($str, $pattern, '', '', '/');
  }

  /**
   * Returns the UTF-8 character with the maximum code point in the given data.
   *
   * @param string|array<string> $arg <p>A UTF-8 encoded string or an array of such strings.</p>
   *
   * @return string|null The character with the highest code point than others, returns null on failure or empty input.
   */
  public static function max($arg)
  {
    if (\is_array($arg) === true) {
      $arg = \implode('', $arg);
    }

    $codepoints = self::codepoints($arg, false);
    if (\count($codepoints) === 0) {
      return null;
    }

    $codepoint_max = \max($codepoints);

    return self::chr($codepoint_max);
  }

  /**
   * Calculates and returns the maximum number of bytes taken by any
   * UTF-8 encoded character in the given string.
   *
   * @param string $str <p>The original Unicode string.</p>
   *
   * @return int Max byte lengths of the given chars.
   */
  public static function max_chr_width(string $str): int
  {
    $bytes = self::chr_size_list($str);
    if (\count($bytes) > 0) {
      return (int)\max($bytes);
    }

    return 0;
  }

  /**
   * Checks whether mbstring is available on the server.
   *
   * @return bool
   *              <strong>true</strong> if available, <strong>false</strong> otherwise.
   */
  public static function mbstring_loaded(): bool
  {
    $return = \extension_loaded('mbstring') ? true : false;

    if ($return === true) {
      \mb_internal_encoding('UTF-8');
    }

    return $return;
  }

  /**
   * Checks whether mbstring "overloaded" is active on the server.
   *
   * @return bool
   */
  private static function mbstring_overloaded(): bool
  {
    /**
     * INI directive 'mbstring.func_overload' is deprecated since PHP 7.2
     */

    /** @noinspection PhpComposerExtensionStubsInspection */
    /** @noinspection PhpUsageOfSilenceOperatorInspection */
    return \defined('MB_OVERLOAD_STRING')
           &&
           (@\ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING);
  }

  /**
   * Returns the UTF-8 character with the minimum code point in the given data.
   *
   * @param mixed $arg <strong>A UTF-8 encoded string or an array of such strings.</strong>
   *
   * @return string|null The character with the lowest code point than others, returns null on failure or empty input.
   */
  public static function min($arg)
  {
    if (\is_array($arg) === true) {
      $arg = \implode('', $arg);
    }

    $codepoints = self::codepoints($arg, false);
    if (\count($codepoints) === 0) {
      return null;
    }

    $codepoint_min = \min($codepoints);

    return self::chr($codepoint_min);
  }

  /**
   * alias for "UTF8::normalize_encoding()"
   *
   * @see        UTF8::normalize_encoding()
   *
   * @param mixed $encoding
   * @param mixed $fallback
   *
   * @return mixed
   *
   * @deprecated <p>use "UTF8::normalize_encoding()"</p>
   */
  public static function normalizeEncoding($encoding, $fallback = '')
  {
    return self::normalize_encoding($encoding, $fallback);
  }

  /**
   * Normalize the encoding-"name" input.
   *
   * @param mixed $encoding <p>e.g.: ISO, UTF8, WINDOWS-1251 etc.</p>
   * @param mixed $fallback <p>e.g.: UTF-8</p>
   *
   * @return mixed e.g.: ISO-8859-1, UTF-8, WINDOWS-1251 etc.<br>Will return a empty string as fallback (by default)
   */
  public static function normalize_encoding($encoding, $fallback = '')
  {
    static $STATIC_NORMALIZE_ENCODING_CACHE = [];

    // init
    $encoding = (string)$encoding;

    if (
        !$encoding
        ||
        $encoding === '1' // only a fallback, for non "strict_types" usage ...
        ||
        $encoding === '0' // only a fallback, for non "strict_types" usage ...
    ) {
      return $fallback;
    }

    if (
        'UTF-8' === $encoding
        ||
        'UTF8' === $encoding
    ) {
      return 'UTF-8';
    }

    if (
        '8BIT' === $encoding
        ||
        'BINARY' === $encoding
    ) {
      return 'CP850';
    }

    if (
        'HTML' === $encoding
        ||
        'HTML-ENTITIES' === $encoding
    ) {
      return 'HTML-ENTITIES';
    }

    if (isset($STATIC_NORMALIZE_ENCODING_CACHE[$encoding])) {
      return $STATIC_NORMALIZE_ENCODING_CACHE[$encoding];
    }

    if (self::$ENCODINGS === null) {
      self::$ENCODINGS = self::getData('encodings');
    }

    if (\in_array($encoding, self::$ENCODINGS, true)) {
      $STATIC_NORMALIZE_ENCODING_CACHE[$encoding] = $encoding;

      return $encoding;
    }

    $encodingOrig = $encoding;
    $encoding = \strtoupper($encoding);
    $encodingUpperHelper = \preg_replace('/[^a-zA-Z0-9\s]/', '', $encoding);

    $equivalences = [
        'ISO8859'     => 'ISO-8859-1',
        'ISO88591'    => 'ISO-8859-1',
        'ISO'         => 'ISO-8859-1',
        'LATIN'       => 'ISO-8859-1',
        'LATIN1'      => 'ISO-8859-1', // Western European
        'ISO88592'    => 'ISO-8859-2',
        'LATIN2'      => 'ISO-8859-2', // Central European
        'ISO88593'    => 'ISO-8859-3',
        'LATIN3'      => 'ISO-8859-3', // Southern European
        'ISO88594'    => 'ISO-8859-4',
        'LATIN4'      => 'ISO-8859-4', // Northern European
        'ISO88595'    => 'ISO-8859-5',
        'ISO88596'    => 'ISO-8859-6', // Greek
        'ISO88597'    => 'ISO-8859-7',
        'ISO88598'    => 'ISO-8859-8', // Hebrew
        'ISO88599'    => 'ISO-8859-9',
        'LATIN5'      => 'ISO-8859-9', // Turkish
        'ISO885911'   => 'ISO-8859-11',
        'TIS620'      => 'ISO-8859-11', // Thai
        'ISO885910'   => 'ISO-8859-10',
        'LATIN6'      => 'ISO-8859-10', // Nordic
        'ISO885913'   => 'ISO-8859-13',
        'LATIN7'      => 'ISO-8859-13', // Baltic
        'ISO885914'   => 'ISO-8859-14',
        'LATIN8'      => 'ISO-8859-14', // Celtic
        'ISO885915'   => 'ISO-8859-15',
        'LATIN9'      => 'ISO-8859-15', // Western European (with some extra chars e.g. €)
        'ISO885916'   => 'ISO-8859-16',
        'LATIN10'     => 'ISO-8859-16', // Southeast European
        'CP1250'      => 'WINDOWS-1250',
        'WIN1250'     => 'WINDOWS-1250',
        'WINDOWS1250' => 'WINDOWS-1250',
        'CP1251'      => 'WINDOWS-1251',
        'WIN1251'     => 'WINDOWS-1251',
        'WINDOWS1251' => 'WINDOWS-1251',
        'CP1252'      => 'WINDOWS-1252',
        'WIN1252'     => 'WINDOWS-1252',
        'WINDOWS1252' => 'WINDOWS-1252',
        'CP1253'      => 'WINDOWS-1253',
        'WIN1253'     => 'WINDOWS-1253',
        'WINDOWS1253' => 'WINDOWS-1253',
        'CP1254'      => 'WINDOWS-1254',
        'WIN1254'     => 'WINDOWS-1254',
        'WINDOWS1254' => 'WINDOWS-1254',
        'CP1255'      => 'WINDOWS-1255',
        'WIN1255'     => 'WINDOWS-1255',
        'WINDOWS1255' => 'WINDOWS-1255',
        'CP1256'      => 'WINDOWS-1256',
        'WIN1256'     => 'WINDOWS-1256',
        'WINDOWS1256' => 'WINDOWS-1256',
        'CP1257'      => 'WINDOWS-1257',
        'WIN1257'     => 'WINDOWS-1257',
        'WINDOWS1257' => 'WINDOWS-1257',
        'CP1258'      => 'WINDOWS-1258',
        'WIN1258'     => 'WINDOWS-1258',
        'WINDOWS1258' => 'WINDOWS-1258',
        'UTF16'       => 'UTF-16',
        'UTF32'       => 'UTF-32',
        'UTF8'        => 'UTF-8',
        'UTF'         => 'UTF-8',
        'UTF7'        => 'UTF-7',
        '8BIT'        => 'CP850',
        'BINARY'      => 'CP850',
    ];

    if (!empty($equivalences[$encodingUpperHelper])) {
      $encoding = $equivalences[$encodingUpperHelper];
    }

    $STATIC_NORMALIZE_ENCODING_CACHE[$encodingOrig] = $encoding;

    return $encoding;
  }

  /**
   * Standardize line ending to unix-like.
   *
   * @param string $str
   *
   * @return string
   */
  public static function normalize_line_ending(string $str): string
  {
    return (string)str_replace(["\r\n", "\r"], "\n", $str);
  }

  /**
   * Normalize some MS Word special characters.
   *
   * @param string $str <p>The string to be normalized.</p>
   *
   * @return string
   */
  public static function normalize_msword(string $str): string
  {
    if ('' === $str) {
      return '';
    }

    static $UTF8_MSWORD_KEYS_CACHE = null;
    static $UTF8_MSWORD_VALUES_CACHE = null;

    if ($UTF8_MSWORD_KEYS_CACHE === null) {

      if (self::$UTF8_MSWORD === null) {
        self::$UTF8_MSWORD = self::getData('utf8_msword');
      }

      $UTF8_MSWORD_KEYS_CACHE = \array_keys(self::$UTF8_MSWORD);
      $UTF8_MSWORD_VALUES_CACHE = \array_values(self::$UTF8_MSWORD);
    }

    return \str_replace($UTF8_MSWORD_KEYS_CACHE, $UTF8_MSWORD_VALUES_CACHE, $str);
  }

  /**
   * Normalize the whitespace.
   *
   * @param string $str                     <p>The string to be normalized.</p>
   * @param bool   $keepNonBreakingSpace    [optional] <p>Set to true, to keep non-breaking-spaces.</p>
   * @param bool   $keepBidiUnicodeControls [optional] <p>Set to true, to keep non-printable (for the web)
   *                                        bidirectional text chars.</p>
   *
   * @return string
   */
  public static function normalize_whitespace(string $str, bool $keepNonBreakingSpace = false, bool $keepBidiUnicodeControls = false): string
  {
    if ('' === $str) {
      return '';
    }

    static $WHITESPACE_CACHE = [];
    $cacheKey = (int)$keepNonBreakingSpace;

    if (!isset($WHITESPACE_CACHE[$cacheKey])) {

      $WHITESPACE_CACHE[$cacheKey] = self::$WHITESPACE_TABLE;

      if ($keepNonBreakingSpace === true) {
        unset($WHITESPACE_CACHE[$cacheKey]['NO-BREAK SPACE']);
      }

      $WHITESPACE_CACHE[$cacheKey] = \array_values($WHITESPACE_CACHE[$cacheKey]);
    }

    if ($keepBidiUnicodeControls === false) {
      static $BIDI_UNICODE_CONTROLS_CACHE = null;

      if ($BIDI_UNICODE_CONTROLS_CACHE === null) {
        $BIDI_UNICODE_CONTROLS_CACHE = \array_values(self::$BIDI_UNI_CODE_CONTROLS_TABLE);
      }

      $str = \str_replace($BIDI_UNICODE_CONTROLS_CACHE, '', $str);
    }

    return \str_replace($WHITESPACE_CACHE[$cacheKey], ' ', $str);
  }

  /**
   * Calculates Unicode code point of the given UTF-8 encoded character.
   *
   * INFO: opposite to UTF8::chr()
   *
   * @param string $chr      <p>The character of which to calculate code point.<p/>
   * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return int
   *             Unicode code point of the given character,<br>
   *             0 on invalid UTF-8 byte sequence.
   */
  public static function ord($chr, string $encoding = 'UTF-8'): int
  {
    // init
    $chr = (string)$chr;

    static $CHAR_CACHE = [];

    // save the original string
    $chr_orig = $chr;

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');

      // check again, if it's still not UTF-8
      if ($encoding !== 'UTF-8') {
        $chr = self::encode($encoding, $chr);
      }
    }

    $cacheKey = $chr_orig . $encoding;
    if (isset($CHAR_CACHE[$cacheKey]) === true) {
      return $CHAR_CACHE[$cacheKey];
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['intlChar'] === true) {
      /** @noinspection PhpComposerExtensionStubsInspection */
      $code = \IntlChar::ord($chr);
      if ($code) {
        return $CHAR_CACHE[$cacheKey] = $code;
      }
    }

    /** @noinspection CallableParameterUseCaseInTypeContextInspection */
    $chr = \unpack('C*', (string)self::substr($chr, 0, 4, 'CP850'));
    $code = $chr ? $chr[1] : 0;

    if (0xF0 <= $code && isset($chr[4])) {
      /** @noinspection UnnecessaryCastingInspection */
      return $CHAR_CACHE[$cacheKey] = (int)((($code - 0xF0) << 18) + (($chr[2] - 0x80) << 12) + (($chr[3] - 0x80) << 6) + $chr[4] - 0x80);
    }

    if (0xE0 <= $code && isset($chr[3])) {
      /** @noinspection UnnecessaryCastingInspection */
      return $CHAR_CACHE[$cacheKey] = (int)((($code - 0xE0) << 12) + (($chr[2] - 0x80) << 6) + $chr[3] - 0x80);
    }

    if (0xC0 <= $code && isset($chr[2])) {
      /** @noinspection UnnecessaryCastingInspection */
      return $CHAR_CACHE[$cacheKey] = (int)((($code - 0xC0) << 6) + $chr[2] - 0x80);
    }

    return $CHAR_CACHE[$cacheKey] = $code;
  }

  /**
   * Parses the string into an array (into the the second parameter).
   *
   * WARNING: Instead of "parse_str()" this method do not (re-)placing variables in the current scope,
   *          if the second parameter is not set!
   *
   * @link http://php.net/manual/en/function.parse-str.php
   *
   * @param string $str       <p>The input string.</p>
   * @param array  $result    <p>The result will be returned into this reference parameter.</p>
   * @param bool   $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return bool
   *              Will return <strong>false</strong> if php can't parse the string and we haven't any $result.
   */
  public static function parse_str(string $str, &$result, bool $cleanUtf8 = false): bool
  {
    if ($cleanUtf8 === true) {
      $str = self::clean($str);
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['mbstring'] === true) {
      $return = \mb_parse_str($str, $result);

      return !($return === false || empty($result));
    }

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    \parse_str($str, $result);

    return !empty($result);
  }

  /**
   * Checks if \u modifier is available that enables Unicode support in PCRE.
   *
   * @return bool
   *              <strong>true</strong> if support is available,<br>
   *              <strong>false</strong> otherwise.
   */
  public static function pcre_utf8_support(): bool
  {
    /** @noinspection PhpUsageOfSilenceOperatorInspection */
    return (bool)@\preg_match('//u', '');
  }

  /**
   * Create an array containing a range of UTF-8 characters.
   *
   * @param mixed $var1 <p>Numeric or hexadecimal code points, or a UTF-8 character to start from.</p>
   * @param mixed $var2 <p>Numeric or hexadecimal code points, or a UTF-8 character to end at.</p>
   *
   * @return string[]
   */
  public static function range($var1, $var2): array
  {
    if (!$var1 || !$var2) {
      return [];
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['ctype'] === false) {
      throw new \RuntimeException('ext-ctype: is not installed');
    }

    /** @noinspection PhpComposerExtensionStubsInspection */
    if (\ctype_digit((string)$var1)) {
      $start = (int)$var1;
    } /** @noinspection PhpComposerExtensionStubsInspection */ elseif (\ctype_xdigit($var1)) {
      $start = (int)self::hex_to_int($var1);
    } else {
      $start = self::ord($var1);
    }

    if (!$start) {
      return [];
    }

    /** @noinspection PhpComposerExtensionStubsInspection */
    if (\ctype_digit((string)$var2)) {
      $end = (int)$var2;
    } /** @noinspection PhpComposerExtensionStubsInspection */ elseif (\ctype_xdigit($var2)) {
      $end = (int)self::hex_to_int($var2);
    } else {
      $end = self::ord($var2);
    }

    if (!$end) {
      return [];
    }

    return \array_map(
        [
            self::class,
            'chr',
        ],
        \range($start, $end)
    );
  }

  /**
   * Multi decode html entity & fix urlencoded-win1252-chars.
   *
   * e.g:
   * 'test+test'                     => 'test+test'
   * 'D&#252;sseldorf'               => 'Düsseldorf'
   * 'D%FCsseldorf'                  => 'Düsseldorf'
   * 'D&#xFC;sseldorf'               => 'Düsseldorf'
   * 'D%26%23xFC%3Bsseldorf'         => 'Düsseldorf'
   * 'DÃ¼sseldorf'                   => 'Düsseldorf'
   * 'D%C3%BCsseldorf'               => 'Düsseldorf'
   * 'D%C3%83%C2%BCsseldorf'         => 'Düsseldorf'
   * 'D%25C3%2583%25C2%25BCsseldorf' => 'Düsseldorf'
   *
   * @param string $str          <p>The input string.</p>
   * @param bool   $multi_decode <p>Decode as often as possible.</p>
   *
   * @return string
   */
  public static function rawurldecode(string $str, bool $multi_decode = true): string
  {
    if ('' === $str) {
      return '';
    }

    $pattern = '/%u([0-9a-f]{3,4})/i';
    if (\preg_match($pattern, $str)) {
      $str = (string)\preg_replace($pattern, '&#x\\1;', \rawurldecode($str));
    }

    $flags = ENT_QUOTES | ENT_HTML5;

    do {
      $str_compare = $str;

      $str = self::fix_simple_utf8(
          \rawurldecode(
              self::html_entity_decode(
                  self::to_utf8($str),
                  $flags
              )
          )
      );

    } while ($multi_decode === true && $str_compare !== $str);

    return $str;
  }

  /**
   * @param array $strings
   * @param bool  $removeEmptyValues
   * @param int   $removeShortValues
   *
   * @return array
   */
  private static function reduce_string_array(array $strings, bool $removeEmptyValues, int $removeShortValues = null): array
  {
    // init
    $return = [];

    foreach ($strings as $str) {
      if (
          $removeShortValues !== null
          &&
          self::strlen($str) <= $removeShortValues
      ) {
        continue;
      }

      if (
          $removeEmptyValues === true
          &&
          \trim($str) === ''
      ) {
        continue;
      }

      $return[] = $str;
    }

    return $return;
  }

  /**
   * Replaces all occurrences of $pattern in $str by $replacement.
   *
   * @param string $str         <p>The input string.</p>
   * @param string $pattern     <p>The regular expression pattern.</p>
   * @param string $replacement <p>The string to replace with.</p>
   * @param string $options     [optional] <p>Matching conditions to be used.</p>
   * @param string $delimiter   [optional] <p>Delimiter the the regex. Default: '/'</p>
   *
   * @return string
   */
  public static function regex_replace(string $str, string $pattern, string $replacement, string $options = '', string $delimiter = '/'): string
  {
    if ($options === 'msr') {
      $options = 'ms';
    }

    // fallback
    if (!$delimiter) {
      $delimiter = '/';
    }

    $str = (string)\preg_replace(
        $delimiter . $pattern . $delimiter . 'u' . $options,
        $replacement,
        $str
    );

    return $str;
  }

  /**
   * alias for "UTF8::remove_bom()"
   *
   * @see        UTF8::remove_bom()
   *
   * @param string $str
   *
   * @return string
   *
   * @deprecated <p>use "UTF8::remove_bom()"</p>
   */
  public static function removeBOM(string $str): string
  {
    return self::remove_bom($str);
  }

  /**
   * Remove the BOM from UTF-8 / UTF-16 / UTF-32 strings.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return string String without UTF-BOM.
   */
  public static function remove_bom(string $str): string
  {
    if ('' === $str) {
      return '';
    }

    $strLength = self::strlen_in_byte($str);
    foreach (self::$BOM as $bomString => $bomByteLength) {
      if (0 === self::strpos_in_byte($str, $bomString, 0)) {
        $strTmp = self::substr_in_byte($str, $bomByteLength, $strLength);
        if ($strTmp === false) {
          return '';
        }

        $strLength -= $bomByteLength;

        $str = (string)$strTmp;
      }
    }

    return $str;
  }

  /**
   * Removes duplicate occurrences of a string in another string.
   *
   * @param string          $str  <p>The base string.</p>
   * @param string|string[] $what <p>String to search for in the base string.</p>
   *
   * @return string The result string with removed duplicates.
   */
  public static function remove_duplicates(string $str, $what = ' '): string
  {
    if (\is_string($what) === true) {
      $what = [$what];
    }

    if (\is_array($what) === true) {
      /** @noinspection ForeachSourceInspection */
      foreach ($what as $item) {
        $str = (string)\preg_replace('/(' . \preg_quote($item, '/') . ')+/', $item, $str);
      }
    }

    return $str;
  }

  /**
   * Remove html via "strip_tags()" from the string.
   *
   * @param string $str
   * @param string $allowableTags [optional] <p>You can use the optional second parameter to specify tags which should
   *                              not be stripped. Default: null
   *                              </p>
   *
   * @return string
   */
  public static function remove_html(string $str, string $allowableTags = ''): string
  {
    return \strip_tags($str, $allowableTags);
  }

  /**
   * Remove all breaks [<br> | \r\n | \r | \n | ...] from the string.
   *
   * @param string $str
   * @param string $replacement [optional] <p>Default is a empty string.</p>
   *
   * @return string
   */
  public static function remove_html_breaks(string $str, string $replacement = ''): string
  {
    return (string)\preg_replace("#/\r\n|\r|\n|<br.*/?>#isU", $replacement, $str);
  }

  /**
   * Remove invisible characters from a string.
   *
   * e.g.: This prevents sandwiching null characters between ascii characters, like Java\0script.
   *
   * copy&past from https://github.com/bcit-ci/CodeIgniter/blob/develop/system/core/Common.php
   *
   * @param string $str
   * @param bool   $url_encoded
   * @param string $replacement
   *
   * @return string
   */
  public static function remove_invisible_characters(string $str, bool $url_encoded = true, string $replacement = ''): string
  {
    // init
    $non_displayables = [];

    // every control character except newline (dec 10),
    // carriage return (dec 13) and horizontal tab (dec 09)
    if ($url_encoded) {
      $non_displayables[] = '/%0[0-8bcef]/'; // url encoded 00-08, 11, 12, 14, 15
      $non_displayables[] = '/%1[0-9a-f]/'; // url encoded 16-31
    }

    $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127

    do {
      $str = (string)\preg_replace($non_displayables, $replacement, $str, -1, $count);
    } while ($count !== 0);

    return $str;
  }

  /**
   * Returns a new string with the prefix $substring removed, if present.
   *
   * @param string $str
   * @param string $substring <p>The prefix to remove.</p>
   * @param string $encoding  [optional] <p>Default: UTF-8</p>
   *
   * @return string String without the prefix $substring.
   */
  public static function remove_left(string $str, string $substring, string $encoding = 'UTF-8'): string
  {
    if (self::str_starts_with($str, $substring)) {

      return (string)self::substr(
          $str,
          self::strlen($substring, $encoding),
          null,
          $encoding
      );
    }

    return $str;
  }

  /**
   * Returns a new string with the suffix $substring removed, if present.
   *
   * @param string $str
   * @param string $substring <p>The suffix to remove.</p>
   * @param string $encoding  [optional] <p>Default: UTF-8</p>
   *
   * @return string String having a $str without the suffix $substring.
   */
  public static function remove_right(string $str, string $substring, string $encoding = 'UTF-8'): string
  {
    if (self::str_ends_with($str, $substring)) {

      return (string)self::substr(
          $str,
          0,
          self::strlen($str, $encoding) - self::strlen($substring, $encoding)
      );
    }

    return $str;
  }

  /**
   * Replaces all occurrences of $search in $str by $replacement.
   *
   * @param string $str           <p>The input string.</p>
   * @param string $search        <p>The needle to search for.</p>
   * @param string $replacement   <p>The string to replace with.</p>
   * @param bool   $caseSensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>
   *
   * @return string String after the replacements.
   */
  public static function replace(string $str, string $search, string $replacement, bool $caseSensitive = true): string
  {
    if ($caseSensitive) {
      return self::str_replace($search, $replacement, $str);
    }

    return self::str_ireplace($search, $replacement, $str);
  }

  /**
   * Replaces all occurrences of $search in $str by $replacement.
   *
   * @param string       $str           <p>The input string.</p>
   * @param array        $search        <p>The elements to search for.</p>
   * @param string|array $replacement   <p>The string to replace with.</p>
   * @param bool         $caseSensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>
   *
   * @return string String after the replacements.
   */
  public static function replace_all(string $str, array $search, $replacement, bool $caseSensitive = true): string
  {
    if ($caseSensitive) {
      return self::str_replace($search, $replacement, $str);
    }

    return self::str_ireplace($search, $replacement, $str);
  }

  /**
   * Replace the diamond question mark (�) and invalid-UTF8 chars with the replacement.
   *
   * @param string $str                <p>The input string</p>
   * @param string $replacementChar    <p>The replacement character.</p>
   * @param bool   $processInvalidUtf8 <p>Convert invalid UTF-8 chars </p>
   *
   * @return string
   */
  public static function replace_diamond_question_mark(string $str, string $replacementChar = '', bool $processInvalidUtf8 = true): string
  {
    if ('' === $str) {
      return '';
    }

    if ($processInvalidUtf8 === true) {
      $replacementCharHelper = $replacementChar;
      if ($replacementChar === '') {
        $replacementCharHelper = 'none';
      }

      if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
        self::checkForSupport();
      }

      if (self::$SUPPORT['mbstring'] === false) {
        // if there is no native support for "mbstring",
        // then we need to clean the string before ...
        $str = self::clean($str);
      }

      // always fallback via symfony polyfill
      $save = \mb_substitute_character();
      \mb_substitute_character($replacementCharHelper);
      $strTmp = \mb_convert_encoding($str, 'UTF-8', 'UTF-8');
      \mb_substitute_character($save);

      if (\is_string($strTmp)) {
        $str = $strTmp;
      } else {
        $str = '';
      }
    }

    return str_replace(
        [
            "\xEF\xBF\xBD",
            '�',
        ],
        [
            $replacementChar,
            $replacementChar,
        ],
        $str
    );
  }

  /**
   * Strip whitespace or other characters from end of a UTF-8 string.
   *
   * @param string $str   <p>The string to be trimmed.</p>
   * @param mixed  $chars <p>Optional characters to be stripped.</p>
   *
   * @return string The string with unwanted characters stripped from the right.
   */
  public static function rtrim(string $str = '', $chars = INF): string
  {
    if ('' === $str) {
      return '';
    }

    // Info: http://nadeausoftware.com/articles/2007/9/php_tip_how_strip_punctuation_characters_web_page#Unicodecharactercategories
    if ($chars === INF || !$chars) {
      $pattern = "[\pZ\pC]+\$";
    } else {
      $chars = \preg_quote($chars, '/');
      $pattern = "[$chars]+\$";
    }

    return self::regex_replace($str, $pattern, '', '', '/');
  }

  /**
   * rxClass
   *
   * @param string $s
   * @param string $class
   *
   * @return string
   */
  private static function rxClass(string $s, string $class = ''): string
  {
    static $RX_CLASSS_CACHE = [];

    $cacheKey = $s . $class;

    if (isset($RX_CLASSS_CACHE[$cacheKey])) {
      return $RX_CLASSS_CACHE[$cacheKey];
    }

    /** @noinspection CallableParameterUseCaseInTypeContextInspection */
    $class = [$class];

    /** @noinspection SuspiciousLoopInspection */
    foreach (self::str_split($s) as $s) {
      if ('-' === $s) {
        $class[0] = '-' . $class[0];
      } elseif (!isset($s[2])) {
        $class[0] .= \preg_quote($s, '/');
      } elseif (1 === self::strlen($s)) {
        $class[0] .= $s;
      } else {
        $class[] = $s;
      }
    }

    if ($class[0]) {
      $class[0] = '[' . $class[0] . ']';
    }

    if (1 === \count($class)) {
      $return = $class[0];
    } else {
      $return = '(?:' . \implode('|', $class) . ')';
    }

    $RX_CLASSS_CACHE[$cacheKey] = $return;

    return $return;
  }

  /**
   * WARNING: Print native UTF-8 support (libs), e.g. for debugging.
   */
  public static function showSupport()
  {
    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    echo '<pre>';
    foreach (self::$SUPPORT as $key => $value) {
      echo $key . ' - ' . \print_r($value, true) . "\n<br>";
    }
    echo '</pre>';
  }

  /**
   * Converts a UTF-8 character to HTML Numbered Entity like "&#123;".
   *
   * @param string $char           <p>The Unicode character to be encoded as numbered entity.</p>
   * @param bool   $keepAsciiChars <p>Set to <strong>true</strong> to keep ASCII chars.</>
   * @param string $encoding       [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return string The HTML numbered entity.
   */
  public static function single_chr_html_encode(string $char, bool $keepAsciiChars = false, string $encoding = 'UTF-8'): string
  {
    if ('' === $char) {
      return '';
    }

    if (
        $keepAsciiChars === true
        &&
        self::is_ascii($char) === true
    ) {
      return $char;
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    return '&#' . self::ord($char, $encoding) . ';';
  }

  /**
   * @param string $str
   * @param int    $tabLength
   *
   * @return string
   */
  public static function spaces_to_tabs(string $str, int $tabLength = 4): string
  {
    return \str_replace(\str_repeat(' ', $tabLength), "\t", $str);
  }

  /**
   * Convert a string to an array of Unicode characters.
   *
   * @param string|int|string[]|int[] $str       <p>The string to split into array.</p>
   * @param int                       $length    [optional] <p>Max character length of each array element.</p>
   * @param bool                      $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return string[] An array containing chunks of the string.
   */
  public static function split($str, int $length = 1, bool $cleanUtf8 = false): array
  {
    if ($length <= 0) {
      return [];
    }

    if (\is_array($str) === true) {
      foreach ($str as $k => $v) {
        $str[$k] = self::split($v, $length);
      }

      return $str;
    }

    // init
    $str = (string)$str;

    if ('' === $str) {
      return [];
    }

    // init
    $ret = [];

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if ($cleanUtf8 === true) {
      $str = self::clean($str);
    }

    if (self::$SUPPORT['pcre_utf8'] === true) {

      \preg_match_all('/./us', $str, $retArray);
      if (isset($retArray[0])) {
        $ret = $retArray[0];
      }
      unset($retArray);

    } else {

      // fallback

      if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
        self::checkForSupport();
      }

      $len = self::strlen_in_byte($str);

      /** @noinspection ForeachInvariantsInspection */
      for ($i = 0; $i < $len; $i++) {

        if (($str[$i] & "\x80") === "\x00") {

          $ret[] = $str[$i];

        } elseif (
            isset($str[$i + 1])
            &&
            ($str[$i] & "\xE0") === "\xC0"
        ) {

          if (($str[$i + 1] & "\xC0") === "\x80") {
            $ret[] = $str[$i] . $str[$i + 1];

            $i++;
          }

        } elseif (
            isset($str[$i + 2])
            &&
            ($str[$i] & "\xF0") === "\xE0"
        ) {

          if (
              ($str[$i + 1] & "\xC0") === "\x80"
              &&
              ($str[$i + 2] & "\xC0") === "\x80"
          ) {
            $ret[] = $str[$i] . $str[$i + 1] . $str[$i + 2];

            $i += 2;
          }

        } elseif (
            isset($str[$i + 3])
            &&
            ($str[$i] & "\xF8") === "\xF0"
        ) {

          if (
              ($str[$i + 1] & "\xC0") === "\x80"
              &&
              ($str[$i + 2] & "\xC0") === "\x80"
              &&
              ($str[$i + 3] & "\xC0") === "\x80"
          ) {
            $ret[] = $str[$i] . $str[$i + 1] . $str[$i + 2] . $str[$i + 3];

            $i += 3;
          }

        }
      }
    }

    if ($length > 1) {
      $ret = \array_chunk($ret, $length);

      return \array_map(
          function ($item) {
            return \implode('', $item);
          }, $ret
      );
    }

    if (isset($ret[0]) && $ret[0] === '') {
      return [];
    }

    return $ret;
  }

  /**
   * Returns a camelCase version of the string. Trims surrounding spaces,
   * capitalizes letters following digits, spaces, dashes and underscores,
   * and removes spaces, dashes, as well as underscores.
   *
   * @param string      $str                   <p>The input string.</p>
   * @param string      $encoding              [optional] <p>Default: UTF-8</p>
   * @param bool        $cleanUtf8             [optional] <p>Remove non UTF-8 chars from the string.</p>
   * @param string|null $lang                  [optional] <p>Set the language for special cases: az, el, lt, tr</p>
   * @param bool        $tryToKeepStringLength [optional] <p>true === try to keep the string length: e.g. ẞ -> ß</p>
   *
   * @return string
   */
  public static function str_camelize(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false, string $lang = null, bool $tryToKeepStringLength = false): string
  {
    $str = self::lcfirst(self::trim($str), $encoding, $cleanUtf8, $lang, $tryToKeepStringLength);
    $str = (string)\preg_replace('/^[-_]+/', '', $str);

    $str = (string)\preg_replace_callback(
        '/[-_\s]+(.)?/u',
        function ($match) use ($encoding, $cleanUtf8, $lang, $tryToKeepStringLength) {
          if (isset($match[1])) {
            return UTF8::strtoupper($match[1], $encoding, $cleanUtf8, $lang, $tryToKeepStringLength);
          }

          return '';
        },
        $str
    );

    $str = (string)\preg_replace_callback(
        '/[\d]+(.)?/u',
        function ($match) use ($encoding, $cleanUtf8, $lang, $tryToKeepStringLength) {
          return UTF8::strtoupper($match[0], $encoding, $cleanUtf8, $lang, $tryToKeepStringLength);
        },
        $str
    );

    return $str;
  }

  /**
   * Returns the string with the first letter of each word capitalized,
   * except for when the word is a name which shouldn't be capitalized.
   *
   * @param string $str
   *
   * @return string String with $str capitalized.
   */
  public static function str_capitalize_name(string $str): string
  {
    $str = self::collapse_whitespace($str);

    $str = self::str_capitalize_name_helper($str, ' ');
    $str = self::str_capitalize_name_helper($str, '-');

    return $str;
  }

  /**
   * Personal names such as "Marcus Aurelius" are sometimes typed incorrectly using lowercase ("marcus aurelius").
   *
   * @param string $names
   * @param string $delimiter
   * @param string $encoding
   *
   * @return string
   */
  private static function str_capitalize_name_helper(string $names, string $delimiter, string $encoding = 'UTF-8'): string
  {
    // init
    $namesArray = \explode($delimiter, $names);

    if ($namesArray === false) {
      return '';
    }

    $specialCases = [
        'names'    => [
            'ab',
            'af',
            'al',
            'and',
            'ap',
            'bint',
            'binte',
            'da',
            'de',
            'del',
            'den',
            'der',
            'di',
            'dit',
            'ibn',
            'la',
            'mac',
            'nic',
            'of',
            'ter',
            'the',
            'und',
            'van',
            'von',
            'y',
            'zu',
        ],
        'prefixes' => [
            'al-',
            "d'",
            'ff',
            "l'",
            'mac',
            'mc',
            'nic',
        ],
    ];

    foreach ($namesArray as &$name) {
      if (\in_array($name, $specialCases['names'], true)) {
        continue;
      }

      $continue = false;

      if ($delimiter == '-') {
        foreach ($specialCases['names'] as $beginning) {
          if (self::strpos($name, $beginning, 0, $encoding) === 0) {
            $continue = true;
          }
        }
      }

      foreach ($specialCases['prefixes'] as $beginning) {
        if (self::strpos($name, $beginning, 0, $encoding) === 0) {
          $continue = true;
        }
      }

      if ($continue) {
        continue;
      }

      $name = self::str_upper_first($name);
    }

    return \implode($delimiter, $namesArray);
  }

  /**
   * Returns true if the string contains $needle, false otherwise. By default
   * the comparison is case-sensitive, but can be made insensitive by setting
   * $caseSensitive to false.
   *
   * @param string $haystack      <p>The input string.</p>
   * @param string $needle        <p>Substring to look for.</p>
   * @param bool   $caseSensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>
   * @param string $encoding      [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return bool Whether or not $haystack contains $needle.
   */
  public static function str_contains(string $haystack, string $needle, $caseSensitive = true, string $encoding = 'UTF-8'): bool
  {
    if ('' === $haystack || '' === $needle) {
      return false;
    }

    // only a fallback to prevent BC in the api ...
    if ($caseSensitive !== false && $caseSensitive !== true) {
      $encoding = (string)$caseSensitive;
    }

    if ($caseSensitive) {
      return (self::strpos($haystack, $needle, 0, $encoding) !== false);
    }

    return (self::stripos($haystack, $needle, 0, $encoding) !== false);
  }

  /**
   * Returns true if the string contains all $needles, false otherwise. By
   * default the comparison is case-sensitive, but can be made insensitive by
   * setting $caseSensitive to false.
   *
   * @param string $haystack      <p>The input string.</p>
   * @param array  $needles       <p>SubStrings to look for.</p>
   * @param bool   $caseSensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>
   * @param string $encoding      [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return bool Whether or not $haystack contains $needle.
   */
  public static function str_contains_all(string $haystack, array $needles, $caseSensitive = true, string $encoding = 'UTF-8'): bool
  {
    if ('' === $haystack) {
      return false;
    }

    if (empty($needles)) {
      return false;
    }

    // only a fallback to prevent BC in the api ...
    if ($caseSensitive !== false && $caseSensitive !== true) {
      $encoding = (string)$caseSensitive;
    }

    foreach ($needles as $needle) {
      if (!self::str_contains($haystack, $needle, $caseSensitive, $encoding)) {
        return false;
      }
    }

    return true;
  }

  /**
   * Returns true if the string contains any $needles, false otherwise. By
   * default the comparison is case-sensitive, but can be made insensitive by
   * setting $caseSensitive to false.
   *
   * @param string $haystack      <p>The input string.</p>
   * @param array  $needles       <p>SubStrings to look for.</p>
   * @param bool   $caseSensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>
   * @param string $encoding      [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return bool
   *               Whether or not $str contains $needle.
   */
  public static function str_contains_any(string $haystack, array $needles, $caseSensitive = true, string $encoding = 'UTF-8'): bool
  {
    if (empty($needles)) {
      return false;
    }

    foreach ($needles as $needle) {
      if (self::str_contains($haystack, $needle, $caseSensitive, $encoding)) {
        return true;
      }
    }

    return false;
  }

  /**
   * Returns a lowercase and trimmed string separated by dashes. Dashes are
   * inserted before uppercase characters (with the exception of the first
   * character of the string), and in place of spaces as well as underscores.
   *
   * @param string $str      <p>The input string.</p>
   * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return string
   */
  public static function str_dasherize(string $str, string $encoding = 'UTF-8'): string
  {
    return self::str_delimit($str, '-', $encoding);
  }

  /**
   * Returns a lowercase and trimmed string separated by the given delimiter.
   * Delimiters are inserted before uppercase characters (with the exception
   * of the first character of the string), and in place of spaces, dashes,
   * and underscores. Alpha delimiters are not converted to lowercase.
   *
   * @param string      $str                           <p>The input string.</p>
   * @param string      $delimiter                     <p>Sequence used to separate parts of the string.</p>
   * @param string      $encoding                      [optional] <p>Set the charset for e.g. "mb_" function</p>
   * @param bool        $cleanUtf8                     [optional] <p>Remove non UTF-8 chars from the string.</p>
   * @param string|null $lang                          [optional] <p>Set the language for special cases: az, el, lt,
   *                                                   tr</p>
   * @param bool        $tryToKeepStringLength         [optional] <p>true === try to keep the string length: e.g. ẞ ->
   *                                                   ß</p>
   *
   * @return string
   */
  public static function str_delimit(string $str, string $delimiter, string $encoding = 'UTF-8', bool $cleanUtf8 = false, string $lang = null, bool $tryToKeepStringLength = false): string
  {
    $str = self::trim($str);

    $str = (string)\preg_replace('/\B([A-Z])/u', '-\1', $str);

    $str = self::strtolower($str, $encoding, $cleanUtf8, $lang, $tryToKeepStringLength);

    return (string)\preg_replace('/[-_\s]+/u', $delimiter, $str);
  }

  /**
   * Optimized "mb_detect_encoding()"-function -> with support for UTF-16 and UTF-32.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return false|string
   *                      The detected string-encoding e.g. UTF-8 or UTF-16BE,<br>
   *                      otherwise it will return false e.g. for BINARY or not detected encoding.
   */
  public static function str_detect_encoding($str)
  {
    // init
    $str = (string)$str;

    //
    // 1.) check binary strings (010001001...) like UTF-16 / UTF-32 / PDF / Images / ...
    //

    if (self::is_binary($str, true) === true) {

      $isUtf16 = self::is_utf16($str, false);
      if ($isUtf16 === 1) {
        return 'UTF-16LE';
      }
      if ($isUtf16 === 2) {
        return 'UTF-16BE';
      }

      $isUtf32 = self::is_utf32($str, false);
      if ($isUtf32 === 1) {
        return 'UTF-32LE';
      }
      if ($isUtf32 === 2) {
        return 'UTF-32BE';
      }

      // is binary but not "UTF-16" or "UTF-32"
      return false;
    }

    //
    // 2.) simple check for ASCII chars
    //

    if (self::is_ascii($str) === true) {
      return 'ASCII';
    }

    //
    // 3.) simple check for UTF-8 chars
    //

    if (self::is_utf8($str) === true) {
      return 'UTF-8';
    }

    //
    // 4.) check via "mb_detect_encoding()"
    //
    // INFO: UTF-16, UTF-32, UCS2 and UCS4, encoding detection will fail always with "mb_detect_encoding()"

    $detectOrder = [
        'ISO-8859-1',
        'ISO-8859-2',
        'ISO-8859-3',
        'ISO-8859-4',
        'ISO-8859-5',
        'ISO-8859-6',
        'ISO-8859-7',
        'ISO-8859-8',
        'ISO-8859-9',
        'ISO-8859-10',
        'ISO-8859-13',
        'ISO-8859-14',
        'ISO-8859-15',
        'ISO-8859-16',
        'WINDOWS-1251',
        'WINDOWS-1252',
        'WINDOWS-1254',
        'CP932',
        'CP936',
        'CP950',
        'CP866',
        'CP850',
        'CP51932',
        'CP50220',
        'CP50221',
        'CP50222',
        'ISO-2022-JP',
        'ISO-2022-KR',
        'JIS',
        'JIS-ms',
        'EUC-CN',
        'EUC-JP',
    ];

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['mbstring'] === true) {
      // info: do not use the symfony polyfill here
      $encoding = \mb_detect_encoding($str, $detectOrder, true);
      if ($encoding) {
        return $encoding;
      }
    }

    //
    // 5.) check via "iconv()"
    //

    if (self::$ENCODINGS === null) {
      self::$ENCODINGS = self::getData('encodings');
    }

    foreach (self::$ENCODINGS as $encodingTmp) {
      # INFO: //IGNORE but still throw notice
      /** @noinspection PhpUsageOfSilenceOperatorInspection */
      if ((string)@\iconv($encodingTmp, $encodingTmp . '//IGNORE', $str) === $str) {
        return $encodingTmp;
      }
    }

    return false;
  }

  /**
   * Check if the string ends with the given substring.
   *
   * @param string $haystack <p>The string to search in.</p>
   * @param string $needle   <p>The substring to search for.</p>
   *
   * @return bool
   */
  public static function str_ends_with(string $haystack, string $needle): bool
  {
    if ('' === $haystack || '' === $needle) {
      return false;
    }

    return \substr($haystack, -\strlen($needle)) === $needle;
  }

  /**
   * Returns true if the string ends with any of $substrings, false otherwise.
   *
   * - case-sensitive
   *
   * @param string   $str        <p>The input string.</p>
   * @param string[] $substrings <p>Substrings to look for.</p>
   *
   * @return bool Whether or not $str ends with $substring.
   */
  public static function str_ends_with_any(string $str, array $substrings): bool
  {
    if (empty($substrings)) {
      return false;
    }

    foreach ($substrings as $substring) {
      if (self::str_ends_with($str, $substring)) {
        return true;
      }
    }

    return false;
  }

  /**
   * Ensures that the string begins with $substring. If it doesn't, it's
   * prepended.
   *
   * @param string $str       <p>The input string.</p>
   * @param string $substring <p>The substring to add if not present.</p>
   *
   * @return string
   */
  public static function str_ensure_left(string $str, string $substring): string
  {
    if (!self::str_starts_with($str, $substring)) {
      $str = $substring . $str;
    }

    return $str;
  }

  /**
   * Ensures that the string ends with $substring. If it doesn't, it's appended.
   *
   * @param string $str       <p>The input string.</p>
   * @param string $substring <p>The substring to add if not present.</p>
   *
   * @return string
   */
  public static function str_ensure_right(string $str, string $substring): string
  {
    if (!self::str_ends_with($str, $substring)) {
      $str .= $substring;
    }

    return $str;
  }

  /**
   * Capitalizes the first word of the string, replaces underscores with
   * spaces, and strips '_id'.
   *
   * @param string $str
   *
   * @return string
   */
  public static function str_humanize($str): string
  {
    $str = self::str_replace(
        [
            '_id',
            '_',
        ],
        [
            '',
            ' ',
        ],
        $str
    );

    return self::ucfirst(self::trim($str));
  }

  /**
   * Check if the string ends with the given substring, case insensitive.
   *
   * @param string $haystack <p>The string to search in.</p>
   * @param string $needle   <p>The substring to search for.</p>
   *
   * @return bool
   */
  public static function str_iends_with(string $haystack, string $needle): bool
  {
    if ('' === $haystack || '' === $needle) {
      return false;
    }

    if (self::strcasecmp(\substr($haystack, -\strlen($needle)), $needle) === 0) {
      return true;
    }

    return false;
  }

  /**
   * Returns true if the string ends with any of $substrings, false otherwise.
   *
   * - case-insensitive
   *
   * @param string   $str        <p>The input string.</p>
   * @param string[] $substrings <p>Substrings to look for.</p>
   *
   * @return bool Whether or not $str ends with $substring.
   */
  public static function str_iends_with_any(string $str, array $substrings): bool
  {
    if (empty($substrings)) {
      return false;
    }

    foreach ($substrings as $substring) {
      if (self::str_iends_with($str, $substring)) {
        return true;
      }
    }

    return false;
  }

  /**
   * Returns the index of the first occurrence of $needle in the string,
   * and false if not found. Accepts an optional offset from which to begin
   * the search.
   *
   * @param string $str      <p>The input string.</p>
   * @param string $needle   <p>Substring to look for.</p>
   * @param int    $offset   [optional] <p>Offset from which to search. Default: 0</p>
   * @param string $encoding [optional] <p>Default: UTF-8</p>
   *
   * @return int|false
   *                    The occurrence's <strong>index</strong> if found, otherwise <strong>false</strong>.
   */
  public static function str_iindex_first(string $str, string $needle, int $offset = 0, string $encoding = 'UTF-8')
  {
    return self::stripos(
        $str,
        $needle,
        $offset,
        $encoding
    );
  }

  /**
   * Returns the index of the last occurrence of $needle in the string,
   * and false if not found. Accepts an optional offset from which to begin
   * the search. Offsets may be negative to count from the last character
   * in the string.
   *
   * @param string $str      <p>The input string.</p>
   * @param string $needle   <p>Substring to look for.</p>
   * @param int    $offset   [optional] <p>Offset from which to search. Default: 0</p>
   * @param string $encoding [optional] <p>Default: UTF-8</p>
   *
   * @return int|false
   *                   The last occurrence's <strong>index</strong> if found, otherwise <strong>false</strong>.
   */
  public static function str_iindex_last(string $str, string $needle, int $offset = 0, string $encoding = 'UTF-8')
  {
    return self::strripos(
        $str,
        $needle,
        $offset,
        $encoding
    );
  }

  /**
   * Returns the index of the first occurrence of $needle in the string,
   * and false if not found. Accepts an optional offset from which to begin
   * the search.
   *
   * @param string $str      <p>The input string.</p>
   * @param string $needle   <p>Substring to look for.</p>
   * @param int    $offset   [optional] <p>Offset from which to search. Default: 0</p>
   * @param string $encoding [optional] <p>Default: UTF-8</p>
   *
   * @return int|false
   *                   The occurrence's <strong>index</strong> if found, otherwise <strong>false</strong>.
   */
  public static function str_index_first(string $str, string $needle, int $offset = 0, string $encoding = 'UTF-8')
  {
    return self::strpos(
        $str,
        $needle,
        $offset,
        $encoding
    );
  }

  /**
   * Returns the index of the last occurrence of $needle in the string,
   * and false if not found. Accepts an optional offset from which to begin
   * the search. Offsets may be negative to count from the last character
   * in the string.
   *
   * @param string $str      <p>The input string.</p>
   * @param string $needle   <p>Substring to look for.</p>
   * @param int    $offset   [optional] <p>Offset from which to search. Default: 0</p>
   * @param string $encoding [optional] <p>Default: UTF-8</p>
   *
   * @return int|false
   *                   The last occurrence's <strong>index</strong> if found, otherwise <strong>false</strong>.
   */
  public static function str_index_last(string $str, string $needle, int $offset = 0, string $encoding = 'UTF-8')
  {
    return self::strrpos(
        $str,
        $needle,
        $offset,
        $encoding
    );
  }

  /**
   * Inserts $substring into the string at the $index provided.
   *
   * @param string $str       <p>The input string.</p>
   * @param string $substring <p>String to be inserted.</p>
   * @param int    $index     <p>The index at which to insert the substring.</p>
   * @param string $encoding  [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return string
   */
  public static function str_insert(string $str, string $substring, int $index, string $encoding = 'UTF-8'): string
  {
    $len = self::strlen($str, $encoding);

    if ($index > $len) {
      return $str;
    }

    $start = self::substr($str, 0, $index, $encoding);
    $end = self::substr($str, $index, $len, $encoding);

    return $start . $substring . $end;
  }

  /**
   * Case-insensitive and UTF-8 safe version of <function>str_replace</function>.
   *
   * @link  http://php.net/manual/en/function.str-ireplace.php
   *
   * @param mixed $search  <p>
   *                       Every replacement with search array is
   *                       performed on the result of previous replacement.
   *                       </p>
   * @param mixed $replace <p>
   *                       </p>
   * @param mixed $subject <p>
   *                       If subject is an array, then the search and
   *                       replace is performed with every entry of
   *                       subject, and the return value is an array as
   *                       well.
   *                       </p>
   * @param int   $count   [optional] <p>
   *                       The number of matched and replaced needles will
   *                       be returned in count which is passed by
   *                       reference.
   *                       </p>
   *
   * @return mixed A string or an array of replacements.
   */
  public static function str_ireplace($search, $replace, $subject, &$count = null)
  {
    $search = (array)$search;

    /** @noinspection AlterInForeachInspection */
    foreach ($search as &$s) {
      if ('' === $s .= '') {
        $s = '/^(?<=.)$/';
      } else {
        $s = '/' . \preg_quote($s, '/') . '/ui';
      }
    }

    $subject = \preg_replace($search, $replace, $subject, -1, $replace);
    $count = $replace; // used as reference parameter

    return $subject;
  }

  /**
   * Check if the string starts with the given substring, case insensitive.
   *
   * @param string $haystack <p>The string to search in.</p>
   * @param string $needle   <p>The substring to search for.</p>
   *
   * @return bool
   */
  public static function str_istarts_with(string $haystack, string $needle): bool
  {
    if ('' === $haystack || '' === $needle) {
      return false;
    }

    if (self::stripos($haystack, $needle) === 0) {
      return true;
    }

    return false;
  }

  /**
   * Returns true if the string begins with any of $substrings, false otherwise.
   *
   * - case-insensitive
   *
   * @param string $str        <p>The input string.</p>
   * @param array  $substrings <p>Substrings to look for.</p>
   *
   * @return bool Whether or not $str starts with $substring.
   */
  public static function str_istarts_with_any(string $str, array $substrings): bool
  {
    if ('' === $str) {
      return false;
    }

    if (empty($substrings)) {
      return false;
    }

    foreach ($substrings as $substring) {
      if (self::str_istarts_with($str, $substring)) {
        return true;
      }
    }

    return false;
  }

  /**
   * Gets the substring after the first occurrence of a separator.
   *
   * @param string $str       <p>The input string.</p>
   * @param string $separator <p>The string separator.</p>
   * @param string $encoding  [optional] <p>Default: UTF-8</p>
   *
   * @return string
   */
  public static function str_isubstr_after_first_separator(string $str, string $separator, string $encoding = 'UTF-8'): string
  {
    if (
        $separator === ''
        ||
        $str === ''
    ) {
      return '';
    }

    $offset = self::str_iindex_first($str, $separator);
    if ($offset === false) {
      return '';
    }

    return (string)self::substr(
        $str,
        $offset + self::strlen($separator, $encoding),
        null,
        $encoding
    );
  }

  /**
   * Gets the substring after the last occurrence of a separator.
   *
   * @param string $str       <p>The input string.</p>
   * @param string $separator <p>The string separator.</p>
   * @param string $encoding  [optional] <p>Default: UTF-8</p>
   *
   * @return string
   */
  public static function str_isubstr_after_last_separator(string $str, string $separator, string $encoding = 'UTF-8'): string
  {
    if (
        $separator === ''
        ||
        $str === ''
    ) {
      return '';
    }

    $offset = self::str_iindex_last($str, $separator);
    if ($offset === false) {
      return '';
    }

    return (string)self::substr(
        $str,
        $offset + self::strlen($separator, $encoding),
        null,
        $encoding
    );
  }

  /**
   * Gets the substring before the first occurrence of a separator.
   *
   * @param string $str       <p>The input string.</p>
   * @param string $separator <p>The string separator.</p>
   * @param string $encoding  [optional] <p>Default: UTF-8</p>
   *
   * @return string
   */
  public static function str_isubstr_before_first_separator(string $str, string $separator, string $encoding = 'UTF-8'): string
  {
    if (
        $separator === ''
        ||
        $str === ''
    ) {
      return '';
    }

    $offset = self::str_iindex_first($str, $separator);
    if ($offset === false) {
      return '';
    }

    return (string)self::substr($str, 0, $offset, $encoding);
  }

  /**
   * Gets the substring before the last occurrence of a separator.
   *
   * @param string $str       <p>The input string.</p>
   * @param string $separator <p>The string separator.</p>
   * @param string $encoding  [optional] <p>Default: UTF-8</p>
   *
   * @return string
   */
  public static function str_isubstr_before_last_separator(string $str, string $separator, string $encoding = 'UTF-8'): string
  {
    if (
        $separator === ''
        ||
        $str === ''
    ) {
      return '';
    }

    $offset = self::str_iindex_last($str, $separator);
    if ($offset === false) {
      return '';
    }

    return (string)self::substr($str, 0, $offset, $encoding);
  }

  /**
   * Gets the substring after (or before via "$beforeNeedle") the first occurrence of the "$needle".
   *
   * @param string $str          <p>The input string.</p>
   * @param string $needle       <p>The string to look for.</p>
   * @param bool   $beforeNeedle [optional] <p>Default: false</p>
   * @param string $encoding     [optional] <p>Default: UTF-8</p>
   *
   * @return string
   */
  public static function str_isubstr_first(string $str, string $needle, bool $beforeNeedle = false, string $encoding = 'UTF-8'): string
  {
    if (
        '' === $needle
        ||
        '' === $str
    ) {
      return '';
    }

    $part = self::stristr(
        $str,
        $needle,
        $beforeNeedle,
        $encoding
    );
    if (false === $part) {
      return '';
    }

    return $part;
  }

  /**
   * Gets the substring after (or before via "$beforeNeedle") the last occurrence of the "$needle".
   *
   * @param string $str          <p>The input string.</p>
   * @param string $needle       <p>The string to look for.</p>
   * @param bool   $beforeNeedle [optional] <p>Default: false</p>
   * @param string $encoding     [optional] <p>Default: UTF-8</p>
   *
   * @return string
   */
  public static function str_isubstr_last(string $str, string $needle, bool $beforeNeedle = false, string $encoding = 'UTF-8'): string
  {
    if (
        '' === $needle
        ||
        '' === $str
    ) {
      return '';
    }

    $part = self::strrichr($str, $needle, $beforeNeedle, $encoding);
    if (false === $part) {
      return '';
    }

    return $part;
  }

  /**
   * Returns the last $n characters of the string.
   *
   * @param string $str      <p>The input string.</p>
   * @param int    $n        <p>Number of characters to retrieve from the end.</p>
   * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return string
   */
  public static function str_last_char(string $str, int $n = 1, string $encoding = 'UTF-8'): string
  {
    if ($n <= 0) {
      return '';
    }

    $returnTmp = self::substr($str, -$n, null, $encoding);

    return ($returnTmp === false ? '' : $returnTmp);
  }

  /**
   * Limit the number of characters in a string.
   *
   * @param string $str      <p>The input string.</p>
   * @param int    $length   [optional] <p>Default: 100</p>
   * @param string $strAddOn [optional] <p>Default: …</p>
   * @param string $encoding [optional] <p>Default: UTF-8</p>
   *
   * @return string
   */
  public static function str_limit(string $str, int $length = 100, string $strAddOn = '…', string $encoding = 'UTF-8'): string
  {
    if ('' === $str) {
      return '';
    }

    if ($length <= 0) {
      return '';
    }

    if (self::strlen($str, $encoding) <= $length) {
      return $str;
    }

    return self::substr($str, 0, $length - self::strlen($strAddOn), $encoding) . $strAddOn;
  }

  /**
   * Limit the number of characters in a string, but also after the next word.
   *
   * @param string $str      <p>The input string.</p>
   * @param int    $length   [optional] <p>Default: 100</p>
   * @param string $strAddOn [optional] <p>Default: …</p>
   * @param string $encoding [optional] <p>Default: UTF-8</p>
   *
   * @return string
   */
  public static function str_limit_after_word(string $str, int $length = 100, string $strAddOn = '…', string $encoding = 'UTF-8'): string
  {
    if ('' === $str) {
      return '';
    }

    if ($length <= 0) {
      return '';
    }

    if (self::strlen($str, $encoding) <= $length) {
      return $str;
    }

    if (self::substr($str, $length - 1, 1, $encoding) === ' ') {
      return self::substr($str, 0, $length - 1, $encoding) . $strAddOn;
    }

    $str = (string)self::substr($str, 0, $length, $encoding);
    $array = \explode(' ', $str);
    \array_pop($array);
    $new_str = \implode(' ', $array);

    if ($new_str === '') {
      $str = self::substr($str, 0, $length - 1, $encoding) . $strAddOn;
    } else {
      $str = $new_str . $strAddOn;
    }

    return $str;
  }

  /**
   * Returns the longest common prefix between the string and $otherStr.
   *
   * @param string $str      <p>The input sting.</p>
   * @param string $otherStr <p>Second string for comparison.</p>
   * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return string
   */
  public static function str_longest_common_prefix(string $str, string $otherStr, string $encoding = 'UTF-8'): string
  {
    $maxLength = \min(self::strlen($str, $encoding), self::strlen($otherStr, $encoding));

    $longestCommonPrefix = '';
    for ($i = 0; $i < $maxLength; $i++) {
      $char = self::substr($str, $i, 1, $encoding);

      if ($char == self::substr($otherStr, $i, 1, $encoding)) {
        $longestCommonPrefix .= $char;
      } else {
        break;
      }
    }

    return $longestCommonPrefix;
  }

  /**
   * Returns the longest common substring between the string and $otherStr.
   * In the case of ties, it returns that which occurs first.
   *
   * @param string $str
   * @param string $otherStr <p>Second string for comparison.</p>
   * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return string String with its $str being the longest common substring.
   */
  public static function str_longest_common_substring(string $str, string $otherStr, string $encoding = 'UTF-8'): string
  {
    // Uses dynamic programming to solve
    // http://en.wikipedia.org/wiki/Longest_common_substring_problem
    $strLength = self::strlen($str, $encoding);
    $otherLength = self::strlen($otherStr, $encoding);

    // Return if either string is empty
    if ($strLength == 0 || $otherLength == 0) {
      return '';
    }

    $len = 0;
    $end = 0;
    $table = \array_fill(
        0,
        $strLength + 1,
        \array_fill(0, $otherLength + 1, 0)
    );

    for ($i = 1; $i <= $strLength; $i++) {
      for ($j = 1; $j <= $otherLength; $j++) {
        $strChar = self::substr($str, $i - 1, 1, $encoding);
        $otherChar = self::substr($otherStr, $j - 1, 1, $encoding);

        if ($strChar == $otherChar) {
          $table[$i][$j] = $table[$i - 1][$j - 1] + 1;
          if ($table[$i][$j] > $len) {
            $len = $table[$i][$j];
            $end = $i;
          }
        } else {
          $table[$i][$j] = 0;
        }
      }
    }

    $returnTmp = self::substr($str, $end - $len, $len, $encoding);

    return ($returnTmp === false ? '' : $returnTmp);
  }

  /**
   * Returns the longest common suffix between the string and $otherStr.
   *
   * @param string $str
   * @param string $otherStr <p>Second string for comparison.</p>
   * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return string
   */
  public static function str_longest_common_suffix(string $str, string $otherStr, string $encoding = 'UTF-8'): string
  {
    $maxLength = \min(self::strlen($str, $encoding), self::strlen($otherStr, $encoding));

    $longestCommonSuffix = '';
    for ($i = 1; $i <= $maxLength; $i++) {
      $char = self::substr($str, -$i, 1, $encoding);

      if ($char == self::substr($otherStr, -$i, 1, $encoding)) {
        $longestCommonSuffix = $char . $longestCommonSuffix;
      } else {
        break;
      }
    }

    return $longestCommonSuffix;
  }

  /**
   * Returns true if $str matches the supplied pattern, false otherwise.
   *
   * @param string $str     <p>The input string.</p>
   * @param string $pattern <p>Regex pattern to match against.</p>
   *
   * @return bool Whether or not $str matches the pattern.
   */
  public static function str_matches_pattern(string $str, string $pattern): bool
  {
    if (\preg_match('/' . $pattern . '/u', $str)) {
      return true;
    }

    return false;
  }

  /**
   * Returns whether or not a character exists at an index. Offsets may be
   * negative to count from the last character in the string. Implements
   * part of the ArrayAccess interface.
   *
   * @param string $str      <p>The input string.</p>
   * @param int    $offset   <p>The index to check.</p>
   * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   *
   * @return bool Whether or not the index exists.
   */
  public static function str_offset_exists(string $str, int $offset, string $encoding = 'UTF-8'): bool
  {
    // init
    $length = self::strlen($str, $encoding);

    if ($offset >= 0) {
      return ($length > $offset);
    }

    return ($length >= \abs($offset));
  }

  /**
   * Returns the character at the given index. Offsets may be negative to
   * count from the last character in the string. Implements part of the
   * ArrayAccess interface, and throws an OutOfBoundsException if the index
   * does not exist.
   *
   * @param string $str      <p>The input string.</p>
   * @param int    $index    <p>The <strong>index</strong> from which to retrieve the char.</p>
   * @param string $encoding [optional] <p>Default: UTF-8</p>
   *
   * @return string The character at the specified index.
   *
   * @throws \OutOfBoundsException If the positive or negative offset does not exist.
   */
  public static function str_offset_get(string $str, int $index, string $encoding = 'UTF-8'): string
  {
    // init
    $length = self::strlen($str);

    if (
        ($index >= 0 && $length <= $index)
        ||
        $length < \abs($index)
    ) {
      throw new \OutOfBoundsException('No character exists at the index');
    }

    return self::char_at($str, $index, $encoding);
  }

  /**
   * Pad a UTF-8 string to given length with another string.
   *
   * @param string $str        <p>The input string.</p>
   * @param int    $pad_length <p>The length of return string.</p>
   * @param string $pad_string [optional] <p>String to use for padding the input string.</p>
   * @param int    $pad_type   [optional] <p>
   *                           Can be <strong>STR_PAD_RIGHT</strong> (default),
   *                           <strong>STR_PAD_LEFT</strong> or <strong>STR_PAD_BOTH</strong>
   *                           </p>
   * @param string $encoding   [optional] <p>Default: UTF-8</p>
   *
   * @return string Returns the padded string.
   */
  public static function str_pad(string $str, int $pad_length, string $pad_string = ' ', $pad_type = STR_PAD_RIGHT, string $encoding = 'UTF-8'): string
  {
    if ('' === $str) {
      return '';
    }

    if ($pad_type !== (int)$pad_type) {
      if ($pad_type == 'left') {
        $pad_type = STR_PAD_LEFT;
      } elseif ($pad_type == 'right') {
        $pad_type = STR_PAD_RIGHT;
      } elseif ($pad_type == 'both') {
        $pad_type = STR_PAD_BOTH;
      } else {
        throw new \InvalidArgumentException(
            'Pad expects $padType to be "STR_PAD_*" or ' . "to be one of 'left', 'right' or 'both'"
        );
      }
    }

    $str_length = self::strlen($str, $encoding);

    if (
        $pad_length > 0
        &&
        $pad_length >= $str_length
    ) {
      $ps_length = self::strlen($pad_string, $encoding);

      $diff = ($pad_length - $str_length);

      switch ($pad_type) {
        case STR_PAD_LEFT:
          $pre = \str_repeat($pad_string, (int)\ceil($diff / $ps_length));
          $pre = (string)self::substr($pre, 0, $diff, $encoding);
          $post = '';
          break;

        case STR_PAD_BOTH:
          $pre = \str_repeat($pad_string, (int)\ceil($diff / $ps_length / 2));
          $pre = (string)self::substr($pre, 0, (int)\floor($diff / 2), $encoding);
          $post = \str_repeat($pad_string, (int)\ceil($diff / $ps_length / 2));
          $post = (string)self::substr($post, 0, (int)\ceil($diff / 2), $encoding);
          break;

        case STR_PAD_RIGHT:
        default:
          $post = \str_repeat($pad_string, (int)\ceil($diff / $ps_length));
          $post = (string)self::substr($post, 0, $diff, $encoding);
          $pre = '';
      }

      return $pre . $str . $post;
    }

    return $str;
  }

  /**
   * Returns a new string of a given length such that both sides of the
   * string are padded. Alias for pad() with a $padType of 'both'.
   *
   * @param string $str
   * @param int    $length   <p>Desired string length after padding.</p>
   * @param string $padStr   [optional] <p>String used to pad, defaults to space. Default: ' '</p>
   * @param string $encoding [optional] <p>Default: UTF-8</p>
   *
   * @return string String with padding applied.
   */
  public static function str_pad_both(string $str, int $length, string $padStr = ' ', string $encoding = 'UTF-8'): string
  {
    $padding = $length - self::strlen($str, $encoding);

    return self::apply_padding($str, (int)\floor($padding / 2), (int)\ceil($padding / 2), $padStr, $encoding);
  }

  /**
   * Returns a new string of a given length such that the beginning of the
   * string is padded. Alias for pad() with a $padType of 'left'.
   *
   * @param string $str
   * @param int    $length   <p>Desired string length after padding.</p>
   * @param string $padStr   [optional] <p>String used to pad, defaults to space. Default: ' '</p>
   * @param string $encoding [optional] <p>Default: UTF-8</p>
   *
   * @return string String with left padding.
   */
  public static function str_pad_left(string $str, int $length, string $padStr = ' ', string $encoding = 'UTF-8'): string
  {
    return self::apply_padding($str, $length - self::strlen($str), 0, $padStr, $encoding);
  }

  /**
   * Returns a new string of a given length such that the end of the string
   * is padded. Alias for pad() with a $padType of 'right'.
   *
   * @param string $str
   * @param int    $length   <p>Desired string length after padding.</p>
   * @param string $padStr   [optional] <p>String used to pad, defaults to space. Default: ' '</p>
   * @param string $encoding [optional] <p>Default: UTF-8</p>
   *
   * @return string String with right padding.
   */
  public static function str_pad_right(string $str, int $length, string $padStr = ' ', string $encoding = 'UTF-8'): string
  {
    return self::apply_padding($str, 0, $length - self::strlen($str), $padStr, $encoding);
  }

  /**
   * Repeat a string.
   *
   * @param string $str        <p>
   *                           The string to be repeated.
   *                           </p>
   * @param int    $multiplier <p>
   *                           Number of time the input string should be
   *                           repeated.
   *                           </p>
   *                           <p>
   *                           multiplier has to be greater than or equal to 0.
   *                           If the multiplier is set to 0, the function
   *                           will return an empty string.
   *                           </p>
   *
   * @return string The repeated string.
   */
  public static function str_repeat(string $str, int $multiplier): string
  {
    $str = self::filter($str);

    return \str_repeat($str, $multiplier);
  }

  /**
   * INFO: This is only a wrapper for "str_replace()"  -> the original functions is already UTF-8 safe.
   *
   * Replace all occurrences of the search string with the replacement string
   *
   * @link http://php.net/manual/en/function.str-replace.php
   *
   * @param mixed $search  <p>
   *                       The value being searched for, otherwise known as the needle.
   *                       An array may be used to designate multiple needles.
   *                       </p>
   * @param mixed $replace <p>
   *                       The replacement value that replaces found search
   *                       values. An array may be used to designate multiple replacements.
   *                       </p>
   * @param mixed $subject <p>
   *                       The string or array being searched and replaced on,
   *                       otherwise known as the haystack.
   *                       </p>
   *                       <p>
   *                       If subject is an array, then the search and
   *                       replace is performed with every entry of
   *                       subject, and the return value is an array as
   *                       well.
   *                       </p>
   * @param int   $count   [optional] If passed, this will hold the number of matched and replaced needles.
   *
   * @return mixed This function returns a string or an array with the replaced values.
   */
  public static function str_replace($search, $replace, $subject, int &$count = null)
  {
    return \str_replace($search, $replace, $subject, $count);
  }

  /**
   * Replaces all occurrences of $search from the beginning of string with $replacement.
   *
   * @param string $str         <p>The input string.</p>
   * @param string $search      <p>The string to search for.</p>
   * @param string $replacement <p>The replacement.</p>
   *
   * @return string String after the replacements.
   */
  public static function str_replace_beginning(string $str, string $search, string $replacement): string
  {
    return self::regex_replace(
        $str,
        '^' . \preg_quote($search, '/'),
        self::str_replace('\\', '\\\\', $replacement)
    );
  }

  /**
   * Replaces all occurrences of $search from the ending of string with $replacement.
   *
   * @param string $str         <p>The input string.</p>
   * @param string $search      <p>The string to search for.</p>
   * @param string $replacement <p>The replacement.</p>
   *
   * @return string String after the replacements.
   */
  public static function str_replace_ending(string $str, string $search, string $replacement): string
  {
    return self::regex_replace(
        $str,
        \preg_quote($search, '/') . '$',
        self::str_replace('\\', '\\\\', $replacement)
    );
  }

  /**
   * Replace the first "$search"-term with the "$replace"-term.
   *
   * @param string $search
   * @param string $replace
   * @param string $subject
   *
   * @return string
   */
  public static function str_replace_first(string $search, string $replace, string $subject): string
  {
    $pos = self::strpos($subject, $search);
    if ($pos !== false) {
      return self::substr_replace($subject, $replace, $pos, self::strlen($search));
    }

    return $subject;
  }

  /**
   * Replace the last "$search"-term with the "$replace"-term.
   *
   * @param string $search
   * @param string $replace
   * @param string $subject
   *
   * @return string
   */
  public static function str_replace_last(string $search, string $replace, string $subject): string
  {
    $pos = self::strrpos($subject, $search);
    if ($pos !== false) {
      return self::substr_replace($subject, $replace, $pos, self::strlen($search));
    }

    return $subject;
  }

  /**
   * Shuffles all the characters in the string.
   *
   * PS: uses random algorithm which is weak for cryptography purposes
   *
   * @param string $str <p>The input string</p>
   *
   * @return string The shuffled string.
   */
  public static function str_shuffle(string $str): string
  {
    $indexes = \range(0, self::strlen($str) - 1);
    /** @noinspection NonSecureShuffleUsageInspection */
    \shuffle($indexes);

    $shuffledStr = '';
    foreach ($indexes as $i) {
      $shuffledStr .= self::substr($str, $i, 1);
    }

    return $shuffledStr;
  }

  /**
   * Returns the substring beginning at $start, and up to, but not including
   * the index specified by $end. If $end is omitted, the function extracts
   * the remaining string. If $end is negative, it is computed from the end
   * of the string.
   *
   * @param string $str
   * @param int    $start    <p>Initial index from which to begin extraction.</p>
   * @param int    $end      [optional] <p>Index at which to end extraction. Default: null</p>
   * @param string $encoding [optional] <p>Default: UTF-8</p>
   *
   * @return string|false
   *                     <p>The extracted substring.</p><p>If <i>str</i> is shorter than <i>start</i>
   *                     characters long, <b>FALSE</b> will be returned.
   */
  public static function str_slice(string $str, int $start, int $end = null, string $encoding = 'UTF-8')
  {
    if ($end === null) {
      $length = self::strlen($str);
    } elseif ($end >= 0 && $end <= $start) {
      return '';
    } elseif ($end < 0) {
      $length = self::strlen($str) + $end - $start;
    } else {
      $length = $end - $start;
    }

    return self::substr($str, $start, $length, $encoding);
  }

  /**
   * Convert a string to e.g.: "snake_case"
   *
   * @param string $str
   * @param string $encoding [optional] <p>Default: UTF-8</p>
   *
   * @return string String in snake_case.
   */
  public static function str_snakeize(string $str, string $encoding = 'UTF-8'): string
  {
    $str = self::normalize_whitespace($str);
    $str = \str_replace('-', '_', $str);

    $str = (string)\preg_replace_callback(
        '/([\d|A-Z])/u',
        function ($matches) use ($encoding) {
          $match = $matches[1];
          $matchInt = (int)$match;

          if ((string)$matchInt == $match) {
            return '_' . $match . '_';
          }

          return '_' . UTF8::strtolower($match, $encoding);
        },
        $str
    );

    $str = (string)\preg_replace(
        [
            '/\s+/',        // convert spaces to "_"
            '/^\s+|\s+$/',  // trim leading & trailing spaces
            '/_+/',         // remove double "_"
        ],
        [
            '_',
            '',
            '_',
        ],
        $str
    );

    $str = self::trim($str, '_'); // trim leading & trailing "_"
    $str = self::trim($str); // trim leading & trailing whitespace

    return $str;
  }

  /**
   * Sort all characters according to code points.
   *
   * @param string $str    <p>A UTF-8 string.</p>
   * @param bool   $unique <p>Sort unique. If <strong>true</strong>, repeated characters are ignored.</p>
   * @param bool   $desc   <p>If <strong>true</strong>, will sort characters in reverse code point order.</p>
   *
   * @return string String of sorted characters.
   */
  public static function str_sort(string $str, bool $unique = false, bool $desc = false): string
  {
    $array = self::codepoints($str);

    if ($unique) {
      $array = \array_flip(\array_flip($array));
    }

    if ($desc) {
      \arsort($array);
    } else {
      \asort($array);
    }

    return self::string($array);
  }

  /**
   * alias for "UTF8::split()"
   *
   * @see UTF8::split()
   *
   * @param string|string[] $str
   * @param int             $len
   *
   * @return string[]
   */
  public static function str_split($str, int $len = 1): array
  {
    return self::split($str, $len);
  }

  /**
   * Splits the string with the provided regular expression, returning an
   * array of Stringy objects. An optional integer $limit will truncate the
   * results.
   *
   * @param string $str
   * @param string $pattern <p>The regex with which to split the string.</p>
   * @param int    $limit   [optional] <p>Maximum number of results to return. Default: -1 === no limit</p>
   *
   * @return string[] An array of strings.
   */
  public static function str_split_pattern(string $str, string $pattern, int $limit = -1): array
  {
    if ($limit === 0) {
      return [];
    }

    // this->split errors when supplied an empty pattern in < PHP 5.4.13
    // and current versions of HHVM (3.8 and below)
    if ($pattern === '') {
      return [$str];
    }

    // this->split returns the remaining unsplit string in the last index when
    // supplying a limit
    if ($limit > 0) {
      ++$limit;
    } else {
      $limit = -1;
    }

    $array = \preg_split('/' . \preg_quote($pattern, '/') . '/u', $str, $limit);

    if ($array === false) {
      return [];
    }

    if ($limit > 0 && \count($array) === $limit) {
      \array_pop($array);
    }

    return $array;
  }

  /**
   * Check if the string starts with the given substring.
   *
   * @param string $haystack <p>The string to search in.</p>
   * @param string $needle   <p>The substring to search for.</p>
   *
   * @return bool
   */
  public static function str_starts_with(string $haystack, string $needle): bool
  {
    if ('' === $haystack || '' === $needle) {
      return false;
    }

    if (\strpos($haystack, $needle) === 0) {
      return true;
    }

    return false;
  }

  /**
   * Returns true if the string begins with any of $substrings, false otherwise.
   *
   * - case-sensitive
   *
   * @param string $str        <p>The input string.</p>
   * @param array  $substrings <p>Substrings to look for.</p>
   *
   * @return bool Whether or not $str starts with $substring.
   */
  public static function str_starts_with_any(string $str, array $substrings): bool
  {
    if ('' === $str) {
      return false;
    }

    if (empty($substrings)) {
      return false;
    }

    foreach ($substrings as $substring) {
      if (self::str_starts_with($str, $substring)) {
        return true;
      }
    }

    return false;
  }

  /**
   * Gets the substring after the first occurrence of a separator.
   *
   * @param string $str       <p>The input string.</p>
   * @param string $separator <p>The string separator.</p>
   * @param string $encoding  [optional] <p>Default: UTF-8</p>
   *
   * @return string
   */
  public static function str_substr_after_first_separator(string $str, string $separator, string $encoding = 'UTF-8'): string
  {
    if (
        $separator === ''
        ||
        $str === ''
    ) {
      return '';
    }

    $offset = self::str_index_first($str, $separator);
    if ($offset === false) {
      return '';
    }

    return (string)self::substr(
        $str,
        $offset + self::strlen($separator, $encoding),
        null,
        $encoding
    );
  }

  /**
   * Gets the substring after the last occurrence of a separator.
   *
   * @param string $str       <p>The input string.</p>
   * @param string $separator <p>The string separator.</p>
   * @param string $encoding  [optional] <p>Default: UTF-8</p>
   *
   * @return string
   */
  public static function str_substr_after_last_separator(string $str, string $separator, string $encoding = 'UTF-8'): string
  {
    if (
        $separator === ''
        ||
        $str === ''
    ) {
      return '';
    }

    $offset = self::str_index_last($str, $separator);
    if ($offset === false) {
      return '';
    }

    return (string)self::substr(
        $str,
        $offset + self::strlen($separator, $encoding),
        null,
        $encoding
    );
  }

  /**
   * Gets the substring before the first occurrence of a separator.
   *
   * @param string $str       <p>The input string.</p>
   * @param string $separator <p>The string separator.</p>
   * @param string $encoding  [optional] <p>Default: UTF-8</p>
   *
   * @return string
   */
  public static function str_substr_before_first_separator(string $str, string $separator, string $encoding = 'UTF-8'): string
  {
    if (
        $separator === ''
        ||
        $str === ''
    ) {
      return '';
    }

    $offset = self::str_index_first($str, $separator);
    if ($offset === false) {
      return '';
    }

    return (string)self::substr(
        $str,
        0,
        $offset,
        $encoding
    );
  }

  /**
   * Gets the substring before the last occurrence of a separator.
   *
   * @param string $str       <p>The input string.</p>
   * @param string $separator <p>The string separator.</p>
   * @param string $encoding  [optional] <p>Default: UTF-8</p>
   *
   * @return string
   */
  public static function str_substr_before_last_separator(string $str, string $separator, string $encoding = 'UTF-8'): string
  {
    if (
        $separator === ''
        ||
        $str === ''
    ) {
      return '';
    }

    $offset = self::str_index_last($str, $separator);
    if ($offset === false) {
      return '';
    }

    return (string)self::substr(
        $str,
        0,
        $offset,
        $encoding
    );
  }

  /**
   * Gets the substring after (or before via "$beforeNeedle") the first occurrence of the "$needle".
   *
   * @param string $str          <p>The input string.</p>
   * @param string $needle       <p>The string to look for.</p>
   * @param bool   $beforeNeedle [optional] <p>Default: false</p>
   * @param string $encoding     [optional] <p>Default: UTF-8</p>
   *
   * @return string
   */
  public static function str_substr_first(string $str, string $needle, bool $beforeNeedle = false, string $encoding = 'UTF-8'): string
  {
    if (
        '' === $str
        ||
        '' === $needle
    ) {
      return '';
    }

    $part = self::strstr(
        $str,
        $needle,
        $beforeNeedle,
        $encoding
    );
    if (false === $part) {
      return '';
    }

    return $part;
  }

  /**
   * Gets the substring after (or before via "$beforeNeedle") the last occurrence of the "$needle".
   *
   * @param string $str          <p>The input string.</p>
   * @param string $needle       <p>The string to look for.</p>
   * @param bool   $beforeNeedle [optional] <p>Default: false</p>
   * @param string $encoding     [optional] <p>Default: UTF-8</p>
   *
   * @return string
   */
  public static function str_substr_last(string $str, string $needle, bool $beforeNeedle = false, string $encoding = 'UTF-8'): string
  {
    if (
        '' === $str
        ||
        '' === $needle
    ) {
      return '';
    }

    $part = self::strrchr($str, $needle, $beforeNeedle, $encoding);
    if (false === $part) {
      return '';
    }

    return $part;
  }

  /**
   * Surrounds $str with the given substring.
   *
   * @param string $str
   * @param string $substring <p>The substring to add to both sides.</P>
   *
   * @return string String with the substring both prepended and appended.
   */
  public static function str_surround(string $str, string $substring): string
  {
    return \implode('', [$substring, $str, $substring]);
  }

  /**
   * Returns a trimmed string with the first letter of each word capitalized.
   * Also accepts an array, $ignore, allowing you to list words not to be
   * capitalized.
   *
   * @param string              $str
   * @param string[]|array|null $ignore                [optional] <p>An array of words not to capitalize or null.
   *                                                   Default: null</p>
   * @param string              $encoding              [optional] <p>Default: UTF-8</p>
   * @param bool                $cleanUtf8             [optional] <p>Remove non UTF-8 chars from the string.</p>
   * @param string|null         $lang                  [optional] <p>Set the language for special cases: az, el, lt,
   *                                                   tr</p>
   * @param bool                $tryToKeepStringLength [optional] <p>true === try to keep the string length: e.g. ẞ ->
   *                                                   ß</p>
   * @param bool                $useTrimFirst          [optional] <p>true === trim the input string, first</p>
   *
   * @return string The titleized string.
   */
  public static function str_titleize(
      string $str,
      array $ignore = null,
      string $encoding = 'UTF-8',
      bool $cleanUtf8 = false,
      string $lang = null,
      bool $tryToKeepStringLength = false,
      bool $useTrimFirst = true
  ): string
  {
    if ($useTrimFirst === true) {
      $str = self::trim($str);
    }

    $str_array = self::str_to_words($str);

    foreach ($str_array as &$str_tmp) {

      if ($ignore && \in_array($str_tmp, $ignore, true)) {
        continue;
      }

      $str_tmp = self::str_upper_first(
          self::strtolower(
              $str_tmp,
              $encoding,
              $cleanUtf8,
              $lang,
              $tryToKeepStringLength
          ),
          $encoding,
          $cleanUtf8,
          $lang,
          $tryToKeepStringLength
      );
    }

    return \implode('', $str_array);
  }

  /**
   * Returns a trimmed string in proper title case.
   *
   * Also accepts an array, $ignore, allowing you to list words not to be
   * capitalized.
   *
   * Adapted from John Gruber's script.
   *
   * @see https://gist.github.com/gruber/9f9e8650d68b13ce4d78
   *
   * @param string $str
   * @param array  $ignore   <p>An array of words not to capitalize.</p>
   * @param string $encoding [optional] <p>Default: UTF-8</p>
   *
   * @return string The titleized string.
   */
  public static function str_titleize_for_humans(string $str, array $ignore = [], string $encoding = 'UTF-8'): string
  {
    $smallWords = \array_merge(
        [
            '(?<!q&)a',
            'an',
            'and',
            'as',
            'at(?!&t)',
            'but',
            'by',
            'en',
            'for',
            'if',
            'in',
            'of',
            'on',
            'or',
            'the',
            'to',
            'v[.]?',
            'via',
            'vs[.]?',
        ],
        $ignore
    );

    $smallWordsRx = \implode('|', $smallWords);
    $apostropheRx = '(?x: [\'’] [[:lower:]]* )?';

    $str = self::trim($str);

    if (self::has_lowercase($str) === false) {
      $str = self::strtolower($str);
    }

    // The main substitutions
    $str = (string)\preg_replace_callback(
        '~\b (_*) (?:                                                              # 1. Leading underscore and
                        ( (?<=[ ][/\\\\]) [[:alpha:]]+ [-_[:alpha:]/\\\\]+ |              # 2. file path or 
                          [-_[:alpha:]]+ [@.:] [-_[:alpha:]@.:/]+ ' . $apostropheRx . ' ) #    URL, domain, or email
                        |
                        ( (?i: ' . $smallWordsRx . ' ) ' . $apostropheRx . ' )            # 3. or small word (case-insensitive)
                        |
                        ( [[:alpha:]] [[:lower:]\'’()\[\]{}]* ' . $apostropheRx . ' )     # 4. or word w/o internal caps
                        |
                        ( [[:alpha:]] [[:alpha:]\'’()\[\]{}]* ' . $apostropheRx . ' )     # 5. or some other word
                      ) (_*) \b                                                           # 6. With trailing underscore
                    ~ux',
        function ($matches) use ($encoding) {
          // Preserve leading underscore
          $str = $matches[1];
          if ($matches[2]) {
            // Preserve URLs, domains, emails and file paths
            $str .= $matches[2];
          } elseif ($matches[3]) {
            // Lower-case small words
            $str .= self::strtolower($matches[3], $encoding);
          } elseif ($matches[4]) {
            // Capitalize word w/o internal caps
            $str .= static::str_upper_first($matches[4], $encoding);
          } else {
            // Preserve other kinds of word (iPhone)
            $str .= $matches[5];
          }
          // Preserve trailing underscore
          $str .= $matches[6];

          return $str;
        },
        $str
    );

    // Exceptions for small words: capitalize at start of title...
    $str = (string)\preg_replace_callback(
        '~(  \A [[:punct:]]*                # start of title...
                      |  [:.;?!][ ]+               # or of subsentence...
                      |  [ ][\'"“‘(\[][ ]* )       # or of inserted subphrase...
                      ( ' . $smallWordsRx . ' ) \b # ...followed by small word
                     ~uxi',
        function ($matches) use ($encoding) {
          return $matches[1] . static::str_upper_first($matches[2], $encoding);
        },
        $str
    );

    // ...and end of title
    $str = (string)\preg_replace_callback(
        '~\b ( ' . $smallWordsRx . ' ) # small word...
                      (?= [[:punct:]]* \Z     # ...at the end of the title...
                      |   [\'"’”)\]] [ ] )    # ...or of an inserted subphrase?
                     ~uxi',
        function ($matches) use ($encoding) {
          return static::str_upper_first($matches[1], $encoding);
        },
        $str
    );

    // Exceptions for small words in hyphenated compound words
    // e.g. "in-flight" -> In-Flight
    $str = (string)\preg_replace_callback(
        '~\b
                        (?<! -)                   # Negative lookbehind for a hyphen; we do not want to match man-in-the-middle but do want (in-flight)
                        ( ' . $smallWordsRx . ' )
                        (?= -[[:alpha:]]+)        # lookahead for "-someword"
                       ~uxi',
        function ($matches) use ($encoding) {
          return static::str_upper_first($matches[1], $encoding);
        },
        $str
    );

    // e.g. "Stand-in" -> "Stand-In" (Stand is already capped at this point)
    $str = (string)\preg_replace_callback(
        '~\b
                      (?<!…)                    # Negative lookbehind for a hyphen; we do not want to match man-in-the-middle but do want (stand-in)
                      ( [[:alpha:]]+- )         # $1 = first word and hyphen, should already be properly capped
                      ( ' . $smallWordsRx . ' ) # ...followed by small word
                      (?!	- )                   # Negative lookahead for another -
                     ~uxi',
        function ($matches) use ($encoding) {
          return $matches[1] . static::str_upper_first($matches[2], $encoding);
        },
        $str
    );

    return $str;
  }

  /**
   * Get a binary representation of a specific string.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return string
   */
  public static function str_to_binary(string $str): string
  {
    $value = \unpack('H*', $str);

    return \base_convert($value[1], 16, 2);
  }

  /**
   * @param string   $str
   * @param bool     $removeEmptyValues <p>Remove empty values.</p>
   * @param null|int $removeShortValues <p>The min. string length or null to disable</p>
   *
   * @return string[]
   */
  public static function str_to_lines(string $str, bool $removeEmptyValues = false, int $removeShortValues = null): array
  {
    if ('' === $str) {
      return ($removeEmptyValues === true ? [] : ['']);
    }

    $return = \preg_split("/[\r\n]{1,2}/u", $str);

    if ($return === false) {
      return ($removeEmptyValues === true ? [] : ['']);
    }

    if (
        $removeShortValues === null
        &&
        $removeEmptyValues === false
    ) {
      return $return;
    }

    $tmpReturn = self::reduce_string_array(
        $return,
        $removeEmptyValues,
        $removeShortValues
    );

    return $tmpReturn;
  }

  /**
   * Convert a string into an array of words.
   *
   * @param string   $str
   * @param string   $charList          <p>Additional chars for the definition of "words".</p>
   * @param bool     $removeEmptyValues <p>Remove empty values.</p>
   * @param null|int $removeShortValues <p>The min. string length or null to disable</p>
   *
   * @return string[]
   */
  public static function str_to_words(string $str, string $charList = '', bool $removeEmptyValues = false, int $removeShortValues = null): array
  {
    if ('' === $str) {
      return ($removeEmptyValues === true ? [] : ['']);
    }

    $charList = self::rxClass($charList, '\pL');

    $return = \preg_split("/({$charList}+(?:[\p{Pd}’']{$charList}+)*)/u", $str, -1, PREG_SPLIT_DELIM_CAPTURE);

    if ($return === false) {
      return ($removeEmptyValues === true ? [] : ['']);
    }

    if (
        $removeShortValues === null
        &&
        $removeEmptyValues === false
    ) {
      return $return;
    }

    $tmpReturn = self::reduce_string_array(
        $return,
        $removeEmptyValues,
        $removeShortValues
    );

    foreach ($tmpReturn as &$item) {
      $item = (string)$item;
    }

    return $tmpReturn;
  }

  /**
   * alias for "UTF8::to_ascii()"
   *
   * @see UTF8::to_ascii()
   *
   * @param string $str
   * @param string $unknown
   * @param bool   $strict
   *
   * @return string
   */
  public static function str_transliterate(string $str, string $unknown = '?', bool $strict = false): string
  {
    return self::to_ascii($str, $unknown, $strict);
  }

  /**
   * Truncates the string to a given length. If $substring is provided, and
   * truncating occurs, the string is further truncated so that the substring
   * may be appended without exceeding the desired length.
   *
   * @param string $str
   * @param int    $length    <p>Desired length of the truncated string.</p>
   * @param string $substring [optional] <p>The substring to append if it can fit. Default: ''</p>
   * @param string $encoding  [optional] <p>Default: UTF-8</p>
   *
   * @return string String after truncating.
   */
  public static function str_truncate($str, int $length, string $substring = '', string $encoding = 'UTF-8'): string
  {
    // init
    $str = (string)$str;

    if ('' === $str) {
      return '';
    }

    if ($length >= self::strlen($str, $encoding)) {
      return $str;
    }

    // Need to further trim the string so we can append the substring
    $substringLength = self::strlen($substring, $encoding);
    $length -= $substringLength;

    $truncated = self::substr($str, 0, $length, $encoding);

    return $truncated . $substring;
  }

  /**
   * Truncates the string to a given length, while ensuring that it does not
   * split words. If $substring is provided, and truncating occurs, the
   * string is further truncated so that the substring may be appended without
   * exceeding the desired length.
   *
   * @param string $str
   * @param int    $length    <p>Desired length of the truncated string.</p>
   * @param string $substring [optional] <p>The substring to append if it can fit. Default: ''</p>
   * @param string $encoding  [optional] <p>Default: UTF-8</p>
   *
   * @return string String after truncating.
   */
  public static function str_truncate_safe(string $str, int $length, string $substring = '', string $encoding = 'UTF-8'): string
  {
    if ($length >= self::strlen($str, $encoding)) {
      return $str;
    }

    // need to further trim the string so we can append the substring
    $substringLength = self::strlen($substring, $encoding);
    $length -= $substringLength;

    $truncated = self::substr($str, 0, $length, $encoding);
    if ($truncated === false) {
      return '';
    }

    // if the last word was truncated
    $strPosSpace = self::strpos($str, ' ', $length - 1, $encoding);
    if ($strPosSpace != $length) {
      // find pos of the last occurrence of a space, get up to that
      $lastPos = self::strrpos($truncated, ' ', 0, $encoding);

      if ($lastPos !== false || $strPosSpace !== false) {
        $truncated = self::substr($truncated, 0, (int)$lastPos, $encoding);
      }
    }

    $str = $truncated . $substring;

    return $str;
  }

  /**
   * Returns a lowercase and trimmed string separated by underscores.
   * Underscores are inserted before uppercase characters (with the exception
   * of the first character of the string), and in place of spaces as well as
   * dashes.
   *
   * @param string $str
   *
   * @return string The underscored string.
   */
  public static function str_underscored(string $str): string
  {
    return self::str_delimit($str, '_');
  }

  /**
   * Returns an UpperCamelCase version of the supplied string. It trims
   * surrounding spaces, capitalizes letters following digits, spaces, dashes
   * and underscores, and removes spaces, dashes, underscores.
   *
   * @param string      $str                   <p>The input string.</p>
   * @param string      $encoding              [optional] <p>Default: UTF-8</p>
   * @param bool        $cleanUtf8             [optional] <p>Remove non UTF-8 chars from the string.</p>
   * @param string|null $lang                  [optional] <p>Set the language for special cases: az, el, lt, tr</p>
   * @param bool        $tryToKeepStringLength [optional] <p>true === try to keep the string length: e.g. ẞ -> ß</p>
   *
   * @return string String in UpperCamelCase.
   */
  public static function str_upper_camelize(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false, string $lang = null, bool $tryToKeepStringLength = false): string
  {
    return self::str_upper_first(self::str_camelize($str, $encoding), $encoding, $cleanUtf8, $lang, $tryToKeepStringLength);
  }

  /**
   * alias for "UTF8::ucfirst()"
   *
   * @see UTF8::ucfirst()
   *
   * @param string      $str
   * @param string      $encoding
   * @param bool        $cleanUtf8
   * @param string|null $lang
   * @param bool        $tryToKeepStringLength
   *
   *
   * @return string
   */
  public static function str_upper_first(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false, string $lang = null, bool $tryToKeepStringLength = false): string
  {
    return self::ucfirst($str, $encoding, $cleanUtf8, $lang, $tryToKeepStringLength);
  }

  /**
   * Counts number of words in the UTF-8 string.
   *
   * @param string $str      <p>The input string.</p>
   * @param int    $format   [optional] <p>
   *                         <strong>0</strong> => return a number of words (default)<br>
   *                         <strong>1</strong> => return an array of words<br>
   *                         <strong>2</strong> => return an array of words with word-offset as key
   *                         </p>
   * @param string $charlist [optional] <p>Additional chars that contains to words and do not start a new word.</p>
   *
   * @return string[]|int The number of words in the string
   */
  public static function str_word_count(string $str, int $format = 0, string $charlist = '')
  {
    $strParts = self::str_to_words($str, $charlist);

    $len = \count($strParts);

    if ($format === 1) {

      $numberOfWords = [];
      for ($i = 1; $i < $len; $i += 2) {
        $numberOfWords[] = $strParts[$i];
      }

    } elseif ($format === 2) {

      $numberOfWords = [];
      $offset = self::strlen($strParts[0]);
      for ($i = 1; $i < $len; $i += 2) {
        $numberOfWords[$offset] = $strParts[$i];
        $offset += self::strlen($strParts[$i]) + self::strlen($strParts[$i + 1]);
      }

    } else {

      $numberOfWords = (int)(($len - 1) / 2);

    }

    return $numberOfWords;
  }

  /**
   * Case-insensitive string comparison.
   *
   * INFO: Case-insensitive version of UTF8::strcmp()
   *
   * @param string $str1     <p>The first string.</p>
   * @param string $str2     <p>The second string.</p>
   * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return int
   *             <strong>&lt; 0</strong> if str1 is less than str2;<br>
   *             <strong>&gt; 0</strong> if str1 is greater than str2,<br>
   *             <strong>0</strong> if they are equal.
   */
  public static function strcasecmp(string $str1, string $str2, string $encoding = 'UTF-8'): int
  {
    return self::strcmp(
        self::strtocasefold($str1, true, false, $encoding, null, false),
        self::strtocasefold($str2, true, false, $encoding, null, false)
    );
  }

  /**
   * alias for "UTF8::strstr()"
   *
   * @see UTF8::strstr()
   *
   * @param string $haystack
   * @param string $needle
   * @param bool   $before_needle
   * @param string $encoding
   * @param bool   $cleanUtf8
   *
   * @return string|false
   */
  public static function strchr(string $haystack, string $needle, bool $before_needle = false, string $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    return self::strstr($haystack, $needle, $before_needle, $encoding, $cleanUtf8);
  }

  /**
   * Case-sensitive string comparison.
   *
   * @param string $str1 <p>The first string.</p>
   * @param string $str2 <p>The second string.</p>
   *
   * @return int
   *              <strong>&lt; 0</strong> if str1 is less than str2<br>
   *              <strong>&gt; 0</strong> if str1 is greater than str2<br>
   *              <strong>0</strong> if they are equal.
   */
  public static function strcmp(string $str1, string $str2): int
  {
    /** @noinspection PhpUndefinedClassInspection */
    return $str1 . '' === $str2 . '' ? 0 : \strcmp(
        \Normalizer::normalize($str1, \Normalizer::NFD),
        \Normalizer::normalize($str2, \Normalizer::NFD)
    );
  }

  /**
   * Find length of initial segment not matching mask.
   *
   * @param string $str
   * @param string $charList
   * @param int    $offset
   * @param int    $length
   *
   * @return int|null
   */
  public static function strcspn(string $str, string $charList, int $offset = 0, int $length = null)
  {
    if ('' === $charList .= '') {
      return null;
    }

    if ($offset || $length !== null) {
      $strTmp = self::substr($str, $offset, $length);
      if ($strTmp === false) {
        return null;
      }
      $str = (string)$strTmp;
    }

    if ('' === $str) {
      return null;
    }

    if (\preg_match('/^(.*?)' . self::rxClass($charList) . '/us', $str, $length)) {
      return self::strlen($length[1]);
    }

    return self::strlen($str);
  }

  /**
   * alias for "UTF8::stristr()"
   *
   * @see UTF8::stristr()
   *
   * @param string $haystack
   * @param string $needle
   * @param bool   $before_needle
   * @param string $encoding
   * @param bool   $cleanUtf8
   *
   * @return string|false
   */
  public static function strichr(string $haystack, string $needle, bool $before_needle = false, string $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    return self::stristr($haystack, $needle, $before_needle, $encoding, $cleanUtf8);
  }

  /**
   * Create a UTF-8 string from code points.
   *
   * INFO: opposite to UTF8::codepoints()
   *
   * @param array $array <p>Integer or Hexadecimal codepoints.</p>
   *
   * @return string UTF-8 encoded string.
   */
  public static function string(array $array): string
  {
    return \implode(
        '',
        \array_map(
            [
                self::class,
                'chr',
            ],
            $array
        )
    );
  }

  /**
   * Checks if string starts with "BOM" (Byte Order Mark Character) character.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return bool
   *              <strong>true</strong> if the string has BOM at the start,<br>
   *              <strong>false</strong> otherwise.
   */
  public static function string_has_bom(string $str): bool
  {
    foreach (self::$BOM as $bomString => $bomByteLength) {
      if (0 === \strpos($str, $bomString)) {
        return true;
      }
    }

    return false;
  }

  /**
   * Strip HTML and PHP tags from a string + clean invalid UTF-8.
   *
   * @link http://php.net/manual/en/function.strip-tags.php
   *
   * @param string $str             <p>
   *                                The input string.
   *                                </p>
   * @param string $allowable_tags  [optional] <p>
   *                                You can use the optional second parameter to specify tags which should
   *                                not be stripped.
   *                                </p>
   *                                <p>
   *                                HTML comments and PHP tags are also stripped. This is hardcoded and
   *                                can not be changed with allowable_tags.
   *                                </p>
   * @param bool   $cleanUtf8       [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return string The stripped string.
   */
  public static function strip_tags(string $str, string $allowable_tags = null, bool $cleanUtf8 = false): string
  {
    if ('' === $str) {
      return '';
    }

    if ($cleanUtf8 === true) {
      $str = self::clean($str);
    }

    return \strip_tags($str, $allowable_tags);
  }

  /**
   * Strip all whitespace characters. This includes tabs and newline
   * characters, as well as multibyte whitespace such as the thin space
   * and ideographic space.
   *
   * @param string $str
   *
   * @return string
   */
  public static function strip_whitespace(string $str): string
  {
    if ('' === $str) {
      return '';
    }

    return (string)\preg_replace('/[[:space:]]+/u', '', $str);
  }

  /**
   * Finds position of first occurrence of a string within another, case insensitive.
   *
   * @link http://php.net/manual/en/function.mb-stripos.php
   *
   * @param string $haystack  <p>The string from which to get the position of the first occurrence of needle.</p>
   * @param string $needle    <p>The string to find in haystack.</p>
   * @param int    $offset    [optional] <p>The position in haystack to start searching.</p>
   * @param string $encoding  [optional] <p>Set the charset for e.g. "mb_" function</p>
   * @param bool   $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return int|false
   *                   Return the <strong>(int)</strong> numeric position of the first occurrence of needle in the
   *                   haystack string,<br> or <strong>false</strong> if needle is not found.
   */
  public static function stripos(string $haystack, string $needle, int $offset = 0, $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    if ('' === $haystack || '' === $needle) {
      return false;
    }

    if ($cleanUtf8 === true) {
      // "mb_strpos()" and "iconv_strpos()" returns wrong position,
      // if invalid characters are found in $haystack before $needle
      $haystack = self::clean($haystack);
      $needle = self::clean($needle);
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['mbstring'] === true) {
      $returnTmp = \mb_stripos($haystack, $needle, $offset, $encoding);
      if ($returnTmp !== false) {
        return $returnTmp;
      }
    }

    if (
        $encoding === 'UTF-8' // INFO: "grapheme_stripos()" can't handle other encodings
        &&
        $offset >= 0 // grapheme_stripos() can't handle negative offset
        &&
        self::$SUPPORT['intl'] === true
    ) {
      $returnTmp = \grapheme_stripos($haystack, $needle, $offset);
      if ($returnTmp !== false) {
        return $returnTmp;
      }
    }

    //
    // fallback for ascii only
    //

    if (self::is_ascii($haystack) && self::is_ascii($needle)) {
      return \stripos($haystack, $needle, $offset);
    }

    //
    // fallback via vanilla php
    //

    $haystack = self::strtocasefold($haystack, true, false, $encoding, null, false);
    $needle = self::strtocasefold($needle, true, false, $encoding, null, false);

    return self::strpos($haystack, $needle, $offset, $encoding);
  }

  /**
   * Returns all of haystack starting from and including the first occurrence of needle to the end.
   *
   * @param string $haystack       <p>The input string. Must be valid UTF-8.</p>
   * @param string $needle         <p>The string to look for. Must be valid UTF-8.</p>
   * @param bool   $before_needle  [optional] <p>
   *                               If <b>TRUE</b>, it returns the part of the
   *                               haystack before the first occurrence of the needle (excluding the needle).
   *                               </p>
   * @param string $encoding       [optional] <p>Set the charset for e.g. "mb_" function</p>
   * @param bool   $cleanUtf8      [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return false|string A sub-string,<br>or <strong>false</strong> if needle is not found.
   */
  public static function stristr(string $haystack, string $needle, bool $before_needle = false, string $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    if ('' === $haystack || '' === $needle) {
      return false;
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if ($cleanUtf8 === true) {
      // "mb_strpos()" and "iconv_strpos()" returns wrong position,
      // if invalid characters are found in $haystack before $needle
      $needle = self::clean($needle);
      $haystack = self::clean($haystack);
    }

    if (!$needle) {
      return $haystack;
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (
        $encoding !== 'UTF-8'
        &&
        self::$SUPPORT['mbstring'] === false
    ) {
      \trigger_error('UTF8::stristr() without mbstring cannot handle "' . $encoding . '" encoding', E_USER_WARNING);
    }

    if (self::$SUPPORT['mbstring'] === true) {
      return \mb_stristr($haystack, $needle, $before_needle, $encoding);
    }

    if (
        $encoding === 'UTF-8' // INFO: "grapheme_stristr()" can't handle other encodings
        &&
        self::$SUPPORT['intl'] === true
    ) {
      $returnTmp = \grapheme_stristr($haystack, $needle, $before_needle);
      if ($returnTmp !== false) {
        return $returnTmp;
      }
    }

    if (self::is_ascii($needle) && self::is_ascii($haystack)) {
      return \stristr($haystack, $needle, $before_needle);
    }

    \preg_match('/^(.*?)' . \preg_quote($needle, '/') . '/usi', $haystack, $match);

    if (!isset($match[1])) {
      return false;
    }

    if ($before_needle) {
      return $match[1];
    }

    return self::substr($haystack, self::strlen($match[1]));
  }

  /**
   * Get the string length, not the byte-length!
   *
   * @link     http://php.net/manual/en/function.mb-strlen.php
   *
   * @param string $str       <p>The string being checked for length.</p>
   * @param string $encoding  [optional] <p>Set the charset for e.g. "mb_" function</p>
   * @param bool   $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return int|false
   *             The number <strong>(int)</strong> of characters in the string $str having character encoding $encoding.
   *             (One multi-byte character counted as +1).
   *             <br>
   *             Can return <strong>false</strong>, if e.g. mbstring is not installed and we process invalid chars.
   */
  public static function strlen(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    if ('' === $str) {
      return 0;
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    //
    // fallback for binary || ascii only
    //

    if (
        $encoding === 'CP850'
        ||
        $encoding === 'ASCII'
    ) {
      return self::strlen_in_byte($str);
    }

    if ($cleanUtf8 === true) {
      // "mb_strlen" and "\iconv_strlen" returns wrong length,
      // if invalid characters are found in $str
      $str = self::clean($str);
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (
        $encoding !== 'UTF-8'
        &&
        self::$SUPPORT['mbstring'] === false
        &&
        self::$SUPPORT['iconv'] === false
    ) {
      \trigger_error('UTF8::strlen() without mbstring / iconv cannot handle "' . $encoding . '" encoding', E_USER_WARNING);
    }

    //
    // fallback via mbstring
    //

    if (self::$SUPPORT['mbstring'] === true) {
      $returnTmp = \mb_strlen($str, $encoding);
      if ($returnTmp !== false) {
        return $returnTmp;
      }
    }

    //
    // fallback via iconv
    //

    if (self::$SUPPORT['iconv'] === true) {
      $returnTmp = \iconv_strlen($str, $encoding);
      if ($returnTmp !== false) {
        return $returnTmp;
      }
    }

    //
    // fallback via intl
    //

    if (
        $encoding === 'UTF-8' // INFO: "grapheme_strlen()" can't handle other encodings
        &&
        self::$SUPPORT['intl'] === true
    ) {
      $returnTmp = \grapheme_strlen($str);
      if ($returnTmp !== null) {
        return $returnTmp;
      }
    }

    //
    // fallback for ascii only
    //

    if (self::is_ascii($str)) {
      return \strlen($str);
    }

    //
    // fallback via vanilla php
    //

    \preg_match_all('/./us', $str, $parts);

    $returnTmp = \count($parts[0]);
    if ($returnTmp === 0 && isset($str[0])) {
      return false;
    }

    return $returnTmp;
  }

  /**
   * Get string length in byte.
   *
   * @param string $str
   *
   * @return int
   */
  public static function strlen_in_byte(string $str): int
  {
    if ($str === '') {
      return 0;
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['mbstring_func_overload'] === true) {
      // "mb_" is available if overload is used, so use it ...
      return \mb_strlen($str, 'CP850'); // 8-BIT
    }

    return \strlen($str);
  }

  /**
   * Case insensitive string comparisons using a "natural order" algorithm.
   *
   * INFO: natural order version of UTF8::strcasecmp()
   *
   * @param string $str1     <p>The first string.</p>
   * @param string $str2     <p>The second string.</p>
   * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return int
   *             <strong>&lt; 0</strong> if str1 is less than str2<br>
   *             <strong>&gt; 0</strong> if str1 is greater than str2<br>
   *             <strong>0</strong> if they are equal
   */
  public static function strnatcasecmp(string $str1, string $str2, string $encoding = 'UTF-8'): int
  {
    return self::strnatcmp(
        self::strtocasefold($str1, true, false, $encoding, null, false),
        self::strtocasefold($str2, true, false, $encoding, null, false)
    );
  }

  /**
   * String comparisons using a "natural order" algorithm
   *
   * INFO: natural order version of UTF8::strcmp()
   *
   * @link  http://php.net/manual/en/function.strnatcmp.php
   *
   * @param string $str1 <p>The first string.</p>
   * @param string $str2 <p>The second string.</p>
   *
   * @return int
   *             <strong>&lt; 0</strong> if str1 is less than str2;<br>
   *             <strong>&gt; 0</strong> if str1 is greater than str2;<br>
   *             <strong>0</strong> if they are equal
   */
  public static function strnatcmp(string $str1, string $str2): int
  {
    return $str1 . '' === $str2 . '' ? 0 : \strnatcmp(self::strtonatfold($str1), self::strtonatfold($str2));
  }

  /**
   * Case-insensitive string comparison of the first n characters.
   *
   * @link  http://php.net/manual/en/function.strncasecmp.php
   *
   * @param string $str1     <p>The first string.</p>
   * @param string $str2     <p>The second string.</p>
   * @param int    $len      <p>The length of strings to be used in the comparison.</p>
   * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return int
   *             <strong>&lt; 0</strong> if <i>str1</i> is less than <i>str2</i>;<br>
   *             <strong>&gt; 0</strong> if <i>str1</i> is greater than <i>str2</i>;<br>
   *             <strong>0</strong> if they are equal
   */
  public static function strncasecmp(string $str1, string $str2, int $len, string $encoding = 'UTF-8'): int
  {
    return self::strncmp(
        self::strtocasefold($str1, true, false, $encoding, null, false),
        self::strtocasefold($str2, true, false, $encoding, null, false),
        $len
    );
  }

  /**
   * String comparison of the first n characters.
   *
   * @link  http://php.net/manual/en/function.strncmp.php
   *
   * @param string $str1 <p>The first string.</p>
   * @param string $str2 <p>The second string.</p>
   * @param int    $len  <p>Number of characters to use in the comparison.</p>
   *
   * @return int
   *             <strong>&lt; 0</strong> if <i>str1</i> is less than <i>str2</i>;<br>
   *             <strong>&gt; 0</strong> if <i>str1</i> is greater than <i>str2</i>;<br>
   *             <strong>0</strong> if they are equal
   */
  public static function strncmp(string $str1, string $str2, int $len): int
  {
    $str1 = (string)self::substr($str1, 0, $len);
    $str2 = (string)self::substr($str2, 0, $len);

    return self::strcmp($str1, $str2);
  }

  /**
   * Search a string for any of a set of characters.
   *
   * @link  http://php.net/manual/en/function.strpbrk.php
   *
   * @param string $haystack  <p>The string where char_list is looked for.</p>
   * @param string $char_list <p>This parameter is case sensitive.</p>
   *
   * @return string|false String starting from the character found, or false if it is not found.
   */
  public static function strpbrk(string $haystack, string $char_list)
  {
    if ('' === $haystack || '' === $char_list) {
      return false;
    }

    if (\preg_match('/' . self::rxClass($char_list) . '/us', $haystack, $m)) {
      return \substr($haystack, (int)\strpos($haystack, $m[0]));
    }

    return false;
  }

  /**
   * Find position of first occurrence of string in a string.
   *
   * @link http://php.net/manual/en/function.mb-strpos.php
   *
   * @param string     $haystack  <p>The string from which to get the position of the first occurrence of needle.</p>
   * @param string|int $needle    <p>The string to find in haystack.<br>Or a code point as int.</p>
   * @param int        $offset    [optional] <p>The search offset. If it is not specified, 0 is used.</p>
   * @param string     $encoding  [optional] <p>Set the charset for e.g. "mb_" function</p>
   * @param bool       $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return int|false
   *                   The <strong>(int)</strong> numeric position of the first occurrence of needle in the haystack
   *                   string.<br> If needle is not found it returns false.
   */
  public static function strpos(string $haystack, $needle, int $offset = 0, $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    if ('' === $haystack) {
      return false;
    }

    // iconv and mbstring do not support integer $needle
    if ((int)$needle === $needle && $needle >= 0) {
      $needle = (string)self::chr($needle);
    }
    $needle = (string)$needle;

    if ('' === $needle) {
      return false;
    }

    if ($cleanUtf8 === true) {
      // "mb_strpos()" and "iconv_strpos()" returns wrong position,
      // if invalid characters are found in $haystack before $needle
      $needle = self::clean($needle);
      $haystack = self::clean($haystack);
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    //
    // fallback for binary || ascii only
    //

    if (
        $encoding === 'CP850'
        ||
        $encoding === 'ASCII'
    ) {
      return self::strpos_in_byte($haystack, $needle, $offset);
    }

    if (
        $encoding !== 'UTF-8'
        &&
        self::$SUPPORT['iconv'] === false
        &&
        self::$SUPPORT['mbstring'] === false
    ) {
      \trigger_error('UTF8::strpos() without mbstring / iconv cannot handle "' . $encoding . '" encoding', E_USER_WARNING);
    }

    //
    // fallback via mbstring
    //

    if (self::$SUPPORT['mbstring'] === true) {
      $returnTmp = \mb_strpos($haystack, $needle, $offset, $encoding);
      if ($returnTmp !== false) {
        return $returnTmp;
      }
    }

    //
    // fallback via intl
    //

    if (
        $encoding === 'UTF-8' // INFO: "grapheme_strpos()" can't handle other encodings
        &&
        $offset >= 0 // grapheme_strpos() can't handle negative offset
        &&
        self::$SUPPORT['intl'] === true
    ) {
      $returnTmp = \grapheme_strpos($haystack, $needle, $offset);
      if ($returnTmp !== false) {
        return $returnTmp;
      }
    }

    //
    // fallback via iconv
    //

    if (
        $offset >= 0 // iconv_strpos() can't handle negative offset
        &&
        self::$SUPPORT['iconv'] === true
    ) {
      // ignore invalid negative offset to keep compatibility
      // with php < 5.5.35, < 5.6.21, < 7.0.6
      $returnTmp = \iconv_strpos($haystack, $needle, $offset > 0 ? $offset : 0, $encoding);
      if ($returnTmp !== false) {
        return $returnTmp;
      }
    }

    //
    // fallback for ascii only
    //

    if (($haystackIsAscii = self::is_ascii($haystack)) && self::is_ascii($needle)) {
      return \strpos($haystack, $needle, $offset);
    }

    //
    // fallback via vanilla php
    //

    if ($haystackIsAscii) {
      $haystackTmp = \substr($haystack, $offset);
    } else {
      $haystackTmp = self::substr($haystack, $offset, null, $encoding);
    }
    if ($haystackTmp === false) {
      $haystackTmp = '';
    }
    $haystack = (string)$haystackTmp;

    if ($offset < 0) {
      $offset = 0;
    }

    $pos = \strpos($haystack, $needle);
    if ($pos === false) {
      return false;
    }

    if ($pos) {
      return ($offset + (self::strlen(substr($haystack, 0, $pos), $encoding)));
    }

    return ($offset + 0);
  }

  /**
   * Find position of first occurrence of string in a string.
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
   * @return int|false The numeric position of the first occurrence of needle in the
   *                   haystack string. If needle is not found, it returns false.
   */
  public static function strpos_in_byte(string $haystack, string $needle, int $offset = 0)
  {
    if ($haystack === '' || $needle === '') {
      return false;
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['mbstring_func_overload'] === true) {
      // "mb_" is available if overload is used, so use it ...
      return \mb_strpos($haystack, $needle, $offset, 'CP850'); // 8-BIT
    }

    return \strpos($haystack, $needle, $offset);
  }

  /**
   * Finds the last occurrence of a character in a string within another.
   *
   * @link http://php.net/manual/en/function.mb-strrchr.php
   *
   * @param string $haystack      <p>The string from which to get the last occurrence of needle.</p>
   * @param string $needle        <p>The string to find in haystack</p>
   * @param bool   $before_needle [optional] <p>
   *                              Determines which portion of haystack
   *                              this function returns.
   *                              If set to true, it returns all of haystack
   *                              from the beginning to the last occurrence of needle.
   *                              If set to false, it returns all of haystack
   *                              from the last occurrence of needle to the end,
   *                              </p>
   * @param string $encoding      [optional] <p>Set the charset for e.g. "mb_" function</p>
   * @param bool   $cleanUtf8     [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return string|false The portion of haystack or false if needle is not found.
   */
  public static function strrchr(string $haystack, string $needle, bool $before_needle = false, string $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    if ('' === $haystack || '' === $needle) {
      return false;
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if ($cleanUtf8 === true) {
      // "mb_strpos()" and "iconv_strpos()" returns wrong position,
      // if invalid characters are found in $haystack before $needle
      $needle = self::clean($needle);
      $haystack = self::clean($haystack);
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (
        $encoding !== 'UTF-8'
        &&
        self::$SUPPORT['mbstring'] === false
    ) {
      \trigger_error('UTF8::strrchr() without mbstring cannot handle "' . $encoding . '" encoding', E_USER_WARNING);
    }

    if (self::$SUPPORT['mbstring'] === true) {
      return \mb_strrchr($haystack, $needle, $before_needle, $encoding);
    }

    //
    // fallback for binary || ascii only
    //

    if (
        $before_needle === false
        &&
        (
            $encoding === 'CP850'
            ||
            $encoding === 'ASCII'
        )
    ) {
      return \strrchr($haystack, $needle);
    }

    //
    // fallback via iconv
    //

    if (self::$SUPPORT['iconv'] === true) {
      $needleTmp = self::substr($needle, 0, 1, $encoding);
      if ($needleTmp === false) {
        return false;
      }
      $needle = (string)$needleTmp;

      $pos = \iconv_strrpos($haystack, $needle, $encoding);
      if (false === $pos) {
        return false;
      }

      if ($before_needle) {
        return self::substr($haystack, 0, $pos, $encoding);
      }

      return self::substr($haystack, $pos, null, $encoding);
    }

    //
    // fallback via vanilla php
    //

    $needleTmp = self::substr($needle, 0, 1, $encoding);
    if ($needleTmp === false) {
      return false;
    }
    $needle = (string)$needleTmp;

    $pos = self::strrpos($haystack, $needle, null, $encoding);
    if ($pos === false) {
      return false;
    }

    if ($before_needle) {
      return self::substr($haystack, 0, $pos, $encoding);
    }

    return self::substr($haystack, $pos, null, $encoding);
  }

  /**
   * Reverses characters order in the string.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return string The string with characters in the reverse sequence.
   */
  public static function strrev(string $str): string
  {
    if ('' === $str) {
      return '';
    }

    $reversed = '';
    $i = self::strlen($str);
    while ($i--) {
      $reversed .= self::substr($str, $i, 1);
    }

    return $reversed;
  }

  /**
   * Finds the last occurrence of a character in a string within another, case insensitive.
   *
   * @link http://php.net/manual/en/function.mb-strrichr.php
   *
   * @param string $haystack       <p>The string from which to get the last occurrence of needle.</p>
   * @param string $needle         <p>The string to find in haystack.</p>
   * @param bool   $before_needle  [optional] <p>
   *                               Determines which portion of haystack
   *                               this function returns.
   *                               If set to true, it returns all of haystack
   *                               from the beginning to the last occurrence of needle.
   *                               If set to false, it returns all of haystack
   *                               from the last occurrence of needle to the end,
   *                               </p>
   * @param string $encoding       [optional] <p>Set the charset for e.g. "mb_" function</p>
   * @param bool   $cleanUtf8      [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return string|false The portion of haystack or<br>false if needle is not found.
   */
  public static function strrichr(string $haystack, string $needle, bool $before_needle = false, string $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    if ('' === $haystack || '' === $needle) {
      return false;
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if ($cleanUtf8 === true) {
      // "mb_strpos()" and "iconv_strpos()" returns wrong position,
      // if invalid characters are found in $haystack before $needle
      $needle = self::clean($needle);
      $haystack = self::clean($haystack);
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    //
    // fallback via mbstring
    //

    if (self::$SUPPORT['mbstring'] === true) {
      return \mb_strrichr($haystack, $needle, $before_needle, $encoding);
    }

    //
    // fallback via vanilla php
    //

    $needleTmp = self::substr($needle, 0, 1, $encoding);
    if ($needleTmp === false) {
      return false;
    }
    $needle = (string)$needleTmp;

    $pos = self::strripos($haystack, $needle, 0, $encoding);
    if ($pos === false) {
      return false;
    }

    if ($before_needle) {
      return self::substr($haystack, 0, $pos, $encoding);
    }

    return self::substr($haystack, $pos, null, $encoding);
  }

  /**
   * Find position of last occurrence of a case-insensitive string.
   *
   * @param string     $haystack  <p>The string to look in.</p>
   * @param string|int $needle    <p>The string to look for.</p>
   * @param int        $offset    [optional] <p>Number of characters to ignore in the beginning or end.</p>
   * @param string     $encoding  [optional] <p>Set the charset for e.g. "mb_" function</p>
   * @param bool       $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return int|false
   *                   The <strong>(int)</strong> numeric position of the last occurrence of needle in the haystack
   *                   string.<br>If needle is not found, it returns false.
   */
  public static function strripos(string $haystack, $needle, int $offset = 0, string $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    if ('' === $haystack) {
      return false;
    }

    // iconv and mbstring do not support integer $needle
    if ((int)$needle === $needle && $needle >= 0) {
      $needle = (string)self::chr($needle);
    }
    $needle = (string)$needle;

    if ('' === $needle) {
      return false;
    }

    if ($cleanUtf8 === true) {
      // mb_strripos() && iconv_strripos() is not tolerant to invalid characters
      $needle = self::clean($needle);
      $haystack = self::clean($haystack);
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    //
    // fallback for binary || ascii only
    //

    if (
        $encoding === 'CP850'
        ||
        $encoding === 'ASCII'
    ) {
      return self::strripos_in_byte($haystack, $needle, $offset);
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (
        $encoding !== 'UTF-8'
        &&
        self::$SUPPORT['mbstring'] === false
    ) {
      \trigger_error('UTF8::strripos() without mbstring cannot handle "' . $encoding . '" encoding', E_USER_WARNING);
    }

    //
    // fallback via mbstrig
    //

    if (self::$SUPPORT['mbstring'] === true) {
      return \mb_strripos($haystack, $needle, $offset, $encoding);
    }

    //
    // fallback via intl
    //

    if (
        $encoding === 'UTF-8' // INFO: "grapheme_strripos()" can't handle other encodings
        &&
        $offset >= 0 // grapheme_strripos() can't handle negative offset
        &&
        self::$SUPPORT['intl'] === true
    ) {
      $returnTmp = \grapheme_strripos($haystack, $needle, $offset);
      if ($returnTmp !== false) {
        return $returnTmp;
      }
    }

    //
    // fallback for ascii only
    //

    if (self::is_ascii($haystack) && self::is_ascii($needle)) {
      return self::strripos_in_byte($haystack, $needle, $offset);
    }

    //
    // fallback via vanilla php
    //

    $haystack = self::strtocasefold($haystack, true, false, $encoding);
    $needle = self::strtocasefold($needle, true, false, $encoding);

    return self::strrpos($haystack, $needle, $offset, $encoding, $cleanUtf8);
  }

  /**
   * Finds position of last occurrence of a string within another, case insensitive.
   *
   * @param string $haystack <p>
   *                         The string from which to get the position of the last occurrence
   *                         of needle.
   *                         </p>
   * @param string $needle   <p>
   *                         The string to find in haystack.
   *                         </p>
   * @param int    $offset   [optional] <p>
   *                         The position in haystack
   *                         to start searching.
   *                         </p>
   *
   * @return int|false Return the numeric position of the last occurrence of needle in the
   *                   haystack string, or false if needle is not found.
   */
  public static function strripos_in_byte(string $haystack, string $needle, int $offset = 0)
  {
    if ($haystack === '' || $needle === '') {
      return false;
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['mbstring_func_overload'] === true) {
      // "mb_" is available if overload is used, so use it ...
      return \mb_strripos($haystack, $needle, $offset, 'CP850'); // 8-BIT
    }

    return \strripos($haystack, $needle, $offset);
  }

  /**
   * Find position of last occurrence of a string in a string.
   *
   * @link http://php.net/manual/en/function.mb-strrpos.php
   *
   * @param string     $haystack  <p>The string being checked, for the last occurrence of needle</p>
   * @param string|int $needle    <p>The string to find in haystack.<br>Or a code point as int.</p>
   * @param int        $offset    [optional] <p>May be specified to begin searching an arbitrary number of characters
   *                              into the string. Negative values will stop searching at an arbitrary point prior to
   *                              the end of the string.
   *                              </p>
   * @param string     $encoding  [optional] <p>Set the charset.</p>
   * @param bool       $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return int|false
   *                   The <strong>(int)</strong> numeric position of the last occurrence of needle in the haystack
   *                   string.<br>If needle is not found, it returns false.
   */
  public static function strrpos(string $haystack, $needle, int $offset = null, string $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    if ('' === $haystack) {
      return false;
    }

    // iconv and mbstring do not support integer $needle
    if ((int)$needle === $needle && $needle >= 0) {
      $needle = (string)self::chr($needle);
    }
    $needle = (string)$needle;

    if ('' === $needle) {
      return false;
    }

    if ($cleanUtf8 === true) {
      // \mb_strrpos && iconv_strrpos is not tolerant to invalid characters
      $needle = self::clean($needle);
      $haystack = self::clean($haystack);
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    //
    // fallback for binary || ascii only
    //

    if (
        $encoding === 'CP850'
        ||
        $encoding === 'ASCII'
    ) {
      return self::strrpos_in_byte($haystack, $needle, $offset);
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (
        $encoding !== 'UTF-8'
        &&
        self::$SUPPORT['mbstring'] === false
    ) {
      \trigger_error('UTF8::strrpos() without mbstring cannot handle "' . $encoding . '" encoding', E_USER_WARNING);
    }

    //
    // fallback via mbstring
    //

    if (self::$SUPPORT['mbstring'] === true) {
      return \mb_strrpos($haystack, $needle, $offset, $encoding);
    }

    //
    // fallback via intl
    //

    if (
        $offset !== null
        &&
        $offset >= 0 // grapheme_strrpos() can't handle negative offset
        &&
        $encoding === 'UTF-8' // INFO: "grapheme_strrpos()" can't handle other encodings
        &&
        self::$SUPPORT['intl'] === true
    ) {
      $returnTmp = \grapheme_strrpos($haystack, $needle, $offset);
      if ($returnTmp !== false) {
        return $returnTmp;
      }
    }

    //
    // fallback for ascii only
    //

    if (
        $offset !== null
        &&
        self::is_ascii($haystack)
        &&
        self::is_ascii($needle)
    ) {
      return self::strrpos_in_byte($haystack, $needle, $offset);
    }

    //
    // fallback via vanilla php
    //

    $haystackTmp = null;
    if ($offset > 0) {
      $haystackTmp = self::substr($haystack, $offset);
    } elseif ($offset < 0) {
      $haystackTmp = self::substr($haystack, 0, $offset);
      $offset = 0;
    }

    if ($haystackTmp !== null) {
      if ($haystackTmp === false) {
        $haystackTmp = '';
      }
      $haystack = (string)$haystackTmp;
    }

    $pos = self::strrpos_in_byte($haystack, $needle);
    if ($pos === false) {
      return false;
    }

    return $offset + self::strlen(self::substr_in_byte($haystack, 0, $pos));
  }

  /**
   * Find position of last occurrence of a string in a string.
   *
   * @param string $haystack <p>
   *                         The string being checked, for the last occurrence
   *                         of needle.
   *                         </p>
   * @param string $needle   <p>
   *                         The string to find in haystack.
   *                         </p>
   * @param int    $offset   [optional] May be specified to begin searching an arbitrary number of characters into
   *                         the string. Negative values will stop searching at an arbitrary point
   *                         prior to the end of the string.
   *
   * @return int|false The numeric position of the last occurrence of needle in the
   *                   haystack string. If needle is not found, it returns false.
   */
  public static function strrpos_in_byte(string $haystack, string $needle, int $offset = 0)
  {
    if ($haystack === '' || $needle === '') {
      return false;
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['mbstring_func_overload'] === true) {
      // "mb_" is available if overload is used, so use it ...
      return \mb_strrpos($haystack, $needle, $offset, 'CP850'); // 8-BIT
    }

    return \strrpos($haystack, $needle, $offset);
  }

  /**
   * Finds the length of the initial segment of a string consisting entirely of characters contained within a given
   * mask.
   *
   * @param string $str    <p>The input string.</p>
   * @param string $mask   <p>The mask of chars</p>
   * @param int    $offset [optional]
   * @param int    $length [optional]
   *
   * @return int
   */
  public static function strspn(string $str, string $mask, int $offset = 0, int $length = null): int
  {
    if ($offset || $length !== null) {
      $strTmp = self::substr($str, $offset, $length);
      if ($strTmp === false) {
        $strTmp = '';
      }
      $str = (string)$strTmp;
    }

    if ('' === $str || '' === $mask) {
      return 0;
    }

    return \preg_match('/^' . self::rxClass($mask) . '+/u', $str, $str) ? self::strlen($str[0]) : 0;
  }

  /**
   * Returns part of haystack string from the first occurrence of needle to the end of haystack.
   *
   * @param string $haystack       <p>The input string. Must be valid UTF-8.</p>
   * @param string $needle         <p>The string to look for. Must be valid UTF-8.</p>
   * @param bool   $before_needle  [optional] <p>
   *                               If <b>TRUE</b>, strstr() returns the part of the
   *                               haystack before the first occurrence of the needle (excluding the needle).
   *                               </p>
   * @param string $encoding       [optional] <p>Set the charset for e.g. "mb_" function</p>
   * @param bool   $cleanUtf8      [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return string|false
   *                       A sub-string,<br>or <strong>false</strong> if needle is not found.
   */
  public static function strstr(string $haystack, string $needle, bool $before_needle = false, string $encoding = 'UTF-8', $cleanUtf8 = false)
  {
    if ('' === $haystack || '' === $needle) {
      return false;
    }

    if ($cleanUtf8 === true) {
      // "mb_strpos()" and "iconv_strpos()" returns wrong position,
      // if invalid characters are found in $haystack before $needle
      $needle = self::clean($needle);
      $haystack = self::clean($haystack);
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    //
    // fallback for binary || ascii only
    //

    if (
        $encoding === 'CP850'
        ||
        $encoding === 'ASCII'
    ) {
      return self::strstr_in_byte($haystack, $needle, $before_needle);
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (
        $encoding !== 'UTF-8'
        &&
        self::$SUPPORT['mbstring'] === false
    ) {
      \trigger_error('UTF8::strstr() without mbstring cannot handle "' . $encoding . '" encoding', E_USER_WARNING);
    }

    //
    // fallback via mbstring
    //

    if (self::$SUPPORT['mbstring'] === true) {
      return \mb_strstr($haystack, $needle, $before_needle, $encoding);
    }

    //
    // fallback via intl
    //

    if (
        $encoding === 'UTF-8' // INFO: "grapheme_strstr()" can't handle other encodings
        &&
        self::$SUPPORT['intl'] === true
    ) {
      $returnTmp = \grapheme_strstr($haystack, $needle, $before_needle);
      if ($returnTmp !== false) {
        return $returnTmp;
      }
    }

    //
    // fallback for ascii only
    //

    if (self::is_ascii($haystack) && self::is_ascii($needle)) {
      return self::strstr_in_byte($haystack, $needle, $before_needle);
    }

    //
    // fallback via vanilla php
    //

    \preg_match('/^(.*?)' . \preg_quote($needle, '/') . '/us', $haystack, $match);

    if (!isset($match[1])) {
      return false;
    }

    if ($before_needle) {
      return $match[1];
    }

    return self::substr($haystack, self::strlen($match[1]));
  }

  /**
   *  * Finds first occurrence of a string within another.
   *
   * @param string $haystack      <p>
   *                              The string from which to get the first occurrence
   *                              of needle.
   *                              </p>
   * @param string $needle        <p>
   *                              The string to find in haystack.
   *                              </p>
   * @param bool   $before_needle [optional] <p>
   *                              Determines which portion of haystack
   *                              this function returns.
   *                              If set to true, it returns all of haystack
   *                              from the beginning to the first occurrence of needle.
   *                              If set to false, it returns all of haystack
   *                              from the first occurrence of needle to the end,
   *                              </p>
   *
   * @return string|false The portion of haystack,
   *                      or false if needle is not found.
   */
  public static function strstr_in_byte(string $haystack, string $needle, bool $before_needle = false)
  {
    if ($haystack === '' || $needle === '') {
      return false;
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['mbstring_func_overload'] === true) {
      // "mb_" is available if overload is used, so use it ...
      return \mb_strstr($haystack, $needle, $before_needle, 'CP850'); // 8-BIT
    }

    return \strstr($haystack, $needle, $before_needle);
  }

  /**
   * Unicode transformation for case-less matching.
   *
   * @link http://unicode.org/reports/tr21/tr21-5.html
   *
   * @param string      $str       <p>The input string.</p>
   * @param bool        $full      [optional] <p>
   *                               <b>true</b>, replace full case folding chars (default)<br>
   *                               <b>false</b>, use only limited static array [UTF8::$COMMON_CASE_FOLD]
   *                               </p>
   * @param bool        $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   * @param string      $encoding  [optional] <p>Set the charset.</p>
   * @param string|null $lang      [optional] <p>Set the language for special cases: az, el, lt, tr</p>
   * @param bool        $lower     [optional] <p>Use lowercase string, otherwise use uppercase string. PS: uppercase is
   *                               for some languages better ...</p>
   *
   * @return string
   */
  public static function strtocasefold(
      string $str,
      bool $full = true,
      bool $cleanUtf8 = false,
      string $encoding = 'UTF-8',
      string $lang = null,
      $lower = true
  ): string
  {
    if ('' === $str) {
      return '';
    }

    $str = self::fixStrCaseHelper($str, $lower, $full);

    if ($lower === true) {
      return self::strtolower($str, $encoding, $cleanUtf8, $lang);
    }

    return self::strtoupper($str, $encoding, $cleanUtf8, $lang);
  }

  /**
   * Make a string lowercase.
   *
   * @link http://php.net/manual/en/function.mb-strtolower.php
   *
   * @param string      $str                   <p>The string being lowercased.</p>
   * @param string      $encoding              [optional] <p>Set the charset for e.g. "mb_" function</p>
   * @param bool        $cleanUtf8             [optional] <p>Remove non UTF-8 chars from the string.</p>
   * @param string|null $lang                  [optional] <p>Set the language for special cases: az, el, lt, tr</p>
   * @param bool        $tryToKeepStringLength [optional] <p>true === try to keep the string length: e.g. ẞ -> ß</p>
   *
   * @return string String with all alphabetic characters converted to lowercase.
   */
  public static function strtolower($str, string $encoding = 'UTF-8', bool $cleanUtf8 = false, string $lang = null, bool $tryToKeepStringLength = false): string
  {
    // init
    $str = (string)$str;

    if ('' === $str) {
      return '';
    }

    if ($cleanUtf8 === true) {
      // "mb_strpos()" and "iconv_strpos()" returns wrong position,
      // if invalid characters are found in $haystack before $needle
      $str = self::clean($str);
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    // hack for old php version or for the polyfill ...
    if ($tryToKeepStringLength === true) {
      $str = self::fixStrCaseHelper($str, true);
    }

    if ($lang !== null) {

      if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
        self::checkForSupport();
      }

      if (self::$SUPPORT['intl'] === true) {

        $langCode = $lang . '-Lower';
        if (!\in_array($langCode, self::$SUPPORT['intl__transliterator_list_ids'], true)) {
          \trigger_error('UTF8::strtolower() cannot handle special language: ' . $lang, E_USER_WARNING);

          $langCode = 'Any-Lower';
        }

        /** @noinspection PhpComposerExtensionStubsInspection */
        return transliterator_transliterate($langCode, $str);
      }

      \trigger_error('UTF8::strtolower() without intl cannot handle the "lang" parameter: ' . $lang, E_USER_WARNING);
    }

    // always fallback via symfony polyfill
    return \mb_strtolower($str, $encoding);
  }

  /**
   * Generic case sensitive transformation for collation matching.
   *
   * @param string $str <p>The input string</p>
   *
   * @return string
   */
  private static function strtonatfold(string $str): string
  {
    /** @noinspection PhpUndefinedClassInspection */
    return \preg_replace('/\p{Mn}+/u', '', \Normalizer::normalize($str, \Normalizer::NFD));
  }

  /**
   * Make a string uppercase.
   *
   * @link http://php.net/manual/en/function.mb-strtoupper.php
   *
   * @param string      $str                   <p>The string being uppercased.</p>
   * @param string      $encoding              [optional] <p>Set the charset.</p>
   * @param bool        $cleanUtf8             [optional] <p>Remove non UTF-8 chars from the string.</p>
   * @param string|null $lang                  [optional] <p>Set the language for special cases: az, el, lt, tr</p>
   * @param bool        $tryToKeepStringLength [optional] <p>true === try to keep the string length: e.g. ẞ -> ß</p>
   *
   * @return string String with all alphabetic characters converted to uppercase.
   */
  public static function strtoupper($str, string $encoding = 'UTF-8', bool $cleanUtf8 = false, string $lang = null, bool $tryToKeepStringLength = false): string
  {
    // init
    $str = (string)$str;

    if ('' === $str) {
      return '';
    }

    if ($cleanUtf8 === true) {
      // "mb_strpos()" and "iconv_strpos()" returns wrong position,
      // if invalid characters are found in $haystack before $needle
      $str = self::clean($str);
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    // hack for old php version or for the polyfill ...
    if ($tryToKeepStringLength === true) {
      $str = self::fixStrCaseHelper($str, false);
    }

    if ($lang !== null) {

      if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
        self::checkForSupport();
      }

      if (self::$SUPPORT['intl'] === true) {

        $langCode = $lang . '-Upper';
        if (!\in_array($langCode, self::$SUPPORT['intl__transliterator_list_ids'], true)) {
          \trigger_error('UTF8::strtoupper() without intl for special language: ' . $lang, E_USER_WARNING);

          $langCode = 'Any-Upper';
        }

        /** @noinspection PhpComposerExtensionStubsInspection */
        return transliterator_transliterate($langCode, $str);
      }

      \trigger_error('UTF8::strtolower() without intl + PHP >= 5.4 cannot handle the "lang"-parameter: ' . $lang, E_USER_WARNING);
    }

    // always fallback via symfony polyfill
    return \mb_strtoupper($str, $encoding);
  }

  /**
   * Translate characters or replace sub-strings.
   *
   * @link  http://php.net/manual/en/function.strtr.php
   *
   * @param string          $str  <p>The string being translated.</p>
   * @param string|string[] $from <p>The string replacing from.</p>
   * @param string|string[] $to   <p>The string being translated to to.</p>
   *
   * @return string
   *                This function returns a copy of str, translating all occurrences of each character in from to the
   *                corresponding character in to.
   */
  public static function strtr(string $str, $from, $to = INF): string
  {
    if ('' === $str) {
      return '';
    }

    if ($from === $to) {
      return $str;
    }

    if (INF !== $to) {
      $from = self::str_split($from);
      $to = self::str_split($to);
      $countFrom = \count($from);
      $countTo = \count($to);

      if ($countFrom > $countTo) {
        $from = \array_slice($from, 0, $countTo);
      } elseif ($countFrom < $countTo) {
        $to = \array_slice($to, 0, $countFrom);
      }

      $from = \array_combine($from, $to);
    }

    if (\is_string($from)) {
      return \str_replace($from, '', $str);
    }

    return \strtr($str, $from);
  }

  /**
   * Return the width of a string.
   *
   * @param string $str       <p>The input string.</p>
   * @param string $encoding  [optional] <p>Set the charset for e.g. "mb_" function</p>
   * @param bool   $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return int
   */
  public static function strwidth(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false): int
  {
    if ('' === $str) {
      return 0;
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if ($cleanUtf8 === true) {
      // iconv and mbstring are not tolerant to invalid encoding
      // further, their behaviour is inconsistent with that of PHP's substr
      $str = self::clean($str);
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    //
    // fallback via mbstring
    //

    if (self::$SUPPORT['mbstring'] === true) {
      return \mb_strwidth($str, $encoding);
    }

    //
    // fallback via vanilla php
    //

    if ('UTF-8' !== $encoding) {
      $str = self::encode('UTF-8', $str, false, $encoding);
    }

    $wide = 0;
    $str = (string)preg_replace('/[\x{1100}-\x{115F}\x{2329}\x{232A}\x{2E80}-\x{303E}\x{3040}-\x{A4CF}\x{AC00}-\x{D7A3}\x{F900}-\x{FAFF}\x{FE10}-\x{FE19}\x{FE30}-\x{FE6F}\x{FF00}-\x{FF60}\x{FFE0}-\x{FFE6}\x{20000}-\x{2FFFD}\x{30000}-\x{3FFFD}]/u', '', $str, -1, $wide);

    return ($wide << 1) + self::strlen($str, 'UTF-8');
  }

  /**
   * Get part of a string.
   *
   * @link http://php.net/manual/en/function.mb-substr.php
   *
   * @param string $str       <p>The string being checked.</p>
   * @param int    $offset    <p>The first position used in str.</p>
   * @param int    $length    [optional] <p>The maximum length of the returned string.</p>
   * @param string $encoding  [optional] <p>Set the charset for e.g. "mb_" function</p>
   * @param bool   $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return string|false
   *                      The portion of <i>str</i> specified by the <i>offset</i> and
   *                      <i>length</i> parameters.</p><p>If <i>str</i> is shorter than <i>offset</i>
   *                      characters long, <b>FALSE</b> will be returned.
   */
  public static function substr(string $str, int $offset = 0, int $length = null, string $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    if ('' === $str) {
      return '';
    }

    // Empty string
    if ($length === 0) {
      return '';
    }

    if ($cleanUtf8 === true) {
      // iconv and mbstring are not tolerant to invalid encoding
      // further, their behaviour is inconsistent with that of PHP's substr
      $str = self::clean($str);
    }

    // Whole string
    if (!$offset && $length === null) {
      return $str;
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    //
    // fallback for binary || ascii only
    //

    if (
        $encoding === 'CP850'
        ||
        $encoding === 'ASCII'
    ) {
      return self::substr_in_byte($str, $offset, $length);
    }

    //
    // fallback via mbstring
    //

    if (self::$SUPPORT['mbstring'] === true) {
      $return = \mb_substr($str, $offset, $length ?? 2147483647, $encoding);
      if ($return !== false) {
        return $return;
      }
    }

    // otherwise we need the string-length and can't fake it via "2147483647"
    $str_length = 0;
    if ($offset || $length === null) {
      $str_length = self::strlen($str, $encoding);
    }

    // e.g.: invalid chars + mbstring not installed
    if ($str_length === false) {
      return false;
    }

    // Empty string
    if ($offset === $str_length && !$length) {
      return '';
    }

    // Impossible
    if ($offset && $offset > $str_length) {
      // "false" is the php native return type here,
      //  but we optimized this for performance ... see "2147483647" instead of "strlen"
      return '';

    }

    if ($length === null) {
      $length = (int)$str_length;
    } else {
      $length = (int)$length;
    }

    if (
        $encoding !== 'UTF-8'
        &&
        self::$SUPPORT['mbstring'] === false
    ) {
      \trigger_error('UTF8::substr() without mbstring cannot handle "' . $encoding . '" encoding', E_USER_WARNING);
    }

    //
    // fallback via intl
    //

    if (
        $encoding === 'UTF-8' // INFO: "grapheme_substr()" can't handle other encodings
        &&
        $offset >= 0 // grapheme_substr() can't handle negative offset
        &&
        self::$SUPPORT['intl'] === true
    ) {
      $returnTmp = \grapheme_substr($str, $offset, $length);
      if ($returnTmp !== false) {
        return $returnTmp;
      }
    }

    //
    // fallback via iconv
    //

    if (
        $length >= 0 // "iconv_substr()" can't handle negative length
        &&
        self::$SUPPORT['iconv'] === true
    ) {
      $returnTmp = \iconv_substr($str, $offset, $length);
      if ($returnTmp !== false) {
        return $returnTmp;
      }
    }

    //
    // fallback for ascii only
    //

    if (self::is_ascii($str)) {
      return \substr($str, $offset, $length);
    }

    //
    // fallback via vanilla php
    //

    // split to array, and remove invalid characters
    $array = self::split($str);

    // extract relevant part, and join to make sting again
    return \implode('', \array_slice($array, $offset, $length));
  }

  /**
   * Binary safe comparison of two strings from an offset, up to length characters.
   *
   * @param string   $str1               <p>The main string being compared.</p>
   * @param string   $str2               <p>The secondary string being compared.</p>
   * @param int      $offset             [optional] <p>The start position for the comparison. If negative, it starts
   *                                     counting from the end of the string.</p>
   * @param int|null $length             [optional] <p>The length of the comparison. The default value is the largest of
   *                                     the length of the str compared to the length of main_str less the offset.</p>
   * @param bool     $case_insensitivity [optional] <p>If case_insensitivity is TRUE, comparison is case
   *                                     insensitive.</p>
   *
   * @return int
   *             <strong>&lt; 0</strong> if str1 is less than str2;<br>
   *             <strong>&gt; 0</strong> if str1 is greater than str2,<br>
   *             <strong>0</strong> if they are equal.
   */
  public static function substr_compare(string $str1, string $str2, int $offset = 0, int $length = null, bool $case_insensitivity = false): int
  {
    if (
        $offset !== 0
        ||
        $length !== null
    ) {
      $str1Tmp = self::substr($str1, $offset, $length);
      if ($str1Tmp === false) {
        $str1Tmp = '';
      }
      $str1 = (string)$str1Tmp;

      $str2Tmp = self::substr($str2, 0, self::strlen($str1));
      if ($str2Tmp === false) {
        $str2Tmp = '';
      }
      $str2 = (string)$str2Tmp;
    }

    if ($case_insensitivity === true) {
      return self::strcasecmp($str1, $str2);
    }

    return self::strcmp($str1, $str2);
  }

  /**
   * Count the number of substring occurrences.
   *
   * @link  http://php.net/manual/en/function.substr-count.php
   *
   * @param string $haystack   <p>The string to search in.</p>
   * @param string $needle     <p>The substring to search for.</p>
   * @param int    $offset     [optional] <p>The offset where to start counting.</p>
   * @param int    $length     [optional] <p>
   *                           The maximum length after the specified offset to search for the
   *                           substring. It outputs a warning if the offset plus the length is
   *                           greater than the haystack length.
   *                           </p>
   * @param string $encoding   [optional] <p>Set the charset for e.g. "mb_" function</p>
   * @param bool   $cleanUtf8  [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return int|false This functions returns an integer or false if there isn't a string.
   */
  public static function substr_count(
      string $haystack,
      string $needle,
      int $offset = 0,
      int $length = null,
      string $encoding = 'UTF-8',
      bool $cleanUtf8 = false
  )
  {
    if ('' === $haystack || '' === $needle) {
      return false;
    }

    if ($offset || $length !== null) {

      if ($length === null) {
        $lengthTmp = self::strlen($haystack);
        if ($lengthTmp === false) {
          return false;
        }
        $length = (int)$lengthTmp;
      }

      if (
          (
              $length !== 0
              &&
              $offset !== 0
          )
          &&
          ($length + $offset) <= 0
          &&
          Bootup::is_php('7.1') === false // output from "substr_count()" have changed in PHP 7.1
      ) {
        return false;
      }

      $haystackTmp = self::substr($haystack, $offset, $length, $encoding);
      if ($haystackTmp === false) {
        $haystackTmp = '';
      }
      $haystack = (string)$haystackTmp;
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if ($cleanUtf8 === true) {
      // "mb_strpos()" and "iconv_strpos()" returns wrong position,
      // if invalid characters are found in $haystack before $needle
      $needle = self::clean($needle);
      $haystack = self::clean($haystack);
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (
        $encoding !== 'UTF-8'
        &&
        self::$SUPPORT['mbstring'] === false
    ) {
      \trigger_error('UTF8::substr_count() without mbstring cannot handle "' . $encoding . '" encoding', E_USER_WARNING);
    }

    if (self::$SUPPORT['mbstring'] === true) {
      return \mb_substr_count($haystack, $needle, $encoding);
    }

    \preg_match_all('/' . \preg_quote($needle, '/') . '/us', $haystack, $matches, PREG_SET_ORDER);

    return \count($matches);
  }

  /**
   * Count the number of substring occurrences.
   *
   * @param string $haystack <p>
   *                         The string being checked.
   *                         </p>
   * @param string $needle   <p>
   *                         The string being found.
   *                         </p>
   * @param int    $offset   [optional] <p>
   *                         The offset where to start counting
   *                         </p>
   * @param int    $length   [optional] <p>
   *                         The maximum length after the specified offset to search for the
   *                         substring. It outputs a warning if the offset plus the length is
   *                         greater than the haystack length.
   *                         </p>
   *
   * @return int|false The number of times the
   *                   needle substring occurs in the
   *                   haystack string.
   */
  public static function substr_count_in_byte(string $haystack, string $needle, int $offset = 0, int $length = null)
  {
    if ($haystack === '' || $needle === '') {
      return 0;
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (
        ($offset || $length !== null)
        &&
        self::$SUPPORT['mbstring_func_overload'] === true
    ) {

      if ($length === null) {
        $lengthTmp = self::strlen($haystack);
        if ($lengthTmp === false) {
          return false;
        }
        $length = (int)$lengthTmp;
      }

      if (
          (
              $length !== 0
              &&
              $offset !== 0
          )
          &&
          ($length + $offset) <= 0
          &&
          Bootup::is_php('7.1') === false // output from "substr_count()" have changed in PHP 7.1
      ) {
        return false;
      }

      $haystackTmp = self::substr_in_byte($haystack, $offset, $length);
      if ($haystackTmp === false) {
        $haystackTmp = '';
      }
      $haystack = (string)$haystackTmp;
    }

    if (self::$SUPPORT['mbstring_func_overload'] === true) {
      // "mb_" is available if overload is used, so use it ...
      return \mb_substr_count($haystack, $needle, 'CP850'); // 8-BIT
    }

    return \substr_count($haystack, $needle, $offset, $length);
  }

  /**
   * Returns the number of occurrences of $substring in the given string.
   * By default, the comparison is case-sensitive, but can be made insensitive
   * by setting $caseSensitive to false.
   *
   * @param string $str           <p>The input string.</p>
   * @param string $substring     <p>The substring to search for.</p>
   * @param bool   $caseSensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>
   * @param string $encoding      [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return int
   */
  public static function substr_count_simple(string $str, string $substring, $caseSensitive = true, string $encoding = 'UTF-8'): int
  {
    if ('' === $str || '' === $substring) {
      return 0;
    }

    // only a fallback to prevent BC in the api ...
    if ($caseSensitive !== false && $caseSensitive !== true) {
      $encoding = (string)$caseSensitive;
    }

    if (!$caseSensitive) {
      $str = self::strtocasefold($str, true, false, $encoding, null, false);
      $substring = self::strtocasefold($substring, true, false, $encoding, null, false);
    }

    return (int)self::substr_count($str, $substring, 0, null, $encoding);
  }

  /**
   * Removes an prefix ($needle) from start of the string ($haystack), case insensitive.
   *
   * @param string $haystack <p>The string to search in.</p>
   * @param string $needle   <p>The substring to search for.</p>
   *
   * @return string Return the sub-string.
   */
  public static function substr_ileft(string $haystack, string $needle): string
  {
    if ('' === $haystack) {
      return '';
    }

    if ('' === $needle) {
      return $haystack;
    }

    if (self::str_istarts_with($haystack, $needle) === true) {
      $haystackTmp = self::substr($haystack, self::strlen($needle));
      if ($haystackTmp === false) {
        $haystackTmp = '';
      }
      $haystack = (string)$haystackTmp;
    }

    return $haystack;
  }

  /**
   * Get part of a string process in bytes.
   *
   * @param string $str    <p>The string being checked.</p>
   * @param int    $offset <p>The first position used in str.</p>
   * @param int    $length [optional] <p>The maximum length of the returned string.</p>
   *
   * @return string|false
   *                      The portion of <i>str</i> specified by the <i>offset</i> and
   *                      <i>length</i> parameters.</p><p>If <i>str</i> is shorter than <i>offset</i>
   *                      characters long, <b>FALSE</b> will be returned.
   */
  public static function substr_in_byte(string $str, int $offset = 0, int $length = null)
  {
    if ($str === '') {
      return '';
    }

    // Empty string
    if ($length === 0) {
      return '';
    }

    // Whole string
    if (!$offset && $length === null) {
      return $str;
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['mbstring_func_overload'] === true) {
      // "mb_" is available if overload is used, so use it ...
      return \mb_substr($str, $offset, $length ?? 2147483647, 'CP850'); // 8-BIT
    }

    return \substr($str, $offset, $length ?? 2147483647);
  }

  /**
   * Removes an suffix ($needle) from end of the string ($haystack), case insensitive.
   *
   * @param string $haystack <p>The string to search in.</p>
   * @param string $needle   <p>The substring to search for.</p>
   *
   * @return string Return the sub-string.
   */
  public static function substr_iright(string $haystack, string $needle): string
  {
    if ('' === $haystack) {
      return '';
    }

    if ('' === $needle) {
      return $haystack;
    }

    if (self::str_iends_with($haystack, $needle) === true) {
      $haystackTmp = self::substr($haystack, 0, self::strlen($haystack) - self::strlen($needle));
      if ($haystackTmp === false) {
        $haystackTmp = '';
      }
      $haystack = (string)$haystackTmp;
    }

    return $haystack;
  }

  /**
   * Removes an prefix ($needle) from start of the string ($haystack).
   *
   * @param string $haystack <p>The string to search in.</p>
   * @param string $needle   <p>The substring to search for.</p>
   *
   * @return string Return the sub-string.
   */
  public static function substr_left(string $haystack, string $needle): string
  {
    if ('' === $haystack) {
      return '';
    }

    if ('' === $needle) {
      return $haystack;
    }

    if (self::str_starts_with($haystack, $needle) === true) {
      $haystackTmp = self::substr($haystack, self::strlen($needle));
      if ($haystackTmp === false) {
        $haystackTmp = '';
      }
      $haystack = (string)$haystackTmp;
    }

    return $haystack;
  }

  /**
   * Replace text within a portion of a string.
   *
   * source: https://gist.github.com/stemar/8287074
   *
   * @param string|string[] $str              <p>The input string or an array of stings.</p>
   * @param string|string[] $replacement      <p>The replacement string or an array of stings.</p>
   * @param int|int[]       $offset           <p>
   *                                          If start is positive, the replacing will begin at the start'th offset
   *                                          into string.
   *                                          <br><br>
   *                                          If start is negative, the replacing will begin at the start'th character
   *                                          from the end of string.
   *                                          </p>
   * @param int|int[]|null  $length           [optional] <p>If given and is positive, it represents the length of the
   *                                          portion of string which is to be replaced. If it is negative, it
   *                                          represents the number of characters from the end of string at which to
   *                                          stop replacing. If it is not given, then it will default to strlen(
   *                                          string ); i.e. end the replacing at the end of string. Of course, if
   *                                          length is zero then this function will have the effect of inserting
   *                                          replacement into string at the given start offset.</p>
   * @param string          $encoding         [optional] <p>Set the charset for e.g. "mb_" function</p>
   *
   * @return string|string[] The result string is returned. If string is an array then array is returned.
   */
  public static function substr_replace($str, $replacement, $offset, $length = null, string $encoding = 'UTF-8')
  {
    if (\is_array($str) === true) {
      $num = \count($str);

      // the replacement
      if (\is_array($replacement) === true) {
        $replacement = \array_slice($replacement, 0, $num);
      } else {
        $replacement = \array_pad([$replacement], $num, $replacement);
      }

      // the offset
      if (\is_array($offset) === true) {
        $offset = \array_slice($offset, 0, $num);
        foreach ($offset as &$valueTmp) {
          $valueTmp = (int)$valueTmp === $valueTmp ? $valueTmp : 0;
        }
        unset($valueTmp);
      } else {
        $offset = \array_pad([$offset], $num, $offset);
      }

      // the length
      if (null === $length) {
        $length = \array_fill(0, $num, 0);
      } elseif (\is_array($length) === true) {
        $length = \array_slice($length, 0, $num);
        foreach ($length as &$valueTmpV2) {
          if (null !== $valueTmpV2) {
            $valueTmpV2 = (int)$valueTmpV2 === $valueTmpV2 ? $valueTmpV2 : $num;
          } else {
            $valueTmpV2 = 0;
          }
        }
        unset($valueTmpV2);
      } else {
        $length = \array_pad([$length], $num, $length);
      }

      // recursive call
      return \array_map([self::class, 'substr_replace'], $str, $replacement, $offset, $length);
    }

    if (\is_array($replacement) === true) {
      if (\count($replacement) > 0) {
        $replacement = $replacement[0];
      } else {
        $replacement = '';
      }
    }

    // init
    $str = (string)$str;
    $replacement = (string)$replacement;

    if ('' === $str) {
      return $replacement;
    }

    if (self::is_ascii($str)) {
      return ($length === null) ?
          \substr_replace($str, $replacement, $offset) :
          \substr_replace($str, $replacement, $offset, $length);
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['mbstring'] === true) {
      $string_length = self::strlen($str, $encoding);

      if ($offset < 0) {
        $offset = \max(0, $string_length + $offset);
      } elseif ($offset > $string_length) {
        $offset = $string_length;
      }

      if ($length < 0) {
        $length = \max(0, $string_length - $offset + $length);
      } elseif ($length === null || $length > $string_length) {
        $length = $string_length;
      }

      if (($offset + $length) > $string_length) {
        $length = $string_length - $offset;
      }

      return self::substr($str, 0, $offset, $encoding) . $replacement . self::substr($str, $offset + $length, $string_length - $offset - $length, $encoding);
    }

    \preg_match_all('/./us', $str, $smatches);
    \preg_match_all('/./us', $replacement, $rmatches);

    if ($length === null) {
      $lengthTmp = self::strlen($str, $encoding);
      if ($lengthTmp === false) {
        // e.g.: non mbstring support + invalid chars
        return '';
      }
      $length = (int)$lengthTmp;
    }

    \array_splice($smatches[0], $offset, $length, $rmatches[0]);

    return \implode('', $smatches[0]);
  }

  /**
   * Removes an suffix ($needle) from end of the string ($haystack).
   *
   * @param string $haystack <p>The string to search in.</p>
   * @param string $needle   <p>The substring to search for.</p>
   *
   * @return string Return the sub-string.
   */
  public static function substr_right(string $haystack, string $needle): string
  {
    if ('' === $haystack) {
      return '';
    }

    if ('' === $needle) {
      return $haystack;
    }

    if (self::str_ends_with($haystack, $needle) === true) {
      $haystackTmp = self::substr($haystack, 0, self::strlen($haystack) - self::strlen($needle));
      if ($haystackTmp === false) {
        $haystackTmp = '';
      }
      $haystack = (string)$haystackTmp;
    }

    return $haystack;
  }

  /**
   * Returns a case swapped version of the string.
   *
   * @param string $str       <p>The input string.</p>
   * @param string $encoding  [optional] <p>Set the charset for e.g. "mb_" function</p>
   * @param bool   $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return string Each character's case swapped.
   */
  public static function swapCase(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false): string
  {
    if ('' === $str) {
      return '';
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if ($cleanUtf8 === true) {
      // "mb_strpos()" and "iconv_strpos()" returns wrong position,
      // if invalid characters are found in $haystack before $needle
      $str = self::clean($str);
    }

    return (string)(self::strtolower($str, $encoding) ^ self::strtoupper($str, $encoding) ^ $str);
  }

  /**
   * Checks whether mbstring is available on the server.
   *
   * @return bool
   *              <strong>true</strong> if available, <strong>false</strong> otherwise.
   */
  public static function symfony_polyfill_used(): bool
  {
    // init
    $return = false;

    $returnTmp = \extension_loaded('mbstring') ? true : false;
    if ($returnTmp === false && \function_exists('mb_strlen')) {
      $return = true;
    }

    $returnTmp = \extension_loaded('iconv') ? true : false;
    if ($returnTmp === false && \function_exists('iconv')) {
      $return = true;
    }

    return $return;
  }

  /**
   * @param string $str
   * @param int    $tabLength
   *
   * @return string
   */
  public static function tabs_to_spaces(string $str, int $tabLength = 4): string
  {
    return \str_replace("\t", \str_repeat(' ', $tabLength), $str);
  }

  /**
   * Converts the first character of each word in the string to uppercase
   * and all other chars to lowercase.
   *
   * @param string      $str                   <p>The input string.</p>
   * @param string      $encoding              [optional] <p>Set the charset for e.g. "mb_" function</p>
   * @param bool        $cleanUtf8             [optional] <p>Remove non UTF-8 chars from the string.</p>
   * @param string|null $lang                  [optional] <p>Set the language for special cases: az, el, lt, tr</p>
   * @param bool        $tryToKeepStringLength [optional] <p>true === try to keep the string length: e.g. ẞ -> ß</p>
   *
   * @return string String with all characters of $str being title-cased.
   */
  public static function titlecase(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false, string $lang = null, bool $tryToKeepStringLength = false): string
  {
    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    return self::str_titleize($str, null, $encoding, $cleanUtf8, $lang, $tryToKeepStringLength, false);
  }

  /**
   * alias for "UTF8::to_ascii()"
   *
   * @see        UTF8::to_ascii()
   *
   * @param string $str
   * @param string $subst_chr
   * @param bool   $strict
   *
   * @return string
   *
   * @deprecated <p>use "UTF8::to_ascii()"</p>
   */
  public static function toAscii(string $str, string $subst_chr = '?', bool $strict = false): string
  {
    return self::to_ascii($str, $subst_chr, $strict);
  }

  /**
   * alias for "UTF8::to_iso8859()"
   *
   * @see        UTF8::to_iso8859()
   *
   * @param string|string[] $str
   *
   * @return string|string[]
   *
   * @deprecated <p>use "UTF8::to_iso8859()"</p>
   */
  public static function toIso8859($str)
  {
    return self::to_iso8859($str);
  }

  /**
   * alias for "UTF8::to_latin1()"
   *
   * @see        UTF8::to_latin1()
   *
   * @param string|string[] $str
   *
   * @return string|string[]
   *
   * @deprecated <p>use "UTF8::to_latin1()"</p>
   */
  public static function toLatin1($str)
  {
    return self::to_latin1($str);
  }

  /**
   * alias for "UTF8::to_utf8()"
   *
   * @see        UTF8::to_utf8()
   *
   * @param string|string[] $str
   *
   * @return string|string[]
   *
   * @deprecated <p>use "UTF8::to_utf8()"</p>
   */
  public static function toUTF8($str)
  {
    return self::to_utf8($str);
  }

  /**
   * Convert a string into ASCII.
   *
   * @param string $str     <p>The input string.</p>
   * @param string $unknown [optional] <p>Character use if character unknown. (default is ?)</p>
   * @param bool   $strict  [optional] <p>Use "transliterator_transliterate()" from PHP-Intl | WARNING: bad
   *                        performance</p>
   *
   * @return string
   */
  public static function to_ascii(string $str, string $unknown = '?', bool $strict = false): string
  {
    static $UTF8_TO_ASCII;

    if ('' === $str) {
      return '';
    }

    // check if we only have ASCII, first (better performance)
    if (self::is_ascii($str) === true) {
      return $str;
    }

    $str = self::clean(
        $str,
        true,
        true,
        true,
        false,
        true,
        true
    );

    // check again, if we only have ASCII, now ...
    if (self::is_ascii($str) === true) {
      return $str;
    }

    if ($strict === true) {

      if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
        self::checkForSupport();
      }

      if (self::$SUPPORT['intl'] === true) {
        // INFO: https://unicode.org/cldr/utility/character.jsp?a=%E2%84%8C
        /** @noinspection PhpComposerExtensionStubsInspection */
        $str = \transliterator_transliterate('NFKC; [:Nonspacing Mark:] Remove; NFKC; Any-Latin; Latin-ASCII;', $str);

        // check again, if we only have ASCII, now ...
        if (self::is_ascii($str) === true) {
          return $str;
        }

      }
    }

    if (self::$ORD === null) {
      self::$ORD = self::getData('ord');
    }

    \preg_match_all('/.{1}|[^\x00]{1,1}$/us', $str, $ar);
    $chars = $ar[0];
    $ord = null;
    foreach ($chars as &$c) {

      $ordC0 = self::$ORD[$c[0]];

      if ($ordC0 >= 0 && $ordC0 <= 127) {
        continue;
      }

      $ordC1 = self::$ORD[$c[1]];

      // ASCII - next please
      if ($ordC0 >= 192 && $ordC0 <= 223) {
        $ord = ($ordC0 - 192) * 64 + ($ordC1 - 128);
      }

      if ($ordC0 >= 224) {
        $ordC2 = self::$ORD[$c[2]];

        if ($ordC0 <= 239) {
          $ord = ($ordC0 - 224) * 4096 + ($ordC1 - 128) * 64 + ($ordC2 - 128);
        }

        if ($ordC0 >= 240) {
          $ordC3 = self::$ORD[$c[3]];

          if ($ordC0 <= 247) {
            $ord = ($ordC0 - 240) * 262144 + ($ordC1 - 128) * 4096 + ($ordC2 - 128) * 64 + ($ordC3 - 128);
          }

          if ($ordC0 >= 248) {
            $ordC4 = self::$ORD[$c[4]];

            if ($ordC0 <= 251) {
              $ord = ($ordC0 - 248) * 16777216 + ($ordC1 - 128) * 262144 + ($ordC2 - 128) * 4096 + ($ordC3 - 128) * 64 + ($ordC4 - 128);
            }

            if ($ordC0 >= 252) {
              $ordC5 = self::$ORD[$c[5]];

              if ($ordC0 <= 253) {
                $ord = ($ordC0 - 252) * 1073741824 + ($ordC1 - 128) * 16777216 + ($ordC2 - 128) * 262144 + ($ordC3 - 128) * 4096 + ($ordC4 - 128) * 64 + ($ordC5 - 128);
              }
            }
          }
        }
      }

      if ($ordC0 === 254 || $ordC0 === 255) {
        $c = $unknown;
        continue;
      }

      if ($ord === null) {
        $c = $unknown;
        continue;
      }

      $bank = $ord >> 8;
      if (!isset($UTF8_TO_ASCII[$bank])) {
        $UTF8_TO_ASCII[$bank] = self::getData(\sprintf('x%02x', $bank));
        if ($UTF8_TO_ASCII[$bank] === false) {
          $UTF8_TO_ASCII[$bank] = [];
        }
      }

      $newchar = $ord & 255;

      if (isset($UTF8_TO_ASCII[$bank][$newchar])) {

        // keep for debugging
        /*
        echo "file: " . sprintf('x%02x', $bank) . "\n";
        echo "char: " . $c . "\n";
        echo "ord: " . $ord . "\n";
        echo "newchar: " . $newchar . "\n";
        echo "ascii: " . $UTF8_TO_ASCII[$bank][$newchar] . "\n";
        echo "bank:" . $bank . "\n\n";
        */

        $c = $UTF8_TO_ASCII[$bank][$newchar];
      } else {

        // keep for debugging missing chars
        /*
        echo "file: " . sprintf('x%02x', $bank) . "\n";
        echo "char: " . $c . "\n";
        echo "ord: " . $ord . "\n";
        echo "newchar: " . $newchar . "\n";
        echo "bank:" . $bank . "\n\n";
        */

        $c = $unknown;
      }
    }

    return \implode('', $chars);
  }

  /**
   * @param mixed $str
   *
   * @return bool
   */
  public static function to_boolean($str): bool
  {
    // init
    $str = (string)$str;

    if ('' === $str) {
      return false;
    }

    $key = \strtolower($str);

    // Info: http://php.net/manual/en/filter.filters.validate.php
    $map = [
        'true'  => true,
        '1'     => true,
        'on'    => true,
        'yes'   => true,
        'false' => false,
        '0'     => false,
        'off'   => false,
        'no'    => false,
    ];

    if (isset($map[$key])) {
      return $map[$key];
    }

    /** @noinspection CallableParameterUseCaseInTypeContextInspection */
    if (\is_numeric($str)) {
      return (((float)$str + 0) > 0);
    }

    return (bool)self::trim($str);
  }

  /**
   * Convert given string to safe filename (and keep string case).
   *
   * @param string $string
   * @param bool   $use_transliterate No transliteration, conversion etc. is done by default - unsafe characters are
   *                                  simply replaced with hyphen.
   * @param string $fallback_char
   *
   * @return string
   */
  public static function to_filename(string $string, bool $use_transliterate = false, string $fallback_char = '-'): string
  {
    if ($use_transliterate === true) {
      $string = self::str_transliterate($string, $fallback_char);
    }

    $fallback_char_escaped = \preg_quote($fallback_char, '/');

    $string = (string)\preg_replace(
        [
            '/[^' . $fallback_char_escaped . '\.\-a-zA-Z0-9\s]/', // 1) remove un-needed chars
            '/[\s]+/',                                            // 2) convert spaces to $fallback_char
            '/[' . $fallback_char_escaped . ']+/',                // 3) remove double $fallback_char's
        ],
        [
            '',
            $fallback_char,
            $fallback_char,
        ],
        $string
    );

    // trim "$fallback_char" from beginning and end of the string
    return \trim($string, $fallback_char);
  }

  /**
   * Convert a string into "ISO-8859"-encoding (Latin-1).
   *
   * @param string|string[] $str
   *
   * @return string|string[]
   */
  public static function to_iso8859($str)
  {
    if (\is_array($str) === true) {
      foreach ($str as $k => $v) {
        $str[$k] = self::to_iso8859($v);
      }

      return $str;
    }

    $str = (string)$str;
    if ('' === $str) {
      return '';
    }

    return self::utf8_decode($str);
  }

  /**
   * alias for "UTF8::to_iso8859()"
   *
   * @see UTF8::to_iso8859()
   *
   * @param string|string[] $str
   *
   * @return string|string[]
   */
  public static function to_latin1($str)
  {
    return self::to_iso8859($str);
  }

  /**
   * This function leaves UTF-8 characters alone, while converting almost all non-UTF8 to UTF8.
   *
   * <ul>
   * <li>It decode UTF-8 codepoints and unicode escape sequences.</li>
   * <li>It assumes that the encoding of the original string is either WINDOWS-1252 or ISO-8859.</li>
   * <li>WARNING: It does not remove invalid UTF-8 characters, so you maybe need to use "UTF8::clean()" for this
   * case.</li>
   * </ul>
   *
   * @param string|string[] $str                    <p>Any string or array.</p>
   * @param bool            $decodeHtmlEntityToUtf8 <p>Set to true, if you need to decode html-entities.</p>
   *
   * @return string|string[] The UTF-8 encoded string.
   */
  public static function to_utf8($str, bool $decodeHtmlEntityToUtf8 = false)
  {
    if (\is_array($str) === true) {
      foreach ($str as $k => $v) {
        $str[$k] = self::to_utf8($v, $decodeHtmlEntityToUtf8);
      }

      return $str;
    }

    $str = (string)$str;
    if ('' === $str) {
      return $str;
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    $max = self::strlen_in_byte($str);
    $buf = '';

    /** @noinspection ForeachInvariantsInspection */
    for ($i = 0; $i < $max; $i++) {
      $c1 = $str[$i];

      if ($c1 >= "\xC0") { // should be converted to UTF8, if it's not UTF8 already

        if ($c1 <= "\xDF") { // looks like 2 bytes UTF8

          $c2 = $i + 1 >= $max ? "\x00" : $str[$i + 1];

          if ($c2 >= "\x80" && $c2 <= "\xBF") { // yeah, almost sure it's UTF8 already
            $buf .= $c1 . $c2;
            $i++;
          } else { // not valid UTF8 - convert it
            $buf .= self::to_utf8_convert_helper($c1);
          }

        } elseif ($c1 >= "\xE0" && $c1 <= "\xEF") { // looks like 3 bytes UTF8

          $c2 = $i + 1 >= $max ? "\x00" : $str[$i + 1];
          $c3 = $i + 2 >= $max ? "\x00" : $str[$i + 2];

          if ($c2 >= "\x80" && $c2 <= "\xBF" && $c3 >= "\x80" && $c3 <= "\xBF") { // yeah, almost sure it's UTF8 already
            $buf .= $c1 . $c2 . $c3;
            $i += 2;
          } else { // not valid UTF8 - convert it
            $buf .= self::to_utf8_convert_helper($c1);
          }

        } elseif ($c1 >= "\xF0" && $c1 <= "\xF7") { // looks like 4 bytes UTF8

          $c2 = $i + 1 >= $max ? "\x00" : $str[$i + 1];
          $c3 = $i + 2 >= $max ? "\x00" : $str[$i + 2];
          $c4 = $i + 3 >= $max ? "\x00" : $str[$i + 3];

          if ($c2 >= "\x80" && $c2 <= "\xBF" && $c3 >= "\x80" && $c3 <= "\xBF" && $c4 >= "\x80" && $c4 <= "\xBF") { // yeah, almost sure it's UTF8 already
            $buf .= $c1 . $c2 . $c3 . $c4;
            $i += 3;
          } else { // not valid UTF8 - convert it
            $buf .= self::to_utf8_convert_helper($c1);
          }

        } else { // doesn't look like UTF8, but should be converted
          $buf .= self::to_utf8_convert_helper($c1);
        }

      } elseif (($c1 & "\xC0") === "\x80") { // needs conversion

        $buf .= self::to_utf8_convert_helper($c1);

      } else { // it doesn't need conversion
        $buf .= $c1;
      }
    }

    // decode unicode escape sequences
    $buf = \preg_replace_callback(
        '/\\\\u([0-9a-f]{4})/i',
        function ($match) {
          // always fallback via symfony polyfill
          return \mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        },
        $buf
    );

    // decode UTF-8 codepoints
    if ($decodeHtmlEntityToUtf8 === true) {
      $buf = self::html_entity_decode($buf);
    }

    return $buf;
  }

  /**
   * @param int|string $input
   *
   * @return string
   */
  private static function to_utf8_convert_helper($input): string
  {
    // init
    $buf = '';

    if (self::$ORD === null) {
      self::$ORD = self::getData('ord');
    }

    if (self::$CHR === null) {
      self::$CHR = self::getData('chr');
    }

    if (self::$WIN1252_TO_UTF8 === null) {
      self::$WIN1252_TO_UTF8 = self::getData('win1252_to_utf8');
    }

    $ordC1 = self::$ORD[$input];
    if (isset(self::$WIN1252_TO_UTF8[$ordC1])) { // found in Windows-1252 special cases
      $buf .= self::$WIN1252_TO_UTF8[$ordC1];
    } else {
      $cc1 = self::$CHR[$ordC1 / 64] | "\xC0";
      $cc2 = ((string)$input & "\x3F") | "\x80";
      $buf .= $cc1 . $cc2;
    }

    return $buf;
  }

  /**
   * Strip whitespace or other characters from beginning or end of a UTF-8 string.
   *
   * INFO: This is slower then "trim()"
   *
   * We can only use the original-function, if we use <= 7-Bit in the string / chars
   * but the check for ACSII (7-Bit) cost more time, then we can safe here.
   *
   * @param string $str   <p>The string to be trimmed</p>
   * @param mixed  $chars [optional] <p>Optional characters to be stripped</p>
   *
   * @return string The trimmed string.
   */
  public static function trim(string $str = '', $chars = INF): string
  {
    if ('' === $str) {
      return '';
    }

    // Info: http://nadeausoftware.com/articles/2007/9/php_tip_how_strip_punctuation_characters_web_page#Unicodecharactercategories
    if ($chars === INF || !$chars) {
      $pattern = "^[\pZ\pC]+|[\pZ\pC]+\$";
    } else {
      $chars = \preg_quote($chars, '/');
      $pattern = "^[$chars]+|[$chars]+\$";
    }

    return self::regex_replace($str, $pattern, '', '', '/');
  }

  /**
   * Makes string's first char uppercase.
   *
   * @param string      $str                   <p>The input string.</p>
   * @param string      $encoding              [optional] <p>Set the charset for e.g. "mb_" function</p>
   * @param bool        $cleanUtf8             [optional] <p>Remove non UTF-8 chars from the string.</p>
   * @param string|null $lang                  [optional] <p>Set the language for special cases: az, el, lt, tr</p>
   * @param bool        $tryToKeepStringLength [optional] <p>true === try to keep the string length: e.g. ẞ -> ß</p>
   *
   * @return string The resulting string.
   */
  public static function ucfirst(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false, string $lang = null, bool $tryToKeepStringLength = false): string
  {
    if ($cleanUtf8 === true) {
      // "mb_strpos()" and "iconv_strpos()" returns wrong position,
      // if invalid characters are found in $haystack before $needle
      $str = self::clean($str);
    }

    $strPartTwo = self::substr($str, 1, null, $encoding);
    if ($strPartTwo === false) {
      $strPartTwo = '';
    }

    $strPartOne = self::strtoupper(
        (string)self::substr($str, 0, 1, $encoding),
        $encoding,
        $cleanUtf8,
        $lang,
        $tryToKeepStringLength
    );

    return $strPartOne . $strPartTwo;
  }

  /**
   * alias for "UTF8::ucfirst()"
   *
   * @see UTF8::ucfirst()
   *
   * @param string $str
   * @param string $encoding
   * @param bool   $cleanUtf8
   *
   * @return string
   */
  public static function ucword(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false): string
  {
    return self::ucfirst($str, $encoding, $cleanUtf8);
  }

  /**
   * Uppercase for all words in the string.
   *
   * @param string   $str        <p>The input string.</p>
   * @param string[] $exceptions [optional] <p>Exclusion for some words.</p>
   * @param string   $charlist   [optional] <p>Additional chars that contains to words and do not start a new word.</p>
   * @param string   $encoding   [optional] <p>Set the charset.</p>
   * @param bool     $cleanUtf8  [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return string
   */
  public static function ucwords(string $str, array $exceptions = [], string $charlist = '', string $encoding = 'UTF-8', bool $cleanUtf8 = false): string
  {
    if (!$str) {
      return '';
    }

    // INFO: mb_convert_case($str, MB_CASE_TITLE);
    // -> MB_CASE_TITLE didn't only uppercase the first letter, it also lowercase all other letters

    if ($cleanUtf8 === true) {
      // "mb_strpos()" and "iconv_strpos()" returns wrong position,
      // if invalid characters are found in $haystack before $needle
      $str = self::clean($str);
    }

    $usePhpDefaultFunctions = !(bool)($charlist . \implode('', $exceptions));

    if (
        $usePhpDefaultFunctions === true
        &&
        self::is_ascii($str) === true
    ) {
      return \ucwords($str);
    }

    $words = self::str_to_words($str, $charlist);
    $newWords = [];

    if (\count($exceptions) > 0) {
      $useExceptions = true;
    } else {
      $useExceptions = false;
    }

    foreach ($words as $word) {

      if (!$word) {
        continue;
      }

      if (
          $useExceptions === false
          ||
          (
              $useExceptions === true
              &&
              !\in_array($word, $exceptions, true)
          )
      ) {
        $word = self::ucfirst($word, $encoding);
      }

      $newWords[] = $word;
    }

    return \implode('', $newWords);
  }

  /**
   * Multi decode html entity & fix urlencoded-win1252-chars.
   *
   * e.g:
   * 'test+test'                     => 'test test'
   * 'D&#252;sseldorf'               => 'Düsseldorf'
   * 'D%FCsseldorf'                  => 'Düsseldorf'
   * 'D&#xFC;sseldorf'               => 'Düsseldorf'
   * 'D%26%23xFC%3Bsseldorf'         => 'Düsseldorf'
   * 'DÃ¼sseldorf'                   => 'Düsseldorf'
   * 'D%C3%BCsseldorf'               => 'Düsseldorf'
   * 'D%C3%83%C2%BCsseldorf'         => 'Düsseldorf'
   * 'D%25C3%2583%25C2%25BCsseldorf' => 'Düsseldorf'
   *
   * @param string $str          <p>The input string.</p>
   * @param bool   $multi_decode <p>Decode as often as possible.</p>
   *
   * @return string
   */
  public static function urldecode(string $str, bool $multi_decode = true): string
  {
    if ('' === $str) {
      return '';
    }

    $pattern = '/%u([0-9a-f]{3,4})/i';
    if (\preg_match($pattern, $str)) {
      $str = (string)\preg_replace($pattern, '&#x\\1;', \urldecode($str));
    }

    $flags = ENT_QUOTES | ENT_HTML5;

    do {
      $str_compare = $str;

      $str = self::fix_simple_utf8(
          \urldecode(
              self::html_entity_decode(
                  self::to_utf8($str),
                  $flags
              )
          )
      );

    } while ($multi_decode === true && $str_compare !== $str);

    return $str;
  }

  /**
   * Return a array with "urlencoded"-win1252 -> UTF-8
   *
   * @deprecated <p>use the "UTF8::urldecode()" function to decode a string</p>
   *
   * @return string[]
   */
  public static function urldecode_fix_win1252_chars(): array
  {
    return [
        '%20' => ' ',
        '%21' => '!',
        '%22' => '"',
        '%23' => '#',
        '%24' => '$',
        '%25' => '%',
        '%26' => '&',
        '%27' => "'",
        '%28' => '(',
        '%29' => ')',
        '%2A' => '*',
        '%2B' => '+',
        '%2C' => ',',
        '%2D' => '-',
        '%2E' => '.',
        '%2F' => '/',
        '%30' => '0',
        '%31' => '1',
        '%32' => '2',
        '%33' => '3',
        '%34' => '4',
        '%35' => '5',
        '%36' => '6',
        '%37' => '7',
        '%38' => '8',
        '%39' => '9',
        '%3A' => ':',
        '%3B' => ';',
        '%3C' => '<',
        '%3D' => '=',
        '%3E' => '>',
        '%3F' => '?',
        '%40' => '@',
        '%41' => 'A',
        '%42' => 'B',
        '%43' => 'C',
        '%44' => 'D',
        '%45' => 'E',
        '%46' => 'F',
        '%47' => 'G',
        '%48' => 'H',
        '%49' => 'I',
        '%4A' => 'J',
        '%4B' => 'K',
        '%4C' => 'L',
        '%4D' => 'M',
        '%4E' => 'N',
        '%4F' => 'O',
        '%50' => 'P',
        '%51' => 'Q',
        '%52' => 'R',
        '%53' => 'S',
        '%54' => 'T',
        '%55' => 'U',
        '%56' => 'V',
        '%57' => 'W',
        '%58' => 'X',
        '%59' => 'Y',
        '%5A' => 'Z',
        '%5B' => '[',
        '%5C' => '\\',
        '%5D' => ']',
        '%5E' => '^',
        '%5F' => '_',
        '%60' => '`',
        '%61' => 'a',
        '%62' => 'b',
        '%63' => 'c',
        '%64' => 'd',
        '%65' => 'e',
        '%66' => 'f',
        '%67' => 'g',
        '%68' => 'h',
        '%69' => 'i',
        '%6A' => 'j',
        '%6B' => 'k',
        '%6C' => 'l',
        '%6D' => 'm',
        '%6E' => 'n',
        '%6F' => 'o',
        '%70' => 'p',
        '%71' => 'q',
        '%72' => 'r',
        '%73' => 's',
        '%74' => 't',
        '%75' => 'u',
        '%76' => 'v',
        '%77' => 'w',
        '%78' => 'x',
        '%79' => 'y',
        '%7A' => 'z',
        '%7B' => '{',
        '%7C' => '|',
        '%7D' => '}',
        '%7E' => '~',
        '%7F' => '',
        '%80' => '`',
        '%81' => '',
        '%82' => '‚',
        '%83' => 'ƒ',
        '%84' => '„',
        '%85' => '…',
        '%86' => '†',
        '%87' => '‡',
        '%88' => 'ˆ',
        '%89' => '‰',
        '%8A' => 'Š',
        '%8B' => '‹',
        '%8C' => 'Œ',
        '%8D' => '',
        '%8E' => 'Ž',
        '%8F' => '',
        '%90' => '',
        '%91' => '‘',
        '%92' => '’',
        '%93' => '“',
        '%94' => '”',
        '%95' => '•',
        '%96' => '–',
        '%97' => '—',
        '%98' => '˜',
        '%99' => '™',
        '%9A' => 'š',
        '%9B' => '›',
        '%9C' => 'œ',
        '%9D' => '',
        '%9E' => 'ž',
        '%9F' => 'Ÿ',
        '%A0' => '',
        '%A1' => '¡',
        '%A2' => '¢',
        '%A3' => '£',
        '%A4' => '¤',
        '%A5' => '¥',
        '%A6' => '¦',
        '%A7' => '§',
        '%A8' => '¨',
        '%A9' => '©',
        '%AA' => 'ª',
        '%AB' => '«',
        '%AC' => '¬',
        '%AD' => '',
        '%AE' => '®',
        '%AF' => '¯',
        '%B0' => '°',
        '%B1' => '±',
        '%B2' => '²',
        '%B3' => '³',
        '%B4' => '´',
        '%B5' => 'µ',
        '%B6' => '¶',
        '%B7' => '·',
        '%B8' => '¸',
        '%B9' => '¹',
        '%BA' => 'º',
        '%BB' => '»',
        '%BC' => '¼',
        '%BD' => '½',
        '%BE' => '¾',
        '%BF' => '¿',
        '%C0' => 'À',
        '%C1' => 'Á',
        '%C2' => 'Â',
        '%C3' => 'Ã',
        '%C4' => 'Ä',
        '%C5' => 'Å',
        '%C6' => 'Æ',
        '%C7' => 'Ç',
        '%C8' => 'È',
        '%C9' => 'É',
        '%CA' => 'Ê',
        '%CB' => 'Ë',
        '%CC' => 'Ì',
        '%CD' => 'Í',
        '%CE' => 'Î',
        '%CF' => 'Ï',
        '%D0' => 'Ð',
        '%D1' => 'Ñ',
        '%D2' => 'Ò',
        '%D3' => 'Ó',
        '%D4' => 'Ô',
        '%D5' => 'Õ',
        '%D6' => 'Ö',
        '%D7' => '×',
        '%D8' => 'Ø',
        '%D9' => 'Ù',
        '%DA' => 'Ú',
        '%DB' => 'Û',
        '%DC' => 'Ü',
        '%DD' => 'Ý',
        '%DE' => 'Þ',
        '%DF' => 'ß',
        '%E0' => 'à',
        '%E1' => 'á',
        '%E2' => 'â',
        '%E3' => 'ã',
        '%E4' => 'ä',
        '%E5' => 'å',
        '%E6' => 'æ',
        '%E7' => 'ç',
        '%E8' => 'è',
        '%E9' => 'é',
        '%EA' => 'ê',
        '%EB' => 'ë',
        '%EC' => 'ì',
        '%ED' => 'í',
        '%EE' => 'î',
        '%EF' => 'ï',
        '%F0' => 'ð',
        '%F1' => 'ñ',
        '%F2' => 'ò',
        '%F3' => 'ó',
        '%F4' => 'ô',
        '%F5' => 'õ',
        '%F6' => 'ö',
        '%F7' => '÷',
        '%F8' => 'ø',
        '%F9' => 'ù',
        '%FA' => 'ú',
        '%FB' => 'û',
        '%FC' => 'ü',
        '%FD' => 'ý',
        '%FE' => 'þ',
        '%FF' => 'ÿ',
    ];
  }

  /**
   * Decodes an UTF-8 string to ISO-8859-1.
   *
   * @param string $str <p>The input string.</p>
   * @param bool   $keepUtf8Chars
   *
   * @return string
   */
  public static function utf8_decode(string $str, bool $keepUtf8Chars = false): string
  {
    if ('' === $str) {
      return '';
    }

    static $UTF8_TO_WIN1252_KEYS_CACHE = null;
    static $UTF8_TO_WIN1252_VALUES_CACHE = null;

    if ($UTF8_TO_WIN1252_KEYS_CACHE === null) {

      if (self::$WIN1252_TO_UTF8 === null) {
        self::$WIN1252_TO_UTF8 = self::getData('win1252_to_utf8');
      }

      $UTF8_TO_WIN1252_KEYS_CACHE = \array_keys(self::$WIN1252_TO_UTF8);
      $UTF8_TO_WIN1252_VALUES_CACHE = \array_values(self::$WIN1252_TO_UTF8);
    }

    /** @noinspection PhpInternalEntityUsedInspection */
    $str = \str_replace($UTF8_TO_WIN1252_KEYS_CACHE, $UTF8_TO_WIN1252_VALUES_CACHE, $str);

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    // save for later comparision
    $str_backup = $str;
    $len = self::strlen_in_byte($str);

    if (self::$ORD === null) {
      self::$ORD = self::getData('ord');
    }

    if (self::$CHR === null) {
      self::$CHR = self::getData('chr');
    }

    $noCharFound = '?';
    /** @noinspection ForeachInvariantsInspection */
    for ($i = 0, $j = 0; $i < $len; ++$i, ++$j) {
      switch ($str[$i] & "\xF0") {
        case "\xC0":
        case "\xD0":
          $c = (self::$ORD[$str[$i] & "\x1F"] << 6) | self::$ORD[$str[++$i] & "\x3F"];
          $str[$j] = $c < 256 ? self::$CHR[$c] : $noCharFound;
          break;

        /** @noinspection PhpMissingBreakStatementInspection */
        case "\xF0":
          ++$i;
        case "\xE0":
          $str[$j] = $noCharFound;
          $i += 2;
          break;

        default:
          $str[$j] = $str[$i];
      }
    }

    $return = self::substr_in_byte($str, 0, $j);
    if ($return === false) {
      $return = '';
    }

    if (
        $keepUtf8Chars === true
        &&
        self::strlen($return) >= self::strlen($str_backup)
    ) {
      return $str_backup;
    }

    return $return;
  }

  /**
   * Encodes an ISO-8859-1 string to UTF-8.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return string
   */
  public static function utf8_encode(string $str): string
  {
    if ('' === $str) {
      return '';
    }

    $str = \utf8_encode($str);

    // the polyfill maybe return false
    /** @noinspection CallableParameterUseCaseInTypeContextInspection */
    if ($str === false) {
      return '';
    }

    if (false === \strpos($str, "\xC2")) {
      return $str;
    }

    static $WIN1252_TO_UTF8_KEYS_CACHE = null;
    static $WIN1252_TO_UTF8_VALUES_CACHE = null;

    if ($WIN1252_TO_UTF8_KEYS_CACHE === null) {

      if (self::$WIN1252_TO_UTF8 === null) {
        self::$WIN1252_TO_UTF8 = self::getData('win1252_to_utf8');
      }

      $WIN1252_TO_UTF8_KEYS_CACHE = \array_keys(self::$WIN1252_TO_UTF8);
      $WIN1252_TO_UTF8_VALUES_CACHE = \array_values(self::$WIN1252_TO_UTF8);
    }

    return \str_replace($WIN1252_TO_UTF8_KEYS_CACHE, $WIN1252_TO_UTF8_VALUES_CACHE, $str);
  }

  /**
   * fix -> utf8-win1252 chars
   *
   * @param string $str <p>The input string.</p>
   *
   * @return string
   *
   * @deprecated <p>use "UTF8::fix_simple_utf8()"</p>
   */
  public static function utf8_fix_win1252_chars(string $str): string
  {
    return self::fix_simple_utf8($str);
  }

  /**
   * Returns an array with all utf8 whitespace characters.
   *
   * @see   : http://www.bogofilter.org/pipermail/bogofilter/2003-March/001889.html
   *
   * @author: Derek E. derek.isname@gmail.com
   *
   * @return string[]
   *                 An array with all known whitespace characters as values and the type of whitespace as keys
   *                 as defined in above URL.
   */
  public static function whitespace_table(): array
  {
    return self::$WHITESPACE_TABLE;
  }

  /**
   * Limit the number of words in a string.
   *
   * @param string $str      <p>The input string.</p>
   * @param int    $limit    <p>The limit of words as integer.</p>
   * @param string $strAddOn <p>Replacement for the striped string.</p>
   *
   * @return string
   */
  public static function words_limit(string $str, int $limit = 100, string $strAddOn = '…'): string
  {
    if ('' === $str) {
      return '';
    }

    if ($limit < 1) {
      return '';
    }

    \preg_match('/^\s*+(?:\S++\s*+){1,' . $limit . '}/u', $str, $matches);

    if (
        !isset($matches[0])
        ||
        self::strlen($str) === self::strlen($matches[0])
    ) {
      return $str;
    }

    return self::rtrim($matches[0]) . $strAddOn;
  }

  /**
   * Wraps a string to a given number of characters
   *
   * @link  http://php.net/manual/en/function.wordwrap.php
   *
   * @param string $str   <p>The input string.</p>
   * @param int    $width [optional] <p>The column width.</p>
   * @param string $break [optional] <p>The line is broken using the optional break parameter.</p>
   * @param bool   $cut   [optional] <p>
   *                      If the cut is set to true, the string is
   *                      always wrapped at or before the specified width. So if you have
   *                      a word that is larger than the given width, it is broken apart.
   *                      </p>
   *
   * @return string The given string wrapped at the specified column.
   */
  public static function wordwrap(string $str, int $width = 75, string $break = "\n", bool $cut = false): string
  {
    if ('' === $str || '' === $break) {
      return '';
    }

    $w = '';
    $strSplit = \explode($break, $str);
    if ($strSplit === false) {
      $count = 0;
    } else {
      $count = \count($strSplit);
    }

    $chars = [];
    /** @noinspection ForeachInvariantsInspection */
    for ($i = 0; $i < $count; ++$i) {

      if ($i) {
        $chars[] = $break;
        $w .= '#';
      }

      $c = $strSplit[$i];
      unset($strSplit[$i]);

      if ($c !== null) {
        foreach (self::split($c) as $c) {
          $chars[] = $c;
          $w .= ' ' === $c ? ' ' : '?';
        }
      }
    }

    $strReturn = '';
    $j = 0;
    $b = $i = -1;
    $w = \wordwrap($w, $width, '#', $cut);

    while (false !== $b = self::strpos($w, '#', $b + 1)) {
      for (++$i; $i < $b; ++$i) {
        $strReturn .= $chars[$j];
        unset($chars[$j++]);
      }

      if ($break === $chars[$j] || ' ' === $chars[$j]) {
        unset($chars[$j++]);
      }

      $strReturn .= $break;
    }

    return $strReturn . \implode('', $chars);
  }

  /**
   * Line-Wrap the string after $limit, but also after the next word.
   *
   * @param string $str
   * @param int    $limit
   *
   * @return string
   */
  public static function wordwrap_per_line(string $str, int $limit): string
  {
    $strings = (array)\preg_split('/\\r\\n|\\r|\\n/', $str);

    $string = '';
    foreach ($strings as $value) {
      if ($value === false) {
        continue;
      }

      $string .= wordwrap($value, $limit);
      $string .= "\n";
    }

    return $string;
  }

  /**
   * Returns an array of Unicode White Space characters.
   *
   * @return string[] An array with numeric code point as key and White Space Character as value.
   */
  public static function ws(): array
  {
    return self::$WHITESPACE;
  }


}
