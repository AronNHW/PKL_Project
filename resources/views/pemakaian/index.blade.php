<x-app-layout>
    <x-slot name="title">Pemakaian Bahan Percetakan (Produksi / SPK)</x-slot>

    <!-- Filter & Action Bar -->
    <div class="panel p-3.5 mb-4" x-data="{ openPakai: false, items: [{ id_bahan: '', jumlah: 1 }] }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('pemakaian.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
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

                <div class="w-56">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari no pemakaian, no SPK, job..." class="input-text text-xs">
                </div>

                <button type="submit" class="btn btn-secondary text-xs">Cari</button>

                @if(request()->anyFilled(['q', 'lokasi']))
                    <a href="{{ route('pemakaian.index') }}" class="text-xs text-[#6B665A] hover:text-danger ml-1">Reset</a>
                @endif
            </form>

            @if(!auth()->user()->isOwner())
                <button type="button" @click="openPakai = true" class="btn btn-primary text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Catat Pemakaian Produksi</span>
                </button>
            @endif
        </div>

        <!-- Modal Form Pemakaian -->
        <div x-show="openPakai" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="panel bg-white w-full max-w-xl max-h-[90vh] overflow-y-auto p-5" @click.outside="openPakai = false">
                <div class="flex justify-between items-center pb-3 border-b border-[#E2DED2] mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-[#1C1A15]">Pencatatan Pemakaian Bahan Produksi (SPK)</h3>
                        <p class="text-xs text-[#6B665A] mt-0.5">Stok bahan akan langsung dipotong dari cabang tempat bahan dipakai.</p>
                    </div>
                    <button @click="openPakai = false" class="text-[#6B665A] hover:text-danger text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('pemakaian.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        @if(!$isCabang)
                            <div>
                                <label class="block text-xs font-medium text-[#1C1A15] mb-1">Lokasi Pemakaian <span class="text-danger">*</span></label>
                                <select name="id_lokasi" required class="select-text text-xs">
                                    <option value="">-- Pilih Lokasi --</option>
                                    @foreach($lokasiList as $lok)
                                        <option value="{{ $lok->id }}">{{ $lok->nama_lokasi }} ({{ $lok->kode_lokasi }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="{{ $isCabang ? 'md:col-span-2' : '' }}">
                            <label class="block text-xs font-medium text-[#1C1A15] mb-1">Tanggal Pemakaian <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_pemakaian" value="{{ date('Y-m-d') }}" required class="input-text text-xs font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#1C1A15] mb-1">Nomor SPK / Order Kerja</label>
                            <input type="text" name="nomor_spk" placeholder="Contoh: SPK-2026-0881" class="input-text text-xs font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Keperluan Order / Judul Cetakan <span class="text-danger">*</span></label>
                        <input type="text" name="keperluan" required placeholder="Contoh: Cetak Spanduk HUT Kota Surabaya (50 pcs) & Stiker Label Kemasan" class="input-text text-xs">
                    </div>

                    <!-- Items Pemakaian -->
                    <div class="pt-2 border-t border-[#E2DED2]">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-semibold text-[#1C1A15]">Daftar Bahan yang Digunakan</span>
                            <button type="button" @click="items.push({ id_bahan: '', jumlah: 1 })" class="btn btn-secondary text-xs py-0.5 px-2">
                                + Tambah Bahan
                            </button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(item, idx) in items" :key="idx">
                                <div class="grid grid-cols-12 gap-2 items-center bg-[#F1EEE6] p-2 rounded-md border border-[#E2DED2]">
                                    <div class="col-span-8">
                                        <select :name="'items['+idx+'][id_bahan]'" required class="select-text text-xs" x-model="item.id_bahan">
                                            <option value="">-- Pilih Bahan --</option>
                                            @foreach($bahanList as $b)
                                                <option value="{{ $b->id }}">{{ $b->nama_bahan }} ({{ $b->satuan }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-3">
                                        <input type="number" step="any" min="0.01" :name="'items['+idx+'][jumlah]'" x-model="item.jumlah" required placeholder="Jumlah" class="input-text text-xs font-mono font-semibold">
                                    </div>
                                    <div class="col-span-1 text-center">
                                        <button type="button" x-show="items.length > 1" @click="items.splice(idx, 1)" class="text-danger font-bold text-sm hover:opacity-75">&times;</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Catatan Tambahan (Waste / Sisa)</label>
                        <textarea name="catatan" rows="2" placeholder="Keterangan tambahan jika ada waste potong..." class="input-text text-xs"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-[#E2DED2]">
                        <button type="button" @click="openPakai = false" class="btn btn-secondary text-xs">Batal</button>
                        <button type="submit" class="btn btn-primary text-xs">Potong Stok & Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Table Dense Pemakaian -->
    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-dense">
                <thead>
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>No. Dokumen</th>
                        <th>Tanggal</th>
                        <th>Lokasi</th>
                        <th>No. SPK</th>
                        <th>Keperluan Order Cetak</th>
                        <th>Bahan yang Digunakan</th>
                        <th>Operator</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pemakaianList as $index => $pk)
                        <tr>
                            <td class="text-center font-mono text-xs text-[#6B665A]">{{ $pemakaianList->firstItem() + $index }}</td>
                            <td class="font-mono text-xs font-bold text-[#0B6B73]">{{ $pk->nomor_pemakaian }}</td>
                            <td class="font-mono text-xs text-[#6B665A]">{{ $pk->tanggal_pemakaian->format('d/m/Y') }}</td>
                            <td class="text-xs font-medium text-[#1C1A15]">
                                <span class="px-1.5 py-0.5 rounded text-[10.5px] font-mono bg-blue-100 text-blue-800">
                                    {{ $pk->lokasi->kode_lokasi }}
                                </span>
                            </td>
                            <td class="font-mono text-xs text-[#1C1A15]">
                                {{ $pk->nomor_spk ?? '-' }}
                            </td>
                            <td class="text-xs text-[#1C1A15] max-w-xs">
                                <div>{{ $pk->keperluan }}</div>
                                @if($pk->catatan)
                                    <div class="text-[11px] text-[#6B665A] italic">{{ $pk->catatan }}</div>
                                @endif
                            </td>
                            <td class="text-xs text-[#1C1A15]">
                                <ul class="list-disc list-inside space-y-0.5">
                                    @foreach($pk->details as $d)
                                        <li>{{ $d->bahan->nama_bahan }}: <strong class="font-mono text-danger">-{{ number_format($d->jumlah, 0, ',', '.') }} {{ $d->bahan->satuan }}</strong></li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-xs text-[#6B665A]">{{ $pk->user->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-xs text-[#6B665A]">Belum ada riwayat pemakaian bahan produksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pemakaianList->hasPages())
            <div class="p-3 border-t border-[#E2DED2] bg-[#FAFAFA]">
                {{ $pemakaianList->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
