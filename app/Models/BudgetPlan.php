<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BudgetPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'user_id',
        'title',
        'fiscal_year',
        'description',
        'total_budget',
        'status',
        'type',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(BudgetPlanItem::class);
    }
}
