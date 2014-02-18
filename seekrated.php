<?php

/**
 * This script extracts exif rating system of Canon RAW and copy all photo
 * above a given rating in a given target directory
 */
require_once __DIR__ . '/vendor/autoload.php';

define('EXIF_META', 'ExtensibleMetadataPlatform');

$rootDir = $argv[1];
$targetDir = $argv[2];
$threshold = isset($argv[3]) ? $argv[3] : 1;

$cursor = new \Symfony\Component\Finder\Finder();
$cursor->name('*.CR2')
        ->files()
        ->in($rootDir);

foreach ($cursor as $fch) {
    $stat = @exif_read_data($fch);
    if (array_key_exists(EXIF_META, $stat)) {
        $tree = simplexml_load_string($stat[EXIF_META]);
        $tree->registerXPathNamespace('xmp', 'http://ns.adobe.com/xap/1.0/');
        $val = $tree->xpath('//xmp:Rating');
        if (count($val)) {
            $rating = $val[0];
            if ($rating >= $threshold) {
                echo $fch . '=' . $rating . PHP_EOL;
            }
        }
    }
}