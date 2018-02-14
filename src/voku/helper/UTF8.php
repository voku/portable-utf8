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
  const GRAPHEME_CLUSTER_RX = '(?:\r\n|(?:[ -~\x{200C}\x{200D}]|[ᆨ-ᇹ]+|[ᄀ-ᅟ]*(?:[가개갸걔거게겨계고과괘괴교구궈궤귀규그긔기까깨꺄꺠꺼께껴꼐꼬꽈꽤꾀꾜꾸꿔꿰뀌뀨끄끠끼나내냐냬너네녀녜노놔놰뇌뇨누눠눼뉘뉴느늬니다대댜댸더데뎌뎨도돠돼되됴두둬뒈뒤듀드듸디따때땨떄떠떼뗘뗴또똬뙈뙤뚀뚜뚸뛔뛰뜌뜨띄띠라래랴럐러레려례로롸뢔뢰료루뤄뤠뤼류르릐리마매먀먜머메며몌모뫄뫠뫼묘무뭐뭬뮈뮤므믜미바배뱌뱨버베벼볘보봐봬뵈뵤부붜붸뷔뷰브븨비빠빼뺘뺴뻐뻬뼈뼤뽀뽜뽸뾔뾰뿌뿨쀄쀠쀼쁘쁴삐사새샤섀서세셔셰소솨쇄쇠쇼수숴쉐쉬슈스싀시싸쌔쌰썌써쎄쎠쎼쏘쏴쐐쐬쑈쑤쒀쒜쒸쓔쓰씌씨아애야얘어에여예오와왜외요우워웨위유으의이자재쟈쟤저제져졔조좌좨죄죠주줘줴쥐쥬즈즤지짜째쨔쨰쩌쩨쪄쪠쪼쫘쫴쬐쬬쭈쭤쮀쮜쮸쯔쯰찌차채챠챼처체쳐쳬초촤쵀최쵸추춰췌취츄츠츼치카캐캬컈커케켜켸코콰쾌쾨쿄쿠쿼퀘퀴큐크킈키타태탸턔터테텨톄토톼퇘퇴툐투퉈퉤튀튜트틔티파패퍄퍠퍼페펴폐포퐈퐤푀표푸풔풰퓌퓨프픠피하해햐햬허헤혀혜호화홰회효후훠훼휘휴흐희히]?[ᅠ-ᆢ]+|[가-힣])[ᆨ-ᇹ]*|[ᄀ-ᅟ]+|[^\p{Cc}\p{Cf}\p{Zl}\p{Zp}])[\p{Mn}\p{Me}\x{09BE}\x{09D7}\x{0B3E}\x{0B57}\x{0BBE}\x{0BD7}\x{0CC2}\x{0CD5}\x{0CD6}\x{0D3E}\x{0D57}\x{0DCF}\x{0DDF}\x{200C}\x{200D}\x{1D165}\x{1D16E}-\x{1D172}]*|[\p{Cc}\p{Cf}\p{Zl}\p{Zp}])';

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
      'ſ'            => 's',
      "\xCD\x85"     => 'ι',
      'ς'            => 'σ',
      "\xCF\x90"     => 'β',
      "\xCF\x91"     => 'θ',
      "\xCF\x95"     => 'φ',
      "\xCF\x96"     => 'π',
      "\xCF\xB0"     => 'κ',
      "\xCF\xB1"     => 'ρ',
      "\xCF\xB5"     => 'ε',
      "\xE1\xBA\x9B" => "\xE1\xB9\xA1",
      "\xE1\xBE\xBE" => 'ι',
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
   * @return string <p>Single Multi-Byte character.</p>
   */
  public static function access(string $str, int $pos): string
  {
    if (!isset($str[0])) {
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
   * @return string <p>The output string that contains BOM.</p>
   */
  public static function add_bom_to_string(string $str): string
  {
    if (self::string_has_bom($str) === false) {
      $str = self::bom() . $str;
    }

    return $str;
  }

  /**
   * Changes all keys in an array.
   *
   * @param array $array <p>The array to work on</p>
   * @param int   $case  [optional] <p> Either <strong>CASE_UPPER</strong><br>
   *                     or <strong>CASE_LOWER</strong> (default)</p>
   *
   * @return array <p>An array with its keys lower or uppercased.</p>
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
   * @return array
   */
  public static function callback($callback, string $str): array
  {
    return self::chr_map($callback, $str);
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
      if (
          self::$SUPPORT['intl'] === true
          &&
          \function_exists('transliterator_list_ids') === true
      ) {
        self::$SUPPORT['intl__transliterator_list_ids'] = transliterator_list_ids();
      }

      // http://php.net/manual/en/class.intlchar.php
      self::$SUPPORT['intlChar'] = self::intlChar_loaded();

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
   * @return string|null <p>Multi-Byte character, returns null on failure or empty input.</p>
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
        self::$CHR = self::getData('chr');
      }

      $chr = self::$CHR[$code_point];

      if ($encoding !== 'UTF-8') {
        $chr = \mb_convert_encoding($chr, $encoding, 'UTF-8');
      }

      return $CHAR_CACHE[$cacheKey] = $chr;
    }

    if (self::$SUPPORT['intlChar'] === true) {
      $chr = \IntlChar::chr($code_point);

      if ($encoding !== 'UTF-8') {
        $chr = \mb_convert_encoding($chr, $encoding, 'UTF-8');
      }

      return $CHAR_CACHE[$cacheKey] = $chr;
    }

    if (self::$CHR === null) {
      self::$CHR = self::getData('chr');
    }

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
      $chr = \mb_convert_encoding($chr, $encoding, 'UTF-8');
    }

    return $CHAR_CACHE[$cacheKey] = $chr;
  }

  /**
   * Applies callback to all characters of a string.
   *
   * @param string|array $callback <p>The callback function.</p>
   * @param string       $str      <p>UTF-8 string to run callback on.</p>
   *
   * @return array <p>The outcome of callback.</p>
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
   * @return array <p>An array of byte lengths of each character.</p>
   */
  public static function chr_size_list(string $str): array
  {
    if (!isset($str[0])) {
      return [];
    }

    $strSplit = self::split($str);

    if (self::$SUPPORT['mbstring_func_overload'] === true) {
      return \array_map(
          function ($data) {
            return UTF8::strlen($data, 'CP850'); // 8-BIT
          },
          $strSplit
      );
    }

    return \array_map(
        function ($data) {
          return \strlen($data);
        },
        $strSplit
    );
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
   * @param string $char <p>The input character</p>
   * @param string $pfix [optional]
   *
   * @return string <p>The code point encoded as U+xxxx<p>
   */
  public static function chr_to_hex(string $char, string $pfix = 'U+'): string
  {
    if (!isset($char[0])) {
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
   * @return string <p>The chunked string</p>
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
   * @return string <p>Clean UTF-8 encoded string.</p>
   */
  public static function clean(string $str, bool $remove_bom = false, bool $normalize_whitespace = false, bool $normalize_msword = false, bool $keep_non_breaking_space = false, bool $replace_diamond_question_mark = false, bool $remove_invisible_characters = true): string
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
  public static function cleanup(string $str): string
  {
    if (!isset($str[0])) {
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
   * @return array <p>The array of code points.</p>
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
   * Returns count of characters used in a string.
   *
   * @param string $str       <p>The input string.</p>
   * @param bool   $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return array <p>An associative array of Character as keys and
   *               their count as values.</p>
   */
  public static function count_chars(string $str, bool $cleanUtf8 = false): array
  {
    return \array_count_values(self::split($str, 1, $cleanUtf8));
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
   * Encode a string with a new charset-encoding.
   *
   * INFO:  The different to "UTF8::utf8_encode()" is that this function, try to fix also broken / double encoding,
   *        so you can call this function also on a UTF-8 String and you don't mess the string.
   *
   * @param string $encoding <p>e.g. 'UTF-16', 'UTF-8', 'ISO-8859-1', etc.</p>
   * @param string $str      <p>The input string</p>
   * @param bool   $force    [optional] <p>Force the new encoding (we try to fix broken / double encoding for
   *                         UTF-8)<br> otherwise we auto-detect the current string-encoding</p>
   *
   * @return string
   */
  public static function encode(string $encoding, string $str, bool $force = true): string
  {
    if (!isset($str[0], $encoding[0])) {
      return $str;
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    $encodingDetected = self::str_detect_encoding($str);

    if (
        $force === true
        ||
        $encodingDetected !== $encoding
    ) {

      if (
          $encoding === 'UTF-8'
          &&
          (
              $force === true
              || $encodingDetected === 'UTF-8'
              || $encodingDetected === 'WINDOWS-1252'
              || $encodingDetected === 'ISO-8859-1'
          )
      ) {
        return self::to_utf8($str);
      }

      if (
          $encoding === 'ISO-8859-1'
          &&
          (
              $force === true
              || $encodingDetected === 'ISO-8859-1'
              || $encodingDetected === 'WINDOWS-1252'
              || $encodingDetected === 'UTF-8'
          )
      ) {
        return self::to_iso8859($str);
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
        \trigger_error('UTF8::encode() without mbstring cannot handle "' . $encoding . '" encoding', E_USER_WARNING);
      }

      $strEncoded = \mb_convert_encoding(
          $str,
          $encoding,
          $encodingDetected
      );

      if ($strEncoded) {
        return $strEncoded;
      }
    }

    return $str;
  }

  /**
   * Reads entire file into a string.
   *
   * WARNING: do not use UTF-8 Option ($convertToUtf8) for binary-files (e.g.: images) !!!
   *
   * @link http://php.net/manual/en/function.file-get-contents.php
   *
   * @param string        $filename         <p>
   *                                        Name of the file to read.
   *                                        </p>
   * @param bool          $use_include_path [optional] <p>
   *                                        Prior to PHP 5, this parameter is called
   *                                        use_include_path and is a bool.
   *                                        As of PHP 5 the FILE_USE_INCLUDE_PATH can be used
   *                                        to trigger include path
   *                                        search.
   *                                        </p>
   * @param resource|null $context          [optional] <p>
   *                                        A valid context resource created with
   *                                        stream_context_create. If you don't need to use a
   *                                        custom context, you can skip this parameter by &null;.
   *                                        </p>
   * @param int|null      $offset           [optional] <p>
   *                                        The offset where the reading starts.
   *                                        </p>
   * @param int|null      $maxLength        [optional] <p>
   *                                        Maximum length of data read. The default is to read until end
   *                                        of file is reached.
   *                                        </p>
   * @param int           $timeout          <p>The time in seconds for the timeout.</p>
   *
   * @param bool          $convertToUtf8    <strong>WARNING!!!</strong> <p>Maybe you can't use this option for e.g.
   *                                        images or pdf, because they used non default utf-8 chars.</p>
   *
   * @return string|false <p>The function returns the read data or false on failure.</p>
   */
  public static function file_get_contents(string $filename, bool $use_include_path = false, $context = null, int $offset = null, int $maxLength = null, int $timeout = 10, bool $convertToUtf8 = true)
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
          self::is_utf16($data) === false
          &&
          self::is_utf32($data) === false
      ) {
        // do nothing, it's binary and not UTF16 or UTF32
      } else {

        $data = self::encode('UTF-8', $data, false);
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
   * @return bool <p><strong>true</strong> if the file has BOM at the start, <strong>false</strong> otherwise.</>
   */
  public static function file_has_bom(string $file_path): bool
  {
    return self::string_has_bom(\file_get_contents($file_path));
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
          $var = \str_replace(["\r\n", "\r"], "\n", $var);
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
   * @return mixed Value of the requested variable on success, <b>FALSE</b> if the filter fails,
   * or <b>NULL</b> if the <i>variable_name</i> variable is not set.
   * If the flag <b>FILTER_NULL_ON_FAILURE</b> is used, it
   * returns <b>FALSE</b> if the variable is not set and <b>NULL</b> if the filter fails.
   * @since 5.2.0
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
   * @return mixed An array containing the values of the requested variables on success, or <b>FALSE</b>
   * on failure. An array value will be <b>FALSE</b> if the filter fails, or <b>NULL</b> if
   * the variable is not set. Or if the flag <b>FILTER_NULL_ON_FAILURE</b>
   * is used, it returns <b>FALSE</b> if the variable is not set and <b>NULL</b> if the filter
   * fails.
   * @since 5.2.0
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
   * @since 5.2.0
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
   * @return mixed An array containing the values of the requested variables on success, or <b>FALSE</b>
   * on failure. An array value will be <b>FALSE</b> if the filter fails, or <b>NULL</b> if
   * the variable is not set.
   * @since 5.2.0
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
    if (!isset($str[0])) {
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
   * @param string|string[] $str <p>You can use a string or an array of strings.</p>
   *
   * @return string|string[] <p>Will return the fixed input-"array" or
   *                         the fixed input-"string".</p>
   */
  public static function fix_utf8($str)
  {
    if (\is_array($str) === true) {
      foreach ($str as $k => $v) {
        $str[$k] = self::fix_utf8($v);
      }

      return $str;
    }

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
   * @return string <p>'RTL' or 'LTR'</p>
   */
  public static function getCharDirection(string $char): string
  {
    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (self::$SUPPORT['intlChar'] === true) {
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
   * @return bool|string|array|int <p>Will return false on error.</p>
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
   * @return mixed <p>Return the full support-"array", if $key === null<br>
   *               return bool-value, if $key is used and available<br>
   *               otherwise return null</p>
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
   * Converts a hexadecimal-value into an UTF-8 character.
   *
   * @param string $hexdec <p>The hexadecimal value.</p>
   *
   * @return string|false <p>One single UTF-8 character.</p>
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
   * @return int|false <p>The code point, or false on failure.</p>
   */
  public static function hex_to_int(string $hexDec)
  {
    if (!isset($hexDec[0])) {
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
   * @param string $encoding       [optional] <p>Default is UTF-8</p>
   *
   * @return string <p>HTML numbered entities.</p>
   */
  public static function html_encode(string $str, bool $keepAsciiChars = false, string $encoding = 'UTF-8'): string
  {
    if (!isset($str[0])) {
      return '';
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    # INFO: http://stackoverflow.com/questions/35854535/better-explanation-of-convmap-in-mb-encode-numericentity
    if (\function_exists('mb_encode_numericentity')) {

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

    return \implode(
        '',
        \array_map(
            function ($data) use ($keepAsciiChars, $encoding) {
              return UTF8::single_chr_html_encode($data, $keepAsciiChars, $encoding);
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
   * @param string $encoding [optional] <p>Encoding to use.</p>
   *
   * @return string <p>The decoded string.</p>
   */
  public static function html_entity_decode(string $str, int $flags = null, string $encoding = 'UTF-8'): string
  {
    if (!isset($str[0])) {
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

    do {
      $str_compare = $str;

      $str = (string)\preg_replace_callback(
          "/&#\d{2,6};/",
          function ($matches) use ($encoding) {
            $returnTmp = \mb_convert_encoding($matches[0], $encoding, 'HTML-ENTITIES');

            if ($returnTmp !== '"' && $returnTmp !== "'") {
              return $returnTmp;
            }

            return $matches[0];
          },
          $str
      );

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
   * @return string the encoded string.
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
   * @return bool <p><strong>true</strong> if available, <strong>false</strong> otherwise.</p>
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
   * @return string <p>The code point, or empty string on failure.</p>
   */
  public static function int_to_hex(int $int, string $pfix = 'U+'): string
  {
    $hex = \dechex($int);

    $hex = (\strlen($hex) < 4 ? \substr('0000' . $hex, -4) : $hex);

    return $pfix . $hex;
  }

  /**
   * Checks whether intl-char is available on the server.
   *
   * @return bool <p><strong>true</strong> if available, <strong>false</strong> otherwise.</p>
   */
  public static function intlChar_loaded(): bool
  {
    return \class_exists('IntlChar');
  }

  /**
   * Checks whether intl is available on the server.
   *
   * @return bool <p><strong>true</strong> if available, <strong>false</strong> otherwise.</p>
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
   * @return boolean
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
  public static function isBase64(string $str): bool
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
   * @return boolean
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
   * @return boolean
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
   * @param string $str
   *
   * @return int|false false if is't not UTF16, 1 for UTF-16LE, 2 for UTF-16BE.
   *
   * @deprecated <p>use "UTF8::is_utf16()"</p>
   */
  public static function isUtf16(string $str)
  {
    return self::is_utf16($str);
  }

  /**
   * alias for "UTF8::is_utf32()"
   *
   * @see        UTF8::is_utf32()
   *
   * @param string $str
   *
   * @return int|false false if is't not UTF16, 1 for UTF-32LE, 2 for UTF-32BE.
   *
   * @deprecated <p>use "UTF8::is_utf32()"</p>
   */
  public static function isUtf32(string $str)
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
   * Checks if a string is 7 bit ASCII.
   *
   * @param string $str <p>The string to check.</p>
   *
   * @return bool <p>
   *              <strong>true</strong> if it is ASCII<br>
   *              <strong>false</strong> otherwise
   *              </p>
   */
  public static function is_ascii(string $str): bool
  {
    if (!isset($str[0])) {
      return true;
    }

    return !\preg_match('/[^\x09\x10\x13\x0A\x0D\x20-\x7E]/', $str);
  }

  /**
   * Returns true if the string is base64 encoded, false otherwise.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return bool <p>Whether or not $str is base64 encoded.</p>
   */
  public static function is_base64(string $str): bool
  {
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
    if (!isset($input[0])) {
      return false;
    }

    if (\preg_match('~^[01]+$~', $input)) {
      return true;
    }

    $testNull = 0;
    $testLength = \strlen($input);
    if ($testLength) {
      $testNull = \substr_count($input, "\x0");
      if (($testNull / $testLength) > 0.3) {
        return true;
      }
    }

    if (
        $strict === true
        &&
        \class_exists('finfo')
    ) {

      $finfo = new \finfo(FILEINFO_MIME_ENCODING);
      $finfo_encoding = $finfo->buffer($input);
      if ($finfo_encoding && $finfo_encoding === 'binary') {
        return true;
      }


    } else {

      if ($testNull > 0) {
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
   * @return boolean
   */
  public static function is_binary_file($file): bool
  {
    try {
      $fp = \fopen($file, 'rb');
      $block = \fread($fp, 512);
      \fclose($fp);
    } catch (\Exception $e) {
      $block = '';
    }

    return self::is_binary($block, true);
  }

  /**
   * Checks if the given string is equal to any "Byte Order Mark".
   *
   * WARNING: Use "UTF8::string_has_bom()" if you will check BOM in a string.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return bool <p><strong>true</strong> if the $utf8_chr is Byte Order Mark, <strong>false</strong> otherwise.</p>
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
   * Check if the string contains any html-tags <lall>.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return boolean
   */
  public static function is_html(string $str): bool
  {
    if (!isset($str[0])) {
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
    if (!isset($str[0])) {
      return false;
    }

    $json = self::json_decode($str);

    return (
               \is_object($json) === true
               ||
               \is_array($json) === true
           )
           &&
           \json_last_error() === JSON_ERROR_NONE;
  }

  /**
   * Check if the string is UTF-16.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return int|false <p>
   *                   <strong>false</strong> if is't not UTF-16,<br>
   *                   <strong>1</strong> for UTF-16LE,<br>
   *                   <strong>2</strong> for UTF-16BE.
   *                   </p>
   */
  public static function is_utf16(string $str)
  {
    if (self::is_binary($str) === false) {
      return false;
    }

    // init
    $strChars = [];

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
   * @param string $str
   *
   * @return int|false <p>
   *                   <strong>false</strong> if is't not UTF-32,<br>
   *                   <strong>1</strong> for UTF-32LE,<br>
   *                   <strong>2</strong> for UTF-32BE.
   *                   </p>
   */
  public static function is_utf32(string $str)
  {
    if (self::is_binary($str) === false) {
      return false;
    }

    // init
    $strChars = [];

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

    if (!isset($str[0])) {
      return true;
    }

    if ($strict === true) {
      if (self::is_utf16($str) !== false) {
        return false;
      }

      if (self::is_utf32($str) !== false) {
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

    $len = self::strlen_in_byte($str);
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
          /* Current octet is neither in the US-ASCII range nor a legal first
           * octet of a multi-octet sequence.
           */
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
          /**
           * End of the multi-octet sequence. mUcs4 now contains the final
           * Unicode code point to be output
           */
          if (0 === --$mState) {
            /*
            * Check for illegal sequences and code points.
            */
            // From Unicode 3.1, non-shortest form is illegal
            if (
                (2 === $mBytes && $mUcs4 < 0x0080) ||
                (3 === $mBytes && $mUcs4 < 0x0800) ||
                (4 === $mBytes && $mUcs4 < 0x10000) ||
                (4 < $mBytes) ||
                // From Unicode 3.2, surrogate characters are illegal.
                (($mUcs4 & 0xFFFFF800) === 0xD800) ||
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
          /**
           *((0xC0 & (*in) != 0x80) && (mState != 0))
           * Incomplete multi-octet sequence.
           */
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
   * @return mixed the value encoded in <i>json</i> in appropriate
   * PHP type. Values true, false and
   * null (case-insensitive) are returned as <b>TRUE</b>, <b>FALSE</b>
   * and <b>NULL</b> respectively. <b>NULL</b> is returned if the
   * <i>json</i> cannot be decoded or if the encoded
   * data is deeper than the recursion limit.
   */
  public static function json_decode(string $json, bool $assoc = false, int $depth = 512, int $options = 0)
  {
    $json = self::filter($json);

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
   * @return string a JSON encoded string on success or <b>FALSE</b> on failure.
   */
  public static function json_encode($value, int $options = 0, int $depth = 512): string
  {
    $value = self::filter($value);

    $json = \json_encode($value, $options, $depth);

    return $json;
  }

  /**
   * Makes string's first char lowercase.
   *
   * @param string $str       <p>The input string</p>
   * @param string $encoding  [optional] <p>Set the charset.</p>
   * @param bool   $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return string <p>The resulting string</p>
   */
  public static function lcfirst(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false): string
  {
    $strPartTwo = self::substr($str, 1, null, $encoding, $cleanUtf8);
    if ($strPartTwo === false) {
      $strPartTwo = '';
    }

    $strPartOne = self::strtolower(
        (string)self::substr($str, 0, 1, $encoding, $cleanUtf8),
        $encoding,
        $cleanUtf8
    );

    return $strPartOne . $strPartTwo;
  }

  /**
   * alias for "UTF8::lcfirst()"
   *
   * @see UTF8::lcfirst()
   *
   * @param string $word
   * @param string $encoding
   * @param bool   $cleanUtf8
   *
   * @return string
   */
  public static function lcword(string $word, string $encoding = 'UTF-8', bool $cleanUtf8 = false): string
  {
    return self::lcfirst($word, $encoding, $cleanUtf8);
  }

  /**
   * Lowercase for all words in the string.
   *
   * @param string   $str        <p>The input string.</p>
   * @param string[] $exceptions [optional] <p>Exclusion for some words.</p>
   * @param string   $charlist   [optional] <p>Additional chars that contains to words and do not start a new word.</p>
   * @param string   $encoding   [optional] <p>Set the charset.</p>
   * @param bool     $cleanUtf8  [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return string
   */
  public static function lcwords(string $str, array $exceptions = [], string $charlist = '', string $encoding = 'UTF-8', bool $cleanUtf8 = false): string
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
        $word = self::lcfirst($word, $encoding, $cleanUtf8);
      }

      $newWords[] = $word;
    }

    return \implode('', $newWords);
  }

  /**
   * Strip whitespace or other characters from beginning of a UTF-8 string.
   *
   * @param string $str   <p>The string to be trimmed</p>
   * @param mixed  $chars <p>Optional characters to be stripped</p>
   *
   * @return string <p>The string with unwanted characters stripped from the left.</p>
   */
  public static function ltrim(string $str = '', $chars = INF): string
  {
    if (!isset($str[0])) {
      return '';
    }

    // Info: http://nadeausoftware.com/articles/2007/9/php_tip_how_strip_punctuation_characters_web_page#Unicodecharactercategories
    if ($chars === INF || !$chars) {
      return \preg_replace('/^[\pZ\pC]+/u', '', $str);
    }

    return \preg_replace('/^' . self::rxClass($chars) . '+/u', '', $str);
  }

  /**
   * Returns the UTF-8 character with the maximum code point in the given data.
   *
   * @param mixed $arg <p>A UTF-8 encoded string or an array of such strings.</p>
   *
   * @return string <p>The character with the highest code point than others.</p>
   */
  public static function max($arg): string
  {
    if (\is_array($arg) === true) {
      $arg = \implode('', $arg);
    }

    return self::chr(\max(self::codepoints($arg)));
  }

  /**
   * Calculates and returns the maximum number of bytes taken by any
   * UTF-8 encoded character in the given string.
   *
   * @param string $str <p>The original Unicode string.</p>
   *
   * @return int <p>Max byte lengths of the given chars.</p>
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
   * @return bool <p><strong>true</strong> if available, <strong>false</strong> otherwise.</p>
   */
  public static function mbstring_loaded(): bool
  {
    $return = \extension_loaded('mbstring') ? true : false;

    if ($return === true) {
      \mb_internal_encoding('UTF-8');
    }

    return $return;
  }

  private static function mbstring_overloaded(): bool
  {
    return \defined('MB_OVERLOAD_STRING')
           &&
           \ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING;
  }

  /**
   * Returns the UTF-8 character with the minimum code point in the given data.
   *
   * @param mixed $arg <strong>A UTF-8 encoded string or an array of such strings.</strong>
   *
   * @return string <p>The character with the lowest code point than others.</p>
   */
  public static function min($arg): string
  {
    if (\is_array($arg) === true) {
      $arg = \implode('', $arg);
    }

    return self::chr(\min(self::codepoints($arg)));
  }

  /**
   * alias for "UTF8::normalize_encoding()"
   *
   * @see        UTF8::normalize_encoding()
   *
   * @param string $encoding
   * @param mixed  $fallback
   *
   * @return string
   *
   * @deprecated <p>use "UTF8::normalize_encoding()"</p>
   */
  public static function normalizeEncoding(string $encoding, $fallback = '')
  {
    return self::normalize_encoding($encoding, $fallback);
  }

  /**
   * Normalize the encoding-"name" input.
   *
   * @param string $encoding <p>e.g.: ISO, UTF8, WINDOWS-1251 etc.</p>
   * @param mixed  $fallback <p>e.g.: UTF-8</p>
   *
   * @return string <p>e.g.: ISO-8859-1, UTF-8, WINDOWS-1251 etc.<br>Will return a empty string as fallback (by
   *                default)</p>
   */
  public static function normalize_encoding(string $encoding, $fallback = '')
  {
    static $STATIC_NORMALIZE_ENCODING_CACHE = [];

    if (!$encoding) {
      return $fallback;
    }

    if (
        'UTF-8' === $encoding
        ||
        'UTF8' === $encoding
    ) {
      return 'UTF-8';
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
   * Normalize some MS Word special characters.
   *
   * @param string $str <p>The string to be normalized.</p>
   *
   * @return string
   */
  public static function normalize_msword(string $str): string
  {
    if (!isset($str[0])) {
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
    if (!isset($str[0])) {
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
   * @param string $encoding [optional] <p>Default is UTF-8</p>
   *
   * @return int <p>
   *             Unicode code point of the given character,<br>
   *             0 on invalid UTF-8 byte sequence.
   *             </p>
   */
  public static function ord(string $chr, string $encoding = 'UTF-8'): int
  {
    // init
    static $CHAR_CACHE = [];

    // save the original string
    $chr_orig = $chr;

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');

      // check again, if it's still not UTF-8
      /** @noinspection NotOptimalIfConditionsInspection */
      if ($encoding !== 'UTF-8') {
        $chr = (string)\mb_convert_encoding($chr, 'UTF-8', $encoding);
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
      $code = \IntlChar::ord($chr);
      if ($code) {
        return $CHAR_CACHE[$cacheKey] = $code;
      }
    }

    /** @noinspection CallableParameterUseCaseInTypeContextInspection */
    $chr = \unpack('C*', (string)self::substr($chr, 0, 4, 'CP850'));
    $code = $chr ? $chr[1] : 0;

    if (0xF0 <= $code && isset($chr[4])) {
      return $CHAR_CACHE[$cacheKey] = (($code - 0xF0) << 18) + (($chr[2] - 0x80) << 12) + (($chr[3] - 0x80) << 6) + $chr[4] - 0x80;
    }

    if (0xE0 <= $code && isset($chr[3])) {
      return $CHAR_CACHE[$cacheKey] = (($code - 0xE0) << 12) + (($chr[2] - 0x80) << 6) + $chr[3] - 0x80;
    }

    if (0xC0 <= $code && isset($chr[2])) {
      return $CHAR_CACHE[$cacheKey] = (($code - 0xC0) << 6) + $chr[2] - 0x80;
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
   * @return bool <p>Will return <strong>false</strong> if php can't parse the string and we haven't any $result.</p>
   */
  public static function parse_str(string $str, &$result, bool $cleanUtf8 = false): bool
  {
    if ($cleanUtf8 === true) {
      $str = self::clean($str);
    }

    /** @noinspection PhpVoidFunctionResultUsedInspection */
    $return = \mb_parse_str($str, $result);

    return !($return === false || empty($result));
  }

  /**
   * Checks if \u modifier is available that enables Unicode support in PCRE.
   *
   * @return bool <p><strong>true</strong> if support is available, <strong>false</strong> otherwise.</p>
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
   * @return array
   */
  public static function range($var1, $var2): array
  {
    if (!$var1 || !$var2) {
      return [];
    }

    if (\ctype_digit((string)$var1)) {
      $start = (int)$var1;
    } elseif (\ctype_xdigit($var1)) {
      $start = (int)self::hex_to_int($var1);
    } else {
      $start = self::ord($var1);
    }

    if (!$start) {
      return [];
    }

    if (\ctype_digit((string)$var2)) {
      $end = (int)$var2;
    } elseif (\ctype_xdigit($var2)) {
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
    if (!isset($str[0])) {
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
   * @return string <p>String without UTF-BOM</p>
   */
  public static function remove_bom(string $str): string
  {
    if (!isset($str[0])) {
      return '';
    }

    foreach (self::$BOM as $bomString => $bomByteLength) {
      if (0 === self::strpos($str, $bomString, 0, 'CP850')) {
        $strTmp = self::substr($str, $bomByteLength, null, 'CP850');
        if ($strTmp === false) {
          $strTmp = '';
        }
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
   * @return string <p>The result string with removed duplicates.</p>
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
    if (!isset($str[0])) {
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
   * @return string <p>The string with unwanted characters stripped from the right.</p>
   */
  public static function rtrim(string $str = '', $chars = INF): string
  {
    if (!isset($str[0])) {
      return '';
    }

    // Info: http://nadeausoftware.com/articles/2007/9/php_tip_how_strip_punctuation_characters_web_page#Unicodecharactercategories
    if ($chars === INF || !$chars) {
      return \preg_replace('/[\pZ\pC]+$/u', '', $str);
    }

    return \preg_replace('/' . self::rxClass($chars) . '+$/u', '', $str);
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
   * @param string $encoding       [optional] <p>Default is UTF-8</p>
   *
   * @return string <p>The HTML numbered entity.</p>
   */
  public static function single_chr_html_encode(string $char, bool $keepAsciiChars = false, string $encoding = 'UTF-8'): string
  {
    if (!isset($char[0])) {
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
   * Convert a string to an array of Unicode characters.
   *
   * @param string $str       <p>The string to split into array.</p>
   * @param int    $length    [optional] <p>Max character length of each array element.</p>
   * @param bool   $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return string[] <p>An array containing chunks of the string.</p>
   */
  public static function split(string $str, int $length = 1, bool $cleanUtf8 = false): array
  {
    if (!isset($str[0])) {
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
   * Optimized "\mb_detect_encoding()"-function -> with support for UTF-16 and UTF-32.
   *
   * @param string $str <p>The input string.</p>
   *
   * @return false|string <p>
   *                      The detected string-encoding e.g. UTF-8 or UTF-16BE,<br>
   *                      otherwise it will return false e.g. for BINARY or not detected encoding.
   *                      </p>
   */
  public static function str_detect_encoding(string $str)
  {
    //
    // 1.) check binary strings (010001001...) like UTF-16 / UTF-32 / PDF / Images / ...
    //

    if (self::is_binary($str, true) === true) {

      if (self::is_utf16($str) === 1) {
        return 'UTF-16LE';
      }

      if (self::is_utf16($str) === 2) {
        return 'UTF-16BE';
      }

      if (self::is_utf32($str) === 1) {
        return 'UTF-32LE';
      }

      if (self::is_utf32($str) === 2) {
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
    // 4.) check via "\mb_detect_encoding()"
    //
    // INFO: UTF-16, UTF-32, UCS2 and UCS4, encoding detection will fail always with "\mb_detect_encoding()"

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
        'ISO-2022-JP',
        'JIS',
        'EUC-JP',
    ];

    $encoding = \mb_detect_encoding($str, $detectOrder, true);
    if ($encoding) {
      return $encoding;
    }

    //
    // 5.) check via "iconv()"
    //

    if (self::$ENCODINGS === null) {
      self::$ENCODINGS = self::getData('encodings');
    }

    $md5 = \md5($str);
    foreach (self::$ENCODINGS as $encodingTmp) {
      # INFO: //IGNORE and //TRANSLIT still throw notice
      /** @noinspection PhpUsageOfSilenceOperatorInspection */
      if (\md5(@\iconv($encodingTmp, $encodingTmp . '//IGNORE', $str)) === $md5) {
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
    if (!isset($haystack[0], $needle[0])) {
      return false;
    }

    if (\substr($haystack, -\strlen($needle)) === $needle) {
      return true;
    }

    return false;
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
    if (!isset($haystack[0], $needle[0])) {
      return false;
    }

    if (self::strcasecmp(\substr($haystack, -\strlen($needle)), $needle) === 0) {
      return true;
    }

    return false;
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
   * @return mixed <p>A string or an array of replacements.</p>
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
    if (!isset($haystack[0], $needle[0])) {
      return false;
    }

    if (self::stripos($haystack, $needle) === 0) {
      return true;
    }

    return false;
  }

  /**
   * Limit the number of characters in a string, but also after the next word.
   *
   * @param string $str
   * @param int    $length
   * @param string $strAddOn
   *
   * @return string
   */
  public static function str_limit_after_word(string $str, int $length = 100, string $strAddOn = '…'): string
  {
    if (!isset($str[0])) {
      return '';
    }

    if (self::strlen($str) <= $length) {
      return $str;
    }

    if (self::substr($str, $length - 1, 1) === ' ') {
      return (string)self::substr($str, 0, $length - 1) . $strAddOn;
    }

    $str = (string)self::substr($str, 0, $length);
    $array = \explode(' ', $str);
    \array_pop($array);
    $new_str = \implode(' ', $array);

    if ($new_str === '') {
      $str = (string)self::substr($str, 0, $length - 1) . $strAddOn;
    } else {
      $str = $new_str . $strAddOn;
    }

    return $str;
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
   *
   * @return string <strong>Returns the padded string</strong>
   */
  public static function str_pad(string $str, int $pad_length, string $pad_string = ' ', int $pad_type = STR_PAD_RIGHT): string
  {
    $str_length = self::strlen($str);

    if (
        \is_int($pad_length) === true
        &&
        $pad_length > 0
        &&
        $pad_length >= $str_length
    ) {
      $ps_length = self::strlen($pad_string);

      $diff = ($pad_length - $str_length);

      switch ($pad_type) {
        case STR_PAD_LEFT:
          $pre = \str_repeat($pad_string, (int)\ceil($diff / $ps_length));
          $pre = (string)self::substr($pre, 0, $diff);
          $post = '';
          break;

        case STR_PAD_BOTH:
          $pre = \str_repeat($pad_string, (int)\ceil($diff / $ps_length / 2));
          $pre = (string)self::substr($pre, 0, $diff / 2);
          $post = \str_repeat($pad_string, (int)\ceil($diff / $ps_length / 2));
          $post = (string)self::substr($post, 0, (int)\ceil($diff / 2));
          break;

        case STR_PAD_RIGHT:
        default:
          $post = \str_repeat($pad_string, (int)\ceil($diff / $ps_length));
          $post = (string)self::substr($post, 0, $diff);
          $pre = '';
      }

      return $pre . $str . $post;
    }

    return $str;
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
   * @return string <p>The repeated string.</p>
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
   * @return mixed <p>This function returns a string or an array with the replaced values.</p>
   */
  public static function str_replace($search, $replace, $subject, int &$count = null)
  {
    return \str_replace($search, $replace, $subject, $count);
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
   * Shuffles all the characters in the string.
   *
   * @param string $str <p>The input string</p>
   *
   * @return string <p>The shuffled string.</p>
   */
  public static function str_shuffle(string $str): string
  {
    $array = self::split($str);

    \shuffle($array);

    return \implode('', $array);
  }

  /**
   * Sort all characters according to code points.
   *
   * @param string $str    <p>A UTF-8 string.</p>
   * @param bool   $unique <p>Sort unique. If <strong>true</strong>, repeated characters are ignored.</p>
   * @param bool   $desc   <p>If <strong>true</strong>, will sort characters in reverse code point order.</p>
   *
   * @return string <p>String of sorted characters.</p>
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
   * Split a string into an array.
   *
   * @param string|string[] $str
   * @param int             $len
   *
   * @return array
   */
  public static function str_split($str, int $len = 1): array
  {
    if (\is_array($str) === true) {
      foreach ($str as $k => $v) {
        $str[$k] = self::str_split($v, $len);
      }

      return $str;
    }

    if (!isset($str[0])) {
      return [];
    }

    if ($len < 1) {
      return \str_split($str, $len);
    }

    /** @noinspection PhpInternalEntityUsedInspection */
    \preg_match_all('/' . self::GRAPHEME_CLUSTER_RX . '/u', $str, $a);
    $a = $a[0];

    if ($len === 1) {
      return $a;
    }

    $arrayOutput = [];
    $p = -1;

    /** @noinspection PhpForeachArrayIsUsedAsValueInspection */
    foreach ($a as $l => $a) {
      if ($l % $len) {
        $arrayOutput[$p] .= $a;
      } else {
        $arrayOutput[++$p] = $a;
      }
    }

    return $arrayOutput;
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
    if (!isset($haystack[0], $needle[0])) {
      return false;
    }

    if (\strpos($haystack, $needle) === 0) {
      return true;
    }

    return false;
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
   * Convert a string into an array of words.
   *
   * @param string   $str
   * @param string   $charList          <p>Additional chars for the definition of "words".</p>
   * @param bool     $removeEmptyValues <p>Remove empty values.</p>
   * @param null|int $removeShortValues
   *
   * @return array
   */
  public static function str_to_words(string $str, string $charList = '', bool $removeEmptyValues = false, int $removeShortValues = null): array
  {
    if (!isset($str[0])) {
      if ($removeEmptyValues === true) {
        return [];
      }

      return [''];
    }

    $charList = self::rxClass($charList, '\pL');

    $return = \preg_split("/({$charList}+(?:[\p{Pd}’']{$charList}+)*)/u", $str, -1, PREG_SPLIT_DELIM_CAPTURE);

    if (
        $removeShortValues === null
        &&
        $removeEmptyValues === false
    ) {
      return $return;
    }

    $tmpReturn = [];
    foreach ($return as $returnValue) {
      if (
          $removeShortValues !== null
          &&
          self::strlen($returnValue) <= $removeShortValues
      ) {
        continue;
      }

      if (
          $removeEmptyValues === true
          &&
          \trim($returnValue) === ''
      ) {
        continue;
      }

      $tmpReturn[] = $returnValue;
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
   * @return array|int <p>The number of words in the string</p>
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

      $numberOfWords = ($len - 1) / 2;

    }

    return $numberOfWords;
  }

  /**
   * Case-insensitive string comparison.
   *
   * INFO: Case-insensitive version of UTF8::strcmp()
   *
   * @param string $str1
   * @param string $str2
   *
   * @return int <p>
   *             <strong>&lt; 0</strong> if str1 is less than str2;<br>
   *             <strong>&gt; 0</strong> if str1 is greater than str2,<br>
   *             <strong>0</strong> if they are equal.
   *             </p>
   */
  public static function strcasecmp(string $str1, string $str2): int
  {
    return self::strcmp(self::strtocasefold($str1), self::strtocasefold($str2));
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
   * @param string $str1
   * @param string $str2
   *
   * @return int  <p>
   *              <strong>&lt; 0</strong> if str1 is less than str2<br>
   *              <strong>&gt; 0</strong> if str1 is greater than str2<br>
   *              <strong>0</strong> if they are equal.
   *              </p>
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

    if (!isset($str[0])) {
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
   * @return string <p>UTF-8 encoded string.</p>
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
   * @return bool <p><strong>true</strong> if the string has BOM at the start, <strong>false</strong> otherwise.</p>
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
   * @return string <p>The stripped string.</p>
   */
  public static function strip_tags(string $str, string $allowable_tags = null, bool $cleanUtf8 = false): string
  {
    if (!isset($str[0])) {
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
    if (!isset($str[0])) {
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
   * @param string $encoding  [optional] <p>Set the charset.</p>
   * @param bool   $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return int|false <p>
   *                   Return the numeric position of the first occurrence of needle in the haystack string,<br>
   *                   or false if needle is not found.
   *                   </p>
   */
  public static function stripos(string $haystack, string $needle, int $offset = 0, string $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    if (!isset($haystack[0], $needle[0])) {
      return false;
    }

    if ($cleanUtf8 === true) {
      // "\mb_strpos" and "\iconv_strpos" returns wrong position,
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

    if (
        $encoding === 'UTF-8' // INFO: "grapheme_stripos()" can't handle other encodings
        &&
        self::$SUPPORT['intl'] === true
    ) {
      return \grapheme_stripos($haystack, $needle, $offset);
    }

    // fallback to "mb_"-function via polyfill
    return \mb_stripos($haystack, $needle, $offset, $encoding);
  }

  /**
   * Returns all of haystack starting from and including the first occurrence of needle to the end.
   *
   * @param string $haystack       <p>The input string. Must be valid UTF-8.</p>
   * @param string $needle         <p>The string to look for. Must be valid UTF-8.</p>
   * @param bool   $before_needle  [optional] <p>
   *                               If <b>TRUE</b>, grapheme_strstr() returns the part of the
   *                               haystack before the first occurrence of the needle (excluding the needle).
   *                               </p>
   * @param string $encoding       [optional] <p>Set the charset for e.g. "\mb_" function</p>
   * @param bool   $cleanUtf8      [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return false|string A sub-string,<br>or <strong>false</strong> if needle is not found.
   */
  public static function stristr(string $haystack, string $needle, bool $before_needle = false, string $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    if (!isset($haystack[0], $needle[0])) {
      return false;
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if ($cleanUtf8 === true) {
      // "\mb_strpos" and "\iconv_strpos" returns wrong position,
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
        $encoding === 'UTF-8' // INFO: "grapheme_stripos()" can't handle other encodings
        &&
        self::$SUPPORT['intl'] === true
    ) {
      return \grapheme_stristr($haystack, $needle, $before_needle);
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
   * @param string $encoding  [optional] <p>Set the charset.</p>
   * @param bool   $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return int <p>The number of characters in the string $str having character encoding $encoding. (One multi-byte
   *             character counted as +1)</p>
   */
  public static function strlen(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false): int
  {
    if (!isset($str[0])) {
      return 0;
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    switch ($encoding) {
      case 'ASCII':
      case 'CP850':
        if (
            $encoding === 'CP850'
            &&
            self::$SUPPORT['mbstring_func_overload'] === false
        ) {
          return \strlen($str);
        }

        return \mb_strlen($str, 'CP850'); // 8-BIT
    }

    if ($cleanUtf8 === true) {
      // "\mb_strlen" and "\iconv_strlen" returns wrong length,
      // if invalid characters are found in $str
      $str = self::clean($str);
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

    if (
        $encoding !== 'UTF-8'
        &&
        self::$SUPPORT['iconv'] === true
        &&
        self::$SUPPORT['mbstring'] === false
    ) {
      $returnTmp = \iconv_strlen($str, $encoding);
      if ($returnTmp !== false) {
        return $returnTmp;
      }
    }

    if (self::$SUPPORT['mbstring'] === true) {
      $returnTmp = \mb_strlen($str, $encoding);
      if ($returnTmp !== false) {
        return $returnTmp;
      }
    }

    if (self::$SUPPORT['iconv'] === true) {
      $returnTmp = \iconv_strlen($str, $encoding);
      if ($returnTmp !== false) {
        return $returnTmp;
      }
    }

    if (
        $encoding === 'UTF-8' // INFO: "grapheme_stripos()" can't handle other encodings
        &&
        self::$SUPPORT['intl'] === true
    ) {
      return \grapheme_strlen($str);
    }

    if (self::is_ascii($str)) {
      return \strlen($str);
    }

    // fallback via vanilla php
    \preg_match_all('/./us', $str, $parts);
    $returnTmp = \count($parts[0]);
    if ($returnTmp !== 0) {
      return $returnTmp;
    }

    // fallback to "mb_"-function via polyfill
    return \mb_strlen($str, $encoding);
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
    if (self::$SUPPORT['mbstring_func_overload'] === true) {
      $len = \mb_strlen($str, 'CP850'); // 8-BIT
    } else {
      $len = \strlen($str);
    }

    return $len;
  }

  /**
   * Case insensitive string comparisons using a "natural order" algorithm.
   *
   * INFO: natural order version of UTF8::strcasecmp()
   *
   * @param string $str1 <p>The first string.</p>
   * @param string $str2 <p>The second string.</p>
   *
   * @return int <strong>&lt; 0</strong> if str1 is less than str2<br>
   *             <strong>&gt; 0</strong> if str1 is greater than str2<br>
   *             <strong>0</strong> if they are equal
   */
  public static function strnatcasecmp(string $str1, string $str2): int
  {
    return self::strnatcmp(self::strtocasefold($str1), self::strtocasefold($str2));
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
   * @return int <strong>&lt; 0</strong> if str1 is less than str2;<br>
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
   * @param string $str1 <p>The first string.</p>
   * @param string $str2 <p>The second string.</p>
   * @param int    $len  <p>The length of strings to be used in the comparison.</p>
   *
   * @return int <strong>&lt; 0</strong> if <i>str1</i> is less than <i>str2</i>;<br>
   *             <strong>&gt; 0</strong> if <i>str1</i> is greater than <i>str2</i>;<br>
   *             <strong>0</strong> if they are equal
   */
  public static function strncasecmp(string $str1, string $str2, int $len): int
  {
    return self::strncmp(self::strtocasefold($str1), self::strtocasefold($str2), $len);
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
   * @return int <strong>&lt; 0</strong> if <i>str1</i> is less than <i>str2</i>;<br>
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
   * @return string|false <p>String starting from the character found, or false if it is not found.</p>
   */
  public static function strpbrk(string $haystack, string $char_list)
  {
    if (!isset($haystack[0], $char_list[0])) {
      return false;
    }

    if (\preg_match('/' . self::rxClass($char_list) . '/us', $haystack, $m)) {
      return \substr($haystack, \strpos($haystack, $m[0]));
    }

    return false;
  }

  /**
   * Find position of first occurrence of string in a string.
   *
   * @link http://php.net/manual/en/function.mb-strpos.php
   *
   * @param string $haystack  <p>The string from which to get the position of the first occurrence of needle.</p>
   * @param string $needle    <p>The string to find in haystack.</p>
   * @param int    $offset    [optional] <p>The search offset. If it is not specified, 0 is used.</p>
   * @param string $encoding  [optional] <p>Set the charset.</p>
   * @param bool   $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return int|false <p>
   *                   The numeric position of the first occurrence of needle in the haystack string.<br>
   *                   If needle is not found it returns false.
   *                   </p>
   */
  public static function strpos(string $haystack, string $needle, int $offset = 0, string $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    if (!isset($haystack[0], $needle[0])) {
      return false;
    }

    // iconv and mbstring do not support integer $needle
    if ((int)$needle === $needle && $needle >= 0) {
      $needle = (string)self::chr((int)$needle);
    }

    if ($cleanUtf8 === true) {
      // "\mb_strpos" and "\iconv_strpos" returns wrong position,
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

    if (
        $encoding === 'CP850'
        &&
        self::$SUPPORT['mbstring_func_overload'] === false
    ) {
      return \strpos($haystack, $needle, $offset);
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

    if (
        $offset >= 0 // iconv_strpos() can't handle negative offset
        &&
        $encoding !== 'UTF-8'
        &&
        self::$SUPPORT['mbstring'] === false
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

    if (self::$SUPPORT['mbstring'] === true) {
      $returnTmp = \mb_strpos($haystack, $needle, $offset, $encoding);
      if ($returnTmp !== false) {
        return $returnTmp;
      }
    }

    if (
        $encoding === 'UTF-8' // INFO: "grapheme_stripos()" can't handle other encodings
        &&
        self::$SUPPORT['intl'] === true
    ) {
      return \grapheme_strpos($haystack, $needle, $offset);
    }

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

    $haystackIsAscii = self::is_ascii($haystack);
    if ($haystackIsAscii && self::is_ascii($needle)) {
      return \strpos($haystack, $needle, $offset);
    }

    // fallback via vanilla php

    if ($haystackIsAscii) {
      $haystackTmp = \substr($haystack, $offset);
    } else {
      $haystackTmp = self::substr($haystack, $offset);
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

    $returnTmp = $offset + self::strlen(\substr($haystack, 0, $pos));
    if ($returnTmp !== false) {
      return $returnTmp;
    }

    // fallback to "mb_"-function via polyfill
    return \mb_strpos($haystack, $needle, $offset, $encoding);
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
   * @param string $encoding      [optional] <p>
   *                              Character encoding name to use.
   *                              If it is omitted, internal character encoding is used.
   *                              </p>
   * @param bool   $cleanUtf8     [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return string|false The portion of haystack or false if needle is not found.
   */
  public static function strrchr(string $haystack, string $needle, bool $before_needle = false, string $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if ($cleanUtf8 === true) {
      // "\mb_strpos" and "\iconv_strpos" returns wrong position,
      // if invalid characters are found in $haystack before $needle
      $needle = self::clean($needle);
      $haystack = self::clean($haystack);
    }

    // fallback to "mb_"-function via polyfill
    return \mb_strrchr($haystack, $needle, $before_needle, $encoding);
  }

  /**
   * Reverses characters order in the string.
   *
   * @param string $str The input string
   *
   * @return string The string with characters in the reverse sequence
   */
  public static function strrev(string $str): string
  {
    if (!isset($str[0])) {
      return '';
    }

    return \implode('', \array_reverse(self::split($str)));
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
   * @param string $encoding       [optional] <p>
   *                               Character encoding name to use.
   *                               If it is omitted, internal character encoding is used.
   *                               </p>
   * @param bool   $cleanUtf8      [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return string|false <p>The portion of haystack or<br>false if needle is not found.</p>
   */
  public static function strrichr(string $haystack, string $needle, bool $before_needle = false, string $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if ($cleanUtf8 === true) {
      // "\mb_strpos" and "\iconv_strpos" returns wrong position,
      // if invalid characters are found in $haystack before $needle
      $needle = self::clean($needle);
      $haystack = self::clean($haystack);
    }

    return \mb_strrichr($haystack, $needle, $before_needle, $encoding);
  }

  /**
   * Find position of last occurrence of a case-insensitive string.
   *
   * @param string $haystack  <p>The string to look in.</p>
   * @param string $needle    <p>The string to look for.</p>
   * @param int    $offset    [optional] <p>Number of characters to ignore in the beginning or end.</p>
   * @param string $encoding  [optional] <p>Set the charset.</p>
   * @param bool   $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return int|false <p>
   *                   The numeric position of the last occurrence of needle in the haystack string.<br>If needle is
   *                   not found, it returns false.
   *                   </p>
   */
  public static function strripos(string $haystack, string $needle, int $offset = 0, string $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    if ((int)$needle === $needle && $needle >= 0) {
      $needle = (string)self::chr((int)$needle);
    }

    if (!isset($haystack[0], $needle[0])) {
      return false;
    }

    if ($cleanUtf8 === true) {
      // \mb_strripos && iconv_strripos is not tolerant to invalid characters
      $needle = self::clean($needle);
      $haystack = self::clean($haystack);
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
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

    if (self::$SUPPORT['mbstring'] === true) {
      return \mb_strripos($haystack, $needle, $offset, $encoding);
    }

    if (
        $encoding === 'UTF-8' // INFO: "grapheme_stripos()" can't handle other encodings
        &&
        self::$SUPPORT['intl'] === true
    ) {
      return \grapheme_strripos($haystack, $needle, $offset);
    }

    // fallback via vanilla php

    return self::strrpos(self::strtoupper($haystack), self::strtoupper($needle), $offset, $encoding, $cleanUtf8);
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
   * @return int|false <p>The numeric position of the last occurrence of needle in the haystack string.<br>If needle
   *                   is not found, it returns false.</p>
   */
  public static function strrpos(string $haystack, $needle, int $offset = null, string $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    if ((int)$needle === $needle && $needle >= 0) {
      $needle = (string)self::chr($needle);
    }
    $needle = (string)$needle;

    if (!isset($haystack[0], $needle[0])) {
      return false;
    }

    if (
        $cleanUtf8 === true
        ||
        $encoding === true // INFO: the "bool"-check is only a fallback for old versions
    ) {
      // \mb_strrpos && iconv_strrpos is not tolerant to invalid characters
      $needle = self::clean($needle);
      $haystack = self::clean($haystack);
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
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

    if (self::$SUPPORT['mbstring'] === true) {
      return \mb_strrpos($haystack, $needle, $offset, $encoding);
    }

    if (
        $encoding === 'UTF-8' // INFO: "grapheme_stripos()" can't handle other encodings
        &&
        self::$SUPPORT['intl'] === true
    ) {
      return \grapheme_strrpos($haystack, $needle, $offset);
    }

    // fallback via vanilla php

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

    $pos = \strrpos($haystack, $needle);
    if ($pos === false) {
      return false;
    }

    return $offset + self::strlen(\substr($haystack, 0, $pos));
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

    if (!isset($str[0], $mask[0])) {
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
   * @param string $encoding       [optional] <p>Set the charset.</p>
   * @param bool   $cleanUtf8      [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return string|false A sub-string,<br>or <strong>false</strong> if needle is not found.
   */
  public static function strstr(string $haystack, string $needle, bool $before_needle = false, $encoding = 'UTF-8', $cleanUtf8 = false)
  {
    if (!isset($haystack[0], $needle[0])) {
      return false;
    }

    if ($cleanUtf8 === true) {
      // "\mb_strpos" and "\iconv_strpos" returns wrong position,
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

    if (
        $encoding !== 'UTF-8'
        &&
        self::$SUPPORT['mbstring'] === false
    ) {
      \trigger_error('UTF8::strstr() without mbstring cannot handle "' . $encoding . '" encoding', E_USER_WARNING);
    }

    if (self::$SUPPORT['mbstring'] === true) {
      return \mb_strstr($haystack, $needle, $before_needle, $encoding);
    }

    if (
        $encoding === 'UTF-8' // INFO: "grapheme_stripos()" can't handle other encodings
        &&
        self::$SUPPORT['intl'] === true
    ) {
      return \grapheme_strstr($haystack, $needle, $before_needle);
    }

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
   * Unicode transformation for case-less matching.
   *
   * @link http://unicode.org/reports/tr21/tr21-5.html
   *
   * @param string $str        <p>The input string.</p>
   * @param bool   $full       [optional] <p>
   *                           <b>true</b>, replace full case folding chars (default)<br>
   *                           <b>false</b>, use only limited static array [UTF8::$commonCaseFold]
   *                           </p>
   * @param bool   $cleanUtf8  [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return string
   */
  public static function strtocasefold(string $str, bool $full = true, bool $cleanUtf8 = false): string
  {
    if (!isset($str[0])) {
      return '';
    }

    static $COMMON_CASE_FOLD_KEYS_CACHE = null;
    static $COMMAN_CASE_FOLD_VALUES_CACHE = null;

    if ($COMMON_CASE_FOLD_KEYS_CACHE === null) {
      $COMMON_CASE_FOLD_KEYS_CACHE = \array_keys(self::$COMMON_CASE_FOLD);
      $COMMAN_CASE_FOLD_VALUES_CACHE = \array_values(self::$COMMON_CASE_FOLD);
    }

    $str = (string)\str_replace($COMMON_CASE_FOLD_KEYS_CACHE, $COMMAN_CASE_FOLD_VALUES_CACHE, $str);

    if ($full) {

      static $FULL_CASE_FOLD = null;
      if ($FULL_CASE_FOLD === null) {
        $FULL_CASE_FOLD = self::getData('caseFolding_full');
      }

      $str = (string)\str_replace($FULL_CASE_FOLD[0], $FULL_CASE_FOLD[1], $str);
    }

    if ($cleanUtf8 === true) {
      $str = self::clean($str);
    }

    return self::strtolower($str);
  }

  /**
   * Make a string lowercase.
   *
   * @link http://php.net/manual/en/function.mb-strtolower.php
   *
   * @param string      $str       <p>The string being lowercased.</p>
   * @param string      $encoding  [optional] <p>Set the charset for e.g. "\mb_" function</p>
   * @param bool        $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   * @param string|null $lang      [optional] <p>Set the language for special cases: az, el, lt, tr</p>
   *
   * @return string str with all alphabetic characters converted to lowercase.
   */
  public static function strtolower($str, string $encoding = 'UTF-8', bool $cleanUtf8 = false, string $lang = null): string
  {
    // init
    $str = (string)$str;
    if (!isset($str[0])) {
      return '';
    }

    if ($cleanUtf8 === true) {
      // "\mb_strpos" and "\iconv_strpos" returns wrong position,
      // if invalid characters are found in $haystack before $needle
      $str = self::clean($str);
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if ($lang !== null) {
      if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
        self::checkForSupport();
      }

      if (self::$SUPPORT['intl'] === true) {

        $langCode = $lang . '-Lower';
        if (!\in_array($langCode, self::$SUPPORT['intl__transliterator_list_ids'], true)) {
          \trigger_error('UTF8::strtolower() without intl for special language: ' . $lang, E_USER_WARNING);

          $langCode = 'Any-Lower';
        }

        return transliterator_transliterate($langCode, $str);
      }

      \trigger_error('UTF8::strtolower() without intl + PHP >= 5.4 cannot handle the "lang"-parameter: ' . $lang, E_USER_WARNING);
    }

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
   * @param string      $str       <p>The string being uppercased.</p>
   * @param string      $encoding  [optional] <p>Set the charset.</p>
   * @param bool        $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   * @param string|null $lang      [optional] <p>Set the language for special cases: az, el, lt, tr</p>
   *
   * @return string <p>$str with all alphabetic characters converted to uppercase.</p>
   */
  public static function strtoupper($str, string $encoding = 'UTF-8', bool $cleanUtf8 = false, string $lang = null): string
  {
    $str = (string)$str;
    if (!isset($str[0])) {
      return '';
    }

    if ($cleanUtf8 === true) {
      // "\mb_strpos" and "\iconv_strpos" returns wrong position,
      // if invalid characters are found in $haystack before $needle
      $str = self::clean($str);
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
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

        return transliterator_transliterate($langCode, $str);
      }

      \trigger_error('UTF8::strtolower() without intl + PHP >= 5.4 cannot handle the "lang"-parameter: ' . $lang, E_USER_WARNING);
    }

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
   * @return string <p>
   *                This function returns a copy of str, translating all occurrences of each character in from to the
   *                corresponding character in to.
   *                </p>
   */
  public static function strtr(string $str, $from, $to = INF): string
  {
    if (!isset($str[0])) {
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
   * @param string $encoding  [optional] <p>Default is UTF-8</p>
   * @param bool   $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return int
   */
  public static function strwidth(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false): int
  {
    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if ($cleanUtf8 === true) {
      // iconv and mbstring are not tolerant to invalid encoding
      // further, their behaviour is inconsistent with that of PHP's substr
      $str = self::clean($str);
    }

    // fallback to "mb_"-function via polyfill
    return \mb_strwidth($str, $encoding);
  }

  /**
   * Get part of a string.
   *
   * @link http://php.net/manual/en/function.mb-substr.php
   *
   * @param string $str       <p>The string being checked.</p>
   * @param int    $offset    <p>The first position used in str.</p>
   * @param int    $length    [optional] <p>The maximum length of the returned string.</p>
   * @param string $encoding  [optional] <p>Default is UTF-8</p>
   * @param bool   $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return string|false <p>The portion of <i>str</i> specified by the <i>offset</i> and
   *                      <i>length</i> parameters.</p><p>If <i>str</i> is shorter than <i>offset</i>
   *                      characters long, <b>FALSE</b> will be returned.</p>
   */
  public static function substr(string $str, int $offset = 0, int $length = null, string $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    if (!isset($str[0])) {
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

    $str_length = 0;
    if ($offset || $length === null) {
      $str_length = self::strlen($str, $encoding);
    }

    // Empty string
    if ($offset === $str_length && !$length) {
      return '';
    }

    // Impossible
    if ($offset && $offset > $str_length) {
      return false;
    }

    if ($length === null) {
      $length = $str_length;
    } else {
      $length = (int)$length;
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if (!isset(self::$SUPPORT['already_checked_via_portable_utf8'])) {
      self::checkForSupport();
    }

    if (
        $encoding === 'CP850'
        &&
        self::$SUPPORT['mbstring_func_overload'] === false
    ) {
      return \substr($str, $offset, $length ?? $str_length);
    }

    if (
        $encoding !== 'UTF-8'
        &&
        self::$SUPPORT['mbstring'] === false
    ) {
      \trigger_error('UTF8::substr() without mbstring cannot handle "' . $encoding . '" encoding', E_USER_WARNING);
    }

    if (self::$SUPPORT['mbstring'] === true) {
      return \mb_substr($str, $offset, $length, $encoding);
    }

    if (
        $encoding === 'UTF-8' // INFO: "grapheme_stripos()" can't handle other encodings
        &&
        self::$SUPPORT['intl'] === true
    ) {
      return \grapheme_substr($str, $offset, $length);
    }

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

    if (self::is_ascii($str)) {
      return ($length === null) ?
          \substr($str, $offset) :
          \substr($str, $offset, $length);
    }

    // fallback via vanilla php

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
   * @return int <p>
   *             <strong>&lt; 0</strong> if str1 is less than str2;<br>
   *             <strong>&gt; 0</strong> if str1 is greater than str2,<br>
   *             <strong>0</strong> if they are equal.
   *             </p>
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
   * @param string $encoding   <p>Set the charset.</p>
   * @param bool   $cleanUtf8  [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return int|false <p>This functions returns an integer or false if there isn't a string.</p>
   */
  public static function substr_count(string $haystack, string $needle, int $offset = 0, int $length = null, string $encoding = 'UTF-8', bool $cleanUtf8 = false)
  {
    if (!isset($haystack[0], $needle[0])) {
      return false;
    }

    if ($offset || $length !== null) {

      if ($length === null) {
        $length = self::strlen($haystack);
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
      // "\mb_strpos" and "\iconv_strpos" returns wrong position,
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
   * Removes an prefix ($needle) from start of the string ($haystack), case insensitive.
   *
   * @param string $haystack <p>The string to search in.</p>
   * @param string $needle   <p>The substring to search for.</p>
   *
   * @return string <p>Return the sub-string.</p>
   */
  public static function substr_ileft(string $haystack, string $needle): string
  {
    if (!isset($haystack[0])) {
      return '';
    }

    if (!isset($needle[0])) {
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
   * Removes an suffix ($needle) from end of the string ($haystack), case insensitive.
   *
   * @param string $haystack <p>The string to search in.</p>
   * @param string $needle   <p>The substring to search for.</p>
   *
   * @return string <p>Return the sub-string.</p>
   */
  public static function substr_iright(string $haystack, string $needle): string
  {
    if (!isset($haystack[0])) {
      return '';
    }

    if (!isset($needle[0])) {
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
   * @return string <p>Return the sub-string.</p>
   */
  public static function substr_left(string $haystack, string $needle): string
  {
    if (!isset($haystack[0])) {
      return '';
    }

    if (!isset($needle[0])) {
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
   *
   * @return string|string[] <p>The result string is returned. If string is an array then array is returned.</p>
   */
  public static function substr_replace($str, $replacement, $offset, $length = null)
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

    if (!isset($str[0])) {
      return $replacement;
    }

    if (self::is_ascii($str)) {
      return ($length === null) ?
          \substr_replace($str, $replacement, $offset) :
          \substr_replace($str, $replacement, $offset, $length);
    }

    \preg_match_all('/./us', $str, $smatches);
    \preg_match_all('/./us', $replacement, $rmatches);

    if ($length === null) {
      $length = self::strlen($str);
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
   * @return string <p>Return the sub-string.</p>
   */
  public static function substr_right(string $haystack, string $needle): string
  {
    if (!isset($haystack[0])) {
      return '';
    }

    if (!isset($needle[0])) {
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
   * @param string $encoding  [optional] <p>Default is UTF-8</p>
   * @param bool   $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return string <p>Each character's case swapped.</p>
   */
  public static function swapCase(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false): string
  {
    if (!isset($str[0])) {
      return '';
    }

    if ($encoding !== 'UTF-8' && $encoding !== 'CP850') {
      $encoding = self::normalize_encoding($encoding, 'UTF-8');
    }

    if ($cleanUtf8 === true) {
      // "\mb_strpos" and "\iconv_strpos" returns wrong position,
      // if invalid characters are found in $haystack before $needle
      $str = self::clean($str);
    }

    $strSwappedCase = \preg_replace_callback(
        '/[\S]/u',
        function ($match) use ($encoding) {
          $marchToUpper = UTF8::strtoupper($match[0], $encoding);

          if ($match[0] === $marchToUpper) {
            return UTF8::strtolower($match[0], $encoding);
          }

          return $marchToUpper;
        },
        $str
    );

    return $strSwappedCase;
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

    if (!isset($str[0])) {
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

        // HACK for issue from "transliterator_transliterate()"
        $str = \str_replace(
            'ℌ',
            'H',
            $str
        );

        $str = transliterator_transliterate('NFD; [:Nonspacing Mark:] Remove; NFC; Any-Latin; Latin-ASCII;', $str);

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

      if (!isset($ord)) {
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
    if (!isset($str[0])) {
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
   * @return string|string[] <p>The UTF-8 encoded string.</p>
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
    if (!isset($str[0])) {
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
            $buf .= self::to_utf8_convert($c1);
          }

        } elseif ($c1 >= "\xE0" && $c1 <= "\xEF") { // looks like 3 bytes UTF8

          $c2 = $i + 1 >= $max ? "\x00" : $str[$i + 1];
          $c3 = $i + 2 >= $max ? "\x00" : $str[$i + 2];

          if ($c2 >= "\x80" && $c2 <= "\xBF" && $c3 >= "\x80" && $c3 <= "\xBF") { // yeah, almost sure it's UTF8 already
            $buf .= $c1 . $c2 . $c3;
            $i += 2;
          } else { // not valid UTF8 - convert it
            $buf .= self::to_utf8_convert($c1);
          }

        } elseif ($c1 >= "\xF0" && $c1 <= "\xF7") { // looks like 4 bytes UTF8

          $c2 = $i + 1 >= $max ? "\x00" : $str[$i + 1];
          $c3 = $i + 2 >= $max ? "\x00" : $str[$i + 2];
          $c4 = $i + 3 >= $max ? "\x00" : $str[$i + 3];

          if ($c2 >= "\x80" && $c2 <= "\xBF" && $c3 >= "\x80" && $c3 <= "\xBF" && $c4 >= "\x80" && $c4 <= "\xBF") { // yeah, almost sure it's UTF8 already
            $buf .= $c1 . $c2 . $c3 . $c4;
            $i += 3;
          } else { // not valid UTF8 - convert it
            $buf .= self::to_utf8_convert($c1);
          }

        } else { // doesn't look like UTF8, but should be converted
          $buf .= self::to_utf8_convert($c1);
        }

      } elseif (($c1 & "\xC0") === "\x80") { // needs conversion

        $buf .= self::to_utf8_convert($c1);

      } else { // it doesn't need conversion
        $buf .= $c1;
      }
    }

    // decode unicode escape sequences
    $buf = \preg_replace_callback(
        '/\\\\u([0-9a-f]{4})/i',
        function ($match) {
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
   * @param int $int
   *
   * @return string
   */
  private static function to_utf8_convert($int): string
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

    $ordC1 = self::$ORD[$int];
    if (isset(self::$WIN1252_TO_UTF8[$ordC1])) { // found in Windows-1252 special cases
      $buf .= self::$WIN1252_TO_UTF8[$ordC1];
    } else {
      $cc1 = self::$CHR[$ordC1 / 64] | "\xC0";
      $cc2 = ($int & "\x3F") | "\x80";
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
   * @return string <p>The trimmed string.</p>
   */
  public static function trim(string $str = '', $chars = INF): string
  {
    if (!isset($str[0])) {
      return '';
    }

    // Info: http://nadeausoftware.com/articles/2007/9/php_tip_how_strip_punctuation_characters_web_page#Unicodecharactercategories
    if ($chars === INF || !$chars) {
      return \preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $str);
    }

    return self::rtrim(self::ltrim($str, $chars), $chars);
  }

  /**
   * Makes string's first char uppercase.
   *
   * @param string $str       <p>The input string.</p>
   * @param string $encoding  [optional] <p>Set the charset.</p>
   * @param bool   $cleanUtf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
   *
   * @return string <p>The resulting string</p>
   */
  public static function ucfirst(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false): string
  {
    if ($cleanUtf8 === true) {
      // "\mb_strpos" and "\iconv_strpos" returns wrong position,
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
        $cleanUtf8
    );

    return $strPartOne . $strPartTwo;
  }

  /**
   * alias for "UTF8::ucfirst()"
   *
   * @see UTF8::ucfirst()
   *
   * @param string $word
   * @param string $encoding
   * @param bool   $cleanUtf8
   *
   * @return string
   */
  public static function ucword(string $word, string $encoding = 'UTF-8', bool $cleanUtf8 = false): string
  {
    return self::ucfirst($word, $encoding, $cleanUtf8);
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
      // "\mb_strpos" and "\iconv_strpos" returns wrong position,
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
    if (!isset($str[0])) {
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
   * @return array
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
    if (!isset($str[0])) {
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

    $return = (string)self::substr($str, 0, $j, 'CP850'); // 8-BIT

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
    if (!isset($str[0])) {
      return '';
    }

    $strTmp = \utf8_encode($str);

    // the polyfill maybe return false
    if ($strTmp === false) {
      return '';
    }

    $str = (string)$strTmp;
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
   * @return array <p>
   *               An array with all known whitespace characters as values and the type of whitespace as keys
   *               as defined in above URL.
   *               </p>
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
    if (!isset($str[0])) {
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
   * @return string <p>The given string wrapped at the specified column.</p>
   */
  public static function wordwrap(string $str, int $width = 75, string $break = "\n", bool $cut = false): string
  {
    if (!isset($str[0], $break[0])) {
      return '';
    }

    $w = '';
    $strSplit = \explode($break, $str);
    $count = \count($strSplit);

    $chars = [];
    /** @noinspection ForeachInvariantsInspection */
    for ($i = 0; $i < $count; ++$i) {

      if ($i) {
        $chars[] = $break;
        $w .= '#';
      }

      $c = $strSplit[$i];
      unset($strSplit[$i]);

      foreach (self::split($c) as $c) {
        $chars[] = $c;
        $w .= ' ' === $c ? ' ' : '?';
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
   * Returns an array of Unicode White Space characters.
   *
   * @return array <p>An array with numeric code point as key and White Space Character as value.</p>
   */
  public static function ws(): array
  {
    return self::$WHITESPACE;
  }

}
