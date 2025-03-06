@extends('template.admin')

@push('style')
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Penerimaan Barang</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data Penerimaan Barang</h6>
            <button class="btn btn-primary btn-sm" id="btnTambahPenerimaan" data-toggle="modal" data-target="#modalPenerimaan">
                <i class="fas fa-plus"></i> Tambah Penerimaan
            </button>
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
                                <td>{{ $terima->tanggal_penerimaan }}</td>
                                <td>Rp {{ number_format($terima->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm btnDetailPenerimaan"
                                        data-id="{{ $terima->id }}" data-toggle="modal"
                                        data-target="#modalDetailPenerimaan">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                    <form action="{{ route('penerimaan.destroy', $terima->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm btnHapusPenerimaan">
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
        @include('admin.manajemenStok.penerimaanBarangModal')
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    @if (session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Berhasil!",
                    text: {!! json_encode(session('success')) !!},
                    icon: "success",
                    confirmButtonColor: "#4a69bd",
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            });
        </script>
    @endif


    <script>
        $(document).ready(function() {
            $('#produkSelect').change(function() {
                let hargaBeli = $(this).find(':selected').data('harga-beli');
                $('#hargaBeli').val(hargaBeli ? `Rp ${hargaBeli.toLocaleString('id-ID')}` : '');
            });

            $('#btnTambahPenerimaan').click(function() {
                $('#modalPenerimaan').modal('show');
                $('#produkSelect').val('');
                $('#hargaBeli').val('');
            });

            $('.btnHapusPenerimaan').click(function() {
                let form = $(this).closest('form');
                Swal.fire({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menghapus data ini?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, Hapus!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
