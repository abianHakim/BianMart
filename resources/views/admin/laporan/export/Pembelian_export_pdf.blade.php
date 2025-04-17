<!DOCTYPE html>
<html>

<head>
    <title>Laporan Pembelian</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        h3,
        p {
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>
    <h3>Laporan Pembelian Barang</h3>
    @if ($tanggalAwal && $tanggalAkhir)
        <p>Periode: {{ \Carbon\Carbon::parse($tanggalAwal)->format('d-m-Y') }} s/d
            {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d-m-Y') }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Harga Beli</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($laporanPembelian as $penerimaan)
                @foreach ($penerimaan->detailPenerimaan as $detail)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($penerimaan->tgl_masuk)->format('d-m-Y') }}</td>
                        <td>{{ $penerimaan->supplier->nama_supplier ?? '-' }}</td>
                        <td>{{ $detail->produk->nama_barang ?? '-' }}</td>
                        <td>{{ $detail->jumlah }}</td>
                        <td>Rp {{ number_format($detail->harga_beli, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($detail->jumlah * $detail->harga_beli, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>

</html>
