<x-app-layout>
    <x-slot name="title">Permintaan Bahan Cabang</x-slot>

    <!-- Top Action Bar -->
    <div class="panel p-3.5 mb-4" x-data="{ openMinta: false, items: [{ id_bahan: '', jumlah_minta: 1 }] }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('distribusi.permintaan') }}" class="flex flex-wrap items-center gap-2 flex-1">
                <div class="w-48">
                    <select name="status" class="select-text text-xs" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Sedang Diproses/Kirim</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai Diterima</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                @if(request()->filled('status'))
                    <a href="{{ route('distribusi.permintaan') }}" class="text-xs text-[#6B665A] hover:text-danger ml-1">Reset</a>
                @endif
            </form>

            @if(auth()->user()->isAdminCabang())
                <button type="button" @click="openMinta = true" class="btn btn-primary text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Ajukan Permintaan Bahan</span>
                </button>
            @endif
        </div>

        <!-- Modal Ajukan Permintaan -->
        <div x-show="openMinta" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="panel bg-white w-full max-w-xl max-h-[90vh] overflow-y-auto p-5" @click.outside="openMinta = false">
                <div class="flex justify-between items-center pb-3 border-b border-[#E2DED2] mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-[#1C1A15]">Formulir Permintaan Stok Bahan ke Pusat</h3>
                        <p class="text-xs text-[#6B665A] mt-0.5">Permintaan akan diteruskan ke gudang pusat untuk diterbitkan surat jalan.</p>
                    </div>
                    <button @click="openMinta = false" class="text-[#6B665A] hover:text-danger text-lg font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('distribusi.permintaan.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-semibold text-[#1C1A15]">Bahan yang Dibutuhkan</span>
                            <button type="button" @click="items.push({ id_bahan: '', jumlah_minta: 1 })" class="btn btn-secondary text-xs py-0.5 px-2">
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
                                        <input type="number" step="any" min="0.01" :name="'items['+idx+'][jumlah_minta]'" x-model="item.jumlah_minta" required placeholder="Jumlah" class="input-text text-xs font-mono font-semibold">
                                    </div>
                                    <div class="col-span-1 text-center">
                                        <button type="button" x-show="items.length > 1" @click="items.splice(idx, 1)" class="text-danger font-bold text-sm hover:opacity-75">&times;</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Catatan Kebutuhan (Keperluan SPK / Proyek)</label>
                        <textarea name="catatan" rows="2" placeholder="Jelaskan perkiraan kebutuhan atau deadline cetak..." class="input-text text-xs"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-[#E2DED2]">
                        <button type="button" @click="openMinta = false" class="btn btn-secondary text-xs">Batal</button>
                        <button type="submit" class="btn btn-primary text-xs">Kirim Permintaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Table Dense Permintaan -->
    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-dense">
                <thead>
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>No. Permintaan</th>
                        <th>Tanggal</th>
                        <th>Cabang Pemohon</th>
                        <th>Rincian Bahan</th>
                        <th class="text-center">Status</th>
                        <th>Pemohon</th>
                        @if(auth()->user()->isAdminPusat())
                            <th class="text-right w-36">Aksi Pusat</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($permintaanList as $index => $p)
                        <tr>
                            <td class="text-center font-mono text-xs text-[#6B665A]">{{ $permintaanList->firstItem() + $index }}</td>
                            <td class="font-mono text-xs font-bold text-[#0B6B73]">{{ $p->nomor_permintaan }}</td>
                            <td class="font-mono text-xs text-[#6B665A]">{{ $p->tanggal_permintaan->format('d/m/Y') }}</td>
                            <td class="text-xs font-medium text-[#1C1A15]">
                                <span class="px-1.5 py-0.5 rounded text-[10.5px] font-mono bg-amber-100 text-amber-800">
                                    {{ $p->lokasiPemohon->kode_lokasi }}
                                </span>
                                <span class="ml-1">{{ $p->lokasiPemohon->nama_lokasi }}</span>
                            </td>
                            <td class="text-xs text-[#1C1A15]">
                                <ul class="list-disc list-inside space-y-0.5">
                                    @foreach($p->details as $d)
                                        <li>{{ $d->bahan->nama_bahan }}: <strong class="font-mono">{{ number_format($d->jumlah_minta, 0, ',', '.') }} {{ $d->bahan->satuan }}</strong></li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center">
                                <x-status-chip :status="ucfirst($p->status)" />
                            </td>
                            <td class="text-xs text-[#6B665A]">{{ $p->userPemohon->name ?? '-' }}</td>
                            @if(auth()->user()->isAdminPusat())
                                <td class="text-right">
                                    @if($p->status === 'menunggu')
                                        <div class="inline-flex gap-1.5">
                                            <form method="POST" action="{{ route('distribusi.permintaan.status', $p) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="disetujui">
                                                <button type="submit" class="text-xs text-ok hover:underline font-semibold">Setujui</button>
                                            </form>
                                            <span class="text-[#E2DED2]">|</span>
                                            <form method="POST" action="{{ route('distribusi.permintaan.status', $p) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="ditolak">
                                                <button type="submit" class="text-xs text-danger hover:underline">Tolak</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-[11px] text-[#6B665A] font-mono">Telah diproses</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdminPusat() ? '8' : '7' }}" class="text-center py-8 text-xs text-[#6B665A]">
                                Belum ada pengajuan permintaan bahan dari cabang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($permintaanList->hasPages())
            <div class="p-3 border-t border-[#E2DED2] bg-[#FAFAFA]">
                {{ $permintaanList->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
