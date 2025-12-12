<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;

class OrderController extends Controller
{
    /**
     * Extract Firebase ID token from the Authorization header.
     * Accepts either:
     *   - "Bearer <token>"
     *   - "<token>" (raw)
     */
    protected function getFirebaseToken(Request $request): ?string
    {
        $token = $request->bearerToken();

        if ($token) {
            return $token;
        }

        $raw = $request->header('Authorization');
        if (!$raw) {
            return null;
        }

        $raw = trim($raw);
        if (stripos($raw, 'Bearer ') === 0) {
            return trim(substr($raw, 7));
        }

        return $raw; // raw token
    }

    protected function getFirebaseUser(Request $request, FirebaseAuth $firebaseAuth)
    {
        $firebaseToken = $this->getFirebaseToken($request);

        if (!$firebaseToken) {
            return response()->json(['error' => 'Missing Firebase token'], 401);
        }

        try {
            $verifiedIdToken = $firebaseAuth->verifyIdToken($firebaseToken);
        } catch (AuthException|FirebaseException $e) {
            return response()->json(['error' => 'Invalid Firebase token'], 401);
        }

        $uid   = (string) $verifiedIdToken->claims()->get('sub');
        $email = $verifiedIdToken->claims()->get('email') ? (string) $verifiedIdToken->claims()->get('email') : null;
        $name  = $verifiedIdToken->claims()->get('name') ? (string) $verifiedIdToken->claims()->get('name') : 'Customer';

        // Keep Customer table in sync (best-effort)
        if ($email) {
            Customer::updateOrCreate(
                ['email' => $email],
                [
                    'firebase_uid' => $uid,
                    'name' => $name,
                ]
            );
        } else {
            // If no email in token, fall back to firebase_uid matching
            Customer::updateOrCreate(
                ['firebase_uid' => $uid],
                [
                    'name' => $name,
                    'email' => $uid.'@firebase.local',
                ]
            );
        }

        return ['uid' => $uid, 'email' => $email, 'name' => $name];
    }

    /**
     * POST /api/orders
     * Body:
     * {
     *   "items": [{"product_id": 1, "quantity": 2}],
     *   "total_price": 250,
     *   "order_type": "pickup" // optional
     * }
     */
    public function store(Request $request, FirebaseAuth $firebaseAuth)
{
    $fb = $this->getFirebaseUser($request, $firebaseAuth);
    if ($fb instanceof \Illuminate\Http\JsonResponse) {
        return $fb;
    }

    $validated = $request->validate([
        'items' => ['required', 'array', 'min:1'],
        'items.*.product_id' => ['required', 'exists:products,id'],
        'items.*.quantity' => ['required', 'integer', 'min:1'],
        // optional for later (when you add to FlutterFlow)
        'items.*.size' => ['nullable', 'in:small,medium,large'],
        'items.*.temperature' => ['nullable', 'in:hot,iced'],

        // keep but ignore in calculation
        'total_price' => ['nullable', 'numeric', 'min:0'],
        'order_type' => ['nullable', 'string', 'max:50'],
        'customer_name' => ['nullable', 'string', 'max:255'],
    ]);

    $productIds = collect($validated['items'])->pluck('product_id')->unique()->values();
    $products = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');

    // Compute total from DB prices
    $computedTotal = 0;

    foreach ($validated['items'] as $item) {
        $p = $products[$item['product_id']];
        $qty = (int) $item['quantity'];
        $size = $item['size'] ?? null;

        // Choose unit price based on size if provided
        if ($size === 'small') {
            $unit = (float) ($p->price_small ?? $p->price ?? 0);
        } elseif ($size === 'medium') {
            $unit = (float) ($p->price_medium ?? $p->price ?? 0);
        } elseif ($size === 'large') {
            $unit = (float) ($p->price_large ?? $p->price ?? 0);
        } else {
            $unit = (float) ($p->price ?? $p->price_small ?? $p->price_medium ?? $p->price_large ?? 0);
        }

        $computedTotal += ($unit * $qty);
    }

    $order = \DB::transaction(function () use ($validated, $fb, $products, $computedTotal) {
        $order = \App\Models\Order::create([
            'firebase_uid' => $fb['uid'],
            'order_id' => 'ORD-' . now()->format('YmdHis') . '-' . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),
            'customer_name' => $validated['customer_name'] ?? $fb['name'] ?? 'Customer',
            'order_type' => $validated['order_type'] ?? 'pickup',
            'total_price' => $computedTotal,
            'status' => 'pending',
        ]);

        foreach ($validated['items'] as $item) {
            $p = $products[$item['product_id']];
            $qty = (int) $item['quantity'];
            $size = $item['size'] ?? null;

            if ($size === 'small') {
                $unit = (float) ($p->price_small ?? $p->price ?? 0);
            } elseif ($size === 'medium') {
                $unit = (float) ($p->price_medium ?? $p->price ?? 0);
            } elseif ($size === 'large') {
                $unit = (float) ($p->price_large ?? $p->price ?? 0);
            } else {
                $unit = (float) ($p->price ?? $p->price_small ?? $p->price_medium ?? $p->price_large ?? 0);
            }

            // Attach to pivot (quantity always exists). Size/temp/unit_price only if columns exist.
            $pivot = [
                'quantity' => $qty,
            ];

            // Add optional pivot columns if present in DB
            if (\Schema::hasColumn('order_product', 'size')) {
                $pivot['size'] = $size;
            }
            if (\Schema::hasColumn('order_product', 'temperature')) {
                $pivot['temperature'] = $item['temperature'] ?? null;
            }
            if (\Schema::hasColumn('order_product', 'unit_price')) {
                $pivot['unit_price'] = $unit;
            }

            $order->products()->attach($item['product_id'], $pivot);
        }

        return $order;
    });

    // Trigger n8n Webhook
    try {
        $webhookUrl = env('N8N_WEBHOOK_URL', 'http://localhost:5678/webhook/transaction-created');
        // Load relationships to send full data
        $payload = $order->load('products')->toArray();
        \Illuminate\Support\Facades\Http::timeout(2)->post($webhookUrl, $payload);
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('n8n Webhook failed: ' . $e->getMessage());
    }

    return response()->json([
        'success' => true,
        'order_id' => $order->order_id,
        'total_price' => $order->total_price,
    ], 201);
}






    /**
     * GET /api/orders/history
     * Returns: { "orders": [...] }
     */
    public function index(Request $request, FirebaseAuth $firebaseAuth)
{
    $fb = $this->getFirebaseUser($request, $firebaseAuth);
    if ($fb instanceof \Illuminate\Http\JsonResponse) {
        return $fb;
    }

    $orders = \App\Models\Order::with(['products' => function ($q) {
            $q->select('products.id', 'products.name', 'products.image');
        }])
        ->where('firebase_uid', $fb['uid'])
        ->latest()
        ->get();

    $data = $orders->map(function ($o) {
        return [
            'id' => $o->id,
            'order_id' => $o->order_id,
            'status' => $o->status,
            'total_price' => $o->total_price,
            'order_type' => $o->order_type,
            'created_at' => optional($o->created_at)->toDateTimeString(),

            'items' => $o->products->map(function ($p) {
                return [
                    'product_id' => $p->id,
                    'name' => $p->name,
                    'image_url' => $p->image ? asset('storage/' . $p->image) : null,
                    'quantity' => (int) ($p->pivot->quantity ?? 0),
                    'size' => $p->pivot->size ?? null,
                    'temperature' => $p->pivot->temperature ?? null,
                    'unit_price' => $p->pivot->unit_price ?? null,
                ];
            })->values(),
        ];
    });

    return response()->json($data);
}

}
