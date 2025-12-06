<x-app-layout>
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold text-cafe-900">Sales Reports</h1>
        <div class="flex gap-2">
            <a href="{{ route('reports.export.csv', request()->query()) }}" class="px-4 py-2 rounded-md bg-emerald-100 text-emerald-800">Export CSV</a>
            <a href="{{ route('reports.print', request()->query()) }}" target="_blank" class="px-4 py-2 rounded-md bg-amber-100 text-cafe-900">Print</a>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-lg shadow p-4 mb-4 grid md:grid-cols-12 gap-3 items-end">
        <div class="md:col-span-3">
            <label class="text-sm text-cafe-900">Start Date</label>
            <input type="date" name="date_start" value="{{ request('date_start', $filters['date_start']) }}" class="w-full rounded-lg border input-cafe px-3 py-2" />
        </div>
        <div class="md:col-span-3">
            <label class="text-sm text-cafe-900">End Date</label>
            <input type="date" name="date_end" value="{{ request('date_end', $filters['date_end']) }}" class="w-full rounded-lg border input-cafe px-3 py-2" />
        </div>
        <div class="md:col-span-3">
            <label class="text-sm text-cafe-900">Payment Method</label>
            <select name="payment_method" class="w-full rounded-lg border input-cafe px-3 py-2">
                <option value="">All</option>
                <option value="Cash" @selected(request('payment_method')==='Cash')>Cash</option>
                <option value="GCash" @selected(request('payment_method')==='GCash')>GCash</option>
                <option value="Card" @selected(request('payment_method')==='Card')>Card</option>
            </select>
        </div>
        <div class="md:col-span-3 flex gap-2">
            <button class="px-8 py-2 rounded-md btn-cafe">Filter</button>
            <a href="{{ route('reports.index') }}" class="px-8 py-2 rounded-md bg-gray-100 border">Reset</a>
        </div>
    </form>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if ($transactions->count() === 0)
            <div class="p-10 text-center space-y-2">
                <div class="text-3xl">📊</div>
                <h3 class="text-cafe-900 font-semibold">No transactions found</h3>
                <p class="text-cafe-700 text-sm">Try adjusting the filters or record a sale in POS.</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-[#5b4334] text-white">
                    <tr>
                        <th class="text-left px-4 py-3">Date</th>
                        <th class="text-left px-4 py-3">Items</th>
                        <th class="text-left px-4 py-3">Amount</th>
                        <th class="text-left px-4 py-3">Method</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $t)
                        <tr class="odd:bg-[#faf5ef] even:bg-white border-b align-top">
                            <td class="px-4 py-3 whitespace-nowrap">{{ $t->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 text-sm">
                                @foreach($t->items as $it)
                                    <div>
                                        {{ $it->quantity }} × {{ $it->product->name }}

                                        @if($it->size || $it->temperature)
                                            (
                                            {{ $it->size ? ucfirst($it->size) : '' }}
                                            @if($it->size && $it->temperature)
                                                ·
                                            @endif
                                            {{ $it->temperature ? ucfirst($it->temperature) : '' }}
                                            )
                                        @endif

                                        — ₱{{ number_format($it->subtotal, 2) }}
                                    </div>
                                @endforeach
                            </td>
                            <td class="px-4 py-3 font-medium">₱{{ number_format($t->total_amount, 2) }}</td>
                            <td class="px-4 py-3">{{ $t->payment_method }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-amber-100">
                    <tr>
                        <td class="px-4 py-3 font-semibold">Summary</td>
                        <td class="px-4 py-3">Total items: {{ $totalItems }}</td>
                        <td class="px-4 py-3 font-semibold">₱{{ number_format($totalSales, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

    {{-- 🔥 NEW: Summary per product & temperature --}}
    @if(isset($summaryByProductTemp) && $summaryByProductTemp->count())
        <div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
            <div class="px-4 py-3 border-b bg-[#faf5ef]">
                <h2 class="text-sm font-semibold text-cafe-900">
                    Breakdown by Product &amp; Temperature
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#5b4334] text-white">
                        <tr>
                            <th class="text-left px-4 py-3">Product</th>
                            <th class="text-left px-4 py-3">Temperature</th>
                            <th class="text-left px-4 py-3">Total Qty</th>
                            <th class="text-left px-4 py-3">Total Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summaryByProductTemp as $row)
                            <tr class="odd:bg-[#faf5ef] even:bg-white border-b">
                                <td class="px-4 py-2">
                                    {{ $row->product->name ?? 'Unknown product' }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ $row->temperature ? ucfirst($row->temperature) : 'N/A' }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ $row->total_qty }}
                                </td>
                                <td class="px-4 py-2">
                                    ₱{{ number_format($row->total_amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif


    <div class="mt-4">{{ $transactions->links() }}</div>
</x-app-layout>
