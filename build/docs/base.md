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

%__functions_index__voku\helper\UTF8__%

%__functions_list__voku\helper\UTF8__%


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
