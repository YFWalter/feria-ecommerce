@extends('layouts.admin')
@section('title', 'Categorías')

@section('header-actions')
    <a href="{{ route('admin.categorias.create') }}"
       class="bg-primary-500 hover:bg-primary-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
        + Nueva categoría
    </a>
@endsection

@section('content')

<form method="GET" class="mb-4">
    <input type="text" name="q" value="{{ request('q') }}"
           placeholder="Buscar categoría por nombre…"
           class="w-full max-w-sm border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
</form>

<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
    @if($categories->isEmpty())
        <p class="px-5 py-12 text-center text-sm text-gray-500">
            @if(request('q'))
                No se encontraron categorías para “{{ request('q') }}”.
            @else
                No hay categorías todavía.
                <a href="{{ route('admin.categorias.create') }}" class="text-primary-600 hover:underline">Crear la primera</a>.
            @endif
        </p>
    @else
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b border-gray-100">
                    <th class="px-5 py-3 font-medium">Categoría</th>
                    <th class="px-5 py-3 font-medium">Productos</th>
                    <th class="px-5 py-3 font-medium">Orden</th>
                    <th class="px-5 py-3 font-medium">Estado</th>
                    <th class="px-5 py-3 font-medium">En inicio</th>
                    <th class="px-5 py-3 font-medium text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                    @if($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}" alt="" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $category->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $category->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $category->products_count }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $category->order }}</td>
                        <td class="px-5 py-3">
                            @if($category->is_active)
                                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-green-50 text-green-700">Activa</span>
                            @else
                                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-500">Inactiva</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <form action="{{ route('admin.categorias.toggle-home', $category) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="text-xs font-medium px-2.5 py-1 rounded-full transition-colors {{ $category->show_on_home ? 'bg-primary-100 text-primary-700 hover:bg-primary-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}"
                                        title="Clic para {{ $category->show_on_home ? 'quitar del' : 'mostrar en el' }} inicio">
                                    {{ $category->show_on_home ? '✓ Sí' : '— No' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.categorias.edit', $category) }}"
                               class="text-primary-600 hover:text-primary-700 mr-3">Editar</a>
                            <form action="{{ route('admin.categorias.destroy', $category) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="button" class="text-red-500 hover:text-red-600"
                                        @click="$dispatch('confirm-action', { message: 'Vas a eliminar la categoría &quot;{{ $category->name }}&quot;.', target: $el.closest('form') })">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    @endif
</div>

<div class="mt-4">
    {{ $categories->links() }}
</div>
@endsection
