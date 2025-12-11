<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;

class OrderController extends Controller
{
    // Helper: get or create user from Firebase token
    protected function getUserFromFirebase(Request $request, FirebaseAuth $firebaseAuth)
    {
        $firebaseToken = $request->bearerToken() ?? $request->header('Authorization');

        if (!$firebaseToken) {
            return response()->json(['error' => 'Missing Firebase token'], 401);
        }

        try {
            $verifiedIdToken = $firebaseAuth->verifyIdToken($firebaseToken);
        } catch (AuthException|FirebaseException $e) {
            return response()->json(['error' => 'Invalid Firebase token'], 401);
        }

        // UID from Firebase
        $uid = $verifiedIdToken->claims()->get('sub');

        // Optional: get email & name claims if available
        $email = $verifiedIdToken->claims()->get('email');
        $name  = $verifiedIdToken->claims()->get('name') ?? 'Customer';

        // Find or create the user in local DB
        $user = User::updateOrCreate(
            ['firebase_uid' => $uid],
            [
                'name'  => $name,
                'email' => $email ?? ($uid.'@firebase.local'), // fallback email
            ]
        );

        return $user;
    }

    // Store an order
    public function store(Request $request, FirebaseAuth $firebaseAuth)
    {
        // Get user from Firebase token
        $user = $this->getUserFromFirebase($request, $firebaseAuth);
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user; // error response from getUserFromFirebase
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric',
        ]);

        // Create the order for the user
        $order = $user->orders()->create([
            'total_price' => $validated['total_price'],
            'status' => 'pending',
        ]);

        // Attach the products to the order
        foreach ($validated['items'] as $item) {
            $order->products()->attach($item['product_id'], [
                'quantity' => $item['quantity'],
            ]);
        }

        return response()->json([
            'message' => 'Order placed successfully!',
            'order'   => $order->load('products'),
        ], 201);
    }

    // Get order history for the authenticated user
    public function index(Request $request, FirebaseAuth $firebaseAuth)
    {
        $user = $this->getUserFromFirebase($request, $firebaseAuth);
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        $orders = $user->orders()->with('products')->get();

        return response()->json(['orders' => $orders]);
    }
}
