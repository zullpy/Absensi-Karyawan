<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekapitulasi Kehadiran Bulanan</title>
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
        .btn-indigo { background-color: #4f46e5; color: #ffffff; }
        .btn-indigo:hover { background-color: #4338ca; }
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
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 15px;
            margin-bottom: 18px;
        }
        .header-title h1 {
            font-size: 18px;
            font-weight: 800;
            color: #3730a3;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title h2 { font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 3px; }
        .header-title p { font-size: 11px; color: #64748b; margin-top: 2px; }
        .header-meta { text-align: right; font-size: 10px; color: #64748b; }
        table.table-report { width: 100%; border-collapse: collapse; font-size: 11px; }
        table.table-report th {
            background-color: #4338ca;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            padding: 8px 6px;
            border: 1px solid #4338ca;
            text-align: center;
        }
        table.table-report td { padding: 6px 8px; border: 1px solid #cbd5e1; vertical-align: middle; }
        table.table-report tbody tr:nth-child(even) { background-color: #f8fafc; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .total-row { background-color: #e0e7ff !important; font-weight: 700; }
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
            <strong style="font-size: 14px; color: #1e293b;">Pratinjau Cetak Rekapitulasi Kehadiran Bulanan</strong>
            <p style="font-size: 12px; color: #64748b;">Ringkasan evaluasi kehadiran bulanan per karyawan untuk arsip & HRD.</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn btn-indigo">
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
                <h2>REKAPITULASI KEHADIRAN BULANAN KARYAWAN</h2>
                <p>Periode: <strong>{{ $data['month_label'] }} {{ $data['year'] }}</strong></p>
            </div>
            <div class="header-meta">
                Tanggal Unduh: <strong>{{ $printedAt }}</strong><br>
                Admin Pembuat: <strong>{{ $printedBy }}</strong>
            </div>
        </div>

        <table class="table-report">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="18%">Nama Karyawan</th>
                    <th width="14%">Divisi</th>
                    <th width="12%">No. HP</th>
                    <th width="8%">Tepat Waktu</th>
                    <th width="8%">Terlambat</th>
                    <th width="9%">Belum Pulang</th>
                    <th width="7%">Izin</th>
                    <th width="7%">Sakit</th>
                    <th width="7%">Cuti</th>
                    <th width="8%">Total Hadir</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['records'] as $index => $rec)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-left"><strong>{{ $rec['username'] }}</strong></td>
                        <td class="text-left">{{ $rec['division'] }}</td>
                        <td class="text-center">{{ $rec['no_hp'] }}</td>
                        <td class="text-center" style="color: #16a34a; font-weight: 700;">{{ $rec['present_count'] }}</td>
                        <td class="text-center" style="color: #dc2626; font-weight: 700;">{{ $rec['late_count'] }}</td>
                        <td class="text-center" style="color: #d97706;">{{ $rec['not_clocked_out_count'] }}</td>
                        <td class="text-center">{{ $rec['izin_count'] }}</td>
                        <td class="text-center">{{ $rec['sakit_count'] }}</td>
                        <td class="text-center">{{ $rec['cuti_count'] }}</td>
                        <td class="text-center" style="font-weight: 700; background-color: #f1f5f9;">{{ $rec['total_attendances'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center" style="padding: 25px; color: #94a3b8;">
                            Tidak ada data karyawan pada periode ini.
                        </td>
                    </tr>
                @endforelse
                @if (count($data['records']) > 0)
                    <tr class="total-row">
                        <td colspan="4" class="text-center">TOTAL REKAPITULASI ({{ count($data['records']) }} Karyawan)</td>
                        <td class="text-center">{{ $data['summary']['total_present'] }}</td>
                        <td class="text-center">{{ $data['summary']['total_late'] }}</td>
                        <td class="text-center">{{ $data['summary']['total_not_clocked_out'] }}</td>
                        <td class="text-center">{{ $data['summary']['total_izin'] }}</td>
                        <td class="text-center">{{ $data['summary']['total_sakit'] }}</td>
                        <td class="text-center">{{ $data['summary']['total_cuti'] }}</td>
                        <td class="text-center">{{ $data['summary']['total_attendances'] }}</td>
                    </tr>
                @endif
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
