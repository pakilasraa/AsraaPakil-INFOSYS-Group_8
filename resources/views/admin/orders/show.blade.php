<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <!-- Back Button -->
                <a href="{{ route('admin.orders.index') }}" class="inline-block bg-blue-500 text-white py-2 px-4 rounded-md mb-4">Back to Orders</a>

                <div class="mb-4">
                    <p><strong>Order ID:</strong> {{ $order->order_id }}</p>
                    <p><strong>Customer Name:</strong> {{ $order->customer_name }}</p>
                    <p><strong>Order Type:</strong> {{ ucfirst($order->order_type) }}</p>
                    <p><strong>Total Price:</strong> ₱{{ number_format($order->total_price, 2) }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold">Products in this Order:</h3>
                    <ul class="list-disc pl-6">
                        @foreach ($order->products as $product)
                            <li>
                                {{ $product->name }} (x{{ $product->pivot->quantity }})
                                @if ($product->pivot->temperature)
                                    - {{ ucfirst($product->pivot->temperature) }} <!-- Display temperature -->
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Update order status -->
                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                    @csrf
                    <div class="flex items-center space-x-4">
                        <select name="status" class="form-select rounded-md border-gray-300">
                            <option value="pending" @selected($order->status == 'pending')>Pending</option>
                            <option value="confirmed" @selected($order->status == 'confirmed')>Confirmed</option>
                            <option value="completed" @selected($order->status == 'completed')>Completed</option>
                            <option value="canceled" @selected($order->status == 'canceled')>Canceled</option>
                        </select>
                        <button type="submit" class="ml-4 bg-[#3d2b1f] text-white py-2 px-6 rounded-md">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
