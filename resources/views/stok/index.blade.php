<x-app-layout>
    <x-slot name="title">Inventaris & Stok per Lokasi</x-slot>

    <!-- Filter & Action Bar -->
    <div class="panel p-3.5 mb-4">
        <form method="GET" action="{{ route('stok.index') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2 flex-1">
                <!-- Lokasi Selector -->
                <div class="w-full sm:w-56">
                    @if($isCabang)
                        <div class="input-text text-xs bg-gray-100 text-[#1C1A15] font-medium flex items-center justify-between cursor-not-allowed">
                            <span>{{ $lokasiUser->nama_lokasi ?? 'Cabang Anda' }}</span>
                            <span class="text-[10px] text-[#6B665A] font-mono">Terkunci</span>
                        </div>
                    @else
                        <select name="lokasi" class="select-text text-xs" onchange="this.form.submit()">
                            <option value="">Semua Lokasi (Pusat & Cabang)</option>
                            @foreach($lokasiList as $lok)
                                <option value="{{ $lok->id }}" {{ $selectedLokasiId == $lok->id ? 'selected' : '' }}>
                                    {{ $lok->nama_lokasi }} ({{ $lok->kode_lokasi }})
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <!-- Status Filter (Aman, Menipis, Kritis) -->
                <div class="w-full sm:w-40">
                    <select name="status" class="select-text text-xs" onchange="this.form.submit()">
                        <option value="">Semua Status Stok</option>
                        <option value="kritis" {{ request('status') == 'kritis' ? 'selected' : '' }}>Kritis (&le; 50% min)</option>
                        <option value="menipis" {{ request('status') == 'menipis' ? 'selected' : '' }}>Menipis (&le; batas min)</option>
                        <option value="aman" {{ request('status') == 'aman' ? 'selected' : '' }}>Aman (&gt; batas min)</option>
                    </select>
                </div>

                <!-- Kategori Filter -->
                <div class="w-full sm:w-44">
                    <select name="kategori" class="select-text text-xs" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat->id }}" {{ request('kategori') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Input -->
                <div class="w-full sm:w-48">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / kode..." class="input-text text-xs">
                </div>

                <button type="submit" class="btn btn-secondary text-xs">Filter</button>

                @if(request()->anyFilled(['lokasi', 'status', 'kategori', 'q']))
                    <a href="{{ route('stok.index') }}" class="text-xs text-[#6B665A] hover:text-danger ml-1">Reset</a>
                @endif
            </div>

            <!-- Riwayat Mutasi Shortcut -->
            <a href="{{ route('stok.mutasi') }}" class="btn btn-secondary text-xs shrink-0">
                <svg class="w-3.5 h-3.5 text-[#6B665A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Lihat Buku Mutasi</span>
            </a>
        </form>
    </div>

    <!-- Stock Table Dense -->
    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-dense">
                <thead>
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>Kode</th>
                        <th>Nama Bahan Percetakan</th>
                        <th>Kategori</th>
                        <th>Lokasi Gudang</th>
                        <th class="text-right">Stok Fisik</th>
                        <th class="text-right">Batas Min</th>
                        <th class="text-center">Status</th>
                        <th class="text-right w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stokList as $index => $item)
                        <tr>
                            <td class="text-center font-mono text-xs text-[#6B665A]">
                                {{ $stokList->firstItem() + $index }}
                            </td>
                            <td class="font-mono text-xs font-semibold text-[#0B6B73]">
                                {{ $item->bahan->kode_bahan }}
                            </td>
                            <td class="font-medium text-[#1C1A15]">
                                <a href="{{ route('bahan.show', $item->bahan) }}" class="hover:text-[#0B6B73]">
                                    {{ $item->bahan->nama_bahan }}
                                </a>
                            </td>
                            <td class="text-xs text-[#6B665A]">
                                {{ $item->bahan->kategori->nama_kategori ?? '-' }}
                            </td>
                            <td class="text-xs font-medium text-[#1C1A15]">
                                <span class="px-2 py-0.5 rounded text-[11px] font-mono {{ $item->lokasi->isPusat() ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $item->lokasi->kode_lokasi }}
                                </span>
                                <span class="text-[#6B665A] ml-1">{{ $item->lokasi->nama_lokasi }}</span>
                            </td>
                            <td class="text-right font-mono text-xs font-bold {{ $item->status_stok === 'Kritis' ? 'text-danger' : ($item->status_stok === 'Menipis' ? 'text-warn' : 'text-[#1E7B4F]') }}">
                                {{ number_format($item->jumlah, 0, ',', '.') }} {{ $item->bahan->satuan }}
                            </td>
                            <td class="text-right font-mono text-xs text-[#6B665A]">
                                {{ number_format($item->stok_minimum, 0, ',', '.') }} {{ $item->bahan->satuan }}
                            </td>
                            <td class="text-center">
                                <x-status-chip :status="$item->status_stok" />
                            </td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-1.5" x-data>
                                    @if(!auth()->user()->isOwner())
                                        <button type="button" 
                                                @click="$dispatch('trigger-opname', { bahan: {{ json_encode($item->bahan) }}, lokasi: {{ json_encode($item->lokasi) }}, stok: {{ (float) $item->jumlah }} })"
                                                class="text-xs text-[#0B6B73] hover:underline font-medium">
                                            Opname
                                        </button>
                                        <span class="text-[#E2DED2]">|</span>
                                    @endif
                                    <a href="{{ route('stok.mutasi', ['bahan_id' => $item->id_bahan, 'lokasi' => $item->id_lokasi]) }}" 
                                       class="text-xs text-[#6B665A] hover:text-[#1C1A15]">
                                        Mutasi
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-[#6B665A] text-xs">
                                Tidak ada data stok yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stokList->hasPages())
            <div class="p-3 border-t border-[#E2DED2] bg-[#FAFAFA]">
                {{ $stokList->links() }}
            </div>
        @endif
    </div>

    <!-- Global Opname Listener Script -->
    <div x-data="{ openModal: false, dataBahan: null, dataLokasi: null, dataStok: 0 }"
         @trigger-opname.window="openModal = true; dataBahan = $event.detail.bahan; dataLokasi = $event.detail.lokasi; dataStok = $event.detail.stok">
        
        <div x-show="openModal" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="panel bg-white w-full max-w-md p-5" @click.outside="openModal = false">
                <div class="flex justify-between items-center pb-3 border-b border-[#E2DED2] mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-[#1C1A15]">Stock Opname Fisik</h3>
                        <p class="text-xs text-[#6B665A] mt-0.5">Penyesuaian stok riil gudang dengan pencatatan mutasi otomatis.</p>
                    </div>
                    <button @click="openModal = false" class="text-[#6B665A] hover:text-danger text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('stok.sesuaikan') }}" class="space-y-3.5">
                    @csrf
                    <input type="hidden" name="id_bahan" :value="dataBahan ? dataBahan.id : ''">
                    <input type="hidden" name="id_lokasi" :value="dataLokasi ? dataLokasi.id : ''">

                    <div class="p-3 bg-[#F1EEE6] border border-[#E2DED2] rounded text-xs space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-[#6B665A]">Bahan:</span>
                            <span class="font-semibold text-[#1C1A15]" x-text="dataBahan ? dataBahan.nama_bahan : ''"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#6B665A]">Gudang:</span>
                            <span class="font-medium text-[#1C1A15]" x-text="dataLokasi ? (dataLokasi.nama_lokasi + ' (' + dataLokasi.kode_lokasi + ')') : ''"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#6B665A]">Stok Terdata Sistem:</span>
                            <span class="font-mono font-bold text-[#0B6B73]" x-text="dataStok + ' ' + (dataBahan ? dataBahan.satuan : '')"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">
                            Jumlah Fisik Hasil Hitung Riil <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="any" min="0" name="jumlah_fisik" required placeholder="0" class="input-text text-sm font-mono font-bold">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">
                            Keterangan Penyesuaian <span class="text-danger">*</span>
                        </label>
                        <textarea name="keterangan" rows="2" required placeholder="Contoh: Audit fisik stok opname tanggal {{ date('d/m/Y') }}" class="input-text text-xs"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-[#E2DED2]">
                        <button type="button" @click="openModal = false" class="btn btn-secondary text-xs">Batal</button>
                        <button type="submit" class="btn btn-primary text-xs">Simpan Hasil Opname</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
