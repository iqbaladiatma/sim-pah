<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Vehicle extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'plate_number',
        'type',
        'brand',
        'year',
        'stnk_expiry',
        'tax_expiry',
        'status',
        'note'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Vehicle has been {$eventName}");
    }

    public function requests()
    {
        return $this->hasMany(VehicleRequest::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }
}
