<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
    'category_id',
    'name',
    'price',
    'price_small',
    'price_medium',
    'price_large',
    'image',
    ];
    protected $casts = [
    'price' => 'decimal:2',
    'price_small' => 'decimal:2',
    'price_medium' => 'decimal:2',
    'price_large' => 'decimal:2',
    ];

    public function getSizePricesAttribute(): array
    {
    return [
        'small' => $this->price_small,
        'medium' => $this->price_medium,
        'large' => $this->price_large,
    ];
    }

    public function orders()
{
    return $this->belongsToMany(Order::class)->withPivot('quantity', 'temperature');
}



    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }
}


