<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Admin also sees user management section
        if ($user->isAdmin()) {
            $allUsers = User::orderBy('role')->orderBy('name')->paginate(20);
            return view('profil.index', compact('user', 'allUsers'));
        }

        return view('profil.index', compact('user'));
    }

    // Admin: manage user accounts
    public function users()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $users = User::orderBy('role')->orderBy('name')->paginate(20);
        return view('profil.users', compact('users'));
    }

    public function createUser()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }
        return view('profil.create_user');
    }

    public function storeUser(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'nip' => 'required|string|max:20|unique:users,nip',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'position' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'role' => 'required|in:pegawai,admin,manajemen',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'nip.unique' => 'NIP sudah terdaftar.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        User::create([
            'nip' => $validated['nip'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'position' => $validated['position'],
            'unit' => $validated['unit'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profil.users')
            ->with('success', "Akun pengguna {$validated['name']} berhasil dibuat.");
    }

    public function editUser(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }
        return view('profil.edit_user', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'position' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'role' => 'required|in:pegawai,admin,manajemen',
            'is_active' => 'boolean',
            'new_password' => 'nullable|string|min:8|confirmed',
        ], [
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $updateData = [
            'position' => $validated['position'],
            'unit' => $validated['unit'],
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (!empty($validated['new_password'])) {
            $updateData['password'] = Hash::make($validated['new_password']);
        }

        $user->update($updateData);

        return redirect()->route('profil.users')
            ->with('success', "Akun {$user->name} berhasil diperbarui.");
    }

    public function toggleUserStatus(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        // Prevent deactivating own account
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun {$user->name} berhasil {$status}.");
    }
}
