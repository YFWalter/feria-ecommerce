@php
    $themeKey = setting('theme_color', 'amber');
    $themes   = config('store.themes');
    $shades   = $themes[$themeKey]['shades'] ?? $themes['amber']['shades'];
@endphp
<style>
    :root {
        @foreach($shades as $shade => $rgb)
        --color-primary-{{ $shade }}: {{ $rgb }};
        @endforeach
    }
</style>
