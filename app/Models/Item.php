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
            ->logOnly(['name', 'code', 'brand', 'specification', 'serial_number', 'size', 'material', 'purchased_at', 'received_at', 'stock', 'unit', 'source', 'price', 'condition', 'note', 'no_urut_satker', 'no_urut_pondok', 'depreciation_price', 'responsible_person'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Item has been {$eventName}");
    }

    protected $fillable = [
        'institution_id',
        'room_id',
        'name',
        'code',
        'brand',
        'specification',
        'serial_number',
        'size',
        'material',
        'purchased_at',
        'stock',
        'unit',
        'source',
        'price',
        'condition',
        'responsible_person',
        'note',
        'min_stock',
        'is_active',
        'no_urut_satker',
        'no_urut_pondok',
        'depreciation_price',
        'received_at',
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
