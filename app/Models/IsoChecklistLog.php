<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class IsoChecklistLog extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'iso_checklist_id',
        'user_id',
        'results',
        'notes',
        'photo_evidence',
        'checked_at'
    ];

    protected $casts = [
        'results' => 'json',
        'checked_at' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "ISO Checklist execution has been {$eventName}");
    }

    public function checklist()
    {
        return $this->belongsTo(IsoChecklist::class, 'iso_checklist_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
