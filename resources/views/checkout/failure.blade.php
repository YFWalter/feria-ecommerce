@extends('layouts.app')
@section('title', 'Pago rechazado')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white border border-gray-200 rounded-xl p-8 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900">No pudimos procesar tu pago</h1>
        <p class="text-gray-500 mt-2">
            @if($order)
                El pago de tu pedido <span class="font-semibold text-gray-700">{{ $order->number }}</span> fue rechazado o cancelado.
            @else
                El pago fue rechazado o cancelado.
            @endif
            Podés intentar nuevamente desde tu carrito.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center mt-6">
            <a href="{{ route('cart.index') }}"
               class="inline-block bg-primary-500 hover:bg-primary-600 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">
                Volver al carrito
            </a>
            <a href="{{ route('products.index') }}"
               class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-2.5 rounded-xl transition-colors">
                Seguir comprando
            </a>
        </div>
    </div>
</div>
@endsection
