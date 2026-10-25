<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function index()
    {
        $lokasiList = Lokasi::withCount(['users', 'stokLokasi', 'mesin'])->orderBy('tipe')->orderBy('nama_lokasi')->get();
        return view('lokasi.index', compact('lokasiList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_lokasi' => 'required|string|max:20|unique:lokasi,kode_lokasi',
            'nama_lokasi' => 'required|string|max:100',
            'tipe' => 'required|in:pusat,cabang',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:30',
            'penanggung_jawab' => 'nullable|string|max:100',
        ]);

        Lokasi::create([
            'kode_lokasi' => strtoupper(trim($validated['kode_lokasi'])),
            'nama_lokasi' => trim($validated['nama_lokasi']),
            'tipe' => $validated['tipe'],
            'alamat' => $validated['alamat'] ?? null,
            'telepon' => $validated['telepon'] ?? null,
            'penanggung_jawab' => $validated['penanggung_jawab'] ?? null,
            'is_aktif' => true,
        ]);

        return redirect()->route('lokasi.index')->with('success', 'Lokasi baru berhasil ditambahkan.');
    }

    public function update(Request $request, Lokasi $lokasi)
    {
        $validated = $request->validate([
            'kode_lokasi' => 'required|string|max:20|unique:lokasi,kode_lokasi,' . $lokasi->id,
            'nama_lokasi' => 'required|string|max:100',
            'tipe' => 'required|in:pusat,cabang',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:30',
            'penanggung_jawab' => 'nullable|string|max:100',
            'is_aktif' => 'required|boolean',
        ]);

        $lokasi->update([
            'kode_lokasi' => strtoupper(trim($validated['kode_lokasi'])),
            'nama_lokasi' => trim($validated['nama_lokasi']),
            'tipe' => $validated['tipe'],
            'alamat' => $validated['alamat'] ?? null,
            'telepon' => $validated['telepon'] ?? null,
            'penanggung_jawab' => $validated['penanggung_jawab'] ?? null,
            'is_aktif' => $validated['is_aktif'],
        ]);

        return redirect()->route('lokasi.index')->with('success', 'Data lokasi berhasil diperbarui.');
    }
}
