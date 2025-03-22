@extends('template.member')

@push('style')
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengajuan Barang</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalPengajuan" id="btnTambah">+ Tambah
            Pengajuan</button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengajuan Barang</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pengaju</th>
                            <th>Nama Barang</th>
                            <th>Qty</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pengajuan as $index => $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->member->nama ?? 'Tidak diketahui' }}</td>
                                <td>{{ $item->nama_barang }}</td>      
                                <td>{{ $item->qty }}</td>
                                <td>{{ $item->tanggal_pengajuan }}</td>
                                <td>
                                    @if ($item->terpenuhi)
                                        <span class="badge badge-success">Terpenuhi</span>
                                    @else
                                        <span class="badge badge-warning">Belum Terpenuhi</span>
                                    @endif
                                </td>
                                <td>
                                    @if (!$item->terpenuhi)
                                        <button type="button" class="btn btn-warning btn-sm btnEdit"
                                            data-id="{{ $item->id }}" data-nama="{{ $item->nama_barang }}"
                                            data-qty="{{ $item->qty }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <button type="button" class="btn btn-danger btn-sm btnHapusPengajuan"
                                            data-id="{{ $item->id }}">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

<!-- MODAL (Tambah/Edit Pengajuan) -->
<div class="modal fade" id="modalPengajuan" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pengajuan</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="pengajuanForm" method="POST">
                @csrf
                <input type="hidden" id="pengajuan_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" id="nama_barang" name="nama_barang" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Qty</label>
                        <input type="number" id="qty" name="qty" class="form-control" required
                            min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
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
            $('#dataTable').DataTable();
        });

        // Event listener untuk tombol tambah pengajuan
        $('#btnTambah').click(function() {
            $('#pengajuan_id').val('');
            $('#pengajuanForm').attr('action', "{{ route('pengajuan.store') }}");
            $('#pengajuanForm')[0].reset();
            $('.modal-title').text('Tambah Pengajuan');
            $('#modalPengajuan').modal('show');
        });

        // Event listener untuk tombol edit
        $(document).on('click', '.btnEdit', function() {
            let id = $(this).data('id');
            let nama = $(this).data('nama');
            let qty = $(this).data('qty');

            console.log("Edit clicked - ID:", id, "Nama:", nama, "Qty:", qty); // Debugging

            if (!id) {
                Swal.fire("Error", "Data tidak ditemukan!", "error");
                return;
            }

            $('#pengajuan_id').val(id);
            $('#nama_barang').val(nama);
            $('#qty').val(qty);
            $('#pengajuanForm').attr('action', '/pengajuan/update/' + id);
            $('.modal-title').text('Edit Pengajuan');
            $('#modalPengajuan').modal('show');
        });

        // Event listener untuk tombol hapus
        $(document).on('click', '.btnHapusPengajuan', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: "Konfirmasi",
                text: "Yakin ingin menghapus pengajuan ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/pengajuan/delete/" + id,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "DELETE"
                        },
                        success: function(response) {
                            Swal.fire({
                                title: "Dihapus!",
                                text: "Pengajuan berhasil dihapus.",
                                icon: "success",
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            Swal.fire("Error", "Gagal menghapus pengajuan", "error");
                        }
                    });
                }
            });
        });
    </script>
@endpush
