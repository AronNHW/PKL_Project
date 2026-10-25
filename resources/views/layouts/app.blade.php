<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - ' : '' }}Sistem Inventori Gudang</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500..800&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ mobileMenuOpen: false }" @keydown.escape.window="mobileMenuOpen = false">
    <div class="flex h-screen overflow-hidden">
        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col min-w-0">
            @include('layouts.header', ['title' => $title ?? 'Dashboard'])

            <main class="flex-1 overflow-y-auto bg-sheet border border-border rounded-xl mx-2 md:ml-0 mb-2 p-4 md:p-6">
                @include('components.flash-message')

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
