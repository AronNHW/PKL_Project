<x-app-layout>
    <x-slot name="title">Barang Masuk (Penerimaan Supplier)</x-slot>

    <!-- Top Action Bar -->
    <div class="panel p-3.5 mb-4" x-data="{ openModal: false, items: [{ id_bahan: '', jumlah: 1, harga_satuan: 0 }] }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('barang-masuk.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
                <div class="w-64">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari no masuk, no PO, SJ..." class="input-text text-xs">
                </div>

                <div class="w-48">
                    <select name="supplier_id" class="select-text text-xs" onchange="this.form.submit()">
                        <option value="">Semua Supplier</option>
                        @foreach($supplierList as $sup)
                            <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-secondary text-xs">Cari</button>

                @if(request()->anyFilled(['q', 'supplier_id']))
                    <a href="{{ route('barang-masuk.index') }}" class="text-xs text-[#6B665A] hover:text-danger ml-1">Reset</a>
                @endif
            </form>

            @if(auth()->user()->isAdminPusat())
                <button type="button" @click="openModal = true" class="btn btn-primary text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Catat Barang Masuk</span>
                </button>
            @endif
        </div>

        <!-- Modal Form Barang Masuk -->
        <div x-show="openModal" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="panel bg-white w-full max-w-2xl max-h-[90vh] overflow-y-auto p-5" @click.outside="openModal = false">
                <div class="flex justify-between items-center pb-3 border-b border-[#E2DED2] mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-[#1C1A15]">Penerimaan Barang Masuk dari Supplier</h3>
                        <p class="text-xs text-[#6B665A] mt-0.5">Stok gudang tujuan akan bertambah otomatis melalui transaksi StokService.</p>
                    </div>
                    <button @click="openModal = false" class="text-[#6B665A] hover:text-danger text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('barang-masuk.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-[#1C1A15] mb-1">Supplier Rekanan <span class="text-danger">*</span></label>
                            <select name="id_supplier" required class="select-text text-xs">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($supplierList as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->nama_supplier }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#1C1A15] mb-1">Gudang Penerima <span class="text-danger">*</span></label>
                            <select name="id_lokasi" required class="select-text text-xs">
                                @foreach($lokasiList as $lok)
                                    <option value="{{ $lok->id }}">{{ $lok->nama_lokasi }} ({{ $lok->kode_lokasi }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#1C1A15] mb-1">Tanggal Terima <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_masuk" value="{{ date('Y-m-d') }}" required class="input-text text-xs font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-[#1C1A15] mb-1">No. Purchase Order (PO)</label>
                            <input type="text" name="nomor_po" placeholder="Contoh: PO-2026-0042" class="input-text text-xs font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#1C1A15] mb-1">No. Surat Jalan Supplier</label>
                            <input type="text" name="nomor_surat_jalan_supplier" placeholder="Contoh: SJ-SMG/1029" class="input-text text-xs font-mono">
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="pt-2 border-t border-[#E2DED2]">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-semibold text-[#1C1A15]">Daftar Item Bahan yang Diterima</span>
                            <button type="button" @click="items.push({ id_bahan: '', jumlah: 1, harga_satuan: 0 })" class="btn btn-secondary text-xs py-0.5 px-2">
                                + Tambah Baris
                            </button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(item, idx) in items" :key="idx">
                                <div class="grid grid-cols-12 gap-2 items-center bg-[#F1EEE6] p-2 rounded-md border border-[#E2DED2]">
                                    <div class="col-span-6">
                                        <select :name="'items['+idx+'][id_bahan]'" required class="select-text text-xs" x-model="item.id_bahan">
                                            <option value="">-- Pilih Bahan --</option>
                                            @foreach($bahanList as $b)
                                                <option value="{{ $b->id }}">{{ $b->nama_bahan }} ({{ $b->satuan }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-3">
                                        <input type="number" step="any" min="0.01" :name="'items['+idx+'][jumlah]'" x-model="item.jumlah" required placeholder="Jumlah" class="input-text text-xs font-mono">
                                    </div>
                                    <div class="col-span-2">
                                        <input type="number" step="any" min="0" :name="'items['+idx+'][harga_satuan]'" x-model="item.harga_satuan" placeholder="Harga" class="input-text text-xs font-mono">
                                    </div>
                                    <div class="col-span-1 text-center">
                                        <button type="button" x-show="items.length > 1" @click="items.splice(idx, 1)" class="text-danger font-bold text-sm hover:opacity-75">&times;</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Catatan Tambahan</label>
                        <textarea name="catatan" rows="2" placeholder="Kondisi pengiriman, sopir, atau keterangan lainnya..." class="input-text text-xs"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-[#E2DED2]">
                        <button type="button" @click="openModal = false" class="btn btn-secondary text-xs">Batal</button>
                        <button type="submit" class="btn btn-primary text-xs">Simpan & Perbarui Stok</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Table Dense Barang Masuk -->
    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-dense">
                <thead>
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>Nomor Masuk</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Gudang Tujuan</th>
                        <th>No. PO / Ref Surat Jalan</th>
                        <th class="text-center">Item Diterima</th>
                        <th>Pencatat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangMasukList as $index => $bm)
                        <tr>
                            <td class="text-center font-mono text-xs text-[#6B665A]">{{ $barangMasukList->firstItem() + $index }}</td>
                            <td class="font-mono text-xs font-bold text-[#0B6B73]">{{ $bm->nomor_masuk }}</td>
                            <td class="font-mono text-xs text-[#6B665A]">{{ $bm->tanggal_masuk->format('d/m/Y') }}</td>
                            <td class="text-xs font-medium text-[#1C1A15]">{{ $bm->supplier->nama_supplier }}</td>
                            <td class="text-xs text-[#1C1A15]">
                                <span class="px-1.5 py-0.5 rounded text-[10.5px] font-mono bg-blue-100 text-blue-800">
                                    {{ $bm->lokasi->kode_lokasi }}
                                </span>
                                <span class="text-[#6B665A] ml-1">{{ $bm->lokasi->nama_lokasi }}</span>
                            </td>
                            <td class="font-mono text-xs text-[#6B665A]">
                                {{ $bm->nomor_po ?? '-' }} / {{ $bm->nomor_surat_jalan_supplier ?? '-' }}
                            </td>
                            <td class="text-center text-xs">
                                <span class="px-2 py-0.5 bg-[#F1EEE6] border border-[#E2DED2] rounded font-mono font-semibold">
                                    {{ $bm->details->count() }} jenis
                                </span>
                            </td>
                            <td class="text-xs text-[#6B665A]">{{ $bm->user->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-xs text-[#6B665A]">Belum ada riwayat penerimaan barang masuk dari supplier.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($barangMasukList->hasPages())
            <div class="p-3 border-t border-[#E2DED2] bg-[#FAFAFA]">
                {{ $barangMasukList->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
