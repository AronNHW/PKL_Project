<x-app-layout>
    <x-slot name="title">Master Bahan Percetakan</x-slot>

    <!-- Top Action & Search Bar -->
    <div class="panel p-3.5 mb-4 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <!-- Search and Filters Form -->
        <form method="GET" action="{{ route('bahan.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
            <div class="w-64">
                <input type="text" 
                       name="q" 
                       value="{{ request('q') }}" 
                       placeholder="Cari kode, nama bahan, spesifikasi..." 
                       class="input-text text-xs">
            </div>

            <div class="w-48">
                <select name="kategori" class="select-text text-xs" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-secondary text-xs">
                Cari
            </button>

            @if(request()->anyFilled(['q', 'kategori']))
                <a href="{{ route('bahan.index') }}" class="text-xs text-[#6B665A] hover:text-danger ml-1">
                    Reset Filter
                </a>
            @endif
        </form>

        <!-- Action Buttons (Pusat Only) -->
        @if(auth()->user()->isAdminPusat())
            <div class="flex items-center gap-2" x-data="{ openImport: false }">
                <!-- Impor CSV Button -->
                <button type="button" @click="openImport = true" class="btn btn-secondary text-xs">
                    <svg class="w-3.5 h-3.5 text-[#6B665A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Impor CSV</span>
                </button>

                <!-- Tambah Bahan Button -->
                <a href="{{ route('bahan.create') }}" class="btn btn-primary text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Bahan</span>
                </a>

                <!-- Modal Impor CSV -->
                <div x-show="openImport" 
                     x-cloak 
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                    <div class="panel bg-white w-full max-w-md p-5" @click.outside="openImport = false">
                        <div class="flex justify-between items-center pb-3 border-b border-[#E2DED2] mb-4">
                            <h3 class="text-sm font-semibold text-[#1C1A15]">Impor Data Master Bahan</h3>
                            <button @click="openImport = false" class="text-[#6B665A] hover:text-danger text-lg font-bold">&times;</button>
                        </div>

                        <form method="POST" action="{{ route('bahan.import-csv') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-xs font-medium text-[#1C1A15] mb-1">Pilih File CSV (.csv)</label>
                                <input type="file" name="file_csv" accept=".csv" required class="input-text text-xs file:mr-3 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-[#0B6B73] file:text-white">
                                <p class="text-[11px] text-[#6B665A] mt-1.5">File harus berformat UTF-8 dengan pemisah koma (,).</p>
                            </div>

                            <div class="mb-5 p-3 bg-[#F1EEE6] border border-[#E2DED2] rounded text-xs text-[#6B665A]">
                                <span class="font-medium text-[#1C1A15]">Format Kolom:</span>
                                <div class="font-mono text-[11px] mt-0.5">kode_bahan, nama_bahan, kode_kategori, satuan, harga_estimasi, spesifikasi, stok_minimum</div>
                                <div class="mt-2">
                                    <a href="{{ route('bahan.template-csv') }}" class="text-[#0B6B73] underline">Unduh contoh template CSV</a>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2">
                                <button type="button" @click="openImport = false" class="btn btn-secondary text-xs">Batal</button>
                                <button type="submit" class="btn btn-primary text-xs">Unggah & Proses</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Master Bahan Dense Table (Section 9.3) -->
    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-dense">
                <thead>
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>Kode Bahan</th>
                        <th>Nama Bahan Percetakan</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th class="text-right">Harga Estimasi</th>
                        <th class="text-right">Total Stok (Semua Lokasi)</th>
                        <th class="text-center">Status</th>
                        <th class="text-right w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bahanList as $index => $b)
                        <tr>
                            <td class="text-center text-xs text-[#6B665A] font-mono">
                                {{ $bahanList->firstItem() + $index }}
                            </td>
                            <td class="font-mono text-xs font-semibold text-[#0B6B73]">
                                {{ $b->kode_bahan }}
                            </td>
                            <td>
                                <div class="font-medium text-[#1C1A15]">{{ $b->nama_bahan }}</div>
                                @if($b->spesifikasi)
                                    <div class="text-[11.5px] text-[#6B665A] truncate max-w-xs">{{ $b->spesifikasi }}</div>
                                @endif
                            </td>
                            <td class="text-xs">
                                <span class="px-2 py-0.5 bg-[#F1EEE6] border border-[#E2DED2] rounded text-[11px] text-[#1C1A15]">
                                    {{ $b->kategori->nama_kategori ?? '-' }}
                                </span>
                            </td>
                            <td class="font-mono text-xs text-[#6B665A]">
                                {{ $b->satuan }}
                            </td>
                            <td class="text-right font-mono text-xs text-[#1C1A15]">
                                Rp {{ number_format($b->harga_estimasi, 0, ',', '.') }}
                            </td>
                            <td class="text-right font-mono text-xs font-semibold {{ $b->stok_total > 0 ? 'text-[#1C1A15]' : 'text-danger' }}">
                                {{ number_format($b->stok_total, 0, ',', '.') }} {{ $b->satuan }}
                            </td>
                            <td class="text-center">
                                @if($b->is_aktif)
                                    <span class="badge badge-ok">Aktif</span>
                                @else
                                    <span class="badge badge-muted">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('bahan.show', $b) }}" class="text-xs text-[#0B6B73] hover:underline" title="Detail Stok & Mutasi">
                                        Detail
                                    </a>
                                    @if(auth()->user()->isAdminPusat())
                                        <span class="text-[#E2DED2]">|</span>
                                        <a href="{{ route('bahan.edit', $b) }}" class="text-xs text-[#6B665A] hover:text-[#1C1A15]" title="Ubah Data">
                                            Edit
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-10 text-[#6B665A] text-xs">
                                <p class="mb-2">Belum ada data bahan percetakan yang terdaftar.</p>
                                @if(auth()->user()->isAdminPusat())
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('bahan.create') }}" class="btn btn-secondary text-xs">
                                            + Tambah Bahan Baru
                                        </a>
                                        <a href="{{ route('bahan.template-csv') }}" class="text-[#0B6B73] hover:underline text-xs">
                                            Unduh Template CSV
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($bahanList->hasPages())
            <div class="p-3 border-t border-[#E2DED2] bg-[#FAFAFA]">
                {{ $bahanList->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
