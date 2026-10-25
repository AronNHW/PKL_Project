<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $usersList = User::with('lokasi')->orderBy('role')->orderBy('name')->get();
        $lokasiList = Lokasi::where('is_aktif', true)->get();

        return view('users.index', compact('usersList', 'lokasiList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:owner,admin_pusat,admin_cabang',
            'id_lokasi' => 'nullable|exists:lokasi,id',
            'telepon' => 'nullable|string|max:30',
        ]);

        User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'id_lokasi' => $validated['role'] === 'admin_cabang' ? $validated['id_lokasi'] : ($validated['role'] === 'admin_pusat' ? ($validated['id_lokasi'] ?? Lokasi::where('tipe', 'pusat')->value('id')) : null),
            'telepon' => $validated['telepon'] ?? null,
            'is_aktif' => true,
        ]);

        return redirect()->route('users.index')->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'role' => 'required|in:owner,admin_pusat,admin_cabang',
            'id_lokasi' => 'nullable|exists:lokasi,id',
            'telepon' => 'nullable|string|max:30',
            'is_aktif' => 'required|boolean',
        ]);

        $updateData = [
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'role' => $validated['role'],
            'id_lokasi' => $validated['role'] === 'admin_cabang' ? $validated['id_lokasi'] : ($validated['role'] === 'admin_pusat' ? ($validated['id_lokasi'] ?? Lokasi::where('tipe', 'pusat')->value('id')) : null),
            'telepon' => $validated['telepon'] ?? null,
            'is_aktif' => $validated['is_aktif'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('users.index')->with('success', 'Akun pengguna berhasil diperbarui.');
    }
}
