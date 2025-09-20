<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function index(Request $request): View
    {
        $start = $request->date_start ? Carbon::parse($request->date_start)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $request->date_end ? Carbon::parse($request->date_end)->endOfDay() : Carbon::now()->endOfDay();
        $method = $request->payment_method;

        $query = Transaction::query()
            ->whereBetween('created_at', [$start, $end])
            ->when($method, fn($q) => $q->where('payment_method', $method))
            ->with(['items']);

        $transactions = $query->latest('id')->paginate(10)->withQueryString();

        // Summary
        $summaryQuery = Transaction::query()
            ->whereBetween('created_at', [$start, $end])
            ->when($method, fn($q) => $q->where('payment_method', $method));

        $totalSales = (clone $summaryQuery)->sum('total_amount');
        $totalItems = TransactionItem::whereIn('transaction_id', (clone $summaryQuery)->pluck('id'))->sum('quantity');

        return view('reports.index', [
            'transactions' => $transactions,
            'totalSales' => $totalSales,
            'totalItems' => $totalItems,
            'filters' => [
                'date_start' => $start->toDateString(),
                'date_end' => $end->toDateString(),
                'payment_method' => $method,
            ]
        ]);
    }

    public function exportCsv(Request $request)
    {
        $start = $request->date_start ? Carbon::parse($request->date_start)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $request->date_end ? Carbon::parse($request->date_end)->endOfDay() : Carbon::now()->endOfDay();
        $method = $request->payment_method;

        $transactions = Transaction::query()
            ->whereBetween('created_at', [$start, $end])
            ->when($method, fn($q) => $q->where('payment_method', $method))
            ->with(['items.product'])
            ->orderBy('id')
            ->get();

        $filename = 'sales_' . $start->toDateString() . '_' . $end->toDateString() . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($transactions) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Items', 'Amount', 'Method']);
            foreach ($transactions as $t) {
                $items = $t->items->map(fn($it) => $it->quantity . 'x ' . $it->product->name)->implode('; ');
                fputcsv($out, [
                    $t->created_at->format('Y-m-d H:i'),
                    $items,
                    number_format($t->total_amount, 2, '.', ''),
                    $t->payment_method,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function printView(Request $request): View
    {
        $start = $request->date_start ? Carbon::parse($request->date_start)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $request->date_end ? Carbon::parse($request->date_end)->endOfDay() : Carbon::now()->endOfDay();
        $method = $request->payment_method;

        $transactions = Transaction::query()
            ->whereBetween('created_at', [$start, $end])
            ->when($method, fn($q) => $q->where('payment_method', $method))
            ->with('items.product')
            ->orderBy('id')
            ->get();

        $totalSales = $transactions->sum('total_amount');
        $totalItems = $transactions->flatMap->items->sum('quantity');

        return view('reports.print', compact('transactions','totalSales','totalItems','start','end','method'));
    }
}


