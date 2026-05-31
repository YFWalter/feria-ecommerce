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
                <a href="{{ route('cart.index') }}" class="relative text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    @php $cartCount = collect(session('cart', []))->sum('quantity'); @endphp
                    @if($cartCount > 0)
                        <span class="absolute -top-2 -right-2 bg-amber-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-medium">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

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
