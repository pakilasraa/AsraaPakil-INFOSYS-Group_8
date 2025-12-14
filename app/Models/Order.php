<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
    'firebase_uid',
    'order_id',
    'customer_name',
    'customer_phone',
    'address',
    'order_type',
    'total_price',
    'status',
    'items',
];

protected $casts = [
    'items' => 'array',
];

public function products()
{
    // IMPORTANT: para match sa code mo sa API controller
    return $this->belongsToMany(Product::class)
        ->withPivot('quantity', 'size', 'temperature', 'unit_price');
}


}