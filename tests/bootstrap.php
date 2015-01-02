<?php
if (is_file(dirname(__DIR__) . '/src/voku/helper/UTF8.php')) {
  # for netbeans
  require_once dirname(__DIR__) . '/vendor/voku/urlify/URLify.php';
  require_once dirname(__DIR__) . '/src/voku/helper/UTF8.php';
  require_once dirname(__DIR__) . '/src/voku/helper/Bootup.php';
} else {
  # for travis-ci
  require_once dirname(__DIR__) . '/vendor/composer/autoload_real.php';
}