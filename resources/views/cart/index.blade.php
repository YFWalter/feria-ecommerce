@extends('layouts.app')
@section('title', 'Carrito')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Tu carrito</h1>

    @if(empty($cart))
        <div class="text-center py-20">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-gray-500 mb-4">Tu carrito está vacío</p>
            <a href="{{ route('products.index') }}"
               class="inline-block bg-primary-500 hover:bg-primary-600 text-white font-medium px-6 py-2.5 rounded-xl transition-colors">
                Ver productos
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Items --}}
            <div class="lg:col-span-2 space-y-3">
                @foreach($cart as $rowId => $item)
                    <div class="bg-white border border-gray-200 rounded-xl p-4 flex gap-4 items-center">
                        <a href="{{ route('products.show', $item['slug']) }}" class="flex-shrink-0">
                            <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden">
                                @if($item['image'])
                                    <img src="{{ asset('storage/' . $item['image']) }}"
                                         alt="{{ $item['name'] }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </div>
                        </a>

                        <div class="flex-1 min-w-0">
                            <a href="{{ route('products.show', $item['slug']) }}"
                               class="font-medium text-gray-900 hover:text-primary-600 transition-colors line-clamp-1">
                                {{ $item['name'] }}
                            </a>
                            <p class="text-primary-600 font-semibold mt-1">
                                ${{ number_format($item['price'], 0, ',', '.') }}
                            </p>
                        </div>

                        {{-- Cantidad --}}
                        <form action="{{ route('cart.update', $rowId) }}" method="POST" class="flex items-center gap-2">
                            @csrf @method('PATCH')
                            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}"
                                        class="px-2.5 py-1.5 hover:bg-gray-100 text-gray-600 transition-colors text-sm">−</button>
                                <span class="px-3 py-1.5 text-sm font-medium border-x border-gray-300">{{ $item['quantity'] }}</span>
                                <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}"
                                        @if($item['quantity'] >= $item['stock']) disabled @endif
                                        class="px-2.5 py-1.5 hover:bg-gray-100 text-gray-600 transition-colors text-sm disabled:opacity-40">+</button>
                            </div>
                        </form>

                        <p class="font-bold text-gray-900 w-24 text-right">
                            ${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                        </p>

                        <form action="{{ route('cart.remove', $rowId) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="text-gray-400 hover:text-red-500 transition-colors ml-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            {{-- Resumen --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5 h-fit">
                <h2 class="font-semibold text-gray-800 mb-4">Resumen del pedido</h2>
                <div class="space-y-2 text-sm">
                    @foreach($cart as $item)
                        <div class="flex justify-between text-gray-600">
                            <span class="truncate mr-2">{{ $item['name'] }} × {{ $item['quantity'] }}</span>
                            <span class="whitespace-nowrap">${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-gray-200 mt-4 pt-4 flex justify-between font-bold text-gray-900">
                    <span>Total</span>
                    <span>${{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <a href="{{ route('checkout.index') }}"
                   class="mt-4 block w-full text-center bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 rounded-xl transition-colors">
                    Finalizar compra
                </a>
                <a href="{{ route('products.index') }}"
                   class="mt-2 block text-center text-sm text-gray-500 hover:text-gray-700">
                    Seguir comprando
                </a>
            </div>

        </div>
    @endif
</div>
@endsection
