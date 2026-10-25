@php
    $u = auth()->user();
    $pusat = $u->isAdminPusat();
    $cabang = $u->isAdminCabang();
    $roleLabel = $u->isOwner() ? 'Owner' : ($pusat ? 'Admin pusat' : 'Admin cabang');

    $i = [
        'home' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'box' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'tag' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        'building' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        'pin' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
        'clipboard' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'inbound' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',
        'doc' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'swap' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
        'pen' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
        'cog' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        'trend' => 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z',
        'chart' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'users' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
    ];

    // [judul grup, [[rute, label, pola aktif, ikon, tampil?], ...]]
    $groups = [
        [null, [['dashboard', 'Ringkasan', 'dashboard', 'home', true]]],
        ['Master data', [
            ['bahan.index', 'Master bahan', 'bahan.*', 'box', !$cabang],
            ['kategori.index', 'Kategori bahan', 'kategori.*', 'tag', !$cabang],
            ['supplier.index', 'Supplier', 'supplier.*', 'building', !$cabang],
            ['lokasi.index', 'Gudang dan cabang', 'lokasi.*', 'pin', !$cabang],
        ]],
        ['Stok', [
            ['stok.index', 'Stok per lokasi', 'stok.index', 'clipboard', true],
            ['stok.mutasi', 'Riwayat mutasi', 'stok.mutasi', 'clock', true],
        ]],
        ['Operasional', [
            ['barang-masuk.index', 'Barang masuk', 'barang-masuk.*', 'inbound', $pusat],
            ['distribusi.permintaan', 'Permintaan cabang', 'distribusi.permintaan*', 'doc', true],
            ['distribusi.pengiriman', 'Surat jalan', 'distribusi.pengiriman*', 'swap', true],
            ['pemakaian.index', 'Pemakaian bahan', 'pemakaian.*', 'pen', true],
            ['mesin.index', 'Mesin dan kerusakan', 'mesin.*', 'cog', true],
        ]],
        ['Analisis', [
            ['prediksi.index', 'Prediksi dan ROP', 'prediksi.*', 'trend', !$cabang],
            ['laporan.index', 'Laporan dan ekspor', 'laporan.*', 'chart', true],
        ]],
        ['Sistem', [['users.index', 'Pengguna', 'users.*', 'users', $pusat]]],
    ];
@endphp

<nav class="flex-1 px-3 pb-3 overflow-y-auto text-[13.5px]" aria-label="Menu utama">
    @foreach($groups as [$judul, $items])
        @php $items = array_filter($items, fn($x) => $x[4]); @endphp
        @continue(!count($items))
        @if($judul)
            <div class="pt-5 pb-1.5 px-3 text-xs text-muted">{{ $judul }}</div>
        @endif
        @foreach($items as [$rute, $label, $pola, $ikon])
            @php $aktif = request()->routeIs($pola); @endphp
            <a href="{{ route($rute) }}" @if($aktif) aria-current="page" @endif
               class="flex items-center gap-2.5 px-3 min-h-[38px] rounded-lg transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-accent {{ $aktif ? 'bg-black/[0.07] text-ink font-semibold' : 'text-ink/75 hover:bg-black/[0.04] hover:text-ink' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="{{ $i[$ikon] }}"/></svg>
                <span>{{ $label }}</span>
            </a>
        @endforeach
    @endforeach
</nav>

<div class="p-3 shrink-0">
    <div class="flex items-center gap-2.5 px-2 py-2">
        <div class="w-8 h-8 rounded-full bg-accent-light text-accent flex items-center justify-center font-semibold text-sm shrink-0">
            {{ strtoupper(substr($u->name, 0, 1)) }}
        </div>
        <div class="min-w-0 flex-1">
            <div class="text-[13px] font-semibold text-ink truncate">{{ $u->name }}</div>
            <div class="text-xs text-muted truncate">{{ $roleLabel }}@if($u->lokasi) · {{ $u->lokasi->kode_lokasi }}@endif</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" title="Keluar" aria-label="Keluar akun"
                    class="w-9 h-9 flex items-center justify-center rounded-md text-muted hover:bg-black/5 hover:text-ink focus:outline-none focus-visible:ring-2 focus-visible:ring-accent transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </button>
        </form>
    </div>
</div>
