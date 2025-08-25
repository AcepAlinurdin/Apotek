<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembelian - Transaksi #{{ $pembelian->id }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
            color: #333;
            font-size: 14px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #eee;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; font-size: 14px; }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }
        .details-grid div strong {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-size: 12px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .total-row td {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #333;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #777;
        }
        @media print {
            body { margin: 0; }
            .container { border: none; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>APOTEK PARAKAN MUNCANG</h1>
            <p>Jl. Raya Parakan Muncang No. XX, Kabupaten Sumedang</p>
            <h2>BUKTI PEMBELIAN OBAT</h2>
        </div>

        <div class="details-grid">
            <div>
                <strong>Supplier:</strong>
                <span>{{ $pembelian->supplier->nama_supplier ?? 'N/A' }}</span>
            </div>
            <div>
                <strong>Tanggal Pembelian:</strong>
                <span>{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->isoFormat('LL') }}</span>
            </div>
            <div>
                <strong>No. Transaksi:</strong>
                <span>PEM-{{ str_pad($pembelian->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div>
                <strong>Status:</strong>
                <span>{{ $pembelian->status }}</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th class="text-right">Jumlah (pcs)</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pembelian->detailPembelians as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $detail->obat->nama_obat }}</td>
                    <td class="text-right">{{ $detail->jumlah }}</td>
                    <td class="text-right">Rp {{ number_format($detail->harga_beli_satuan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-right">TOTAL PEMBAYARAN</td>
                    <td class="text-right">Rp {{ number_format($pembelian->total_harga, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <p>Dicetak oleh: {{ $pembelian->pegawai->name ?? 'N/A' }} pada {{ now()->isoFormat('LLLL') }}</p>
            <p>Terima kasih.</p>
        </div>
    </div>

    <script>
        // Otomatis memunculkan dialog print saat halaman dimuat
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>