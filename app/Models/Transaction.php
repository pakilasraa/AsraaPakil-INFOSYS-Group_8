<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_amount',
        'payment_method',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }
}


