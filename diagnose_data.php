<?php
use App\Models\User;
use App\Models\Room;
use App\Models\Institution;
use App\Models\Item;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- DATA DIAGNOSIS ---\n";

$rooms = Room::all();
echo "\nTotal Rooms: " . $rooms->count() . "\n";
foreach ($rooms as $room) {
    echo "Room ID: {$room->id}, Name: {$room->name}, InstID: {$room->institution_id}\n";
}

$items = Item::all();
echo "\nTotal Items: " . $items->count() . "\n";
foreach ($items as $item) {
    echo "Item: {$item->name}, InstID: {$item->institution_id}, RoomID: " . ($item->room_id ?? 'NULL') . "\n";
}

$institutions = Institution::all();
echo "\nTotal Institutions: " . $institutions->count() . "\n";
foreach ($institutions as $inst) {
    echo "Inst: {$inst->name}, ID: {$inst->id}\n";
}
