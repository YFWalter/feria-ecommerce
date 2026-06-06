<div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
    <a href="{{ route('products.show', $product->slug) }}">
        <div class="aspect-square bg-gray-100 overflow-hidden">
            @if($product->first_image)
                <img src="{{ asset('storage/' . $product->first_image) }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-300">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif
        </div>
    </a>

    <div class="p-4">
        @if($product->category)
            <span class="text-xs text-primary-600 font-medium">{{ $product->category->name }}</span>
        @endif
        <a href="{{ route('products.show', $product->slug) }}">
            <h3 class="font-medium text-gray-900 mt-1 hover:text-primary-600 transition-colors line-clamp-2">
                {{ $product->name }}
            </h3>
        </a>

        <div class="mt-3">
            <div class="mb-3">
                <span class="text-lg font-bold text-gray-900">${{ number_format($product->price, 0, ',', '.') }}</span>
                @if($product->hasDiscount())
                    <span class="ml-2 text-sm text-gray-400 line-through">${{ number_format($product->compare_price, 0, ',', '.') }}</span>
                    <span class="ml-1 text-xs bg-primary-100 text-primary-700 font-medium px-1.5 py-0.5 rounded">-{{ $product->discountPercent() }}%</span>
                @endif
            </div>

            @if($product->stock > 0)
                <form action="{{ route('cart.add') }}" method="POST" x-data="{ qty: 1 }"
                      class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" :value="qty">

                    {{-- Selector de cantidad --}}
                    <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden flex-shrink-0 self-start">
                        <button type="button" @click="qty = Math.max(1, qty - 1)"
                                class="px-2.5 py-1.5 hover:bg-gray-100 text-gray-600 transition-colors text-sm">−</button>
                        <span class="px-2 py-1.5 text-sm font-medium border-x border-gray-300 min-w-[1.75rem] text-center" x-text="qty"></span>
                        <button type="button" @click="qty = Math.min({{ $product->stock }}, qty + 1)"
                                class="px-2.5 py-1.5 hover:bg-gray-100 text-gray-600 transition-colors text-sm">+</button>
                    </div>

                    <button type="submit"
                            class="w-full sm:flex-1 bg-primary-500 hover:bg-primary-600 text-white text-xs font-medium px-2 py-2 sm:py-1.5 rounded-lg transition-colors">
                        Agregar
                    </button>
                </form>
            @else
                <span class="block text-center bg-gray-100 text-gray-400 text-xs font-medium px-3 py-1.5 rounded-lg cursor-not-allowed">
                    Sin stock
                </span>
            @endif
        </div>
    </div>
</div>
