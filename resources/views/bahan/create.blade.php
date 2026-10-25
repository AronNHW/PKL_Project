<x-app-layout>
    <x-slot name="title">Tambah Bahan Baru</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="panel p-5">
            <div class="pb-3 mb-4 border-b border-[#E2DED2] flex justify-between items-center">
                <div>
                    <h2 class="text-sm font-semibold text-[#1C1A15]">Formulir Pendaftaran Bahan</h2>
                    <p class="text-xs text-[#6B665A]">Masukkan rincian spesifikasi media cetak atau suku cadang mesin.</p>
                </div>
                <a href="{{ route('bahan.index') }}" class="btn btn-secondary text-xs">&larr; Kembali</a>
            </div>

            <form method="POST" action="{{ route('bahan.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Kode Bahan <span class="text-danger">*</span></label>
                        <input type="text" name="kode_bahan" value="{{ old('kode_bahan') }}" required placeholder="Contoh: BHN-FLX-280" class="input-text text-xs font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Kategori <span class="text-danger">*</span></label>
                        <select name="id_kategori" required class="select-text text-xs">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat->id }}" {{ old('id_kategori') == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }} ({{ $kat->kode_kategori }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Nama Lengkap Bahan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_bahan" value="{{ old('nama_bahan') }}" required placeholder="Contoh: Spanduk Flexi Frontlite 280gr (3.2m x 70m)" class="input-text text-xs">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Satuan Ukur <span class="text-danger">*</span></label>
                        <select name="satuan" required class="select-text text-xs">
                            <option value="roll" {{ old('satuan') == 'roll' ? 'selected' : '' }}>Roll (Gulungan)</option>
                            <option value="rim" {{ old('satuan') == 'rim' ? 'selected' : '' }}>Rim (500 Lembar)</option>
                            <option value="liter" {{ old('satuan') == 'liter' ? 'selected' : '' }}>Liter (Cairan / Tinta)</option>
                            <option value="pcs" {{ old('satuan') == 'pcs' ? 'selected' : '' }}>Pcs (Suku Cadang / Buah)</option>
                            <option value="box" {{ old('satuan') == 'box' ? 'selected' : '' }}>Box (Kotak)</option>
                            <option value="meter" {{ old('satuan') == 'meter' ? 'selected' : '' }}>Meter</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Estimasi Harga Beli Satuan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="any" min="0" name="harga_estimasi" value="{{ old('harga_estimasi', 0) }}" required class="input-text text-xs font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Spesifikasi / Karakteristik Teknis</label>
                    <textarea name="spesifikasi" rows="2" placeholder="Ketebalan gsm, dimensi lebar x panjang, tipe lem perekat, kegunaan khusus..." class="input-text text-xs">{{ old('spesifikasi') }}</textarea>
                </div>

                <div class="p-3.5 bg-[#F1EEE6] border border-[#E2DED2] rounded-md space-y-3">
                    <h3 class="text-xs font-semibold text-[#1C1A15]">Inisialisasi Batas Stok & Saldo Awal</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#1C1A15] mb-1">Batas Stok Minimum Default</label>
                            <input type="number" step="any" min="0" name="stok_minimum" value="{{ old('stok_minimum', 5) }}" class="input-text text-xs font-mono">
                            <span class="text-[11px] text-[#6B665A]">Diaplikasikan ke semua cabang</span>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#1C1A15] mb-1">Saldo Awal Gudang Pusat</label>
                            <input type="number" step="any" min="0" name="stok_awal_pusat" value="{{ old('stok_awal_pusat', 0) }}" class="input-text text-xs font-mono">
                            <span class="text-[11px] text-[#6B665A]">Opsional (dicatat otomatis ke mutasi stok)</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-[#E2DED2]">
                    <a href="{{ route('bahan.index') }}" class="btn btn-secondary text-xs">Batal</a>
                    <button type="submit" class="btn btn-primary text-xs">Simpan Data Bahan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
