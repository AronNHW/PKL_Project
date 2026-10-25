<x-app-layout>
    <x-slot name="title">Riwayat Mutasi Stok (Buku Mutasi)</x-slot>

    <!-- Filters Bar -->
    <div class="panel p-3.5 mb-4">
        <form method="GET" action="{{ route('stok.mutasi') }}" class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2 flex-1">
                <!-- Filter Lokasi -->
                @if(!$isCabang)
                    <div class="w-48">
                        <select name="lokasi" class="select-text text-xs" onchange="this.form.submit()">
                            <option value="">Semua Lokasi</option>
                            @foreach($lokasiList as $lok)
                                <option value="{{ $lok->id }}" {{ request('lokasi') == $lok->id ? 'selected' : '' }}>
                                    {{ $lok->nama_lokasi }} ({{ $lok->kode_lokasi }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Filter Jenis Mutasi -->
                <div class="w-44">
                    <select name="jenis" class="select-text text-xs" onchange="this.form.submit()">
                        <option value="">Semua Jenis Mutasi</option>
                        <option value="masuk" {{ request('jenis') == 'masuk' ? 'selected' : '' }}>Masuk (Penerimaan Supplier / Saldo)</option>
                        <option value="keluar" {{ request('jenis') == 'keluar' ? 'selected' : '' }}>Keluar (Produksi SPK / Service)</option>
                        <option value="kirim" {{ request('jenis') == 'kirim' ? 'selected' : '' }}>Kirim (Surat Jalan)</option>
                        <option value="terima" {{ request('jenis') == 'terima' ? 'selected' : '' }}>Terima (Penerimaan Distribusi)</option>
                        <option value="penyesuaian" {{ request('jenis') == 'penyesuaian' ? 'selected' : '' }}>Penyesuaian (Stock Opname)</option>
                    </select>
                </div>

                <!-- Search Input -->
                <div class="w-56">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama bahan, kode..." class="input-text text-xs">
                </div>

                <button type="submit" class="btn btn-secondary text-xs">Filter</button>

                @if(request()->anyFilled(['lokasi', 'jenis', 'q', 'bahan_id']))
                    <a href="{{ route('stok.mutasi') }}" class="text-xs text-[#6B665A] hover:text-danger ml-1">Reset</a>
                @endif
            </div>

            <a href="{{ route('stok.index') }}" class="btn btn-secondary text-xs">
                &larr; Kembali ke Stok Lokasi
            </a>
        </form>
    </div>

    <!-- Mutasi Table Dense -->
    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-dense">
                <thead>
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>Waktu (WIB)</th>
                        <th>Lokasi</th>
                        <th>Bahan Percetakan</th>
                        <th class="text-center">Jenis</th>
                        <th class="text-right">Stok Sebelum</th>
                        <th class="text-right">Mutasi</th>
                        <th class="text-right">Stok Sesudah</th>
                        <th>Keterangan / Referensi</th>
                        <th>Operator</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mutasiList as $index => $m)
                        <tr>
                            <td class="text-center font-mono text-xs text-[#6B665A]">
                                {{ $mutasiList->firstItem() + $index }}
                            </td>
                            <td class="font-mono text-xs text-[#6B665A] whitespace-nowrap">
                                {{ $m->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-xs font-medium text-[#1C1A15]">
                                <span class="px-1.5 py-0.5 rounded text-[10.5px] font-mono {{ $m->lokasi->isPusat() ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $m->lokasi->kode_lokasi }}
                                </span>
                            </td>
                            <td>
                                <div class="font-medium text-[#1C1A15] text-xs">{{ $m->bahan->nama_bahan }}</div>
                                <div class="font-mono text-[10.5px] text-[#6B665A]">{{ $m->bahan->kode_bahan }}</div>
                            </td>
                            <td class="text-center">
                                <x-status-chip :status="$m->jenis_label" :type="$m->jenis_badge_class" />
                            </td>
                            <td class="text-right font-mono text-xs text-[#6B665A]">
                                {{ number_format($m->stok_sebelum, 0, ',', '.') }} {{ $m->bahan->satuan }}
                            </td>
                            <td class="text-right font-mono text-xs font-bold {{ in_array($m->jenis, ['masuk', 'terima']) ? 'text-ok' : (in_array($m->jenis, ['keluar', 'kirim']) ? 'text-danger' : 'text-[#0B6B73]') }}">
                                {{ in_array($m->jenis, ['masuk', 'terima']) ? '+' : (in_array($m->jenis, ['keluar', 'kirim']) ? '-' : '&plusmn;') }}{{ number_format($m->jumlah, 0, ',', '.') }} {{ $m->bahan->satuan }}
                            </td>
                            <td class="text-right font-mono text-xs font-semibold text-[#1C1A15]">
                                {{ number_format($m->stok_sesudah, 0, ',', '.') }} {{ $m->bahan->satuan }}
                            </td>
                            <td class="text-xs text-[#1C1A15] max-w-xs truncate">
                                {{ $m->keterangan }}
                            </td>
                            <td class="text-xs text-[#6B665A] whitespace-nowrap">
                                {{ $m->user->name ?? 'Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-8 text-[#6B665A] text-xs">
                                Tidak ada data mutasi yang tercatat untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mutasiList->hasPages())
            <div class="p-3 border-t border-[#E2DED2] bg-[#FAFAFA]">
                {{ $mutasiList->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
