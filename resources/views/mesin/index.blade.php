<x-app-layout>
    <x-slot name="title">Manajemen Mesin & Laporan Kerusakan</x-slot>

    <!-- Top Action & Grid Mesin -->
    <div class="mb-6" x-data="{ openLapor: false, openStatusModal: false, activeLaporan: null }">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-sm font-semibold text-[#1C1A15]">Daftar Mesin Operasional</h2>
            @if(!auth()->user()->isOwner())
                <button type="button" @click="openLapor = true" class="btn btn-primary text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Buat Laporan Kerusakan Mesin</span>
                </button>
            @endif
        </div>

        <!-- Cards Unit Mesin -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            @foreach($mesinList as $m)
                <div class="panel p-3 border-l-4 {{ $m->status === 'rusak' ? 'border-l-danger bg-red-50/20' : ($m->status === 'perbaikan' ? 'border-l-warn bg-amber-50/20' : 'border-l-ok') }}">
                    <div class="flex justify-between items-start">
                        <span class="font-mono text-[10px] text-[#6B665A]">{{ $m->kode_mesin }}</span>
                        <x-status-chip :status="ucfirst($m->status)" />
                    </div>
                    <div class="font-semibold text-xs text-[#1C1A15] mt-1">{{ $m->nama_mesin }}</div>
                    <div class="text-[11px] text-[#6B665A] mt-0.5">{{ $m->tipe_mesin ?? 'Printer' }}</div>
                    <div class="mt-2 text-[10px] font-mono text-[#0B6B73] bg-blue-50/50 px-1.5 py-0.5 rounded w-fit">
                        {{ $m->lokasi->kode_lokasi }} - {{ $m->lokasi->nama_lokasi }}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Modal Buat Laporan Kerusakan (Section 9.6: mobile-friendly 44px buttons, camera upload) -->
        <div x-show="openLapor" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="panel bg-white w-full max-w-lg max-h-[90vh] overflow-y-auto p-5" @click.outside="openLapor = false">
                <div class="flex justify-between items-center pb-3 border-b border-[#E2DED2] mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-[#1C1A15]">Pelaporan Kerusakan Mesin</h3>
                        <p class="text-xs text-[#6B665A] mt-0.5">Sertakan foto kendala fisik dan kronologi lengkap untuk teknisi pusat.</p>
                    </div>
                    <button @click="openLapor = false" class="text-[#6B665A] hover:text-danger text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('mesin.lapor') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Pilih Mesin yang Bermasalah <span class="text-danger">*</span></label>
                        <select name="id_mesin" required class="select-text text-xs min-h-[40px]">
                            <option value="">-- Pilih Mesin --</option>
                            @foreach($mesinList as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mesin }} ({{ $m->lokasi->kode_lokasi }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Tanggal Kerusakan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_lapor" value="{{ date('Y-m-d') }}" required class="input-text text-xs font-mono min-h-[40px]">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Gejala Kerusakan Singkat <span class="text-danger">*</span></label>
                        <input type="text" name="gejala" required placeholder="Contoh: Nozzle head mampet total, motor carriage bunyi mendengung" class="input-text text-xs min-h-[40px]">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Kronologi Kejadian <span class="text-danger">*</span></label>
                        <textarea name="kronologi" rows="3" required placeholder="Ceritakan bagaimana kerusakan terjadi saat proses cetak..." class="input-text text-xs"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-3 bg-[#F1EEE6] border border-[#E2DED2] rounded">
                        <div>
                            <label class="block text-xs font-medium text-[#1C1A15] mb-1">Dugaan Suku Cadang Butuh Ganti</label>
                            <select name="id_bahan" class="select-text text-xs min-h-[38px]">
                                <option value="">-- Tidak Tahu / Belum Ada --</option>
                                @foreach($sukuCadangList as $sk)
                                    <option value="{{ $sk->id }}">{{ $sk->nama_bahan }} ({{ $sk->satuan }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[#1C1A15] mb-1">Estimasi Jumlah</label>
                            <input type="number" step="any" min="1" name="jumlah_suku_cadang" value="1" class="input-text text-xs font-mono min-h-[38px]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Foto Bukti Kerusakan / Hasil Cetak Rusak</label>
                        <input type="file" name="foto_kerusakan" accept="image/*" capture="environment" class="input-text text-xs file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-[#0B6B73] file:text-white">
                        <span class="text-[11px] text-[#6B665A] block mt-1">Dapat difoto langsung dari kamera HP (maks 5MB).</span>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-[#E2DED2]">
                        <button type="button" @click="openLapor = false" class="btn btn-secondary text-xs min-h-[40px]">Batal</button>
                        <button type="submit" class="btn btn-primary text-xs min-h-[40px]">Kirim Laporan Kerusakan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Laporan Kerusakan & Riwayat Perbaikan -->
        <div class="panel overflow-hidden">
            <div class="p-3.5 border-b border-[#E2DED2] bg-[#FAFAFA] flex justify-between items-center">
                <h2 class="text-xs font-semibold text-[#1C1A15]">Histori Laporan Kerusakan & Perbaikan</h2>
                <span class="text-[11px] text-[#6B665A]">{{ $laporanList->total() }} Total Laporan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="table-dense">
                    <thead>
                        <tr>
                            <th class="w-12 text-center">No</th>
                            <th>No. Laporan</th>
                            <th>Tanggal</th>
                            <th>Mesin & Lokasi</th>
                            <th>Gejala Kerusakan</th>
                            <th>Suku Cadang</th>
                            <th class="text-center">Status</th>
                            <th>Foto</th>
                            @if(auth()->user()->isAdminPusat())
                                <th class="text-right w-28">Tindak Lanjut</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanList as $index => $lap)
                            <tr>
                                <td class="text-center font-mono text-xs text-[#6B665A]">{{ $laporanList->firstItem() + $index }}</td>
                                <td class="font-mono text-xs font-bold text-[#0B6B73]">{{ $lap->nomor_laporan }}</td>
                                <td class="font-mono text-xs text-[#6B665A]">{{ $lap->tanggal_lapor->format('d/m/Y') }}</td>
                                <td class="text-xs text-[#1C1A15]">
                                    <div class="font-medium">{{ $lap->mesin->nama_mesin }}</div>
                                    <div class="text-[11px] font-mono text-[#6B665A]">{{ $lap->lokasi->kode_lokasi }} - {{ $lap->lokasi->nama_lokasi }}</div>
                                </td>
                                <td class="text-xs text-[#1C1A15] max-w-xs">
                                    <div>{{ $lap->gejala }}</div>
                                    <div class="text-[11px] text-[#6B665A] truncate">{{ $lap->kronologi }}</div>
                                </td>
                                <td class="text-xs text-[#1C1A15]">
                                    @if($lap->detailKerusakan->count() > 0)
                                        @foreach($lap->detailKerusakan as $dk)
                                            <div class="font-mono text-[11px]">{{ $dk->sukuCadang->nama_bahan }} ({{ (int)$dk->jumlah }} pcs)</div>
                                        @endforeach
                                    @else
                                        <span class="text-[#6B665A] text-[11px]">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <x-status-chip :status="ucfirst($lap->status)" />
                                </td>
                                <td class="text-xs">
                                    @if($lap->foto_kerusakan)
                                        <a href="{{ asset('storage/' . $lap->foto_kerusakan) }}" target="_blank" class="text-[#0B6B73] underline text-[11px]">Lihat Foto</a>
                                    @else
                                        <span class="text-[#6B665A] text-[11px]">-</span>
                                    @endif
                                </td>
                                @if(auth()->user()->isAdminPusat())
                                    <td class="text-right">
                                        @if($lap->status !== 'selesai')
                                            <button type="button" 
                                                    @click="activeLaporan = {{ json_encode($lap) }}; openStatusModal = true" 
                                                    class="btn btn-secondary text-[11px] py-1 px-2">
                                                Update Status
                                            </button>
                                        @else
                                            <span class="text-[11px] text-ok font-mono font-medium">Selesai</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isAdminPusat() ? '9' : '8' }}" class="text-center py-8 text-xs text-[#6B665A]">
                                    Belum ada data laporan kerusakan mesin.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($laporanList->hasPages())
                <div class="p-3 border-t border-[#E2DED2] bg-[#FAFAFA]">
                    {{ $laporanList->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Update Status Perbaikan (Admin Pusat) -->
        <div x-show="openStatusModal" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="panel bg-white w-full max-w-md p-5" @click.outside="openStatusModal = false">
                <div class="flex justify-between items-center pb-3 border-b border-[#E2DED2] mb-4">
                    <h3 class="text-sm font-semibold text-[#1C1A15]">Pemeriksaan & Tindak Lanjut Perbaikan</h3>
                    <button @click="openStatusModal = false" class="text-[#6B665A] hover:text-danger text-lg font-bold">&times;</button>
                </div>

                <form method="POST" :action="activeLaporan ? '{{ url('mesin/kerusakan') }}/' + activeLaporan.id + '/status' : '#'" class="space-y-3.5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Status Progres <span class="text-danger">*</span></label>
                        <select name="status" required class="select-text text-xs">
                            <option value="diperiksa">Sedang Diperiksa Teknisi</option>
                            <option value="diperbaiki">Dalam Tahap Perbaikan</option>
                            <option value="selesai">Selesai (Potong Stok Suku Cadang & Aktifkan Mesin)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Nama Teknisi / Vendor Service</label>
                        <input type="text" name="teknisi" placeholder="Contoh: Budi Santoso (Teknisi Pusat)" class="input-text text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Tindakan Perbaikan / Komponen Ditukar</label>
                        <textarea name="tindakan" rows="2" placeholder="Jelaskan tindakan servis yang dilakukan..." class="input-text text-xs"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-[#E2DED2]">
                        <button type="button" @click="openStatusModal = false" class="btn btn-secondary text-xs">Batal</button>
                        <button type="submit" class="btn btn-primary text-xs">Simpan Progres</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
