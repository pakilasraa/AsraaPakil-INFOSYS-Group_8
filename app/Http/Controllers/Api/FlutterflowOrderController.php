<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class FlutterflowOrderController extends Controller
{
    // ✅ PLACE ORDER (FlutterFlow -> Laravel)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'address'        => 'nullable|string|max:255',
            'order_type'     => 'required|string|max:50', // delivery/pickup/dinein
            'total_price'    => 'required|numeric|min:0',
            'cartItems'      => 'required|array|min:1',
        ]);

        $order = Order::create([
            // firebase fields null for FlutterFlow
            'firebase_uid'    => null,
            'order_id'        => 'FF-' . now()->format('YmdHis') . '-' . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),

            'customer_name'   => $validated['customer_name'],
            'customer_phone'  => $validated['customer_phone'] ?? null,
            'address'         => $validated['address'] ?? null,
            'order_type'      => $validated['order_type'],
            'total_price'     => $validated['total_price'],
            'status'          => 'pending',

            // JSON column (Step 3 migration)
            'items'           => $validated['cartItems'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order received!',
            'order'   => $order,
        ], 201);
    }

    // ✅ ORDER HISTORY (FlutterFlow -> page)
    public function history()
    {
        $orders = Order::whereNotNull('order_id')
            ->whereNull('firebase_uid') // optional: para FlutterFlow orders lang
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'orders' => $orders,
        ]);
    }
}
