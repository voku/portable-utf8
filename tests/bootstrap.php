<?php
if (is_file(dirname(__DIR__) . '/src/voku/helper/UTF8.php')) {
  require_once dirname(__DIR__) . '/src/voku/helper/UTF8.php';
  require_once dirname(__DIR__) . '/src/voku/helper/Bootup.php';
  require_once dirname(__DIR__) . '/src/voku/helper/shim/Iconv.php';
  require_once dirname(__DIR__) . '/src/voku/helper/shim/Intl.php';
  require_once dirname(__DIR__) . '/src/voku/helper/shim/Mbstring.php';
  require_once dirname(__DIR__) . '/src/voku/helper/shim/Normalizer.php';
  require_once dirname(__DIR__) . '/src/voku/helper/shim/Xml.php';
} else {
  require_once dirname(__DIR__) . '/vendor/composer/autoload_real.php';
}