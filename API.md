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

##### chr()

Generates a UTF-8 encoded character from the given code point.

```php
UTF8::chr(666); // 'ʚ'
```

##### chr_map()

Applies callback to all characters of a string.

```php
UTF8::chr_map(['voku\helper\UTF8', 'strtolower'], 'Κόσμε'); // ['κ','ό', 'σ', 'μ', 'ε']
```

##### chr_size_list()

Generates a UTF-8 encoded character from the given code point.

 1 byte => U+0000  - U+007F
 2 byte => U+0080  - U+07FF
 3 byte => U+0800  - U+FFFF
 4 byte => U+10000 - U+10FFFF

```php
UTF8::chr_size_list('中文空白-test'); // [3, 3, 3, 3, 1, 1, 1, 1, 1]
```

##### chr_to_decimal()

Get a decimal code representation of a specific character.

```php
UTF8::chr_to_decimal('§'); // 0xa7
```

##### chr_to_hex()

Get hexadecimal code point (U+xxxx) of a UTF-8 encoded character.

```php
UTF8::chr_to_hex('§'); // 0xa7
```

... TODO