<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    /**
     * Display a listing of admin accounts.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = User::where('role', 'admin')->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $admins = $query->paginate(10)->withQueryString();

        return view('admin.master.data-admin', compact('admins', 'search'));
    }

    /**
     * Store a newly created admin account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'no_hp' => 'required|string|max:20|unique:users,no_hp',
            'password' => ['required', 'string', 'min:6'],
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'no_hp.required' => 'Nomor HP WhatsApp wajib diisi.',
            'no_hp.unique' => 'Nomor HP sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        User::create([
            'username' => $request->username,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'profile_photo' => $photoPath,
        ]);

        return redirect()->route('admin.master.admin')->with('success', 'Akun administrator baru berhasil dibuat.');
    }

    /**
     * Update the specified admin account.
     */
    public function update(Request $request, User $admin)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $admin->id,
            'no_hp' => 'required|string|max:20|unique:users,no_hp,' . $admin->id,
            'password' => 'nullable|string|min:6',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'no_hp.required' => 'Nomor HP WhatsApp wajib diisi.',
            'no_hp.unique' => 'Nomor HP sudah terdaftar.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $data = [
            'username' => $request->username,
            'no_hp' => $request->no_hp,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($admin->profile_photo && Storage::disk('public')->exists($admin->profile_photo)) {
                Storage::disk('public')->delete($admin->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $admin->update($data);

        return redirect()->route('admin.master.admin')->with('success', 'Data administrator berhasil diperbarui.');
    }

    /**
     * Remove the specified admin account.
     */
    public function destroy(User $admin)
    {
        if ($admin->id === Auth::id()) {
            return redirect()->route('admin.master.admin')->with('error', 'Anda tidak dapat menghapus akun administrator yang sedang aktif digunakan.');
        }

        // Delete photo if exists
        if ($admin->profile_photo && Storage::disk('public')->exists($admin->profile_photo)) {
            Storage::disk('public')->delete($admin->profile_photo);
        }

        $admin->delete();

        return redirect()->route('admin.master.admin')->with('success', 'Akun administrator berhasil dihapus.');
    }
}
