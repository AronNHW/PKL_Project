<x-app-layout>
    <x-slot name="title">Prediksi Kebutuhan & Reorder Point (ROP)</x-slot>

    <!-- Top Action Bar -->
    <div class="panel p-3.5 mb-4 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <form method="GET" action="{{ route('prediksi.index') }}" class="flex items-center gap-2">
            <span class="text-xs text-[#6B665A] font-medium">Periode:</span>
            <select name="bulan" class="select-text text-xs w-32" onchange="this.form.submit()">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endfor
            </select>

            <select name="tahun" class="select-text text-xs w-24" onchange="this.form.submit()">
                <option value="2026" {{ $tahun == 2026 ? 'selected' : '' }}>2026</option>
                <option value="2027" {{ $tahun == 2027 ? 'selected' : '' }}>2027</option>
            </select>
        </form>

        <form method="POST" action="{{ route('prediksi.generate') }}" class="flex items-center gap-2">
            @csrf
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <div class="flex items-center gap-1.5 text-xs text-[#6B665A]">
                <span>Konstanta &alpha;:</span>
                <input type="number" step="0.05" min="0.05" max="0.95" name="alpha" value="0.20" class="input-text text-xs font-mono w-16 py-1">
            </div>
            <button type="submit" class="btn btn-primary text-xs">
                Jalankan SES & Hitung ROP
            </button>
        </form>
    </div>

    <!-- Recommendations Grid (Saran Pembelian & Saran Distribusi) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Saran Pembelian Pusat -->
        <div class="panel">
            <div class="p-3.5 border-b border-[#E2DED2] bg-[#FAFAFA] flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0B6B73]"></span>
                    <h2 class="text-xs font-semibold text-[#1C1A15]">Rekomendasi Pengadaan / Pembelian (Gudang Pusat)</h2>
                </div>
                <span class="text-[11px] text-[#6B665A] font-mono">{{ $saranPembelian->count() }} item</span>
            </div>

            <div class="overflow-x-auto">
                <table class="table-dense">
                    <thead>
                        <tr>
                            <th>Bahan</th>
                            <th class="text-right">Saran Beli</th>
                            <th class="text-right">Estimasi Biaya</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($saranPembelian as $sp)
                            <tr>
                                <td class="font-medium text-xs text-[#1C1A15]">
                                    <div>{{ $sp->bahan->nama_bahan }}</div>
                                    <div class="text-[10.5px] font-mono text-[#6B665A]">{{ $sp->bahan->kode_bahan }}</div>
                                </td>
                                <td class="text-right font-mono text-xs font-bold text-[#0B6B73]">
                                    {{ number_format($sp->jumlah_saran, 0, ',', '.') }} {{ $sp->bahan->satuan }}
                                </td>
                                <td class="text-right font-mono text-xs text-[#1C1A15]">
                                    Rp {{ number_format($sp->estimasi_biaya, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-accent">Draft PO</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-xs text-[#6B665A]">
                                    Stok pusat mencukupi atau klik tombol "Jalankan SES & Hitung ROP" untuk menganalisis.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Saran Distribusi ke Cabang -->
        <div class="panel">
            <div class="p-3.5 border-b border-[#E2DED2] bg-[#FAFAFA] flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-warn"></span>
                    <h2 class="text-xs font-semibold text-[#1C1A15]">Rekomendasi Drop Distribusi (Pusat &rarr; Cabang)</h2>
                </div>
                <span class="text-[11px] text-[#6B665A] font-mono">{{ $saranDistribusi->count() }} rute</span>
            </div>

            <div class="overflow-x-auto">
                <table class="table-dense">
                    <thead>
                        <tr>
                            <th>Cabang Tujuan</th>
                            <th>Bahan</th>
                            <th class="text-right">Saran Kirim</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($saranDistribusi as $sd)
                            <tr>
                                <td class="text-xs font-medium text-[#1C1A15]">
                                    <span class="px-1.5 py-0.5 rounded text-[10.5px] font-mono bg-blue-100 text-blue-800">
                                        {{ $sd->lokasiTujuan->kode_lokasi }}
                                    </span>
                                    <span class="ml-1">{{ $sd->lokasiTujuan->nama_lokasi }}</span>
                                </td>
                                <td class="font-medium text-xs text-[#1C1A15]">
                                    {{ $sd->bahan->nama_bahan }}
                                </td>
                                <td class="text-right font-mono text-xs font-bold text-warn">
                                    {{ number_format($sd->jumlah_saran, 0, ',', '.') }} {{ $sd->bahan->satuan }}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-warn">Perlu SJ</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-xs text-[#6B665A]">
                                    Tidak ada cabang yang berada di bawah titik pemesanan ulang (ROP).
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Hasil Prediksi SES Table -->
    <div class="panel overflow-hidden">
        <div class="p-3.5 border-b border-[#E2DED2] bg-[#FAFAFA] flex justify-between items-center">
            <h2 class="text-xs font-semibold text-[#1C1A15]">Hasil Peramalan Single Exponential Smoothing (SES) Periode {{ $bulan }}/{{ $tahun }}</h2>
            <span class="text-[11px] text-[#6B665A]">Metode Peramalan Kuantitatif</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table-dense">
                <thead>
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>Kode Bahan</th>
                        <th>Nama Bahan Percetakan</th>
                        <th>Lokasi</th>
                        <th class="text-right">Hasil Prediksi Pemakaian</th>
                        <th>Metode Perhitungan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prediksiList as $index => $pred)
                        <tr>
                            <td class="text-center font-mono text-xs text-[#6B665A]">{{ $prediksiList->firstItem() + $index }}</td>
                            <td class="font-mono text-xs font-semibold text-[#0B6B73]">{{ $pred->bahan->kode_bahan }}</td>
                            <td class="font-medium text-xs text-[#1C1A15]">{{ $pred->bahan->nama_bahan }}</td>
                            <td class="text-xs text-[#1C1A15]">
                                {{ $pred->lokasi->kode_lokasi }} - {{ $pred->lokasi->nama_lokasi }}
                            </td>
                            <td class="text-right font-mono text-xs font-bold text-[#1C1A15]">
                                {{ number_format($pred->hasil_prediksi, 2, ',', '.') }} {{ $pred->bahan->satuan }}
                            </td>
                            <td class="text-xs font-mono text-[#6B665A]">
                                {{ $pred->metode }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-xs text-[#6B665A]">
                                Belum ada komputasi prediksi untuk periode ini. Klik "Jalankan SES & Hitung ROP" di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($prediksiList->hasPages())
            <div class="p-3 border-t border-[#E2DED2] bg-[#FAFAFA]">
                {{ $prediksiList->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
