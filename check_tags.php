<?php
$file = 'c:/laragon/www/sim-pah/resources/js/Pages/Admin/Procedures/Show.vue';
$content = file_get_contents($file);

// Extract the <thead> block
if (preg_match('/<thead>(.*?)<\/thead>/s', $content, $matches, PREG_OFFSET_CAPTURE)) {
    $theadContent = $matches[1][0];
    $startOffset = $matches[0][1];

    // Find all opening and closing tags
    preg_match_all('/<([a-zA-Z0-9\-]+)(?:\s.*?)?>|<\/([a-zA-Z0-9\-]+)>/s', $theadContent, $tags, PREG_OFFSET_CAPTURE);

    $stack = [];
    foreach ($tags[0] as $i => $fullTag) {
        $tagName = strtolower($tags[1][$i] ?: $tags[2][$i]);
        $isClosing = (bool) $tags[2][$i];
        $offset = $tags[0][$i][1] + $startOffset; // Adjusted offset relative to file start

        // Skip void elements
        if (in_array($tagName, ['img', 'br', 'hr', 'input', 'link', 'meta']))
            continue;

        if (!$isClosing) {
            $stack[] = ['tag' => $tagName, 'offset' => $offset];
        } else {
            if (empty($stack)) {
                echo "Error: Unexpected closing tag </$tagName> at offset $offset\n";
            } else {
                $last = array_pop($stack);
                if ($last['tag'] !== $tagName) {
                    echo "Error: Mismatched tag </$tagName> at offset $offset (expected </{$last['tag']}> opened at {$last['offset']})\n";
                }
            }
        }
    }

    if (!empty($stack)) {
        foreach ($stack as $s) {
            echo "Error: Unclosed tag <{$s['tag']}> opened at offset {$s['offset']}\n";
        }
    } else {
        echo "No tag mismatch found in thead.\n";
    }
} else {
    echo "Could not find thead check.\n";
}
