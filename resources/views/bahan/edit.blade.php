<x-app-layout>
    <x-slot name="title">Edit Bahan: {{ $bahan->kode_bahan }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="panel p-5">
            <div class="pb-3 mb-4 border-b border-[#E2DED2] flex justify-between items-center">
                <div>
                    <h2 class="text-sm font-semibold text-[#1C1A15]">Ubah Data Bahan</h2>
                    <p class="text-xs text-[#6B665A]">Perbarui informasi master data {{ $bahan->nama_bahan }}.</p>
                </div>
                <a href="{{ route('bahan.index') }}" class="btn btn-secondary text-xs">&larr; Kembali</a>
            </div>

            <form method="POST" action="{{ route('bahan.update', $bahan) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Kode Bahan <span class="text-danger">*</span></label>
                        <input type="text" name="kode_bahan" value="{{ old('kode_bahan', $bahan->kode_bahan) }}" required class="input-text text-xs font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Kategori <span class="text-danger">*</span></label>
                        <select name="id_kategori" required class="select-text text-xs">
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat->id }}" {{ old('id_kategori', $bahan->id_kategori) == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }} ({{ $kat->kode_kategori }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Nama Lengkap Bahan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_bahan" value="{{ old('nama_bahan', $bahan->nama_bahan) }}" required class="input-text text-xs">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Satuan Ukur <span class="text-danger">*</span></label>
                        <select name="satuan" required class="select-text text-xs">
                            @foreach(['roll', 'rim', 'liter', 'pcs', 'box', 'meter'] as $sat)
                                <option value="{{ $sat }}" {{ old('satuan', $bahan->satuan) == $sat ? 'selected' : '' }}>
                                    {{ ucfirst($sat) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Estimasi Harga Beli (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="any" min="0" name="harga_estimasi" value="{{ old('harga_estimasi', $bahan->harga_estimasi) }}" required class="input-text text-xs font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Spesifikasi Teknis</label>
                    <textarea name="spesifikasi" rows="3" class="input-text text-xs">{{ old('spesifikasi', $bahan->spesifikasi) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Status Keaktifan</label>
                    <select name="is_aktif" class="select-text text-xs">
                        <option value="1" {{ old('is_aktif', $bahan->is_aktif) ? 'selected' : '' }}>Aktif (Dapat Ditransaksikan)</option>
                        <option value="0" {{ !old('is_aktif', $bahan->is_aktif) ? 'selected' : '' }}>Nonaktif (Arsip / Tidak Digunakan)</option>
                    </select>
                </div>

                <div class="flex justify-between items-center pt-3 border-t border-[#E2DED2]">
                    <button type="button" 
                            onclick="if(confirm('Hapus bahan ini? Jika sudah ada riwayat mutasi stok, bahan akan dinonaktifkan otomatis.')) document.getElementById('delete-form').submit();" 
                            class="btn btn-danger text-xs">
                        Hapus Bahan
                    </button>

                    <div class="flex gap-2">
                        <a href="{{ route('bahan.index') }}" class="btn btn-secondary text-xs">Batal</a>
                        <button type="submit" class="btn btn-primary text-xs">Simpan Perubahan</button>
                    </div>
                </div>
            </form>

            <form id="delete-form" method="POST" action="{{ route('bahan.destroy', $bahan) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</x-app-layout>
