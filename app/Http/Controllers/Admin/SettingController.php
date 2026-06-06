<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /** Claves de texto editables desde el formulario. */
    private const TEXT_KEYS = [
        'site_name', 'theme_color', 'meta_description',
        'hero_title', 'hero_highlight', 'hero_text', 'hero_button',
        'footer_tagline', 'footer_email', 'footer_phone',
        'whatsapp_number', 'whatsapp_message',
        'home_categories_limit', 'home_categories_style',
    ];

    public function edit()
    {
        return view('admin.settings.edit', [
            'themes' => config('store.themes'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'      => 'required|string|max:100',
            'theme_color'    => 'required|in:' . implode(',', array_keys(config('store.themes'))),
            'hero_title'     => 'nullable|string|max:150',
            'hero_highlight' => 'nullable|string|max:150',
            'hero_text'      => 'nullable|string|max:500',
            'hero_button'    => 'nullable|string|max:50',
            'footer_tagline' => 'nullable|string|max:200',
            'footer_email'   => 'nullable|email|max:150',
            'footer_phone'   => 'nullable|string|max:50',
            'whatsapp_number' => 'nullable|string|max:30',
            'whatsapp_message' => 'nullable|string|max:300',
            'meta_description' => 'nullable|string|max:300',
            'home_categories_limit' => 'nullable|integer|min:0|max:50',
            'home_categories_style' => 'required|in:cards,compact',
            'logo'           => 'nullable|image|max:1024',
        ]);

        foreach (self::TEXT_KEYS as $key) {
            Setting::set($key, $request->input($key));
        }

        // Quitar el logo actual.
        if ($request->boolean('remove_logo') && setting('logo_path')) {
            Storage::disk('public')->delete(setting('logo_path'));
            Setting::set('logo_path', null);
        }

        // Subir un logo nuevo (reemplaza el anterior).
        if ($request->hasFile('logo')) {
            if (setting('logo_path')) {
                Storage::disk('public')->delete(setting('logo_path'));
            }
            Setting::set('logo_path', $request->file('logo')->store('branding', 'public'));
        }

        return back()->with('success', 'Configuración guardada correctamente.');
    }
}
