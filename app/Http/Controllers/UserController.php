<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Tampilkan Daftar User
    public function index()
    {
        $users = User::latest()->get();
        return view('user.index', compact('users'));
    }

    // Form Tambah
    public function create()
    {
        return view('user.create');
    }

    // Simpan Data Baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|unique:users,username', // Username harus unik
            'password' => 'required|min:6', // Minimal 6 karakter
            'role' => 'required|in:admin,kasir', // Sesuai PDF hal 5
        ]);

        User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'password' => Hash::make($request->password), // PENTING: Hash password
            'role' => $request->role,
        ]);

        return redirect()->route('user.index')->with('success', 'Pegawai baru berhasil ditambahkan');
    }

    // Form Edit
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('user.edit', compact('user'));
    }

    // Update Data
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:100',
            // Validasi username unik, KECUALI untuk user ini sendiri
            'username' => ['required', Rule::unique('users')->ignore($user->id_user, 'id_user')],
            'role' => 'required|in:admin,kasir',
            'password' => 'nullable|min:6', // Password boleh kosong jika tidak ingin diganti
        ]);

        // Data yang akan diupdate
        $data = [
            'nama' => $request->nama,
            'username' => $request->username,
            'role' => $request->role,
        ];

        // Jika password diisi, hash dan masukkan ke array data
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('user.index')->with('success', 'Data pegawai diperbarui');
    }

    // Hapus Data
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Jangan izinkan user menghapus dirinya sendiri saat sedang login
        if ($user->id_user == auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri saat sedang login!');
        }

        // Cek riwayat transaksi
        if ($user->transaksi()->exists()) {
            return back()->with('error', 'Gagal! Pegawai ini memiliki riwayat transaksi.');
        }

        $user->delete();
        return redirect()->route('user.index')->with('success', 'Pegawai dihapus');
    }
}