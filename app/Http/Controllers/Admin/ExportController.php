<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Division;
use App\Models\Leave;
use App\Models\OfficeSetting;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Tampilkan halaman utama Export Data dengan tab Absensi, Izin, dan Rekap Bulanan.
     */
    public function index(Request $request)
    {
        $divisions = Division::orderBy('name', 'asc')->get();
        $employees = User::where('role', 'user')->orderBy('username', 'asc')->get();
        $office = OfficeSetting::getActiveSetting();

        // Default rentang tanggal: Awal bulan ini hingga hari ini
        $defaultStartDate = Carbon::now()->startOfMonth()->toDateString();
        $defaultEndDate = Carbon::now()->toDateString();

        // Data awal untuk Absensi
        $initialAttendanceQuery = Attendance::with(['user.division', 'shift'])
            ->whereBetween('date', [$defaultStartDate, $defaultEndDate]);

        $attendanceStats = [
            'total' => (clone $initialAttendanceQuery)->count(),
            'present' => (clone $initialAttendanceQuery)->where('status', 'present')->count(),
            'late' => (clone $initialAttendanceQuery)->where('status', 'late')->count(),
            'not_clocked_out' => (clone $initialAttendanceQuery)->whereNull('time_out')->count(),
        ];

        // Data awal untuk Izin
        $initialLeaveQuery = Leave::with(['user.division'])
            ->where(function ($q) use ($defaultStartDate, $defaultEndDate) {
                $q->whereBetween('start_date', [$defaultStartDate, $defaultEndDate])
                  ->orWhereBetween('end_date', [$defaultStartDate, $defaultEndDate]);
            });

        $leaveStats = [
            'total' => (clone $initialLeaveQuery)->count(),
            'approved' => (clone $initialLeaveQuery)->where('status', 'approved')->count(),
            'pending' => (clone $initialLeaveQuery)->where('status', 'pending')->count(),
            'rejected' => (clone $initialLeaveQuery)->where('status', 'rejected')->count(),
        ];

        return view('admin.export', compact(
            'divisions',
            'employees',
            'office',
            'defaultStartDate',
            'defaultEndDate',
            'attendanceStats',
            'leaveStats'
        ));
    }

    /**
     * AJAX Preview Data Absensi
     */
    public function previewAbsensi(Request $request)
    {
        $query = $this->buildAttendanceQuery($request);

        $totalCount = (clone $query)->count();
        $presentCount = (clone $query)->where('status', 'present')->count();
        $lateCount = (clone $query)->where('status', 'late')->count();
        $notClockedOutCount = (clone $query)->whereNull('time_out')->count();

        $attendances = $query->limit(10)->get()->map(function ($att, $idx) {
            $duration = '-';
            if ($att->time_in && $att->time_out) {
                $in = Carbon::parse($att->time_in);
                $out = Carbon::parse($att->time_out);
                $diff = $in->diff($out);
                $duration = sprintf('%dj %dm', $diff->h, $diff->i);
            }

            return [
                'no' => $idx + 1,
                'date' => Carbon::parse($att->date)->locale('id')->isoFormat('D MMMM Y'),
                'raw_date' => Carbon::parse($att->date)->format('d/m/Y'),
                'employee_name' => $att->user ? $att->user->username : 'Tidak Dikenal',
                'division' => $att->user && $att->user->division ? $att->user->division->name : '-',
                'shift' => $att->shift ? $att->shift->name : 'Reguler',
                'time_in' => $att->time_in ? Carbon::parse($att->time_in)->format('H:i') : '-',
                'time_out' => $att->time_out ? Carbon::parse($att->time_out)->format('H:i') : '-',
                'duration' => $duration,
                'status' => $att->status,
                'status_label' => $att->status === 'late' ? 'Terlambat' : 'Tepat Waktu',
                'is_clocked_out' => !is_null($att->time_out),
            ];
        });

        return response()->json([
            'success' => true,
            'stats' => [
                'total' => $totalCount,
                'present' => $presentCount,
                'late' => $lateCount,
                'not_clocked_out' => $notClockedOutCount,
            ],
            'items' => $attendances,
            'total_preview' => $attendances->count(),
        ]);
    }

    /**
     * AJAX Preview Data Pengajuan Izin
     */
    public function previewIzin(Request $request)
    {
        $query = $this->buildLeaveQuery($request);

        $totalCount = (clone $query)->count();
        $approvedCount = (clone $query)->where('status', 'approved')->count();
        $pendingCount = (clone $query)->where('status', 'pending')->count();
        $rejectedCount = (clone $query)->where('status', 'rejected')->count();

        $leaves = $query->limit(10)->get()->map(function ($leave, $idx) {
            $start = Carbon::parse($leave->start_date);
            $end = Carbon::parse($leave->end_date);
            $days = $start->diffInDays($end) + 1;

            return [
                'no' => $idx + 1,
                'created_at' => $leave->created_at ? Carbon::parse($leave->created_at)->locale('id')->isoFormat('D MMMM Y, H:i') : '-',
                'employee_name' => $leave->user ? $leave->user->username : 'Tidak Dikenal',
                'division' => $leave->user && $leave->user->division ? $leave->user->division->name : '-',
                'type' => ucfirst($leave->type),
                'date_range' => $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y'),
                'duration' => $days . ' Hari',
                'reason' => $leave->reason,
                'status' => $leave->status,
                'status_label' => match ($leave->status) {
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                    default => 'Menunggu',
                },
                'rejection_note' => $leave->rejection_note ?? '-',
            ];
        });

        return response()->json([
            'success' => true,
            'stats' => [
                'total' => $totalCount,
                'approved' => $approvedCount,
                'pending' => $pendingCount,
                'rejected' => $rejectedCount,
            ],
            'items' => $leaves,
            'total_preview' => $leaves->count(),
        ]);
    }

    /**
     * AJAX Preview Data Rekap Bulanan
     */
    public function previewRekap(Request $request)
    {
        $data = $this->calculateMonthlyRekap($request);

        return response()->json([
            'success' => true,
            'month_label' => $data['month_label'],
            'year' => $data['year'],
            'stats' => $data['summary'],
            'items' => array_slice($data['records'], 0, 10),
            'total_preview' => min(10, count($data['records'])),
            'total_employees' => count($data['records']),
        ]);
    }

    /**
     * Export Absensi ke Excel (.xlsx)
     */
    public function exportAbsensiExcel(Request $request)
    {
        $attendances = $this->buildAttendanceQuery($request)->get();
        $office = OfficeSetting::getActiveSetting();
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->locale('id')->isoFormat('D MMMM Y') : 'Semua';
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->locale('id')->isoFormat('D MMMM Y') : 'Semua';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Absensi');

        // Style Title
        $sheet->setCellValue('A1', strtoupper($office->name ?? 'APLIKASI ABSENSI'));
        $sheet->setCellValue('A2', 'LAPORAN REKAPITULASI PRESENSI KARYAWAN');
        $sheet->setCellValue('A3', "Periode: {$startDate} s/d {$endDate} | Dicetak: " . Carbon::now()->locale('id')->isoFormat('D MMMM Y HH:mm'));

        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');

        $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF1E3A8A'));
        $sheet->getStyle('A2')->getFont()->setSize(12)->setBold(true);
        $sheet->getStyle('A3')->getFont()->setSize(10)->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header Table
        $headers = [
            'A5' => 'No',
            'B5' => 'Tanggal',
            'C5' => 'Nama Karyawan',
            'D5' => 'Divisi',
            'E5' => 'Shift',
            'F5' => 'Jam Masuk',
            'G5' => 'Jam Pulang',
            'H5' => 'Durasi Kerja',
            'I5' => 'Status Masuk',
            'J5' => 'Status Pulang',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E40AF']], // Blue 800
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']]],
        ];
        $sheet->getStyle('A5:J5')->applyFromArray($headerStyle);
        $sheet->getRowDimension(5)->setRowHeight(26);

        // Rows
        $rowNum = 6;
        $no = 1;
        $totalPresent = 0;
        $totalLate = 0;

        foreach ($attendances as $att) {
            $duration = '-';
            if ($att->time_in && $att->time_out) {
                $in = Carbon::parse($att->time_in);
                $out = Carbon::parse($att->time_out);
                $diff = $in->diff($out);
                $duration = sprintf('%02dj %02dm', $diff->h, $diff->i);
            }

            if ($att->status === 'late') {
                $totalLate++;
                $statusLabel = 'Terlambat';
            } else {
                $totalPresent++;
                $statusLabel = 'Tepat Waktu';
            }

            $clockOutLabel = $att->time_out ? 'Sudah Pulang' : 'Belum Pulang';

            $sheet->setCellValue('A' . $rowNum, $no);
            $sheet->setCellValue('B' . $rowNum, Carbon::parse($att->date)->format('d/m/Y'));
            $sheet->setCellValue('C' . $rowNum, $att->user ? $att->user->username : '-');
            $sheet->setCellValue('D' . $rowNum, $att->user && $att->user->division ? $att->user->division->name : '-');
            $sheet->setCellValue('E' . $rowNum, $att->shift ? $att->shift->name : 'Reguler');
            $sheet->setCellValue('F' . $rowNum, $att->time_in ? Carbon::parse($att->time_in)->format('H:i:s') : '-');
            $sheet->setCellValue('G' . $rowNum, $att->time_out ? Carbon::parse($att->time_out)->format('H:i:s') : '-');
            $sheet->setCellValue('H' . $rowNum, $duration);
            $sheet->setCellValue('I' . $rowNum, $statusLabel);
            $sheet->setCellValue('J' . $rowNum, $clockOutLabel);

            // Zebra striping
            if ($no % 2 === 0) {
                $sheet->getStyle("A{$rowNum}:J{$rowNum}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF8FAFC');
            }

            // Cell align
            $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$rowNum}:G{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H{$rowNum}:J{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Highlight Late
            if ($att->status === 'late') {
                $sheet->getStyle("I{$rowNum}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFDC2626'))->setBold(true);
            } else {
                $sheet->getStyle("I{$rowNum}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF16A34A'));
            }

            if (!$att->time_out) {
                $sheet->getStyle("J{$rowNum}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFD97706'));
            }

            $rowNum++;
            $no++;
        }

        // Summary row
        $lastDataRow = $rowNum - 1;
        if ($attendances->count() > 0) {
            $sheet->setCellValue('A' . $rowNum, 'TOTAL DATA');
            $sheet->setCellValue('C' . $rowNum, $attendances->count() . ' Catatan Presensi');
            $sheet->setCellValue('I' . $rowNum, "Tepat: {$totalPresent} | Telat: {$totalLate}");
            $sheet->mergeCells("A{$rowNum}:B{$rowNum}");
            $sheet->getStyle("A{$rowNum}:J{$rowNum}")->getFont()->setBold(true);
            $sheet->getStyle("A{$rowNum}:J{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF1F5F9');
            $sheet->getStyle("A{$rowNum}:J{$rowNum}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FF94A3B8');
        }

        // Borders for table data
        if ($lastDataRow >= 6) {
            $sheet->getStyle("A6:J{$lastDataRow}")->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)
                ->getColor()->setARGB('FFE2E8F0');
        }

        // Auto width columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Laporan_Presensi_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export Absensi ke PDF (.pdf)
     */
    public function exportAbsensiPdf(Request $request)
    {
        $attendances = $this->buildAttendanceQuery($request)->get();
        $office = OfficeSetting::getActiveSetting();
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->locale('id')->isoFormat('D MMMM Y') : 'Semua';
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->locale('id')->isoFormat('D MMMM Y') : 'Semua';

        $stats = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'not_clocked_out' => $attendances->whereNull('time_out')->count(),
        ];

        $pdf = Pdf::loadView('admin.export.pdf.absensi', [
            'attendances' => $attendances,
            'office' => $office,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'stats' => $stats,
            'printedBy' => Auth::user()->username ?? 'Admin',
            'printedAt' => Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm'),
        ])->setPaper('a4', 'landscape');

        $fileName = 'Laporan_Presensi_' . Carbon::now()->format('Ymd_His') . '.pdf';

        if ($request->has('preview')) {
            return $pdf->stream($fileName);
        }

        return $pdf->download($fileName);
    }

    /**
     * Print View Absensi (HTML Siap Cetak)
     */
    public function printAbsensi(Request $request)
    {
        $attendances = $this->buildAttendanceQuery($request)->get();
        $office = OfficeSetting::getActiveSetting();
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->locale('id')->isoFormat('D MMMM Y') : 'Semua';
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->locale('id')->isoFormat('D MMMM Y') : 'Semua';

        $stats = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'not_clocked_out' => $attendances->whereNull('time_out')->count(),
        ];

        return view('admin.export.print.absensi', [
            'attendances' => $attendances,
            'office' => $office,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'stats' => $stats,
            'printedBy' => Auth::user()->username ?? 'Admin',
            'printedAt' => Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm'),
        ]);
    }

    /**
     * Export Izin ke Excel (.xlsx)
     */
    public function exportIzinExcel(Request $request)
    {
        $leaves = $this->buildLeaveQuery($request)->get();
        $office = OfficeSetting::getActiveSetting();
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->locale('id')->isoFormat('D MMMM Y') : 'Semua';
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->locale('id')->isoFormat('D MMMM Y') : 'Semua';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Izin Cuti');

        // Style Title
        $sheet->setCellValue('A1', strtoupper($office->name ?? 'APLIKASI ABSENSI'));
        $sheet->setCellValue('A2', 'LAPORAN PENGAJUAN IZIN & CUTI KARYAWAN');
        $sheet->setCellValue('A3', "Periode: {$startDate} s/d {$endDate} | Dicetak: " . Carbon::now()->locale('id')->isoFormat('D MMMM Y HH:mm'));

        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');

        $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF1E3A8A'));
        $sheet->getStyle('A2')->getFont()->setSize(12)->setBold(true);
        $sheet->getStyle('A3')->getFont()->setSize(10)->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header Table
        $headers = [
            'A5' => 'No',
            'B5' => 'Tgl Diajukan',
            'C5' => 'Nama Karyawan',
            'D5' => 'Divisi',
            'E5' => 'Jenis',
            'F5' => 'Rentang Tanggal',
            'G5' => 'Durasi',
            'H5' => 'Alasan Pengajuan',
            'I5' => 'Status Persetujuan',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF047857']], // Emerald 700
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']]],
        ];
        $sheet->getStyle('A5:I5')->applyFromArray($headerStyle);
        $sheet->getRowDimension(5)->setRowHeight(26);

        // Rows
        $rowNum = 6;
        $no = 1;
        $totalApproved = 0;
        $totalPending = 0;
        $totalRejected = 0;

        foreach ($leaves as $leave) {
            $start = Carbon::parse($leave->start_date);
            $end = Carbon::parse($leave->end_date);
            $days = $start->diffInDays($end) + 1;

            if ($leave->status === 'approved') {
                $totalApproved++;
                $statusLabel = 'Disetujui';
            } elseif ($leave->status === 'rejected') {
                $totalRejected++;
                $statusLabel = 'Ditolak';
            } else {
                $totalPending++;
                $statusLabel = 'Menunggu';
            }

            $sheet->setCellValue('A' . $rowNum, $no);
            $sheet->setCellValue('B' . $rowNum, $leave->created_at ? Carbon::parse($leave->created_at)->format('d/m/Y') : '-');
            $sheet->setCellValue('C' . $rowNum, $leave->user ? $leave->user->username : '-');
            $sheet->setCellValue('D' . $rowNum, $leave->user && $leave->user->division ? $leave->user->division->name : '-');
            $sheet->setCellValue('E' . $rowNum, ucfirst($leave->type));
            $sheet->setCellValue('F' . $rowNum, $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y'));
            $sheet->setCellValue('G' . $rowNum, $days . ' Hari');
            $sheet->setCellValue('H' . $rowNum, $leave->reason);
            $sheet->setCellValue('I' . $rowNum, $statusLabel);

            if ($no % 2 === 0) {
                $sheet->getStyle("A{$rowNum}:I{$rowNum}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF8FAFC');
            }

            $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$rowNum}:G{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($leave->status === 'approved') {
                $sheet->getStyle("I{$rowNum}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF16A34A'))->setBold(true);
            } elseif ($leave->status === 'rejected') {
                $sheet->getStyle("I{$rowNum}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFDC2626'))->setBold(true);
            } else {
                $sheet->getStyle("I{$rowNum}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFD97706'))->setBold(true);
            }

            $rowNum++;
            $no++;
        }

        $lastDataRow = $rowNum - 1;
        if ($leaves->count() > 0) {
            $sheet->setCellValue('A' . $rowNum, 'TOTAL DATA');
            $sheet->setCellValue('C' . $rowNum, $leaves->count() . ' Pengajuan');
            $sheet->setCellValue('I' . $rowNum, "Setuju: {$totalApproved} | Tunggu: {$totalPending} | Tolak: {$totalRejected}");
            $sheet->mergeCells("A{$rowNum}:B{$rowNum}");
            $sheet->getStyle("A{$rowNum}:I{$rowNum}")->getFont()->setBold(true);
            $sheet->getStyle("A{$rowNum}:I{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF1F5F9');
            $sheet->getStyle("A{$rowNum}:I{$rowNum}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FF94A3B8');
        }

        if ($lastDataRow >= 6) {
            $sheet->getStyle("A6:I{$lastDataRow}")->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)
                ->getColor()->setARGB('FFE2E8F0');
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Laporan_Izin_Cuti_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export Izin ke PDF (.pdf)
     */
    public function exportIzinPdf(Request $request)
    {
        $leaves = $this->buildLeaveQuery($request)->get();
        $office = OfficeSetting::getActiveSetting();
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->locale('id')->isoFormat('D MMMM Y') : 'Semua';
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->locale('id')->isoFormat('D MMMM Y') : 'Semua';

        $stats = [
            'total' => $leaves->count(),
            'approved' => $leaves->where('status', 'approved')->count(),
            'pending' => $leaves->where('status', 'pending')->count(),
            'rejected' => $leaves->where('status', 'rejected')->count(),
        ];

        $pdf = Pdf::loadView('admin.export.pdf.izin', [
            'leaves' => $leaves,
            'office' => $office,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'stats' => $stats,
            'printedBy' => Auth::user()->username ?? 'Admin',
            'printedAt' => Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm'),
        ])->setPaper('a4', 'landscape');

        $fileName = 'Laporan_Izin_Cuti_' . Carbon::now()->format('Ymd_His') . '.pdf';

        if ($request->has('preview')) {
            return $pdf->stream($fileName);
        }

        return $pdf->download($fileName);
    }

    /**
     * Print View Izin (HTML Siap Cetak)
     */
    public function printIzin(Request $request)
    {
        $leaves = $this->buildLeaveQuery($request)->get();
        $office = OfficeSetting::getActiveSetting();
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->locale('id')->isoFormat('D MMMM Y') : 'Semua';
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->locale('id')->isoFormat('D MMMM Y') : 'Semua';

        $stats = [
            'total' => $leaves->count(),
            'approved' => $leaves->where('status', 'approved')->count(),
            'pending' => $leaves->where('status', 'pending')->count(),
            'rejected' => $leaves->where('status', 'rejected')->count(),
        ];

        return view('admin.export.print.izin', [
            'leaves' => $leaves,
            'office' => $office,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'stats' => $stats,
            'printedBy' => Auth::user()->username ?? 'Admin',
            'printedAt' => Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm'),
        ]);
    }

    /**
     * Export Rekap Bulanan ke Excel (.xlsx)
     */
    public function exportRekapExcel(Request $request)
    {
        $data = $this->calculateMonthlyRekap($request);
        $office = OfficeSetting::getActiveSetting();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Bulanan');

        // Style Title
        $sheet->setCellValue('A1', strtoupper($office->name ?? 'APLIKASI ABSENSI'));
        $sheet->setCellValue('A2', 'REKAPITULASI KEHADIRAN BULANAN KARYAWAN');
        $sheet->setCellValue('A3', "Periode: {$data['month_label']} {$data['year']} | Dicetak: " . Carbon::now()->locale('id')->isoFormat('D MMMM Y HH:mm'));

        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');
        $sheet->mergeCells('A3:K3');

        $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF1E3A8A'));
        $sheet->getStyle('A2')->getFont()->setSize(12)->setBold(true);
        $sheet->getStyle('A3')->getFont()->setSize(10)->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header Table
        $headers = [
            'A5' => 'No',
            'B5' => 'Nama Karyawan',
            'C5' => 'Divisi',
            'D5' => 'No. HP',
            'E5' => 'Tepat Waktu',
            'F5' => 'Terlambat',
            'G5' => 'Belum Pulang',
            'H5' => 'Izin',
            'I5' => 'Sakit',
            'J5' => 'Cuti',
            'K5' => 'Total Kehadiran',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F46E5']], // Indigo 600
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']]],
        ];
        $sheet->getStyle('A5:K5')->applyFromArray($headerStyle);
        $sheet->getRowDimension(5)->setRowHeight(26);

        // Rows
        $rowNum = 6;
        $no = 1;

        foreach ($data['records'] as $rec) {
            $sheet->setCellValue('A' . $rowNum, $no);
            $sheet->setCellValue('B' . $rowNum, $rec['username']);
            $sheet->setCellValue('C' . $rowNum, $rec['division']);
            $sheet->setCellValue('D' . $rowNum, $rec['no_hp']);
            $sheet->setCellValue('E' . $rowNum, $rec['present_count']);
            $sheet->setCellValue('F' . $rowNum, $rec['late_count']);
            $sheet->setCellValue('G' . $rowNum, $rec['not_clocked_out_count']);
            $sheet->setCellValue('H' . $rowNum, $rec['izin_count']);
            $sheet->setCellValue('I' . $rowNum, $rec['sakit_count']);
            $sheet->setCellValue('J' . $rowNum, $rec['cuti_count']);
            $sheet->setCellValue('K' . $rowNum, $rec['total_attendances']);

            if ($no % 2 === 0) {
                $sheet->getStyle("A{$rowNum}:K{$rowNum}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF8FAFC');
            }

            $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$rowNum}:K{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $rowNum++;
            $no++;
        }

        $lastDataRow = $rowNum - 1;
        if (count($data['records']) > 0) {
            $sheet->setCellValue('A' . $rowNum, 'TOTAL REKAP');
            $sheet->setCellValue('B' . $rowNum, count($data['records']) . ' Karyawan');
            $sheet->setCellValue('E' . $rowNum, $data['summary']['total_present']);
            $sheet->setCellValue('F' . $rowNum, $data['summary']['total_late']);
            $sheet->setCellValue('G' . $rowNum, $data['summary']['total_not_clocked_out']);
            $sheet->setCellValue('H' . $rowNum, $data['summary']['total_izin']);
            $sheet->setCellValue('I' . $rowNum, $data['summary']['total_sakit']);
            $sheet->setCellValue('J' . $rowNum, $data['summary']['total_cuti']);
            $sheet->setCellValue('K' . $rowNum, $data['summary']['total_attendances']);

            $sheet->getStyle("A{$rowNum}:K{$rowNum}")->getFont()->setBold(true);
            $sheet->getStyle("A{$rowNum}:K{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF1F5F9');
            $sheet->getStyle("A{$rowNum}:K{$rowNum}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FF94A3B8');
            $sheet->getStyle("E{$rowNum}:K{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        if ($lastDataRow >= 6) {
            $sheet->getStyle("A6:K{$lastDataRow}")->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)
                ->getColor()->setARGB('FFE2E8F0');
        }

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Rekap_Bulanan_' . $data['year'] . '_' . sprintf('%02d', $data['month']) . '_' . Carbon::now()->format('His') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export Rekap Bulanan ke PDF (.pdf)
     */
    public function exportRekapPdf(Request $request)
    {
        $data = $this->calculateMonthlyRekap($request);
        $office = OfficeSetting::getActiveSetting();

        $pdf = Pdf::loadView('admin.export.pdf.rekap', [
            'data' => $data,
            'office' => $office,
            'printedBy' => Auth::user()->username ?? 'Admin',
            'printedAt' => Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm'),
        ])->setPaper('a4', 'landscape');

        $fileName = 'Rekap_Bulanan_' . $data['year'] . '_' . sprintf('%02d', $data['month']) . '_' . Carbon::now()->format('His') . '.pdf';

        if ($request->has('preview')) {
            return $pdf->stream($fileName);
        }

        return $pdf->download($fileName);
    }

    /**
     * Print View Rekap Bulanan (HTML Siap Cetak)
     */
    public function printRekap(Request $request)
    {
        $data = $this->calculateMonthlyRekap($request);
        $office = OfficeSetting::getActiveSetting();

        return view('admin.export.print.rekap', [
            'data' => $data,
            'office' => $office,
            'printedBy' => Auth::user()->username ?? 'Admin',
            'printedAt' => Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm'),
        ]);
    }

    // -------------------------------------------------------------
    // Helper Methods
    // -------------------------------------------------------------

    /**
     * Build attendance filter query.
     */
    private function buildAttendanceQuery(Request $request)
    {
        $query = Attendance::with(['user.division', 'shift']);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $divisionId = $request->input('division_id');
        $userId = $request->input('user_id');
        $status = $request->input('status');

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('date', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        if ($divisionId) {
            $query->whereHas('user', function ($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($status === 'present') {
            $query->where('status', 'present');
        } elseif ($status === 'late') {
            $query->where('status', 'late');
        } elseif ($status === 'not_clocked_out') {
            $query->whereNull('time_out');
        } elseif ($status === 'clocked_out') {
            $query->whereNotNull('time_out');
        }

        return $query->orderBy('date', 'desc')->orderBy('time_in', 'desc');
    }

    /**
     * Build leave filter query.
     */
    private function buildLeaveQuery(Request $request)
    {
        $query = Leave::with(['user.division']);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $divisionId = $request->input('division_id');
        $userId = $request->input('user_id');
        $type = $request->input('type');
        $status = $request->input('status');

        if ($startDate && $endDate) {
            $query->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($sub) use ($startDate, $endDate) {
                      $sub->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                  });
            });
        }

        if ($divisionId) {
            $query->whereHas('user', function ($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($type && in_array($type, ['sakit', 'izin', 'cuti'])) {
            $query->where('type', $type);
        }

        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Hitung Rekap Kehadiran Bulanan
     */
    private function calculateMonthlyRekap(Request $request)
    {
        $month = (int) ($request->input('month') ?? Carbon::now()->month);
        $year = (int) ($request->input('year') ?? Carbon::now()->year);
        $divisionId = $request->input('division_id');

        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
        $monthLabel = Carbon::createFromDate($year, $month, 1)->locale('id')->isoFormat('MMMM');

        $usersQuery = User::where('role', 'user')->with('division');
        if ($divisionId) {
            $usersQuery->where('division_id', $divisionId);
        }

        $users = $usersQuery->orderBy('username', 'asc')->get();

        $records = [];
        $summary = [
            'total_present' => 0,
            'total_late' => 0,
            'total_not_clocked_out' => 0,
            'total_izin' => 0,
            'total_sakit' => 0,
            'total_cuti' => 0,
            'total_attendances' => 0,
        ];

        foreach ($users as $user) {
            // Attendances in that month
            $userAttendances = Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->get();

            $presentCount = $userAttendances->where('status', 'present')->count();
            $lateCount = $userAttendances->where('status', 'late')->count();
            $notClockedOutCount = $userAttendances->whereNull('time_out')->count();
            $totalAtt = $userAttendances->count();

            // Approved leaves in that month
            $userLeaves = Leave::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                      ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth]);
                })
                ->get();

            $izinCount = $userLeaves->where('type', 'izin')->count();
            $sakitCount = $userLeaves->where('type', 'sakit')->count();
            $cutiCount = $userLeaves->where('type', 'cuti')->count();

            $records[] = [
                'user_id' => $user->id,
                'username' => $user->username,
                'no_hp' => $user->no_hp ?? '-',
                'division' => $user->division ? $user->division->name : '-',
                'present_count' => $presentCount,
                'late_count' => $lateCount,
                'not_clocked_out_count' => $notClockedOutCount,
                'izin_count' => $izinCount,
                'sakit_count' => $sakitCount,
                'cuti_count' => $cutiCount,
                'total_attendances' => $totalAtt,
            ];

            $summary['total_present'] += $presentCount;
            $summary['total_late'] += $lateCount;
            $summary['total_not_clocked_out'] += $notClockedOutCount;
            $summary['total_izin'] += $izinCount;
            $summary['total_sakit'] += $sakitCount;
            $summary['total_cuti'] += $cutiCount;
            $summary['total_attendances'] += $totalAtt;
        }

        return [
            'month' => $month,
            'year' => $year,
            'month_label' => $monthLabel,
            'records' => $records,
            'summary' => $summary,
        ];
    }
}
