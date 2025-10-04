<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $start = $request->date_start ? Carbon::parse($request->date_start)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $request->date_end ? Carbon::parse($request->date_end)->endOfDay() : Carbon::now()->endOfDay();

        // Sales per product
        $salesPerProduct = TransactionItem::selectRaw('product_id, SUM(quantity) as qty, SUM(subtotal) as revenue')
            ->whereHas('transaction', fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->with('product:id,name')
            ->get();

        $topProducts = $salesPerProduct->sortByDesc('qty')->take(5)->values();
        $leastProducts = $salesPerProduct->sortBy('qty')->take(5)->values();

        // Monthly trend (last 12 months)
        $trend = Transaction::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(total_amount) as revenue")
            ->whereBetween('created_at', [Carbon::now()->subMonths(11)->startOfMonth(), Carbon::now()->endOfMonth()])
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        // Payment method distribution
        $paymentDist = Transaction::selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as revenue')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('payment_method')
            ->get();

        return view('analytics.index', compact('salesPerProduct','trend','paymentDist','start','end','topProducts','leastProducts'));
    }
}


