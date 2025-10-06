<x-app-layout>
    <h1 class="text-2xl font-semibold text-cafe-900 mb-4">Dashboard</h1>

    <!-- Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        <div class="rounded-xl shadow p-4 bg-amber-100">
            <div class="text-sm text-cafe-700">Today’s Sales</div>
            <div class="text-2xl font-bold text-cafe-900">₱{{ number_format($todaysSales, 2) }}</div>
        </div>
        <div class="rounded-xl shadow p-4 bg-amber-100">
            <div class="text-sm text-cafe-700">Transactions Today</div>
            <div class="text-2xl font-bold text-cafe-900">{{ $transactionsToday }}</div>
        </div>
        <div class="rounded-xl shadow p-4 bg-amber-100">
            <div class="text-sm text-cafe-700">Best Seller</div>
            <div class="text-lg font-semibold text-cafe-900">
                @if($bestSeller)
                    {{ $bestSeller->product->name }} ({{ $bestSeller->qty }})
                @else
                    —
                @endif
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow p-4">
            <h2 class="text-sm font-semibold text-cafe-900 mb-2">Sales Last 7 Days</h2>
            <canvas id="dashLine" height="160"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <h2 class="text-sm font-semibold text-cafe-900 mb-2">Sales per Category</h2>
            <canvas id="dashPie" height="160"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const cafePalette = ['#C59B78','#E9CDAF','#7a5c47','#5b4334','#F5E6D3'];
        const lineLabels = @json($line->pluck('d'));
        const lineRevenue = @json($line->pluck('revenue'));

        new Chart(document.getElementById('dashLine'), {
            type: 'line',
            data: { labels: lineLabels, datasets: [{ label: 'Revenue', data: lineRevenue, borderColor: cafePalette[2], backgroundColor: cafePalette[4], tension: 0.3, fill: true }] },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        const catLabels = @json($perCategory->map(fn($r) => $categoryNames[$r->category_id] ?? 'Unknown'));
        const catRevenue = @json($perCategory->pluck('revenue'));
        new Chart(document.getElementById('dashPie'), {
            type: 'doughnut',
            data: { labels: catLabels, datasets: [{ data: catRevenue, backgroundColor: cafePalette }] },
            options: { responsive: true }
        });
    </script>
</x-app-layout>
