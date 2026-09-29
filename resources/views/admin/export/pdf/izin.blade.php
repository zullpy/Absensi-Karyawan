<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengajuan Izin & Cuti Karyawan</title>
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
            border-bottom: 2px solid #047857;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }
        .company-name {
            font-size: 15pt;
            font-weight: bold;
            color: #047857;
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
            color: #047857;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        table.data-table th {
            background-color: #047857;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            padding: 7px 6px;
            border: 1px solid #047857;
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
        .badge-approved { background-color: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-rejected { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .badge-pending { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

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

    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="company-name">{{ $office->name ?? 'Aplikasi Absensi Karyawan' }}</div>
                <div class="report-title">LAPORAN PENGAJUAN IZIN & CUTI KARYAWAN</div>
                <div class="report-subtitle">Periode: {{ $startDate }} s/d {{ $endDate }}</div>
            </td>
            <td style="text-align: right; vertical-align: top; font-size: 8pt; color: #64748b;">
                Tanggal Unduh: <strong>{{ $printedAt }}</strong><br>
                Admin Pembuat: <strong>{{ $printedBy }}</strong>
            </td>
        </tr>
    </table>

    <div style="margin-bottom: 10px;">
        <span class="stat-box">Total Pengajuan: <span class="stat-val">{{ $stats['total'] }}</span></span>
        <span class="stat-box">Disetujui: <span class="stat-val" style="color: #16a34a;">{{ $stats['approved'] }}</span></span>
        <span class="stat-box">Menunggu: <span class="stat-val" style="color: #d97706;">{{ $stats['pending'] }}</span></span>
        <span class="stat-box">Ditolak: <span class="stat-val" style="color: #dc2626;">{{ $stats['rejected'] }}</span></span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="10%">Diajukan</th>
                <th width="16%">Nama Karyawan</th>
                <th width="13%">Divisi</th>
                <th width="8%">Jenis</th>
                <th width="16%">Rentang Tanggal</th>
                <th width="8%">Durasi</th>
                <th width="15%">Alasan</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($leaves as $index => $leave)
                @php
                    $start = \Carbon\Carbon::parse($leave->start_date);
                    $end = \Carbon\Carbon::parse($leave->end_date);
                    $days = $start->diffInDays($end) + 1;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $leave->created_at ? \Carbon\Carbon::parse($leave->created_at)->format('d/m/Y') : '-' }}</td>
                    <td class="text-left"><strong>{{ $leave->user ? $leave->user->username : '-' }}</strong></td>
                    <td class="text-left">{{ $leave->user && $leave->user->division ? $leave->user->division->name : '-' }}</td>
                    <td class="text-center"><strong>{{ ucfirst($leave->type) }}</strong></td>
                    <td class="text-center">{{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $days }} Hari</td>
                    <td class="text-left">{{ $leave->reason }}</td>
                    <td class="text-center">
                        @if ($leave->status === 'approved')
                            <span class="badge badge-approved">Disetujui</span>
                        @elseif ($leave->status === 'rejected')
                            <span class="badge badge-rejected">Ditolak</span>
                        @else
                            <span class="badge badge-pending">Menunggu</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada catatan pengajuan izin pada periode dan kriteria filter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

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
