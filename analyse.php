<?php

$handle = fopen('stat.csv', 'r');
$header = fgetcsv($handle);
var_export($header);

$compil = [];
while (!feof($handle)) {
    $row = fgetcsv($handle);

    if (($row[2] == 2) && ($row[4] == 10)) {
        $row[4] = 85;
        $row[2] = "1.5";
    }

    if (($row[2] == 0) && ($row[4] == 0)) {
        $row[4] = 8;
        $row[2] = "6.3";
    }

    foreach ([
1 => 'Speed',
 2 => 'Aperture',
 3 => 'ISO',
 4 => 'Focal',
    ] as $idx => $stat) {
        $value = $row[$idx];
        if (array_key_exists($stat, $compil) && array_key_exists($value, $compil[$stat])) {
            $compil[$stat][$value]++;
        } else {
            $compil[$stat][$value] = 1;
        }
    }
}

fclose($handle);

$handle = fopen('result.csv', 'w');
foreach ($compil as $stat => $tab) {
    fputcsv($handle, [$stat, 'count']);
    foreach ($tab as $val => $cpt) {
        fputcsv($handle, [$val, $cpt]);
    }
}
fclose($handle);
