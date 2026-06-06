<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Atajo para leer una configuración de la tienda.
     * Cae al default de config('store.defaults') si no está en la base.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}
