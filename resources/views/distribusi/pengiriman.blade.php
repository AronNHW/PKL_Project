<x-app-layout>
    <x-slot name="title">Surat Jalan & Distribusi Antar Cabang</x-slot>

    <!-- Top Action Bar -->
    <div class="panel p-3.5 mb-4" x-data="{ openKirim: false, items: [{ id_bahan: '', jumlah_kirim: 1 }] }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('distribusi.pengiriman') }}" class="flex flex-wrap items-center gap-2 flex-1">
                <div class="w-48">
                    <select name="status" class="select-text text-xs" onchange="this.form.submit()">
                        <option value="">Semua Status Pengiriman</option>
                        <option value="dikirim" {{ request('status') == 'dikirim' ? 'selected' : '' }}>Dikirim (Dalam Transit)</option>
                        <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima (Sesuai)</option>
                        <option value="selisih" {{ request('status') == 'selisih' ? 'selected' : '' }}>Ada Selisih Fisik</option>
                    </select>
                </div>

                @if(request()->filled('status'))
                    <a href="{{ route('distribusi.pengiriman') }}" class="text-xs text-[#6B665A] hover:text-danger ml-1">Reset</a>
                @endif
            </form>

            @if(auth()->user()->isAdminPusat())
                <button type="button" @click="openKirim = true" class="btn btn-primary text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Terbitkan Surat Jalan (Kirim)</span>
                </button>
            @endif
        </div>

        <!-- Modal Terbitkan Surat Jalan -->
        <div x-show="openKirim" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="panel bg-white w-full max-w-xl max-h-[90vh] overflow-y-auto p-5" @click.outside="openKirim = false">
                <div class="flex justify-between items-center pb-3 border-b border-[#E2DED2] mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-[#1C1A15]">Terbitkan Surat Jalan Distribusi (Pusat ke Cabang)</h3>
                        <p class="text-xs text-[#6B665A] mt-0.5">Stok gudang pusat akan langsung dipotong melalui StokService.</p>
                    </div>
                    <button @click="openKirim = false" class="text-[#6B665A] hover:text-danger text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('distribusi.pengiriman.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-[#1C1A15] mb-1">Cabang Tujuan <span class="text-danger">*</span></label>
                            <select name="id_lokasi_tujuan" required class="select-text text-xs">
                                <option value="">-- Pilih Cabang Tujuan --</option>
                                @foreach($lokasiCabangList as $cabang)
                                    <option value="{{ $cabang->id }}">{{ $cabang->nama_lokasi }} ({{ $cabang->kode_lokasi }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#1C1A15] mb-1">Tanggal Kirim <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_kirim" value="{{ date('Y-m-d') }}" required class="input-text text-xs font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-[#1C1A15] mb-1">Ekspedisi / Nama Driver / Sopir</label>
                            <input type="text" name="ekspedisi_sopir" placeholder="Contoh: Mobil Box L300 (Pak Slamet)" class="input-text text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[#1C1A15] mb-1">Catatan Pengiriman</label>
                            <input type="text" name="catatan_kirim" placeholder="Keterangan muatan..." class="input-text text-xs">
                        </div>
                    </div>

                    <!-- Items Kirim -->
                    <div class="pt-2 border-t border-[#E2DED2]">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-semibold text-[#1C1A15]">Daftar Bahan yang Dikirim</span>
                            <button type="button" @click="items.push({ id_bahan: '', jumlah_kirim: 1 })" class="btn btn-secondary text-xs py-0.5 px-2">
                                + Tambah Item
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
                                        <input type="number" step="any" min="0.01" :name="'items['+idx+'][jumlah_kirim]'" x-model="item.jumlah_kirim" required placeholder="Jumlah" class="input-text text-xs font-mono font-semibold">
                                    </div>
                                    <div class="col-span-1 text-center">
                                        <button type="button" x-show="items.length > 1" @click="items.splice(idx, 1)" class="text-danger font-bold text-sm hover:opacity-75">&times;</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-[#E2DED2]">
                        <button type="button" @click="openKirim = false" class="btn btn-secondary text-xs">Batal</button>
                        <button type="submit" class="btn btn-primary text-xs">Terbitkan Surat Jalan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Table Dense Pengiriman -->
    <div class="panel overflow-hidden" x-data="{ openKonfirmasi: false, activePengiriman: null }">
        <div class="overflow-x-auto">
            <table class="table-dense">
                <thead>
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>No. Surat Jalan</th>
                        <th>Tanggal Kirim</th>
                        <th>Rute Distribusi</th>
                        <th>Muatan Item</th>
                        <th>Ekspedisi / Sopir</th>
                        <th class="text-center">Status</th>
                        <th>Tgl Terima</th>
                        <th class="text-right w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengirimanList as $index => $sj)
                        <tr>
                            <td class="text-center font-mono text-xs text-[#6B665A]">{{ $pengirimanList->firstItem() + $index }}</td>
                            <td class="font-mono text-xs font-bold text-[#0B6B73]">{{ $sj->nomor_surat_jalan }}</td>
                            <td class="font-mono text-xs text-[#6B665A]">{{ $sj->tanggal_kirim->format('d/m/Y') }}</td>
                            <td class="text-xs text-[#1C1A15]">
                                <span class="font-medium">{{ $sj->lokasiAsal->kode_lokasi }}</span>
                                <span class="text-[#6B665A] mx-1">&rarr;</span>
                                <span class="font-medium text-[#0B6B73]">{{ $sj->lokasiTujuan->kode_lokasi }} ({{ $sj->lokasiTujuan->nama_lokasi }})</span>
                            </td>
                            <td class="text-xs text-[#1C1A15]">
                                <ul class="space-y-0.5">
                                    @foreach($sj->details as $d)
                                        <li>
                                            {{ $d->bahan->nama_bahan }}: 
                                            <span class="font-mono font-medium">Kirim: {{ number_format($d->jumlah_kirim, 0, ',', '.') }}</span>
                                            @if($sj->status !== 'dikirim')
                                                | <span class="font-mono {{ $d->selisih != 0 ? 'text-danger font-bold' : 'text-ok' }}">Terima: {{ number_format($d->jumlah_terima, 0, ',', '.') }}</span>
                                                @if($d->selisih != 0)
                                                    <span class="text-[10px] text-danger font-mono">({{ $d->selisih > 0 ? '+' : '' }}{{ $d->selisih }})</span>
                                                @endif
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-xs text-[#6B665A]">{{ $sj->ekspedisi_sopir ?? '-' }}</td>
                            <td class="text-center">
                                <x-status-chip :status="ucfirst($sj->status)" />
                            </td>
                            <td class="font-mono text-xs text-[#6B665A]">
                                {{ $sj->tanggal_terima ? $sj->tanggal_terima->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-right">
                                @if($sj->status === 'dikirim' && (auth()->user()->isAdminPusat() || (auth()->user()->isAdminCabang() && (int) auth()->user()->id_lokasi === (int) $sj->id_lokasi_tujuan)))
                                    <button type="button" 
                                            @click="activePengiriman = {{ json_encode($sj->load('details.bahan')) }}; openKonfirmasi = true"
                                            class="btn btn-primary text-xs py-1 px-2.5">
                                        Konfirmasi
                                    </button>
                                @else
                                    <span class="text-xs text-[#6B665A] font-mono">Tervalidasi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-xs text-[#6B665A]">Belum ada data surat jalan pengiriman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pengirimanList->hasPages())
            <div class="p-3 border-t border-[#E2DED2] bg-[#FAFAFA]">
                {{ $pengirimanList->links() }}
            </div>
        @endif

        <!-- Modal Rekonsiliasi & Penerimaan Cabang -->
        <div x-show="openKonfirmasi" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="panel bg-white w-full max-w-xl max-h-[90vh] overflow-y-auto p-5" @click.outside="openKonfirmasi = false">
                <div class="flex justify-between items-center pb-3 border-b border-[#E2DED2] mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-[#1C1A15]">Konfirmasi Penerimaan & Rekonsiliasi Fisik</h3>
                        <p class="text-xs text-[#6B665A] mt-0.5" x-text="activePengiriman ? 'No. Surat Jalan: ' + activePengiriman.nomor_surat_jalan : ''"></p>
                    </div>
                    <button @click="openKonfirmasi = false" class="text-[#6B665A] hover:text-danger text-lg font-bold">&times;</button>
                </div>

                <form method="POST" :action="activePengiriman ? '{{ url('distribusi/pengiriman') }}/' + activePengiriman.id + '/terima' : '#'" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Tanggal Diterima Nyata di Cabang <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_terima" value="{{ date('Y-m-d') }}" required class="input-text text-xs font-mono">
                    </div>

                    <div class="pt-2 border-t border-[#E2DED2]">
                        <h4 class="text-xs font-semibold text-[#1C1A15] mb-2">Hitung & Rekonsiliasi Fisik per Item</h4>

                        <div class="space-y-3">
                            <template x-for="(detail, index) in (activePengiriman ? activePengiriman.details : [])" :key="detail.id">
                                <div class="p-3 bg-[#F1EEE6] border border-[#E2DED2] rounded text-xs space-y-2">
                                    <div class="flex justify-between items-baseline">
                                        <span class="font-semibold text-[#1C1A15]" x-text="detail.bahan.nama_bahan"></span>
                                        <span class="font-mono text-xs text-[#6B665A]" x-text="'Dikirim Pusat: ' + detail.jumlah_kirim + ' ' + detail.bahan.satuan"></span>
                                    </div>

                                    <input type="hidden" :name="'items['+index+'][detail_id]'" :value="detail.id">

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[11px] font-medium text-[#1C1A15] mb-0.5">Jumlah Fisik Diterima <span class="text-danger">*</span></label>
                                            <input type="number" step="any" min="0" :name="'items['+index+'][jumlah_terima]'" :value="detail.jumlah_kirim" required class="input-text text-xs font-mono font-bold">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-medium text-[#1C1A15] mb-0.5">Catatan Selisih (jika ada)</label>
                                            <input type="text" :name="'items['+index+'][catatan_selisih]'" placeholder="Misal: 1 roll basah/rusak di jalan" class="input-text text-xs">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Catatan Penerima</label>
                        <textarea name="catatan_terima" rows="2" placeholder="Kondisi barang saat dibongkar dari ekspedisi..." class="input-text text-xs"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-[#E2DED2]">
                        <button type="button" @click="openKonfirmasi = false" class="btn btn-secondary text-xs">Batal</button>
                        <button type="submit" class="btn btn-primary text-xs">Konfirmasi & Masukkan ke Stok Cabang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
