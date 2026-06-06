@extends('layouts.admin')
@section('title', 'Configuración')

@section('content')
@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm mb-4 max-w-3xl">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.configuracion.update') }}" method="POST" enctype="multipart/form-data" class="max-w-3xl space-y-6">
    @csrf @method('PUT')

    {{-- Identidad --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
        <h2 class="font-semibold text-gray-800">Identidad de la tienda</h2>

        <div>
            <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la tienda *</label>
            <input type="text" name="site_name" id="site_name" required
                   value="{{ old('site_name', setting('site_name')) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
        </div>

        <div>
            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Descripción para buscadores y redes</label>
            <textarea name="meta_description" id="meta_description" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">{{ old('meta_description', setting('meta_description')) }}</textarea>
            <p class="text-xs text-gray-400 mt-1">Se muestra en Google y al compartir el link en WhatsApp/redes.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
            @if(setting('logo_path'))
                <div class="flex items-center gap-3 mb-2">
                    <img src="{{ asset('storage/' . setting('logo_path')) }}" alt="Logo" class="h-12 w-auto bg-gray-100 rounded-lg p-1">
                    <label class="flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300 text-primary-500 focus:ring-primary-400">
                        Quitar logo actual
                    </label>
                </div>
            @endif
            <input type="file" name="logo" accept="image/*"
                   class="block text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
            <p class="text-xs text-gray-400 mt-1">PNG/SVG, máx 1 MB. Si no hay logo, se muestra el nombre en texto.</p>
        </div>
    </div>

    {{-- Color del tema --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <h2 class="font-semibold text-gray-800 mb-1">Color del tema</h2>
        <p class="text-sm text-gray-500 mb-4">Se aplica a botones, links y acentos de toda la tienda.</p>

        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
            @foreach($themes as $key => $theme)
                <label class="cursor-pointer">
                    <input type="radio" name="theme_color" value="{{ $key }}"
                           {{ old('theme_color', setting('theme_color')) === $key ? 'checked' : '' }}
                           class="peer sr-only">
                    <div class="rounded-xl border-2 border-gray-200 peer-checked:border-gray-800 p-2 text-center transition-colors">
                        <span class="block w-full h-8 rounded-lg mb-1" style="background: rgb({{ $theme['shades'][500] }})"></span>
                        <span class="text-xs text-gray-600">{{ $theme['label'] }}</span>
                    </div>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Hero de la home --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
        <h2 class="font-semibold text-gray-800">Portada (Hero de la home)</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="hero_title" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                <input type="text" name="hero_title" id="hero_title"
                       value="{{ old('hero_title', setting('hero_title')) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
            </div>
            <div>
                <label for="hero_highlight" class="block text-sm font-medium text-gray-700 mb-1">Título destacado (color)</label>
                <input type="text" name="hero_highlight" id="hero_highlight"
                       value="{{ old('hero_highlight', setting('hero_highlight')) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
            </div>
        </div>

        <div>
            <label for="hero_text" class="block text-sm font-medium text-gray-700 mb-1">Texto descriptivo</label>
            <textarea name="hero_text" id="hero_text" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">{{ old('hero_text', setting('hero_text')) }}</textarea>
        </div>

        <div>
            <label for="hero_button" class="block text-sm font-medium text-gray-700 mb-1">Texto del botón</label>
            <input type="text" name="hero_button" id="hero_button"
                   value="{{ old('hero_button', setting('hero_button')) }}"
                   class="w-64 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
        </div>
    </div>

    {{-- Footer --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
        <h2 class="font-semibold text-gray-800">Pie de página</h2>

        <div>
            <label for="footer_tagline" class="block text-sm font-medium text-gray-700 mb-1">Eslogan</label>
            <input type="text" name="footer_tagline" id="footer_tagline"
                   value="{{ old('footer_tagline', setting('footer_tagline')) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="footer_email" class="block text-sm font-medium text-gray-700 mb-1">Email de contacto</label>
                <input type="email" name="footer_email" id="footer_email"
                       value="{{ old('footer_email', setting('footer_email')) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
            </div>
            <div>
                <label for="footer_phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="text" name="footer_phone" id="footer_phone"
                       value="{{ old('footer_phone', setting('footer_phone')) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
            </div>
        </div>
    </div>

    {{-- Categorías en la home --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
        <h2 class="font-semibold text-gray-800">Categorías en la página de inicio</h2>
        <p class="text-sm text-gray-500">Elegí cuántas mostrar y cómo. <em>Cuáles</em> se muestran se controla con la opción “Mostrar en la página de inicio” de cada categoría.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="home_categories_limit" class="block text-sm font-medium text-gray-700 mb-1">Cantidad máxima a mostrar</label>
                <input type="number" name="home_categories_limit" id="home_categories_limit" min="0" max="50"
                       value="{{ old('home_categories_limit', setting('home_categories_limit')) }}"
                       class="w-32 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
                <p class="text-xs text-gray-400 mt-1">0 = mostrar todas.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estilo de visualización</label>
                <div class="flex gap-4 mt-1">
                    @foreach(['cards' => 'Tarjetas con imagen', 'compact' => 'Compacto (chips)'] as $val => $label)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="home_categories_style" value="{{ $val }}"
                                   {{ old('home_categories_style', setting('home_categories_style')) === $val ? 'checked' : '' }}
                                   class="text-primary-500 focus:ring-primary-400">
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- WhatsApp --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
        <h2 class="font-semibold text-gray-800">Botón de WhatsApp</h2>
        <p class="text-sm text-gray-500">Si cargás un número, aparece un botón flotante de WhatsApp en la tienda.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-1">Número (con código de país)</label>
                <input type="text" name="whatsapp_number" id="whatsapp_number"
                       value="{{ old('whatsapp_number', setting('whatsapp_number')) }}"
                       placeholder="5491122334455"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
                <p class="text-xs text-gray-400 mt-1">Ej: 54 9 11 2233-4455 → <code>5491122334455</code> (sin espacios ni signos).</p>
            </div>
            <div>
                <label for="whatsapp_message" class="block text-sm font-medium text-gray-700 mb-1">Mensaje predefinido</label>
                <input type="text" name="whatsapp_message" id="whatsapp_message"
                       value="{{ old('whatsapp_message', setting('whatsapp_message')) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 outline-none">
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
                class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-5 py-2.5 rounded-lg transition-colors">
            Guardar configuración
        </button>
        <a href="{{ route('home') }}" target="_blank" class="text-sm text-gray-500 hover:text-gray-700">Ver la tienda ↗</a>
    </div>
</form>
@endsection
