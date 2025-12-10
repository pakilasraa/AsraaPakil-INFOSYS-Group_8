<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'firebase_uid',
        'name',
        'email',
        'phone',
        // 'address', // kung meron
    ];
}
