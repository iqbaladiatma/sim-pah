<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BudgetPlanAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_plan_item_id',
        'file_path',
        'file_type',
        'original_name',
        'remarks',
    ];

    public function item()
    {
        return $this->belongsTo(BudgetPlanItem::class, 'budget_plan_item_id');
    }
}
