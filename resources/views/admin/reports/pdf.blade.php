<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - {{ $period->format('M Y') }}</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; color: #1f2937; font-size: 11px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .title { font-size: 20px; font-weight: bold; margin-bottom: 5px; }
        .subtitle { font-size: 12px; color: #4b5563; }
        
        .summary-box { 
            background: #f3f4f6; 
            padding: 12px; 
            margin-bottom: 20px; 
            border-radius: 5px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .summary-item { margin-bottom: 5px; font-size: 11px; }
        .summary-label { font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #9ca3af; padding: 6px; text-align: left; font-size: 10px; }
        th { background: #f3f4f6; font-weight: bold; }
        .total-row { font-weight: bold; background: #e5e7eb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        @media print {
            .no-print { display: none; }
            body { padding: 10px; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <div class="title">LAPORAN PENJUALAN BULANAN</div>
        <div class="subtitle">Periode: {{ $period->locale('id')->isoFormat('MMMM Y') }}</div>
        <div class="subtitle" style="margin-top: 5px;">Dicetak Tanggal: {{ date('d/m/Y H:i') }}</div>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <span class="summary-label">Total Transaksi:</span> {{ $summary->total_transactions }} transaksi
        </div>
        <div class="summary-item">
            <span class="summary-label">Barang Terjual:</span> {{ number_format($summary->sold_items, 0, ',', '.') }} item
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Penjualan:</span> Rp {{ number_format($summary->total_sales, 0, ',', '.') }}
        </div>
        <div class="summary-item">
            <span class="summary-label">Laba Bersih:</span> Rp {{ number_format($summary->profit, 0, ',', '.') }}
        </div>
    </div>

    <h3 style="font-size: 13px; margin-bottom: 10px;">Detail Barang Terjual</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Barang</th>
                <th class="text-right">Harga Awal</th>
                <th class="text-right">Harga Jual</th>
                <th class="text-center">Terjual</th>
                <th class="text-right">Sub Total</th>
                <th class="text-right">Laba/Rugi</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Group items by product
                $productSales = [];
                foreach($transactions as $trx) {
                    foreach($trx->details as $detail) {
                        $productId = $detail->product_id;
                        if (!isset($productSales[$productId])) {
                            $productSales[$productId] = [
                                'name' => $detail->product->name,
                                'cost_price' => $detail->product->cost_price,
                                'selling_price' => $detail->price,
                                'quantity' => 0,
                                'subtotal' => 0,
                                'profit' => 0,
                            ];
                        }
                        $productSales[$productId]['quantity'] += $detail->quantity;
                        $productSales[$productId]['subtotal'] += $detail->total;
                        $productSales[$productId]['profit'] += ($detail->price - $detail->product->cost_price) * $detail->quantity;
                    }
                }
                
                $totalSubtotal = 0;
                $totalProfit = 0;
            @endphp
            
            @forelse($productSales as $key => $item)
            @php
                $totalSubtotal += $item['subtotal'];
                $totalProfit += $item['profit'];
            @endphp
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $item['name'] }}</td>
                <td class="text-right">Rp {{ number_format($item['cost_price'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item['selling_price'], 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($item['quantity'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                <td class="text-right" style="color: {{ $item['profit'] >= 0 ? '#10b981' : '#ef4444' }};">
                    Rp {{ number_format($item['profit'], 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">
                    Tidak ada data penjualan.
                </td>
            </tr>
            @endforelse
            
            @if(count($productSales) > 0)
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL:</td>
                <td class="text-right">Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</td>
                <td class="text-right" style="color: {{ $totalProfit >= 0 ? '#10b981' : '#ef4444' }};">
                    Rp {{ number_format($totalProfit, 0, ',', '.') }}
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    <div style="margin-top: 40px; text-align: right; margin-right: 50px;">
        <p>Mengetahui,</p>
        <br><br><br>
        <p>( Administrator )</p>
    </div>
</body>
</html>
