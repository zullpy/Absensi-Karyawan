<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of user / employee accounts.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $divisionId = $request->input('division_id');

        $query = User::with('division')->where('role', 'user')->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        if ($divisionId) {
            $query->where('division_id', $divisionId);
        }

        $users = $query->paginate(10)->withQueryString();
        $divisions = Division::orderBy('name', 'asc')->get();

        return view('admin.master.data-user', compact('users', 'divisions', 'search', 'divisionId'));
    }

    /**
     * Store a newly created user / employee account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'no_hp' => 'required|string|max:20|unique:users,no_hp',
            'division_id' => 'nullable|exists:divisions,id',
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
            'division_id' => $request->division_id,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'profile_photo' => $photoPath,
        ]);

        return redirect()->route('admin.master.user')->with('success', 'Akun karyawan baru berhasil dibuat.');
    }

    /**
     * Update the specified user / employee account.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'no_hp' => 'required|string|max:20|unique:users,no_hp,' . $user->id,
            'division_id' => 'nullable|exists:divisions,id',
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
            'division_id' => $request->division_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user->update($data);

        return redirect()->route('admin.master.user')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified user account.
     */
    public function destroy(User $user)
    {
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->delete();

        return redirect()->route('admin.master.user')->with('success', 'Akun karyawan berhasil dihapus.');
    }
}
