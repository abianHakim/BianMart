@extends('template.admin')

@push('style')
    <style>
        .aksi-btns {
            display: flex;
            gap: 5px;
            justify-content: flex-start;
            align-items: center;
        }

        .aksi-btns form {
            margin: 0;
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
            <button type="button" class="btn btn-primary btn-sm" id="btnTambahKategori">
                Tambah Kategori
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
                        @foreach ($kategori as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nama_kategori }}</td>
                                <td>
                                    <div class="aksi-btns">
                                        <!-- Tombol Edit dengan kata -->
                                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                            data-target="#kategoriModal" data-id="{{ $item->id }}"
                                            data-nama="{{ $item->nama_kategori }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <!-- Tombol Delete dengan kata -->
                                        <form id="formHapus{{ $item->id }}"
                                            action="{{ route('kategori.destroy', $item->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="konfirmasiHapus({{ $item->id }})">
                                                <i class="fas fa-trash-alt"></i> Delete
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
    </div>
@endsection

<!-- Modal Tambah & Edit Kategori -->
<div class="modal fade" id="kategoriModal" tabindex="-1" role="dialog" aria-labelledby="kategoriModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kategoriModalLabel">Tambah Kategori</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formKategori" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodInput" value="POST">
                <div class="modal-body">
                    <input type="hidden" id="kategori_id" name="id">
                    <div class="form-group">
                        <label for="nama_kategori">Nama Kategori</label>
                        <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required
                            autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#btnTambahKategori').on('click', function() {
                $('#kategoriModal').modal('show');
                setTimeout(function() {
                    $('#nama_kategori').focus(); 
                }, 500);
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // tambah kategori
            $('#btnTambahKategori').click(function() {
                $('#kategoriModalLabel').text('Tambah Kategori');
                $('#formKategori').attr('action', '/kategori');
                $('#methodInput').val('POST');
                $('#kategori_id').val('');
                $('#nama_kategori').val('');
            });

            // edit kategori
            $('#kategoriModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var nama = button.data('nama');

                if (id) {
                    $('#kategoriModalLabel').text('Edit Kategori');
                    $('#formKategori').attr('action', '/kategori/' + id);
                    $('#methodInput').val('PATCH');
                    $('#kategori_id').val(id);
                    $('#nama_kategori').val(nama);
                }
            });

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 1500
                });
            @endif
        });

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
    </script>
@endpush
