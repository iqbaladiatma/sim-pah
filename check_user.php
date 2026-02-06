<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Institution;

echo "=== CHECKING SMP PA USER ===\n\n";

// Check institution first
$institution = Institution::where('code', 'SMP_PA')->first();
if ($institution) {
    echo "✅ Institution Found:\n";
    echo "   ID: {$institution->id}\n";
    echo "   Name: {$institution->name}\n";
    echo "   Code: {$institution->code}\n";
    echo "   Active: " . ($institution->is_active ? 'Yes' : 'No') . "\n\n";
} else {
    echo "❌ Institution SMP_PA NOT FOUND!\n\n";
}

// Check user
$user = User::where('email', 'smp_pa@simpah.test')->first();
if ($user) {
    echo "✅ User Found:\n";
    echo "   ID: {$user->id}\n";
    echo "   Name: {$user->name}\n";
    echo "   Email: {$user->email}\n";
    echo "   Role: {$user->role}\n";
    echo "   Institution ID: {$user->institution_id}\n";

    if ($user->institution) {
        echo "   Institution Name: {$user->institution->name}\n";
        echo "   Institution Code: {$user->institution->code}\n";
    } else {
        echo "   ⚠️  Institution: NULL\n";
    }

    // Check password
    echo "\n   Testing password 'password': ";
    if (password_verify('password', $user->password)) {
        echo "✅ CORRECT\n";
    } else {
        echo "❌ WRONG\n";
    }
} else {
    echo "❌ User smp_pa@simpah.test NOT FOUND!\n\n";
}

// List all users
echo "\n=== ALL USERS IN DATABASE ===\n";
$allUsers = User::with('institution')->get();
foreach ($allUsers as $u) {
    $inst = $u->institution ? $u->institution->code : 'NULL';
    echo "- {$u->email} | {$u->role} | Institution: {$inst}\n";
}

echo "\n=== ALL INSTITUTIONS ===\n";
$allInstitutions = Institution::all();
foreach ($allInstitutions as $inst) {
    echo "- {$inst->code} | {$inst->name}\n";
}
