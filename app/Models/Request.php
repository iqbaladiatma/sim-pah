<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Request extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'type'])
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
