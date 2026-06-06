@extends('layouts.app')
@section('title', 'Productos')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

    {{-- Buscador principal --}}
    <div class="mb-6">
        <div class="flex gap-2 max-w-2xl">
            <input type="text" name="q" form="filtros" value="{{ request('q') }}"
                   placeholder="Buscar productos por nombre..."
                   class="flex-1 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
            <button type="submit" form="filtros"
                    class="bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors">
                Buscar
            </button>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-8">

        {{-- Sidebar filtros --}}
        <aside class="w-full md:w-56 flex-shrink-0">
            <form method="GET" action="{{ route('products.index') }}" id="filtros">
                <div class="bg-white rounded-xl border border-gray-200 p-4 space-y-5">
                    <h3 class="font-semibold text-gray-800">Filtros</h3>

                    {{-- Categorías --}}
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Categoría</p>
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="categoria" value="" {{ !request('categoria') ? 'checked' : '' }}
                                       class="text-primary-500" onchange="document.getElementById('filtros').submit()">
                                <span class="text-sm text-gray-700">Todas</span>
                            </label>
                            @foreach($categories as $cat)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="categoria" value="{{ $cat->slug }}"
                                           {{ request('categoria') === $cat->slug ? 'checked' : '' }}
                                           class="text-primary-500" onchange="document.getElementById('filtros').submit()">
                                    <span class="text-sm text-gray-700">{{ $cat->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    @if(request()->hasAny(['categoria', 'q']))
                        <a href="{{ route('products.index') }}"
                           class="block text-center text-xs text-gray-500 hover:text-gray-700">
                            Limpiar filtros
                        </a>
                    @endif
                </div>
            </form>
        </aside>

        {{-- Grid de productos --}}
        <div class="flex-1">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500">
                    {{ $products->total() }} {{ Str::plural('producto', $products->total()) }} encontrado{{ $products->total() != 1 ? 's' : '' }}
                </p>
                <select name="orden" onchange="this.form.submit()" form="filtros"
                        class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    <option value="recent" {{ request('orden','recent') === 'recent' ? 'selected' : '' }}>Más recientes</option>
                    <option value="price_asc" {{ request('orden') === 'price_asc' ? 'selected' : '' }}>Menor precio</option>
                    <option value="price_desc" {{ request('orden') === 'price_desc' ? 'selected' : '' }}>Mayor precio</option>
                </select>
            </div>

            @if($products->isEmpty())
                <div class="text-center py-20 text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="font-medium">No encontramos productos</p>
                    <a href="{{ route('products.index') }}" class="text-primary-600 text-sm hover:underline mt-1 inline-block">Ver todos</a>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>
                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
