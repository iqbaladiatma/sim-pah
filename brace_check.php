<?php
$file = 'app/Exports/IsoProcedureExport.php';
$content = file_get_contents($file);
$tokens = token_get_all($content);
$braces = 0;
$line = 0;
foreach ($tokens as $token) {
    if (is_array($token)) {
        $line = $token[2];
        continue;
    }
    if ($token === '{') {
        $braces++;
    }
    elseif ($token === '}') {
        $braces--;
        if ($braces < 0) {
            echo "Extra closing brace at line $line\n";
            exit(1);
        }
    }
}
if ($braces > 0) {
    echo "Missing $braces closing brace(s) at end of file\n";
    exit(1);
}
echo "Braces are balanced\n";
