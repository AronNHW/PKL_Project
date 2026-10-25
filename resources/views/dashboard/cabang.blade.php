@php
    $kodeCabang = $cabangLokasi->kode_lokasi ?? 'CB';
    $mesinBermasalah = $mesinCabang->filter(fn($m) => $m->status === 'rusak' || ($m->laporanKerusakan?->count() ?? 0) > 0)->count();
    $mesinSiap = $mesinCabang->count() - $mesinBermasalah;
    $temuan = $kritisCabang > 0
        ? $kritisCabang . ' bahan di bawah separuh batas minimum'
        : ($menipisCabang > 0 ? $menipisCabang . ' bahan mendekati batas minimum' : 'Stok cabang aman hari ini');
@endphp

<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
    <div>
        <p class="font-display text-[26px] font-semibold leading-tight text-ink">{{ $temuan }}</p>
        <p class="text-sm text-muted mt-1">{{ $cabangLokasi->nama_lokasi ?? 'Cabang' }} · {{ $kodeCabang }}</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('distribusi.permintaan') }}" class="btn btn-primary">Ajukan permintaan bahan</a>
        <a href="{{ route('pemakaian.index') }}" class="btn btn-secondary">Catat pemakaian</a>
        <a href="{{ route('mesin.index') }}" class="btn btn-secondary">Lapor kerusakan</a>
    </div>
</div>

@if($pengirimanMenujuCabang->count() > 0)
    <div class="mb-6 rounded-lg bg-accent-light px-4 py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <p class="text-sm text-ink">
            <strong class="font-semibold">{{ $pengirimanMenujuCabang->count() }} surat jalan</strong> dari pusat sedang menuju cabang ini.
            Periksa fisik barang saat tiba, lalu isi jumlah yang diterima.
        </p>
        <a href="{{ route('distribusi.pengiriman') }}" class="btn btn-primary shrink-0">Konfirmasi penerimaan</a>
    </div>
@endif

<div class="stat-strip grid-cols-1 md:grid-cols-4 mb-6">
    <a href="{{ route('stok.index', ['status' => 'kritis']) }}">
        <div class="stat-label">Stok kritis</div>
        <div class="stat-value {{ $kritisCabang > 0 ? 'text-danger' : '' }}">{{ $kritisCabang }}</div>
        <div class="stat-note">{{ $kritisCabang > 0 ? 'minta pasokan sekarang' : 'tidak ada' }}</div>
    </a>
    <a href="{{ route('stok.index', ['status' => 'menipis']) }}">
        <div class="stat-label">Stok menipis</div>
        <div class="stat-value {{ $menipisCabang > 0 ? 'text-warn' : '' }}">{{ $menipisCabang }}</div>
        <div class="stat-note">{{ $amanCabang }} bahan lain aman</div>
    </a>
    <a href="{{ route('distribusi.pengiriman') }}">
        <div class="stat-label">Surat jalan menuju cabang</div>
        <div class="stat-value">{{ $pengirimanMenujuCabang->count() }}</div>
        <div class="stat-note">{{ $pengirimanMenujuCabang->count() > 0 ? 'menunggu konfirmasi terima' : 'tidak ada yang transit' }}</div>
    </a>
    <a href="{{ route('mesin.index') }}">
        <div class="stat-label">Mesin siap pakai</div>
        <div class="stat-value {{ $mesinBermasalah > 0 ? 'text-danger' : '' }}">{{ $mesinSiap }}<span class="text-lg text-muted font-medium"> / {{ $mesinCabang->count() }}</span></div>
        <div class="stat-note">{{ $mesinBermasalah > 0 ? $mesinBermasalah . ' sedang bermasalah' : 'semua normal' }}</div>
    </a>
</div>

<section class="panel mb-6" aria-labelledby="h-perlu-minta">
    <div class="px-5 pt-4 pb-3 flex items-start justify-between gap-3">
        <div>
            <h2 id="h-perlu-minta" class="text-base font-semibold text-ink">Bahan yang perlu diminta ke pusat</h2>
            <p class="text-[13px] text-muted mt-0.5">Garis hitam di takaran menandai batas minimum.</p>
        </div>
        <a href="{{ route('distribusi.permintaan') }}" class="text-[13px] font-medium text-accent hover:underline shrink-0">Buat permintaan</a>
    </div>
    <div class="overflow-x-auto">
        <table class="table-dense">
            <thead>
                <tr>
                    <th>Bahan</th>
                    <th class="w-48">Takaran</th>
                    <th class="text-right">Stok</th>
                    <th class="text-right">Minimum</th>
                    <th>Status</th>
                    <th><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse($daftarPerluPesanCabang as $stok)
                    @php
                        $min = (float) $stok->stok_minimum;
                        $pct = $min > 0 ? min(100, ((float) $stok->jumlah / ($min * 2)) * 100) : 100;
                        $kritis = $stok->status_stok === 'Kritis';
                    @endphp
                    <tr>
                        <td>
                            <div class="font-medium">{{ $stok->bahan->nama_bahan }}</div>
                            <div class="text-xs text-muted">{{ $stok->bahan->kode_bahan }}</div>
                        </td>
                        <td>
                            <div class="level" role="img" aria-label="Stok {{ round($pct) }}% dari dua kali batas minimum">
                                <span style="width: {{ $pct }}%; background-color: {{ $kritis ? 'var(--danger)' : 'var(--warn)' }}"></span>
                            </div>
                        </td>
                        <td class="text-right tabular-nums font-semibold {{ $kritis ? 'text-danger' : 'text-warn' }}">
                            {{ number_format($stok->jumlah, 0, ',', '.') }} <span class="text-xs font-normal text-muted">{{ $stok->bahan->satuan }}</span>
                        </td>
                        <td class="text-right tabular-nums text-muted">
                            {{ number_format($stok->stok_minimum, 0, ',', '.') }} <span class="text-xs">{{ $stok->bahan->satuan }}</span>
                        </td>
                        <td><x-status-chip :status="$stok->status_stok" /></td>
                        <td class="text-right">
                            <a href="{{ route('distribusi.permintaan') }}" class="btn btn-secondary !py-1 !px-3 text-[13px]">Minta</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-8">Semua bahan di cabang ini di atas batas minimum.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
    <section class="panel" aria-labelledby="h-pemakaian">
        <div class="px-5 pt-4 pb-3 flex items-center justify-between">
            <h2 id="h-pemakaian" class="text-base font-semibold text-ink">Pemakaian terakhir</h2>
            <a href="{{ route('pemakaian.index') }}" class="text-[13px] font-medium text-accent hover:underline">Semua</a>
        </div>
        <ul>
            @forelse($pemakaianTerbaru as $pakai)
                @foreach($pakai->details as $d)
                    <li class="flex items-center justify-between gap-3 px-5 py-2.5 border-t border-[#EFEBE0]">
                        <div class="min-w-0">
                            <div class="text-[13.5px] font-medium truncate">{{ $d->bahan->nama_bahan ?? '-' }}</div>
                            <div class="text-xs text-muted">SPK {{ $pakai->nomor_spk ?? '-' }} · {{ $pakai->tanggal_pemakaian->format('j M') }}</div>
                        </div>
                        <div class="tabular-nums text-[13.5px] font-semibold shrink-0">-{{ number_format($d->jumlah, 0, ',', '.') }} <span class="text-xs font-normal text-muted">{{ $d->bahan->satuan ?? '' }}</span></div>
                    </li>
                @endforeach
            @empty
                <li class="px-5 py-8 text-center text-muted border-t border-[#EFEBE0]">Belum ada pemakaian tercatat. <a href="{{ route('pemakaian.index') }}" class="text-accent font-medium hover:underline">Catat pemakaian</a></li>
            @endforelse
        </ul>
    </section>

    <section class="panel" aria-labelledby="h-mesin">
        <div class="px-5 pt-4 pb-3 flex items-center justify-between">
            <h2 id="h-mesin" class="text-base font-semibold text-ink">Mesin di cabang ini</h2>
            <a href="{{ route('mesin.index') }}" class="text-[13px] font-medium text-accent hover:underline">Lapor kerusakan</a>
        </div>
        <ul>
            @forelse($mesinCabang as $mesin)
                @php $adaTiket = ($mesin->laporanKerusakan?->count() ?? 0) > 0; @endphp
                <li class="flex items-center justify-between gap-3 px-5 py-2.5 border-t border-[#EFEBE0]">
                    <div class="min-w-0">
                        <div class="text-[13.5px] font-medium truncate">{{ $mesin->nama_mesin }}</div>
                        <div class="text-xs text-muted">{{ $mesin->kode_mesin }} · {{ $mesin->tipe_mesin }}</div>
                    </div>
                    @if($mesin->status === 'rusak')
                        <span class="badge badge-danger">Rusak</span>
                    @elseif($adaTiket || $mesin->status === 'perbaikan')
                        <span class="badge badge-warn">Dalam perbaikan</span>
                    @else
                        <span class="badge badge-ok">Siap</span>
                    @endif
                </li>
            @empty
                <li class="px-5 py-8 text-center text-muted border-t border-[#EFEBE0]">Belum ada mesin terdaftar di cabang ini.</li>
            @endforelse
        </ul>
    </section>
</div>
