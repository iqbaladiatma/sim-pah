<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class IsoChecklist extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['title', 'category', 'frequency', 'items', 'is_active'];

    protected $casts = [
        'items' => 'json',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "ISO Checklist has been {$eventName}");
    }

    public function logs()
    {
        return $this->hasMany(IsoChecklistLog::class);
    }
}
