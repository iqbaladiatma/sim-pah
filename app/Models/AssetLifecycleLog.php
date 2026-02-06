<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class AssetLifecycleLog extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'item_id',
        'type',
        'action_date',
        'value',
        'reason',
        'document_evidence'
    ];

    protected $casts = [
        'action_date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Asset lifecycle event ({$this->type}) has been {$eventName}");
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
