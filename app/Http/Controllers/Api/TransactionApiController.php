<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionApiController extends Controller
{
    public function index(Request $request)
    {
        return Transaction::with('items.product')
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required|exists:products,id',
            'items.*.qty'    => 'required|integer|min:1',
            'payment_method' => 'required|string',
        ]);

        $transaction = null;

        DB::transaction(function () use ($data, &$transaction) {
            $total = 0;

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['id']);
                $subtotal = $product->price * $item['qty'];
                $total   += $subtotal;
            }

            $transaction = Transaction::create([
                'total_amount'   => $total,
                'payment_method' => $data['payment_method'],
            ]);

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['id']);
                $subtotal = $product->price * $item['qty'];

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => $product->id,
                    'quantity'       => $item['qty'],
                    'price'          => $product->price,
                    'subtotal'       => $subtotal,
                ]);
            }
        });

        return response()->json([
            'message'     => 'Transaction created successfully.',
            'transaction' => $transaction->load('items.product'),
        ], 201);
    }
}
