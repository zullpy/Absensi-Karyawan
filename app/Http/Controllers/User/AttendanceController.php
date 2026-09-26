<?php

namespace App\Http\Controllers\User;

use App\Helpers\DistanceHelper;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\OfficeSetting;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    /**
     * Display the dashboard: Admin Dashboard for admin role, Selfie Attendance for user role.
     */
    public function dashboard()
    {
        $user = Auth::user();

        // If user is Admin, render Admin Dashboard
        if ($user->isAdmin()) {
            $totalEmployees = User::where('role', 'user')->count();
            $today = Carbon::today()->toDateString();
            $todayAttendancesCount = Attendance::where('date', $today)->count();
            $pendingLeavesCount = Leave::where('status', 'pending')->count();

            $recentAttendances = Attendance::with('user')
                ->where('date', $today)
                ->latest()
                ->take(5)
                ->get();

            $recentLeaves = Leave::with('user')
                ->where('status', 'pending')
                ->latest()
                ->take(5)
                ->get();

            return view('admin.dashboard', compact(
                'totalEmployees',
                'todayAttendancesCount',
                'pendingLeavesCount',
                'recentAttendances',
                'recentLeaves'
            ));
        }

        // If user is regular Employee/User, render Selfie Camera Attendance Dashboard
        $today = Carbon::today()->toDateString();

        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $officeSetting = OfficeSetting::getActiveSetting();

        return view('dashboard', compact('todayAttendance', 'officeSetting'));
    }

    /**
     * Handle Clock In attendance with selfie & GPS location.
     */
    public function clockIn(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur presensi mandiri hanya untuk akun karyawan (user).',
            ], 403);
        }
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image' => 'required|string',
        ], [
            'latitude.required' => 'Koordinat lokasi tidak terdeteksi.',
            'longitude.required' => 'Koordinat lokasi tidak terdeteksi.',
            'image.required' => 'Foto selfie wajib diambil.',
        ]);

        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // Check if already clocked in today
        $existing = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($existing && $existing->time_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen masuk hari ini pada ' . Carbon::parse($existing->time_in)->format('H:i:s') . '.',
            ], 422);
        }

        $office = OfficeSetting::getActiveSetting();
        $lat = (float) $request->latitude;
        $long = (float) $request->longitude;

        // Calculate distance from office
        $distance = DistanceHelper::calculateDistance(
            $lat,
            $long,
            (float) $office->latitude,
            (float) $office->longitude
        );

        $maxRadius = (int) $office->radius_meters;

        if ($distance > $maxRadius) {
            $formattedDistance = round($distance, 1);
            return response()->json([
                'success' => false,
                'message' => "Posisi Anda berada di luar radius kantor ({$formattedDistance} meter). Maksimal jarak yang diizinkan adalah {$maxRadius} meter.",
                'distance' => $formattedDistance,
            ], 422);
        }

        // Process Base64 selfie image
        $imagePath = $this->saveSelfieImage($request->image, 'in', $user->id);

        $currentTime = Carbon::now()->toTimeString();
        $status = 'present';

        // Check if late (e.g. past 08:30:00)
        if (Carbon::parse($currentTime)->greaterThan(Carbon::parse('08:30:00'))) {
            $status = 'late';
        }

        // Create Attendance
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'time_in' => $currentTime,
            'photo_in' => $imagePath,
            'lat_in' => $lat,
            'long_in' => $long,
            'status' => $status,
            'distance_in_meters' => round($distance, 2),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen masuk berhasil dicatat' . ($status === 'late' ? ' (Terlambat).' : ' tepat waktu.'),
            'attendance' => $attendance,
        ]);
    }

    /**
     * Handle Clock Out attendance with selfie & GPS location.
     */
    public function clockOut(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur presensi mandiri hanya untuk akun karyawan (user).',
            ], 403);
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image' => 'required|string',
        ], [
            'latitude.required' => 'Koordinat lokasi tidak terdeteksi.',
            'longitude.required' => 'Koordinat lokasi tidak terdeteksi.',
            'image.required' => 'Foto selfie wajib diambil.',
        ]);

        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->time_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan absen masuk hari ini.',
            ], 422);
        }

        if ($attendance->time_out) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen keluar hari ini pada ' . Carbon::parse($attendance->time_out)->format('H:i:s') . '.',
            ], 422);
        }

        $office = OfficeSetting::getActiveSetting();
        $lat = (float) $request->latitude;
        $long = (float) $request->longitude;

        // Calculate distance from office
        $distance = DistanceHelper::calculateDistance(
            $lat,
            $long,
            (float) $office->latitude,
            (float) $office->longitude
        );

        $maxRadius = (int) $office->radius_meters;

        if ($distance > $maxRadius) {
            $formattedDistance = round($distance, 1);
            return response()->json([
                'success' => false,
                'message' => "Posisi Anda berada di luar radius kantor ({$formattedDistance} meter). Maksimal jarak yang diizinkan adalah {$maxRadius} meter.",
                'distance' => $formattedDistance,
            ], 422);
        }

        // Process Base64 selfie image
        $imagePath = $this->saveSelfieImage($request->image, 'out', $user->id);
        $currentTime = Carbon::now()->toTimeString();

        $attendance->update([
            'time_out' => $currentTime,
            'photo_out' => $imagePath,
            'lat_out' => $lat,
            'long_out' => $long,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen keluar berhasil dicatat pada ' . Carbon::parse($currentTime)->format('H:i:s') . '.',
            'attendance' => $attendance,
        ]);
    }

    /**
     * Display Attendance History for the current user.
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $month = $request->input('month', Carbon::now()->format('m'));
        $year = $request->input('year', Carbon::now()->format('Y'));

        $attendances = Attendance::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'desc')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total_present' => Attendance::where('user_id', $user->id)->whereYear('date', $year)->whereMonth('date', $month)->where('status', 'present')->count(),
            'total_late' => Attendance::where('user_id', $user->id)->whereYear('date', $year)->whereMonth('date', $month)->where('status', 'late')->count(),
        ];

        return view('user.attendance.history', compact('attendances', 'stats', 'month', 'year'));
    }

    /**
     * Save base64 encoded selfie image to public storage.
     */
    private function saveSelfieImage(string $base64String, string $type, int $userId): string
    {
        // Strip data:image/...;base64, prefix if present
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $typeMatch)) {
            $data = substr($base64String, strpos($base64String, ',') + 1);
            $extension = strtolower($typeMatch[1]) === 'png' ? 'png' : 'jpg';
        } else {
            $data = $base64String;
            $extension = 'jpg';
        }

        $decodedImage = base64_decode($data);
        if (!$decodedImage) {
            abort(response()->json([
                'success' => false,
                'message' => 'Format data foto selfie tidak valid.',
            ], 422));
        }

        $imageInfo = @getimagesizefromstring($decodedImage);
        if (!$imageInfo) {
            abort(response()->json([
                'success' => false,
                'message' => 'File foto selfie rusak atau tidak dikenali.',
            ], 422));
        }

        $fileName = 'attendances/selfies/' . $userId . '_' . $type . '_' . time() . '_' . Str::random(6) . '.' . $extension;

        Storage::disk('public')->put($fileName, $decodedImage);

        return $fileName;
    }
}
