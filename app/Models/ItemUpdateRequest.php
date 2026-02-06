<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ItemUpdateRequest extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'reason', 'new_data'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Item Update Request has been {$eventName}");
    }

    protected $fillable = [
        'item_id',
        'user_id',
        'type',
        'old_data',
        'new_data',
        'reason',
        'status',
        'admin_note',
        'approved_by',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class , 'approved_by');
    }
}
