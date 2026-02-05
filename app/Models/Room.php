<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Room extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'institution_id',
        'name',
        'description',
        'is_active',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'institution_id'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Room has been {$eventName}");
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
