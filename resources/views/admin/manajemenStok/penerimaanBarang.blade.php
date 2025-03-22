@extends('template.admin')

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <style>
        .invoice-container {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #fff;
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .invoice-title {
            font-size: 20px;
            font-weight: bold;
        }

        .invoice-table th,
        .invoice-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Penerimaan Barang</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data Penerimaan Barang</h6>
            <a href="{{ route('penerimaan.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Penerimaan
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Supplier</th>
                            <th>Tanggal</th>
                            <th>Total Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penerimaan as $terima)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $terima->supplier->nama_supplier }}</td>
                                <td>{{ $terima->tgl_masuk }}</td>
                                <td>Rp {{ number_format($terima->total, 0, ',', '.') }}</td>
                                <td>

                                    <a href="{{ route('penerimaan.invoice', $terima->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-file-invoice"></i> Invoice
                                    </a>

                                    <form action="{{ route('penerimaan.destroy', $terima->id) }}" method="POST"
                                        class="d-inline formHapus">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm btnHapusPenerimaan">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

<!-- Modal Detail Penerimaan -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailLabel">Detail Penerimaan Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="detail-content" class="invoice-container">
                    <p class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).on('click', '.btnHapusPenerimaan', function(event) {
            event.preventDefault();
            let form = $(this).closest('form');

            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin menghapus stok ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Submit form biasa
                }
            });
        });
    </script>

    @if (session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Berhasil!",
                    text: {!! json_encode(session('success')) !!},
                    icon: "success",
                    confirmButtonColor: "#4a69bd",
                    timer: 1000,
                    timerProgressBar: true,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            });
        </script>
    @endif
@endpush
