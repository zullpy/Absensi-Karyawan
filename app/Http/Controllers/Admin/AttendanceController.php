<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Division;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    /**
     * Display a listing of employee attendances with filters and search.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');
        $divisionId = $request->input('division_id');
        $status = $request->input('status');
        $clockOutStatus = $request->input('clock_out_status');

        $query = Attendance::with(['user.division', 'shift']);

        // Search by username or phone
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        // Filter by Date
        if ($date) {
            $query->where('date', $date);
        }

        // Filter by Division
        if ($divisionId) {
            $query->whereHas('user', function ($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }

        // Filter by Status (present, late, not_clocked_out, clocked_out)
        if ($status === 'present') {
            $query->where('status', 'present');
        } elseif ($status === 'late') {
            $query->where('status', 'late');
        } elseif ($status === 'not_clocked_out') {
            $query->whereNull('time_out');
        } elseif ($status === 'clocked_out') {
            $query->whereNotNull('time_out');
        }

        // Backwards-compatible filter by Clock Out status
        if ($clockOutStatus === 'clocked_out') {
            $query->whereNotNull('time_out');
        } elseif ($clockOutStatus === 'not_clocked_out') {
            $query->whereNull('time_out');
        }

        // Stats calculation based on current date filter (or all/today)
        $statsDate = $date ?? Carbon::today()->toDateString();
        $statsQuery = Attendance::query();
        if ($date) {
            $statsQuery->where('date', $date);
        }

        $totalCount = (clone $statsQuery)->count();
        $presentCount = (clone $statsQuery)->where('status', 'present')->count();
        $lateCount = (clone $statsQuery)->where('status', 'late')->count();
        $notClockedOutCount = (clone $statsQuery)->whereNull('time_out')->count();

        // Order by latest date and latest clock in
        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->paginate(15)
            ->withQueryString();

        $divisions = Division::orderBy('name', 'asc')->get();

        return view('admin.absensi', compact(
            'attendances',
            'divisions',
            'search',
            'date',
            'divisionId',
            'status',
            'clockOutStatus',
            'totalCount',
            'presentCount',
            'lateCount',
            'notClockedOutCount'
        ));
    }

    /**
     * Remove the specified attendance from storage.
     */
    public function destroy(Attendance $attendance)
    {
        // Delete photo files if exists
        if ($attendance->photo_in && Storage::disk('public')->exists($attendance->photo_in)) {
            Storage::disk('public')->delete($attendance->photo_in);
        }

        if ($attendance->photo_out && Storage::disk('public')->exists($attendance->photo_out)) {
            Storage::disk('public')->delete($attendance->photo_out);
        }

        $attendance->delete();

        return back()->with('success', 'Catatan absensi berhasil dihapus.');
    }
}
