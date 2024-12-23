<?php

$infilePath = $argv[1];

$manifest = json_decode(file_get_contents($infilePath), true);
$manifest['version'] = getenv('VERSION');
$manifest['package'] = getenv('DOWNLOAD_URL');

echo json_encode($manifest, JSON_PRETTY_PRINT);
echo PHP_EOL;
