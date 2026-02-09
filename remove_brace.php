<?php
$c = file_get_contents('app/Exports/IsoProcedureExport.php');
$c = rtrim($c);
if (substr($c, -1) === '}') {
    $c = substr($c, 0, -1);
    file_put_contents('app/Exports/IsoProcedureExport.php', $c . "\n");
    echo "Removed one trailing brace.\n";
}
else {
    echo "Last character is not a brace.\n";
}
