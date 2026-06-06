<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public $timestamps = true;

    private const CACHE_KEY = 'settings.all';

    /** Devuelve todos los settings como array key=>value (cacheado). */
    public static function allSettings(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return static::query()->pluck('value', 'key')->all();
        });
    }

    /** Obtiene un setting; si no existe en DB, cae al default de config('store.defaults'). */
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::allSettings();

        return $all[$key] ?? config("store.defaults.$key", $default);
    }

    /** Guarda (o crea) un setting y limpia el caché. */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(self::CACHE_KEY);
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
