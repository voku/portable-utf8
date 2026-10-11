<?php

// Preserve mapping order and values; no compressor or new runtime dependency.
$write = in_array('--write', $argv, true);
$root = dirname(__DIR__, 2) . '/src/voku/helper/data/';
// Emoji generation/import is owned by portable-utf8-emoji-data.
$outputs = [
    'chr.php' => "<?php return \\array_map('chr', \\range(0, 255));\n",
    'ord.php' => "<?php return ['' => 0] + \\array_flip(\\array_map('chr', \\range(0, 255)));\n",
];
$changed = 0;
foreach ($outputs as $name => $contents) {
    $file = $root . $name;
    if ($contents !== file_get_contents($file)) {
        ++$changed;
        if ($write) {
            file_put_contents($file, $contents);
        }
    }
}
echo $changed . ' representations ' . ($write ? 'written' : 'need regeneration') . PHP_EOL;
exit(!$write && $changed !== 0 ? 1 : 0);
