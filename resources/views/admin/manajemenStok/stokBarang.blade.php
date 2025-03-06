@extends('template.admin')

@push('style')
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Stok Barang</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data Stok Barang</h6>
            <button class="btn btn-primary btn-sm" id="btnTambahStok" data-toggle="modal" data-target="#modalStok">
                <i class="fas fa-plus"></i> Tambah Stok
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Produk</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Stok Gudang</th>
                            <th>Stok Toko</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stock as $stok)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $stok->produk->id }}</td>
                                <td>{{ $stok->produk->kode_barang ?? '-' }}</td>
                                <td>{{ $stok->produk->nama_barang ?? '-' }}</td>
                                <td>{{ $stok->stok_gudang }}</td>
                                <td>{{ $stok->stok_toko }}</td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm btnEditStok"
                                        data-id="{{ $stok->id }}" data-toggle="modal" data-target="#modalStok"
                                        data-produk="{{ $stok->produk_id }}" data-stok_gudang="{{ $stok->stok_gudang }}"
                                        data-stok_toko="{{ $stok->stok_toko }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('stokbarang.destroy', $stok->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm btnHapusStok">
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
        @include('admin.manajemenStok.stokBarangModal')
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
                    timer: 1000,
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
            // Ketika tombol "Tambah Stok" ditekan
            $('#btnTambahStok').click(function() {
                $('#modalTitle').text('Tambah Stok');
                $('#formStok').attr('action', "{{ route('stokbarang.store') }}");
                $('#formStok')[0].reset();
                $('#method').val("POST");
                $('#mode').val('add');
            });

            $('.btnEditStok').click(function() {
                $('#modalTitle').text('Edit Stok');

                let id = $(this).data('id');
                let produk_id = $(this).data('produk');
                let stok_gudang = $(this).data('stok_gudang');
                let stok_toko = $(this).data('stok_toko');

                $('#formStok').attr('action', "{{ url('stokbarang') }}/" + id);
                $('#method').val("PATCH");
                $('#stok_id').val(id);
                $('#produk_id').val(produk_id);
                $('#stok_gudang').val(stok_gudang);
                $('#stok_toko').val(stok_toko);
                $('#mode').val('replace');
            });

            $('.btnHapusStok').click(function() {
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
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
