<x-app-layout>
    <x-slot name="title">Manajemen Lokasi (Pusat & Cabang)</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" x-data="{ isEdit: false, editId: null, kode: '', nama: '', tipe: 'cabang', pic: '', telp: '', alamat: '', aktif: 1 }">
        <!-- Form Tambah/Edit Lokasi (1 col) -->
        <div class="panel p-4 h-fit">
            <h2 class="text-sm font-semibold text-[#1C1A15] pb-2 mb-3 border-b border-[#E2DED2]" x-text="isEdit ? 'Ubah Data Lokasi' : 'Tambah Lokasi Baru'"></h2>

            <form method="POST" :action="isEdit ? '{{ url('lokasi') }}/' + editId : '{{ route('lokasi.store') }}'" class="space-y-3">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Kode Lokasi <span class="text-danger">*</span></label>
                        <input type="text" name="kode_lokasi" x-model="kode" required placeholder="CB-06" class="input-text text-xs font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Tipe Lokasi <span class="text-danger">*</span></label>
                        <select name="tipe" x-model="tipe" required class="select-text text-xs">
                            <option value="cabang">Cabang</option>
                            <option value="pusat">Gudang Pusat</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Nama Lokasi / Cabang <span class="text-danger">*</span></label>
                    <input type="text" name="nama_lokasi" x-model="nama" required placeholder="Contoh: Cabang Krian" class="input-text text-xs">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Penanggung Jawab</label>
                        <input type="text" name="penanggung_jawab" x-model="pic" placeholder="Nama Kepala Cabang" class="input-text text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">No. Telepon</label>
                        <input type="text" name="telepon" x-model="telp" placeholder="031-..." class="input-text text-xs font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Alamat Lengkap</label>
                    <textarea name="alamat" x-model="alamat" rows="2" placeholder="Alamat jalan..." class="input-text text-xs"></textarea>
                </div>

                <template x-if="isEdit">
                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Status Keaktifan</label>
                        <select name="is_aktif" x-model="aktif" class="select-text text-xs">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </template>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#E2DED2]">
                    <button type="button" x-show="isEdit" @click="isEdit = false; editId = null; kode = ''; nama = ''; tipe = 'cabang'; pic = ''; telp = ''; alamat = ''; aktif = 1;" class="btn btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn btn-primary text-xs" x-text="isEdit ? 'Simpan Perubahan' : 'Tambahkan Lokasi'"></button>
                </div>
            </form>
        </div>

        <!-- Tabel Lokasi (2 cols) -->
        <div class="md:col-span-2 panel overflow-hidden">
            <div class="p-3.5 border-b border-[#E2DED2] bg-[#FAFAFA] flex justify-between items-center">
                <h2 class="text-xs font-semibold text-[#1C1A15]">Jaringan Gudang & Cabang</h2>
                <span class="text-[11px] text-[#6B665A]">{{ count($lokasiList) }} Lokasi</span>
            </div>

            <div class="overflow-x-auto">
                <table class="table-dense">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Lokasi</th>
                            <th>Tipe</th>
                            <th>Kepala Cabang / PIC</th>
                            <th>Telepon</th>
                            <th class="text-center">Mesin</th>
                            <th class="text-center">Status</th>
                            <th class="text-right w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lokasiList as $lok)
                            <tr>
                                <td class="font-mono text-xs font-semibold text-[#0B6B73]">{{ $lok->kode_lokasi }}</td>
                                <td class="font-medium text-xs text-[#1C1A15]">
                                    <div>{{ $lok->nama_lokasi }}</div>
                                    <div class="text-[11px] text-[#6B665A] truncate max-w-xs">{{ $lok->alamat ?? '-' }}</div>
                                </td>
                                <td>
                                    <span class="px-2 py-0.5 rounded text-[10.5px] font-mono {{ $lok->isPusat() ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ strtoupper($lok->tipe) }}
                                    </span>
                                </td>
                                <td class="text-xs text-[#1C1A15]">{{ $lok->penanggung_jawab ?? '-' }}</td>
                                <td class="text-xs font-mono text-[#6B665A]">{{ $lok->telepon ?? '-' }}</td>
                                <td class="text-center font-mono text-xs text-[#1C1A15]">
                                    {{ $lok->mesin_count }} unit
                                </td>
                                <td class="text-center">
                                    @if($lok->is_aktif)
                                        <span class="badge badge-ok">Aktif</span>
                                    @else
                                        <span class="badge badge-muted">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <button type="button" 
                                            @click="isEdit = true; editId = {{ $lok->id }}; kode = '{{ addslashes($lok->kode_lokasi) }}'; nama = '{{ addslashes($lok->nama_lokasi) }}'; tipe = '{{ $lok->tipe }}'; pic = '{{ addslashes($lok->penanggung_jawab ?? '') }}'; telp = '{{ addslashes($lok->telepon ?? '') }}'; alamat = '{{ addslashes($lok->alamat ?? '') }}'; aktif = {{ $lok->is_aktif ? 1 : 0 }};" 
                                            class="text-xs text-[#0B6B73] hover:underline font-medium">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
