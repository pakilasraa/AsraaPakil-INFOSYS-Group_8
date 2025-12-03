<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get(['id','name']);
        $products = Product::with('category')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'price',
                'price_small',
                'price_medium',
                'price_large',
                'image',
                'category_id',
            ]);

        return view('pos.index', compact('categories','products'));
    }

    public function store(Request $request): RedirectResponse
{
    // Allow JSON string payload from hidden input
    if ($request->has('items') && is_string($request->input('items'))) {
        $decoded = json_decode($request->input('items'), true) ?: [];
        $request->merge(['items' => $decoded]);
    }

    $validated = $request->validate([
        'items' => ['required', 'array', 'min:1'],
        'items.*.product_id' => ['required', 'exists:products,id'],
        'items.*.quantity' => ['required', 'integer', 'min:1'],
        'items.*.size' => ['nullable', 'in:small,medium,large'], // NEW
        'payment_method' => ['required', 'in:Cash,GCash,Card'],
        'discount_type' => ['nullable', 'in:amount,percent'],
        'discount_value' => ['nullable', 'numeric', 'min:0'],
        'cash' => ['nullable', 'numeric', 'min:0'],
    ]);

    DB::transaction(function () use ($validated) {
        $total = 0.0;
        $lineItems = [];

        foreach ($validated['items'] as $line) {
            $product = Product::findOrFail($line['product_id']);
            $qty = (int) $line['quantity'];
            $size = $line['size'] ?? null;

            // Piliin ang tamang price depende sa size
            switch ($size) {
                case 'small':
                    $price = (float) ($product->price_small ?? $product->price);
                    break;
                case 'medium':
                    $price = (float) ($product->price_medium ?? $product->price);
                    break;
                case 'large':
                    $price = (float) ($product->price_large ?? $product->price);
                    break;
                default:
                    $price = (float) $product->price;
                    break;
            }

            $subtotal = $qty * $price;
            $total += $subtotal;
            $lineItems[] = compact('product', 'qty', 'price', 'subtotal');
        }

        // Discount logic (same as before)
        $discountAmount = 0.0;
        if (!empty($validated['discount_value'])) {
            if (($validated['discount_type'] ?? 'amount') === 'percent') {
                $discountAmount = min($total, $total * ((float) $validated['discount_value'] / 100));
            } else {
                $discountAmount = min($total, (float) $validated['discount_value']);
            }
        }

        $grandTotal = max(0, $total - $discountAmount);

        if ($validated['payment_method'] === 'Cash') {
            $cash = (float) ($validated['cash'] ?? 0);
            if ($cash < $grandTotal) {
                abort(422, 'Cash provided is less than total.');
            }
        }

        $transaction = Transaction::create([
            'total_amount' => $grandTotal,
            'payment_method' => $validated['payment_method'],
        ]);

        foreach ($lineItems as $li) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $li['product']->id,
                'quantity' => $li['qty'],
                'price' => $li['price'],
                'subtotal' => $li['subtotal'],
            ]);
        }
    });

    return back()->with('status', 'Sale completed');
}

}


