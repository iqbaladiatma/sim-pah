<?php
$content = file_get_contents('resources/js/Pages/Admin/Procedures/Show.vue');

preg_match('/<thead>(.*?)<\/thead>/s', $content, $match, PREG_OFFSET_CAPTURE);
if (!$match)
    die("No thead found\n");

$thead = $match[1][0];
$thead_offset = $match[1][1];

// Simplified regex, not perfect but okay
preg_match_all('/<\/?([a-zA-Z0-9-]+)[^>]*>/', $thead, $tags, PREG_OFFSET_CAPTURE);

$stack = [];
$void = ['br', 'hr', 'img', 'input'];

foreach ($tags[0] as $i => $fulltag) {
    if (strpos($fulltag[0], '/>') !== false)
        continue; // self closing

    $tagname = strtolower($tags[1][$i][0]);
    if (in_array($tagname, $void))
        continue;

    $offset = $tags[0][$i][1] + $thead_offset;

    if (strpos($fulltag[0], '</') === 0) {
        // Closing tag
        if (empty($stack)) {
            echo "Unexpected closing tag </$tagname> at offset $offset\n";
            continue;
        }
        $last = array_pop($stack);
        if ($last['name'] !== $tagname) {
            echo "Mismatch: expected </{$last['name']}> (opened at {$last['offset']}), found </$tagname> at offset $offset\n";
        }
    } else {
        // Opening tag
        $stack[] = ['name' => $tagname, 'offset' => $offset];
    }
}

if (!empty($stack)) {
    foreach ($stack as $s) {
        echo "Unclosed <{$s['name']}> at offset {$s['offset']}\n";
        // Get context
        $ctx = substr($content, $s['offset'], 100);
        echo "Context: $ctx\n\n";
    }
} else {
    echo "thead valid.\n";
}
