<?php

static $data = array(
    "\xc2\xab"     => '"', // « (U+00AB) in UTF-8
    "\xc2\xbb"     => '"', // » (U+00BB) in UTF-8
    "\xe2\x80\x98" => "'", // ‘ (U+2018) in UTF-8
    "\xe2\x80\x99" => "'", // ’ (U+2019) in UTF-8
    "\xe2\x80\x9a" => "'", // ‚ (U+201A) in UTF-8
    "\xe2\x80\x9b" => "'", // ‛ (U+201B) in UTF-8
    "\xe2\x80\x9c" => '"', // “ (U+201C) in UTF-8
    "\xe2\x80\x9d" => '"', // ” (U+201D) in UTF-8
    "\xe2\x80\x9e" => '"', // „ (U+201E) in UTF-8
    "\xe2\x80\x9f" => '"', // ‟ (U+201F) in UTF-8
    "\xe2\x80\xb9" => "'", // ‹ (U+2039) in UTF-8
    "\xe2\x80\xba" => "'", // › (U+203A) in UTF-8
    "\xe2\x80\x93" => '-', // – (U+2013) in UTF-8
    "\xe2\x80\x94" => '-', // — (U+2014) in UTF-8
    "\xe2\x80\xa6" => '...' // … (U+2026) in UTF-8
);

$result =& $data;
unset($data);
return $result;
