<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Pengajuan Izin & Cuti</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Figtree', sans-serif, system-ui;
            font-size: 11px;
            color: #1e293b;
            background-color: #f8fafc;
            padding: 20px;
        }
        .no-print-bar {
            position: sticky;
            top: 10px;
            z-index: 50;
            max-width: 1000px;
            margin: 0 auto 20px auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 15px -3px rgba(0,0,0,0.07);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }
        .btn-emerald { background-color: #059669; color: #ffffff; }
        .btn-emerald:hover { background-color: #047857; }
        .btn-secondary { background-color: #f1f5f9; color: #475569; }
        .sheet {
            max-width: 1050px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px 35px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-radius: 8px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #059669;
            padding-bottom: 15px;
            margin-bottom: 18px;
        }
        .header-title h1 {
            font-size: 18px;
            font-weight: 800;
            color: #065f46;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title h2 { font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 3px; }
        .header-title p { font-size: 11px; color: #64748b; margin-top: 2px; }
        .header-meta { text-align: right; font-size: 10px; color: #64748b; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }
        .stat-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
        }
        .stat-card .label { font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: 600; }
        .stat-card .val { font-size: 16px; font-weight: 800; color: #047857; margin-top: 2px; }
        table.table-report { width: 100%; border-collapse: collapse; font-size: 11px; }
        table.table-report th {
            background-color: #047857;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            padding: 8px 6px;
            border: 1px solid #047857;
            text-align: center;
        }
        table.table-report td { padding: 6px 8px; border: 1px solid #cbd5e1; vertical-align: middle; }
        table.table-report tbody tr:nth-child(even) { background-color: #f8fafc; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .badge { display: inline-block; padding: 2px 7px; font-size: 9.5px; font-weight: 700; border-radius: 4px; }
        .badge-approved { background-color: #dcfce7; color: #15803d; }
        .badge-rejected { background-color: #fee2e2; color: #b91c1c; }
        .badge-pending { background-color: #fef3c7; color: #b45309; }
        .signatures { margin-top: 40px; display: flex; justify-content: flex-end; page-break-inside: avoid; }
        .sign-box { text-align: center; width: 250px; font-size: 11px; }
        .sign-line { height: 65px; }
        @media print {
            body { background: #ffffff; padding: 0; }
            .no-print-bar { display: none !important; }
            .sheet { box-shadow: none; padding: 0; max-width: 100%; }
            @page { size: A4 landscape; margin: 1cm; }
        }
    </style>
</head>
<body>
    <div class="no-print-bar">
        <div>
            <strong style="font-size: 14px; color: #1e293b;">Pratinjau Cetak Pengajuan Izin & Cuti</strong>
            <p style="font-size: 12px; color: #64748b;">Siap dicetak atau disimpan sebagai PDF melalui dialog browser.</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn btn-emerald">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Dokumen
            </button>
            <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
        </div>
    </div>

    <div class="sheet">
        <div class="header">
            <div class="header-title">
                <h1>{{ $office->name ?? 'Aplikasi Absensi' }}</h1>
                <h2>LAPORAN PENGAJUAN IZIN & CUTI KARYAWAN</h2>
                <p>Periode: <strong>{{ $startDate }}</strong> s/d <strong>{{ $endDate }}</strong></p>
            </div>
            <div class="header-meta">
                Tanggal Unduh: <strong>{{ $printedAt }}</strong><br>
                Admin Pembuat: <strong>{{ $printedBy }}</strong>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">Total Pengajuan</div>
                <div class="stat-val">{{ $stats['total'] }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Disetujui</div>
                <div class="stat-val" style="color: #16a34a;">{{ $stats['approved'] }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Menunggu</div>
                <div class="stat-val" style="color: #d97706;">{{ $stats['pending'] }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Ditolak</div>
                <div class="stat-val" style="color: #dc2626;">{{ $stats['rejected'] }}</div>
            </div>
        </div>

        <table class="table-report">
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
                        <td colspan="9" class="text-center" style="padding: 25px; color: #94a3b8;">
                            Tidak ada catatan izin yang sesuai kriteria filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="signatures">
            <div class="sign-box">
                <div>Mengetahui,</div>
                <div style="font-weight: 700; margin-top: 3px;">Pimpinan / HRD Manager</div>
                <div class="sign-line"></div>
                <div style="text-decoration: underline; font-weight: 700;">( ________________________ )</div>
                <div style="color: #64748b; font-size: 10px; margin-top: 3px;">NIP / ID: .................................</div>
            </div>
        </div>
    </div>
</body>
</html>
