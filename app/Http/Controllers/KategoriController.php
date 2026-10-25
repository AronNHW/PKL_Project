<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoriList = Kategori::withCount('bahan')->orderBy('nama_kategori')->get();
        return view('kategori.index', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_kategori' => 'required|string|max:20|unique:kategori,kode_kategori',
            'nama_kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        Kategori::create([
            'kode_kategori' => strtoupper(trim($validated['kode_kategori'])),
            'nama_kategori' => trim($validated['nama_kategori']),
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'kode_kategori' => 'required|string|max:20|unique:kategori,kode_kategori,' . $kategori->id,
            'nama_kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update([
            'kode_kategori' => strtoupper(trim($validated['kode_kategori'])),
            'nama_kategori' => trim($validated['nama_kategori']),
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->bahan()->exists()) {
            return back()->with('error', 'Kategori ini tidak dapat dihapus karena masih digunakan oleh beberapa bahan.');
        }

        $kategori->delete();
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
