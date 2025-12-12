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

    // 1. Create the transaction record FIRST so we have an ID
    $transaction = Transaction::create([
        'total_amount' => 0, // Will update later
        'payment_method' => $validated['payment_method'],
        'status' => 'pending',
    ]);

    $totalAmount = 0;
    
    // 2. Create items linked to the transaction
    foreach ($validated['items'] as $item) {
        $product = Product::find($item['product_id']);
        
        // Determine price
        switch ($item['size'] ?? null) {
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

        if (($item['temperature'] ?? '') === 'hot') {
            $price += 5; 
        }

        $subtotal = $price * $item['quantity'];
        $totalAmount += $subtotal;

        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => $item['quantity'],
            'price' => $price,
            'temperature' => $item['temperature'] ?? null,
            'size' => $item['size'] ?? null,
            'subtotal' => $subtotal,
        ]);
    }

    // 3. Update total amount
    $transaction->update(['total_amount' => $totalAmount]);

    // 4. Trigger n8n Webhook (Fire and forget, or catch errors so flow doesn't break)
    try {
        $webhookUrl = env('N8N_WEBHOOK_URL', 'http://localhost:5678/webhook/transaction-created');
        
        // Prepare payload with eager loaded items
        $payload = $transaction->load('items.product')->toArray();
        
        // Send async or with short timeout so POS doesn't hang
        \Illuminate\Support\Facades\Http::timeout(2)
            ->post($webhookUrl, $payload);
            
    } catch (\Throwable $e) {
        // Log error but don't fail the transaction
        \Illuminate\Support\Facades\Log::error('n8n Webhook failed: ' . $e->getMessage());
    }

    return response()->json([
        'transaction_id' => $transaction->id,
        'total_amount' => $totalAmount,
        'items' => $transaction->items,
    ], 201);
}

}
