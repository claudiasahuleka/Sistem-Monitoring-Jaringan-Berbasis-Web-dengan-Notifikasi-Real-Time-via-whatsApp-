<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Monitoring Infrastruktur Jaringan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5pt;
            color: #000000;
            line-height: 1.4;
            padding: 20pt 25pt;
            background-color: #ffffff;
        }

        /* ===== HEADER BACKGROUND TEAL ===== */
        .header {
            text-align: center;
            background-color: #10617a;
            padding: 16pt 12pt;
            border-radius: 6px;
            margin-bottom: 18pt;
        }

        .h-title {
            font-size: 14pt;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .h-sub {
            font-size: 11pt;
            font-weight: bold;
            color: #e2f1f5;
            margin-top: 5px;
        }

        .h-sm {
            font-size: 8.5pt;
            color: #b3dfea;
            margin-top: 6px;
        }

        /* ===== SUMMARY WIDGET CARDS ===== */
        .sum-container {
            width: 100%;
            margin-bottom: 15pt;
        }

        .sum-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 7pt 0;
        }

        .sum-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 8pt 4pt;
            text-align: center;
            vertical-align: middle;
        }

        .card-total {
            border-left: 3.5pt solid #3b82f6;
        }

        .card-up {
            border-left: 3.5pt solid #22c55e;
        }

        .card-down {
            border-left: 3.5pt solid #ef4444;
        }

        .card-latency {
            border-left: 3.5pt solid #eab308;
        }

        .card-uptime {
            border-left: 3.5pt solid #14b8a6;
        }

        .sum-card .lbl {
            font-size: 7.5pt;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            display: block;
            margin-bottom: 4px;
        }

        .sum-card .num {
            font-size: 13pt;
            font-weight: bold;
            color: #000000;
            display: block;
        }

        /* ===== SECTION TITLE ===== */
        .stitle {
            color: #000000;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 6pt;
            letter-spacing: 0.3px;
        }

        /* ===== CHART BOX ===== */
        .cbox {
            border: 1px solid #cbd5e1;
            padding: 10pt;
            margin-bottom: 15pt;
            background: #ffffff;
            border-radius: 4px;
        }

        .chart-container {
            text-align: center;
            margin: 5pt 0;
        }

        .chart-container img {
            width: 100%;
            display: block;
        }

        /* ===== LEGEND BAR TEXT ===== */
        .legbar {
            border-top: 1px solid #cbd5e1;
            padding-top: 8pt;
            text-align: center;
            font-size: 8.5pt;
            margin-top: 5pt;
        }

        .cleg-title {
            font-weight: bold;
            color: #000000;
            margin-bottom: 6pt;
            text-transform: uppercase;
            font-size: 8.5pt;
            letter-spacing: 0.5px;
        }

        .legbar .li {
            display: inline-block;
            margin: 0 8pt;
            color: #000000;
            font-weight: bold;
        }

        .legbar .bx {
            display: inline-block;
            width: 9pt;
            height: 9pt;
            vertical-align: middle;
            margin-right: 4pt;
            border-radius: 1px;
        }

        /* ===== DATA TABLE DETAIL WITH FULL COLOR BACKGROUND ===== */
        .dtbl {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20pt;
        }

        .dtbl th {
            background: #10617a;
            color: #ffffff;
            border: 1px solid #94a3b8;
            padding: 9pt 8pt;
            font-size: 9.5pt;
            text-align: left;
            font-weight: bold;
        }

        .dtbl td {
            border: 1px solid #94a3b8;
            padding: 8pt 8pt;
            font-size: 9.5pt;
            color: #000000;
        }

        /* Kelas Latar Belakang Baris Penuh Sesuai Status Kualitas */
        .row-aktif {
            background-color: #dcfce7 !important;
            /* Hijau Pastel / Soft */
        }

        .row-lemah {
            background-color: #fef3c7 !important;
            /* Kuning Pastel / Soft */
        }

        .row-sangat-lemah {
            background-color: #ffedd5 !important;
            /* Orange Pastel / Soft */
        }

        .row-down {
            background-color: #fee2e2 !important;
            /* Merah Pastel / Soft */
        }

        /* Warna Teks Status Internal */
        .txt-aktif {
            color: #16a34a;
            font-weight: bold;
        }

        .txt-lemah {
            color: #b45309;
            font-weight: bold;
        }

        .txt-sangat-lemah {
            color: #c2410c;
            font-weight: bold;
        }

        .txt-down {
            color: #dc2626;
            font-weight: bold;
        }

        /* ===== APPROVAL SECTION ===== */
        .appr {
            margin-top: 25pt;
            page-break-inside: avoid;
            width: 100%;
        }

        .appr-tbl {
            width: 100%;
            border-collapse: collapse;
        }

        .appr-tbl td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0;
            border: none;
        }

        .sig-text {
            font-size: 9pt;
            color: #000000;
            margin-bottom: 45pt;
        }

        .sig-line {
            font-size: 9pt;
            font-weight: bold;
            color: #000000;
            text-decoration: underline;
        }

        .sig-nip {
            font-size: 8.5pt;
            color: #475569;
            margin-top: 2px;
        }
    </style>
</head>

<body>

    @php
        // ---- FUNGSIONAL LOGIKA PENENTUAN KELAS BARIS DAN TEKS STATUS ----
        function getStatusRowConfig($latency, $status)
        {
            $lat = (float) $latency;

            // Kondisi DOWN / RTO
            if ($status === 'down' || $lat == 0) {
                return [
                    'label' => 'Down ',
                    'row_class' => 'row-down',
                    'txt_class' => 'txt-down'
                ];
            }
            // Kondisi AKTIF / ONLINE BAIK (1 - 199 ms)
            if ($lat <= 199) {
                return [
                    'label' => 'Online (Baik)',
                    'row_class' => 'row-aktif',
                    'txt_class' => 'txt-aktif'
                ];
            }
            // Kondisi LEMAH (200 - 399 ms)
            if ($lat <= 399) {
                return [
                    'label' => 'Lemah',
                    'row_class' => 'row-lemah',
                    'txt_class' => 'txt-lemah'
                ];
            }
            // Kondisi SANGAT LEMAH (400+ ms)
            return [
                'label' => 'Sangat Lemah',
                'row_class' => 'row-sangat-lemah',
                'txt_class' => 'txt-sangat-lemah'
            ];
        }

        // ---- PROSES OTOMATIS SKALA GRAFIK SUMBU Y ----
        $maxRaw = collect($devices)->where('status', '!=', 'down')
            ->map(fn($d) => (float) ($d->response_time ?? 0))
            ->filter(fn($v) => $v > 0)->max() ?? 100;
        $maxRaw = max((float) $maxRaw, 100);

        if ($maxRaw <= 200) {
            $maxY = 200;
            $yStep = 40;
        } elseif ($maxRaw <= 400) {
            $maxY = 400;
            $yStep = 80;
        } else {
            $maxY = (int) ceil($maxRaw / 200) * 200;
            $yStep = $maxY / 5;
        }
        $yTicks = (int) ($maxY / $yStep);

        // ---- CONFIGURASI GD IMAGE CHART ----
        $devCount = count($devices);
        $PAD_L = 50;
        $PAD_R = 50;
        $PAD_T = 25;
        $PAD_B = 40;
        $IMG_W = 850;
        $IMG_H = 320;

        $PLOT_W = $IMG_W - $PAD_L - $PAD_R;
        $PLOT_H = $IMG_H - $PAD_T - $PAD_B;
        $colW = $devCount > 0 ? $PLOT_W / $devCount : $PLOT_W;

        $im = imagecreatetruecolor($IMG_W, $IMG_H);
        $cWhite = imagecolorallocate($im, 255, 255, 255);
        $cGrid = imagecolorallocate($im, 226, 232, 240);
        $cAxis = imagecolorallocate($im, 71, 85, 105);
        $cTextBlack = imagecolorallocate($im, 0, 0, 0);

        $cGreen = imagecolorallocate($im, 34, 197, 94);
        $cYellow = imagecolorallocate($im, 234, 179, 8);
        $cOrange = imagecolorallocate($im, 249, 115, 22);
        $cRed = imagecolorallocate($im, 239, 68, 68);

        imagefilledrectangle($im, 0, 0, $IMG_W - 1, $IMG_H - 1, $cWhite);

        for ($i = 0; $i <= $yTicks; $i++) {
            $val = $i * $yStep;
            $yPos = $PAD_T + $PLOT_H - (int) (($val / $maxY) * $PLOT_H);
            if ($i > 0) {
                imageline($im, $PAD_L, $yPos, $PAD_L + $PLOT_W, $yPos, $cGrid);
            }
            imageline($im, $PAD_L - 4, $yPos, $PAD_L, $yPos, $cAxis);
            $lbl = (string) $val;
            $tw = imagefontwidth(2) * strlen($lbl);
            imagestring($im, 2, $PAD_L - $tw - 6, $yPos - 6, $lbl, $cTextBlack);
        }

        imageline($im, $PAD_L, $PAD_T, $PAD_L, $PAD_T + $PLOT_H, $cAxis);
        imageline($im, $PAD_L, $PAD_T + $PLOT_H, $PAD_L + $PLOT_W, $PAD_T + $PLOT_H, $cAxis);

        if ($maxY >= 200) {
            $y200 = $PAD_T + $PLOT_H - (int) ((200 / $maxY) * $PLOT_H);
            for ($x = $PAD_L; $x < $PAD_L + $PLOT_W; $x += 8) {
                imageline($im, $x, $y200, min($x + 4, $PAD_L + $PLOT_W), $y200, $cYellow);
            }
            imagestring($im, 2, $PAD_L + $PLOT_W + 5, $y200 - 6, '200ms', $cTextBlack);
        }

        $totalDevW = $devCount * $colW;
        $startOffset = ($PLOT_W - $totalDevW) / 2;

        foreach ($devices as $idx => $device) {
            $lat = (float) ($device->response_time ?? 0);
            $isDown = ($device->status === 'down' || $lat == 0);
            $cx = (int) ($PAD_L + $startOffset + ($idx + 0.5) * $colW);
            $baseY = $PAD_T + $PLOT_H;

            if ($isDown) {
                imagesetthickness($im, 2);
                imageline($im, $cx - 4, $baseY - 8, $cx + 4, $baseY, $cRed);
                imageline($im, $cx - 4, $baseY, $cx + 4, $baseY - 8, $cRed);
                imagesetthickness($im, 1);
                imagestring($im, 2, $cx - 12, $baseY - 22, 'RTO', $cRed);
            } else {
                $dc = ($lat <= 199) ? $cGreen : (($lat <= 399) ? $cYellow : $cOrange);
                $dotY = $PAD_T + $PLOT_H - (int) (($lat / $maxY) * $PLOT_H);

                imagesetthickness($im, 3);
                imageline($im, $cx, $baseY, $cx, $dotY, $dc);
                imagesetthickness($im, 1);
                imagefilledellipse($im, $cx, $dotY, 12, 12, $dc);

                $lbl = round($lat) . 'ms';
                $tw = imagefontwidth(2) * strlen($lbl);
                imagestring($im, 2, $cx - (int) ($tw / 2), $dotY - 15, $lbl, $cTextBlack);
            }

            imageline($im, $cx, $baseY, $cx, $baseY + 4, $cAxis);
            $name = $device->name;
            $twName = imagefontwidth(2) * strlen($name);
            imagestring($im, 2, $cx - (int) ($twName / 2), $baseY + 8, $name, $cTextBlack);
        }

        ob_start();
        imagepng($im);
        $chartB64 = base64_encode(ob_get_clean());
        imagedestroy($im);
    @endphp

    {{-- ===== KOP REKAP LAPORAN TEAL ===== --}}
    <div class="header">
        <div class="h-title">Laporan Monitoring Infrastruktur Jaringan</div>
        <div class="h-sub">Dinas Tenaga Kerja dan Transmigrasi Provinsi Maluku</div>
        <div class="h-sm">Periode: {{ \Carbon\Carbon::parse($start)->format('d/m/Y') }} s.d.
            {{ \Carbon\Carbon::parse($end)->format('d/m/Y') }} | Generated: {{ now()->format('d F Y H:i:s') }}
        </div>
    </div>

    {{-- ===== CARD SUMMARY BOX ===== --}}
    <div class="sum-container">
        <table class="sum-table">
            <tr>
                <td class="sum-card card-total">
                    <span class="lbl">Total Cek</span>
                    <span class="num">{{ $summary['total_logs'] }}</span>
                </td>
                <td class="sum-card card-up">
                    <span class="lbl">Online </span>
                    <span class="num">{{ $summary['total_up'] }}</span>
                </td>
                <td class="sum-card card-down">
                    <span class="lbl">Downtime </span>
                    <span class="num">{{ $summary['total_down'] }}</span>
                </td>
                <td class="sum-card card-latency">
                    <span class="lbl">Avg Latency</span>
                    <span class="num">{{ $summary['avg_response'] }} ms</span>
                </td>
                <td class="sum-card card-uptime">
                    <span class="lbl">Uptime Kinerja</span>
                    <span class="num">{{ $summary['uptime_pct'] }}%</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- ===== BOX GRAFIK DENGAN TEKS TAJAM ===== --}}
    <div class="cbox">
        <div class="stitle" style="text-align: center; margin-bottom: 4pt;">Status Respon Infrastruktur Jaringan</div>
        <div class="chart-container">
            <img src="data:image/png;base64,{{ $chartB64 }}" alt="Grafik Latency" />
        </div>
        <div class="legbar">
            <div class="cleg-title">Keterangan Status Berdasarkan Latency</div>
            <span class="li"><span class="bx" style="background:#22c55e;"></span> Online (baik) (0&ndash;199 ms)</span>
            <span class="li"><span class="bx" style="background:#eab308;"></span> Lemah (200&ndash;399 ms)</span>
            <span class="li"><span class="bx" style="background:#f97316;"></span> Sangat Lemah (400+ ms)</span>
            <span class="li"><span class="bx" style="background:#ef4444;"></span> Down Tidak Respon (0 ms)</span>
        </div>
    </div>

    {{-- ===== DATA TABLE DETAIL (WARNA BACKGROUND PENUH SEPERTI CONTOH GAMBAR) ===== --}}
    <div class="stitle">Data Detail Infrastruktur Jaringan</div>
    <table class="dtbl">
        <thead>
            <tr>
                <th>Nama Perangkat</th>
                <th>IP Address</th>
                <th>Lokasi</th>
                <th>Status Kualitas</th>
                <th>Latency </th>
                <th>Uptime Historis</th>
            </tr>
        </thead>
        <tbody>
            @foreach($devices as $device)
                @php
                    $lat = (float) ($device->response_time ?? 0);
                    // Ambil konfigurasi nama label, warna baris, dan warna teks status kualitas
                    $cfg = getStatusRowConfig($lat, $device->status);
                @endphp
                <tr class="{{ $cfg['row_class'] }}">
                    <td style="font-weight: bold; vertical-align: middle;">{{ $device->name }}</td>
                    <td style="vertical-align: middle;">{{ $device->ip_address }}</td>
                    <td style="vertical-align: middle;">{{ $device->location ?? 'lantai 1' }}</td>
                    <td style="vertical-align: middle;">
                        <span class="{{ $cfg['txt_class'] }}">{{ $cfg['label'] }}</span>
                    </td>
                    <td style="font-weight: bold; vertical-align: middle;">
                        {{ ($device->status === 'down' || $lat == 0) ? '0 ms' : round($lat) . ' ms' }}
                    </td>
                    <td style="vertical-align: middle;">{{ $device->uptime_percent ?? 0 }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ===== LEMBAR TANDA TANGAN ===== --}}
    <div class="appr">
        <table class="appr-tbl">
            <tr>
                <td>
                    <div class="sig-text">Mengetahui,<br><strong>Kepala Bidang IT</strong></div>
                    <div class="sig-line">Ahmad Fauzi, S.Kom</div>
                    <div class="sig-nip">NIP. 198501152010011001</div>
                </td>
                <td>
                    <div class="sig-text"><br><strong>Staf IT Senior</strong></div>
                    <div class="sig-line">Siti Rahayu, M.T.</div>
                    <div class="sig-nip">NIP. 198703202012022002</div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>