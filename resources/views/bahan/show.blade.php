<x-app-layout>
    <x-slot name="title">{{ $bahan->nama_bahan }}</x-slot>

    <!-- Header Details -->
    <div class="panel p-5 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-[#E2DED2] gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-mono text-xs px-2 py-0.5 bg-[#E3F0F0] text-[#0B6B73] border border-[#0B6B73]/20 rounded font-semibold">
                        {{ $bahan->kode_bahan }}
                    </span>
                    <span class="text-xs text-[#6B665A]">&bull;</span>
                    <span class="text-xs text-[#6B665A]">{{ $bahan->kategori->nama_kategori ?? '-' }}</span>
                </div>
                <h1 class="text-lg font-bold text-[#1C1A15]">{{ $bahan->nama_bahan }}</h1>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('bahan.index') }}" class="btn btn-secondary text-xs">&larr; Kembali</a>
                @if(auth()->user()->isAdminPusat())
                    <a href="{{ route('bahan.edit', $bahan) }}" class="btn btn-primary text-xs">Edit Bahan</a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 text-xs">
            <div>
                <span class="text-[#6B665A] block">Satuan Operasional</span>
                <span class="font-mono font-medium text-[#1C1A15] text-sm">{{ $bahan->satuan }}</span>
            </div>
            <div>
                <span class="text-[#6B665A] block">Estimasi Harga Beli</span>
                <span class="font-mono font-medium text-[#1C1A15] text-sm">Rp {{ number_format($bahan->harga_estimasi, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-[#6B665A] block">Total Ketersediaan Fisik</span>
                <span class="font-mono font-bold text-sm {{ $bahan->stok_total > 0 ? 'text-[#1E7B4F]' : 'text-danger' }}">
                    {{ number_format($bahan->stok_total, 0, ',', '.') }} {{ $bahan->satuan }}
                </span>
            </div>
            <div>
                <span class="text-[#6B665A] block">Status Pendaftaran</span>
                <div class="mt-0.5">
                    @if($bahan->is_aktif)
                        <span class="badge badge-ok">Aktif</span>
                    @else
                        <span class="badge badge-muted">Nonaktif</span>
                    @endif
                </div>
            </div>
        </div>

        @if($bahan->spesifikasi)
            <div class="mt-4 pt-3 border-t border-[#F0F2F5] text-xs">
                <span class="text-[#6B665A] block mb-0.5 font-medium">Spesifikasi Detail:</span>
                <p class="text-[#1C1A15] leading-relaxed">{{ $bahan->spesifikasi }}</p>
            </div>
        @endif
    </div>

    <!-- Stock Breakdown Per Location Table -->
    <div class="panel mb-6">
        <div class="p-3.5 border-b border-[#E2DED2] bg-[#FAFAFA] flex justify-between items-center">
            <h2 class="text-xs font-semibold text-[#1C1A15]">Sebaran Stok di Gudang Pusat & Cabang</h2>
            <span class="text-[11px] text-[#6B665A]">Data Stok Terkini</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table-dense">
                <thead>
                    <tr>
                        <th>Kode Lokasi</th>
                        <th>Nama Gudang / Cabang</th>
                        <th>Tipe</th>
                        <th class="text-right">Stok Fisik Saat Ini</th>
                        <th class="text-right">Batas Minimum</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lokasiList as $lokasi)
                        @php
                            $stokItem = $bahan->stokLokasi->firstWhere('id_lokasi', $lokasi->id);
                            $jumlah = $stokItem ? (float) $stokItem->jumlah : 0;
                            $min = $stokItem ? (float) $stokItem->stok_minimum : 5;
                            $status = $stokItem ? $stokItem->status_stok : 'Kritis';
                        @endphp
                        <tr>
                            <td class="font-mono text-xs font-semibold text-[#0B6B73]">
                                {{ $lokasi->kode_lokasi }}
                            </td>
                            <td class="text-xs font-medium text-[#1C1A15]">
                                {{ $lokasi->nama_lokasi }}
                            </td>
                            <td class="text-xs">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono {{ $lokasi->isPusat() ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ strtoupper($lokasi->tipe) }}
                                </span>
                            </td>
                            <td class="text-right font-mono text-xs font-semibold {{ $status === 'Kritis' ? 'text-danger' : ($status === 'Menipis' ? 'text-warn' : 'text-[#1C1A15]') }}">
                                {{ number_format($jumlah, 0, ',', '.') }} {{ $bahan->satuan }}
                            </td>
                            <td class="text-right font-mono text-xs text-[#6B665A]">
                                {{ number_format($min, 0, ',', '.') }} {{ $bahan->satuan }}
                            </td>
                            <td class="text-center">
                                <x-status-chip :status="$status" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Mutations for this material -->
    <div class="panel">
        <div class="p-3.5 border-b border-[#E2DED2] bg-[#FAFAFA] flex justify-between items-center">
            <h2 class="text-xs font-semibold text-[#1C1A15]">20 Riwayat Mutasi Terakhir Bahan Ini</h2>
            <a href="{{ route('stok.mutasi', ['bahan_id' => $bahan->id]) }}" class="text-xs text-[#0B6B73] hover:underline">Semua Mutasi &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="table-dense">
                <thead>
                    <tr>
                        <th>Waktu (WIB)</th>
                        <th>Lokasi</th>
                        <th>Jenis Mutasi</th>
                        <th class="text-right">Stok Sebelum</th>
                        <th class="text-right">Perubahan</th>
                        <th class="text-right">Stok Sesudah</th>
                        <th>Keterangan</th>
                        <th>Oleh User</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bahan->mutasiStok as $m)
                        <tr>
                            <td class="font-mono text-xs text-[#6B665A]">
                                {{ $m->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-xs font-medium text-[#1C1A15]">
                                {{ $m->lokasi->kode_lokasi }}
                            </td>
                            <td>
                                <x-status-chip :status="$m->jenis_label" :type="$m->jenis_badge_class" />
                            </td>
                            <td class="text-right font-mono text-xs text-[#6B665A]">
                                {{ number_format($m->stok_sebelum, 0, ',', '.') }}
                            </td>
                            <td class="text-right font-mono text-xs font-bold {{ in_array($m->jenis, ['masuk', 'terima']) ? 'text-ok' : 'text-danger' }}">
                                {{ in_array($m->jenis, ['masuk', 'terima']) ? '+' : '-' }}{{ number_format($m->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="text-right font-mono text-xs font-semibold text-[#1C1A15]">
                                {{ number_format($m->stok_sesudah, 0, ',', '.') }}
                            </td>
                            <td class="text-xs text-[#1C1A15]">
                                {{ $m->keterangan }}
                            </td>
                            <td class="text-xs text-[#6B665A]">
                                {{ $m->user->name ?? 'Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-6 text-xs text-[#6B665A]">
                                Belum ada riwayat mutasi stok untuk bahan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
