<?php
$file = 'app/Exports/IsoProcedureExport.php';
$content = file_get_contents($file);
$tokens = token_get_all($content);
$braces = 0;
foreach ($tokens as $token) {
    if (is_array($token)) {
        $line = $token[2];
        if ($token[0] === T_CURLY_OPEN || $token[0] === T_DOLLAR_OPEN_CURLY_BRACES) {
            $braces++;
            echo "Open at $line (Count: $braces)\n";
        }
        continue;
    }
    if ($token === '{') {
        $braces++;
        echo "Open at $line (Count: $braces)\n";
    }
    elseif ($token === '}') {
        $braces--;
        echo "Close at $line (Count: $braces)\n";
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
