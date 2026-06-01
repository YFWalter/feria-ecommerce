<nav class="bg-white border-b border-gray-200 sticky top-0 z-50" x-data="{ open: false }">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="text-2xl font-bold text-amber-500">
                Feria
            </a>

            {{-- Links desktop --}}
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">Inicio</a>
                <a href="{{ route('products.index') }}" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">Productos</a>
            </div>

            {{-- Carrito + menú --}}
            <div class="flex items-center gap-4">

                {{-- Mini carrito (popup) --}}
                @php
                    $cart      = session('cart', []);
                    $cartCount = collect($cart)->sum('quantity');
                    $cartTotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
                @endphp
                <div class="relative" x-data="{ cartOpen: false }" @keydown.escape="cartOpen = false">
                    <button @click="cartOpen = !cartOpen"
                            class="relative text-gray-600 hover:text-gray-900 transition-colors"
                            aria-label="Carrito">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        @if($cartCount > 0)
                            <span class="absolute -top-2 -right-2 bg-amber-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-medium">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </button>

                    {{-- Panel desplegable --}}
                    <div x-show="cartOpen" x-transition @click.away="cartOpen = false"
                         class="absolute right-0 mt-3 w-80 max-w-[calc(100vw-2rem)] bg-white rounded-xl shadow-lg border border-gray-200 z-50">

                        <div class="px-4 py-3 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-800 text-sm">Tu carrito</h3>
                        </div>

                        @if(empty($cart))
                            <div class="px-4 py-8 text-center">
                                <p class="text-sm text-gray-500 mb-3">Tu carrito está vacío</p>
                                <a href="{{ route('products.index') }}" class="text-sm text-amber-600 hover:underline">Ver productos</a>
                            </div>
                        @else
                            <div class="max-h-72 overflow-y-auto divide-y divide-gray-50">
                                @foreach($cart as $rowId => $item)
                                    <div class="flex items-center gap-3 px-4 py-3">
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                            @if($item['image'])
                                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $item['name'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $item['quantity'] }} × ${{ number_format($item['price'], 0, ',', '.') }}</p>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900 whitespace-nowrap">
                                            ${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                        </span>
                                        <form action="{{ route('cart.remove', $rowId) }}" method="POST" class="flex-shrink-0">
                                            @csrf @method('DELETE')
                                            <button class="text-gray-300 hover:text-red-500 transition-colors" title="Quitar" aria-label="Quitar producto">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>

                            <div class="px-4 py-3 border-t border-gray-100">
                                <div class="flex justify-between text-sm font-semibold text-gray-900 mb-3">
                                    <span>Total</span>
                                    <span>${{ number_format($cartTotal, 0, ',', '.') }}</span>
                                </div>
                                <a href="{{ route('checkout.index') }}"
                                   class="block w-full text-center bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors mb-2">
                                    Finalizar compra
                                </a>
                                <a href="{{ route('cart.index') }}"
                                   class="block w-full text-center border border-gray-300 text-gray-700 text-sm font-medium py-2 rounded-lg hover:bg-gray-50 transition-colors">
                                    Ver carrito
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Hamburger mobile --}}
                <button @click="open = !open" class="md:hidden text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="open" x-transition class="md:hidden pb-4 space-y-2">
            <a href="{{ route('home') }}" class="block px-2 py-2 text-sm text-gray-600 hover:text-gray-900">Inicio</a>
            <a href="{{ route('products.index') }}" class="block px-2 py-2 text-sm text-gray-600 hover:text-gray-900">Productos</a>
        </div>
    </div>
</nav>
