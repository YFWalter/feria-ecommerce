<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public const STATUSES = [
        'pending'    => 'Pendiente',
        'processing' => 'En proceso',
        'shipped'    => 'Enviado',
        'delivered'  => 'Entregado',
        'cancelled'  => 'Cancelado',
    ];

    public function index(Request $request)
    {
        $orders = Order::withCount('items')
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->q, fn($q, $term) =>
                $q->where(fn($sub) =>
                    $sub->where('number', 'like', "%{$term}%")
                        ->orWhere('customer_name', 'like', "%{$term}%")
                        ->orWhere('customer_email', 'like', "%{$term}%")))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders'   => $orders,
            'statuses' => self::STATUSES,
        ]);
    }

    public function show(Order $order)
    {
        $order->load('items');

        return view('admin.orders.show', [
            'order'    => $order,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status'         => 'required|in:' . implode(',', array_keys(self::STATUSES)),
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        $order->update($data);

        return back()->with('success', 'Pedido actualizado.');
    }
}
