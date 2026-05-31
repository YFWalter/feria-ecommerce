@extends('layouts.app')
@section('title', 'Inicio')

@section('content')

{{-- Hero --}}
<section class="bg-amber-50 border-b border-amber-100">
    <div class="max-w-6xl mx-auto px-4 py-16 flex flex-col items-center text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight">
            Tu bazar online<br>
            <span class="text-amber-500">con todo lo que necesitás</span>
        </h1>
        <p class="mt-4 text-gray-500 text-lg max-w-xl">
            Encontrá artículos únicos y variados. Calidad a buen precio, directo a tu puerta.
        </p>
        <a href="{{ route('products.index') }}"
           class="mt-8 inline-block bg-amber-500 hover:bg-amber-600 text-white font-semibold px-8 py-3 rounded-xl transition-colors text-lg">
            Ver productos
        </a>
    </div>
</section>

{{-- Categorías --}}
@if($categories->count())
<section class="max-w-6xl mx-auto px-4 py-12">
    <h2 class="text-xl font-bold text-gray-900 mb-6">Categorías</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($categories as $category)
            <a href="{{ route('products.index', ['categoria' => $category->slug]) }}"
               class="flex flex-col items-center gap-2 p-4 bg-white rounded-xl border border-gray-200 hover:border-amber-400 hover:shadow-sm transition-all group">
                @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                         class="w-12 h-12 object-cover rounded-lg">
                @else
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                @endif
                <span class="text-xs text-gray-700 font-medium text-center group-hover:text-amber-600 transition-colors">
                    {{ $category->name }}
                </span>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- Productos destacados --}}
@if($featured->count())
<section class="max-w-6xl mx-auto px-4 pb-16">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">Destacados</h2>
        <a href="{{ route('products.index') }}" class="text-sm text-amber-600 hover:underline">Ver todos →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        @foreach($featured as $product)
            @include('components.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif

@endsection
