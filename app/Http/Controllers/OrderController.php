<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Show all orders for the admin
    public function index()
    {
        $orders = Order::all();  // Fetch all orders
        return view('admin.orders.index', compact('orders'));  // Pass orders to the view
    }

    // Show details for a specific order
    public function show($id)
{
    // Attempt to get the order by ID
    $order = Order::find($id);

    // Check if the order exists
    if (!$order) {
        // If no order found, show an error or redirect
        return redirect()->route('admin.orders.index')->with('error', 'Order not found.');
    }

    // Pass the order to the view
    return view('admin.orders.show', compact('order'));
}




    // Update the status of an order (e.g., from pending to confirmed)
    public function updateStatus($id, Request $request)
    {
        $order = Order::findOrFail($id);
        $order->status = $request->status;  // Update the status field
        $order->save();

        return redirect()->route('admin.orders.index')->with('message', 'Order status updated!');
    }
}