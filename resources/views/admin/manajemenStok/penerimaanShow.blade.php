@extends('template.admin')

@push('style')
    <style>
        .invoice-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 2px solid #ddd;
            border-radius: 10px;
            background: #fff;
            font-family: Arial, sans-serif;
        }

        /* KOP SURAT */
        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }

        .brand-icon {
            font-size: 50px;
            color: #007bff;
        }

        .brand-text {
            font-size: 22px;
            font-weight: bold;
            color: #007bff;
        }

        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }

        /* INFORMASI */
        .invoice-info {
            margin-bottom: 20px;
            font-size: 16px;
        }

        /* TABEL DETAIL */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .invoice-table th,
        .invoice-table td {
            border: 1px solid #ddd;
            padding: 10px;
            font-size: 14px;
        }

        .invoice-table th {
            background: #f5f5f5;
            text-align: center;
        }

        /* RINGKASAN */
        .invoice-summary {
            margin-top: 20px;
            font-size: 16px;
        }

        .text-right {
            text-align: right;
        }

        /* BUTTON PRINT */
        .btn-print {
            display: block;
            width: 100%;
            padding: 10px;
            font-size: 16px;
            text-align: center;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        .btn-print:hover {
            background: #0056b3;
        }

        .btn-back {
            display: inline-block;
            padding: 8px 12px;
            font-size: 14px;
            color: #fff;
            background: #dc3545;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 10px;
        }

        .btn-back:hover {
            background: #c82333;
        }

        .btn-back:hover {
            color: #000;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .sidebar {
                display: none;
            }

            .content-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }

            body,
            html {
                margin: 0;
                padding: 0;
            }

            .btn-back {
                display: none;
            }

            .invoice-container {
                width: 100%;
                max-width: none;
                border: none;
                padding: 0;
            }

            .invoice-table th,
            .invoice-table td {
                padding: 8px;
                font-size: 12px;
            }

            .invoice-summary {
                font-size: 14px;
            }

            .btn-print {
                display: none;
            }

            .invoice-table,
            .invoice-summary {
                page-break-inside: avoid;
            }
        }
    </style>
@endpush

@section('content')
    <div class="invoice-container">

        <a href="{{ route('penerimaan.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>

        <div class="invoice-header">
            <i class="fas fa-store brand-icon"></i>
            <div class="brand-text">Bian Mart</div>
            <h2 class="invoice-title">INVOICE PENERIMAAN</h2>
        </div>


        <div class="invoice-info">
            <p><strong>Kode Penerimaan:</strong> {{ $penerimaan->kode_penerimaan }}</p>
            <p><strong>Tanggal Masuk:</strong> {{ $penerimaan->tgl_masuk }}</p>
            <p><strong>Supplier:</strong> {{ $penerimaan->supplier->nama_supplier }}</p>
            <p><strong>Alamat Supplier:</strong> {{ $penerimaan->supplier->alamat ?? 'Alamat Tidak Didaftarkan  ' }}</p>
            <p><strong>Kasir:</strong> {{ $penerimaan->user->name }}</p>
        </div>

        <!-- DETAIL BARANG -->
        <h4>Detail Barang:</h4>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Harga Beli</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($penerimaan->detailPenerimaan as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $detail->produk->nama_barang }}</td>
                        <td class="text-center">{{ $detail->jumlah }}</td>
                        <td class="text-right">Rp {{ number_format($detail->harga_beli, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- RINGKASAN -->
        <div class="invoice-summary text-right">
            <h4><strong>Total: Rp {{ number_format($penerimaan->total, 0, ',', '.') }}</strong></h4>
        </div>

        <!-- BUTTON CETAK -->
        <button onclick="window.print()" class="btn-print">Cetak Invoice</button>
    </div>
@endsection
