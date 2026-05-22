<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #1e3a8a;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #64748b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 13px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .footer {
            margin-top: 30px;
            font-size: 11px;
            color: #94a3b8;
            text-align: right;
        }
        .print-btn-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .print-btn {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .print-btn:hover { background: #1d4ed8; }
        
        @media print {
            .print-btn-container { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="print-btn-container">
        <button class="print-btn" onclick="window.print()">🖨️ Cetak / Simpan sebagai PDF</button>
        <br><br>
        <a href="javascript:history.back()" style="color:#64748b; text-decoration:none; font-size:13px;">&larr; Kembali ke Dashboard</a>
    </div>

    <div class="header">
        <h1>Sistem Informasi Mango GDD</h1>
        <p>{{ $title }}</p>
        <p>Dicetak pada: {{ now()->timezone('Asia/Jakarta')->format('d F Y H:i') }} WIB</p>
    </div>

    @if($role === 'admin')
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kebun</th>
                    <th>Pemilik (Petani)</th>
                    <th>Lokasi</th>
                    <th>Varietas</th>
                    <th>Progress GDD</th>
                    <th>Status Panen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kebuns as $i => $k)
                    @php
                        $status = 'Sedang Berkembang';
                        if ($k->gdd_progress < 30) $status = 'Awal Pertumbuhan';
                        elseif ($k->gdd_progress < 100) $status = 'Hampir Panen';
                        if ($k->is_siap_panen) $status = 'Siap Panen';
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $k->nama_kebun }}</td>
                        <td>{{ $k->user->name ?? '-' }}</td>
                        <td>{{ $k->lokasi }}</td>
                        <td>{{ $k->jenis_mangga }}</td>
                        <td>{{ round($k->total_gdd, 1) }} / {{ $k->target_gdd }}</td>
                        <td><strong>{{ $status }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($role === 'petani')
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kebun</th>
                    <th>Tanggal Berbunga</th>
                    <th>Tanggal Panen</th>
                    <th>Lama Siklus</th>
                    <th>Total GDD</th>
                    <th>Hasil (Kg)</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayats as $i => $r)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $r->kebun->nama_kebun ?? '-' }}</td>
                        <td>{{ $r->tanggal_berbunga ? \Carbon\Carbon::parse($r->tanggal_berbunga)->format('d/m/Y') : '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($r->tanggal_panen)->format('d/m/Y') }}</td>
                        <td>{{ $r->tanggal_berbunga ? \Carbon\Carbon::parse($r->tanggal_berbunga)->diffInDays(\Carbon\Carbon::parse($r->tanggal_panen)) . ' Hari' : '-' }}</td>
                        <td>{{ round($r->total_gdd, 1) }}</td>
                        <td>{{ round($r->hasil_panen_kg, 1) }} Kg</td>
                        <td>{{ $r->catatan ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($role === 'pengepul')
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kebun</th>
                    <th>Nama Petani</th>
                    <th>Lokasi/Alamat</th>
                    <th>Varietas Mangga</th>
                    <th>Jumlah Pohon</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kebuns as $i => $k)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $k->nama_kebun }}</td>
                        <td>{{ $k->user->name ?? '-' }}</td>
                        <td>{{ $k->lokasi }}</td>
                        <td>{{ $k->jenis_mangga }}</td>
                        <td>{{ $k->jumlah_pohon }}</td>
                        <td><strong>SIAP PANEN</strong> ({{ round($k->total_gdd, 1) }} GDD)</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>Dokumen ini dicetak otomatis dari Sistem Mango GDD. Sah dan valid.</p>
    </div>

    <script>
        // Otomatis buka dialog print saat halaman dimuat
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
