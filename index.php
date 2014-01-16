<?php

require_once __DIR__ . '/vendor/autoload.php';

$rootDir = $argv[1];
$cursor = new \Symfony\Component\Finder\Finder();
$cursor->name('*.CR2')
        ->files()
        ->in($rootDir);

$maxIndexPerDay = [];
foreach ($cursor as $fch) {
    $matches = [];
    if (preg_match('#_(\d+)\.CR2#i', $fch->getBasename(), $matches)) {
        $photoIndex = $matches[1];
        $stat = @exif_read_data($fch);
        $photoTime = substr($stat['DateTimeOriginal'], 0, 10);
        if (!array_key_exists($photoTime, $maxIndexPerDay)) {
            $maxIndexPerDay[$photoTime] = $photoIndex;
        } else {
            $maxIndexPerDay[$photoTime] = max($photoIndex, $maxIndexPerDay[$photoTime]);
        }
    }
}

ksort($maxIndexPerDay);

$lastIndex = 0;
$resetCounter = 0;
foreach ($maxIndexPerDay as $day => $maxIndex) {
    if ($maxIndex < $lastIndex) {
        printf("---Reset---\n");
        $resetCounter++;
    }
    printf("%s => %d\n", $day, $maxIndex);
    $lastIndex = $maxIndex;
}

printf("Counter reseted %d times", $resetCounter);
