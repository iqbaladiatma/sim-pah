<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ParkingLog extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'vehicle_type',
        'plate_number',
        'owner_name',
        'entry_time',
        'exit_time',
        'gate_name'
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Parking log entry has been {$eventName}");
    }
}
