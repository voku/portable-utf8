[![Build Status](https://travis-ci.org/voku/portable-utf8.svg?branch=master)](https://travis-ci.org/voku/portable-utf8)
[![Build status](https://ci.appveyor.com/api/projects/status/gnejjnk7qplr7f5t/branch/master?svg=true)](https://ci.appveyor.com/project/voku/portable-utf8/branch/master)
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fvoku%2Fportable-utf8.svg?type=shield)](https://app.fossa.io/projects/git%2Bgithub.com%2Fvoku%2Fportable-utf8?ref=badge_shield)
[![Coverage Status](https://coveralls.io/repos/voku/portable-utf8/badge.svg?branch=master&service=github)](https://coveralls.io/github/voku/portable-utf8?branch=master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/997c9bb10d1c4791967bdf2e42013e8e)](https://www.codacy.com/app/voku/portable-utf8)
[![Latest Stable Version](https://poser.pugx.org/voku/portable-utf8/v/stable)](https://packagist.org/packages/voku/portable-utf8) 
[![Total Downloads](https://poser.pugx.org/voku/portable-utf8/downloads)](https://packagist.org/packages/voku/portable-utf8)
[![License](https://poser.pugx.org/voku/portable-utf8/license)](https://packagist.org/packages/voku/portable-utf8)
[![Donate to this project using PayPal](https://img.shields.io/badge/paypal-donate-yellow.svg)](https://www.paypal.me/moelleken)
[![Donate to this project using Patreon](https://img.shields.io/badge/patreon-donate-yellow.svg)](https://www.patreon.com/voku)

# 🉑 Portable UTF-8

## Description

It is written in PHP (PHP 7+) and can work without "mbstring", "iconv" or any other extra encoding php-extension on your server. 

The benefit of Portable UTF-8 is that it is easy to use, easy to bundle. This library will also 
auto-detect your server environment and will use the installed php-extensions if they are available, 
so you will have the best possible performance.

As a fallback we will use Symfony Polyfills, if needed. (https://github.com/symfony/polyfill)

The project based on ...
+ Hamid Sarfraz's work - [portable-utf8](http://pageconfig.com/attachments/portable-utf8.php) 
+ Nicolas Grekas's work - [tchwork/utf8](https://github.com/tchwork/utf8) 
+ Behat's work - [Behat/Transliterator](https://github.com/Behat/Transliterator) 
+ Sebastián Grignoli's work - [neitanod/forceutf8](https://github.com/neitanod/forceutf8) 
+ Ivan Enderlin's work - [hoaproject/Ustring](https://github.com/hoaproject/Ustring)
+ and many cherry-picks from "GitHub"-gists and "Stack Overflow"-snippets ...

## Demo

Here you can test some basic functions from this library and you can compare some results with the native php function results.

+ [encoder.suckup.de](https://encoder.suckup.de/)

## Index

* [Alternative](#alternative)
* [Install](#install-portable-utf-8-via-composer-require)
* [Why Portable UTF-8?](#why-portable-utf-8)
* [Requirements and Recommendations](#requirements-and-recommendations)
* [Warning](#warning)
* [Usage](#usage)
* [Class methods](#class-methods)
* [Unit Test](#unit-test)
* [License and Copyright](#license-and-copyright)

## Alternative

If you like a more Object Oriented Way to edit strings, then you can take a look at [voku/Stringy](https://github.com/voku/Stringy), it's a fork of "danielstjules/Stringy" but it used the "Portable UTF-8"-Class and some extra methods. 

```php
// Standard library
strtoupper('fòôbàř');       // 'FòôBàř'
strlen('fòôbàř');           // 10

// mbstring 
// WARNING: if you don't use a polyfill like "Portable UTF-8", you need to install the php-extension "mbstring" on your server
mb_strtoupper('fòôbàř');    // 'FÒÔBÀŘ'
mb_strlen('fòôbàř');        // '6'

// Portable UTF-8
use voku\helper\UTF8;
UTF8::strtoupper('fòôbàř');    // 'FÒÔBÀŘ'
UTF8::strlen('fòôbàř');        // '6'

// voku/Stringy
use Stringy\Stringy as S;
$stringy = S::create('fòôbàř');
$stringy->toUpperCase();    // 'FÒÔBÀŘ'
$stringy->length();         // '6'
```


## Install "Portable UTF-8" via "composer require"
```shell
composer require voku/portable-utf8
```

If your project do not need some of the Symfony polyfills please use the `replace` section of your `composer.json`. 
This removes any overhead from these polyfills as they are no longer part of your project. e.g.:
```json
{
  "replace": {
    "symfony/polyfill-php72": "1.99",
    "symfony/polyfill-iconv": "1.99",
    "symfony/polyfill-intl-grapheme": "1.99",
    "symfony/polyfill-intl-normalizer": "1.99",
    "symfony/polyfill-mbstring": "1.99"
  }
}
```

##  Why Portable UTF-8?[]()
PHP 5 and earlier versions have no native Unicode support. To bridge the gap, there exist several extensions like "mbstring", "iconv" and "intl".

The problem with "mbstring" and others is that most of the time you cannot ensure presence of a specific one on a server. If you rely on one of these, your application is no more portable. This problem gets even severe for open source applications that have to run on different servers with different configurations. Considering these, I decided to write a library:

## Requirements and Recommendations

*   No extensions are required to run this library. Portable UTF-8 only needs PCRE library that is available by default since PHP 4.2.0 and cannot be disabled since PHP 5.3.0. "\u" modifier support in PCRE for UTF-8 handling is not a must.
*   PHP 5.3 is the minimum requirement, and all later versions are fine with Portable UTF-8.
*   PHP 7.0 is the minimum requirement since version 4.0 of Portable UTF-8, otherwise composer will install an older version
*   To speed up string handling, it is recommended that you have "mbstring" or "iconv" available on your server, as well as the latest version of PCRE library
*   Although Portable UTF-8 is easy to use; moving from native API to Portable UTF-8 may not be straight-forward for everyone. It is highly recommended that you do not update your scripts to include Portable UTF-8 or replace or change anything before you first know the reason and consequences. Most of the time, some native function may be all what you need.
*   There is also a shim for "mbstring", "iconv" and "intl", so you can use it also on shared webspace. 

## Info

Since version 5.4.26 this library will NOT force "UTF-8" by "bootstrap.php" anymore.
If you need to enable this behavior you can define "PORTABLE_UTF8__ENABLE_AUTO_FILTER", before requiring the autoloader.

```php
define('PORTABLE_UTF8__ENABLE_AUTO_FILTER', 1);
```

Before version 5.4.26 this behavior was enabled by default and you could disable it via "PORTABLE_UTF8__DISABLE_AUTO_FILTER",
but the code had potential security vulnerabilities via injecting code while redirecting via ```header('Location ...```.
This is the reason I decided to add this BC in a bug fix release, so that everybody using the current version will receive the security-fix.

## Usage

Example 1: UTF8::cleanup()
```php
  echo UTF8::cleanup('�DÃ¼sseldorf�');
  
  // will output:
  // Düsseldorf
```

Example 2: UTF8::strlen()
```php
  $string = 'string <strong>with utf-8 chars åèä</strong> - doo-bee doo-bee dooh';

  echo strlen($string) . "\n<br />";
  echo UTF8::strlen($string) . "\n<br />";

  // will output:
  // 70
  // 67

  $string_test1 = strip_tags($string);
  $string_test2 = UTF8::strip_tags($string);

  echo strlen($string_test1) . "\n<br />";
  echo UTF8::strlen($string_test2) . "\n<br />";

  // will output:
  // 53
  // 50
```

Example 3: UTF8::fix_utf8()
```php

  echo UTF8::fix_utf8('DÃ¼sseldorf');
  echo UTF8::fix_utf8('Ã¤');
  
  // will output:
  // Düsseldorf
  // ä
```

# Portable UTF-8 | API

The API from the "UTF8"-Class is written as small static methods that will match the default PHP-API.


## Class methods


<table>
    <tr><td><a href="#accessstring-str-int-pos-string-encoding-string">access</a>
</td><td><a href="#add_bom_to_stringstring-str-string">add_bom_to_string</a>
</td><td><a href="#array_change_key_casearray-array-int-case-string-encoding-string">array_change_key_case</a>
</td><td><a href="#betweenstring-str-string-start-string-end-int-offset-string-encoding-string">between</a>
</td></tr><tr><td><a href="#binary_to_strmixed-bin-mixed">binary_to_str</a>
</td><td><a href="#bom-mixed">bom</a>
</td><td><a href="#callbackcallable-callback-string-str-string">callback</a>
</td><td><a href="#char_atstring-str-int-index-string-encoding-string">char_at</a>
</td></tr><tr><td><a href="#charsstring-str-string">chars</a>
</td><td><a href="#checkforsupport-string">checkForSupport</a>
</td><td><a href="#chrintstring-code_point-string-encoding-string">chr</a>
</td><td><a href="#chr_mapcallable-callback-string-str-string">chr_map</a>
</td></tr><tr><td><a href="#chr_size_liststring-str-string">chr_size_list</a>
</td><td><a href="#chr_to_decimalstring-char-string">chr_to_decimal</a>
</td><td><a href="#chr_to_hexintstring-char-string-prefix-string">chr_to_hex</a>
</td><td><a href="#chr_to_intstring-chr-string">chr_to_int</a>
</td></tr><tr><td><a href="#chunk_splitstring-body-int-chunk_length-string-end-string">chunk_split</a>
</td><td><a href="#cleanstring-str-bool-remove_bom-bool-normalize_whitespace-bool-normalize_msword-bool-keep_non_breaking_space-bool-replace_diamond_question_mark-bool-remove_invisible_characters-bool-remove_invisible_characters_url_encoded-bool">clean</a>
</td><td><a href="#cleanupstring-str-string">cleanup</a>
</td><td><a href="#codepointsstringstring--arg-bool-use_u_style-bool">codepoints</a>
</td></tr><tr><td><a href="#collapse_whitespacestring-str-string">collapse_whitespace</a>
</td><td><a href="#count_charsstring-str-bool-clean_utf8-bool-try_to_use_mb_functions-bool">count_chars</a>
</td><td><a href="#css_identifierstring-str-array-filter-array">css_identifier</a>
</td><td><a href="#css_stripe_media_queriesstring-str-string">css_stripe_media_queries</a>
</td></tr><tr><td><a href="#ctype_loaded-string">ctype_loaded</a>
</td><td><a href="#decimal_to_chrmixed-int-mixed">decimal_to_chr</a>
</td><td><a href="#decode_mimeheaderstring-str-string-encoding-string">decode_mimeheader</a>
</td><td><a href="#emoji_decodestring-str-bool-use_reversible_string_mappings-bool">emoji_decode</a>
</td></tr><tr><td><a href="#emoji_encodestring-str-bool-use_reversible_string_mappings-bool">emoji_encode</a>
</td><td><a href="#emoji_from_country_codestring-country_code_iso_3166_1-string">emoji_from_country_code</a>
</td><td><a href="#encodestring-to_encoding-string-str-bool-auto_detect_the_from_encoding-string-from_encoding-string">encode</a>
</td><td><a href="#encode_mimeheaderstring-str-string-from_charset-string-to_charset-string-transfer_encoding-string-linefeed-int-indent-int">encode_mimeheader</a>
</td></tr><tr><td><a href="#extract_textstring-str-string-search-intnull-length-string-replacer_for_skipped_text-string-encoding-string">extract_text</a>
</td><td><a href="#file_get_contentsstring-filename-bool-use_include_path-resourcenull-context-intnull-offset-intnull-max_length-int-timeout-bool-convert_to_utf8-string-from_encoding-string">file_get_contents</a>
</td><td><a href="#file_has_bomstring-file_path-string">file_has_bom</a>
</td><td><a href="#filtermixed-var-int-normalization_form-string-leading_combining-string">filter</a>
</td></tr><tr><td><a href="#filter_inputint-type-string-variable_name-int-filter-mixed-options-mixed">filter_input</a>
</td><td><a href="#filter_input_arrayint-type-mixed-definition-bool-add_empty-bool">filter_input_array</a>
</td><td><a href="#filter_varmixed-variable-int-filter-mixed-options-mixed">filter_var</a>
</td><td><a href="#filter_var_arrayarray-data-mixed-definition-bool-add_empty-bool">filter_var_array</a>
</td></tr><tr><td><a href="#finfo_loaded-bool">finfo_loaded</a>
</td><td><a href="#first_charstring-str-int-n-string-encoding-string">first_char</a>
</td><td><a href="#fits_insidestring-str-int-box_size-int">fits_inside</a>
</td><td><a href="#fix_simple_utf8string-str-string">fix_simple_utf8</a>
</td></tr><tr><td><a href="#fix_utf8stringstring--str-stringstring">fix_utf8</a>
</td><td><a href="#getchardirectionstring-char-string">getCharDirection</a>
</td><td><a href="#getsupportinfostringnull-key-stringnull">getSupportInfo</a>
</td><td><a href="#get_file_typestring-str-array-fallback-array">get_file_type</a>
</td></tr><tr><td><a href="#get_random_stringint-length-string-possible_chars-string-encoding-string">get_random_string</a>
</td><td><a href="#get_unique_stringintstring-extra_entropy-bool-use_md5-bool">get_unique_string</a>
</td><td><a href="#hasbomstring-str-string">hasBom</a>
</td><td><a href="#has_lowercasestring-str-string">has_lowercase</a>
</td></tr><tr><td><a href="#has_uppercasestring-str-string">has_uppercase</a>
</td><td><a href="#has_whitespacestring-str-string">has_whitespace</a>
</td><td><a href="#hex_to_chrstring-hexdec-string">hex_to_chr</a>
</td><td><a href="#hex_to_intstring-hexdec-string">hex_to_int</a>
</td></tr><tr><td><a href="#html_decodestring-str-int-flags-string-encoding-string">html_decode</a>
</td><td><a href="#html_encodestring-str-bool-keep_ascii_chars-string-encoding-string">html_encode</a>
</td><td><a href="#html_entity_decodestring-str-int-flags-string-encoding-string">html_entity_decode</a>
</td><td><a href="#html_escapestring-str-string-encoding-string">html_escape</a>
</td></tr><tr><td><a href="#html_stripe_empty_tagsstring-str-string">html_stripe_empty_tags</a>
</td><td><a href="#htmlentitiesstring-str-int-flags-string-encoding-bool-double_encode-bool">htmlentities</a>
</td><td><a href="#htmlspecialcharsstring-str-int-flags-string-encoding-bool-double_encode-bool">htmlspecialchars</a>
</td><td><a href="#iconv_loaded-bool">iconv_loaded</a>
</td></tr><tr><td><a href="#int_to_chrmixed-int-mixed">int_to_chr</a>
</td><td><a href="#int_to_hexint-int-string-prefix-string">int_to_hex</a>
</td><td><a href="#intlchar_loaded-string">intlChar_loaded</a>
</td><td><a href="#intl_loaded-string">intl_loaded</a>
</td></tr><tr><td><a href="#isasciistring-str-string">isAscii</a>
</td><td><a href="#isbase64string-str-string">isBase64</a>
</td><td><a href="#isbinarymixed-str-bool-strict-bool">isBinary</a>
</td><td><a href="#isbomstring-utf8_chr-string">isBom</a>
</td></tr><tr><td><a href="#ishtmlstring-str-string">isHtml</a>
</td><td><a href="#isjsonstring-str-string">isJson</a>
</td><td><a href="#isutf16mixed-str-mixed">isUtf16</a>
</td><td><a href="#isutf32mixed-str-mixed">isUtf32</a>
</td></tr><tr><td><a href="#isutf8string-str-bool-strict-bool">isUtf8</a>
</td><td><a href="#is_alphastring-str-string">is_alpha</a>
</td><td><a href="#is_alphanumericstring-str-string">is_alphanumeric</a>
</td><td><a href="#is_asciistring-str-string">is_ascii</a>
</td></tr><tr><td><a href="#is_base64mixedstring-str-bool-empty_string_is_valid-bool">is_base64</a>
</td><td><a href="#is_binarymixed-input-bool-strict-bool">is_binary</a>
</td><td><a href="#is_binary_filestring-file-string">is_binary_file</a>
</td><td><a href="#is_blankstring-str-string">is_blank</a>
</td></tr><tr><td><a href="#is_bomstring-str-string">is_bom</a>
</td><td><a href="#is_emptymixed-str-mixed">is_empty</a>
</td><td><a href="#is_hexadecimalstring-str-string">is_hexadecimal</a>
</td><td><a href="#is_htmlstring-str-string">is_html</a>
</td></tr><tr><td><a href="#is_jsonstring-str-bool-only_array_or_object_results_are_valid-bool">is_json</a>
</td><td><a href="#is_lowercasestring-str-string">is_lowercase</a>
</td><td><a href="#is_printablestring-str-string">is_printable</a>
</td><td><a href="#is_punctuationstring-str-string">is_punctuation</a>
</td></tr><tr><td><a href="#is_serializedstring-str-string">is_serialized</a>
</td><td><a href="#is_uppercasestring-str-string">is_uppercase</a>
</td><td><a href="#is_urlstring-url-bool-disallow_localhost-bool">is_url</a>
</td><td><a href="#is_utf16mixed-str-bool-check_if_string_is_binary-bool">is_utf16</a>
</td></tr><tr><td><a href="#is_utf32mixed-str-bool-check_if_string_is_binary-bool">is_utf32</a>
</td><td><a href="#is_utf8intstringstring-null-str-bool-strict-bool">is_utf8</a>
</td><td><a href="#json_decodestring-json-bool-assoc-int-depth-int-options-int">json_decode</a>
</td><td><a href="#json_encodemixed-value-int-options-int-depth-int">json_encode</a>
</td></tr><tr><td><a href="#json_loaded-int">json_loaded</a>
</td><td><a href="#lcfirststring-str-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-bool">lcfirst</a>
</td><td><a href="#lcwordstring-str-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-bool">lcword</a>
</td><td><a href="#lcwordsstring-str-string--exceptions-string-char_list-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-bool">lcwords</a>
</td></tr><tr><td><a href="#lowercasefirststring-str-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-bool">lowerCaseFirst</a>
</td><td><a href="#ltrimstring-str-stringnull-chars-stringnull">ltrim</a>
</td><td><a href="#maxstring-string-arg-string-string">max</a>
</td><td><a href="#max_chr_widthstring-str-string">max_chr_width</a>
</td></tr><tr><td><a href="#mbstring_loaded-string">mbstring_loaded</a>
</td><td><a href="#minmixed-arg-mixed">min</a>
</td><td><a href="#normalizeencodingmixed-encoding-mixed-fallback-mixed">normalizeEncoding</a>
</td><td><a href="#normalize_encodingmixed-encoding-mixed-fallback-mixed">normalize_encoding</a>
</td></tr><tr><td><a href="#normalize_line_endingstring-str-stringstring--replacer-stringstring">normalize_line_ending</a>
</td><td><a href="#normalize_mswordstring-str-string">normalize_msword</a>
</td><td><a href="#normalize_whitespacestring-str-bool-keep_non_breaking_space-bool-keep_bidi_unicode_controls-bool">normalize_whitespace</a>
</td><td><a href="#ordstring-chr-string-encoding-string">ord</a>
</td></tr><tr><td><a href="#parse_strstring-str-array-result-bool-clean_utf8-bool">parse_str</a>
</td><td><a href="#pcre_utf8_support-bool">pcre_utf8_support</a>
</td><td><a href="#rangemixed-var1-mixed-var2-bool-use_ctype-string-encoding-floatint-step-floatint">range</a>
</td><td><a href="#rawurldecodestring-str-bool-multi_decode-bool">rawurldecode</a>
</td></tr><tr><td><a href="#regex_replacestring-str-string-pattern-string-replacement-string-options-string-delimiter-string">regex_replace</a>
</td><td><a href="#removebomstring-str-string">removeBOM</a>
</td><td><a href="#remove_bomstring-str-string">remove_bom</a>
</td><td><a href="#remove_duplicatesstring-str-stringstring--what-stringstring">remove_duplicates</a>
</td></tr><tr><td><a href="#remove_htmlstring-str-string-allowable_tags-string">remove_html</a>
</td><td><a href="#remove_html_breaksstring-str-string-replacement-string">remove_html_breaks</a>
</td><td><a href="#remove_invisible_charactersstring-str-bool-url_encoded-string-replacement-string">remove_invisible_characters</a>
</td><td><a href="#remove_leftstring-str-string-substring-string-encoding-string">remove_left</a>
</td></tr><tr><td><a href="#remove_rightstring-str-string-substring-string-encoding-string">remove_right</a>
</td><td><a href="#replacestring-str-string-search-string-replacement-bool-case_sensitive-bool">replace</a>
</td><td><a href="#replace_allstring-str-array-search-arraystring-replacement-bool-case_sensitive-bool">replace_all</a>
</td><td><a href="#replace_diamond_question_markstring-str-string-replacement_char-bool-process_invalid_utf8_chars-bool">replace_diamond_question_mark</a>
</td></tr><tr><td><a href="#rtrimstring-str-stringnull-chars-stringnull">rtrim</a>
</td><td><a href="#showsupportbool-useecho-bool">showSupport</a>
</td><td><a href="#single_chr_html_encodestring-char-bool-keep_ascii_chars-string-encoding-string">single_chr_html_encode</a>
</td><td><a href="#spaces_to_tabsstring-str-int-tab_length-int">spaces_to_tabs</a>
</td></tr><tr><td><a href="#splitintstring-str-int-length-bool-clean_utf8-bool">split</a>
</td><td><a href="#str_beginsstring-haystack-string-needle-string">str_begins</a>
</td><td><a href="#str_camelizestring-str-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-bool">str_camelize</a>
</td><td><a href="#str_capitalize_namestring-str-string">str_capitalize_name</a>
</td></tr><tr><td><a href="#str_containsstring-haystack-string-needle-bool-case_sensitive-bool">str_contains</a>
</td><td><a href="#str_contains_allstring-haystack-array-needles-bool-case_sensitive-bool">str_contains_all</a>
</td><td><a href="#str_contains_anystring-haystack-array-needles-bool-case_sensitive-bool">str_contains_any</a>
</td><td><a href="#str_dasherizestring-str-string-encoding-string">str_dasherize</a>
</td></tr><tr><td><a href="#str_delimitstring-str-string-delimiter-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-bool">str_delimit</a>
</td><td><a href="#str_detect_encodingstring-str-string">str_detect_encoding</a>
</td><td><a href="#str_endsstring-haystack-string-needle-string">str_ends</a>
</td><td><a href="#str_ends_withstring-haystack-string-needle-string">str_ends_with</a>
</td></tr><tr><td><a href="#str_ends_with_anystring-str-string--substrings-string">str_ends_with_any</a>
</td><td><a href="#str_ensure_leftstring-str-string-substring-string">str_ensure_left</a>
</td><td><a href="#str_ensure_rightstring-str-string-substring-string">str_ensure_right</a>
</td><td><a href="#str_humanizestring-str-string">str_humanize</a>
</td></tr><tr><td><a href="#str_ibeginsstring-haystack-string-needle-string">str_ibegins</a>
</td><td><a href="#str_iendsstring-haystack-string-needle-string">str_iends</a>
</td><td><a href="#str_iends_withstring-haystack-string-needle-string">str_iends_with</a>
</td><td><a href="#str_iends_with_anystring-str-string--substrings-string">str_iends_with_any</a>
</td></tr><tr><td><a href="#str_iindex_firststring-str-string-needle-int-offset-string-encoding-string">str_iindex_first</a>
</td><td><a href="#str_iindex_laststring-str-string-needle-int-offset-string-encoding-string">str_iindex_last</a>
</td><td><a href="#str_index_firststring-str-string-needle-int-offset-string-encoding-string">str_index_first</a>
</td><td><a href="#str_index_laststring-str-string-needle-int-offset-string-encoding-string">str_index_last</a>
</td></tr><tr><td><a href="#str_insertstring-str-string-substring-int-index-string-encoding-string">str_insert</a>
</td><td><a href="#str_ireplacestringstring--search-stringstring--replacement-mixed-subject-int-count-int">str_ireplace</a>
</td><td><a href="#str_ireplace_beginningstring-str-string-search-string-replacement-string">str_ireplace_beginning</a>
</td><td><a href="#str_ireplace_endingstring-str-string-search-string-replacement-string">str_ireplace_ending</a>
</td></tr><tr><td><a href="#str_istarts_withstring-haystack-string-needle-string">str_istarts_with</a>
</td><td><a href="#str_istarts_with_anystring-str-array-substrings-array">str_istarts_with_any</a>
</td><td><a href="#str_isubstr_after_first_separatorstring-str-string-separator-string-encoding-string">str_isubstr_after_first_separator</a>
</td><td><a href="#str_isubstr_after_last_separatorstring-str-string-separator-string-encoding-string">str_isubstr_after_last_separator</a>
</td></tr><tr><td><a href="#str_isubstr_before_first_separatorstring-str-string-separator-string-encoding-string">str_isubstr_before_first_separator</a>
</td><td><a href="#str_isubstr_before_last_separatorstring-str-string-separator-string-encoding-string">str_isubstr_before_last_separator</a>
</td><td><a href="#str_isubstr_firststring-str-string-needle-bool-before_needle-string-encoding-string">str_isubstr_first</a>
</td><td><a href="#str_isubstr_laststring-str-string-needle-bool-before_needle-string-encoding-string">str_isubstr_last</a>
</td></tr><tr><td><a href="#str_last_charstring-str-int-n-string-encoding-string">str_last_char</a>
</td><td><a href="#str_limitstring-str-int-length-string-str_add_on-string-encoding-string">str_limit</a>
</td><td><a href="#str_limit_after_wordstring-str-int-length-string-str_add_on-string-encoding-string">str_limit_after_word</a>
</td><td><a href="#str_longest_common_prefixstring-str1-string-str2-string-encoding-string">str_longest_common_prefix</a>
</td></tr><tr><td><a href="#str_longest_common_substringstring-str1-string-str2-string-encoding-string">str_longest_common_substring</a>
</td><td><a href="#str_longest_common_suffixstring-str1-string-str2-string-encoding-string">str_longest_common_suffix</a>
</td><td><a href="#str_matches_patternstring-str-string-pattern-string">str_matches_pattern</a>
</td><td><a href="#str_offset_existsstring-str-int-offset-string-encoding-string">str_offset_exists</a>
</td></tr><tr><td><a href="#str_offset_getstring-str-int-index-string-encoding-string">str_offset_get</a>
</td><td><a href="#str_padstring-str-int-pad_length-string-pad_string-intstring-pad_type-string-encoding-string">str_pad</a>
</td><td><a href="#str_pad_bothstring-str-int-length-string-pad_str-string-encoding-string">str_pad_both</a>
</td><td><a href="#str_pad_leftstring-str-int-length-string-pad_str-string-encoding-string">str_pad_left</a>
</td></tr><tr><td><a href="#str_pad_rightstring-str-int-length-string-pad_str-string-encoding-string">str_pad_right</a>
</td><td><a href="#str_repeatstring-str-int-multiplier-int">str_repeat</a>
</td><td><a href="#str_replacemixed-search-mixed-replace-mixed-subject-int-count-int">str_replace</a>
</td><td><a href="#str_replace_beginningstring-str-string-search-string-replacement-string">str_replace_beginning</a>
</td></tr><tr><td><a href="#str_replace_endingstring-str-string-search-string-replacement-string">str_replace_ending</a>
</td><td><a href="#str_replace_firststring-search-string-replace-string-subject-string">str_replace_first</a>
</td><td><a href="#str_replace_laststring-search-string-replace-string-subject-string">str_replace_last</a>
</td><td><a href="#str_shufflestring-str-string-encoding-string">str_shuffle</a>
</td></tr><tr><td><a href="#str_slicestring-str-int-start-int-end-string-encoding-string">str_slice</a>
</td><td><a href="#str_snakeizestring-str-string-encoding-string">str_snakeize</a>
</td><td><a href="#str_sortstring-str-bool-unique-bool-desc-bool">str_sort</a>
</td><td><a href="#str_splitintstring-input-int-length-bool-clean_utf8-bool-try_to_use_mb_functions-bool">str_split</a>
</td></tr><tr><td><a href="#str_split_arrayint-string--input-int-length-bool-clean_utf8-bool-try_to_use_mb_functions-bool">str_split_array</a>
</td><td><a href="#str_split_patternstring-str-string-pattern-int-limit-int">str_split_pattern</a>
</td><td><a href="#str_starts_withstring-haystack-string-needle-string">str_starts_with</a>
</td><td><a href="#str_starts_with_anystring-str-array-substrings-array">str_starts_with_any</a>
</td></tr><tr><td><a href="#str_substr_after_first_separatorstring-str-string-separator-string-encoding-string">str_substr_after_first_separator</a>
</td><td><a href="#str_substr_after_last_separatorstring-str-string-separator-string-encoding-string">str_substr_after_last_separator</a>
</td><td><a href="#str_substr_before_first_separatorstring-str-string-separator-string-encoding-string">str_substr_before_first_separator</a>
</td><td><a href="#str_substr_before_last_separatorstring-str-string-separator-string-encoding-string">str_substr_before_last_separator</a>
</td></tr><tr><td><a href="#str_substr_firststring-str-string-needle-bool-before_needle-string-encoding-string">str_substr_first</a>
</td><td><a href="#str_substr_laststring-str-string-needle-bool-before_needle-string-encoding-string">str_substr_last</a>
</td><td><a href="#str_surroundstring-str-string-substring-string">str_surround</a>
</td><td><a href="#str_titleizestring-str-arraystring-null-ignore-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-bool-use_trim_first-stringnull-word_define_chars-stringnull">str_titleize</a>
</td></tr><tr><td><a href="#str_titleize_for_humansstring-str-array-ignore-string-encoding-string">str_titleize_for_humans</a>
</td><td><a href="#str_to_binarystring-str-string">str_to_binary</a>
</td><td><a href="#str_to_linesstring-str-bool-remove_empty_values-intnull-remove_short_values-intnull">str_to_lines</a>
</td><td><a href="#str_to_wordsstring-str-string-char_list-bool-remove_empty_values-intnull-remove_short_values-intnull">str_to_words</a>
</td></tr><tr><td><a href="#str_transliteratestring-str-string-unknown-bool-strict-bool">str_transliterate</a>
</td><td><a href="#str_truncatestring-str-int-length-string-substring-string-encoding-string">str_truncate</a>
</td><td><a href="#str_truncate_safestring-str-int-length-string-substring-string-encoding-bool-ignore_do_not_split_words_for_one_word-bool">str_truncate_safe</a>
</td><td><a href="#str_underscoredstring-str-string">str_underscored</a>
</td></tr><tr><td><a href="#str_upper_camelizestring-str-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-bool">str_upper_camelize</a>
</td><td><a href="#str_upper_firststring-str-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-bool">str_upper_first</a>
</td><td><a href="#str_word_countstring-str-int-format-string-char_list-string">str_word_count</a>
</td><td><a href="#strcasecmpstring-str1-string-str2-string-encoding-string">strcasecmp</a>
</td></tr><tr><td><a href="#strchrstring-haystack-string-needle-bool-before_needle-string-encoding-bool-clean_utf8-bool">strchr</a>
</td><td><a href="#strcmpstring-str1-string-str2-string">strcmp</a>
</td><td><a href="#strcspnstring-str-string-char_list-int-offset-int-length-string-encoding-string">strcspn</a>
</td><td><a href="#strichrstring-haystack-string-needle-bool-before_needle-string-encoding-bool-clean_utf8-bool">strichr</a>
</td></tr><tr><td><a href="#stringarray-array-array">string</a>
</td><td><a href="#string_has_bomstring-str-string">string_has_bom</a>
</td><td><a href="#strip_tagsstring-str-string-allowable_tags-bool-clean_utf8-bool">strip_tags</a>
</td><td><a href="#strip_whitespacestring-str-string">strip_whitespace</a>
</td></tr><tr><td><a href="#striposstring-haystack-string-needle-int-offset-string-encoding-bool-clean_utf8-bool">stripos</a>
</td><td><a href="#stripos_in_bytestring-haystack-string-needle-int-offset-int">stripos_in_byte</a>
</td><td><a href="#stristrstring-haystack-string-needle-bool-before_needle-string-encoding-bool-clean_utf8-bool">stristr</a>
</td><td><a href="#strlenstring-str-string-encoding-bool-clean_utf8-bool">strlen</a>
</td></tr><tr><td><a href="#strlen_in_bytestring-str-string">strlen_in_byte</a>
</td><td><a href="#strnatcasecmpstring-str1-string-str2-string-encoding-string">strnatcasecmp</a>
</td><td><a href="#strnatcmpstring-str1-string-str2-string">strnatcmp</a>
</td><td><a href="#strncasecmpstring-str1-string-str2-int-len-string-encoding-string">strncasecmp</a>
</td></tr><tr><td><a href="#strncmpstring-str1-string-str2-int-len-string-encoding-string">strncmp</a>
</td><td><a href="#strpbrkstring-haystack-string-char_list-string">strpbrk</a>
</td><td><a href="#strposstring-haystack-intstring-needle-int-offset-string-encoding-bool-clean_utf8-bool">strpos</a>
</td><td><a href="#strpos_in_bytestring-haystack-string-needle-int-offset-int">strpos_in_byte</a>
</td></tr><tr><td><a href="#strrchrstring-haystack-string-needle-bool-before_needle-string-encoding-bool-clean_utf8-bool">strrchr</a>
</td><td><a href="#strrevstring-str-string-encoding-string">strrev</a>
</td><td><a href="#strrichrstring-haystack-string-needle-bool-before_needle-string-encoding-bool-clean_utf8-bool">strrichr</a>
</td><td><a href="#strriposstring-haystack-intstring-needle-int-offset-string-encoding-bool-clean_utf8-bool">strripos</a>
</td></tr><tr><td><a href="#strripos_in_bytestring-haystack-string-needle-int-offset-int">strripos_in_byte</a>
</td><td><a href="#strrposstring-haystack-intstring-needle-int-offset-string-encoding-bool-clean_utf8-bool">strrpos</a>
</td><td><a href="#strrpos_in_bytestring-haystack-string-needle-int-offset-int">strrpos_in_byte</a>
</td><td><a href="#strspnstring-str-string-mask-int-offset-int-length-string-encoding-string">strspn</a>
</td></tr><tr><td><a href="#strstrstring-haystack-string-needle-bool-before_needle-string-encoding-bool-clean_utf8-bool">strstr</a>
</td><td><a href="#strstr_in_bytestring-haystack-string-needle-bool-before_needle-bool">strstr_in_byte</a>
</td><td><a href="#strtocasefoldstring-str-bool-full-bool-clean_utf8-string-encoding-stringnull-lang-bool-lower-bool">strtocasefold</a>
</td><td><a href="#strtolowerstring-str-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-bool">strtolower</a>
</td></tr><tr><td><a href="#strtoupperstring-str-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-bool">strtoupper</a>
</td><td><a href="#strtrstring-str-stringstring--from-stringstring--to-stringstring">strtr</a>
</td><td><a href="#strwidthstring-str-string-encoding-bool-clean_utf8-bool">strwidth</a>
</td><td><a href="#substrstring-str-int-offset-int-length-string-encoding-bool-clean_utf8-bool">substr</a>
</td></tr><tr><td><a href="#substr_comparestring-str1-string-str2-int-offset-intnull-length-bool-case_insensitivity-string-encoding-string">substr_compare</a>
</td><td><a href="#substr_countstring-haystack-string-needle-int-offset-int-length-string-encoding-bool-clean_utf8-bool">substr_count</a>
</td><td><a href="#substr_count_in_bytestring-haystack-string-needle-int-offset-int-length-int">substr_count_in_byte</a>
</td><td><a href="#substr_count_simplestring-str-string-substring-bool-case_sensitive-string-encoding-string">substr_count_simple</a>
</td></tr><tr><td><a href="#substr_ileftstring-haystack-string-needle-string">substr_ileft</a>
</td><td><a href="#substr_in_bytestring-str-int-offset-int-length-int">substr_in_byte</a>
</td><td><a href="#substr_irightstring-haystack-string-needle-string">substr_iright</a>
</td><td><a href="#substr_leftstring-haystack-string-needle-string">substr_left</a>
</td></tr><tr><td><a href="#substr_replacestringstring--str-stringstring--replacement-intint--offset-intint-null-length-string-encoding-string">substr_replace</a>
</td><td><a href="#substr_rightstring-haystack-string-needle-string-encoding-string">substr_right</a>
</td><td><a href="#swapcasestring-str-string-encoding-bool-clean_utf8-bool">swapCase</a>
</td><td><a href="#symfony_polyfill_used-bool">symfony_polyfill_used</a>
</td></tr><tr><td><a href="#tabs_to_spacesstring-str-int-tab_length-int">tabs_to_spaces</a>
</td><td><a href="#titlecasestring-str-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-bool">titlecase</a>
</td><td><a href="#toasciistring-str-string-subst_chr-bool-strict-bool">toAscii</a>
</td><td><a href="#toiso8859stringstring--str-stringstring">toIso8859</a>
</td></tr><tr><td><a href="#tolatin1stringstring--str-stringstring">toLatin1</a>
</td><td><a href="#toutf8stringstring--str-stringstring">toUTF8</a>
</td><td><a href="#to_asciistring-str-string-unknown-bool-strict-bool">to_ascii</a>
</td><td><a href="#to_booleanmixed-str-mixed">to_boolean</a>
</td></tr><tr><td><a href="#to_filenamestring-str-bool-use_transliterate-string-fallback_char-string">to_filename</a>
</td><td><a href="#to_intstring-str-string">to_int</a>
</td><td><a href="#to_iso8859stringstring--str-stringstring">to_iso8859</a>
</td><td><a href="#to_latin1stringstring--str-stringstring">to_latin1</a>
</td></tr><tr><td><a href="#to_stringmixed-input-mixed">to_string</a>
</td><td><a href="#to_utf8stringstring--str-bool-decode_html_entity_to_utf8-bool">to_utf8</a>
</td><td><a href="#trimstring-str-stringnull-chars-stringnull">trim</a>
</td><td><a href="#ucfirststring-str-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-bool">ucfirst</a>
</td></tr><tr><td><a href="#ucwordstring-str-string-encoding-bool-clean_utf8-bool">ucword</a>
</td><td><a href="#ucwordsstring-str-string--exceptions-string-char_list-string-encoding-bool-clean_utf8-bool">ucwords</a>
</td><td><a href="#urldecodestring-str-bool-multi_decode-bool">urldecode</a>
</td><td><a href="#urldecode_fix_win1252_chars-bool">urldecode_fix_win1252_chars</a>
</td></tr><tr><td><a href="#utf8_decodestring-str-bool-keep_utf8_chars-bool">utf8_decode</a>
</td><td><a href="#utf8_encodestring-str-string">utf8_encode</a>
</td><td><a href="#utf8_fix_win1252_charsstring-str-string">utf8_fix_win1252_chars</a>
</td><td><a href="#whitespace_table-string">whitespace_table</a>
</td></tr><tr><td><a href="#words_limitstring-str-int-limit-string-str_add_on-string">words_limit</a>
</td><td><a href="#wordwrapstring-str-int-width-string-break-bool-cut-bool">wordwrap</a>
</td><td><a href="#wordwrap_per_linestring-str-int-width-string-break-bool-cut-bool-add_final_break-stringnull-delimiter-stringnull">wordwrap_per_line</a>
</td><td><a href="#ws-stringnull">ws</a>
</td></tr>
</table>


## access(string $str, int $pos, string $encoding): string


**Parameters:**
- string $str <p>A UTF-8 string.</p>
- int $pos <p>The position of character to return.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string <p>Single multi-byte character.</p>
--------
## add_bom_to_string(string $str): string
INFO: If BOM already existed there, the Input string is returned.

**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- string <p>The output string that contains BOM.</p>
--------
## array_change_key_case(array<string,mixed> $array, int $case, string $encoding): string


**Parameters:**
- array<string,mixed> $array <p>The array to work on</p>
- int $case [optional] <p> Either <strong>CASE_UPPER</strong><br>
or <strong>CASE_LOWER</strong> (default)</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string[] <p>An array with its keys lower- or uppercased.</p>
--------
## between(string $str, string $start, string $end, int $offset, string $encoding): string


**Parameters:**
- string $str 
- string $start <p>Delimiter marking the start of the substring.</p>
- string $end <p>Delimiter marking the end of the substring.</p>
- int $offset [optional] <p>Index from which to begin the search. Default: 0</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string 
--------
## binary_to_str(mixed $bin): mixed


**Parameters:**
- mixed $bin 1|0

**Return:**
- string 
--------
## bom(): mixed
INFO: take a look at UTF8::$bom for e.g. UTF-16 and UTF-32 BOM values

**Parameters:**
__nothing__

**Return:**
- string <p>UTF-8 Byte Order Mark.</p>
--------
## callback(callable $callback, string $str): string


**Parameters:**
- callable $callback 
- string $str 

**Return:**
- string[] 
--------
## char_at(string $str, int $index, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- int $index <p>Position of the character.</p>
- string $encoding [optional] <p>Default is UTF-8</p>

**Return:**
- string <p>The character at $index.</p>
--------
## chars(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- string[] <p>An array of chars.</p>
--------
## checkForSupport(): string


**Parameters:**
__nothing__

**Return:**
- bool|null 
--------
## chr(int|string $code_point, string $encoding): string
INFO: opposite to UTF8::ord()

**Parameters:**
- int|string $code_point <p>The code point for which to generate a character.</p>
- string $encoding [optional] <p>Default is UTF-8</p>

**Return:**
- string|null <p>Multi-byte character, returns null on failure or empty input.</p>
--------
## chr_map(callable $callback, string $str): string


**Parameters:**
- callable $callback <p>The callback function.</p>
- string $str <p>UTF-8 string to run callback on.</p>

**Return:**
- string[] <p>The outcome of the callback, as array.</p>
--------
## chr_size_list(string $str): string
1 byte => U+0000  - U+007F
2 byte => U+0080  - U+07FF
3 byte => U+0800  - U+FFFF
4 byte => U+10000 - U+10FFFF

**Parameters:**
- string $str <p>The original unicode string.</p>

**Return:**
- int[] <p>An array of byte lengths of each character.</p>
--------
## chr_to_decimal(string $char): string


**Parameters:**
- string $char <p>The input character.</p>

**Return:**
- int 
--------
## chr_to_hex(int|string $char, string $prefix): string


**Parameters:**
- int|string $char <p>The input character</p>
- string $prefix [optional]

**Return:**
- string <p>The code point encoded as U+xxxx.</p>
--------
## chr_to_int(string $chr): string


**Parameters:**
- string $chr 

**Return:**
- int 
--------
## chunk_split(string $body, int $chunk_length, string $end): string


**Parameters:**
- string $body <p>The original string to be split.</p>
- int $chunk_length [optional] <p>The maximum character length of a chunk.</p>
- string $end [optional] <p>The character(s) to be inserted at the end of each chunk.</p>

**Return:**
- string <p>The chunked string.</p>
--------
## clean(string $str, bool $remove_bom, bool $normalize_whitespace, bool $normalize_msword, bool $keep_non_breaking_space, bool $replace_diamond_question_mark, bool $remove_invisible_characters, bool $remove_invisible_characters_url_encoded): bool


**Parameters:**
- string $str <p>The string to be sanitized.</p>
- bool $remove_bom [optional] <p>Set to true, if you need to remove
UTF-BOM.</p>
- bool $normalize_whitespace [optional] <p>Set to true, if you need to normalize the
whitespace.</p>
- bool $normalize_msword [optional] <p>Set to true, if you need to normalize MS
Word chars e.g.: "…"
=> "..."</p>
- bool $keep_non_breaking_space [optional] <p>Set to true, to keep non-breaking-spaces,
in
combination with
$normalize_whitespace</p>
- bool $replace_diamond_question_mark [optional] <p>Set to true, if you need to remove diamond
question mark e.g.: "�"</p>
- bool $remove_invisible_characters [optional] <p>Set to false, if you not want to remove
invisible characters e.g.: "\0"</p>
- bool $remove_invisible_characters_url_encoded [optional] <p>Set to true, if you not want to remove
invisible url encoded characters e.g.: "%0B"<br> WARNING:
maybe contains false-positives e.g. aa%0Baa -> aaaa.
</p>

**Return:**
- string <p>An clean UTF-8 encoded string.</p>
--------
## cleanup(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- string 
--------
## codepoints(string|string[] $arg, bool $use_u_style): bool
INFO: opposite to UTF8::string()

**Parameters:**
- string|string[] $arg <p>A UTF-8 encoded string or an array of such strings.</p>
- bool $use_u_style <p>If True, will return code points in U+xxxx format,
default, code points will be returned as integers.</p>

**Return:**
- (int|string)[] <p>
The array of code points:<br>
array<int> for $u_style === false<br>
array<string> for $u_style === true<br>
</p>
--------
## collapse_whitespace(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- string <p>A string with trimmed $str and condensed whitespace.</p>
--------
## count_chars(string $str, bool $clean_utf8, bool $try_to_use_mb_functions): bool


**Parameters:**
- string $str <p>The input string.</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
- bool $try_to_use_mb_functions [optional] <p>Set to false, if you don't want to use

**Return:**
- int[] <p>An associative array of Character as keys and
their count as values.</p>
--------
## css_identifier(string $str, array<string,string> $filter): array<string,string>


**Parameters:**
- string $str <p>INFO: if no identifier is given e.g. " " or "", we will create a unique string automatically</p>
- array<string,string> $filter 

**Return:**
- string 
--------
## css_stripe_media_queries(string $str): string


**Parameters:**
- string $str 

**Return:**
- string 
--------
## ctype_loaded(): string


**Parameters:**
__nothing__

**Return:**
- bool <strong>true</strong> if available, <strong>false</strong> otherwise
--------
## decimal_to_chr(mixed $int): mixed


**Parameters:**
- mixed $int 

**Return:**
- string 
--------
## decode_mimeheader(string $str, string $encoding): string


**Parameters:**
- string $str 
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- bool|string <p>A decoded MIME field on success,
or false if an error occurs during the decoding.</p>
--------
## emoji_decode(string $str, bool $use_reversible_string_mappings): bool


**Parameters:**
- string $str <p>The input string.</p>
- bool $use_reversible_string_mappings [optional] <p>
When <b>TRUE</b>, we se a reversible string mapping
between "emoji_encode" and "emoji_decode".</p>

**Return:**
- string 
--------
## emoji_encode(string $str, bool $use_reversible_string_mappings): bool


**Parameters:**
- string $str <p>The input string</p>
- bool $use_reversible_string_mappings [optional] <p>
when <b>TRUE</b>, we se a reversible string mapping
between "emoji_encode" and "emoji_decode"</p>

**Return:**
- string 
--------
## emoji_from_country_code(string $country_code_iso_3166_1): string


**Parameters:**
- string $country_code_iso_3166_1 <p>e.g. DE</p>

**Return:**
- string <p>Emoji or empty string on error.</p>
--------
## encode(string $to_encoding, string $str, bool $auto_detect_the_from_encoding, string $from_encoding): string
INFO:  This function will also try to fix broken / double encoding,
so you can call this function also on a UTF-8 string and you don't mess up the string.

**Parameters:**
- string $to_encoding <p>e.g. 'UTF-16', 'UTF-8', 'ISO-8859-1', etc.</p>
- string $str <p>The input string</p>
- bool $auto_detect_the_from_encoding [optional] <p>Force the new encoding (we try to fix broken / double
encoding for UTF-8)<br> otherwise we auto-detect the current
string-encoding</p>
- string $from_encoding [optional] <p>e.g. 'UTF-16', 'UTF-8', 'ISO-8859-1', etc.<br>
A empty string will trigger the autodetect anyway.</p>

**Return:**
- string 
--------
## encode_mimeheader(string $str, string $from_charset, string $to_charset, string $transfer_encoding, string $linefeed, int $indent): int


**Parameters:**
- string $str 
- string $from_charset [optional] <p>Set the input charset.</p>
- string $to_charset [optional] <p>Set the output charset.</p>
- string $transfer_encoding [optional] <p>Set the transfer encoding.</p>
- string $linefeed [optional] <p>Set the used linefeed.</p>
- int $indent [optional] <p>Set the max length indent.</p>

**Return:**
- bool|string <p>An encoded MIME field on success,
or false if an error occurs during the encoding.</p>
--------
## extract_text(string $str, string $search, int|null $length, string $replacer_for_skipped_text, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $search <p>The searched string.</p>
- int|null $length [optional] <p>Default: null === text->length / 2</p>
- string $replacer_for_skipped_text [optional] <p>Default: …</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string 
--------
## file_get_contents(string $filename, bool $use_include_path, resource|null $context, int|null $offset, int|null $max_length, int $timeout, bool $convert_to_utf8, string $from_encoding): string
WARNING: Do not use UTF-8 Option ($convert_to_utf8) for binary files (e.g.: images) !!!

**Parameters:**
- string $filename <p>
Name of the file to read.
</p>
- bool $use_include_path [optional] <p>
Prior to PHP 5, this parameter is called
use_include_path and is a bool.
As of PHP 5 the FILE_USE_INCLUDE_PATH can be used
to trigger include path
search.
</p>
- resource|null $context [optional] <p>
A valid context resource created with
stream_context_create. If you don't need to use a
custom context, you can skip this parameter by &null;.
</p>
- int|null $offset [optional] <p>
The offset where the reading starts.
</p>
- int|null $max_length [optional] <p>
Maximum length of data read. The default is to read until end
of file is reached.
</p>
- int $timeout <p>The time in seconds for the timeout.</p>
- bool $convert_to_utf8 <strong>WARNING!!!</strong> <p>Maybe you can't use this option for
some files, because they used non default utf-8 chars. Binary files
like images or pdf will not be converted.</p>
- string $from_encoding [optional] <p>e.g. 'UTF-16', 'UTF-8', 'ISO-8859-1', etc.<br>
A empty string will trigger the autodetect anyway.</p>

**Return:**
- bool|string <p>The function returns the read data as string or <b>false</b> on failure.</p>
--------
## file_has_bom(string $file_path): string


**Parameters:**
- string $file_path <p>Path to a valid file.</p>

**Return:**
- bool <p><strong>true</strong> if the file has BOM at the start, <strong>false</strong> otherwise</p>
--------
## filter(mixed $var, int $normalization_form, string $leading_combining): string


**Parameters:**
- mixed $var 
- int $normalization_form 
- string $leading_combining 

**Return:**
- mixed 
--------
## filter_input(int $type, string $variable_name, int $filter, mixed $options): mixed
Gets a specific external variable by name and optionally filters it

**Parameters:**
- int $type <p>
One of <b>INPUT_GET</b>, <b>INPUT_POST</b>,
<b>INPUT_COOKIE</b>, <b>INPUT_SERVER</b>, or
<b>INPUT_ENV</b>.
</p>
- string $variable_name <p>
Name of a variable to get.
</p>
- int $filter [optional] <p>
The ID of the filter to apply. The
manual page lists the available filters.
</p>
- mixed $options [optional] <p>
Associative array of options or bitwise disjunction of flags. If filter
accepts options, flags can be provided in "flags" field of array.
</p>

**Return:**
- mixed <p>
Value of the requested variable on success, <b>FALSE</b> if the filter fails, or <b>NULL</b> if the
<i>variable_name</i> variable is not set. If the flag <b>FILTER_NULL_ON_FAILURE</b> is used, it
returns <b>FALSE</b> if the variable is not set and <b>NULL</b> if the filter fails.
</p>
--------
## filter_input_array(int $type, mixed $definition, bool $add_empty): bool
Gets external variables and optionally filters them

**Parameters:**
- int $type <p>
One of <b>INPUT_GET</b>, <b>INPUT_POST</b>,
<b>INPUT_COOKIE</b>, <b>INPUT_SERVER</b>, or
<b>INPUT_ENV</b>.
</p>
- mixed $definition [optional] <p>
An array defining the arguments. A valid key is a string
containing a variable name and a valid value is either a filter type, or an array
optionally specifying the filter, flags and options. If the value is an
array, valid keys are filter which specifies the
filter type,
flags which specifies any flags that apply to the
filter, and options which specifies any options that
apply to the filter. See the example below for a better understanding.
</p>
<p>
This parameter can be also an integer holding a filter constant. Then all values in the
input array are filtered by this filter.
</p>
- bool $add_empty [optional] <p>
Add missing keys as <b>NULL</b> to the return value.
</p>

**Return:**
- mixed <p>
An array containing the values of the requested variables on success, or <b>FALSE</b> on failure.
An array value will be <b>FALSE</b> if the filter fails, or <b>NULL</b> if the variable is not
set. Or if the flag <b>FILTER_NULL_ON_FAILURE</b> is used, it returns <b>FALSE</b> if the variable
is not set and <b>NULL</b> if the filter fails.
</p>
--------
## filter_var(mixed $variable, int $filter, mixed $options): mixed
Filters a variable with a specified filter

**Parameters:**
- mixed $variable <p>
Value to filter.
</p>
- int $filter [optional] <p>
The ID of the filter to apply. The
manual page lists the available filters.
</p>
- mixed $options [optional] <p>
Associative array of options or bitwise disjunction of flags. If filter
accepts options, flags can be provided in "flags" field of array. For
the "callback" filter, callable type should be passed. The
callback must accept one argument, the value to be filtered, and return
the value after filtering/sanitizing it.
</p>
<p>
<code>
// for filters that accept options, use this format
$options = array(
'options' => array(
'default' => 3, // value to return if the filter fails
// other options here
'min_range' => 0
),
'flags' => FILTER_FLAG_ALLOW_OCTAL,
);
$var = filter_var('0755', FILTER_VALIDATE_INT, $options);
// for filter that only accept flags, you can pass them directly
$var = filter_var('oops', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
// for filter that only accept flags, you can also pass as an array
$var = filter_var('oops', FILTER_VALIDATE_BOOLEAN,
array('flags' => FILTER_NULL_ON_FAILURE));
// callback validate filter
function foo($value)
{
// Expected format: Surname, GivenNames
if (strpos($value, ", ") === false) return false;
list($surname, $givennames) = explode(", ", $value, 2);
$empty = (empty($surname) || empty($givennames));
$notstrings = (!is_string($surname) || !is_string($givennames));
if ($empty || $notstrings) {
return false;
} else {
return $value;
}
}
$var = filter_var('Doe, Jane Sue', FILTER_CALLBACK, array('options' => 'foo'));
</code>
</p>

**Return:**
- mixed <p>The filtered data, or <b>FALSE</b> if the filter fails.</p>
--------
## filter_var_array(array $data, mixed $definition, bool $add_empty): bool
Gets multiple variables and optionally filters them

**Parameters:**
- array $data <p>
An array with string keys containing the data to filter.
</p>
- mixed $definition [optional] <p>
An array defining the arguments. A valid key is a string
containing a variable name and a valid value is either a
filter type, or an
array optionally specifying the filter, flags and options.
If the value is an array, valid keys are filter
which specifies the filter type,
flags which specifies any flags that apply to the
filter, and options which specifies any options that
apply to the filter. See the example below for a better understanding.
</p>
<p>
This parameter can be also an integer holding a filter constant. Then all values
in the input array are filtered by this filter.
</p>
- bool $add_empty [optional] <p>
Add missing keys as <b>NULL</b> to the return value.
</p>

**Return:**
- mixed <p>
An array containing the values of the requested variables on success, or <b>FALSE</b> on failure.
An array value will be <b>FALSE</b> if the filter fails, or <b>NULL</b> if the variable is not
set.
</p>
--------
## finfo_loaded(): bool


**Parameters:**
__nothing__

**Return:**
- bool <strong>true</strong> if available, <strong>false</strong> otherwise
--------
## first_char(string $str, int $n, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- int $n <p>Number of characters to retrieve from the start.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string 
--------
## fits_inside(string $str, int $box_size): int


**Parameters:**
- string $str the original string to be checked
- int $box_size the size in number of chars to be checked against string

**Return:**
- bool <p><strong>TRUE</strong> if string is less than or equal to $box_size, <strong>FALSE</strong> otherwise.</p>
--------
## fix_simple_utf8(string $str): string
INFO: Take a look at "UTF8::fix_utf8()" if you need a more advanced fix for broken UTF-8 strings.

If you received an UTF-8 string that was converted from Windows-1252 as it was ISO-8859-1
(ignoring Windows-1252 chars from 80 to 9F) use this function to fix it.
See: http://en.wikipedia.org/wiki/Windows-1252

**Parameters:**
- string $str <p>The input string</p>

**Return:**
- string 
--------
## fix_utf8(string|string[] $str): string|string[]


**Parameters:**
- string|string[] $str you can use a string or an array of strings

**Return:**
- string|string[] Will return the fixed input-"array" or
the fixed input-"string"
--------
## getCharDirection(string $char): string


**Parameters:**
- string $char 

**Return:**
- string <p>'RTL' or 'LTR'.</p>
--------
## getSupportInfo(string|null $key): string|null


**Parameters:**
- string|null $key 

**Return:**
- mixed Return the full support-"array", if $key === null<br>
return bool-value, if $key is used and available<br>
otherwise return <strong>null</strong>
--------
## get_file_type(string $str, array $fallback): array


**Parameters:**
- string $str 
- array $fallback <p>with this keys: 'ext', 'mime', 'type'

**Return:**
- array<string,string|null> <p>with this keys: 'ext', 'mime', 'type'</p>
--------
## get_random_string(int $length, string $possible_chars, string $encoding): string


**Parameters:**
- int $length <p>Length of the random string.</p>
- string $possible_chars [optional] <p>Characters string for the random selection.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string 
--------
## get_unique_string(int|string $extra_entropy, bool $use_md5): bool


**Parameters:**
- int|string $extra_entropy [optional] <p>Extra entropy via a string or int value.</p>
- bool $use_md5 [optional] <p>Return the unique identifier as md5-hash? Default: true</p>

**Return:**
- string 
--------
## hasBom(string $str): string


**Parameters:**
- string $str 

**Return:**
- bool 
--------
## has_lowercase(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- bool <p>Whether or not the string contains a lower case character.</p>
--------
## has_uppercase(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- bool whether or not the string contains an upper case character
--------
## has_whitespace(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- bool <p>Whether or not the string contains whitespace.</p>
--------
## hex_to_chr(string $hexdec): string


**Parameters:**
- string $hexdec <p>The hexadecimal value.</p>

**Return:**
- bool|string one single UTF-8 character
--------
## hex_to_int(string $hexdec): string
INFO: opposite to UTF8::int_to_hex()

**Parameters:**
- string $hexdec <p>The hexadecimal code point representation.</p>

**Return:**
- bool|int <p>The code point, or false on failure.</p>
--------
## html_decode(string $str, int $flags, string $encoding): string


**Parameters:**
- string $str 
- int $flags 
- string $encoding 

**Return:**
- string 
--------
## html_encode(string $str, bool $keep_ascii_chars, string $encoding): string
INFO: opposite to UTF8::html_decode()

**Parameters:**
- string $str <p>The Unicode string to be encoded as numbered entities.</p>
- bool $keep_ascii_chars [optional] <p>Keep ASCII chars.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string HTML numbered entities
--------
## html_entity_decode(string $str, int $flags, string $encoding): string
The reason we are not using html_entity_decode() by itself is because
while it is not technically correct to leave out the semicolon
at the end of an entity most browsers will still interpret the entity
correctly. html_entity_decode() does not convert entities without
semicolons, so we are left with our own little solution here. Bummer.

Convert all HTML entities to their applicable characters

INFO: opposite to UTF8::html_encode()

**Parameters:**
- string $str <p>
The input string.
</p>
- int $flags [optional] <p>
A bitmask of one or more of the following flags, which specify how to handle quotes
and which document type to use. The default is ENT_COMPAT | ENT_HTML401.
<table>
Available <i>flags</i> constants
<tr valign="top">
<td>Constant Name</td>
<td>Description</td>
</tr>
<tr valign="top">
<td><b>ENT_COMPAT</b></td>
<td>Will convert double-quotes and leave single-quotes alone.</td>
</tr>
<tr valign="top">
<td><b>ENT_QUOTES</b></td>
<td>Will convert both double and single quotes.</td>
</tr>
<tr valign="top">
<td><b>ENT_NOQUOTES</b></td>
<td>Will leave both double and single quotes unconverted.</td>
</tr>
<tr valign="top">
<td><b>ENT_HTML401</b></td>
<td>
Handle code as HTML 4.01.
</td>
</tr>
<tr valign="top">
<td><b>ENT_XML1</b></td>
<td>
Handle code as XML 1.
</td>
</tr>
<tr valign="top">
<td><b>ENT_XHTML</b></td>
<td>
Handle code as XHTML.
</td>
</tr>
<tr valign="top">
<td><b>ENT_HTML5</b></td>
<td>
Handle code as HTML 5.
</td>
</tr>
</table>
</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string the decoded string
--------
## html_escape(string $str, string $encoding): string


**Parameters:**
- string $str 
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string 
--------
## html_stripe_empty_tags(string $str): string
e.g.: <pre><tag></tag></pre>

**Parameters:**
- string $str 

**Return:**
- string 
--------
## htmlentities(string $str, int $flags, string $encoding, bool $double_encode): bool


**Parameters:**
- string $str <p>
The input string.
</p>
- int $flags [optional] <p>
A bitmask of one or more of the following flags, which specify how to handle
quotes, invalid code unit sequences and the used document type. The default is
ENT_COMPAT | ENT_HTML401.
<table>
Available <i>flags</i> constants
<tr valign="top">
<td>Constant Name</td>
<td>Description</td>
</tr>
<tr valign="top">
<td><b>ENT_COMPAT</b></td>
<td>Will convert double-quotes and leave single-quotes alone.</td>
</tr>
<tr valign="top">
<td><b>ENT_QUOTES</b></td>
<td>Will convert both double and single quotes.</td>
</tr>
<tr valign="top">
<td><b>ENT_NOQUOTES</b></td>
<td>Will leave both double and single quotes unconverted.</td>
</tr>
<tr valign="top">
<td><b>ENT_IGNORE</b></td>
<td>
Silently discard invalid code unit sequences instead of returning
an empty string. Using this flag is discouraged as it
may have security implications.
</td>
</tr>
<tr valign="top">
<td><b>ENT_SUBSTITUTE</b></td>
<td>
Replace invalid code unit sequences with a Unicode Replacement Character
U+FFFD (UTF-8) or &#38;#38;#FFFD; (otherwise) instead of returning an empty
string.
</td>
</tr>
<tr valign="top">
<td><b>ENT_DISALLOWED</b></td>
<td>
Replace invalid code points for the given document type with a
Unicode Replacement Character U+FFFD (UTF-8) or &#38;#38;#FFFD;
(otherwise) instead of leaving them as is. This may be useful, for
instance, to ensure the well-formedness of XML documents with
embedded external content.
</td>
</tr>
<tr valign="top">
<td><b>ENT_HTML401</b></td>
<td>
Handle code as HTML 4.01.
</td>
</tr>
<tr valign="top">
<td><b>ENT_XML1</b></td>
<td>
Handle code as XML 1.
</td>
</tr>
<tr valign="top">
<td><b>ENT_XHTML</b></td>
<td>
Handle code as XHTML.
</td>
</tr>
<tr valign="top">
<td><b>ENT_HTML5</b></td>
<td>
Handle code as HTML 5.
</td>
</tr>
</table>
</p>
- string $encoding [optional] <p>
Like <b>htmlspecialchars</b>,
<b>htmlentities</b> takes an optional third argument
<i>encoding</i> which defines encoding used in
conversion.
Although this argument is technically optional, you are highly
encouraged to specify the correct value for your code.
</p>
- bool $double_encode [optional] <p>
When <i>double_encode</i> is turned off PHP will not
encode existing html entities. The default is to convert everything.
</p>

**Return:**
- string <p>
The encoded string.
<br><br>
If the input <i>string</i> contains an invalid code unit
sequence within the given <i>encoding</i> an empty string
will be returned, unless either the <b>ENT_IGNORE</b> or
<b>ENT_SUBSTITUTE</b> flags are set.
</p>
--------
## htmlspecialchars(string $str, int $flags, string $encoding, bool $double_encode): bool
INFO: Take a look at "UTF8::htmlentities()"

**Parameters:**
- string $str <p>
The string being converted.
</p>
- int $flags [optional] <p>
A bitmask of one or more of the following flags, which specify how to handle
quotes, invalid code unit sequences and the used document type. The default is
ENT_COMPAT | ENT_HTML401.
<table>
Available <i>flags</i> constants
<tr valign="top">
<td>Constant Name</td>
<td>Description</td>
</tr>
<tr valign="top">
<td><b>ENT_COMPAT</b></td>
<td>Will convert double-quotes and leave single-quotes alone.</td>
</tr>
<tr valign="top">
<td><b>ENT_QUOTES</b></td>
<td>Will convert both double and single quotes.</td>
</tr>
<tr valign="top">
<td><b>ENT_NOQUOTES</b></td>
<td>Will leave both double and single quotes unconverted.</td>
</tr>
<tr valign="top">
<td><b>ENT_IGNORE</b></td>
<td>
Silently discard invalid code unit sequences instead of returning
an empty string. Using this flag is discouraged as it
may have security implications.
</td>
</tr>
<tr valign="top">
<td><b>ENT_SUBSTITUTE</b></td>
<td>
Replace invalid code unit sequences with a Unicode Replacement Character
U+FFFD (UTF-8) or &#38;#38;#FFFD; (otherwise) instead of returning an empty
string.
</td>
</tr>
<tr valign="top">
<td><b>ENT_DISALLOWED</b></td>
<td>
Replace invalid code points for the given document type with a
Unicode Replacement Character U+FFFD (UTF-8) or &#38;#38;#FFFD;
(otherwise) instead of leaving them as is. This may be useful, for
instance, to ensure the well-formedness of XML documents with
embedded external content.
</td>
</tr>
<tr valign="top">
<td><b>ENT_HTML401</b></td>
<td>
Handle code as HTML 4.01.
</td>
</tr>
<tr valign="top">
<td><b>ENT_XML1</b></td>
<td>
Handle code as XML 1.
</td>
</tr>
<tr valign="top">
<td><b>ENT_XHTML</b></td>
<td>
Handle code as XHTML.
</td>
</tr>
<tr valign="top">
<td><b>ENT_HTML5</b></td>
<td>
Handle code as HTML 5.
</td>
</tr>
</table>
</p>
- string $encoding [optional] <p>
Defines encoding used in conversion.
</p>
<p>
For the purposes of this function, the encodings
ISO-8859-1, ISO-8859-15,
UTF-8, cp866,
cp1251, cp1252, and
KOI8-R are effectively equivalent, provided the
<i>string</i> itself is valid for the encoding, as
the characters affected by <b>htmlspecialchars</b> occupy
the same positions in all of these encodings.
</p>
- bool $double_encode [optional] <p>
When <i>double_encode</i> is turned off PHP will not
encode existing html entities, the default is to convert everything.
</p>

**Return:**
- string the converted string.
</p>
<p>
If the input <i>string</i> contains an invalid code unit
sequence within the given <i>encoding</i> an empty string
will be returned, unless either the <b>ENT_IGNORE</b> or
<b>ENT_SUBSTITUTE</b> flags are set
--------
## iconv_loaded(): bool


**Parameters:**
__nothing__

**Return:**
- bool <strong>true</strong> if available, <strong>false</strong> otherwise
--------
## int_to_chr(mixed $int): mixed


**Parameters:**
- mixed $int 

**Return:**
- string 
--------
## int_to_hex(int $int, string $prefix): string
INFO: opposite to UTF8::hex_to_int()

**Parameters:**
- int $int <p>The integer to be converted to hexadecimal code point.</p>
- string $prefix [optional]

**Return:**
- string the code point, or empty string on failure
--------
## intlChar_loaded(): string


**Parameters:**
__nothing__

**Return:**
- bool <strong>true</strong> if available, <strong>false</strong> otherwise
--------
## intl_loaded(): string


**Parameters:**
__nothing__

**Return:**
- bool <strong>true</strong> if available, <strong>false</strong> otherwise
--------
## isAscii(string $str): string


**Parameters:**
- string $str 

**Return:**
- bool 
--------
## isBase64(string $str): string


**Parameters:**
- string $str 

**Return:**
- bool 
--------
## isBinary(mixed $str, bool $strict): bool


**Parameters:**
- mixed $str 
- bool $strict 

**Return:**
- bool 
--------
## isBom(string $utf8_chr): string


**Parameters:**
- string $utf8_chr 

**Return:**
- bool 
--------
## isHtml(string $str): string


**Parameters:**
- string $str 

**Return:**
- bool 
--------
## isJson(string $str): string


**Parameters:**
- string $str 

**Return:**
- bool 
--------
## isUtf16(mixed $str): mixed


**Parameters:**
- mixed $str 

**Return:**
- bool|int <strong>false</strong> if is't not UTF16,<br>
<strong>1</strong> for UTF-16LE,<br>
<strong>2</strong> for UTF-16BE
--------
## isUtf32(mixed $str): mixed


**Parameters:**
- mixed $str 

**Return:**
- bool|int <strong>false</strong> if is't not UTF16,
<strong>1</strong> for UTF-32LE,
<strong>2</strong> for UTF-32BE
--------
## isUtf8(string $str, bool $strict): bool


**Parameters:**
- string $str 
- bool $strict 

**Return:**
- bool 
--------
## is_alpha(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- bool <p>Whether or not $str contains only alphabetic chars.</p>
--------
## is_alphanumeric(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- bool <p>Whether or not $str contains only alphanumeric chars.</p>
--------
## is_ascii(string $str): string


**Parameters:**
- string $str <p>The string to check.</p>

**Return:**
- bool <p>
<strong>true</strong> if it is ASCII<br>
<strong>false</strong> otherwise
</p>
--------
## is_base64(mixed|string $str, bool $empty_string_is_valid): bool


**Parameters:**
- mixed|string $str <p>The input string.</p>
- bool $empty_string_is_valid [optional] <p>Is an empty string valid base64 or not?</p>

**Return:**
- bool <p>Whether or not $str is base64 encoded.</p>
--------
## is_binary(mixed $input, bool $strict): bool


**Parameters:**
- mixed $input 
- bool $strict 

**Return:**
- bool 
--------
## is_binary_file(string $file): string


**Parameters:**
- string $file 

**Return:**
- bool 
--------
## is_blank(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- bool <p>Whether or not $str contains only whitespace characters.</p>
--------
## is_bom(string $str): string
WARNING: Use "UTF8::string_has_bom()" if you will check BOM in a string.

**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- bool <p><strong>true</strong> if the $utf8_chr is Byte Order Mark, <strong>false</strong> otherwise.</p>
--------
## is_empty(mixed $str): mixed
A variable is considered empty if it does not exist or if its value equals FALSE.
empty() does not generate a warning if the variable does not exist.

**Parameters:**
- mixed $str 

**Return:**
- bool <p>Whether or not $str is empty().</p>
--------
## is_hexadecimal(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- bool <p>Whether or not $str contains only hexadecimal chars.</p>
--------
## is_html(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- bool <p>Whether or not $str contains html elements.</p>
--------
## is_json(string $str, bool $only_array_or_object_results_are_valid): bool


**Parameters:**
- string $str <p>The input string.</p>
- bool $only_array_or_object_results_are_valid [optional] <p>Only array and objects are valid json
results.</p>

**Return:**
- bool <p>Whether or not the $str is in JSON format.</p>
--------
## is_lowercase(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- bool <p>Whether or not $str contains only lowercase chars.</p>
--------
## is_printable(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- bool <p>Whether or not $str contains only printable (non-invisible) chars.</p>
--------
## is_punctuation(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- bool <p>Whether or not $str contains only punctuation chars.</p>
--------
## is_serialized(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- bool <p>Whether or not $str is serialized.</p>
--------
## is_uppercase(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- bool <p>Whether or not $str contains only lower case characters.</p>
--------
## is_url(string $url, bool $disallow_localhost): bool


**Parameters:**
- string $url 
- bool $disallow_localhost 

**Return:**
- bool 
--------
## is_utf16(mixed $str, bool $check_if_string_is_binary): bool


**Parameters:**
- mixed $str <p>The input string.</p>
- bool $check_if_string_is_binary 

**Return:**
- bool|int <strong>false</strong> if is't not UTF-16,<br>
<strong>1</strong> for UTF-16LE,<br>
<strong>2</strong> for UTF-16BE
--------
## is_utf32(mixed $str, bool $check_if_string_is_binary): bool


**Parameters:**
- mixed $str <p>The input string.</p>
- bool $check_if_string_is_binary 

**Return:**
- bool|int <strong>false</strong> if is't not UTF-32,<br>
<strong>1</strong> for UTF-32LE,<br>
<strong>2</strong> for UTF-32BE
--------
## is_utf8(int|string|string[]|null $str, bool $strict): bool


**Parameters:**
- int|string|string[]|null $str <p>The input to be checked.</p>
- bool $strict <p>Check also if the string is not UTF-16 or UTF-32.</p>

**Return:**
- bool 
--------
## json_decode(string $json, bool $assoc, int $depth, int $options): int


**Parameters:**
- string $json <p>
The <i>json</i> string being decoded.
</p>
<p>
This function only works with UTF-8 encoded strings.
</p>
<p>PHP implements a superset of
JSON - it will also encode and decode scalar types and <b>NULL</b>. The JSON standard
only supports these values when they are nested inside an array or an object.
</p>
- bool $assoc [optional] <p>
When <b>TRUE</b>, returned objects will be converted into
associative arrays.
</p>
- int $depth [optional] <p>
User specified recursion depth.
</p>
- int $options [optional] <p>
Bitmask of JSON decode options. Currently only
<b>JSON_BIGINT_AS_STRING</b>
is supported (default is to cast large integers as floats)
</p>

**Return:**
- mixed <p>The value encoded in <i>json</i> in appropriate PHP type. Values true, false and
null (case-insensitive) are returned as <b>TRUE</b>, <b>FALSE</b> and <b>NULL</b> respectively.
<b>NULL</b> is returned if the <i>json</i> cannot be decoded or if the encoded data
is deeper than the recursion limit.</p>
--------
## json_encode(mixed $value, int $options, int $depth): int


**Parameters:**
- mixed $value <p>
The <i>value</i> being encoded. Can be any type except
a resource.
</p>
<p>
All string data must be UTF-8 encoded.
</p>
<p>PHP implements a superset of
JSON - it will also encode and decode scalar types and <b>NULL</b>. The JSON standard
only supports these values when they are nested inside an array or an object.
</p>
- int $options [optional] <p>
Bitmask consisting of <b>JSON_HEX_QUOT</b>,
<b>JSON_HEX_TAG</b>,
<b>JSON_HEX_AMP</b>,
<b>JSON_HEX_APOS</b>,
<b>JSON_NUMERIC_CHECK</b>,
<b>JSON_PRETTY_PRINT</b>,
<b>JSON_UNESCAPED_SLASHES</b>,
<b>JSON_FORCE_OBJECT</b>,
<b>JSON_UNESCAPED_UNICODE</b>. The behaviour of these
constants is described on
the JSON constants page.
</p>
- int $depth [optional] <p>
Set the maximum depth. Must be greater than zero.
</p>

**Return:**
- bool|string A JSON encoded <strong>string</strong> on success or<br>
<strong>FALSE</strong> on failure
--------
## json_loaded(): int


**Parameters:**
__nothing__

**Return:**
- bool <strong>true</strong> if available, <strong>false</strong> otherwise
--------
## lcfirst(string $str, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): bool


**Parameters:**
- string $str <p>The input string</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
- string|null $lang [optional] <p>Set the language for special cases: az, el, lt,
tr</p>
- bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
-> ß</p>

**Return:**
- string the resulting string
--------
## lcword(string $str, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): bool


**Parameters:**
- string $str 
- string $encoding 
- bool $clean_utf8 
- string|null $lang 
- bool $try_to_keep_the_string_length 

**Return:**
- string 
--------
## lcwords(string $str, string[] $exceptions, string $char_list, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): bool


**Parameters:**
- string $str <p>The input string.</p>
- string[] $exceptions [optional] <p>Exclusion for some words.</p>
- string $char_list [optional] <p>Additional chars that contains to words and do
not start a new word.</p>
- string $encoding [optional] <p>Set the charset.</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
- string|null $lang [optional] <p>Set the language for special cases: az, el, lt,
tr</p>
- bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
-> ß</p>

**Return:**
- string 
--------
## lowerCaseFirst(string $str, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): bool


**Parameters:**
- string $str 
- string $encoding 
- bool $clean_utf8 
- string|null $lang 
- bool $try_to_keep_the_string_length 

**Return:**
- string 
--------
## ltrim(string $str, string|null $chars): string|null


**Parameters:**
- string $str <p>The string to be trimmed</p>
- string|null $chars <p>Optional characters to be stripped</p>

**Return:**
- string the string with unwanted characters stripped from the left
--------
## max(string[]|string $arg): string[]|string


**Parameters:**
- string[]|string $arg <p>A UTF-8 encoded string or an array of such strings.</p>

**Return:**
- string|null the character with the highest code point than others, returns null on failure or empty input
--------
## max_chr_width(string $str): string


**Parameters:**
- string $str <p>The original Unicode string.</p>

**Return:**
- int <p>Max byte lengths of the given chars.</p>
--------
## mbstring_loaded(): string


**Parameters:**
__nothing__

**Return:**
- bool <strong>true</strong> if available, <strong>false</strong> otherwise
--------
## min(mixed $arg): mixed


**Parameters:**
- mixed $arg <strong>A UTF-8 encoded string or an array of such strings.</strong>

**Return:**
- string|null the character with the lowest code point than others, returns null on failure or empty input
--------
## normalizeEncoding(mixed $encoding, mixed $fallback): mixed


**Parameters:**
- mixed $encoding 
- mixed $fallback 

**Return:**
- mixed 
--------
## normalize_encoding(mixed $encoding, mixed $fallback): mixed


**Parameters:**
- mixed $encoding <p>e.g.: ISO, UTF8, WINDOWS-1251 etc.</p>
- mixed $fallback <p>e.g.: UTF-8</p>

**Return:**
- mixed e.g.: ISO-8859-1, UTF-8, WINDOWS-1251 etc.<br>Will return a empty string as fallback (by default)
--------
## normalize_line_ending(string $str, string|string[] $replacer): string|string[]


**Parameters:**
- string $str <p>The input string.</p>
- string|string[] $replacer <p>The replacer char e.g. "\n" (Linux) or "\r\n" (Windows). You can also use \PHP_EOL
here.</p>

**Return:**
- string <p>A string with normalized line ending.</p>
--------
## normalize_msword(string $str): string


**Parameters:**
- string $str <p>The string to be normalized.</p>

**Return:**
- string <p>A string with normalized characters for commonly used chars in Word documents.</p>
--------
## normalize_whitespace(string $str, bool $keep_non_breaking_space, bool $keep_bidi_unicode_controls): bool


**Parameters:**
- string $str <p>The string to be normalized.</p>
- bool $keep_non_breaking_space [optional] <p>Set to true, to keep non-breaking-spaces.</p>
- bool $keep_bidi_unicode_controls [optional] <p>Set to true, to keep non-printable (for the web)
bidirectional text chars.</p>

**Return:**
- string <p>A string with normalized whitespace.</p>
--------
## ord(string $chr, string $encoding): string
INFO: opposite to UTF8::chr()

**Parameters:**
- string $chr <p>The character of which to calculate code point.<p/>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- int <p>Unicode code point of the given character,<br>
0 on invalid UTF-8 byte sequence</p>
--------
## parse_str(string $str, array $result, bool $clean_utf8): bool
WARNING: Unlike "parse_str()", this method does not (re-)place variables in the current scope,
if the second parameter is not set!

**Parameters:**
- string $str <p>The input string.</p>
- array $result <p>The result will be returned into this reference parameter.</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>

**Return:**
- bool <p>Will return <strong>false</strong> if php can't parse the string and we haven't any $result.</p>
--------
## pcre_utf8_support(): bool


**Parameters:**
__nothing__

**Return:**
- bool <p>
<strong>true</strong> if support is available,<br>
<strong>false</strong> otherwise
</p>
--------
## range(mixed $var1, mixed $var2, bool $use_ctype, string $encoding, float|int $step): float|int


**Parameters:**
- mixed $var1 <p>Numeric or hexadecimal code points, or a UTF-8 character to start from.</p>
- mixed $var2 <p>Numeric or hexadecimal code points, or a UTF-8 character to end at.</p>
- bool $use_ctype <p>use ctype to detect numeric and hexadecimal, otherwise we will use a simple
"is_numeric"</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
- float|int $step [optional] <p>
If a step value is given, it will be used as the
increment between elements in the sequence. step
should be given as a positive number. If not specified,
step will default to 1.
</p>

**Return:**
- string[] 
--------
## rawurldecode(string $str, bool $multi_decode): bool
e.g:
'test+test'                     => 'test+test'
'D&#252;sseldorf'               => 'Düsseldorf'
'D%FCsseldorf'                  => 'Düsseldorf'
'D&#xFC;sseldorf'               => 'Düsseldorf'
'D%26%23xFC%3Bsseldorf'         => 'Düsseldorf'
'DÃ¼sseldorf'                   => 'Düsseldorf'
'D%C3%BCsseldorf'               => 'Düsseldorf'
'D%C3%83%C2%BCsseldorf'         => 'Düsseldorf'
'D%25C3%2583%25C2%25BCsseldorf' => 'Düsseldorf'

**Parameters:**
- string $str <p>The input string.</p>
- bool $multi_decode <p>Decode as often as possible.</p>

**Return:**
- string <p>The decoded URL, as a string.</p>
--------
## regex_replace(string $str, string $pattern, string $replacement, string $options, string $delimiter): string


**Parameters:**
- string $str <p>The input string.</p>
- string $pattern <p>The regular expression pattern.</p>
- string $replacement <p>The string to replace with.</p>
- string $options [optional] <p>Matching conditions to be used.</p>
- string $delimiter [optional] <p>Delimiter the the regex. Default: '/'</p>

**Return:**
- string 
--------
## removeBOM(string $str): string


**Parameters:**
- string $str 

**Return:**
- string 
--------
## remove_bom(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- string <p>A string without UTF-BOM.</p>
--------
## remove_duplicates(string $str, string|string[] $what): string|string[]


**Parameters:**
- string $str <p>The base string.</p>
- string|string[] $what <p>String to search for in the base string.</p>

**Return:**
- string <p>A string with removed duplicates.</p>
--------
## remove_html(string $str, string $allowable_tags): string


**Parameters:**
- string $str <p>The input string.</p>
- string $allowable_tags [optional] <p>You can use the optional second parameter to specify tags which
should not be stripped. Default: null
</p>

**Return:**
- string <p>A string with without html tags.</p>
--------
## remove_html_breaks(string $str, string $replacement): string


**Parameters:**
- string $str <p>The input string.</p>
- string $replacement [optional] <p>Default is a empty string.</p>

**Return:**
- string <p>A string without breaks.</p>
--------
## remove_invisible_characters(string $str, bool $url_encoded, string $replacement): string
e.g.: This prevents sandwiching null characters between ascii characters, like Java\0script.

copy&past from https://github.com/bcit-ci/CodeIgniter/blob/develop/system/core/Common.php

**Parameters:**
- string $str <p>The input string.</p>
- bool $url_encoded [optional] <p>
Try to remove url encoded control character.
WARNING: maybe contains false-positives e.g. aa%0Baa -> aaaa.
<br>
Default: false
</p>
- string $replacement [optional] <p>The replacement character.</p>

**Return:**
- string <p>A string without invisible chars.</p>
--------
## remove_left(string $str, string $substring, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $substring <p>The prefix to remove.</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>

**Return:**
- string <p>A string without the prefix $substring.</p>
--------
## remove_right(string $str, string $substring, string $encoding): string


**Parameters:**
- string $str 
- string $substring <p>The suffix to remove.</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>

**Return:**
- string <p>A string having a $str without the suffix $substring.</p>
--------
## replace(string $str, string $search, string $replacement, bool $case_sensitive): bool


**Parameters:**
- string $str <p>The input string.</p>
- string $search <p>The needle to search for.</p>
- string $replacement <p>The string to replace with.</p>
- bool $case_sensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>

**Return:**
- string <p>A string with replaced parts.</p>
--------
## replace_all(string $str, array $search, array|string $replacement, bool $case_sensitive): bool


**Parameters:**
- string $str <p>The input string.</p>
- array $search <p>The elements to search for.</p>
- array|string $replacement <p>The string to replace with.</p>
- bool $case_sensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>

**Return:**
- string <p>A string with replaced parts.</p>
--------
## replace_diamond_question_mark(string $str, string $replacement_char, bool $process_invalid_utf8_chars): bool


**Parameters:**
- string $str <p>The input string</p>
- string $replacement_char <p>The replacement character.</p>
- bool $process_invalid_utf8_chars <p>Convert invalid UTF-8 chars </p>

**Return:**
- string <p>A string without diamond question marks (�).</p>
--------
## rtrim(string $str, string|null $chars): string|null


**Parameters:**
- string $str <p>The string to be trimmed.</p>
- string|null $chars <p>Optional characters to be stripped.</p>

**Return:**
- string <p>A string with unwanted characters stripped from the right.</p>
--------
## showSupport(bool $useEcho): bool


**Parameters:**
- bool $useEcho 

**Return:**
- string|void 
--------
## single_chr_html_encode(string $char, bool $keep_ascii_chars, string $encoding): string


**Parameters:**
- string $char <p>The Unicode character to be encoded as numbered entity.</p>
- bool $keep_ascii_chars <p>Set to <strong>true</strong> to keep ASCII chars.</>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string <p>The HTML numbered entity for the given character.</p>
--------
## spaces_to_tabs(string $str, int $tab_length): int


**Parameters:**
- string $str 
- int $tab_length 

**Return:**
- string 
--------
## split(int|string $str, int $length, bool $clean_utf8): bool


**Parameters:**
- int|string $str 
- int $length 
- bool $clean_utf8 

**Return:**
- string[] 
--------
## str_begins(string $haystack, string $needle): string


**Parameters:**
- string $haystack 
- string $needle 

**Return:**
- bool 
--------
## str_camelize(string $str, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): bool


**Parameters:**
- string $str <p>The input string.</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
- string|null $lang [optional] <p>Set the language for special cases: az, el, lt,
tr</p>
- bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
-> ß</p>

**Return:**
- string 
--------
## str_capitalize_name(string $str): string


**Parameters:**
- string $str 

**Return:**
- string <p>A string with $str capitalized.</p>
--------
## str_contains(string $haystack, string $needle, bool $case_sensitive): bool


**Parameters:**
- string $haystack <p>The input string.</p>
- string $needle <p>Substring to look for.</p>
- bool $case_sensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>

**Return:**
- bool whether or not $haystack contains $needle
--------
## str_contains_all(string $haystack, array $needles, bool $case_sensitive): bool


**Parameters:**
- string $haystack <p>The input string.</p>
- array $needles <p>SubStrings to look for.</p>
- bool $case_sensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>

**Return:**
- bool whether or not $haystack contains $needle
--------
## str_contains_any(string $haystack, array $needles, bool $case_sensitive): bool


**Parameters:**
- string $haystack <p>The input string.</p>
- array $needles <p>SubStrings to look for.</p>
- bool $case_sensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>

**Return:**
- bool Whether or not $str contains $needle
--------
## str_dasherize(string $str, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string 
--------
## str_delimit(string $str, string $delimiter, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): bool
Delimiters are inserted before uppercase characters (with the exception
of the first character of the string), and in place of spaces, dashes,
and underscores. Alpha delimiters are not converted to lowercase.

**Parameters:**
- string $str <p>The input string.</p>
- string $delimiter <p>Sequence used to separate parts of the string.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
- string|null $lang [optional] <p>Set the language for special cases: az, el, lt,
tr</p>
- bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ ->
ß</p>

**Return:**
- string 
--------
## str_detect_encoding(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- bool|string The detected string-encoding e.g. UTF-8 or UTF-16BE,<br>
otherwise it will return false e.g. for BINARY or not detected encoding.
--------
## str_ends(string $haystack, string $needle): string


**Parameters:**
- string $haystack 
- string $needle 

**Return:**
- bool 
--------
## str_ends_with(string $haystack, string $needle): string


**Parameters:**
- string $haystack <p>The string to search in.</p>
- string $needle <p>The substring to search for.</p>

**Return:**
- bool 
--------
## str_ends_with_any(string $str, string[] $substrings): string[]
- case-sensitive

**Parameters:**
- string $str <p>The input string.</p>
- string[] $substrings <p>Substrings to look for.</p>

**Return:**
- bool whether or not $str ends with $substring
--------
## str_ensure_left(string $str, string $substring): string


**Parameters:**
- string $str <p>The input string.</p>
- string $substring <p>The substring to add if not present.</p>

**Return:**
- string 
--------
## str_ensure_right(string $str, string $substring): string


**Parameters:**
- string $str <p>The input string.</p>
- string $substring <p>The substring to add if not present.</p>

**Return:**
- string 
--------
## str_humanize(string $str): string


**Parameters:**
- string $str 

**Return:**
- string 
--------
## str_ibegins(string $haystack, string $needle): string


**Parameters:**
- string $haystack 
- string $needle 

**Return:**
- bool 
--------
## str_iends(string $haystack, string $needle): string


**Parameters:**
- string $haystack 
- string $needle 

**Return:**
- bool 
--------
## str_iends_with(string $haystack, string $needle): string


**Parameters:**
- string $haystack <p>The string to search in.</p>
- string $needle <p>The substring to search for.</p>

**Return:**
- bool 
--------
## str_iends_with_any(string $str, string[] $substrings): string[]
- case-insensitive

**Parameters:**
- string $str <p>The input string.</p>
- string[] $substrings <p>Substrings to look for.</p>

**Return:**
- bool <p>Whether or not $str ends with $substring.</p>
--------
## str_iindex_first(string $str, string $needle, int $offset, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $needle <p>Substring to look for.</p>
- int $offset [optional] <p>Offset from which to search. Default: 0</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- bool|int <p>The occurrence's <strong>index</strong> if found, otherwise <strong>false</strong>.</p>
--------
## str_iindex_last(string $str, string $needle, int $offset, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $needle <p>Substring to look for.</p>
- int $offset [optional] <p>Offset from which to search. Default: 0</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- bool|int <p>The last occurrence's <strong>index</strong> if found, otherwise <strong>false</strong>.</p>
--------
## str_index_first(string $str, string $needle, int $offset, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $needle <p>Substring to look for.</p>
- int $offset [optional] <p>Offset from which to search. Default: 0</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- bool|int <p>The occurrence's <strong>index</strong> if found, otherwise <strong>false</strong>.</p>
--------
## str_index_last(string $str, string $needle, int $offset, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $needle <p>Substring to look for.</p>
- int $offset [optional] <p>Offset from which to search. Default: 0</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- bool|int <p>The last occurrence's <strong>index</strong> if found, otherwise <strong>false</strong>.</p>
--------
## str_insert(string $str, string $substring, int $index, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $substring <p>String to be inserted.</p>
- int $index <p>The index at which to insert the substring.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string 
--------
## str_ireplace(string|string[] $search, string|string[] $replacement, mixed $subject, int $count): int


**Parameters:**
- string|string[] $search <p>
Every replacement with search array is
performed on the result of previous replacement.
</p>
- string|string[] $replacement <p>The replacement.</p>
- mixed $subject <p>
If subject is an array, then the search and
replace is performed with every entry of
subject, and the return value is an array as
well.
</p>
- int $count [optional] <p>
The number of matched and replaced needles will
be returned in count which is passed by
reference.
</p>

**Return:**
- mixed a string or an array of replacements
--------
## str_ireplace_beginning(string $str, string $search, string $replacement): string


**Parameters:**
- string $str <p>The input string.</p>
- string $search <p>The string to search for.</p>
- string $replacement <p>The replacement.</p>

**Return:**
- string string after the replacements
--------
## str_ireplace_ending(string $str, string $search, string $replacement): string


**Parameters:**
- string $str <p>The input string.</p>
- string $search <p>The string to search for.</p>
- string $replacement <p>The replacement.</p>

**Return:**
- string <p>string after the replacements.</p>
--------
## str_istarts_with(string $haystack, string $needle): string


**Parameters:**
- string $haystack <p>The string to search in.</p>
- string $needle <p>The substring to search for.</p>

**Return:**
- bool 
--------
## str_istarts_with_any(string $str, array $substrings): array
- case-insensitive

**Parameters:**
- string $str <p>The input string.</p>
- array $substrings <p>Substrings to look for.</p>

**Return:**
- bool whether or not $str starts with $substring
--------
## str_isubstr_after_first_separator(string $str, string $separator, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $separator <p>The string separator.</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>

**Return:**
- string 
--------
## str_isubstr_after_last_separator(string $str, string $separator, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $separator <p>The string separator.</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>

**Return:**
- string 
--------
## str_isubstr_before_first_separator(string $str, string $separator, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $separator <p>The string separator.</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>

**Return:**
- string 
--------
## str_isubstr_before_last_separator(string $str, string $separator, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $separator <p>The string separator.</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>

**Return:**
- string 
--------
## str_isubstr_first(string $str, string $needle, bool $before_needle, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $needle <p>The string to look for.</p>
- bool $before_needle [optional] <p>Default: false</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>

**Return:**
- string 
--------
## str_isubstr_last(string $str, string $needle, bool $before_needle, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $needle <p>The string to look for.</p>
- bool $before_needle [optional] <p>Default: false</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>

**Return:**
- string 
--------
## str_last_char(string $str, int $n, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- int $n <p>Number of characters to retrieve from the end.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string 
--------
## str_limit(string $str, int $length, string $str_add_on, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- int $length [optional] <p>Default: 100</p>
- string $str_add_on [optional] <p>Default: …</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string 
--------
## str_limit_after_word(string $str, int $length, string $str_add_on, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- int $length [optional] <p>Default: 100</p>
- string $str_add_on [optional] <p>Default: …</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string 
--------
## str_longest_common_prefix(string $str1, string $str2, string $encoding): string


**Parameters:**
- string $str1 <p>The input sting.</p>
- string $str2 <p>Second string for comparison.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string 
--------
## str_longest_common_substring(string $str1, string $str2, string $encoding): string
In the case of ties, it returns that which occurs first.

**Parameters:**
- string $str1 
- string $str2 <p>Second string for comparison.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string <p>A string with its $str being the longest common substring.</p>
--------
## str_longest_common_suffix(string $str1, string $str2, string $encoding): string


**Parameters:**
- string $str1 
- string $str2 <p>Second string for comparison.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string 
--------
## str_matches_pattern(string $str, string $pattern): string


**Parameters:**
- string $str <p>The input string.</p>
- string $pattern <p>Regex pattern to match against.</p>

**Return:**
- bool whether or not $str matches the pattern
--------
## str_offset_exists(string $str, int $offset, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- int $offset <p>The index to check.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- bool whether or not the index exists
--------
## str_offset_get(string $str, int $index, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- int $index <p>The <strong>index</strong> from which to retrieve the char.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string <p>The character at the specified index.</p>
--------
## str_pad(string $str, int $pad_length, string $pad_string, int|string $pad_type, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- int $pad_length <p>The length of return string.</p>
- string $pad_string [optional] <p>String to use for padding the input string.</p>
- int|string $pad_type [optional] <p>
Can be <strong>STR_PAD_RIGHT</strong> (default), [or string "right"]<br>
<strong>STR_PAD_LEFT</strong> [or string "left"] or<br>
<strong>STR_PAD_BOTH</strong> [or string "both"]
</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>

**Return:**
- string <p>Returns the padded string.</p>
--------
## str_pad_both(string $str, int $length, string $pad_str, string $encoding): string


**Parameters:**
- string $str 
- int $length <p>Desired string length after padding.</p>
- string $pad_str [optional] <p>String used to pad, defaults to space. Default: ' '</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string <p>The string with padding applied.</p>
--------
## str_pad_left(string $str, int $length, string $pad_str, string $encoding): string


**Parameters:**
- string $str 
- int $length <p>Desired string length after padding.</p>
- string $pad_str [optional] <p>String used to pad, defaults to space. Default: ' '</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string <p>The string with left padding.</p>
--------
## str_pad_right(string $str, int $length, string $pad_str, string $encoding): string


**Parameters:**
- string $str 
- int $length <p>Desired string length after padding.</p>
- string $pad_str [optional] <p>String used to pad, defaults to space. Default: ' '</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string <p>The string with right padding.</p>
--------
## str_repeat(string $str, int $multiplier): int


**Parameters:**
- string $str <p>
The string to be repeated.
</p>
- int $multiplier <p>
Number of time the input string should be
repeated.
</p>
<p>
multiplier has to be greater than or equal to 0.
If the multiplier is set to 0, the function
will return an empty string.
</p>

**Return:**
- string <p>The repeated string.</p>
--------
## str_replace(mixed $search, mixed $replace, mixed $subject, int $count): int
Replace all occurrences of the search string with the replacement string

**Parameters:**
- mixed $search <p>
The value being searched for, otherwise known as the needle.
An array may be used to designate multiple needles.
</p>
- mixed $replace <p>
The replacement value that replaces found search
values. An array may be used to designate multiple replacements.
</p>
- mixed $subject <p>
The string or array being searched and replaced on,
otherwise known as the haystack.
</p>
<p>
If subject is an array, then the search and
replace is performed with every entry of
subject, and the return value is an array as
well.
</p>
- int $count [optional] If passed, this will hold the number of matched and replaced needles

**Return:**
- mixed this function returns a string or an array with the replaced values
--------
## str_replace_beginning(string $str, string $search, string $replacement): string


**Parameters:**
- string $str <p>The input string.</p>
- string $search <p>The string to search for.</p>
- string $replacement <p>The replacement.</p>

**Return:**
- string <p>A string after the replacements.</p>
--------
## str_replace_ending(string $str, string $search, string $replacement): string


**Parameters:**
- string $str <p>The input string.</p>
- string $search <p>The string to search for.</p>
- string $replacement <p>The replacement.</p>

**Return:**
- string <p>A string after the replacements.</p>
--------
## str_replace_first(string $search, string $replace, string $subject): string


**Parameters:**
- string $search 
- string $replace 
- string $subject 

**Return:**
- string 
--------
## str_replace_last(string $search, string $replace, string $subject): string


**Parameters:**
- string $search 
- string $replace 
- string $subject 

**Return:**
- string 
--------
## str_shuffle(string $str, string $encoding): string
PS: uses random algorithm which is weak for cryptography purposes

**Parameters:**
- string $str <p>The input string</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string <p>The shuffled string.</p>
--------
## str_slice(string $str, int $start, int $end, string $encoding): string


**Parameters:**
- string $str 
- int $start <p>Initial index from which to begin extraction.</p>
- int $end [optional] <p>Index at which to end extraction. Default: null</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- bool|string <p>The extracted substring.</p><p>If <i>str</i> is shorter than <i>start</i>
characters long, <b>FALSE</b> will be returned.
--------
## str_snakeize(string $str, string $encoding): string


**Parameters:**
- string $str 
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string <p>A string in snake_case.</p>
--------
## str_sort(string $str, bool $unique, bool $desc): bool


**Parameters:**
- string $str <p>A UTF-8 string.</p>
- bool $unique <p>Sort unique. If <strong>true</strong>, repeated characters are ignored.</p>
- bool $desc <p>If <strong>true</strong>, will sort characters in reverse code point order.</p>

**Return:**
- string <p>A string of sorted characters.</p>
--------
## str_split(int|string $input, int $length, bool $clean_utf8, bool $try_to_use_mb_functions): bool


**Parameters:**
- int|string $input <p>The string or int to split into array.</p>
- int $length [optional] <p>Max character length of each array
element.</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the
string.</p>
- bool $try_to_use_mb_functions [optional] <p>Set to false, if you don't want to use
"mb_substr"</p>

**Return:**
- string[] <p>An array containing chunks of chars from the input.</p>
--------
## str_split_array(int[]|string[] $input, int $length, bool $clean_utf8, bool $try_to_use_mb_functions): bool


**Parameters:**
- int[]|string[] $input <p>The string[] or int[] to split into array.</p>
- int $length [optional] <p>Max character length of each array
lement.</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the
string.</p>
- bool $try_to_use_mb_functions [optional] <p>Set to false, if you don't want to use
"mb_substr"</p>

**Return:**
- string[][] <p>An array containing chunks of the input.</p>
--------
## str_split_pattern(string $str, string $pattern, int $limit): int


**Parameters:**
- string $str 
- string $pattern <p>The regex with which to split the string.</p>
- int $limit [optional] <p>Maximum number of results to return. Default: -1 === no limit</p>

**Return:**
- string[] <p>An array of strings.</p>
--------
## str_starts_with(string $haystack, string $needle): string


**Parameters:**
- string $haystack <p>The string to search in.</p>
- string $needle <p>The substring to search for.</p>

**Return:**
- bool 
--------
## str_starts_with_any(string $str, array $substrings): array
- case-sensitive

**Parameters:**
- string $str <p>The input string.</p>
- array $substrings <p>Substrings to look for.</p>

**Return:**
- bool whether or not $str starts with $substring
--------
## str_substr_after_first_separator(string $str, string $separator, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $separator <p>The string separator.</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>

**Return:**
- string 
--------
## str_substr_after_last_separator(string $str, string $separator, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $separator <p>The string separator.</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>

**Return:**
- string 
--------
## str_substr_before_first_separator(string $str, string $separator, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $separator <p>The string separator.</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>

**Return:**
- string 
--------
## str_substr_before_last_separator(string $str, string $separator, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $separator <p>The string separator.</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>

**Return:**
- string 
--------
## str_substr_first(string $str, string $needle, bool $before_needle, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $needle <p>The string to look for.</p>
- bool $before_needle [optional] <p>Default: false</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>

**Return:**
- string 
--------
## str_substr_last(string $str, string $needle, bool $before_needle, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $needle <p>The string to look for.</p>
- bool $before_needle [optional] <p>Default: false</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>

**Return:**
- string 
--------
## str_surround(string $str, string $substring): string


**Parameters:**
- string $str 
- string $substring <p>The substring to add to both sides.</p>

**Return:**
- string <p>A string with the substring both prepended and appended.</p>
--------
## str_titleize(string $str, array|string[]|null $ignore, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length, bool $use_trim_first, string|null $word_define_chars): string|null
Also accepts an array, $ignore, allowing you to list words not to be
capitalized.

**Parameters:**
- string $str 
- array|string[]|null $ignore [optional] <p>An array of words not to capitalize or
null. Default: null</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the
string.</p>
- string|null $lang [optional] <p>Set the language for special cases: az,
el, lt, tr</p>
- bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length:
e.g. ẞ -> ß</p>
- bool $use_trim_first [optional] <p>true === trim the input string,
first</p>
- string|null $word_define_chars [optional] <p>An string of chars that will be used as
whitespace separator === words.</p>

**Return:**
- string <p>The titleized string.</p>
--------
## str_titleize_for_humans(string $str, array $ignore, string $encoding): string
Also accepts an array, $ignore, allowing you to list words not to be
capitalized.

Adapted from John Gruber's script.

**Parameters:**
- string $str 
- array $ignore <p>An array of words not to capitalize.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string <p>The titleized string.</p>
--------
## str_to_binary(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- bool|string <p>false on error</p>
--------
## str_to_lines(string $str, bool $remove_empty_values, int|null $remove_short_values): int|null


**Parameters:**
- string $str 
- bool $remove_empty_values <p>Remove empty values.</p>
- int|null $remove_short_values <p>The min. string length or null to disable</p>

**Return:**
- string[] 
--------
## str_to_words(string $str, string $char_list, bool $remove_empty_values, int|null $remove_short_values): int|null


**Parameters:**
- string $str 
- string $char_list <p>Additional chars for the definition of "words".</p>
- bool $remove_empty_values <p>Remove empty values.</p>
- int|null $remove_short_values <p>The min. string length or null to disable</p>

**Return:**
- string[] 
--------
## str_transliterate(string $str, string $unknown, bool $strict): bool


**Parameters:**
- string $str 
- string $unknown 
- bool $strict 

**Return:**
- string 
--------
## str_truncate(string $str, int $length, string $substring, string $encoding): string


**Parameters:**
- string $str 
- int $length <p>Desired length of the truncated string.</p>
- string $substring [optional] <p>The substring to append if it can fit. Default: ''</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>

**Return:**
- string <p>A string after truncating.</p>
--------
## str_truncate_safe(string $str, int $length, string $substring, string $encoding, bool $ignore_do_not_split_words_for_one_word): bool


**Parameters:**
- string $str 
- int $length <p>Desired length of the truncated string.</p>
- string $substring [optional] <p>The substring to append if it can fit.
Default:
''</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>
- bool $ignore_do_not_split_words_for_one_word [optional] <p>Default: false</p>

**Return:**
- string <p>A string after truncating.</p>
--------
## str_underscored(string $str): string
Underscores are inserted before uppercase characters (with the exception
of the first character of the string), and in place of spaces as well as
dashes.

**Parameters:**
- string $str 

**Return:**
- string <p>The underscored string.</p>
--------
## str_upper_camelize(string $str, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): bool


**Parameters:**
- string $str <p>The input string.</p>
- string $encoding [optional] <p>Default: 'UTF-8'</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
- string|null $lang [optional] <p>Set the language for special cases: az, el, lt,
tr</p>
- bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
-> ß</p>

**Return:**
- string <p>A string in UpperCamelCase.</p>
--------
## str_upper_first(string $str, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): bool


**Parameters:**
- string $str 
- string $encoding 
- bool $clean_utf8 
- string|null $lang 
- bool $try_to_keep_the_string_length 

**Return:**
- string 
--------
## str_word_count(string $str, int $format, string $char_list): string


**Parameters:**
- string $str <p>The input string.</p>
- int $format [optional] <p>
<strong>0</strong> => return a number of words (default)<br>
<strong>1</strong> => return an array of words<br>
<strong>2</strong> => return an array of words with word-offset as key
</p>
- string $char_list [optional] <p>Additional chars that contains to words and do not start a new word.</p>

**Return:**
- int|string[] <p>The number of words in the string.</p>
--------
## strcasecmp(string $str1, string $str2, string $encoding): string
INFO: Case-insensitive version of UTF8::strcmp()

**Parameters:**
- string $str1 <p>The first string.</p>
- string $str2 <p>The second string.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- int <strong>&lt; 0</strong> if str1 is less than str2;<br>
<strong>&gt; 0</strong> if str1 is greater than str2,<br>
<strong>0</strong> if they are equal
--------
## strchr(string $haystack, string $needle, bool $before_needle, string $encoding, bool $clean_utf8): bool


**Parameters:**
- string $haystack 
- string $needle 
- bool $before_needle 
- string $encoding 
- bool $clean_utf8 

**Return:**
- bool|string 
--------
## strcmp(string $str1, string $str2): string


**Parameters:**
- string $str1 <p>The first string.</p>
- string $str2 <p>The second string.</p>

**Return:**
- int <strong>&lt; 0</strong> if str1 is less than str2<br>
<strong>&gt; 0</strong> if str1 is greater than str2<br>
<strong>0</strong> if they are equal
--------
## strcspn(string $str, string $char_list, int $offset, int $length, string $encoding): string


**Parameters:**
- string $str 
- string $char_list 
- int $offset 
- int $length 
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- int 
--------
## strichr(string $haystack, string $needle, bool $before_needle, string $encoding, bool $clean_utf8): bool


**Parameters:**
- string $haystack 
- string $needle 
- bool $before_needle 
- string $encoding 
- bool $clean_utf8 

**Return:**
- bool|string 
--------
## string(array $array): array
INFO: opposite to UTF8::codepoints()

**Parameters:**
- array $array <p>Integer or Hexadecimal codepoints.</p>

**Return:**
- string <p>A UTF-8 encoded string.</p>
--------
## string_has_bom(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- bool <strong>true</strong> if the string has BOM at the start,<br>
<strong>false</strong> otherwise
--------
## strip_tags(string $str, string $allowable_tags, bool $clean_utf8): bool


**Parameters:**
- string $str <p>
The input string.
</p>
- string $allowable_tags [optional] <p>
You can use the optional second parameter to specify tags which should
not be stripped.
</p>
<p>
HTML comments and PHP tags are also stripped. This is hardcoded and
can not be changed with allowable_tags.
</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>

**Return:**
- string <p>The stripped string.</p>
--------
## strip_whitespace(string $str): string


**Parameters:**
- string $str 

**Return:**
- string 
--------
## stripos(string $haystack, string $needle, int $offset, string $encoding, bool $clean_utf8): bool


**Parameters:**
- string $haystack <p>The string from which to get the position of the first occurrence of needle.</p>
- string $needle <p>The string to find in haystack.</p>
- int $offset [optional] <p>The position in haystack to start searching.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>

**Return:**
- bool|int Return the <strong>(int)</strong> numeric position of the first occurrence of needle in the
haystack string,<br> or <strong>false</strong> if needle is not found
--------
## stripos_in_byte(string $haystack, string $needle, int $offset): int


**Parameters:**
- string $haystack <p>
The string being checked.
</p>
- string $needle <p>
The position counted from the beginning of haystack.
</p>
- int $offset [optional] <p>
The search offset. If it is not specified, 0 is used.
</p>

**Return:**
- bool|int <p>The numeric position of the first occurrence of needle in the
haystack string. If needle is not found, it returns false.</p>
--------
## stristr(string $haystack, string $needle, bool $before_needle, string $encoding, bool $clean_utf8): bool


**Parameters:**
- string $haystack <p>The input string. Must be valid UTF-8.</p>
- string $needle <p>The string to look for. Must be valid UTF-8.</p>
- bool $before_needle [optional] <p>
If <b>TRUE</b>, it returns the part of the
haystack before the first occurrence of the needle (excluding the needle).
</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>

**Return:**
- bool|string <p>A sub-string,<br>or <strong>false</strong> if needle is not found.</p>
--------
## strlen(string $str, string $encoding, bool $clean_utf8): bool


**Parameters:**
- string $str <p>The string being checked for length.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>

**Return:**
- bool|int <p>
The number <strong>(int)</strong> of characters in the string $str having character encoding
$encoding.
(One multi-byte character counted as +1).
<br>
Can return <strong>false</strong>, if e.g. mbstring is not installed and we process invalid
chars.
</p>
--------
## strlen_in_byte(string $str): string


**Parameters:**
- string $str 

**Return:**
- int 
--------
## strnatcasecmp(string $str1, string $str2, string $encoding): string
INFO: natural order version of UTF8::strcasecmp()

**Parameters:**
- string $str1 <p>The first string.</p>
- string $str2 <p>The second string.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- int <strong>&lt; 0</strong> if str1 is less than str2<br>
<strong>&gt; 0</strong> if str1 is greater than str2<br>
<strong>0</strong> if they are equal
--------
## strnatcmp(string $str1, string $str2): string
INFO: natural order version of UTF8::strcmp()

**Parameters:**
- string $str1 <p>The first string.</p>
- string $str2 <p>The second string.</p>

**Return:**
- int <strong>&lt; 0</strong> if str1 is less than str2;<br>
<strong>&gt; 0</strong> if str1 is greater than str2;<br>
<strong>0</strong> if they are equal
--------
## strncasecmp(string $str1, string $str2, int $len, string $encoding): string


**Parameters:**
- string $str1 <p>The first string.</p>
- string $str2 <p>The second string.</p>
- int $len <p>The length of strings to be used in the comparison.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- int <strong>&lt; 0</strong> if <i>str1</i> is less than <i>str2</i>;<br>
<strong>&gt; 0</strong> if <i>str1</i> is greater than <i>str2</i>;<br>
<strong>0</strong> if they are equal
--------
## strncmp(string $str1, string $str2, int $len, string $encoding): string


**Parameters:**
- string $str1 <p>The first string.</p>
- string $str2 <p>The second string.</p>
- int $len <p>Number of characters to use in the comparison.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- int <strong>&lt; 0</strong> if <i>str1</i> is less than <i>str2</i>;<br>
<strong>&gt; 0</strong> if <i>str1</i> is greater than <i>str2</i>;<br>
<strong>0</strong> if they are equal
--------
## strpbrk(string $haystack, string $char_list): string


**Parameters:**
- string $haystack <p>The string where char_list is looked for.</p>
- string $char_list <p>This parameter is case-sensitive.</p>

**Return:**
- bool|string <p>The string starting from the character found, or false if it is not found.</p>
--------
## strpos(string $haystack, int|string $needle, int $offset, string $encoding, bool $clean_utf8): bool


**Parameters:**
- string $haystack <p>The string from which to get the position of the first occurrence of needle.</p>
- int|string $needle <p>The string to find in haystack.<br>Or a code point as int.</p>
- int $offset [optional] <p>The search offset. If it is not specified, 0 is used.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>

**Return:**
- bool|int The <strong>(int)</strong> numeric position of the first occurrence of needle in the haystack
string.<br> If needle is not found it returns false.
--------
## strpos_in_byte(string $haystack, string $needle, int $offset): int


**Parameters:**
- string $haystack <p>
The string being checked.
</p>
- string $needle <p>
The position counted from the beginning of haystack.
</p>
- int $offset [optional] <p>
The search offset. If it is not specified, 0 is used.
</p>

**Return:**
- bool|int <p>The numeric position of the first occurrence of needle in the
haystack string. If needle is not found, it returns false.</p>
--------
## strrchr(string $haystack, string $needle, bool $before_needle, string $encoding, bool $clean_utf8): bool


**Parameters:**
- string $haystack <p>The string from which to get the last occurrence of needle.</p>
- string $needle <p>The string to find in haystack</p>
- bool $before_needle [optional] <p>
Determines which portion of haystack
this function returns.
If set to true, it returns all of haystack
from the beginning to the last occurrence of needle.
If set to false, it returns all of haystack
from the last occurrence of needle to the end,
</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>

**Return:**
- bool|string <p>The portion of haystack or false if needle is not found.</p>
--------
## strrev(string $str, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string <p>The string with characters in the reverse sequence.</p>
--------
## strrichr(string $haystack, string $needle, bool $before_needle, string $encoding, bool $clean_utf8): bool


**Parameters:**
- string $haystack <p>The string from which to get the last occurrence of needle.</p>
- string $needle <p>The string to find in haystack.</p>
- bool $before_needle [optional] <p>
Determines which portion of haystack
this function returns.
If set to true, it returns all of haystack
from the beginning to the last occurrence of needle.
If set to false, it returns all of haystack
from the last occurrence of needle to the end,
</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>

**Return:**
- bool|string <p>The portion of haystack or<br>false if needle is not found.</p>
--------
## strripos(string $haystack, int|string $needle, int $offset, string $encoding, bool $clean_utf8): bool


**Parameters:**
- string $haystack <p>The string to look in.</p>
- int|string $needle <p>The string to look for.</p>
- int $offset [optional] <p>Number of characters to ignore in the beginning or end.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>

**Return:**
- bool|int <p>The <strong>(int)</strong> numeric position of the last occurrence of needle in the haystack
string.<br>If needle is not found, it returns false.</p>
--------
## strripos_in_byte(string $haystack, string $needle, int $offset): int


**Parameters:**
- string $haystack <p>
The string from which to get the position of the last occurrence
of needle.
</p>
- string $needle <p>
The string to find in haystack.
</p>
- int $offset [optional] <p>
The position in haystack
to start searching.
</p>

**Return:**
- bool|int <p>eturn the numeric position of the last occurrence of needle in the
haystack string, or false if needle is not found.</p>
--------
## strrpos(string $haystack, int|string $needle, int $offset, string $encoding, bool $clean_utf8): bool


**Parameters:**
- string $haystack <p>The string being checked, for the last occurrence of needle</p>
- int|string $needle <p>The string to find in haystack.<br>Or a code point as int.</p>
- int $offset [optional] <p>May be specified to begin searching an arbitrary number of characters
into the string. Negative values will stop searching at an arbitrary point prior to
the end of the string.
</p>
- string $encoding [optional] <p>Set the charset.</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>

**Return:**
- bool|int <p>The <strong>(int)</strong> numeric position of the last occurrence of needle in the haystack
string.<br>If needle is not found, it returns false.</p>
--------
## strrpos_in_byte(string $haystack, string $needle, int $offset): int


**Parameters:**
- string $haystack <p>
The string being checked, for the last occurrence
of needle.
</p>
- string $needle <p>
The string to find in haystack.
</p>
- int $offset [optional] <p>May be specified to begin searching an arbitrary number of characters into
the string. Negative values will stop searching at an arbitrary point
prior to the end of the string.
</p>

**Return:**
- bool|int <p>The numeric position of the last occurrence of needle in the
haystack string. If needle is not found, it returns false.</p>
--------
## strspn(string $str, string $mask, int $offset, int $length, string $encoding): string


**Parameters:**
- string $str <p>The input string.</p>
- string $mask <p>The mask of chars</p>
- int $offset [optional]
- int $length [optional]
- string $encoding [optional] <p>Set the charset.</p>

**Return:**
- bool|int 
--------
## strstr(string $haystack, string $needle, bool $before_needle, string $encoding, bool $clean_utf8): bool


**Parameters:**
- string $haystack <p>The input string. Must be valid UTF-8.</p>
- string $needle <p>The string to look for. Must be valid UTF-8.</p>
- bool $before_needle [optional] <p>
If <b>TRUE</b>, strstr() returns the part of the
haystack before the first occurrence of the needle (excluding the needle).
</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>

**Return:**
- bool|string A sub-string,<br>or <strong>false</strong> if needle is not found
--------
## strstr_in_byte(string $haystack, string $needle, bool $before_needle): bool


**Parameters:**
- string $haystack <p>
The string from which to get the first occurrence
of needle.
</p>
- string $needle <p>
The string to find in haystack.
</p>
- bool $before_needle [optional] <p>
Determines which portion of haystack
this function returns.
If set to true, it returns all of haystack
from the beginning to the first occurrence of needle.
If set to false, it returns all of haystack
from the first occurrence of needle to the end,
</p>

**Return:**
- bool|string <p>The portion of haystack,
or false if needle is not found.</p>
--------
## strtocasefold(string $str, bool $full, bool $clean_utf8, string $encoding, string|null $lang, bool $lower): bool


**Parameters:**
- string $str <p>The input string.</p>
- bool $full [optional] <p>
<b>true</b>, replace full case folding chars (default)<br>
<b>false</b>, use only limited static array [UTF8::$COMMON_CASE_FOLD]
</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
- string $encoding [optional] <p>Set the charset.</p>
- string|null $lang [optional] <p>Set the language for special cases: az, el, lt, tr</p>
- bool $lower [optional] <p>Use lowercase string, otherwise use uppercase string. PS: uppercase
is for some languages better ...</p>

**Return:**
- string 
--------
## strtolower(string $str, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): bool


**Parameters:**
- string $str <p>The string being lowercased.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
- string|null $lang [optional] <p>Set the language for special cases: az, el, lt,
tr</p>
- bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
-> ß</p>

**Return:**
- string <p>String with all alphabetic characters converted to lowercase.</p>
--------
## strtoupper(string $str, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): bool


**Parameters:**
- string $str <p>The string being uppercased.</p>
- string $encoding [optional] <p>Set the charset.</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
- string|null $lang [optional] <p>Set the language for special cases: az, el, lt,
tr</p>
- bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
-> ß</p>

**Return:**
- string <p>String with all alphabetic characters converted to uppercase.</p>
--------
## strtr(string $str, string|string[] $from, string|string[] $to): string|string[]
<p>
<br>
Examples:
<br>
<br>
<code>
UTF8::strtr(string $str, string $from, string $to): string
</code>
<br><br>
<code>
UTF8::strtr(string $str, array $replace_pairs): string
</code>
</p>

**Parameters:**
- string $str <p>The string being translated.</p>
- string|string[] $from <p>The string replacing from.</p>
- string|string[] $to [optional] <p>The string being translated to to.</p>

**Return:**
- string <p>This function returns a copy of str, translating all occurrences of each character in "from"
to the corresponding character in "to".</p>
--------
## strwidth(string $str, string $encoding, bool $clean_utf8): bool


**Parameters:**
- string $str <p>The input string.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>

**Return:**
- int 
--------
## substr(string $str, int $offset, int $length, string $encoding, bool $clean_utf8): bool


**Parameters:**
- string $str <p>The string being checked.</p>
- int $offset <p>The first position used in str.</p>
- int $length [optional] <p>The maximum length of the returned string.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>

**Return:**
- bool|string The portion of <i>str</i> specified by the <i>offset</i> and
<i>length</i> parameters.</p><p>If <i>str</i> is shorter than <i>offset</i>
characters long, <b>FALSE</b> will be returned.
--------
## substr_compare(string $str1, string $str2, int $offset, int|null $length, bool $case_insensitivity, string $encoding): string


**Parameters:**
- string $str1 <p>The main string being compared.</p>
- string $str2 <p>The secondary string being compared.</p>
- int $offset [optional] <p>The start position for the comparison. If negative, it starts
counting from the end of the string.</p>
- int|null $length [optional] <p>The length of the comparison. The default value is the largest
of the length of the str compared to the length of main_str less the
offset.</p>
- bool $case_insensitivity [optional] <p>If case_insensitivity is TRUE, comparison is case
insensitive.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- int <strong>&lt; 0</strong> if str1 is less than str2;<br>
<strong>&gt; 0</strong> if str1 is greater than str2,<br>
<strong>0</strong> if they are equal
--------
## substr_count(string $haystack, string $needle, int $offset, int $length, string $encoding, bool $clean_utf8): bool


**Parameters:**
- string $haystack <p>The string to search in.</p>
- string $needle <p>The substring to search for.</p>
- int $offset [optional] <p>The offset where to start counting.</p>
- int $length [optional] <p>
The maximum length after the specified offset to search for the
substring. It outputs a warning if the offset plus the length is
greater than the haystack length.
</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>

**Return:**
- bool|int <p>This functions returns an integer or false if there isn't a string.</p>
--------
## substr_count_in_byte(string $haystack, string $needle, int $offset, int $length): int


**Parameters:**
- string $haystack <p>
The string being checked.
</p>
- string $needle <p>
The string being found.
</p>
- int $offset [optional] <p>
The offset where to start counting
</p>
- int $length [optional] <p>
The maximum length after the specified offset to search for the
substring. It outputs a warning if the offset plus the length is
greater than the haystack length.
</p>

**Return:**
- bool|int <p>The number of times the
needle substring occurs in the
haystack string.</p>
--------
## substr_count_simple(string $str, string $substring, bool $case_sensitive, string $encoding): string
By default, the comparison is case-sensitive, but can be made insensitive
by setting $case_sensitive to false.

**Parameters:**
- string $str <p>The input string.</p>
- string $substring <p>The substring to search for.</p>
- bool $case_sensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- int 
--------
## substr_ileft(string $haystack, string $needle): string


**Parameters:**
- string $haystack <p>The string to search in.</p>
- string $needle <p>The substring to search for.</p>

**Return:**
- string <p>Return the sub-string.</p>
--------
## substr_in_byte(string $str, int $offset, int $length): int


**Parameters:**
- string $str <p>The string being checked.</p>
- int $offset <p>The first position used in str.</p>
- int $length [optional] <p>The maximum length of the returned string.</p>

**Return:**
- bool|string The portion of <i>str</i> specified by the <i>offset</i> and
<i>length</i> parameters.</p><p>If <i>str</i> is shorter than <i>offset</i>
characters long, <b>FALSE</b> will be returned.
--------
## substr_iright(string $haystack, string $needle): string


**Parameters:**
- string $haystack <p>The string to search in.</p>
- string $needle <p>The substring to search for.</p>

**Return:**
- string <p>Return the sub-string.<p>
--------
## substr_left(string $haystack, string $needle): string


**Parameters:**
- string $haystack <p>The string to search in.</p>
- string $needle <p>The substring to search for.</p>

**Return:**
- string <p>Return the sub-string.</p>
--------
## substr_replace(string|string[] $str, string|string[] $replacement, int|int[] $offset, int|int[]|null $length, string $encoding): string
source: https://gist.github.com/stemar/8287074

**Parameters:**
- string|string[] $str <p>The input string or an array of stings.</p>
- string|string[] $replacement <p>The replacement string or an array of stings.</p>
- int|int[] $offset <p>
If start is positive, the replacing will begin at the start'th offset
into string.
<br><br>
If start is negative, the replacing will begin at the start'th character
from the end of string.
</p>
- int|int[]|null $length [optional] <p>If given and is positive, it represents the length of the
portion of string which is to be replaced. If it is negative, it
represents the number of characters from the end of string at which to
stop replacing. If it is not given, then it will default to strlen(
string ); i.e. end the replacing at the end of string. Of course, if
length is zero then this function will have the effect of inserting
replacement into string at the given start offset.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string|string[] <p>The result string is returned. If string is an array then array is returned.</p>
--------
## substr_right(string $haystack, string $needle, string $encoding): string


**Parameters:**
- string $haystack <p>The string to search in.</p>
- string $needle <p>The substring to search for.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>

**Return:**
- string <p>Return the sub-string.</p>
--------
## swapCase(string $str, string $encoding, bool $clean_utf8): bool


**Parameters:**
- string $str <p>The input string.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>

**Return:**
- string <p>Each character's case swapped.</p>
--------
## symfony_polyfill_used(): bool


**Parameters:**
__nothing__

**Return:**
- bool <strong>true</strong> if in use, <strong>false</strong> otherwise
--------
## tabs_to_spaces(string $str, int $tab_length): int


**Parameters:**
- string $str 
- int $tab_length 

**Return:**
- string 
--------
## titlecase(string $str, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): bool


**Parameters:**
- string $str <p>The input string.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
- string|null $lang [optional] <p>Set the language for special cases: az, el, lt,
tr</p>
- bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
-> ß</p>

**Return:**
- string <p>A string with all characters of $str being title-cased.</p>
--------
## toAscii(string $str, string $subst_chr, bool $strict): bool


**Parameters:**
- string $str 
- string $subst_chr 
- bool $strict 

**Return:**
- string 
--------
## toIso8859(string|string[] $str): string|string[]


**Parameters:**
- string|string[] $str 

**Return:**
- string|string[] 
--------
## toLatin1(string|string[] $str): string|string[]


**Parameters:**
- string|string[] $str 

**Return:**
- string|string[] 
--------
## toUTF8(string|string[] $str): string|string[]


**Parameters:**
- string|string[] $str 

**Return:**
- string|string[] 
--------
## to_ascii(string $str, string $unknown, bool $strict): bool


**Parameters:**
- string $str <p>The input string.</p>
- string $unknown [optional] <p>Character use if character unknown. (default is ?)</p>
- bool $strict [optional] <p>Use "transliterator_transliterate()" from PHP-Intl | WARNING: bad
performance</p>

**Return:**
- string 
--------
## to_boolean(mixed $str): mixed


**Parameters:**
- mixed $str 

**Return:**
- bool 
--------
## to_filename(string $str, bool $use_transliterate, string $fallback_char): string


**Parameters:**
- string $str 
- bool $use_transliterate No transliteration, conversion etc. is done by default - unsafe characters are
simply replaced with hyphen.
- string $fallback_char 

**Return:**
- string 
--------
## to_int(string $str): string


**Parameters:**
- string $str 

**Return:**
- int|null <p>null if the string isn't numeric</p>
--------
## to_iso8859(string|string[] $str): string|string[]


**Parameters:**
- string|string[] $str 

**Return:**
- string|string[] 
--------
## to_latin1(string|string[] $str): string|string[]


**Parameters:**
- string|string[] $str 

**Return:**
- string|string[] 
--------
## to_string(mixed $input): mixed


**Parameters:**
- mixed $input 

**Return:**
- string|null <p>null if the input isn't int|float|string and has no "__toString()" method</p>
--------
## to_utf8(string|string[] $str, bool $decode_html_entity_to_utf8): bool
<ul>
<li>It decode UTF-8 codepoints and Unicode escape sequences.</li>
<li>It assumes that the encoding of the original string is either WINDOWS-1252 or ISO-8859.</li>
<li>WARNING: It does not remove invalid UTF-8 characters, so you maybe need to use "UTF8::clean()" for this
case.</li>
</ul>

**Parameters:**
- string|string[] $str <p>Any string or array.</p>
- bool $decode_html_entity_to_utf8 <p>Set to true, if you need to decode html-entities.</p>

**Return:**
- string|string[] <p>The UTF-8 encoded string</p>
--------
## trim(string $str, string|null $chars): string|null
INFO: This is slower then "trim()"

We can only use the original-function, if we use <= 7-Bit in the string / chars
but the check for ASCII (7-Bit) cost more time, then we can safe here.

**Parameters:**
- string $str <p>The string to be trimmed</p>
- string|null $chars [optional] <p>Optional characters to be stripped</p>

**Return:**
- string <p>The trimmed string.</p>
--------
## ucfirst(string $str, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): bool


**Parameters:**
- string $str <p>The input string.</p>
- string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
- string|null $lang [optional] <p>Set the language for special cases: az, el, lt,
tr</p>
- bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
-> ß</p>

**Return:**
- string <p>The resulting string with with char uppercase.</p>
--------
## ucword(string $str, string $encoding, bool $clean_utf8): bool


**Parameters:**
- string $str 
- string $encoding 
- bool $clean_utf8 

**Return:**
- string 
--------
## ucwords(string $str, string[] $exceptions, string $char_list, string $encoding, bool $clean_utf8): bool


**Parameters:**
- string $str <p>The input string.</p>
- string[] $exceptions [optional] <p>Exclusion for some words.</p>
- string $char_list [optional] <p>Additional chars that contains to words and do not start a new
word.</p>
- string $encoding [optional] <p>Set the charset.</p>
- bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>

**Return:**
- string 
--------
## urldecode(string $str, bool $multi_decode): bool
e.g:
'test+test'                     => 'test test'
'D&#252;sseldorf'               => 'Düsseldorf'
'D%FCsseldorf'                  => 'Düsseldorf'
'D&#xFC;sseldorf'               => 'Düsseldorf'
'D%26%23xFC%3Bsseldorf'         => 'Düsseldorf'
'DÃ¼sseldorf'                   => 'Düsseldorf'
'D%C3%BCsseldorf'               => 'Düsseldorf'
'D%C3%83%C2%BCsseldorf'         => 'Düsseldorf'
'D%25C3%2583%25C2%25BCsseldorf' => 'Düsseldorf'

**Parameters:**
- string $str <p>The input string.</p>
- bool $multi_decode <p>Decode as often as possible.</p>

**Return:**
- string 
--------
## urldecode_fix_win1252_chars(): bool


**Parameters:**
__nothing__

**Return:**
- string[] 
--------
## utf8_decode(string $str, bool $keep_utf8_chars): bool


**Parameters:**
- string $str <p>The input string.</p>
- bool $keep_utf8_chars 

**Return:**
- string 
--------
## utf8_encode(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- string 
--------
## utf8_fix_win1252_chars(string $str): string


**Parameters:**
- string $str <p>The input string.</p>

**Return:**
- string 
--------
## whitespace_table(): string


**Parameters:**
__nothing__

**Return:**
- string[] An array with all known whitespace characters as values and the type of whitespace as keys
as defined in above URL
--------
## words_limit(string $str, int $limit, string $str_add_on): string


**Parameters:**
- string $str <p>The input string.</p>
- int $limit <p>The limit of words as integer.</p>
- string $str_add_on <p>Replacement for the striped string.</p>

**Return:**
- string 
--------
## wordwrap(string $str, int $width, string $break, bool $cut): bool


**Parameters:**
- string $str <p>The input string.</p>
- int $width [optional] <p>The column width.</p>
- string $break [optional] <p>The line is broken using the optional break parameter.</p>
- bool $cut [optional] <p>
If the cut is set to true, the string is
always wrapped at or before the specified width. So if you have
a word that is larger than the given width, it is broken apart.
</p>

**Return:**
- string <p>The given string wrapped at the specified column.</p>
--------
## wordwrap_per_line(string $str, int $width, string $break, bool $cut, bool $add_final_break, string|null $delimiter): string|null


**Parameters:**
- string $str <p>The input string.</p>
- int $width [optional] <p>The column width.</p>
- string $break [optional] <p>The line is broken using the optional break parameter.</p>
- bool $cut [optional] <p>
If the cut is set to true, the string is
always wrapped at or before the specified width. So if you have
a word that is larger than the given width, it is broken apart.
</p>
- bool $add_final_break [optional] <p>
If this flag is true, then the method will add a $break at the end
of the result string.
</p>
- string|null $delimiter [optional] <p>
You can change the default behavior, where we split the string by newline.
</p>

**Return:**
- string 
--------
## ws(): string|null


**Parameters:**
__nothing__

**Return:**
- string[] <p>An array with numeric code point as key and White Space Character as value.</p>
--------


## Unit Test

1) [Composer](https://getcomposer.org) is a prerequisite for running the tests.

```
composer install
```

2) The tests can be executed by running this command from the root directory:

```bash
./vendor/bin/phpunit
```

### Support

For support and donations please visit [GitHub](https://github.com/voku/portable-utf8/) | [Issues](https://github.com/voku/portable-utf8/issues) | [PayPal](https://paypal.me/moelleken) | [Patreon](https://www.patreon.com/voku).

For status updates and release announcements please visit [Releases](https://github.com/voku/portable-utf8/releases) | [Twitter](https://twitter.com/suckup_de) | [Patreon](https://www.patreon.com/voku/posts).

For professional support please contact [me](https://about.me/voku).

### Thanks

- Thanks to [GitHub](https://github.com) (Microsoft) for hosting the code and a good infrastructure including Issues-Management, etc.
- Thanks to [IntelliJ](https://www.jetbrains.com) as they make the best IDEs for PHP and they gave me an open source license for PhpStorm!
- Thanks to [Travis CI](https://travis-ci.com/) for being the most awesome, easiest continuous integration tool out there!
- Thanks to [StyleCI](https://styleci.io/) for the simple but powerful code style check.
- Thanks to [PHPStan](https://github.com/phpstan/phpstan) && [Psalm](https://github.com/vimeo/psalm) for really great Static analysis tools and for discovering bugs in the code!

### License and Copyright

"Portable UTF8" is free software; you can redistribute it and/or modify it under
the terms of the (at your option):
- [Apache License v2.0](http://apache.org/licenses/LICENSE-2.0.txt), or
- [GNU General Public License v2.0](http://gnu.org/licenses/gpl-2.0.txt).

Unicode handling requires tedious work to be implemented and maintained on the
long run. As such, contributions such as unit tests, bug reports, comments or
patches licensed under both licenses are really welcomed.


[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fvoku%2Fportable-utf8.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Fvoku%2Fportable-utf8?ref=badge_large)
