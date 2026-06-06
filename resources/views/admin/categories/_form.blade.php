@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm mb-4">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white border border-gray-200 rounded-xl p-6 max-w-2xl space-y-5">

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
        <input type="text" name="name" id="name" required
               value="{{ old('name', $category->name ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
    </div>

    <div>
        <label for="order" class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
        <input type="number" name="order" id="order" min="0"
               value="{{ old('order', $category->order ?? 0) }}"
               class="w-32 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
        <p class="text-xs text-gray-400 mt-1">Menor número = aparece primero.</p>
    </div>

    <div>
        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Imagen</label>
        @if(!empty($category) && $category->image)
            <img src="{{ asset('storage/' . $category->image) }}" alt=""
                 class="w-24 h-24 object-cover rounded-lg mb-2 border border-gray-200">
        @endif
        <input type="file" name="image" id="image" accept="image/*"
               class="block text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
        <p class="text-xs text-gray-400 mt-1">JPG/PNG, máx 2 MB.</p>
    </div>

    <label class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1"
               {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}
               class="rounded border-gray-300 text-primary-500 focus:ring-primary-400">
        <span class="text-sm text-gray-700">Categoría activa</span>
    </label>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit"
                class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-5 py-2.5 rounded-lg transition-colors">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('admin.categorias.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
    </div>
</div>
