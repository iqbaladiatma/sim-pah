<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MaintenanceLog extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'type',
        'category',
        'subcategory',
        'serial_number',
        'fan_type',
        'st_baik',
        'st_penuh',
        'st_bocor',
        'st_bau',
        'institution_id',
        'room_id',
        'vehicle_id',
        'title',
        'description',
        'before_condition',
        'after_condition',
        'action_taken',
        'location',
        'scheduled_at',
        'completed_at',
        'cost',
        'photo_before',
        'photo_after',
        'performed_by',
        'status',
        // Periodic Checklist Fields
        'check_standard',
        'check_method',
        'check_frequency',
        'year',
        'jul',
        'aug',
        'sep',
        'oct',
        'nov',
        'dec',
        'jan',
        'feb',
        'mar',
        'apr',
        'may',
        'jun',
        // Bathroom Maintenance Fields
        'kran_air',
        'lampu',
        'fiting_lampu',
        'saklar_lampu',
        'ember',
        'gayung',
        'closet',
        'pintu',
        'grendel_pintu',
        // AC Maintenance Fields
        'ac_indoor_pc',
        'ac_indoor_sw',
        'ac_outdoor_freon',
        'ac_outdoor_amp',
        'ac_kelistrikan_jaringan',
        'ac_kelistrikan_tegangan',
        'ac_divisi',
        'ac_tgl_bln',
        // Pump Maintenance Fields
        'jul_putra',
        'jul_putri',
        'jul_lawata',
        'aug_putra',
        'aug_putri',
        'aug_lawata',
        'sep_putra',
        'sep_putri',
        'sep_lawata',
        'oct_putra',
        'oct_putri',
        'oct_lawata',
        'nov_putra',
        'nov_putri',
        'nov_lawata',
        'dec_putra',
        'dec_putri',
        'dec_lawata',
        'jan_putra',
        'jan_putri',
        'jan_lawata',
        'feb_putra',
        'feb_putri',
        'feb_lawata',
        'mar_putra',
        'mar_putri',
        'mar_lawata',
        'apr_putra',
        'apr_putri',
        'apr_lawata',
        'may_putra',
        'may_putri',
        'may_lawata',
        'jun_putra',
        'jun_putri',
        'jun_lawata',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Maintenance log ({$this->type}) has been {$eventName}");
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
