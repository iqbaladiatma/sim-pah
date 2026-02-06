<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MaintenanceLog extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'type',
        'institution_id',
        'room_id',
        'vehicle_id',
        'title',
        'description',
        'location',
        'scheduled_at',
        'completed_at',
        'cost',
        'photo_before',
        'photo_after',
        'performed_by',
        'status'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Maintenance log ({$this->type}) has been {$eventName}");
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
