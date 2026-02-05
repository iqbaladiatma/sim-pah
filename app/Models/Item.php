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
            ->logOnly(['name', 'stock', 'unit', 'room', 'brand', 'purchase_date', 'source', 'condition', 'responsible_person', 'note'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Item has been {$eventName}");
    }

    protected $fillable = [
        'institution_id',
        'room_id',
        'name',
        'brand',
        'purchase_date',
        'stock',
        'unit',
        'source',
        'condition',
        'responsible_person',
        'note',
        'min_stock',
        'is_active',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function updateRequests()
    {
        return $this->hasMany(ItemUpdateRequest::class);
    }
}
