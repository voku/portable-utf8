# Portable UTF-8 | API

The API from the "UTF8"-Class is written as small static methods that will match the default PHP-API e.g.


## Methods

##### access(string $str, int $pos)

Return the character at the specified position: $str[1] like functionality.

```php
UTF8::access('fòô', 1); // 'ô'
```

##### add_bom_to_string(string $str)

Prepends UTF-8 BOM character to the string and returns the whole string.

If BOM already existed there, the Input string is returned.

```php
UTF8::add_bom_to_string('fòô'); // "\xEF\xBB\xBF" . 'fòô'
```

##### binary_to_str(mixed $bin)

Convert binary into an string.

INFO: opposite to UTF8::str_to_binary()

```php
UTF8::binary_to_str('11110000100111111001100010000011'); // '😃'
```

##### bom()

Returns the UTF-8 Byte Order Mark Character.

```php
UTF8::bom(); // "\xEF\xBB\xBF"
```

##### chr(int $code_point) : string

Generates a UTF-8 encoded character from the given code point.

INFO: opposite to UTF8::ord()

```php
UTF8::chr(666); // 'ʚ'
```

##### chr_map(string|array $callback, string $str) : array

Applies callback to all characters of a string.

```php
UTF8::chr_map(['voku\helper\UTF8', 'strtolower'], 'Κόσμε'); // ['κ','ό', 'σ', 'μ', 'ε']
```

##### chr_size_list(string $str) : array

Generates a UTF-8 encoded character from the given code point.

 1 byte => U+0000  - U+007F
 2 byte => U+0080  - U+07FF
 3 byte => U+0800  - U+FFFF
 4 byte => U+10000 - U+10FFFF

```php
UTF8::chr_size_list('中文空白-test'); // [3, 3, 3, 3, 1, 1, 1, 1, 1]
```

##### chr_to_decimal(string $chr) : int

Get a decimal code representation of a specific character.

```php
UTF8::chr_to_decimal('§'); // 0xa7
```

##### chr_to_hex(string $chr, string $pfix = 'U+')

Get hexadecimal code point (U+xxxx) of a UTF-8 encoded character.

```php
UTF8::chr_to_hex('§'); // 0xa7
```

##### chunk_split(string $body, int $chunklen = 76, string $end = "\r\n") : string

Splits a string into smaller chunks and multiple lines, using the specified line ending character.

```php
UTF8::chunk_split('ABC-ÖÄÜ-中文空白-κόσμε', 3); // "ABC\r\n-ÖÄ\r\nÜ-中\r\n文空白\r\n-κό\r\nσμε"
```

##### clean(string $str, bool $remove_bom = false, bool $normalize_whitespace = false, bool $normalize_msword = false, bool $keep_non_breaking_space = false) : string

Accepts a string and removes all non-UTF-8 characters from it + extras if needed.

```php
UTF8::clean("\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃 - DÃ¼sseldorf", true, true); // '„Abcdef  …” — 😃 - DÃ¼sseldorf'
```

##### cleanup(string $str) : string

Clean-up a and show only printable UTF-8 chars at the end + fix UTF-8 encoding.

```php
UTF8::cleanup("\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃 - DÃ¼sseldorf", true, true); // '„Abcdef  …” — 😃 - Düsseldorf'
```

##### codepoints(mixed $arg, bool $u_style = false) : array

Accepts a string and returns an array of Unicode code points.

INFO: opposite to UTF8::string()

```php
UTF8::codepoints('κöñ'); // array(954, 246, 241)
// ... OR ...
UTF8::codepoints('κöñ', true); // array('U+03ba', 'U+00f6', 'U+00f1')
```

##### count_chars(string $str, bool $cleanUtf8 = false) : array

Returns count of characters used in a string.

```php
UTF8::count_chars('κaκbκc'); // array('κ' => 3, 'a' => 1, 'b' => 1, 'c' => 1)
```

##### encode(string $encoding, string $str, bool $force = true) : string

Encode a string with a new charset-encoding.

INFO:  The different to "UTF8::utf8_encode()" is that this function, try to fix also broken / double encoding,
       so you can call this function also on a UTF-8 String and you don't mess the string.

```php
UTF8::encode('ISO-8859-1', '-ABC-中文空白-'); // '-ABC-????-'
//
UTF8::encode('UTF-8', '-ABC-中文空白-'); // '-ABC-中文空白-'
```

##### file_get_contents(string $filename, int|null $flags = null, resource|null $context = null, int|null $offset = null, int|null $maxlen = null, int $timeout = 10, bool $convertToUtf8 = true) : string

Reads entire file into a string.

WARNING: do not use UTF-8 Option ($convertToUtf8) for binary-files (e.g.: images) !!!

```php
UTF8::file_get_contents('utf16le.txt'); // ...
```

##### file_has_bom(string $file_path) : bool

Checks if a file starts with BOM (Byte Order Mark) character.

```php
UTF8::file_has_bom('utf8_with_bom.txt'); // true
```

##### filter(mixed $var, int $normalization_form = 4, string $leading_combining = '◌') : mixed

Normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

```php
UTF8::filter(array("\xE9", 'à', 'a')); // array('é', 'à', 'a')
```

##### filter_input(int $type, string $var, int $filter = FILTER_DEFAULT, null|array $option = null) : string

"filter_input()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

```php
// _GET['foo'] = 'bar';
UTF8::filter_input(INPUT_GET, 'foo', FILTER_SANITIZE_STRING)); // 'bar'
```

##### filter_input_array(int $type, mixed $definition = null, bool $add_empty = true) : mixed

"filter_input_array()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

```php
// _GET['foo'] = 'bar';
UTF8::filter_input_array(INPUT_GET, array('foo' => 'FILTER_SANITIZE_STRING')); // array('bar')
```

##### filter_var(string $var, int $filter = FILTER_DEFAULT, array $option = null) : string

"filter_var()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

```php
UTF8::filter_var('-ABC-中文空白-', FILTER_VALIDATE_URL); // false
```

##### filter_var_array(array $data, mixed $definition = null, bool $add_empty = true) : mixed

"filter_var_array()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

```php
$filters = [ 
  'name'  => ['filter'  => FILTER_CALLBACK, 'options' => ['voku\helper\UTF8', 'ucwords']],
  'age'   => ['filter'  => FILTER_VALIDATE_INT, 'options' => ['min_range' => 1, 'max_range' => 120]],
  'email' => FILTER_VALIDATE_EMAIL,
];

$data = [
  'name' => 'κόσμε', 
  'age' => '18', 
  'email' => 'foo@bar.de'
];

UTF8::filter_var_array($data, $filters, true); // ['name' => 'Κόσμε', 'age' => 18, 'email' => 'foo@bar.de']
```

##### fits_inside(string $str, int $box_size) : bool

Check if the number of unicode characters are not more than the specified integer.

```php
UTF8::fits_inside('κόσμε', 6); // false
```

##### fix_simple_utf8(string $str) : string

Try to fix simple broken UTF-8 strings.

INFO: Take a look at "UTF8::fix_utf8()" if you need a more advanced fix for broken UTF-8 strings.

```php
UTF8::fix_simple_utf8('DÃ¼sseldorf'); // 'Düsseldorf'
```

##### fix_utf8(string|string[] $str) : mixed

Fix a double (or multiple) encoded UTF8 string.

```php
UTF8::fix_utf8('FÃÂÂÂÂ©dÃÂÂÂÂ©ration'); // 'Fédération'
```

##### getCharDirection(string $char) : string ('RTL' or 'LTR')

Get character of a specific character.

```php
UTF8::getCharDirection('ا'); // 'RTL'
```

##### getCharDirection(string $char) : string ('RTL' or 'LTR')

Get character of a specific character.

```php
UTF8::getCharDirection('ا'); // 'RTL'
```

##### hex_to_int(string $str) : int|false

Converts hexadecimal U+xxxx code point representation to integer.

INFO: opposite to UTF8::int_to_hex()

```php
UTF8::hex_to_int('U+00f1'); // 241
```

##### html_encode(string $str, bool $keepAsciiChars = false, string $encoding = 'UTF-8') : string

Converts a UTF-8 string to a series of HTML numbered entities.

INFO: opposite to UTF8::html_decode()

```php
UTF8::html_encode('中文空白'); // '&#20013;&#25991;&#31354;&#30333;'
```

##### html_entity_decode(string $str, int $flags = null, string $encoding = 'UTF-8') : string

UTF-8 version of html_entity_decode()

The reason we are not using html_entity_decode() by itself is because
while it is not technically correct to leave out the semicolon
at the end of an entity most browsers will still interpret the entity
correctly. html_entity_decode() does not convert entities without
semicolons, so we are left with our own little solution here. Bummer.

Convert all HTML entities to their applicable characters

INFO: opposite to UTF8::html_encode()

```php
UTF8::html_encode('&#20013;&#25991;&#31354;&#30333;'); // '中文空白' 
```

##### htmlentities(string $str, int $flags = ENT_COMPAT, string $encoding = 'UTF-8', bool $double_encode = true) : string

Convert all applicable characters to HTML entities: UTF-8 version of htmlentities()

```php
UTF8::htmlentities('<白-öäü>'); // '&lt;&#30333;-&ouml;&auml;&uuml;&gt;'
```

##### htmlspecialchars(string $str, int $flags = ENT_COMPAT, string $encoding = 'UTF-8', bool $double_encode = true) : string

Convert only special characters to HTML entities: UTF-8 version of htmlspecialchars()

INFO: Take a look at "UTF8::htmlentities()"

```php
UTF8::htmlspecialchars('<白-öäü>'); // '&lt;白-öäü&gt;'
```

##### int_to_hex(int $int, string $pfix = 'U+') : str

Converts Integer to hexadecimal U+xxxx code point representation.

INFO: opposite to UTF8::hex_to_int()

```php
UTF8::int_to_hex(241); // 'U+00f1'
```

##### is_ascii(string $str) : bool

Checks if a string is 7 bit ASCII.

alias: UTF8::isAscii()

```php
UTF8::is_ascii('白'); // false
```

##### is_base64(string $str) : bool

Returns true if the string is base64 encoded, false otherwise.

alias: UTF8::isBase64()

```php
UTF8::is_base64('4KSu4KWL4KSo4KS/4KSa'); // true
```

##### is_binary(mixed $input) : bool

Check if the input is binary... (is look like a hack).

alias: UTF8::isBinary()

```php
UTF8::is_binary(01); // true
```

##### is_binary_file(string $file) : bool

Check if the file is binary.

```php
UTF8::is_binary('./utf32.txt'); // true
```

##### is_bom(string $str) : bool

Checks if the given string is equal to any "Byte Order Mark".

WARNING: Use "UTF8::string_has_bom()" if you will check BOM in a string.

alias: UTF8::isBom()

```php
UTF8::is_bom("\xef\xbb\xbf"); // true
```

##### is_json(string $str) : bool

Try to check if "$str" is an json-string.

alias: UTF8::isJson()

```php
UTF8::is_json('{"array":[1,"¥","ä"]}'); // true
```

##### is_html(string $str) : bool

Check if the string contains any html-tags <lall>.

alias: UTF8::isHtml()

```php
UTF8::is_html('<b>lall</b>'); // true
```

##### is_utf16(string $str) : int|false

Check if the string is UTF-16: This function will return  false if is't not UTF-16, 1 for UTF-16LE, 2 for UTF-16BE.

alias: UTF8::isUtf16()

```php
UTF8::is_utf16(file_get_contents('utf-16-le.txt')); // 1
UTF8::is_utf16(file_get_contents('utf-16-be.txt')); // 2
UTF8::is_utf16(file_get_contents('utf-8.txt')); // false
```

##### is_utf32(string $str) : int|false

Check if the string is UTF-32: This function will return  false if is't not UTF-32, 1 for UTF-32LE, 2 for UTF-32BE.

alias: UTF8::isUtf16()

```php
UTF8::is_utf32(file_get_contents('utf-32-le.txt')); // 1
UTF8::is_utf32(file_get_contents('utf-32-be.txt')); // 2
UTF8::is_utf32(file_get_contents('utf-8.txt')); // false
```

##### is_utf8(string $str, bool $strict = false) : bool

Checks whether the passed string contains only byte sequences that appear valid UTF-8 characters.

alias: UTF8::isUtf8()

```php
UTF8::is_utf8('Iñtërnâtiônàlizætiøn'); // true
UTF8::is_utf8("Iñtërnâtiônàlizætiøn\xA0\xA1"); // false
```

##### json_decode(string $json, bool $assoc = false, int $depth = 512, int $options = 0) : mixed

Decodes a JSON string.

```php
UTF8::json_decode('[1,"\u00a5","\u00e4"]'); // array(1, '¥', 'ä')
```

##### json_encode(mixed $value, int $options = 0, int $depth = 512) : string

Returns the JSON representation of a value.

```php
UTF8::json_enocde(array(1, '¥', 'ä')); // '[1,"\u00a5","\u00e4"]'
```

##### lcfirst(string $str) : string

Makes string's first char lowercase.

```php
UTF8::lcfirst('ÑTËRNÂTIÔNÀLIZÆTIØN'); // ñTËRNÂTIÔNÀLIZÆTIØN 
```

##### ltrim(string $str, string $chars = INF) : string

Strip whitespace or other characters from beginning of a UTF-8 string.

```php
UTF8::ltrim('　中文空白　 '); // '中文空白　 '
```

##### max(mixed $arg) : string

Returns the UTF-8 character with the maximum code point in the given data.

```php
UTF8::max('abc-äöü-中文空白'); // 'ø'
```

##### max_chr_width(string $str) : int

Calculates and returns the maximum number of bytes taken by any
UTF-8 encoded character in the given string.

```php
UTF8::max_chr_width('Intërnâtiônàlizætiøn'); // 2
```

##### min(mixed $arg) : string

Returns the UTF-8 character with the minimum code point in the given data.

```php
UTF8::min('abc-äöü-中文空白'); // '-'
```

##### normalize_encoding(string $encoding) : string

Normalize the encoding-"name" input.

```php
UTF8::normalize_encoding('UTF8'); // 'UTF-8'
```

##### normalize_msword(string $str) : string

Normalize some MS Word special characters.

```php
UTF8::normalize_msword('„Abcdef…”'); // '"Abcdef..."'
```

##### normalize_whitespace(string $str, bool $keepNonBreakingSpace = false, bool $keepBidiUnicodeControls = false) : string

Normalize the whitespace.

```php
UTF8::normalize_whitespace("abc-\xc2\xa0-öäü-\xe2\x80\xaf-\xE2\x80\xAC", true); // "abc-\xc2\xa0-öäü- -"
```

##### ord(string $chr) : int

Calculates Unicode code point of the given UTF-8 encoded character.

INFO: opposite to UTF8::chr()

```php
UTF8::ord('中'); // 20013
```

##### parse_str(string $str, &$result) : bool

Parses the string into an array (into the the second parameter).

WARNING: Instead of "parse_str()" this method do not (re-)placing variables in the current scope,
          if the second parameter is not set!

```php
UTF8::parse_str('Iñtërnâtiônéàlizætiøn=測試&arr[]=foo+測試&arr[]=ການທົດສອບ', $array);
echo $array['Iñtërnâtiônéàlizætiøn']; // '測試'
```

##### range(mixed $var1, mixed $var2) : array

Create an array containing a range of UTF-8 characters.

```php
UTF8::range('κ', 'ζ'); // array('κ', 'ι', 'θ', 'η', 'ζ',)
```

##### remove_bom(string $str) : string

Remove the BOM from UTF-8 / UTF-16 / UTF-32 strings.

```php
UTF8::remove_bom("\xEF\xBB\xBFΜπορώ να"); // 'Μπορώ να'
```

##### remove_duplicates(string $str, string|array $what = ' ') : string

Removes duplicate occurrences of a string in another string.

```php
UTF8::remove_duplicates('öäü-κόσμεκόσμε-äöü', 'κόσμε'); // 'öäü-κόσμε-äöü'
```

##### remove_invisible_characters(string $str, bool $url_encoded = true, string $replacement = '') : string

Remove invisible characters from a string.

```php
UTF8::remove_duplicates("κόσ\0με"); // 'κόσμε'
```

##### replace_diamond_question_mark(string $str, string $unknown = '?') : string

Replace the diamond question mark (�) with the replacement.

```php
UTF8::replace_diamond_question_mark('中文空白�'); // '中文空白'
```

##### rtrim(string $str = '', string $chars = INF) : string

Strip whitespace or other characters from end of a UTF-8 string.

```php
UTF8::rtrim('-ABC-中文空白-  '); // '-ABC-中文空白-'
```

##### single_chr_html_encode(string $char, bool $keepAsciiChars = false) : string

Converts a UTF-8 character to HTML Numbered Entity like "&#123;".

```php
UTF8::single_chr_html_encode('κ'); // '&#954;'
```

##### split(string $str, int $length = 1, bool $cleanUtf8 = false) : array

Convert a string to an array of Unicode characters.

```php
UTF8::split('中文空白'); // array('中', '文', '空', '白')
```

##### str_detect_encoding(string $str) : string

Optimized "\mb_detect_encoding()"-function -> with support for UTF-16 and UTF-32.

```php
UTF8::str_detect_encoding('中文空白'); // 'UTF-8'
UTF8::str_detect_encoding('Abc'); // 'ASCII'
```

##### str_ireplace(mixed $search, mixed $replace, mixed $subject, int &$count = null) : mixed

Case-insensitive and UTF-8 safe version of <function>str_replace</function>.

```php
UTF8::str_ireplace('lIzÆ', 'lise', array('Iñtërnâtiônàlizætiøn')); // array('Iñtërnâtiônàlisetiøn')
```

##### str_limit_after_word(string $str, int $length = 100, stirng $strAddOn = '...') : string

Limit the number of characters in a string, but also after the next word.

```php
UTF8::str_limit_after_word('fòô bàř fòô', 8, ''); // 'fòô bàř'
```

##### str_pad(string $str, int $pad_length, string $pad_string = ' ', int $pad_type = STR_PAD_RIGHT) : string

Pad a UTF-8 string to given length with another string.

```php
UTF8::str_pad('中文空白', 10, '_', STR_PAD_BOTH); // '___中文空白___'
```

##### str_pad(string $str, int $pad_length, string $pad_string = ' ', int $pad_type = STR_PAD_RIGHT) : string

Pad a UTF-8 string to given length with another string.

```php
UTF8::str_pad('中文空白', 10, '_', STR_PAD_BOTH); // '___中文空白___'
```

##### str_repeat(string $str, int $multiplier) : string

Repeat a string.

```php
UTF8::str_repeat("°~\xf0\x90\x28\xbc", 2); // '°~ð(¼°~ð(¼'
```

##### str_shuffle(string $str) : string

Shuffles all the characters in the string.

```php
UTF8::str_shuffle('fòô bàř fòô'); // 'àòôřb ffòô '
```

##### str_sort(string $str, bool $unique = false, bool $desc = false) : string

Sort all characters according to code points.

```php
UTF8::str_sort('  -ABC-中文空白-  '); // '    ---ABC中文白空'
```

##### str_split(string $str, int $len = 1) : array

Split a string into an array.

```php
UTF8::split('déjà', 2); // array('dé', 'jà')
```

##### str_to_binary(string $str) : string

Get a binary representation of a specific string.

INFO: opposite to UTF8::binary_to_str()

```php
UTF8::str_to_binary('😃'); // '11110000100111111001100010000011'
```

##### str_word_count(string $str, int $format = 0, string $charlist = '') : string

Get a binary representation of a specific string.

```php
// format: 0 -> return only word count (int)
//
UTF8::str_word_count('中文空白 öäü abc#c'); // 4
UTF8::str_word_count('中文空白 öäü abc#c', 0, '#'); // 3

// format: 1 -> return words (array) 
//
UTF8::str_word_count('中文空白 öäü abc#c', 1); // array('中文空白', 'öäü', 'abc', 'c')
UTF8::str_word_count('中文空白 öäü abc#c', 1, '#'); // array('中文空白', 'öäü', 'abc#c')

// format: 2 -> return words with offset (array) 
//
UTF8::str_word_count('中文空白 öäü ab#c', 2); // array(0 => '中文空白', 5 => 'öäü', 9 => 'abc', 13 => 'c')
UTF8::str_word_count('中文空白 öäü ab#c', 2, '#'); // array(0 => '中文空白', 5 => 'öäü', 9 => 'abc#c')
```

##### strcmp(string $str1, string $str2) : int

Case-insensitive string comparison: < 0 if str1 is less than str2; 
                                    > 0 if str1 is greater than str2, 
                                    0 if they are equal.

```php
UTF8::strcmp("iñtërnâtiôn\nàlizætiøn", "iñtërnâtiôn\nàlizætiøn"); // 0
```

##### strnatcmp(string $str1, string $str2) : int

Case sensitive string comparisons using a "natural order" algorithm: < 0 if str1 is less than str2; 
                                                                     > 0 if str1 is greater than str2, 
                                                                     0 if they are equal.

INFO: natural order version of UTF8::strcmp()

```php
UTF8::strnatcmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!'); // -1
UTF8::strcmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!'); // 1

UTF8::strnatcmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'); // 1
UTF8::strcmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!')); // -1
```

##### strcasecmp(string $str1, string $str2) : int

Case-insensitive string comparison: < 0 if str1 is less than str2; 
                                    > 0 if str1 is greater than str2, 
                                    0 if they are equal.

INFO: Case-insensitive version of UTF8::strcmp()

```php
UTF8::strcasecmp("iñtërnâtiôn\nàlizætiøn", "Iñtërnâtiôn\nàlizætiøn"); // 0
```

##### strnatcasecmp(string $str1, string $str2) : int

Case insensitive string comparisons using a "natural order" algorithm: < 0 if str1 is less than str2; 
                                                                       > 0 if str1 is greater than str2, 
                                                                       0 if they are equal.

INFO: natural order version of UTF8::strcasecmp()

```php
UTF8::strnatcasecmp('2', '10Hello WORLD 中文空白!'); // -1
UTF8::strcasecmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!'); // 1
    
UTF8::strnatcasecmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'); // 1
UTF8::strcasecmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'); // -1
```

##### strncasecmp(string $str1, string $str2, int $len) : int

Case-insensitive string comparison of the first n characters.: 
    < 0 if str1 is less than str2; 
    > 0 if str1 is greater than str2, 
    0 if they are equal.

INFO: Case-insensitive version of UTF8::strncmp()

```php
UTF8::strcasecmp("iñtërnâtiôn\nàlizætiøn321", "iñtërnâtiôn\nàlizætiøn123", 5); // 0
```

##### strncasecmp(string $str1, string $str2, int $len) : int

Case-insensitive string comparison of the first n characters.: 
    < 0 if str1 is less than str2; 
    > 0 if str1 is greater than str2, 
    0 if they are equal.

INFO: Case-insensitive version of UTF8::strncmp()

```php
UTF8::strcasecmp("iñtërnâtiôn\nàlizætiøn321", "Iñtërnâtiôn\nàlizætiøn123", 5); // 0
```

##### strncmp(string $str1, string $str2, int $len) : int

Case-sensitive string comparison of the first n characters.: 
    < 0 if str1 is less than str2; 
    > 0 if str1 is greater than str2, 
    0 if they are equal.

```php
UTF8::strncmp("Iñtërnâtiôn\nàlizætiøn321", "Iñtërnâtiôn\nàlizætiøn123", 5); // 0
```

##### string(string $str1, string $str2) : int

Create a UTF-8 string from code points.

INFO: opposite to UTF8::codepoints()

```php
UTF8::string(array(246, 228, 252)); // 'öäü'
```

##### string_has_bom(string $str) : bool

Checks if string starts with "BOM" (Byte Order Mark Character) character.

alias: UTF8::hasBom()

```php
UTF8::string_has_bom("\xef\xbb\xbf foobar"); // true
```

##### strip_tags(string $str, sting|null $allowable_tags = null) : string

Strip HTML and PHP tags from a string + clean invalid UTF-8.

```php
UTF8::strip_tags("<span>κόσμε\xa0\xa1</span>"); // 'κόσμε'
```

##### strlen(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false) : int

Get the string length, not the byte-length!

```php
UTF8::strlen("Iñtërnâtiôn\xE9àlizætiøn")); // 20
```

##### strwidth(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false) : int

Return the width of a string.

```php
UTF8::strwidth("Iñtërnâtiôn\xE9àlizætiøn")); // 21
```

##### strpbrk(string $haystack, string $char_list) : string

Search a string for any of a set of characters.

```php
UTF8::strpbrk('-中文空白-', '白'); // '白-'
```

##### strpos(string $haystack, string $char_list) : int|false

Find position of first occurrence of string in a string.

```php
UTF8::strpos('ABC-ÖÄÜ-中文空白-中文空白', '中'); // 8
```

##### stripos($str, $needle, $before_needle = false) : int|false

Finds position of first occurrence of a string within another, case insensitive.

```php
UTF8::strpos('ABC-ÖÄÜ-中文空白-中文空白', '中'); // 8
```

##### strrpos(string $haystack, string $needle, int $offset = 0, bool $cleanUtf8 = false) : string|false

Find position of last occurrence of a string in a string.

```php
UTF8::strrpos('ABC-ÖÄÜ-中文空白-中文空白', '中'); // 13
```

##### strripos(string $haystack, string $needle, int $offset = 0, bool $cleanUtf8 = false) : string|false

Find position of last occurrence of a case-insensitive string.

```php
UTF8::strripos('ABC-ÖÄÜ-中文空白-中文空白', '中'); // 13
```

##### strrchr(string $haystack, string $needle, bool $part = false, string $encoding) : string|false

Finds the last occurrence of a character in a string within another.

```php
UTF8::strrchr('κόσμεκόσμε-äöü', 'κόσμε'); // 'κόσμε-äöü'
```

##### strrichr(string $haystack, string $needle, bool $part = false, string $encoding) : string|false

Finds the last occurrence of a character in a string within another, case insensitive.

```php
UTF8::strrichr('Aκόσμεκόσμε-äöü', 'aκόσμε'); // 'Aκόσμεκόσμε-äöü'
```

##### strrev(string $str) : string

Reverses characters order in the string.

```php
UTF8::strrev('κ-öäü'); // 'üäö-κ'
```

##### strspn(string $str, string $mask, int $offset = 0, int $length = 2147483647) : string

Finds the length of the initial segment of a string consisting entirely of characters contained within a given mask.

```php
UTF8::strspn('iñtërnâtiônàlizætiøn', 'itñ'); // '3'
```

##### strstr(string $str, string $needle, bool $before_needle = false) : string

Returns part of haystack string from the first occurrence of needle to the end of haystack.

```php
$str = 'iñtërnâtiônàlizætiøn';
$search = 'nât';

UTF8::strstr($str, $search)); // 'nâtiônàlizætiøn'
UTF8::strstr($str, $search, true)); // 'iñtër'
```

##### stristr(string $str, string $needle, bool $before_needle = false) : string

Returns all of haystack starting from and including the first occurrence of needle to the end.

```php
$str = 'iñtërnâtiônàlizætiøn';
$search = 'NÂT';

UTF8::stristr($str, $search)); // 'nâtiônàlizætiøn'
UTF8::stristr($str, $search, true)); // 'iñtër'
```

##### strtocasefold(string $str, bool $full = true) : string

Unicode transformation for case-less matching.

```php
UTF8::strtocasefold('ǰ◌̱'); // 'ǰ◌̱'
```

##### strtolower(string $str, string $encoding = 'UTF-8') : string

Make a string lowercase.

```php
UTF8::strtolower('DÉJÀ Σσς Iıİi'); // 'déjà σσς iıii'
```

##### strtoupper(string $str, string $encoding = 'UTF-8') : string

Make a string uppercase.

```php
UTF8::strtoupper('Déjà Σσς Iıİi'); // 'DÉJÀ ΣΣΣ IIİI'
```

##### strtr(string $str, string|array $from, string|array $to = INF) : string

Translate characters or replace sub-strings.

```php
$arr = array(
    'Hello'   => '○●◎',
    '中文空白' => 'earth',
);
UTF8::strtr('Hello 中文空白', $arr); // '○●◎ earth'
```


... TODO ...