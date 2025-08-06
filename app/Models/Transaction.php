<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'category_id',
        'amount',
        'type',
        'date',
        'description',
        'payee',
        'is_recurring',
        'recurrence_frequency',
        'recurrence_ends_on',
        'user_id'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'is_recurring' => 'boolean',
        'recurrence_ends_on' => 'date'
    ];

    const TYPES = [
        'expense' => 'Expense',
        'income' => 'Income',
        'transfer' => 'Transfer'
    ];

    const RECURRENCE_FREQUENCIES = [
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
        'yearly' => 'Yearly'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function getTypeBadgeAttribute()
    {
        $badgeClass = [
            'income' => 'bg-success',
            'expense' => 'bg-danger',
            'transfer' => 'bg-info'
        ][$this->type] ?? 'bg-secondary';

        return '<span class="badge '.$badgeClass.'">'.self::TYPES[$this->type].'</span>';
    }
}