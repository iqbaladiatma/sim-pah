<?php
$folders = ['app', 'resources/js', 'routes', 'database/migrations'];
$types = ['*.php', '*.vue', '*.js', '*.css'];
$totalLines = 0;
$stats = [];

foreach ($folders as $folder) {
    if (!is_dir(__DIR__ . '/' . $folder)) continue;
    $folderLines = 0;
    foreach ($types as $type) {
        $files = glob_recursive(__DIR__ . '/' . $folder . '/' . $type);
        foreach ($files as $file) {
            $lines = count(file($file));
            $folderLines += $lines;
            $totalLines += $lines;
        }
    }
    $stats[$folder] = $folderLines;
}

echo "LOC Summary:\n";
foreach ($stats as $folder => $lines) {
    echo "$folder: $lines lines\n";
}
echo "Total Codebase (excluding vendor/modules): $totalLines lines\n";

function glob_recursive($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}
