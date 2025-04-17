@extends('template.admin')

@push('style')
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Absensi Kerja Karyawan</h1>
        </div>

        {{-- @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif --}}

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Absensi Kerja</h6>

                <div class="d-flex align-items-center">
                    {{-- Tombol Tambah Absensi --}}
                    <button class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#modalTambah">
                        <i class="fas fa-plus"></i> Tambah Absensi
                    </button>

                    {{-- Tombol Export & Import --}}
                    <a href="{{ route('absensi.export.excel') }}" class="btn btn-success btn-sm mr-2">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="{{ route('absensi.export.pdf') }}" class="btn btn-danger btn-sm mr-2">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                    <a href="{{ route('absensi.format') }}" class="btn btn-info btn-sm mr-2">
                        <i class="fas fa-download"></i> Format Import
                    </a>

                    <form action="{{ route('absensi.import') }}" method="POST" enctype="multipart/form-data"
                        id="importForm">
                        @csrf
                        <input type="file" name="file" id="fileInput" class="d-none" accept=".xls,.xlsx"
                            onchange="document.getElementById('importForm').submit();">
                        <button type="button" class="btn btn-warning btn-sm mt-3"
                            onclick="document.getElementById('fileInput').click();">
                            <i class="fas fa-file-import"></i> Import Excel
                        </button>
                    </form>

                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable">
                        <thead class="thead">
                            <tr>
                                <th>No</th>
                                <th>Nama Karyawan</th>
                                <th>Tanggal Masuk</th>
                                <th>Waktu Masuk</th>
                                <th>Status</th>
                                <th>Waktu Selesai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($absensi as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->user->name ?? 'Nama tidak ditemukan' }}</td>
                                    <!-- Mengambil nama karyawan dari relasi user -->
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('Y-m-d') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->waktu_masuk)->format('H:i:s') }}</td>

                                    <td>
                                        <form action="{{ route('absensi.updateStatus', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status_masuk" class="form-control form-control-sm"
                                                onchange="this.form.submit()">
                                                <option value="masuk"
                                                    {{ $item->status_masuk == 'masuk' ? 'selected' : '' }}>Masuk</option>
                                                <option value="sakit"
                                                    {{ $item->status_masuk == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                                <option value="cuti"
                                                    {{ $item->status_masuk == 'cuti' ? 'selected' : '' }}>Cuti</option>
                                            </select>
                                        </form>
                                    </td>

                                    <td>
                                        @if ($item->status_masuk == 'masuk' && $item->waktu_selesai_kerja == null)
                                            <button type="button" class="btn btn-sm btn-success mr-2"
                                                id="selesaiButton{{ $item->id }}"
                                                onclick="setWaktuSelesai({{ $item->id }})">
                                                <i class="fas fa-check"></i> Selesai
                                            </button>
                                        @else
                                            <span>{{ \Carbon\Carbon::parse($item->waktu_selesai_kerja)->format('H:i:s') ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            {{-- Button Edit --}}
                                            <button type="button" class="btn btn-sm btn-outline-primary mr-2"
                                                data-toggle="modal" data-target="#modalEdit{{ $item->id }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>

                                            {{-- Button Hapus --}}
                                            <button type="button" class="btn btn-sm btn-outline-danger btnHapusAbsensi"
                                                data-id="{{ $item->id }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                            <form id="delete-form-{{ $item->id }}"
                                                action="{{ route('absensi.destroy', $item->id) }}" method="POST"
                                                class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Modal Edit --}}
                                <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" role="dialog"
                                    aria-labelledby="modalEditLabel{{ $item->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form action="{{ route('absensi.update', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalEditLabel{{ $item->id }}">Edit
                                                        Absensi</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    {{-- Nama Karyawan --}}
                                                    <div class="form-group">
                                                        <label>Nama Karyawan</label>
                                                        <select name="user_id" class="form-control" required>
                                                            @foreach (\App\Models\User::whereIn('role', ['kasir', 'admin'])->get() as $user)
                                                                <option value="{{ $user->id }}"
                                                                    {{ $item->user_id == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }} ({{ $user->email }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    {{-- Tanggal Masuk --}}
                                                    <div class="form-group">
                                                        <label>Tanggal Masuk</label>
                                                        <input type="date" name="tanggal_masuk" class="form-control"
                                                            value="{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('Y-m-d') }}"
                                                            required>
                                                    </div>

                                                    {{-- Waktu Masuk --}}
                                                    <div class="form-group">
                                                        <label>Waktu Masuk</label>
                                                        <input type="time" name="waktu_masuk" class="form-control"
                                                            value="{{ \Carbon\Carbon::parse($item->waktu_masuk)->format('H:i:s') }}"
                                                            required>
                                                    </div>

                                                    {{-- Status --}}
                                                    <div class="form-group">
                                                        <label>Status</label>
                                                        <select name="status_masuk" class="form-control" required>
                                                            <option value="masuk"
                                                                {{ $item->status_masuk == 'masuk' ? 'selected' : '' }}>
                                                                Masuk</option>
                                                            <option value="sakit"
                                                                {{ $item->status_masuk == 'sakit' ? 'selected' : '' }}>
                                                                Sakit</option>
                                                            <option value="cuti"
                                                                {{ $item->status_masuk == 'cuti' ? 'selected' : '' }}>Cuti
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Batal</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Modal Tambah Absensi --}}
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('absensi.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Absensi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{-- Pilih Karyawan --}}
                    <div class="form-group">
                        <label>Pilih Karyawan</label>
                        <select name="user_id" class="form-control" required>
                            <option value="" selected disabled>Pilih Karyawan</option>
                            @foreach (\App\Models\User::whereIn('role', ['kasir', 'admin'])->get() as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tanggal Masuk --}}
                    <div class="form-group">
                        <label>Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control" required>
                    </div>

                    {{-- Waktu Masuk --}}
                    <div class="form-group">
                        <label>Waktu Masuk</label>
                        <input type="time" step="1" name="waktu_masuk" class="form-control" required
                            onclick="this.value = new Date().toLocaleTimeString('it-IT', {hour12: false})">
                    </div>

                    {{-- Status --}}
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status_masuk" class="form-control" required>
                            <option value="masuk">Masuk</option>
                            <option value="sakit">Sakit</option>
                            <option value="cuti">Cuti</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
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

    <script>
        function setWaktuSelesai(id) {
            const currentDate = new Date().toISOString().slice(0, 19).replace('T', ' '); // Ambil waktu sekarang
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('absensi.selesai', '') }}/' + id;
            form.innerHTML = '@csrf<input type="hidden" name="waktu_selesai_kerja" value="' + currentDate + '">';
            document.body.appendChild(form);
            form.submit();
        }

        document.querySelectorAll('.btnHapusAbsensi').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            });
        });
    </script>
@endpush
