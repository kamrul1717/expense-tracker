<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'balance',
        'currency',
        'user_id'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    const TYPES = [
        'cash' => 'Cash',
        'bank' => 'Bank Account',
        'credit_card' => 'Credit Card',
        'investment' => 'Investment',
        'other' => 'Other'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getFormattedBalanceAttribute()
    {
        return number_format($this->balance, 2);
    }
}