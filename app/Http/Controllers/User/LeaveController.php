<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    /**
     * Display a list of user leave applications.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->input('status');

        $query = Leave::where('user_id', $user->id)->orderBy('created_at', 'desc');

        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $leaves = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => Leave::where('user_id', $user->id)->count(),
            'pending' => Leave::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved' => Leave::where('user_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => Leave::where('user_id', $user->id)->where('status', 'rejected')->count(),
        ];

        return view('user.leaves.history', compact('leaves', 'stats', 'status'));
    }

    /**
     * Show form to apply for a leave.
     */
    public function create()
    {
        return view('user.leaves.create');
    }

    /**
     * Store new leave request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:sakit,izin,cuti',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:3072',
        ], [
            'type.required' => 'Pilih jenis permohonan izin.',
            'start_date.required' => 'Pilih tanggal mulai izin.',
            'end_date.required' => 'Pilih tanggal selesai izin.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'reason.required' => 'Alasan permohonan izin wajib diisi.',
            'attachment.max' => 'Ukuran berkas maksimal 3 MB.',
            'attachment.mimes' => 'Format berkas harus JPG, PNG, atau PDF.',
        ]);

        $user = Auth::user();
        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leaves/attachments', 'public');
        }

        Leave::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'attachment' => $attachmentPath,
            'status' => 'pending',
        ]);

        return redirect()->route('user.leaves.history')->with('success', 'Pengajuan izin Anda berhasil dikirim dan sedang menunggu persetujuan admin.');
    }
}
