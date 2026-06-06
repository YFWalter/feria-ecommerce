@extends('layouts.admin')
@section('title', 'Pedido ' . $order->number)

@section('header-actions')
    <a href="{{ route('admin.pedidos.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver a pedidos</a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Detalle de ítems --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800">Productos</h2>
                <span class="text-xs text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <table class="w-full text-sm">
                <tbody>
                    @foreach($order->items as $item)
                        <tr class="border-b border-gray-50">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                                <p class="text-xs text-gray-400">${{ number_format($item->price, 0, ',', '.') }} × {{ $item->quantity }}</p>
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-900">
                                ${{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-5 py-4 space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span>${{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Envío</span>
                    <span>{{ $order->shipping > 0 ? '$' . number_format($order->shipping, 0, ',', '.') : 'A coordinar' }}</span>
                </div>
                <div class="flex justify-between font-bold text-gray-900 border-t border-gray-100 pt-2">
                    <span>Total</span>
                    <span>${{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Datos del cliente --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <h2 class="font-semibold text-gray-800 mb-3">Cliente y envío</h2>
            <dl class="text-sm text-gray-600 space-y-1.5">
                <div><dt class="inline font-medium text-gray-700">Nombre:</dt> <dd class="inline">{{ $order->customer_name }}</dd></div>
                <div><dt class="inline font-medium text-gray-700">Email:</dt> <dd class="inline">{{ $order->customer_email }}</dd></div>
                @if($order->customer_phone)
                    <div><dt class="inline font-medium text-gray-700">Teléfono:</dt> <dd class="inline">{{ $order->customer_phone }}</dd></div>
                @endif
                <div><dt class="inline font-medium text-gray-700">Dirección:</dt> <dd class="inline">{{ $order->shipping_address }}</dd></div>
                @if($order->notes)
                    <div><dt class="inline font-medium text-gray-700">Notas:</dt> <dd class="inline">{{ $order->notes }}</dd></div>
                @endif
            </dl>
        </div>
    </div>

    {{-- Panel de estado --}}
    <div class="space-y-6">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <h2 class="font-semibold text-gray-800 mb-4">Estado del pedido</h2>

            <form action="{{ route('admin.pedidos.update', $order) }}" method="POST" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="status" id="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-sm focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ $order->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Estado del pago</label>
                    <select name="payment_status" id="payment_status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white text-sm focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Pagado</option>
                        <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Fallido</option>
                    </select>
                </div>

                <button class="w-full bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2.5 rounded-lg transition-colors">
                    Guardar
                </button>
            </form>
        </div>

        @if($order->mp_payment_id || $order->mp_preference_id)
            <div class="bg-white border border-gray-200 rounded-xl p-5 text-sm text-gray-600 space-y-1">
                <h3 class="font-semibold text-gray-800 mb-2">MercadoPago</h3>
                @if($order->mp_payment_id)
                    <p><span class="font-medium text-gray-700">Payment ID:</span> {{ $order->mp_payment_id }}</p>
                @endif
                @if($order->mp_preference_id)
                    <p class="break-all"><span class="font-medium text-gray-700">Preference:</span> {{ $order->mp_preference_id }}</p>
                @endif
            </div>
        @endif
    </div>

</div>
@endsection
