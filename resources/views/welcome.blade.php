<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Inventori Gudang - Manajemen Logistik & Bahan Baku</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500..800&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F1EEE6] text-[#1C1A15] antialiased flex items-center justify-center min-h-screen p-4">
    <div class="panel bg-white p-6 w-full max-w-sm text-center">
        <div class="w-10 h-10 rounded-md bg-[#0B6B73] text-white flex items-center justify-center font-bold text-sm font-mono mx-auto mb-3">
            IG
        </div>
        <h1 class="text-sm font-bold text-[#1C1A15]">Sistem Inventori Gudang</h1>
        <p class="text-xs text-[#6B665A] mt-1 mb-5">Manajemen Logistik Bahan Baku (1 Gudang Pusat & 5 Cabang)</p>
        <div class="space-y-2">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary w-full text-xs py-2">
                    Buka Dashboard Operasional
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary w-full text-xs py-2">
                    Masuk ke Sistem Inventaris
                </a>
            @endauth
        </div>
    </div>
</body>
</html>
