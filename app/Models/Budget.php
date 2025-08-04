<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'amount',
        'period',
        'start_date',
        'end_date',
        'user_id'
    ];

    protected $dates = ['start_date', 'end_date'];

    const PERIODS = [
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'yearly' => 'Yearly'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getProgressAttribute()
    {
        $spent = $this->category->transactions()
            ->where('type', 'expense')
            ->whereBetween('date', [
                $this->start_date ?? now()->startOfMonth(),
                $this->end_date ?? now()->endOfMonth()
            ])
            ->sum('amount');

        return min(100, ($spent / $this->amount) * 100);
    }

    public function getSpentAttribute()
    {
        return $this->category->transactions()
            ->where('type', 'expense')
            ->whereBetween('date', [
                $this->start_date ?? now()->startOfMonth(),
                $this->end_date ?? now()->endOfMonth()
            ])
            ->sum('amount');
    }
}