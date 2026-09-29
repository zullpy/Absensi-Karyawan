<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Karyawan</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm 1.5cm 1.5cm;
            size: a4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.35;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }
        .header-logo {
            width: 70px;
            vertical-align: middle;
        }
        .header-title {
            vertical-align: middle;
            text-align: left;
            padding-left: 10px;
        }
        .company-name {
            font-size: 15pt;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .report-title {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 3px;
            margin-bottom: 2px;
        }
        .report-subtitle {
            font-size: 8.5pt;
            color: #64748b;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 14px;
            font-size: 8.5pt;
        }
        .meta-table td {
            padding: 2px 4px;
        }
        .stat-box {
            display: inline-block;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 4px 10px;
            margin-right: 8px;
            font-size: 8pt;
        }
        .stat-val {
            font-weight: bold;
            color: #1e3a8a;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        table.data-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            padding: 7px 6px;
            border: 1px solid #1e3a8a;
            text-align: center;
        }
        table.data-table td {
            padding: 6px 6px;
            border: 1px solid #cbd5e1;
            font-size: 8.5pt;
            vertical-align: middle;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7.5pt;
            font-weight: bold;
            border-radius: 3px;
        }
        .badge-present { background-color: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-late { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .badge-pending { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-done { background-color: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }

        .signature-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            font-size: 8.5pt;
        }
        .signature-space {
            height: 55px;
        }
        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            font-size: 7.5pt;
            color: #94a3b8;
            text-align: right;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="footer">
        Hadirin Absensi System &bull; Dokumen dicetak otomatis pada {{ $printedAt }}
    </div>

    <!-- Header Kop -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="header-title">
                <div class="company-name">{{ $office->name ?? 'Aplikasi Absensi Karyawan' }}</div>
                <div class="report-title">LAPORAN REKAPITULASI PRESENSI HARIAN</div>
                <div class="report-subtitle">Periode: {{ $startDate }} s/d {{ $endDate }} | Titik Radius: {{ $office->radius_meters ?? 50 }} Meter</div>
            </td>
            <td style="text-align: right; vertical-align: top; font-size: 8pt; color: #64748b;">
                Tanggal Unduh: <strong>{{ $printedAt }}</strong><br>
                Admin Pembuat: <strong>{{ $printedBy }}</strong>
            </td>
        </tr>
    </table>

    <!-- Statistik Ringkas -->
    <div style="margin-bottom: 10px;">
        <span class="stat-box">Total Presensi: <span class="stat-val">{{ $stats['total'] }}</span></span>
        <span class="stat-box">Tepat Waktu: <span class="stat-val" style="color: #16a34a;">{{ $stats['present'] }}</span></span>
        <span class="stat-box">Terlambat: <span class="stat-val" style="color: #dc2626;">{{ $stats['late'] }}</span></span>
        <span class="stat-box">Belum Pulang: <span class="stat-val" style="color: #d97706;">{{ $stats['not_clocked_out'] }}</span></span>
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="11%">Tanggal</th>
                <th width="18%">Nama Karyawan</th>
                <th width="14%">Divisi</th>
                <th width="12%">Shift</th>
                <th width="9%">Masuk</th>
                <th width="9%">Pulang</th>
                <th width="10%">Durasi</th>
                <th width="13%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attendances as $index => $att)
                @php
                    $duration = '-';
                    if ($att->time_in && $att->time_out) {
                        $in = \Carbon\Carbon::parse($att->time_in);
                        $out = \Carbon\Carbon::parse($att->time_out);
                        $diff = $in->diff($out);
                        $duration = sprintf('%dj %dm', $diff->h, $diff->i);
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($att->date)->format('d/m/Y') }}</td>
                    <td class="text-left"><strong>{{ $att->user ? $att->user->username : '-' }}</strong></td>
                    <td class="text-left">{{ $att->user && $att->user->division ? $att->user->division->name : '-' }}</td>
                    <td class="text-center">{{ $att->shift ? $att->shift->name : 'Reguler' }}</td>
                    <td class="text-center">{{ $att->time_in ? \Carbon\Carbon::parse($att->time_in)->format('H:i') : '-' }}</td>
                    <td class="text-center">{{ $att->time_out ? \Carbon\Carbon::parse($att->time_out)->format('H:i') : '-' }}</td>
                    <td class="text-center">{{ $duration }}</td>
                    <td class="text-center">
                        @if ($att->status === 'late')
                            <span class="badge badge-late">Terlambat</span>
                        @else
                            <span class="badge badge-present">Tepat Waktu</span>
                        @endif
                        @if (!$att->time_out)
                            <span class="badge badge-pending">Belum Pulang</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada catatan presensi pada periode dan kriteria filter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Section -->
    <table class="signature-table" cellpadding="0" cellspacing="0">
        <tr>
            <td width="60%"></td>
            <td width="40%" class="signature-box">
                <div>Mengetahui,</div>
                <div style="font-weight: bold; margin-top: 3px;">Pimpinan / HRD Manager</div>
                <div class="signature-space"></div>
                <div style="text-decoration: underline; font-weight: bold;">( ________________________ )</div>
                <div style="color: #64748b; font-size: 8pt; margin-top: 3px;">NIP / ID: .................................</div>
            </td>
        </tr>
    </table>
</body>
</html>
