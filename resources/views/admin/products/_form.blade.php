@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm mb-4">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php $p = $product ?? null; @endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-5xl">

    {{-- Columna principal --}}
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                <input type="text" name="name" id="name" required
                       value="{{ old('name', $p->name ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="description" id="description" rows="5"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">{{ old('description', $p->description ?? '') }}</textarea>
            </div>
        </div>

        {{-- Imágenes --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-4">
            <h3 class="text-sm font-medium text-gray-700">Imágenes</h3>

            @if($p && !empty($p->images))
                <div class="grid grid-cols-4 gap-3">
                    @foreach($p->images as $img)
                        <label class="relative block cursor-pointer group">
                            <img src="{{ asset('storage/' . $img) }}" alt=""
                                 class="w-full h-24 object-cover rounded-lg border border-gray-200">
                            <span class="absolute inset-0 bg-red-500/0 group-has-[:checked]:bg-red-500/40 rounded-lg transition-colors flex items-center justify-center">
                                <span class="text-white text-xs font-semibold opacity-0 group-has-[:checked]:opacity-100">Quitar</span>
                            </span>
                            <input type="checkbox" name="remove_images[]" value="{{ $img }}" class="absolute top-1 right-1">
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400">Marcá las imágenes que quieras eliminar.</p>
            @endif

            <div>
                <input type="file" name="images[]" accept="image/*" multiple
                       class="block text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                <p class="text-xs text-gray-400 mt-1">Podés subir varias. JPG/PNG, máx 2 MB cada una. La primera es la principal.</p>
            </div>
        </div>
    </div>

    {{-- Columna lateral --}}
    <div class="space-y-5">
        <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-4">
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                <select name="category_id" id="category_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
                    <option value="">Seleccionar…</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string) old('category_id', $p->category_id ?? '') === (string) $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Precio *</label>
                <input type="number" name="price" id="price" step="0.01" min="0" required
                       value="{{ old('price', $p->price ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
            </div>

            <div>
                <label for="compare_price" class="block text-sm font-medium text-gray-700 mb-1">Precio anterior</label>
                <input type="number" name="compare_price" id="compare_price" step="0.01" min="0"
                       value="{{ old('compare_price', $p->compare_price ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
                <p class="text-xs text-gray-400 mt-1">Para mostrar descuento (tachado).</p>
            </div>

            <div>
                <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stock *</label>
                <input type="number" name="stock" id="stock" min="0" required
                       value="{{ old('stock', $p->stock ?? 0) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
            </div>

            <div>
                <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                <input type="text" name="sku" id="sku"
                       value="{{ old('sku', $p->sku ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-3">
            <label class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $p->is_active ?? true) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-primary-500 focus:ring-primary-400">
                <span class="text-sm text-gray-700">Producto activo (visible en la tienda)</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="hidden" name="featured" value="0">
                <input type="checkbox" name="featured" value="1"
                       {{ old('featured', $p->featured ?? false) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-primary-500 focus:ring-primary-400">
                <span class="text-sm text-gray-700">Destacado en la home</span>
            </label>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-5 py-2.5 rounded-lg transition-colors">
                {{ $submitLabel }}
            </button>
            <a href="{{ route('admin.productos.index') }}"
               class="text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
        </div>
    </div>

</div>
