<x-app-layout>
    <x-slot name="title">Kategori Master Bahan</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" x-data="{ isEdit: false, editId: null, kode: '', nama: '', deskripsi: '' }">
        <!-- Form Tambah/Edit Kategori (1 col) -->
        <div class="panel p-4 h-fit">
            <h2 class="text-sm font-semibold text-[#1C1A15] pb-2 mb-3 border-b border-[#E2DED2]" x-text="isEdit ? 'Ubah Kategori' : 'Tambah Kategori Baru'"></h2>

            <form method="POST" :action="isEdit ? '{{ url('kategori') }}/' + editId : '{{ route('kategori.store') }}'" class="space-y-3">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Kode Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="kode_kategori" x-model="kode" required placeholder="Contoh: KAT-OUT" class="input-text text-xs font-mono">
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="nama_kategori" x-model="nama" required placeholder="Contoh: Media Outdoor" class="input-text text-xs">
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Deskripsi Singkat</label>
                    <textarea name="deskripsi" x-model="deskripsi" rows="2" placeholder="Keterangan pengelompokan..." class="input-text text-xs"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#E2DED2]">
                    <button type="button" x-show="isEdit" @click="isEdit = false; editId = null; kode = ''; nama = ''; deskripsi = '';" class="btn btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn btn-primary text-xs" x-text="isEdit ? 'Simpan Perubahan' : 'Tambahkan Kategori'"></button>
                </div>
            </form>
        </div>

        <!-- Daftar Kategori (2 cols) -->
        <div class="md:col-span-2 panel overflow-hidden">
            <div class="p-3.5 border-b border-[#E2DED2] bg-[#FAFAFA] flex justify-between items-center">
                <h2 class="text-xs font-semibold text-[#1C1A15]">Daftar Kategori Terdaftar</h2>
                <span class="text-[11px] text-[#6B665A]">{{ count($kategoriList) }} Kategori</span>
            </div>

            <div class="overflow-x-auto">
                <table class="table-dense">
                    <thead>
                        <tr>
                            <th class="w-12 text-center">No</th>
                            <th>Kode</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Jumlah Bahan</th>
                            <th class="text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kategoriList as $index => $kat)
                            <tr>
                                <td class="text-center font-mono text-xs text-[#6B665A]">{{ $index + 1 }}</td>
                                <td class="font-mono text-xs font-semibold text-[#0B6B73]">{{ $kat->kode_kategori }}</td>
                                <td class="font-medium text-xs text-[#1C1A15]">{{ $kat->nama_kategori }}</td>
                                <td class="text-xs text-[#6B665A] max-w-xs truncate">{{ $kat->deskripsi ?? '-' }}</td>
                                <td class="text-center font-mono text-xs text-[#1C1A15]">
                                    <span class="px-2 py-0.5 bg-[#F1EEE6] border border-[#E2DED2] rounded font-semibold">
                                        {{ $kat->bahan_count }} item
                                    </span>
                                </td>
                                <td class="text-right">
                                    <button type="button" 
                                            @click="isEdit = true; editId = {{ $kat->id }}; kode = '{{ addslashes($kat->kode_kategori) }}'; nama = '{{ addslashes($kat->nama_kategori) }}'; deskripsi = '{{ addslashes($kat->deskripsi ?? '') }}';" 
                                            class="text-xs text-[#0B6B73] hover:underline font-medium">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-6 text-xs text-[#6B665A]">Belum ada kategori yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
