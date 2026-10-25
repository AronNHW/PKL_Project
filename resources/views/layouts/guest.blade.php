<!DOCTYPE html>
<html lang="id" class="h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk - Sistem Inventori Gudang</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500..800&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-sheet h-full overflow-hidden antialiased selection:bg-accent selection:text-white">
    <div class="h-full w-full grid lg:grid-cols-[1.1fr_1fr] overflow-hidden">
        <!-- Rak gudang: ilustrasi digambar dari benda yang dikelola sistem ini -->
        <section class="hidden lg:flex flex-col justify-between bg-frame p-8 xl:p-10 h-full overflow-hidden select-none" aria-label="Gambaran sistem">
            <div class="flex items-center justify-between">
                <x-application-logo />
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-medium text-[#0B6B73] bg-[#E3F0F0] rounded-full border border-[#0B6B73]/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#0B6B73]"></span> 1 Pusat & 4 Cabang
                </span>
            </div>

            <!-- Strip Telemetri Jaringan Gudang -->
            <div class="grid grid-cols-3 gap-2 py-2 px-3 rounded-lg bg-[#FBFAF6]/80 border border-[#D4CFC0] text-ink my-auto">
                <div>
                    <div class="text-[10px] font-semibold text-muted uppercase tracking-wider">Gudang Pusat</div>
                    <div class="text-xs font-semibold text-ink mt-0.5 truncate">Pusat Logistik</div>
                </div>
                <div class="border-l border-[#D4CFC0] pl-2.5">
                    <div class="text-[10px] font-semibold text-muted uppercase tracking-wider">Unit Cabang</div>
                    <div class="text-xs font-semibold text-ink mt-0.5 truncate">4 Cabang Aktif</div>
                </div>
                <div class="border-l border-[#D4CFC0] pl-2.5">
                    <div class="text-[10px] font-semibold text-muted uppercase tracking-wider">Integrasi Data</div>
                    <div class="text-xs font-semibold text-ink mt-0.5 truncate">Sinkronisasi Real-Time</div>
                </div>
            </div>

            <!-- Ilustrasi Rak Penyimpanan Barang -->
            <svg viewBox="0 0 520 380" class="w-full max-w-[360px] xl:max-w-[420px] max-h-[30vh] self-center my-auto shrink" role="img" aria-label="Rak gudang berisi kardus dan palet">
                <rect x="20" y="20" width="10" height="340" fill="#1C1A15"/>
                <rect x="490" y="20" width="10" height="340" fill="#1C1A15"/>
                <rect x="20" y="118" width="480" height="8" fill="#1C1A15"/>
                <rect x="20" y="226" width="480" height="8" fill="#1C1A15"/>
                <rect x="20" y="332" width="480" height="8" fill="#1C1A15"/>
                <!-- tingkat atas -->
                <rect x="44" y="52" width="92" height="66" fill="#C8B98F"/><rect x="44" y="52" width="92" height="12" fill="#B3A27A"/>
                <rect x="146" y="72" width="70" height="46" fill="#D8CBA6"/>
                <rect x="226" y="40" width="110" height="78" fill="#0B6B73"/><rect x="226" y="40" width="110" height="12" fill="#08565D"/>
                <rect x="346" y="64" width="64" height="54" fill="#C8B98F"/><rect x="346" y="64" width="64" height="10" fill="#B3A27A"/>
                <rect x="420" y="82" width="56" height="36" fill="#D8CBA6"/>
                <!-- tingkat tengah -->
                <rect x="44" y="164" width="60" height="62" fill="#D8CBA6"/>
                <rect x="114" y="140" width="98" height="86" fill="#C8B98F"/><rect x="114" y="140" width="98" height="12" fill="#B3A27A"/>
                <rect x="222" y="178" width="76" height="48" fill="#C8B98F"/><rect x="222" y="178" width="76" height="10" fill="#B3A27A"/>
                <rect x="308" y="150" width="90" height="76" fill="#D8CBA6"/>
                <rect x="408" y="188" width="68" height="38" fill="#C8B98F"/>
                <!-- tingkat bawah: rol spanduk dan palet -->
                <rect x="44" y="296" width="150" height="36" fill="#B3A27A"/>
                <rect x="44" y="284" width="150" height="12" fill="#C8B98F"/>
                <rect x="204" y="258" width="26" height="74" rx="13" fill="#F1EEE6"/>
                <rect x="238" y="258" width="26" height="74" rx="13" fill="#E6DFCB"/>
                <rect x="272" y="258" width="26" height="74" rx="13" fill="#F1EEE6"/>
                <rect x="320" y="270" width="156" height="62" fill="#C8B98F"/><rect x="320" y="270" width="156" height="12" fill="#B3A27A"/>
            </svg>

            <!-- Fitur Utama Rantai Pasok -->
            <div class="space-y-1.5 my-auto">
                <div class="flex items-center gap-2 text-xs text-[#38342A]">
                    <svg class="w-3.5 h-3.5 text-accent shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Surat Jalan & Rekonsiliasi Otomatis Selisih Kirim vs Terima</span>
                </div>
                <div class="flex items-center gap-2 text-xs text-[#38342A]">
                    <svg class="w-3.5 h-3.5 text-accent shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Pencatatan Pemakaian Produksi & Perawatan Mesin Cetak</span>
                </div>
                <div class="flex items-center gap-2 text-xs text-[#38342A]">
                    <svg class="w-3.5 h-3.5 text-accent shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Peramalan Kebutuhan Stok (Single Exponential Smoothing & ROP)</span>
                </div>
            </div>

            <div class="pt-2 border-t border-[#D4CFC0]">
                <p class="font-display text-xl xl:text-2xl font-semibold leading-tight text-ink">
                    Satu catatan stok untuk pusat dan empat cabang.
                </p>
                <p class="text-xs text-muted mt-0.5">Sistem informasi manajemen inventori bahan baku & logistik terpadu.</p>
            </div>
        </section>

        <main class="flex flex-col justify-center px-6 py-6 sm:px-10 xl:px-16 h-full overflow-y-auto no-scrollbar">
            <div class="w-full max-w-md mx-auto my-auto">
                <x-application-logo class="lg:hidden mb-6" />
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
