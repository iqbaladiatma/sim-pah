<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BudgetPlanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_plan_id',
        'sub_unit',
        'category',
        'description',
        'timeline',
        'output',
        'volume',
        'unit',
        'unit_price',
        'total_price',
        'realization_amount',
        'percentage',
        'remaining_amount',
        'remarks',
    ];

    public function plan()
    {
        return $this->belongsTo(BudgetPlan::class, 'budget_plan_id');
    }

    public function attachments()
    {
        return $this->hasMany(BudgetPlanAttachment::class, 'budget_plan_item_id');
    }
}
