@extends('template.admin')

@push('style')
    <style>
        table td {
            vertical-align: middle !important;
        }

        .d-flex button {
            height: 36px;
            line-height: 0px;
            margin-right: 5px;
        }

        .d-flex button {
            padding: 0.2rem 0.6rem;
        }

        .d-flex button {
            margin-top: 10px;
        }

        .img-thumbnail {
            width: 80px;
            height: auto;
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Batch Stok</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data Batch Stok</h6>
            <button class="btn btn-primary btn-sm" id="btnTambahBatch" data-toggle="modal" data-target="#modalBatch">
                <i class="fas fa-plus"></i> Tambah Batch Stok
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Kode Batch</th>
                            <th>Expired Date</th>
                            <th>Stok Gudang</th>
                            <th>Stok Toko</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($batchStok as $batch)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $batch->produk->nama_barang }}</td>
                                <td>{{ $batch->kode_batch }}</td>
                                <td>{{ $batch->expired_date }}</td>
                                <td>{{ $batch->stok_gudang }}</td>
                                <td>{{ $batch->stok_toko }}</td>
                                <td>
                                    <div class="d-flex">
                                        <!-- Tombol Edit -->
                                        <button type="button" class="btn btn-warning btn-sm btnEditBatch"
                                            data-id="{{ $batch->id }}" data-produk="{{ $batch->produk_id }}"
                                            data-kode="{{ $batch->kode_batch }}" data-expired="{{ $batch->expired_date }}"
                                            data-gudang="{{ $batch->stok_gudang }}" data-toko="{{ $batch->stok_toko }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <form action="{{ route('batchstok.destroy', $batch->id) }}" method="POST"
                                            class="ml-2 formHapusBatch">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm btnHapusBatch"
                                                data-id="{{ $batch->id }}">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @include('admin.manajemenStok.batchStokModal')
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
            $(document).on('click', '.btnEditBatch', function() {
                var button = $(this);
                var id = button.data('id');
                var produk = button.data('produk');
                var kode = button.data('kode');
                var expired = button.data('expired');
                var gudang = button.data('gudang');
                var toko = button.data('toko');



                var modal = $('#modalBatch');
                modal.find('.modal-title').text('Edit Batch Stok');
                $('#method').val('PATCH');
                $('#batchForm').attr('action', '/batchstok/' + id);
                $('#batchId').val(id);
                $('#produk_id').val(produk);
                $('#kode_batch').val(kode);
                $('#expired_date').val(expired);
                $('#stok_gudang').val(gudang);
                $('#stok_toko').val(toko);

                modal.modal('show');
            });


            $('#btnTambahBatch').click(function() {
                var modal = $('#modalBatch');
                modal.find('.modal-title').text('Tambah Batch Stok');
                $('#method').val('POST');
                $('#batchForm').attr('action', '/batchstok');
                $('#batchId').val('');
                $('#produk_id').val('');
                $('#kode_batch').val('');
                $('#expired_date').val('');
                $('#stok_gudang').val('');
                $('#stok_toko').val('');
            });
        });


        $('.btnHapusBatch').click(function() {
            let form = $(this).closest('form');
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin menghapus produk ini?",
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
    </script>
@endpush
