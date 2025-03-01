@extends('template.admin')

@push('style')
    <style>
        .d-flex button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            min-height: 36px;
            padding: 6px 12px;
            white-space: nowrap;
        }

        .d-flex button i {
            font-size: 14px;
            margin-right: 5px;
        }

        .d-flex {
            gap: 10px;
        }

        @media (max-width: 768px) {
            .d-flex {
                flex-direction: column;
                align-items: flex-start;
            }

            .d-flex button {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Supplier</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data Supplier</h6>
            <button class="btn btn-primary btn-sm" id="btnTambahSupplier" data-toggle="modal" data-target="#modalSupplier">
                <i class="fas fa-plus"></i> Tambah Supplier
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>no</th>
                            <th>Nama</th>
                            <th>Telepon</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th>Aksi</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $supplier)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $supplier->nama_supplier }}</td>
                                <td>{{ $supplier->telepon }}</td>
                                <td>{{ $supplier->email }}</td>
                                <td>{{ $supplier->alamat }}</td>
                                <td>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-warning btn-sm btnEditSupplier"
                                            data-toggle="modal" data-target="#modalSupplier" data-id="{{ $supplier->id }}"
                                            data-nama="{{ $supplier->nama_supplier }}"
                                            data-telepon="{{ $supplier->telepon }}" data-email="{{ $supplier->email }}"
                                            data-alamat="{{ $supplier->alamat }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <form action="{{ route('supplier.destroy', $supplier->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm btnHapusSupplier"
                                                data-id="{{ $supplier->id }}">
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
        @include('admin.manajemenStok.supplierModal')
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
        $('#btnTambahSupplier').click(function() {
            $('#modalTitle').text('Tambah Produk');
            $('#formSupplier').attr('action', "{{ route('supplier.store') }}");
            $('#formSupplier')[0].reset();

        });

        $('.btnEditSupplier').click(function() {
            $('#modalTitle').text('Edit Supplier');
            $('#formSupplier').attr('action', '/supplier/' + $(this).data('id'));
            $('#formSupplier').append('<input type="hidden" name="_method" value="PATCH">');

            $('#supplier_id').val($(this).data('id'));
            $('#nama_supplier').val($(this).data('nama'));
            $('#telepon').val($(this).data('telepon'));
            $('#email').val($(this).data('email'));
            $('#alamat').val($(this).data('alamat'));
        });
    </script>

    <script>
        $('.btnHapusSupplier').click(function() {
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
