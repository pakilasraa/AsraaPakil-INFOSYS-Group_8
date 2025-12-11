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
    // Validate incoming request
    $validated = $request->validate([
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.size' => 'nullable|in:small,medium,large',
        'items.*.temperature' => 'nullable|in:hot,iced',
        'payment_method' => 'required|in:Cash,GCash,Card',
    ]);

    // Calculate total amount and create transaction items
    $totalAmount = 0;
    foreach ($validated['items'] as $item) {
        // Get the product from DB
        $product = Product::find($item['product_id']);
        
        // Determine price based on size
        switch ($item['size']) {
            case 'small':
                $price = $product->price_small ?? $product->price;
                break;
            case 'medium':
                $price = $product->price_medium ?? $product->price;
                break;
            case 'large':
                $price = $product->price_large ?? $product->price;
                break;
            default:
                $price = $product->price;
                break;
        }

        // Optional: Apply surcharge for hot drinks
        if ($item['temperature'] === 'hot') {
            $price += 5;  // Example surcharge
        }

        // Calculate subtotal
        $subtotal = $price * $item['quantity'];
        $totalAmount += $subtotal;

        // Save the transaction item
        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => $item['quantity'],
            'price' => $price,
            'temperature' => $item['temperature'],
            'size' => $item['size'],
            'subtotal' => $subtotal,
        ]);
    }

    // Create the transaction record
    $transaction = Transaction::create([
        'total_amount' => $totalAmount,
        'payment_method' => $validated['payment_method'],
        'status' => 'pending',
    ]);

    // Return the transaction details
    return response()->json([
        'transaction_id' => $transaction->id,
        'total_amount' => $totalAmount,
        'items' => $transaction->items,
    ], 201);
}

}
