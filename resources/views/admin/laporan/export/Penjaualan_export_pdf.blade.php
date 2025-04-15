<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h3 {
            text-align: center;
        }
    </style>
</head>

<body>
    <h3>Laporan Penjualan</h3>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Faktur</th>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th>Member</th>
                <th>Total Bayar</th>
                <th>Metode</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penjualans as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->no_faktur }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tgl_faktur)->format('d/m/Y') }}</td>
                    <td>{{ $item->user->name ?? '-' }}</td>
                    <td>{{ $item->member->nama ?? '-' }}</td>
                    <td>Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($item->metode_pembayaran) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
