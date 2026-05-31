<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products'       => Product::count(),
            'categories'     => Category::count(),
            'orders'         => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'revenue'        => Order::where('payment_status', 'paid')->sum('total'),
        ];

        $recentOrders = Order::latest()->take(8)->get();

        $lowStock = Product::where('is_active', true)
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStock'));
    }
}
