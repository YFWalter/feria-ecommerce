{{--
    Modal de confirmación reutilizable. Para usarlo, en un botón:
    <button type="button"
        @click="$dispatch('confirm-action', { message: '¿Eliminar X?', target: $el.closest('form') })">
        Eliminar
    </button>
--}}
<div x-data="{ open: false, message: '', target: null }"
     x-on:confirm-action.window="open = true; message = $event.detail.message; target = $event.detail.target"
     x-show="open"
     x-on:keydown.escape.window="open = false"
     class="fixed inset-0 z-[70] flex items-center justify-center p-4"
     style="display:none">

    <div class="absolute inset-0 bg-black/50" x-transition.opacity @click="open = false"></div>

    <div class="relative bg-white rounded-2xl shadow-xl max-w-sm w-full p-6"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900">¿Confirmás la acción?</h3>
                <p class="text-sm text-gray-500 mt-1" x-text="message"></p>
            </div>
        </div>

        <div class="flex justify-end gap-2 mt-6">
            <button type="button" @click="open = false"
                    class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 rounded-lg hover:bg-gray-100 transition-colors">
                Cancelar
            </button>
            <button type="button" @click="if (target) target.submit(); open = false"
                    class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                Sí, eliminar
            </button>
        </div>
    </div>
</div>
