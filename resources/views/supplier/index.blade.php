<x-app-layout>
    <x-slot name="title">Daftar Supplier Mitra Rekanan</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" x-data="{ isEdit: false, editId: null, kode: '', nama: '', kontak: '', telp: '', email: '', alamat: '' }">
        <!-- Form Tambah/Edit Supplier (1 col) -->
        <div class="panel p-4 h-fit">
            <h2 class="text-sm font-semibold text-[#1C1A15] pb-2 mb-3 border-b border-[#E2DED2]" x-text="isEdit ? 'Ubah Data Supplier' : 'Daftarkan Supplier Baru'"></h2>

            <form method="POST" :action="isEdit ? '{{ url('supplier') }}/' + editId : '{{ route('supplier.store') }}'" class="space-y-3">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Kode Supplier <span class="text-danger">*</span></label>
                    <input type="text" name="kode_supplier" x-model="kode" required placeholder="Contoh: SUP-005" class="input-text text-xs font-mono">
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Nama Perusahaan Supplier <span class="text-danger">*</span></label>
                    <input type="text" name="nama_supplier" x-model="nama" required placeholder="Contoh: PT Digital Media Grafika" class="input-text text-xs">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Kontak Person</label>
                        <input type="text" name="kontak_person" x-model="kontak" placeholder="Nama PIC" class="input-text text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">No. Telepon</label>
                        <input type="text" name="telepon" x-model="telp" placeholder="021-..." class="input-text text-xs font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Alamat Email</label>
                    <input type="email" name="email" x-model="email" placeholder="sales@perusahaan.com" class="input-text text-xs">
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Alamat Kantor / Pergudangan</label>
                    <textarea name="alamat" x-model="alamat" rows="2" placeholder="Alamat lengkap..." class="input-text text-xs"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#E2DED2]">
                    <button type="button" x-show="isEdit" @click="isEdit = false; editId = null; kode = ''; nama = ''; kontak = ''; telp = ''; email = ''; alamat = '';" class="btn btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn btn-primary text-xs" x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Supplier'"></button>
                </div>
            </form>
        </div>

        <!-- Tabel Supplier (2 cols) -->
        <div class="md:col-span-2 panel overflow-hidden">
            <div class="p-3.5 border-b border-[#E2DED2] bg-[#FAFAFA] flex justify-between items-center">
                <h2 class="text-xs font-semibold text-[#1C1A15]">Mitra Supplier Terdaftar</h2>
                <span class="text-[11px] text-[#6B665A]">{{ count($supplierList) }} Perusahaan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="table-dense">
                    <thead>
                        <tr>
                            <th class="w-12 text-center">No</th>
                            <th>Kode</th>
                            <th>Nama Supplier</th>
                            <th>Kontak / PIC</th>
                            <th>No. Telepon</th>
                            <th>Alamat</th>
                            <th class="text-right w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplierList as $index => $sup)
                            <tr>
                                <td class="text-center font-mono text-xs text-[#6B665A]">{{ $index + 1 }}</td>
                                <td class="font-mono text-xs font-semibold text-[#0B6B73]">{{ $sup->kode_supplier }}</td>
                                <td class="font-medium text-xs text-[#1C1A15]">
                                    <div>{{ $sup->nama_supplier }}</div>
                                    @if($sup->email)
                                        <div class="text-[11px] text-[#6B665A] font-mono">{{ $sup->email }}</div>
                                    @endif
                                </td>
                                <td class="text-xs text-[#1C1A15]">{{ $sup->kontak_person ?? '-' }}</td>
                                <td class="text-xs font-mono text-[#6B665A]">{{ $sup->telepon ?? '-' }}</td>
                                <td class="text-xs text-[#6B665A] max-w-xs truncate">{{ $sup->alamat ?? '-' }}</td>
                                <td class="text-right">
                                    <button type="button" 
                                            @click="isEdit = true; editId = {{ $sup->id }}; kode = '{{ addslashes($sup->kode_supplier) }}'; nama = '{{ addslashes($sup->nama_supplier) }}'; kontak = '{{ addslashes($sup->kontak_person ?? '') }}'; telp = '{{ addslashes($sup->telepon ?? '') }}'; email = '{{ addslashes($sup->email ?? '') }}'; alamat = '{{ addslashes($sup->alamat ?? '') }}';" 
                                            class="text-xs text-[#0B6B73] hover:underline font-medium">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-6 text-xs text-[#6B665A]">Belum ada supplier yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
