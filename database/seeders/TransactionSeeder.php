<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all(['id','price']);
        if ($products->isEmpty()) {
            return;
        }

        $paymentMethods = ['Cash','GCash','Card'];

        // Generate transactions over the last 45 days
        for ($d = 0; $d < 45; $d++) {
            $date = Carbon::now()->subDays($d);
            // 3 to 8 transactions per day
            $numTx = rand(3, 8);
            for ($i = 0; $i < $numTx; $i++) {
                DB::transaction(function () use ($products, $paymentMethods, $date) {
                    $itemsCount = rand(1, 4);
                    $chosen = $products->random($itemsCount);
                    $total = 0;
                    $transaction = Transaction::create([
                        'total_amount' => 0, // temp, update after items
                        'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                        'created_at' => $date->copy()->setTime(rand(8, 20), rand(0,59), rand(0,59)),
                        'updated_at' => $date,
                    ]);

                    foreach ($chosen as $product) {
                        $qty = rand(1, 3);
                        $price = (float) $product->price;
                        $subtotal = $qty * $price;
                        $total += $subtotal;
                        TransactionItem::create([
                            'transaction_id' => $transaction->id,
                            'product_id' => $product->id,
                            'quantity' => $qty,
                            'price' => $price,
                            'subtotal' => $subtotal,
                            'created_at' => $transaction->created_at,
                            'updated_at' => $transaction->updated_at,
                        ]);
                    }

                    $transaction->update(['total_amount' => $total]);
                });
            }
        }
    }
}


