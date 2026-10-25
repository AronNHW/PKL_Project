@php
    $mesinSiap = $totalMesin - $totalMesinRusak;
    $temuan = $totalKritis > 0
        ? $totalKritis . ' stok kritis di seluruh lokasi'
        : 'Tidak ada stok kritis di seluruh lokasi';
@endphp

<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
    <div>
        <p class="font-display text-[26px] font-semibold leading-tight text-ink">{{ $temuan }}</p>
        <p class="text-sm text-muted mt-1">Nilai persediaan Rp {{ number_format($totalNilaiStok, 0, ',', '.') }} di {{ $lokasiKesehatan->count() }} lokasi</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('laporan.index') }}" class="btn btn-primary">Buka laporan</a>
        <a href="{{ route('laporan.export-csv', ['jenis' => 'stok']) }}" class="btn btn-secondary">Unduh stok (CSV)</a>
    </div>
</div>

<div class="stat-strip grid-cols-1 md:grid-cols-4 mb-6">
    <div>
        <div class="stat-label">Nilai persediaan</div>
        <div class="stat-value !text-[34px]">Rp {{ number_format($totalNilaiStok / 1000000, 1, ',', '.') }} <span class="text-lg text-muted font-medium">juta</span></div>
        <div class="stat-note">pusat dan lima cabang</div>
    </div>
    <div>
        <div class="stat-label">Stok kritis dan menipis</div>
        <div class="stat-value"><span class="{{ $totalKritis > 0 ? 'text-danger' : '' }}">{{ $totalKritis }}</span><span class="text-lg text-muted font-medium"> · </span><span class="{{ $totalMenipis > 0 ? 'text-warn' : '' }}">{{ $totalMenipis }}</span></div>
        <div class="stat-note">dari {{ $totalKritis + $totalMenipis + $totalAman }} baris stok, {{ $totalAman }} aman</div>
    </div>
    <div>
        <div class="stat-label">Mesin siap pakai</div>
        <div class="stat-value {{ $totalMesinRusak > 0 ? 'text-warn' : '' }}">{{ $mesinSiap }}<span class="text-lg text-muted font-medium"> / {{ $totalMesin }}</span></div>
        <div class="stat-note">{{ $totalMesinRusak > 0 ? $totalMesinRusak . ' sedang diperbaiki' : 'semua normal' }}</div>
    </div>
    <div>
        <div class="stat-label">Surat jalan transit</div>
        <div class="stat-value">{{ $pengirimanAktifCount }}</div>
        <div class="stat-note {{ $pengirimanSelisihCount > 0 ? 'text-danger' : '' }}">{{ $pengirimanSelisihCount > 0 ? $pengirimanSelisihCount . ' dengan selisih terima' : 'tidak ada selisih terima' }}</div>
    </div>
</div>

<section class="panel mb-6" aria-labelledby="h-unit">
    <div class="px-5 pt-4 pb-3">
        <h2 id="h-unit" class="text-base font-semibold text-ink">Kondisi tiap lokasi</h2>
        <p class="text-[13px] text-muted mt-0.5">Batang menunjukkan porsi bahan aman, menipis, dan kritis di lokasi itu.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="table-dense">
            <thead>
                <tr>
                    <th>Lokasi</th>
                    <th class="w-64">Status bahan</th>
                    <th>Mesin</th>
                    <th class="text-right">Nilai stok</th>
                    <th><span class="sr-only">Rincian</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach($lokasiKesehatan as $row)
                    @php $total = max(1, $row->aman + $row->menipis + $row->kritis); @endphp
                    <tr>
                        <td>
                            <div class="font-medium">{{ $row->nama_lokasi }}</div>
                            <div class="text-xs text-muted">{{ $row->kode_lokasi }} · {{ $row->penanggung_jawab }}</div>
                        </td>
                        <td>
                            <div class="flex h-2 gap-0.5 rounded-sm overflow-hidden" role="img" aria-label="{{ $row->aman }} aman, {{ $row->menipis }} menipis, {{ $row->kritis }} kritis">
                                @if($row->aman)<span style="flex: {{ $row->aman }}; background-color: var(--ok)"></span>@endif
                                @if($row->menipis)<span style="flex: {{ $row->menipis }}; background-color: var(--warn)"></span>@endif
                                @if($row->kritis)<span style="flex: {{ $row->kritis }}; background-color: var(--danger)"></span>@endif
                            </div>
                            <div class="text-xs text-muted mt-1 tabular-nums">{{ $row->aman }} aman · {{ $row->menipis }} menipis · {{ $row->kritis }} kritis</div>
                        </td>
                        <td class="text-[13px]">
                            @if($row->mesin_rusak > 0)
                                <span class="text-danger font-semibold">{{ $row->mesin_rusak }} rusak</span> <span class="text-muted">dari {{ $row->total_mesin }}</span>
                            @else
                                <span class="text-muted">{{ $row->total_mesin }} unit normal</span>
                            @endif
                        </td>
                        <td class="text-right tabular-nums font-medium">Rp {{ number_format($row->nilai_stok, 0, ',', '.') }}</td>
                        <td class="text-right"><a href="{{ route('stok.index', ['lokasi' => $row->id]) }}" class="text-[13px] font-medium text-accent hover:underline">Rincian</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

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

    <section class="panel lg:col-span-5" aria-labelledby="h-mutasi">
        <div class="px-5 pt-4 pb-3 flex items-center justify-between">
            <h2 id="h-mutasi" class="text-base font-semibold text-ink">Mutasi terakhir</h2>
            <a href="{{ route('stok.mutasi') }}" class="text-[13px] font-medium text-accent hover:underline">Semua</a>
        </div>
        <ul>
            @forelse($mutasiTerbaru as $mutasi)
                @php $masuk = in_array($mutasi->jenis, ['masuk', 'terima', 'penyesuaian_tambah']); @endphp
                <li class="flex items-center justify-between gap-3 px-5 py-2.5 border-t border-[#EFEBE0]">
                    <div class="min-w-0">
                        <div class="text-[13.5px] font-medium truncate">{{ $mutasi->bahan->nama_bahan }}</div>
                        <div class="text-xs text-muted truncate">{{ $mutasi->lokasi->kode_lokasi }} · {{ $mutasi->keterangan }}</div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="tabular-nums text-[13.5px] font-semibold {{ $masuk ? 'text-ok' : 'text-danger' }}">{{ $masuk ? '+' : '-' }}{{ number_format($mutasi->jumlah, 0, ',', '.') }}</div>
                        <div class="text-xs text-muted tabular-nums">{{ $mutasi->created_at->format('j M, H.i') }}</div>
                    </div>
                </li>
            @empty
                <li class="px-5 py-8 text-center text-muted border-t border-[#EFEBE0]">Belum ada mutasi stok tercatat.</li>
            @endforelse
        </ul>
    </section>
</div>
