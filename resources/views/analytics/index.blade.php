<x-app-layout>
    <h1 class="text-xl font-semibold text-cafe-900 mb-4">Analytics</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <h2 class="text-sm font-semibold text-cafe-900 mb-2">Sales per Product</h2>
            <canvas id="chartProducts" height="160"></canvas>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h2 class="text-sm font-semibold text-cafe-900 mb-2">Monthly Sales Trend</h2>
            <canvas id="chartTrend" height="160"></canvas>
        </div>
        <div class="bg-white rounded-lg shadow p-4 lg:col-span-2">
            <h2 class="text-sm font-semibold text-cafe-900 mb-2">Payment Methods</h2>
            <canvas id="chartPayments" height="160"></canvas>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h2 class="text-sm font-semibold text-cafe-900 mb-2">Top 5 Best Sellers</h2>
            <ul class="text-sm list-disc pl-5">
                @forelse($topProducts as $p)
                    <li>{{ $p->product->name }} — {{ $p->qty }} sold</li>
                @empty
                    <li>No data</li>
                @endforelse
            </ul>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h2 class="text-sm font-semibold text-cafe-900 mb-2">Top 5 Least Sellers</h2>
            <ul class="text-sm list-disc pl-5">
                @forelse($leastProducts as $p)
                    <li>{{ $p->product->name }} — {{ $p->qty }} sold</li>
                @empty
                    <li>No data</li>
                @endforelse
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const cafePalette = ['#C59B78','#E9CDAF','#7a5c47','#5b4334','#F5E6D3'];
        // Data from backend
        const salesNames = @json($salesPerProduct->pluck('product.name'));
        const salesQty = @json($salesPerProduct->pluck('qty'));
        const salesRevenue = @json($salesPerProduct->pluck('revenue'));

        const trendLabels = @json($trend->pluck('ym'));
        const trendRevenue = @json($trend->pluck('revenue'));

        const payLabels = @json($paymentDist->pluck('payment_method'));
        const payCounts = @json($paymentDist->pluck('count'));

        // Bar: Sales per product
        new Chart(document.getElementById('chartProducts'), {
            type: 'bar',
            data: {
                labels: salesNames,
                datasets: [{
                    label: 'Revenue',
                    data: salesRevenue,
                    backgroundColor: cafePalette[0]
                },{
                    label: 'Qty',
                    data: salesQty,
                    backgroundColor: cafePalette[1],
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } }
                }
            }
        });

        // Line: Monthly trend
        new Chart(document.getElementById('chartTrend'), {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Revenue',
                    data: trendRevenue,
                    borderColor: cafePalette[2],
                    backgroundColor: cafePalette[4],
                    tension: 0.3,
                    fill: true
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // Donut: Payment methods
        new Chart(document.getElementById('chartPayments'), {
            type: 'doughnut',
            data: {
                labels: payLabels,
                datasets: [{ data: payCounts, backgroundColor: cafePalette }]
            },
            options: { responsive: true }
        });
    </script>
</x-app-layout>


