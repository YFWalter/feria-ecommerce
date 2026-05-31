@extends('layouts.admin')
@section('title', 'Productos')

@section('header-actions')
    <a href="{{ route('admin.productos.create') }}"
       class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
        + Nuevo producto
    </a>
@endsection

@section('content')

<form method="GET" class="mb-4">
    <input type="text" name="q" value="{{ request('q') }}"
           placeholder="Buscar por nombre o SKU…"
           class="w-full max-w-sm border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none">
</form>

<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
    @if($products->isEmpty())
        <p class="px-5 py-12 text-center text-sm text-gray-500">
            No se encontraron productos.
            <a href="{{ route('admin.productos.create') }}" class="text-amber-600 hover:underline">Crear uno</a>.
        </p>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b border-gray-100">
                    <th class="px-5 py-3 font-medium">Producto</th>
                    <th class="px-5 py-3 font-medium">Categoría</th>
                    <th class="px-5 py-3 font-medium text-right">Precio</th>
                    <th class="px-5 py-3 font-medium text-right">Stock</th>
                    <th class="px-5 py-3 font-medium">Estado</th>
                    <th class="px-5 py-3 font-medium text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                    @if($product->first_image)
                                        <img src="{{ asset('storage/' . $product->first_image) }}" alt="" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                    @if($product->featured)
                                        <span class="text-xs text-amber-600">★ Destacado</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $product->category->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-900">
                            ${{ number_format($product->price, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <span class="{{ $product->stock <= 5 ? 'text-amber-600 font-semibold' : 'text-gray-600' }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @if($product->is_active)
                                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-green-50 text-green-700">Activo</span>
                            @else
                                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-500">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.productos.edit', $product) }}"
                               class="text-amber-600 hover:text-amber-700 mr-3">Editar</a>
                            <form action="{{ route('admin.productos.destroy', $product) }}" method="POST" class="inline"
                                  onsubmit="return confirm('¿Eliminar &quot;{{ $product->name }}&quot;?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-600">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="mt-4">
    {{ $products->links() }}
</div>
@endsection
