<footer class="bg-gray-900 text-gray-400 mt-16">
    <div class="max-w-6xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

            {{-- Marca --}}
            <div class="md:col-span-1">
                @if(setting('logo_path'))
                    <img src="{{ asset('storage/' . setting('logo_path')) }}" alt="{{ setting('site_name') }}" class="h-9 w-auto mb-3 brightness-0 invert opacity-90">
                @else
                    <span class="text-2xl font-bold text-primary-400">{{ setting('site_name') }}</span>
                @endif
                <p class="text-sm mt-2">{{ setting('footer_tagline') }}</p>
            </div>

            {{-- Tienda --}}
            <div>
                <p class="uppercase text-gray-500 text-xs font-semibold tracking-wider mb-4">Tienda</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Inicio</a></li>
                    <li><a href="{{ route('products.index') }}" class="hover:text-white transition-colors">Productos</a></li>
                    <li><a href="{{ route('cart.index') }}" class="hover:text-white transition-colors">Carrito</a></li>
                </ul>
            </div>

            {{-- Categorías --}}
            <div>
                <p class="uppercase text-gray-500 text-xs font-semibold tracking-wider mb-4">Categorías</p>
                <ul class="space-y-2 text-sm">
                    @foreach(\App\Models\Category::where('is_active', true)->where('show_on_home', true)->orderBy('order')->take(4)->get() as $cat)
                        <li>
                            <a href="{{ route('products.index', ['categoria' => $cat->slug]) }}" class="hover:text-white transition-colors">
                                {{ $cat->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Contacto --}}
            <div>
                <p class="uppercase text-gray-500 text-xs font-semibold tracking-wider mb-4">Contacto</p>
                <ul class="space-y-2 text-sm">
                    @if(setting('footer_email'))
                        <li><a href="mailto:{{ setting('footer_email') }}" class="hover:text-white transition-colors">{{ setting('footer_email') }}</a></li>
                    @endif
                    @if(setting('footer_phone'))
                        <li><a href="tel:{{ setting('footer_phone') }}" class="hover:text-white transition-colors">{{ setting('footer_phone') }}</a></li>
                    @endif
                    @if(! setting('footer_email') && ! setting('footer_phone'))
                        <li class="text-gray-500">Escribinos para más info.</li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-10 pt-6 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} {{ setting('site_name') }}. Todos los derechos reservados.
            <span class="block mt-2 text-gray-400 text-xs">
                Diseño y desarrollo web por
                <a href="mailto:yanez.walt@gmail.com" class="hover:text-primary-400 transition-colors">Walter Yañez</a>
                <span class="mx-1">·</span>
                <a href="https://yfwalter.github.io/portfolio/" target="_blank" rel="noopener noreferrer" title="Ver portfolio" class="hover:text-primary-400 transition-colors">Portfolio</a>
            </span>
        </div>
    </div>
</footer>
