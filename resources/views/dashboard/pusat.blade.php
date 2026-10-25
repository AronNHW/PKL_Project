@php
    $menunggu = $permintaanMenungguList->count();
    $selisih = $pengirimanSelisihList->count();
    $temuan = $menunggu > 0
        ? $menunggu . ' permintaan cabang menunggu surat jalan'
        : ($kritisPusat > 0 ? $kritisPusat . ' bahan kritis di gudang pusat' : 'Tidak ada antrean, stok pusat aman');
@endphp

<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
    <div>
        <p class="font-display text-[26px] font-semibold leading-tight text-ink">{{ $temuan }}</p>
        <p class="text-sm text-muted mt-1">
            @if($kritisPusat > 0 || $menipisPusat > 0)
                Gudang pusat: {{ $kritisPusat }} kritis, {{ $menipisPusat }} menipis, {{ $amanPusat }} aman
            @else
                Gudang pusat: {{ $amanPusat }} bahan aman
            @endif
        </p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('distribusi.pengiriman') }}" class="btn btn-primary">Buat surat jalan</a>
        <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary">Catat barang masuk</a>
        <a href="{{ route('prediksi.index') }}" class="btn btn-secondary">Prediksi dan ROP</a>
    </div>
</div>

<div class="stat-strip grid-cols-1 md:grid-cols-4 mb-6">
    <a href="{{ route('stok.index', ['status' => 'kritis']) }}">
        <div class="stat-label">Kritis di pusat</div>
        <div class="stat-value {{ $kritisPusat > 0 ? 'text-danger' : '' }}">{{ $kritisPusat }}</div>
        <div class="stat-note">{{ $kritisPusat > 0 ? 'perlu pesan ke supplier' : 'tidak ada' }}</div>
    </a>
    <a href="{{ route('distribusi.permintaan') }}">
        <div class="stat-label">Permintaan menunggu</div>
        <div class="stat-value {{ $menunggu > 0 ? 'text-accent' : '' }}">{{ $menunggu }}</div>
        <div class="stat-note">{{ $menunggu > 0 ? 'belum diterbitkan surat jalan' : 'antrean kosong' }}</div>
    </a>
    <a href="{{ route('distribusi.pengiriman') }}">
        <div class="stat-label">Surat jalan transit</div>
        <div class="stat-value">{{ $pengirimanTransitList->count() }}</div>
        <div class="stat-note">menuju cabang, belum diterima</div>
    </a>
    <a href="{{ route('distribusi.pengiriman') }}">
        <div class="stat-label">Selisih terima</div>
        <div class="stat-value {{ $selisih > 0 ? 'text-danger' : '' }}">{{ $selisih }}</div>
        <div class="stat-note">{{ $selisih > 0 ? 'perlu ditelusuri' : 'jumlah kirim sama dengan terima' }}</div>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6 items-start">
    <section class="panel" aria-labelledby="h-antrean">
        <div class="px-5 pt-4 pb-3 flex items-center justify-between">
            <h2 id="h-antrean" class="text-base font-semibold text-ink">Antrean permintaan cabang</h2>
            <a href="{{ route('distribusi.permintaan') }}" class="text-[13px] font-medium text-accent hover:underline">Semua</a>
        </div>
        <ul>
            @forelse($permintaanMenungguList as $permintaan)
                <li class="flex items-center justify-between gap-3 px-5 py-3 border-t border-[#EFEBE0]">
                    <div class="min-w-0">
                        <div class="text-[13.5px] font-medium truncate">{{ $permintaan->lokasiPemohon->nama_lokasi }}</div>
                        <div class="text-xs text-muted truncate">
                            {{ $permintaan->nomor_permintaan }} · {{ $permintaan->tanggal_permintaan->format('j M') }} ·
                            {{ $permintaan->details->count() }} item, {{ $permintaan->details->first()?->bahan->nama_bahan ?? '-' }}
                        </div>
                    </div>
                    <a href="{{ route('distribusi.pengiriman') }}" class="btn btn-secondary !py-1 !px-3 text-[13px] shrink-0">Terbitkan SJ</a>
                </li>
            @empty
                <li class="px-5 py-8 text-center text-muted border-t border-[#EFEBE0]">Semua permintaan cabang sudah diproses.</li>
            @endforelse
        </ul>
    </section>

    <section class="panel" aria-labelledby="h-kritis">
        <div class="px-5 pt-4 pb-3 flex items-center justify-between">
            <h2 id="h-kritis" class="text-base font-semibold text-ink">Stok pusat di bawah minimum</h2>
            <a href="{{ route('barang-masuk.index') }}" class="text-[13px] font-medium text-accent hover:underline">Catat barang masuk</a>
        </div>
        <ul>
            @forelse($daftarPerluPesanPusat->take(5) as $stok)
                @php
                    $min = (float) $stok->stok_minimum;
                    $pct = $min > 0 ? min(100, ((float) $stok->jumlah / ($min * 2)) * 100) : 100;
                    $kritis = $stok->status_stok === 'Kritis';
                @endphp
                <li class="px-5 py-3 border-t border-[#EFEBE0]">
                    <div class="flex items-baseline justify-between gap-3 mb-2">
                        <div class="text-[13.5px] font-medium truncate">{{ $stok->bahan->nama_bahan }}</div>
                        <div class="tabular-nums text-[13.5px] shrink-0">
                            <strong class="font-semibold {{ $kritis ? 'text-danger' : 'text-warn' }}">{{ number_format($stok->jumlah, 0, ',', '.') }}</strong>
                            <span class="text-xs text-muted">dari min {{ number_format($stok->stok_minimum, 0, ',', '.') }} {{ $stok->bahan->satuan }}</span>
                        </div>
                    </div>
                    <div class="level"><span style="width: {{ $pct }}%; background-color: {{ $kritis ? 'var(--danger)' : 'var(--warn)' }}"></span></div>
                </li>
            @empty
                <li class="px-5 py-8 text-center text-muted border-t border-[#EFEBE0]">Semua bahan di gudang pusat di atas batas minimum.</li>
            @endforelse
        </ul>
    </section>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    <section class="panel lg:col-span-7 p-5" aria-labelledby="h-chart">
        <h2 id="h-chart" class="text-base font-semibold text-ink">Stok bahan utama: pusat dibanding lima cabang</h2>
        <p class="text-[13px] text-muted mt-0.5 mb-4">Jumlah fisik dalam satuan masing-masing bahan.</p>
        <div class="h-64 relative">
            @if($totalJenisBahan > 0)
                <canvas id="stokChart"></canvas>
            @else
                <p class="text-sm text-muted">Belum ada bahan terdaftar.</p>
            @endif
        </div>
    </section>

    <section class="panel lg:col-span-5" aria-labelledby="h-rusak">
        <div class="px-5 pt-4 pb-3 flex items-center justify-between">
            <h2 id="h-rusak" class="text-base font-semibold text-ink">Kerusakan mesin di cabang</h2>
            <a href="{{ route('mesin.index') }}" class="text-[13px] font-medium text-accent hover:underline">Semua</a>
        </div>
        <ul>
            @forelse($kerusakanAktifList as $lapor)
                <li class="flex items-center justify-between gap-3 px-5 py-3 border-t border-[#EFEBE0]">
                    <div class="min-w-0">
                        <div class="text-[13.5px] font-medium truncate">{{ $lapor->mesin->nama_mesin }}</div>
                        <div class="text-xs text-muted truncate">{{ $lapor->lokasi->kode_lokasi }} · {{ $lapor->gejala }}</div>
                    </div>
                    <x-status-chip :status="ucfirst($lapor->status)" />
                </li>
            @empty
                <li class="px-5 py-8 text-center text-muted border-t border-[#EFEBE0]">Tidak ada laporan kerusakan aktif.</li>
            @endforelse
        </ul>
    </section>
</div>
