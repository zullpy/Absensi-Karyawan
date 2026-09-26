<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    /**
     * Display a listing of divisions.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Division::withCount('users')->orderBy('name', 'asc');

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $divisions = $query->paginate(10)->withQueryString();

        return view('admin.master.divisi', compact('divisions', 'search'));
    }

    /**
     * Store a newly created division.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:divisions,name',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama divisi wajib diisi.',
            'name.unique' => 'Nama divisi sudah ada, gunakan nama lain.',
        ]);

        Division::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.master.divisi')->with('success', 'Divisi baru berhasil ditambahkan.');
    }

    /**
     * Update the specified division.
     */
    public function update(Request $request, Division $divisi)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:divisions,name,' . $divisi->id,
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama divisi wajib diisi.',
            'name.unique' => 'Nama divisi sudah ada, gunakan nama lain.',
        ]);

        $divisi->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.master.divisi')->with('success', 'Data divisi berhasil diperbarui.');
    }

    /**
     * Remove the specified division.
     */
    public function destroy(Division $divisi)
    {
        $divisi->delete();

        return redirect()->route('admin.master.divisi')->with('success', 'Divisi berhasil dihapus.');
    }
}
