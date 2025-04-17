<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Absensi Kerja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            white-space: nowrap;
        }

        table th {
            background-color: #f2f2f2;
            font-size: 12px;
        }

        table td {
            font-size: 12px;
        }

        h1 {
            text-align: center;
        }
    </style>
</head>

<body>
    <h1>Daftar Absensi Kerja</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Karyawan</th>
                <th>Status Masuk</th>
                <th>Tanggal Masuk</th>
                <th>Waktu Masuk</th>
                <th>Waktu Selesai Kerja</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->user->name ?? 'Nama tidak ditemukan' }}</td>
                    <td>{{ $item->status_masuk }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('Y-m-d') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->waktu_masuk)->format('H:i:s') }}</td>
                    <td>{{ $item->waktu_selesai_kerja ? \Carbon\Carbon::parse($item->waktu_selesai_kerja)->format('H:i:s') : '-' }}
                    </td>
                    <td>
                        @if ($item->status_masuk == 'masuk')
                            Bekerja
                        @elseif ($item->status_masuk == 'cuti')
                            Cuti
                        @elseif ($item->status_masuk == 'sakit')
                            Sakit
                        @else
                            Tidak ada keterangan
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
