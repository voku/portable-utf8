<?php

if (version_compare(PHP_VERSION, '7.0', '>=')) { // "strict_types=1" === warning with PHP < 7.0
  error_reporting(E_ALL);
}
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';
