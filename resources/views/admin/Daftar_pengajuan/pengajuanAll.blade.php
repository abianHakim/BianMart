@extends('template.admin')

@push('style')
    <!-- Bootstrap Toggle CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap4-toggle/css/bootstrap4-toggle.min.css" rel="stylesheet">
    <style>
        /* Custom Switch Styling */
        .toggle.ios,
        .toggle-on.ios,
        .toggle-off.ios {
            border-radius: 50px;
        }

        /* Warna latar belakang saat off */
        .toggle.ios .toggle-off {
            background-color: #dc3545 !important;
            color: white;
        }

        /* Warna latar belakang saat on */
        .toggle.ios .toggle-on {
            background-color: #28a745 !important;
            color: white;
        }

        /* Ikon status */
        .toggle-on.ios::before {
            content: "✔";
            position: absolute;
            left: 10px;
            font-size: 18px;
        }

        .toggle-off.ios::before {
            content: "✖";
            position: absolute;
            right: 10px;
            font-size: 18px;
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Pengajuan</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengajuan Barang</h6>

            <!-- Filter & Export -->
            <div class="d-flex align-items-center">
                <!-- Filter Tanggal -->
                <input type="date" id="filterTanggalAwal" class="form-control form-control-sm mr-2" style="width: 150px;">
                <input type="date" id="filterTanggalAkhir" class="form-control form-control-sm mr-2"
                    style="width: 150px;">

                <!-- Tombol Reset -->
                <button id="resetFilter" class="btn btn-secondary btn-sm mr-2">
                    <i class="fas fa-undo"></i>
                </button>

                <!-- Tombol Export PDF -->
                <a href="{{ route('pengajuan.exportPDF') }}" id="exportPdfBtn" class="btn btn-danger btn-sm mr-2">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>

                <!-- Tombol Export Excel -->
                <a href="#" id="exportExcelBtn" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>

            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pengaju</th>
                            <th>Nama Barang</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pengajuan as $key => $item)
                            <tr data-tanggal="{{ $item->tanggal_pengajuan }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->member->nama ?? 'Tidak diketahui' }}</td>
                                <td>{{ $item->nama_barang }}</td>
                                <td>{{ $item->tanggal_pengajuan }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>
                                    <input type="checkbox" class="toggle-status" data-id="{{ $item->id }}"
                                        data-toggle="toggle" data-style="ios" data-on="Terpenuhi" data-off="Tidak"
                                        data-onstyle="success" data-offstyle="danger"
                                        {{ $item->terpenuhi ? 'checked' : '' }}>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Bootstrap Toggle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap4-toggle/js/bootstrap4-toggle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.toggle-status').change(function() {
                let id = $(this).data('id');
                let status = $(this).prop('checked') ? 1 : 0;

                $.ajax({
                    url: "/pengajuan/update/status/" + id,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        terpenuhi: status // Sesuaikan dengan field di controller
                    },
                    success: function(response) {
                        console.log("Status berhasil diperbarui!");
                    },
                    error: function(xhr) {
                        alert("Terjadi kesalahan saat mengupdate status!");
                        console.log(xhr.responseText);
                    }
                });

            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Filtering otomatis saat tanggal dipilih
            $('#filterTanggalAwal, #filterTanggalAkhir').on('change', function() {
                filterTable();
                updateExportLinks();
            });

            // Tombol reset filter
            $('#resetFilter').click(function() {
                $('#filterTanggalAwal, #filterTanggalAkhir').val('');
                filterTable();
                updateExportLinks();
            });

            // Fungsi filtering
            function filterTable() {
                let startDate = $('#filterTanggalAwal').val();
                let endDate = $('#filterTanggalAkhir').val();

                $('#dataTable tbody tr').each(function() {
                    let rowDate = $(this).data('tanggal'); // Ambil tanggal dari atribut data-tanggal

                    if ((startDate && rowDate < startDate) || (endDate && rowDate > endDate)) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });

                // Update tombol export PDF agar sesuai filter
                let queryParams = `?start=${startDate}&end=${endDate}`;
                let exportPdfUrl = "{{ route('pengajuan.exportPDF') }}" + queryParams;
                $('#exportPdfBtn').attr('href', exportPdfUrl);
            }

            // Fungsi update tombol Export Excel
            function updateExportLinks() {
                let startDate = $('#filterTanggalAwal').val();
                let endDate = $('#filterTanggalAkhir').val();

                let queryParams = `?start=${startDate}&end=${endDate}`;
                let exportExcelUrl = "{{ route('pengajuan.exportExcel') }}" + queryParams;

                $('#exportExcelBtn').attr('href', exportExcelUrl);
            }

            // Pastikan tombol export diperbarui saat halaman pertama kali dimuat
            updateExportLinks();
        });
    </script>
@endpush
