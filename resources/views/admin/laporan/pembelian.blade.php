@extends('template.admin')

@push('style')
    <style>
        .filter-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .filter-form {
            display: flex;
            align-items: center;
        }

        .filter-form label {
            margin-right: 10px;
        }

        .export-buttons {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Pembelian</h1>
    </div>

    <div class="card-body">
        <!-- Filter Form dan Export Buttons dalam satu container -->
        <div class="filter-container mb-3">
            <form id="filterForm" method="GET" action="{{ route('laporan.pembelian') }}" class="filter-form">
                <label for="tanggal_awal" class="mr-2">Dari:</label>
                <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control mr-2"
                    value="{{ request('tanggal_awal') }}">

                <label for="tanggal_akhir" class="mr-2">Sampai:</label>
                <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control mr-2"
                    value="{{ request('tanggal_akhir') }}">

                <button type="button" id="resetBtn" class="btn btn-danger ml-2">Reset</button>
            </form>

            <!-- Export Buttons -->
            <div class="export-buttons">
                <a href="{{ route('laporan.pembelian.pdf', ['tanggal_awal' => request('tanggal_awal'), 'tanggal_akhir' => request('tanggal_akhir')]) }}"
                    class="btn btn-primary ml-2"><i class="fas fa-file-pdf"></i> Export PDF</a>
                <a href="{{ route('laporan.pembelian.excel', ['tanggal_awal' => request('tanggal_awal'), 'tanggal_akhir' => request('tanggal_akhir')]) }}"
                    class="btn btn-success ml-2"><i class="fas fa-file-excel"></i> Export Excel</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tanggal Penerimaan</th>
                        <th>Supplier</th>
                        <th>Nama Produk</th>
                        <th>Jumlah</th>
                        <th>Harga Beli</th>
                        <th>Total Pembelian</th>
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
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('#tanggal_akhir').on('change', function() {
                if ($('#tanggal_awal').val()) {
                    $('#filterForm').submit();
                }
            });

            $('#resetBtn').on('click', function() {
                $('#tanggal_awal').val('');
                $('#tanggal_akhir').val('');
                $('#filterForm').submit();
            });

            $('#dataTable').DataTable();
        });
    </script>
@endpush
