@extends('layouts.app')
@section('title', 'Pedido confirmado')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white border border-gray-200 rounded-xl p-8 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900">¡Gracias por tu compra!</h1>
        <p class="text-gray-500 mt-2">
            Tu pedido <span class="font-semibold text-gray-700">{{ $order->number }}</span> fue registrado correctamente.
        </p>

        @if($order->payment_status === 'paid')
            <p class="inline-flex items-center gap-1.5 mt-4 px-3 py-1 rounded-full bg-green-50 text-green-700 text-sm font-medium">
                Pago aprobado
            </p>
        @else
            <p class="inline-flex items-center gap-1.5 mt-4 px-3 py-1 rounded-full bg-primary-50 text-primary-700 text-sm font-medium">
                Pago pendiente · nos contactaremos para coordinarlo
            </p>
        @endif
    </div>

    {{-- Detalle del pedido --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 mt-6">
        <h2 class="font-semibold text-gray-800 mb-4">Detalle del pedido</h2>

        <div class="space-y-2 text-sm">
            @foreach($order->items as $item)
                <div class="flex justify-between text-gray-600">
                    <span class="mr-2">{{ $item->product_name }} × {{ $item->quantity }}</span>
                    <span class="whitespace-nowrap">${{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>

        <div class="border-t border-gray-200 mt-4 pt-4 space-y-2 text-sm">
            <div class="flex justify-between text-gray-600">
                <span>Subtotal</span>
                <span>${{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Envío</span>
                <span>{{ $order->shipping > 0 ? '$' . number_format($order->shipping, 0, ',', '.') : 'A coordinar' }}</span>
            </div>
        </div>

        <div class="border-t border-gray-200 mt-3 pt-3 flex justify-between font-bold text-gray-900">
            <span>Total</span>
            <span>${{ number_format($order->total, 0, ',', '.') }}</span>
        </div>

        <div class="border-t border-gray-200 mt-4 pt-4 text-sm text-gray-600 space-y-1">
            <p><span class="font-medium text-gray-700">Envío a:</span> {{ $order->customer_name }}</p>
            <p>{{ $order->shipping_address }}</p>
            <p>{{ $order->customer_email }}@if($order->customer_phone) · {{ $order->customer_phone }}@endif</p>
        </div>
    </div>

    <div class="text-center mt-6">
        <a href="{{ route('products.index') }}"
           class="inline-block bg-primary-500 hover:bg-primary-600 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">
            Seguir comprando
        </a>
    </div>
</div>
@endsection
