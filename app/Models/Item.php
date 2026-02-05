<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Item extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'stock', 'unit'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Item has been {$eventName}");
    }

    protected $fillable = [
        'institution_id',
        'name',
        'stock',
        'unit',
        'min_stock',
        'is_active',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function updateRequests()
    {
        return $this->hasMany(ItemUpdateRequest::class);
    }
}
