<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class VehicleRequest extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'destination',
        'purpose',
        'start_time',
        'end_time',
        'start_mileage',
        'end_mileage',
        'status',
        'admin_note'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Vehicle request has been {$eventName}");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
