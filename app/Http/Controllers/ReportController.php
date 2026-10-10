<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Product;
use App\Models\Station;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        // Completed transactions query scope
        $completedTransactions = Transaction::whereBetween('created_at', ["$from 00:00:00", "$to 23:59:59"])
            ->where('status', 'completed');

        // Revenue Breakdown Calculations
        $netRevenue = (float) $completedTransactions->sum('total_amount');
        $totalDiscounts = (float) $completedTransactions->sum('discount_amount');
        $grossRevenue = $netRevenue + $totalDiscounts;

        // Alias for view compatibility
        $revenue = $netRevenue;

        $expenses = (float) Expense::whereBetween('expense_date', [$from, $to])->sum('amount');
        $profit = $netRevenue - $expenses;

        // Sales breakdown by Station (Gross vs Net)
        $salesByStation = Transaction::selectRaw('station_id, SUM(total_amount) as net_total, SUM(discount_amount) as discount_total, SUM(total_amount + discount_amount) as gross_total')
            ->whereBetween('created_at', ["$from 00:00:00", "$to 23:59:59"])
            ->where('status', 'completed')
            ->groupBy('station_id')
            ->with('station')
            ->get();

        // Top-selling products
        $topProducts = TransactionItem::selectRaw('product_id, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->whereHas('transaction', function ($q) use ($from, $to) {
                $q->whereBetween('created_at', ["$from 00:00:00", "$to 23:59:59"])
                    ->where('status', 'completed');
            })
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->with('product')
            ->take(10)
            ->get();

        // Expenses broken down by category
        $expensesByCategory = Expense::selectRaw('category, SUM(amount) as total')
            ->whereBetween('expense_date', [$from, $to])
            ->groupBy('category')
            ->get();

        return view('reports.index', compact(
            'from',
            'to',
            'revenue',
            'grossRevenue',
            'totalDiscounts',
            'netRevenue',
            'expenses',
            'profit',
            'salesByStation',
            'topProducts',
            'expensesByCategory'
        ));
    }
}