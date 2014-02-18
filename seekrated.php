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

// for each entry :
foreach ($cursor as $fch) {
    $stat = @exif_read_data($fch);

    // there are metadatas :
    if (array_key_exists(EXIF_META, $stat)) {

        $timeIndex = $stat['DateTimeOriginal'];

        $tree = simplexml_load_string($stat[EXIF_META]);
        $tree->registerXPathNamespace('xmp', 'http://ns.adobe.com/xap/1.0/');
        $val = $tree->xpath('//xmp:Rating');

        // if there is a xml rating :
        if (count($val)) {
            $rating = (string) $val[0];
            if ($rating >= $threshold) {
                echo $fch . ' ' . str_repeat('*', $rating) . PHP_EOL;
                $filename = preg_replace('#[^\d]#', '-', $timeIndex);
                // copy the picture in the target directory with a new name
                copy($fch, $targetDir . DIRECTORY_SEPARATOR . 'IMG_' . $rating . '_' . $filename . '.CR2');
            }
        }
    }
}