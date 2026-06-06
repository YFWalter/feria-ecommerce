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
        @elseif($order->payment_method === 'transfer')
            <p class="inline-flex items-center gap-1.5 mt-4 px-3 py-1 rounded-full bg-primary-50 text-primary-700 text-sm font-medium">
                Pago pendiente · transferí y enviá el comprobante
            </p>
        @else
            <p class="inline-flex items-center gap-1.5 mt-4 px-3 py-1 rounded-full bg-primary-50 text-primary-700 text-sm font-medium">
                Pago pendiente · nos contactaremos para coordinarlo
            </p>
        @endif
    </div>

    {{-- Datos para transferencia --}}
    @if($order->payment_method === 'transfer' && $order->payment_status !== 'paid')
        <div class="bg-white border border-primary-200 rounded-xl p-6 mt-6">
            <h2 class="font-semibold text-gray-800 mb-1">Datos para transferir</h2>
            <p class="text-sm text-gray-500 mb-4">{{ setting('transfer_instructions') }}</p>
            <dl class="text-sm space-y-2">
                @if(setting('transfer_holder'))
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">Titular</dt><dd class="font-medium text-gray-900 text-right">{{ setting('transfer_holder') }}</dd></div>
                @endif
                @if(setting('transfer_bank'))
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">Banco</dt><dd class="font-medium text-gray-900 text-right">{{ setting('transfer_bank') }}</dd></div>
                @endif
                @if(setting('transfer_cbu'))
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">CBU / CVU</dt><dd class="font-medium text-gray-900 text-right break-all">{{ setting('transfer_cbu') }}</dd></div>
                @endif
                @if(setting('transfer_alias'))
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">Alias</dt><dd class="font-medium text-gray-900 text-right">{{ setting('transfer_alias') }}</dd></div>
                @endif
                <div class="flex justify-between gap-3 border-t border-gray-100 pt-2 mt-2">
                    <dt class="text-gray-500">Importe a transferir</dt>
                    <dd class="font-bold text-gray-900 text-right">${{ number_format($order->total, 0, ',', '.') }}</dd>
                </div>
            </dl>

            @if(setting('whatsapp_number'))
                @php $wa = preg_replace('/\D+/', '', (string) setting('whatsapp_number')); @endphp
                <a href="https://wa.me/{{ $wa }}?text={{ rawurlencode('¡Hola! Te envío el comprobante del pedido ' . $order->number) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="mt-4 inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.587-5.945C.16 5.335 5.495 0 12.05 0a11.817 11.817 0 018.413 3.488 11.824 11.824 0 013.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 01-5.688-1.448L.057 24z"/></svg>
                    Enviar comprobante por WhatsApp
                </a>
            @endif
        </div>
    @endif

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
