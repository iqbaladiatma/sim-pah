<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'label',
        'value',
        'type',
        'group',
        'description',
    ];
}
