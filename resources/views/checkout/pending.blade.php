@extends('layouts.app')
@section('title', 'Pago pendiente')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white border border-gray-200 rounded-xl p-8 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-amber-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900">Tu pago está pendiente</h1>
        <p class="text-gray-500 mt-2">
            @if($order)
                Estamos esperando la confirmación del pago de tu pedido
                <span class="font-semibold text-gray-700">{{ $order->number }}</span>.
            @else
                Estamos esperando la confirmación de tu pago.
            @endif
            Te avisaremos por email apenas se acredite.
        </p>

        <a href="{{ route('products.index') }}"
           class="inline-block mt-6 bg-amber-500 hover:bg-amber-600 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">
            Volver a la tienda
        </a>
    </div>
</div>
@endsection
