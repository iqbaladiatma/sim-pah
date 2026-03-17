<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

try {
    $totalLogs = Activity::count();
    $logsByUser = Activity::select('causer_id', DB::raw('count(*) as total'))
        ->groupBy('causer_id')
        ->get();
    
    $migrations = DB::table('migrations')->count();
    
    echo "Total Activity Logs: " . $totalLogs . "\n";
    echo "Total Migrations: " . $migrations . "\n";
    
    // Attempt to count commits if git is available
    $commitCount = shell_exec('git rev-list --count HEAD');
    echo "Total Commits: " . trim($commitCount) . "\n";
    
    // Total lines of code
    $locCount = shell_exec('powershell -Command "Get-ChildItem -Recurse -File -Include *.php,*.vue,*.js,*.css | ForEach-Object { Get-Content $_.FullName | Measure-Object -Line } | Measure-Object -Property Lines -Sum | Select-Object -ExpandProperty Sum"');
    echo "Total LOC: " . trim($locCount) . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
