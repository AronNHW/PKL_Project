<x-app-layout>
    <x-slot name="title">Pusat Laporan & Rekapitulasi</x-slot>

    <!-- Filter & Tipe Laporan -->
    <div class="panel p-4 mb-4 print:hidden">
        <form method="GET" action="{{ route('laporan.index') }}" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-[#1C1A15] mb-1">Jenis Laporan</label>
                    <select name="tipe" class="select-text text-xs" onchange="this.form.submit()">
                        <option value="stok" {{ $tipeLaporan === 'stok' ? 'selected' : '' }}>Posisi Stok per Lokasi</option>
                        <option value="mutasi" {{ $tipeLaporan === 'mutasi' ? 'selected' : '' }}>Buku Mutasi Keluar-Masuk</option>
                        <option value="distribusi" {{ $tipeLaporan === 'distribusi' ? 'selected' : '' }}>Distribusi & Surat Jalan</option>
                        <option value="pemakaian" {{ $tipeLaporan === 'pemakaian' ? 'selected' : '' }}>Pemakaian Produksi (SPK)</option>
                        <option value="mesin" {{ $tipeLaporan === 'mesin' ? 'selected' : '' }}>Kerusakan & Servis Mesin</option>
                    </select>
                </div>

                @if(!$isCabang)
                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Gudang / Cabang</label>
                        <select name="lokasi_id" class="select-text text-xs" onchange="this.form.submit()">
                            <option value="">Semua Lokasi</option>
                            @foreach($lokasiList as $lok)
                                <option value="{{ $lok->id }}" {{ $lokasiId == $lok->id ? 'selected' : '' }}>
                                    {{ $lok->nama_lokasi }} ({{ $lok->kode_lokasi }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if($tipeLaporan !== 'stok')
                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Dari Tanggal</label>
                        <input type="date" name="tgl_mulai" value="{{ $tglMulai }}" class="input-text text-xs font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#1C1A15] mb-1">Sampai Tanggal</label>
                        <input type="date" name="tgl_selesai" value="{{ $tglSelesai }}" class="input-text text-xs font-mono">
                    </div>
                @endif
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-[#E2DED2]">
                <button type="submit" class="btn btn-secondary text-xs">Terapkan Filter</button>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.print()" class="btn btn-secondary text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Cetak Laporan</span>
                    </button>

                    <a href="{{ route('laporan.export-csv', ['tipe' => $tipeLaporan]) }}" class="btn btn-primary text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Ekspor File CSV</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Printable Report Panel -->
    <div class="panel p-5 bg-white">
        <!-- Header Dokumen Laporan -->
        <div class="pb-3 mb-4 border-b-2 border-[#1C1A15] flex justify-between items-start">
            <div>
                <h1 class="text-base font-bold text-[#1C1A15]">
                    Laporan {{ ucfirst($tipeLaporan) }} Bahan Percetakan
                </h1>
                <p class="text-xs text-[#6B665A] mt-0.5 font-mono">
                    Sistem Inventori Gudang &bull; Dicetak: {{ now()->format('d/m/Y H:i') }} WIB
                </p>
            </div>
            <div class="text-right text-xs">
                <span class="font-bold text-[#0B6B73] font-mono">SISTEM INVENTORI GUDANG</span>
                <div class="text-[#6B665A]">Gudang Pusat Surabaya & 5 Unit Cabang</div>
            </div>
        </div>

        <!-- Render Tabel Berdasarkan Tipe -->
        <div class="overflow-x-auto">
            @if($tipeLaporan === 'stok')
                <table class="table-dense">
                    <thead>
                        <tr>
                            <th class="w-10 text-center">No</th>
                            <th>Kode</th>
                            <th>Nama Bahan Percetakan</th>
                            <th>Kategori</th>
                            <th>Lokasi Gudang</th>
                            <th class="text-right">Stok Fisik</th>
                            <th class="text-right">Batas Min</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataLaporan as $index => $item)
                            <tr>
                                <td class="text-center font-mono text-xs text-[#6B665A]">{{ $index + 1 }}</td>
                                <td class="font-mono text-xs font-semibold">{{ $item->bahan->kode_bahan }}</td>
                                <td class="font-medium text-xs">{{ $item->bahan->nama_bahan }}</td>
                                <td class="text-xs text-[#6B665A]">{{ $item->bahan->kategori->nama_kategori ?? '-' }}</td>
                                <td class="text-xs font-mono">{{ $item->lokasi->kode_lokasi }} ({{ $item->lokasi->nama_lokasi }})</td>
                                <td class="text-right font-mono text-xs font-bold">{{ number_format($item->jumlah, 0, ',', '.') }} {{ $item->bahan->satuan }}</td>
                                <td class="text-right font-mono text-xs text-[#6B665A]">{{ number_format($item->stok_minimum, 0, ',', '.') }} {{ $item->bahan->satuan }}</td>
                                <td class="text-center"><x-status-chip :status="$item->status_stok" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-6 text-xs text-[#6B665A]">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>

            @elseif($tipeLaporan === 'mutasi')
                <table class="table-dense">
                    <thead>
                        <tr>
                            <th>Waktu (WIB)</th>
                            <th>Lokasi</th>
                            <th>Bahan</th>
                            <th class="text-center">Jenis</th>
                            <th class="text-right">Sebelum</th>
                            <th class="text-right">Mutasi</th>
                            <th class="text-right">Sesudah</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataLaporan as $m)
                            <tr>
                                <td class="font-mono text-xs text-[#6B665A]">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                <td class="font-mono text-xs font-medium">{{ $m->lokasi->kode_lokasi }}</td>
                                <td class="font-medium text-xs">{{ $m->bahan->nama_bahan }}</td>
                                <td class="text-center"><x-status-chip :status="$m->jenis_label" :type="$m->jenis_badge_class" /></td>
                                <td class="text-right font-mono text-xs text-[#6B665A]">{{ number_format($m->stok_sebelum, 0, ',', '.') }}</td>
                                <td class="text-right font-mono text-xs font-bold {{ in_array($m->jenis, ['masuk', 'terima']) ? 'text-ok' : 'text-danger' }}">
                                    {{ in_array($m->jenis, ['masuk', 'terima']) ? '+' : '-' }}{{ number_format($m->jumlah, 0, ',', '.') }} {{ $m->bahan->satuan }}
                                </td>
                                <td class="text-right font-mono text-xs font-semibold">{{ number_format($m->stok_sesudah, 0, ',', '.') }}</td>
                                <td class="text-xs text-[#6B665A]">{{ $m->keterangan }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-6 text-xs text-[#6B665A]">Tidak ada mutasi pada rentang tanggal ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>

            @elseif($tipeLaporan === 'distribusi')
                <table class="table-dense">
                    <thead>
                        <tr>
                            <th>No. Surat Jalan</th>
                            <th>Tgl Kirim</th>
                            <th>Rute</th>
                            <th>Muatan & Realisasi Terima</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataLaporan as $d)
                            <tr>
                                <td class="font-mono text-xs font-bold">{{ $d->nomor_surat_jalan }}</td>
                                <td class="font-mono text-xs text-[#6B665A]">{{ $d->tanggal_kirim->format('d/m/Y') }}</td>
                                <td class="text-xs font-medium">{{ $d->lokasiAsal->kode_lokasi }} &rarr; {{ $d->lokasiTujuan->kode_lokasi }}</td>
                                <td class="text-xs">
                                    @foreach($d->details as $item)
                                        <div>{{ $item->bahan->nama_bahan }}: Kirim {{ (int)$item->jumlah_kirim }}, Terima {{ (int)$item->jumlah_terima }} (Selisih: {{ (int)$item->selisih }})</div>
                                    @endforeach
                                </td>
                                <td class="text-center"><x-status-chip :status="ucfirst($d->status)" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-6 text-xs text-[#6B665A]">Tidak ada surat jalan distribusi.</td></tr>
                        @endforelse
                    </tbody>
                </table>

            @elseif($tipeLaporan === 'pemakaian')
                <table class="table-dense">
                    <thead>
                        <tr>
                            <th>No. Pemakaian</th>
                            <th>Tanggal</th>
                            <th>Lokasi</th>
                            <th>No. SPK</th>
                            <th>Keperluan</th>
                            <th>Bahan Terpakai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataLaporan as $pk)
                            <tr>
                                <td class="font-mono text-xs font-bold">{{ $pk->nomor_pemakaian }}</td>
                                <td class="font-mono text-xs text-[#6B665A]">{{ $pk->tanggal_pemakaian->format('d/m/Y') }}</td>
                                <td class="font-mono text-xs">{{ $pk->lokasi->kode_lokasi }}</td>
                                <td class="font-mono text-xs">{{ $pk->nomor_spk ?? '-' }}</td>
                                <td class="text-xs">{{ $pk->keperluan }}</td>
                                <td class="text-xs">
                                    @foreach($pk->details as $item)
                                        <div>{{ $item->bahan->nama_bahan }}: <span class="font-mono font-bold text-danger">-{{ (int)$item->jumlah }} {{ $item->bahan->satuan }}</span></div>
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-6 text-xs text-[#6B665A]">Tidak ada catatan pemakaian pada rentang waktu ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>

            @elseif($tipeLaporan === 'mesin')
                <table class="table-dense">
                    <thead>
                        <tr>
                            <th>No. Laporan</th>
                            <th>Tanggal</th>
                            <th>Mesin</th>
                            <th>Lokasi</th>
                            <th>Gejala Kerusakan</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataLaporan as $lk)
                            <tr>
                                <td class="font-mono text-xs font-bold">{{ $lk->nomor_laporan }}</td>
                                <td class="font-mono text-xs text-[#6B665A]">{{ $lk->tanggal_lapor->format('d/m/Y') }}</td>
                                <td class="text-xs font-semibold">{{ $lk->mesin->nama_mesin }}</td>
                                <td class="text-xs font-mono">{{ $lk->lokasi->kode_lokasi }}</td>
                                <td class="text-xs">{{ $lk->gejala }}</td>
                                <td class="text-center"><x-status-chip :status="ucfirst($lk->status)" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-6 text-xs text-[#6B665A]">Tidak ada data kerusakan mesin.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-app-layout>
