<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Kehadiran Bulanan Karyawan</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm 1.5cm 1.5cm;
            size: a4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            color: #1e293b;
            line-height: 1.35;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #4338ca;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }
        .company-name {
            font-size: 15pt;
            font-weight: bold;
            color: #4338ca;
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
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        table.data-table th {
            background-color: #4338ca;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7.5pt;
            padding: 7px 5px;
            border: 1px solid #4338ca;
            text-align: center;
        }
        table.data-table td {
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            font-size: 8pt;
            vertical-align: middle;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .total-row {
            background-color: #e0e7ff !important;
            font-weight: bold;
        }
        .total-row td {
            border-top: 2px solid #6366f1;
            border-bottom: 2px solid #6366f1;
        }
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
                <div class="report-title">REKAPITULASI KEHADIRAN BULANAN KARYAWAN</div>
                <div class="report-subtitle">Periode: {{ $data['month_label'] }} {{ $data['year'] }}</div>
            </td>
            <td style="text-align: right; vertical-align: top; font-size: 8pt; color: #64748b;">
                Tanggal Unduh: <strong>{{ $printedAt }}</strong><br>
                Admin Pembuat: <strong>{{ $printedBy }}</strong>
            </td>
        </tr>
    </table>

    <table class="data-table">
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
                    <td class="text-center" style="color: #16a34a; font-weight: bold;">{{ $rec['present_count'] }}</td>
                    <td class="text-center" style="color: #dc2626; font-weight: bold;">{{ $rec['late_count'] }}</td>
                    <td class="text-center" style="color: #d97706;">{{ $rec['not_clocked_out_count'] }}</td>
                    <td class="text-center">{{ $rec['izin_count'] }}</td>
                    <td class="text-center">{{ $rec['sakit_count'] }}</td>
                    <td class="text-center">{{ $rec['cuti_count'] }}</td>
                    <td class="text-center" style="font-weight: bold; background-color: #f1f5f9;">{{ $rec['total_attendances'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px; color: #94a3b8;">
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
