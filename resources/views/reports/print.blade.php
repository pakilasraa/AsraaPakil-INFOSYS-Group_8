<x-app-layout>
    <div class="bg-white p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-cafe-900">Sales Report</h1>
                <p class="text-sm text-cafe-700">{{ $start->toDateString() }} to {{ $end->toDateString() }} @if($method) — {{ $method }} @endif</p>
            </div>
            <button onclick="window.print()" class="px-4 py-2 rounded-md btn-cafe print:hidden">Print</button>
        </div>
        <table class="w-full">
            <thead class="bg-[#5b4334] text-white">
                <tr>
                    <th class="text-left px-4 py-2">Date</th>
                    <th class="text-left px-4 py-2">Items</th>
                    <th class="text-left px-4 py-2">Amount</th>
                    <th class="text-left px-4 py-2">Method</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $t)
                    <tr class="odd:bg-[#faf5ef] even:bg-white border-b align-top">
                        <td class="px-4 py-2 whitespace-nowrap">{{ $t->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-2 text-sm">
                            @foreach($t->items as $it)
                                <div>{{ $it->quantity }} × {{ $it->product->name }} — ₱{{ number_format($it->subtotal, 2) }}</div>
                            @endforeach
                        </td>
                        <td class="px-4 py-2 font-medium">₱{{ number_format($t->total_amount, 2) }}</td>
                        <td class="px-4 py-2">{{ $t->payment_method }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-amber-100">
                <tr>
                    <td class="px-4 py-2 font-semibold">Summary</td>
                    <td class="px-4 py-2">Total items: {{ $totalItems }}</td>
                    <td class="px-4 py-2 font-semibold">₱{{ number_format($totalSales, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</x-app-layout>
