<footer class="bg-gray-900 text-gray-400 mt-16">
    <div class="max-w-6xl mx-auto px-4 py-10">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <span class="text-xl font-bold text-amber-400">Feria</span>
                <p class="text-sm mt-1">Tu bazar online de confianza</p>
            </div>
            <div class="flex gap-6 text-sm">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Inicio</a>
                <a href="{{ route('products.index') }}" class="hover:text-white transition-colors">Productos</a>
                <a href="{{ route('cart.index') }}" class="hover:text-white transition-colors">Carrito</a>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-6 text-center text-xs">
            © {{ date('Y') }} Feria. Todos los derechos reservados.
        </div>
    </div>
</footer>
