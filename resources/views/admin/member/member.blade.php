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
        <h1 class="h3 mb-0 text-gray-800">Member</h1>
    </div>

    <!-- Tabel Daftar Member -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data Member</h6>
            <button class="btn btn-primary btn-sm" id="btnTambahMember">
                <i class="fas fa-plus"></i> Tambah Member
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>No Telp</th>
                            <th>Alamat</th>
                            <th>Email</th>
                            <th>Poin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($member as $m)
                            <tr>
                                <td>{{ $m->nama }}</td>
                                <td>{{ $m->no_telp }}</td>
                                <td>{{ $m->alamat ?? 'Alamat tidak didaftarkan' }}</td>
                                <td>{{ $m->email ?? 'Email tidak didaftarkan' }}</td>
                                <td>{{ $m->loyalty_points }}</td>
                                <td>
                                    <div class="d-flex justify-content-start">
                                        <button type="button" class="btn btn-warning btn-sm"
                                            onclick="editMember({{ json_encode($m) }})">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form action="{{ route('member.destroy', $m->id) }}" method="POST" class="ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm btnHapusMember"
                                                data-id="{{ $m->id }}">
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
    </div>

    <!-- Modal Tambah/Edit Member -->
    <div class="modal fade" id="memberModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Tambah Member</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form id="memberForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="_method">

                    <input type="hidden" id="member_id">
                    <div class="modal-body">
                        <div class="row">
                            <!-- Kolom Kiri -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Nama</strong></label>
                                    <input type="text" id="nama" name="nama" class="form-control" required
                                        placeholder="Masukkan Nama">
                                </div>
                                <div class="form-group">
                                    <label><strong>No HP</strong></label>
                                    <input type="text" id="no_telp" name="no_telp" class="form-control" required
                                        placeholder="Masukkan No HP">
                                </div>

                                <div class="form-group">
                                    <label><strong>Password</strong></label>
                                    <input type="password" id="password" name="password" class="form-control"
                                        placeholder="Masukkan Password ">
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Email</strong></label>
                                    <input type="email" id="email" name="email" class="form-control"
                                        placeholder="Masukkan Email">
                                </div>
                                <div class="form-group">
                                    <label><strong>Alamat</strong></label>
                                    <textarea id="alamat" name="alamat" class="form-control" rows="3" placeholder="Masukkan Alamat"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i>
                            Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
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
            $('#btnTambahMember').click(function() {
                resetForm();
                $('#memberModal').modal('show');
            });

            function resetForm() {
                $('#modalTitle').text('Tambah Member');
                $('#memberForm').attr('action', '{{ route('member.store') }}');
                $('#memberForm').attr('method', 'POST');

                $('#_method').val('');
                $('#member_id').val('');
                $('#nama').val('');
                $('#no_telp').val('');
                $('#alamat').val('');
                $('#email').val('');
                $('#password').val('');
            }

            window.editMember = function(member) {
                $('#modalTitle').text('Edit Member');
                $('#memberForm').attr('action', '/member/' + member.id);
                $('#_method').val('PATCH');
                $('#member_id').val(member.id);
                $('#nama').val(member.nama ? member.nama.trim() : '');
                $('#no_telp').val(member.no_telp ? member.no_telp.trim() : '');
                $('#alamat').val(member.alamat ? member.alamat.trim() : '');
                $('#email').val(member.email ? member.email.trim() : '');

                $('#memberModal').modal('show');
            };


        });
    </script>

    <script>
        $('.btnHapusMember').click(function() {
            let form = $(this).closest('form');
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin menghapus member ini?",
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
