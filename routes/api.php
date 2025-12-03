<?php
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\ProductController;


/*
|--------------------------------------------------------------------------
| PUBLIC API ROUTES (no auth)
|--------------------------------------------------------------------------
*/

// Simple test
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

// List products with size prices
Route::get('/products', function () {
    return Product::with('category')
        ->orderBy('name')
        ->get([
            'id',
            'name',
            'category_id',
            'price',
            'price_small',
            'price_medium',
            'price_large',
            'image',
        ]);
});

// POS checkout test with sizes (NO DB write, just compute)
Route::post('/pos/checkout', function (Request $request) {
    $validated = $request->validate([
        'items' => ['required', 'array', 'min:1'],
        'items.*.product_id' => ['required', 'exists:products,id'],
        'items.*.quantity' => ['required', 'integer', 'min:1'],
        'items.*.size' => ['nullable', 'in:small,medium,large'],
    ]);

    $total = 0;
    $lineItems = [];

    foreach ($validated['items'] as $item) {
        $product = Product::findOrFail($item['product_id']);
        $qty = (int) $item['quantity'];
        $size = $item['size'] ?? null;

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

        $lineItems[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'size' => $size,
            'unit_price' => $price,
            'quantity' => $qty,
            'subtotal' => $subtotal,
        ];
    }

    return response()->json([
        'items' => $lineItems,
        'total' => $total,
    ]);
});

/*
|--------------------------------------------------------------------------
| PROTECTED API ROUTES (auth:sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
