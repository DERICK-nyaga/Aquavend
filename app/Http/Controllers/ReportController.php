<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Product;
use App\Models\Station;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $revenue = Transaction::whereBetween('created_at', ["$from 00:00:00", "$to 23:59:59"])
            ->where('status', 'completed')
            ->sum('total_amount');

        $expenses = Expense::whereBetween('expense_date', [$from, $to])->sum('amount');

        $profit = $revenue - $expenses;

        $salesByStation = Transaction::selectRaw('station_id, SUM(total_amount) as total')
            ->whereBetween('created_at', ["$from 00:00:00", "$to 23:59:59"])
            ->where('status', 'completed')
            ->groupBy('station_id')
            ->with('station')
            ->get();

        $topProducts = \App\Models\TransactionItem::selectRaw('product_id, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->whereHas('transaction', function ($q) use ($from, $to) {
                $q->whereBetween('created_at', ["$from 00:00:00", "$to 23:59:59"])
                    ->where('status', 'completed');
            })
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->with('product')
            ->take(10)
            ->get();

        $expensesByCategory = Expense::selectRaw('category, SUM(amount) as total')
            ->whereBetween('expense_date', [$from, $to])
            ->groupBy('category')
            ->get();

        return view('reports.index', compact(
            'from', 'to', 'revenue', 'expenses', 'profit',
            'salesByStation', 'topProducts', 'expensesByCategory'
        ));
    }
}