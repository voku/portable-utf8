# Portable UTF-8 | API

The API from the "UTF8"-Class is written as small static methods that will match the default PHP-API e.g.


## Methods

##### access(string $str, int $pos)

Return the character at the specified position: $str[1] like functionality.

```php
UTF8::access('fòô', 3); // 'ô'
```

##### add_bom_to_string(string $str)

Prepends UTF-8 BOM character to the string and returns the whole string.

If BOM already existed there, the Input string is returned.

```php
UTF8::add_bom_to_string('fòô'); // "\xEF\xBB\xBF" . 'fòô'
```

##### bom()

Returns the UTF-8 Byte Order Mark Character.

```php
UTF8::bom(); // "\xEF\xBB\xBF"
```

##### chr(int $code_point) : string

Generates a UTF-8 encoded character from the given code point.

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

```php
UTF8::codepoints('κöñ'); // array(954, 246, 241)
// ... OR ...
UTF8::codepoints('κöñ', true); // array('U+03ba', 'U+00f6', 'U+00f1')
```

##### count_chars(string $str) : array

Returns count of characters used in a string.

```php
UTF8::count_chars('κaκbκc'); // array('κ' => 3, 'a' => 1, 'b' => 1, 'c' => 1)
```

... TODO