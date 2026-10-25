<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $supplierList = Supplier::orderBy('nama_supplier')->get();
        return view('supplier.index', compact('supplierList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_supplier' => 'required|string|max:20|unique:supplier,kode_supplier',
            'nama_supplier' => 'required|string|max:150',
            'kontak_person' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'alamat' => 'nullable|string',
        ]);

        Supplier::create([
            'kode_supplier' => strtoupper(trim($validated['kode_supplier'])),
            'nama_supplier' => trim($validated['nama_supplier']),
            'kontak_person' => $validated['kontak_person'] ?? null,
            'telepon' => $validated['telepon'] ?? null,
            'email' => $validated['email'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil didaftarkan.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'kode_supplier' => 'required|string|max:20|unique:supplier,kode_supplier,' . $supplier->id,
            'nama_supplier' => 'required|string|max:150',
            'kontak_person' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'alamat' => 'nullable|string',
        ]);

        $supplier->update([
            'kode_supplier' => strtoupper(trim($validated['kode_supplier'])),
            'nama_supplier' => trim($validated['nama_supplier']),
            'kontak_person' => $validated['kontak_person'] ?? null,
            'telepon' => $validated['telepon'] ?? null,
            'email' => $validated['email'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ]);

        return redirect()->route('supplier.index')->with('success', 'Data supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->barangMasuk()->exists()) {
            return back()->with('error', 'Supplier ini tidak dapat dihapus karena tercatat dalam histori barang masuk.');
        }

        $supplier->delete();
        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
