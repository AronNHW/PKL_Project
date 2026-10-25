<x-app-layout>
    <x-slot name="title">Kelola Akun Pengguna & Hak Akses</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" x-data="{ isEdit: false, editId: null, name: '', email: '', role: 'admin_cabang', lokasiId: '', telp: '', aktif: 1 }">
        <!-- Form Akun (1 col) -->
        <div class="panel p-4 h-fit">
            <h2 class="text-sm font-semibold text-[#1C1A15] pb-2 mb-3 border-b border-[#E2DED2]" x-text="isEdit ? 'Ubah Data Pengguna' : 'Tambah Pengguna Baru'"></h2>

            <form method="POST" :action="isEdit ? '{{ url('users') }}/' + editId : '{{ route('users.store') }}'" class="space-y-3">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" x-model="name" required placeholder="Nama staf / operator" class="input-text text-xs">
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Email Login <span class="text-danger">*</span></label>
                    <input type="email" name="email" x-model="email" required placeholder="user@cetakstok.com" class="input-text text-xs">
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">
                        Kata Sandi <span class="text-danger" x-show="!isEdit">*</span>
                        <span class="text-[#6B665A] font-normal" x-show="isEdit">(Kosongkan jika tidak diganti)</span>
                    </label>
                    <input type="password" name="password" :required="!isEdit" placeholder="••••••••" class="input-text text-xs">
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Peran Hak Akses <span class="text-danger">*</span></label>
                    <select name="role" x-model="role" required class="select-text text-xs">
                        <option value="admin_cabang">Admin Cabang (Operasional Cabang)</option>
                        <option value="admin_pusat">Admin Pusat (Full Control Master & Distribusi)</option>
                        <option value="owner">Owner / Direksi (Monitoring & Laporan Baca Saja)</option>
                    </select>
                </div>

                <div x-show="role === 'admin_cabang'">
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Penempatan Cabang <span class="text-danger">*</span></label>
                    <select name="id_lokasi" x-model="lokasiId" :required="role === 'admin_cabang'" class="select-text text-xs">
                        <option value="">-- Pilih Cabang --</option>
                        @foreach($lokasiList as $lok)
                            <option value="{{ $lok->id }}">{{ $lok->nama_lokasi }} ({{ $lok->kode_lokasi }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">No. WhatsApp / Telepon</label>
                    <input type="text" name="telepon" x-model="telp" placeholder="08..." class="input-text text-xs font-mono">
                </div>

                <template x-if="isEdit">
                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Status Akun</label>
                        <select name="is_aktif" x-model="aktif" class="select-text text-xs">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </template>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#E2DED2]">
                    <button type="button" x-show="isEdit" @click="isEdit = false; editId = null; name = ''; email = ''; role = 'admin_cabang'; lokasiId = ''; telp = ''; aktif = 1;" class="btn btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn btn-primary text-xs" x-text="isEdit ? 'Simpan Akun' : 'Daftarkan Akun'"></button>
                </div>
            </form>
        </div>

        <!-- Tabel Pengguna (2 cols) -->
        <div class="md:col-span-2 panel overflow-hidden">
            <div class="p-3.5 border-b border-[#E2DED2] bg-[#FAFAFA] flex justify-between items-center">
                <h2 class="text-xs font-semibold text-[#1C1A15]">Pengguna Sistem Terdaftar</h2>
                <span class="text-[11px] text-[#6B665A]">{{ count($usersList) }} Pengguna</span>
            </div>

            <div class="overflow-x-auto">
                <table class="table-dense">
                    <thead>
                        <tr>
                            <th class="w-10 text-center">No</th>
                            <th>Nama Pengguna</th>
                            <th>Peran Akses</th>
                            <th>Lokasi Penugasan</th>
                            <th>Kontak</th>
                            <th class="text-center">Status</th>
                            <th class="text-right w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usersList as $index => $u)
                            <tr>
                                <td class="text-center font-mono text-xs text-[#6B665A]">{{ $index + 1 }}</td>
                                <td class="text-xs">
                                    <div class="font-medium text-[#1C1A15]">{{ $u->name }}</div>
                                    <div class="font-mono text-[11px] text-[#6B665A]">{{ $u->email }}</div>
                                </td>
                                <td>
                                    @php
                                        $roleBadge = match($u->role) {
                                            'owner' => 'badge-muted',
                                            'admin_pusat' => 'badge-accent',
                                            'admin_cabang' => 'badge-warn',
                                            default => 'badge-muted'
                                        };
                                        $roleText = match($u->role) {
                                            'owner' => 'Owner',
                                            'admin_pusat' => 'Admin Pusat',
                                            'admin_cabang' => 'Admin Cabang',
                                            default => $u->role
                                        };
                                    @endphp
                                    <span class="badge {{ $roleBadge }}">{{ $roleText }}</span>
                                </td>
                                <td class="text-xs font-medium text-[#1C1A15]">
                                    @if($u->lokasi)
                                        <span class="font-mono">{{ $u->lokasi->kode_lokasi }}</span> - {{ $u->lokasi->nama_lokasi }}
                                    @else
                                        <span class="text-[#6B665A] italic">Semua Lokasi (Pusat/Owner)</span>
                                    @endif
                                </td>
                                <td class="text-xs font-mono text-[#6B665A]">{{ $u->telepon ?? '-' }}</td>
                                <td class="text-center">
                                    @if($u->is_aktif)
                                        <span class="badge badge-ok">Aktif</span>
                                    @else
                                        <span class="badge badge-muted">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <button type="button" 
                                            @click="isEdit = true; editId = {{ $u->id }}; name = '{{ addslashes($u->name) }}'; email = '{{ addslashes($u->email) }}'; role = '{{ $u->role }}'; lokasiId = '{{ $u->id_lokasi ?? '' }}'; telp = '{{ addslashes($u->telepon ?? '') }}'; aktif = {{ $u->is_aktif ? 1 : 0 }};" 
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
