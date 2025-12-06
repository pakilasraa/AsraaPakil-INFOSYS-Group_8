<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

use App\Models\Product;

// Controllers
use App\Http\Controllers\Api\TransactionApiController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\AiController;


/*
|--------------------------------------------------------------------------
| PUBLIC API ROUTES (NO AUTH REQUIRED)
|--------------------------------------------------------------------------
*/

// Health check
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

// AUTH - PUBLIC (register & login)
Route::post('/auth/register', [ApiAuthController::class, 'register']);
Route::post('/auth/login', [ApiAuthController::class, 'login']);


/*
|--------------------------------------------------------------------------
| AI Test (Simple hello test to confirm Ollama is working)
|--------------------------------------------------------------------------
*/
Route::get('/ai/test', function () {
    $response = Http::timeout(60)->post('http://127.0.0.1:11434/api/chat', [
        'model' => 'llama3',
        'stream' => false,
        'messages' => [
            [
                'role' => 'user',
                'content' => 'Say hello from Laravel.',
            ],
        ],
    ]);

    return $response->json();
});


/*
|--------------------------------------------------------------------------
| PUBLIC - PRODUCTS & CATEGORIES API
|--------------------------------------------------------------------------
*/

// Get all products
Route::get('/products', [ProductApiController::class, 'index']);

// Get single product details
Route::get('/products/{id}', [ProductApiController::class, 'show']);

// Get all categories
Route::get('/categories', [ProductApiController::class, 'categories']);

// Get products by category
Route::get('/categories/{categoryId}/products', [ProductApiController::class, 'byCategory']);

/*
|--------------------------------------------------------------------------
| PUBLIC - TRANSACTIONS API (POS Checkout with DB write)
|--------------------------------------------------------------------------
*/

// List all transactions (for now, global list)
Route::get('/transactions', [TransactionApiController::class, 'index']);

// Create a new transaction (checkout)
Route::post('/transactions', [TransactionApiController::class, 'store']);



/*
|--------------------------------------------------------------------------
| PUBLIC - POS CHECKOUT SIMULATION (No DB write)
|--------------------------------------------------------------------------
*/
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
| AI Product Recommendation (Public for now)
|--------------------------------------------------------------------------
*/
Route::post('/ai/recommend', [AiController::class, 'recommend']);


/*
|--------------------------------------------------------------------------
| PROTECTED API ROUTES (AUTH REQUIRED - Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Get authenticated user info
    Route::get('/auth/me', [ApiAuthController::class, 'me']);

    // Logout (revoke current token)
    Route::post('/auth/logout', [ApiAuthController::class, 'logout']);

    // (dito ka pwedeng magdagdag ng customer-only routes later)
});
