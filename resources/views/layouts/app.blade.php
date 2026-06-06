<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', setting('site_name'))</title>

    {{-- SEO / redes sociales --}}
    <meta name="description" content="@yield('meta_description', setting('meta_description'))">
    <meta property="og:site_name" content="{{ setting('site_name') }}">
    <meta property="og:title" content="@yield('og_title', setting('site_name'))">
    <meta property="og:description" content="@yield('meta_description', setting('meta_description'))">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @elseif(setting('logo_path'))
        <meta property="og:image" content="{{ asset('storage/' . setting('logo_path')) }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">

    {{-- Favicon (usa el logo si hay, si no el por defecto) --}}
    @if(setting('logo_path'))
        <link rel="icon" href="{{ asset('storage/' . setting('logo_path')) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme')
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800">

    @include('components.navbar')

    @include('partials.flash')

    <main class="min-h-screen">
        @yield('content')
    </main>

    @include('components.footer')

    @include('components.whatsapp-button')

    @livewireScripts
</body>
</html>
