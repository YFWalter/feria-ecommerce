@extends('layouts.app')
@section('title', 'Inicio')

@section('content')

{{-- Hero --}}
<section class="bg-primary-50 border-b border-primary-100">
    <div class="max-w-6xl mx-auto px-4 py-16 flex flex-col items-center text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight">
            {{ setting('hero_title') }}<br>
            <span class="text-primary-500">{{ setting('hero_highlight') }}</span>
        </h1>
        <p class="mt-4 text-gray-500 text-lg max-w-xl">
            {{ setting('hero_text') }}
        </p>
        <a href="{{ route('products.index') }}"
           class="mt-8 inline-block bg-primary-500 hover:bg-primary-600 text-white font-semibold px-8 py-3 rounded-xl transition-colors text-lg">
            {{ setting('hero_button') }}
        </a>
    </div>
</section>

{{-- Categorías --}}
@if($categories->count())
<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">Categorías</h2>
        @if($categories->count() < $totalCategories)
            <a href="{{ route('products.index') }}" class="text-sm text-primary-600 hover:underline">Ver todas →</a>
        @endif
    </div>

    @if(setting('home_categories_style') === 'compact')
        {{-- Estilo compacto: chips --}}
        <div class="flex flex-wrap gap-2.5">
            @foreach($categories as $category)
                <a href="{{ route('products.index', ['categoria' => $category->slug]) }}"
                   class="inline-flex items-center gap-2 bg-white border border-gray-200 rounded-full pl-2 pr-4 py-1.5 hover:border-primary-400 hover:text-primary-600 text-sm text-gray-700 font-medium transition-colors">
                    <span class="w-7 h-7 rounded-full bg-primary-50 overflow-hidden flex items-center justify-center flex-shrink-0">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="" class="w-full h-full object-cover">
                        @else
                            <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        @endif
                    </span>
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    @else
        {{-- Estilo tarjetas con imagen --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('products.index', ['categoria' => $category->slug]) }}"
                   class="block bg-white rounded-xl border border-gray-200 overflow-hidden hover:border-primary-400 hover:shadow-md transition-all group">
                    <div class="aspect-square bg-primary-50 overflow-hidden">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-3 text-center">
                        <span class="text-sm text-gray-700 font-medium group-hover:text-primary-600 transition-colors">
                            {{ $category->name }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</section>
@endif

{{-- Productos destacados --}}
@if($featured->count())
<section class="max-w-6xl mx-auto px-4 pb-16">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">Destacados</h2>
        <a href="{{ route('products.index') }}" class="text-sm text-primary-600 hover:underline">Ver todos →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        @foreach($featured as $product)
            @include('components.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif

@endsection
