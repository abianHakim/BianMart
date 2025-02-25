@extends('template.admin')

@push('style')
    <style>
        table td {
            vertical-align: middle !important;
        }

        .d-flex button {
            height: 36px;
            line-height: 0px;
            margin-right: 10px;

        }

        .d-flex button {
            padding: 0.375rem 0.75rem;

        }

        .d-flex button {
            margin-top: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kategori</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data Kategori</h6>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalKategori" onclick="resetModal()">
                Tambah Kategori +
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kategori as $k)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $k->nama_kategori }}</td>
                                <td>
                                    <div class="d-flex justify-content-start ">
                                        <button type="button" class="btn btn-warning btn-sm btn-edit" data-toggle="modal"
                                            data-target="#modalKategori" data-id="{{ $k->id }}"
                                            data-nama_kategori="{{ $k->nama_kategori }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <form id="formHapus{{ $k->id }}"
                                            action="{{ route('kategori.destroy', $k->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="konfirmasiHapus({{ $k->id }})">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                    </div>
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

<!-- Modal Tambah/Edit Kategori -->
<div class="modal fade" id="modalKategori" tabindex="-1" aria-labelledby="modalKategoriLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalKategoriLabel">
                    <i class="fas fa-list"></i> Tambah Kategori
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formKategori" action="{{ route('kategori.store') }}" method="POST">
                @csrf
                <input type="hidden" id="kategori_id" name="id"> <!-- ID untuk Edit -->
                <div class="modal-body">
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Nama Kategori</label>
                        <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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

    @if (session('error'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Gagal!",
                    text: {!! json_encode(session('error')) !!},
                    icon: "error",
                    confirmButtonColor: "#d33",
                });
            });
        </script>
    @endif

    <script>
        function konfirmasiHapus(id) {
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data kategori akan dihapus secara permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formHapus' + id).submit();
                }
            });
        }

        $(document).on('click', '.btn-edit', function() {
            let id = $(this).data('id');
            let nama_kategori = $(this).data('nama_kategori');

            $('#kategori_id').val(id);
            $('#nama_kategori').val(nama_kategori);

            $('#formKategori').attr('action', '{{ url('kategori') }}/' + id);
            $('#formKategori').append('<input type="hidden" name="_method" value="PATCH">');

            $('#modalKategoriLabel').text('Edit Kategori');
            $('#modalKategori').modal('show');

            $('#modalKategori').on('shown.bs.modal', function() {
                $('#nama_kategori').trigger('focus');
            });

        });

        function resetModal() {
            $('#formKategori')[0].reset();
            $('#formKategori').attr('action', '{{ route('kategori.store') }}');
            $('#formKategori').find("input[name='_method']").remove();
            $('#modalKategoriLabel').text('Tambah Kategori');
        }
    </script>
@endpush
