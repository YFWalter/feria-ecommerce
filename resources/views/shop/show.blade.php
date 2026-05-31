@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

    <nav class="text-sm text-gray-500 mb-6 flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-amber-600">Inicio</a>
        <span>/</span>
        <a href="{{ route('products.index') }}" class="hover:text-amber-600">Productos</a>
        @if($product->category)
            <span>/</span>
            <a href="{{ route('products.index', ['categoria' => $product->category->slug]) }}"
               class="hover:text-amber-600">{{ $product->category->name }}</a>
        @endif
        <span>/</span>
        <span class="text-gray-700">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

        {{-- Galería --}}
        <div x-data="{ active: 0 }">
            <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden">
                @php $images = $product->images ?? [] @endphp
                @if(count($images))
                    @foreach($images as $i => $img)
                        <img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }}"
                             x-show="active === {{ $i }}"
                             class="w-full h-full object-cover">
                    @endforeach
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                        <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </div>
            @if(count($images) > 1)
                <div class="flex gap-2 mt-3">
                    @foreach($images as $i => $img)
                        <button @click="active = {{ $i }}"
                                :class="active === {{ $i }} ? 'border-amber-500' : 'border-transparent'"
                                class="w-16 h-16 rounded-lg border-2 overflow-hidden flex-shrink-0 transition-colors">
                            <img src="{{ asset('storage/' . $img) }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="flex flex-col">
            @if($product->category)
                <span class="text-sm text-amber-600 font-medium">{{ $product->category->name }}</span>
            @endif

            <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $product->name }}</h1>

            @if($product->sku)
                <p class="text-xs text-gray-400 mt-1">SKU: {{ $product->sku }}</p>
            @endif

            <div class="flex items-center gap-3 mt-4">
                <span class="text-3xl font-bold text-gray-900">
                    ${{ number_format($product->price, 0, ',', '.') }}
                </span>
                @if($product->hasDiscount())
                    <span class="text-lg text-gray-400 line-through">
                        ${{ number_format($product->compare_price, 0, ',', '.') }}
                    </span>
                    <span class="bg-amber-100 text-amber-700 text-sm font-semibold px-2 py-0.5 rounded">
                        -{{ $product->discountPercent() }}%
                    </span>
                @endif
            </div>

            @if($product->description)
                <p class="mt-4 text-gray-600 leading-relaxed">{{ $product->description }}</p>
            @endif

            <div class="mt-4">
                @if($product->stock > 0)
                    <span class="text-sm text-green-600 font-medium">✓ En stock ({{ $product->stock }} disponibles)</span>
                @else
                    <span class="text-sm text-red-500 font-medium">Sin stock</span>
                @endif
            </div>

            @if($product->stock > 0)
                <form action="{{ route('cart.add') }}" method="POST" class="mt-6" x-data="{ qty: 1 }">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" x-model="qty">

                    <div class="flex items-center gap-3 mb-4">
                        <label class="text-sm font-medium text-gray-700">Cantidad:</label>
                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                            <button type="button" @click="qty = Math.max(1, qty - 1)"
                                    class="px-3 py-2 hover:bg-gray-100 text-gray-600 transition-colors">−</button>
                            <span class="px-4 py-2 text-sm font-medium border-x border-gray-300" x-text="qty"></span>
                            <button type="button" @click="qty = Math.min({{ $product->stock }}, qty + 1)"
                                    class="px-3 py-2 hover:bg-gray-100 text-gray-600 transition-colors">+</button>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-3 rounded-xl transition-colors text-lg">
                        Agregar al carrito
                    </button>
                </form>

                <a href="{{ route('cart.index') }}"
                   class="mt-3 block text-center border border-gray-300 text-gray-700 py-2.5 rounded-xl hover:bg-gray-50 transition-colors text-sm">
                    Ver carrito
                </a>
            @endif
        </div>
    </div>

    {{-- Productos relacionados --}}
    @if($related->count())
        <div class="mt-16">
            <h2 class="text-xl font-bold text-gray-900 mb-6">También te puede interesar</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @foreach($related as $rel)
                    @include('components.product-card', ['product' => $rel])
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
