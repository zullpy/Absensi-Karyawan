<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Leave;
use App\Services\WebPushService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    /**
     * Display a listing of employee leave requests.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $type = $request->input('type');
        $divisionId = $request->input('division_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Leave::with(['user.division']);

        // Search by employee username or phone
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        // Filter by Status (pending, approved, rejected)
        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        // Filter by Type (izin, sakit, cuti)
        if ($type && in_array($type, ['izin', 'sakit', 'cuti'])) {
            $query->where('type', $type);
        }

        // Filter by Division
        if ($divisionId) {
            $query->whereHas('user', function ($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }

        // Filter by Date Range
        if ($startDate && $endDate) {
            $query->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($sub) use ($startDate, $endDate) {
                      $sub->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                  });
            });
        } elseif ($startDate) {
            $query->whereDate('start_date', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('end_date', '<=', $endDate);
        }

        // Overview KPI Stats
        $totalCount = Leave::count();
        $pendingCount = Leave::where('status', 'pending')->count();
        $approvedCount = Leave::where('status', 'approved')->count();
        $rejectedCount = Leave::where('status', 'rejected')->count();

        // Get leaves ordered by latest created
        $leaves = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $divisions = Division::orderBy('name', 'asc')->get();

        return view('admin.pengajuan-izin', compact(
            'leaves',
            'divisions',
            'search',
            'status',
            'type',
            'divisionId',
            'startDate',
            'endDate',
            'totalCount',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    /**
     * Approve the specified leave request.
     */
    public function approve(Request $request, Leave $leave, WebPushService $webPushService)
    {
        $leave->update([
            'status' => 'approved',
            'rejection_note' => null,
        ]);

        // Send Push Notification if user subscribed
        try {
            if ($leave->user) {
                $startFormatted = Carbon::parse($leave->start_date)->translatedFormat('d M Y');
                $endFormatted = Carbon::parse($leave->end_date)->translatedFormat('d M Y');
                $dateText = ($leave->start_date == $leave->end_date) ? $startFormatted : "{$startFormatted} - {$endFormatted}";

                $webPushService->sendToUser(
                    $leave->user,
                    '✅ Pengajuan Izin Disetujui',
                    "Permohonan " . ucfirst($leave->type) . " Anda ({$dateText}) telah disetujui oleh admin.",
                    [
                        'tag' => 'leave-status-' . $leave->id,
                        'url' => route('user.leaves.history'),
                    ]
                );
            }
        } catch (\Throwable $e) {
            Log::warning("Gagal mengirim push notifikasi approval izin: " . $e->getMessage());
        }

        return back()->with('success', 'Pengajuan ' . ucfirst($leave->type) . ' untuk ' . ($leave->user->username ?? 'karyawan') . ' berhasil disetujui.');
    }

    /**
     * Reject the specified leave request with notes.
     */
    public function reject(Request $request, Leave $leave, WebPushService $webPushService)
    {
        $request->validate([
            'rejection_note' => 'required|string|max:500',
        ], [
            'rejection_note.required' => 'Alasan penolakan pengajuan izin wajib diisi.',
            'rejection_note.max' => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        $leave->update([
            'status' => 'rejected',
            'rejection_note' => $request->rejection_note,
        ]);

        // Send Push Notification if user subscribed
        try {
            if ($leave->user) {
                $webPushService->sendToUser(
                    $leave->user,
                    '❌ Pengajuan Izin Ditolak',
                    "Permohonan " . ucfirst($leave->type) . " Anda ditolak. Alasan: " . $request->rejection_note,
                    [
                        'tag' => 'leave-status-' . $leave->id,
                        'url' => route('user.leaves.history'),
                    ]
                );
            }
        } catch (\Throwable $e) {
            Log::warning("Gagal mengirim push notifikasi reject izin: " . $e->getMessage());
        }

        return back()->with('success', 'Pengajuan ' . ucfirst($leave->type) . ' untuk ' . ($leave->user->username ?? 'karyawan') . ' telah ditolak.');
    }

    /**
     * Remove the specified leave request from storage.
     */
    public function destroy(Leave $leave)
    {
        // Delete attachment file if exists
        if ($leave->attachment && Storage::disk('public')->exists($leave->attachment)) {
            Storage::disk('public')->delete($leave->attachment);
        }

        $leave->delete();

        return back()->with('success', 'Catatan pengajuan izin berhasil dihapus.');
    }
}
