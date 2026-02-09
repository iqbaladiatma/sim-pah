<?php
$content = file_get_contents('app/Exports/IsoProcedureExport.php');
$tokens = token_get_all($content);
foreach ($tokens as $token) {
    if (is_array($token)) {
        if ($token[2] >= 670 && $token[2] <= 680) {
            echo "Line {$token[2]}: " . token_name($token[0]) . " ('{$token[1]}')\n";
        }
    }
    else {
    // Find line number for simple characters
    // This is tricky with token_get_all, let's just use string search for now or a better script
    }
}
