<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian - {{ $laporan->tanggal }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .info-box {
            background: #f3f4f6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #6b7280;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>LAPORAN HARIAN PENJUALAN</h1>
        <p>Point of Sale System</p>
    </div>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Tanggal:</span>
            <span>{{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('d F Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Transaksi:</span>
            <span>{{ $laporan->total_transaksi }} transaksi</span>
        </div>
        <div class="info-row">
            <span class="info-label">Barang Terjual:</span>
            <span>{{ number_format($laporan->barang_terjual, 0, ',', '.') }} item</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Penjualan:</span>
            <span>Rp {{ number_format($laporan->total_penjualan, 0, ',', '.') }}</span>
        </div>
    </div>

    <h3>Detail Transaksi</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">ID Transaksi</th>
                <th style="width: 15%;">Waktu</th>
                <th style="width: 30%;">Kasir</th>
                <th style="width: 25%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi as $key => $trx)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $trx->nomor_transaksi }}</td>
                <td>{{ \Carbon\Carbon::parse($trx->created_at)->format('H:i') }}</td>
                <td>{{ $trx->kasir_name }}</td>
                <td class="text-right">Rp {{ number_format($trx->sub_total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL PENJUALAN:</td>
                <td class="text-right">Rp {{ number_format($laporan->total_penjualan, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>
</body>
</html>
