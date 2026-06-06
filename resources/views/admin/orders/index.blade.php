@extends('layouts.admin')
@section('title', 'Pedidos')

@section('content')

{{-- Filtros --}}
<form method="GET" class="flex flex-wrap items-center gap-3 mb-4">
    <input type="text" name="q" value="{{ request('q') }}"
           placeholder="Buscar por nº, cliente o email…"
           class="flex-1 min-w-[200px] max-w-sm border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
    <select name="status" onchange="this.form.submit()"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
        <option value="">Todos los estados</option>
        @foreach($statuses as $key => $label)
            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <button class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
        Filtrar
    </button>
</form>

<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
    @if($orders->isEmpty())
        <p class="px-5 py-12 text-center text-sm text-gray-500">No hay pedidos que coincidan.</p>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b border-gray-100">
                    <th class="px-5 py-3 font-medium">Pedido</th>
                    <th class="px-5 py-3 font-medium">Cliente</th>
                    <th class="px-5 py-3 font-medium">Estado</th>
                    <th class="px-5 py-3 font-medium">Pago</th>
                    <th class="px-5 py-3 font-medium text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 cursor-pointer"
                        onclick="window.location='{{ route('admin.pedidos.show', $order) }}'">
                        <td class="px-5 py-3">
                            <span class="font-medium text-gray-900">{{ $order->number }}</span>
                            <p class="text-xs text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }} · {{ $order->items_count }} ítem(s)</p>
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $order->customer_name }}
                            <p class="text-xs text-gray-400">{{ $order->customer_email }}</p>
                        </td>
                        <td class="px-5 py-3">
                            @include('admin.partials.order-status', ['status' => $order->status])
                        </td>
                        <td class="px-5 py-3">
                            @include('admin.partials.payment-status', ['status' => $order->payment_status])
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

<div class="mt-4">
    {{ $orders->links() }}
</div>
@endsection
