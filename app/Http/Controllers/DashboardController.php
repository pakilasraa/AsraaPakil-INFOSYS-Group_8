<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $todayStart = Carbon::today();
        $todayEnd = Carbon::now();

        $todaysSales = Transaction::whereBetween('created_at', [$todayStart, $todayEnd])->sum('total_amount');
        $transactionsToday = Transaction::whereBetween('created_at', [$todayStart, $todayEnd])->count();

        $bestSeller = TransactionItem::selectRaw('product_id, SUM(quantity) as qty')
            ->whereHas('transaction', fn($q) => $q->whereBetween('created_at', [$todayStart, $todayEnd]))
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->with('product:id,name')
            ->first();

        // Last 7 days revenue
        $start7 = Carbon::now()->subDays(6)->startOfDay();
        $line = Transaction::selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as d, SUM(total_amount) as revenue")
            ->whereBetween('created_at', [$start7, $todayEnd])
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        // Sales per category (by revenue)
        $perCategory = TransactionItem::selectRaw('products.category_id, SUM(transaction_items.subtotal) as revenue')
            ->join('products', 'products.id', '=', 'transaction_items.product_id')
            ->whereHas('transaction', fn($q) => $q->whereBetween('created_at', [$start7, $todayEnd]))
            ->groupBy('products.category_id')
            ->get();

        $categoryNames = Category::pluck('name','id');

        return view('dashboard', [
            'todaysSales' => $todaysSales,
            'transactionsToday' => $transactionsToday,
            'bestSeller' => $bestSeller,
            'line' => $line,
            'perCategory' => $perCategory,
            'categoryNames' => $categoryNames,
        ]);
    }
}


