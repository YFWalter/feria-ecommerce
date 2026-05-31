@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

{{-- Tarjetas de estadísticas --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <p class="text-sm text-gray-500">Productos</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['products'] }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <p class="text-sm text-gray-500">Categorías</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['categories'] }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <p class="text-sm text-gray-500">Pedidos</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['orders'] }}</p>
        @if($stats['pending_orders'] > 0)
            <p class="text-xs text-amber-600 mt-1">{{ $stats['pending_orders'] }} pendiente(s)</p>
        @endif
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <p class="text-sm text-gray-500">Ingresos (pagados)</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">${{ number_format($stats['revenue'], 0, ',', '.') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Pedidos recientes --}}
    <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800">Pedidos recientes</h2>
            <a href="{{ route('admin.pedidos.index') }}" class="text-sm text-amber-600 hover:text-amber-700">Ver todos</a>
        </div>

        @if($recentOrders->isEmpty())
            <p class="px-5 py-8 text-center text-sm text-gray-500">Todavía no hay pedidos.</p>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-100">
                        <th class="px-5 py-2.5 font-medium">Pedido</th>
                        <th class="px-5 py-2.5 font-medium">Cliente</th>
                        <th class="px-5 py-2.5 font-medium">Estado</th>
                        <th class="px-5 py-2.5 font-medium text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                        <tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.pedidos.show', $order) }}" class="font-medium text-gray-900 hover:text-amber-600">
                                    {{ $order->number }}
                                </a>
                                <p class="text-xs text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $order->customer_name }}</td>
                            <td class="px-5 py-3">
                                @include('admin.partials.order-status', ['status' => $order->status])
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-900">
                                ${{ number_format($order->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Stock bajo --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200">
            <h2 class="font-semibold text-gray-800">Stock bajo</h2>
        </div>

        @if($lowStock->isEmpty())
            <p class="px-5 py-8 text-center text-sm text-gray-500">Sin productos con stock bajo.</p>
        @else
            <ul class="divide-y divide-gray-50">
                @foreach($lowStock as $product)
                    <li class="px-5 py-3 flex items-center justify-between">
                        <a href="{{ route('admin.productos.edit', $product) }}"
                           class="text-sm text-gray-700 hover:text-amber-600 truncate mr-2">
                            {{ $product->name }}
                        </a>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $product->stock == 0 ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-700' }}">
                            {{ $product->stock }} u.
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

</div>
@endsection
