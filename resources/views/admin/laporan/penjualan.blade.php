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
        <h1 class="h3 mb-0 text-gray-800">Laporan Penjualan</h1>
    </div>

    <div class="card-body">
        <!-- Filter Form dan Export Buttons dalam satu container dengan Flexbox -->
        <div class="filter-container mb-3">
            <form id="filterForm" method="GET" action="{{ route('laporan.penjualan') }}" class="filter-form">
                <label for="tanggal_awal" class="mr-2">Dari:</label>
                <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control mr-2"
                    value="{{ request('tanggal_awal') }}">

                <label for="tanggal_akhir" class="mr-2">Sampai:</label>
                <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control mr-2"
                    value="{{ request('tanggal_akhir') }}">

                <button type="button" id="resetBtn" class="btn btn-danger ml-2">Reset</button>
            </form>

            <!-- Button Export PDF dan Excel -->
            <div class="export-buttons">
                <a href="{{ route('laporan.penjualan.pdf', ['tanggal_awal' => request('tanggal_awal'), 'tanggal_akhir' => request('tanggal_akhir')]) }}"
                    class="btn btn-primary ml-2">Export PDF</a>
                <a href="{{ route('laporan.penjualan.excel', ['tanggal_awal' => request('tanggal_awal'), 'tanggal_akhir' => request('tanggal_akhir')]) }}"
                    class="btn btn-success ml-2">Export Excel</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No </th>
                        <th>No Faktur</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th>Member</th>
                        <th>Total Bayar</th>
                        <th>Metode</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penjualans as $penjualan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $penjualan->no_faktur }}</td>
                            <td>{{ \Carbon\Carbon::parse($penjualan->tgl_faktur)->format('d/m/Y') }}</td>
                            <td>{{ $penjualan->user->name ?? '-' }}</td>
                            <td>{{ $penjualan->member->nama ?? '-' }}</td>
                            <td>Rp {{ number_format($penjualan->total_bayar, 0, ',', '.') }}</td>
                            <td>{{ ucfirst($penjualan->metode_pembayaran) }}</td>
                        </tr>
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

            $('#tanggal_awal').on('change', function() {});

            // Reset filter
            $('#resetBtn').on('click', function() {
                $('#tanggal_awal').val('');
                $('#tanggal_akhir').val('');
                $('#filterForm').submit();
            });

            // Inisialisasi DataTables
            $('#dataTable').DataTable();
        });
    </script>
@endpush
