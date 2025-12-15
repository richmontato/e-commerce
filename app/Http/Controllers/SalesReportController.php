<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SalesReportController extends Controller
{
    /**
     * Show sales report page
     */
    public function index(): View
    {
        Gate::authorize('view-sales-report');
        return view('reports.sales');
    }

    /**
     * Summary sales report (orders, revenue, top products).
     */
    public function summary(Request $request): JsonResponse
    {
        Gate::authorize('view-sales-report');
        $from = $request->date('from', now()->subDays(30));
        $to   = $request->date('to', now());

        // Summary revenue
        $summary = DB::table('orders')
            ->whereBetween('created_at', [$from, $to])
            ->where('status', '!=', 'canceled')
            ->selectRaw('COUNT(*) as orders,
                         SUM(subtotal_amount) as gross,
                         SUM(discount_amount) as discounts,
                         SUM(total_amount) as net')
            ->first();

        // Daily revenue
        $daily = DB::table('orders')
            ->whereBetween('created_at', [$from, $to])
            ->where('status', '!=', 'canceled')
            ->selectRaw("DATE(created_at) as day, SUM(total_amount) as total")
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Top products
        $topProducts = DB::table('order_items')
            ->join('orders','orders.id','=','order_items.order_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->where('orders.status','!=','canceled')
            ->selectRaw('product_name, SUM(qty) as qty, SUM(line_total) as revenue')
            ->groupBy('product_name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return response()->json(compact('summary','daily','topProducts'));
    }
}
