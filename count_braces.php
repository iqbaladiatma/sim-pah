<?php
$c = file_get_contents('app/Exports/IsoProcedureExport.php');
echo "Open: " . substr_count($c, '{') . "\n";
echo "Close: " . substr_count($c, '}') . "\n";
