<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
{
    $order1 = Order::create([
    'order_id'      => 'ORD-0001',
    'customer_name' => 'AngCuteKo',
    'order_type'    => 'dine-in',
    'total_price'   => 150.50,
    'status'        => 'pending',
]);

// Attach products with temperature
$order1->products()->attach([
    1 => ['quantity' => 2, 'temperature' => 'hot'],  // Americano, hot
    2 => ['quantity' => 1, 'temperature' => 'iced'], // Latte, iced
]);
    $order2 = Order::create([
    'order_id'      => 'ORD-0002',
    'customer_name' => 'Asracutie',
    'order_type'    => 'takeaway',
    'total_price'   => 85.00,
    'status'        => 'confirmed',
]);
// Attach products with temperature
$order2->products()->attach([
    3 => ['quantity' => 1, 'temperature' => 'hot'],  // Cappuccino, hot
    4 => ['quantity' => 3, 'temperature' => 'iced'], // Mocha, iced
]);

}

}
