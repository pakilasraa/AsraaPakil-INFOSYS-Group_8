<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'customer_name', 'order_type', 'total_price', 'status'
    ];

    public function products()
{
    return $this->belongsToMany(Product::class)->withPivot('quantity', 'temperature');
}


}