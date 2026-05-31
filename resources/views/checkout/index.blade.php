@extends('layouts.app')
@section('title', 'Finalizar compra')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Finalizar compra</h1>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('checkout.store') }}" method="POST"
          class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf

        {{-- Datos del cliente --}}
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl p-5 space-y-4">
            <h2 class="font-semibold text-gray-800">Tus datos</h2>

            <div>
                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre completo *</label>
                <input type="text" name="customer_name" id="customer_name" required
                       value="{{ old('customer_name') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="customer_email" id="customer_email" required
                           value="{{ old('customer_email') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none">
                </div>
                <div>
                    <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="customer_phone" id="customer_phone"
                           value="{{ old('customer_phone') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none">
                </div>
            </div>

            <div>
                <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">Dirección de envío *</label>
                <textarea name="shipping_address" id="shipping_address" rows="3" required
                          placeholder="Calle, número, piso/depto, localidad, código postal"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none">{{ old('shipping_address') }}</textarea>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
                <textarea name="notes" id="notes" rows="2"
                          placeholder="Indicaciones para la entrega, horarios, etc."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- Resumen --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5 h-fit">
            <h2 class="font-semibold text-gray-800 mb-4">Tu pedido</h2>
            <div class="space-y-2 text-sm">
                @foreach($cart as $item)
                    <div class="flex justify-between text-gray-600">
                        <span class="truncate mr-2">{{ $item['name'] }} × {{ $item['quantity'] }}</span>
                        <span class="whitespace-nowrap">${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-gray-200 mt-4 pt-4 space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span>${{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Envío</span>
                    <span>{{ $shipping > 0 ? '$' . number_format($shipping, 0, ',', '.') : 'A coordinar' }}</span>
                </div>
            </div>

            <div class="border-t border-gray-200 mt-3 pt-3 flex justify-between font-bold text-gray-900">
                <span>Total</span>
                <span>${{ number_format($total, 0, ',', '.') }}</span>
            </div>

            <button type="submit"
                    class="mt-4 block w-full text-center bg-amber-500 hover:bg-amber-600 text-white font-semibold py-3 rounded-xl transition-colors">
                Confirmar pedido
            </button>
            <a href="{{ route('cart.index') }}"
               class="mt-2 block text-center text-sm text-gray-500 hover:text-gray-700">
                Volver al carrito
            </a>
        </div>
    </form>
</div>
@endsection
