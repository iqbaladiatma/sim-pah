<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Request extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'type', 'deletion_reason'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Request has been {$eventName}");
    }

    protected $fillable = [
        'user_id',
        'institution_id',
        'type',
        'title',
        'description',
        'photo_evidence',
        'estimated_cost',
        'status',
        'admin_note',
        'deletion_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
