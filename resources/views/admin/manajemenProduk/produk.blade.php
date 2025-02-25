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
        <h1 class="h3 mb-0 text-gray-800">Produk</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data Produk</h6>
            <button class="btn btn-primary btn-sm" id="btnTambahProduk" data-toggle="modal" data-target="#modalProduk">
                <i class="fas fa-plus"></i> Tambah Produk
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Gambar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($produk as $p)
                            <tr>
                                <td>{{ $p->kode_barang }}</td>
                                <td>{{ $p->nama_barang }}</td>
                                <td>{{ $p->kategori->nama_kategori ?? 'Tidak Ada' }}</td>
                                <td>Rp{{ number_format($p->harga_beli, 0, ',', '.') }}</td>
                                <td>Rp{{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                                <td>
                                    @if ($p->gambar)
                                        <img src="{{ asset('storage/' . $p->gambar) }}" class="img-thumbnail">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-start">
                                        <button type="button" class="btn btn-warning btn-sm btnEditProduk"
                                            data-toggle="modal" data-target="#modalProduk" data-id="{{ $p->id }}"
                                            data-kode="{{ $p->kode_barang }}" data-nama="{{ $p->nama_barang }}"
                                            data-kategori="{{ $p->kategori_id }}" data-harga_beli="{{ $p->harga_beli }}"
                                            data-harga_jual="{{ $p->harga_jual }}" data-deskripsi="{{ $p->deskripsi }}"
                                            data-gambar="{{ $p->gambar }}" data-satuan="{{ $p->satuan }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <form action="{{ route('produk.destroy', $p->id) }}" method="POST" class="ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm btnHapusProduk"
                                                data-id="{{ $p->id }}">
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
@endsection

<div class="modal fade" id="modalProduk" tabindex="-1" aria-labelledby="modalProdukLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalProdukLabel"><span id="modalTitle">Tambah Produk</span></h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="formProduk" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="produk_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <!-- Kode Barang -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-barcode"></i> Kode Barang</label>
                                <input type="text" id="kode_barang" name="kode_barang" class="form-control"
                                    placeholder="Masukkan Kode Barang" required>
                            </div>
                        </div>
                        <!-- Nama Barang -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-box-open"></i> Nama Barang</label>
                                <input type="text" id="nama_barang" name="nama_barang" class="form-control"
                                    placeholder="Masukkan Nama Barang" required>
                            </div>
                        </div>
                        <!-- Kategori -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-tags"></i> Kategori</label>
                                <select id="kategori_id" name="kategori_id" class="form-control" required>
                                    <option value="" disabled selected>🔍 Pilih Kategori</option>
                                    @foreach ($kategori as $k)
                                        <option value="{{ $k->id }}">📌 {{ $k->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Harga Beli -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-money-bill-wave"></i> Harga Beli</label>
                                <input type="number" id="harga_beli" name="harga_beli" class="form-control"
                                    placeholder="Rp 0" required>
                            </div>
                        </div>
                        <!-- Harga Jual -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-dollar-sign"></i> Harga Jual</label>
                                <input type="number" id="harga_jual" name="harga_jual" class="form-control"
                                    placeholder="Rp 0" required>
                            </div>
                        </div>
                        {{-- gambar --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-image"></i> Gambar Produk</label>
                                <input type="file" name="gambar" id="gambar" class="form-control"
                                    accept="image/*">
                            </div>
                        </div>

                        {{-- satuan --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-cubes"></i> Satuan</label>
                                <input type="text" name="satuan" id="satuan" class="form-control">
                            </div>
                        </div>
                        <!-- Preview Gambar -->
                        <div class="col-md-6 d-flex align-items-center justify-content-center">
                            <div class="border rounded d-flex align-items-center justify-content-center"
                                style="width: 150px; height: 150px; background-color: #f8f9fa;">
                                <img id="preview_gambar" src="" class="img-fluid d-none"
                                    style="max-height: 100%; max-width: 100%;">
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><i class="fas fa-file-alt"></i> Deskripsi</label>
                                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="2"
                                    placeholder="Tambahkan deskripsi singkat"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
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
    <script>
        $('#btnTambahProduk').click(function() {
            $('#modalTitle').text('Tambah Produk');
            $('#formProduk').attr('action', "{{ route('produk.store') }}");
            $('#formProduk')[0].reset();
            $('#preview_gambar').attr('src', '').addClass('d-none');
            $('#gambar').val(''); // Reset input file
        });

        $('.btnEditProduk').click(function() {
            $('#modalTitle').text('Edit Produk');
            $('#formProduk').attr('action', '/produk/' + $(this).data('id'));
            $('#formProduk').append('<input type="hidden" name="_method" value="PATCH">');

            $('#produk_id').val($(this).data('id'));
            $('#kode_barang').val($(this).data('kode'));
            $('#nama_barang').val($(this).data('nama'));
            $('#kategori_id').val($(this).data('kategori'));
            $('#harga_beli').val($(this).data('harga_beli'));
            $('#harga_jual').val($(this).data('harga_jual'));
            $('#satuan').val($(this).data('satuan'));
            $('#deskripsi').val($(this).data('deskripsi'));

            $('#gambar').val(''); // Reset input file agar tidak menyimpan gambar sebelumnya

            let gambar = $(this).data('gambar');
            if (gambar) {
                $('#preview_gambar').attr('src', '/storage/' + gambar).removeClass('d-none');
            } else {
                $('#preview_gambar').attr('src', '').addClass('d-none');
            }


        });

        // Preview gambar saat memilih file baru
        $('#gambar').change(function(event) {
            let file = event.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview_gambar').attr('src', e.target.result).removeClass('d-none');
                }
                reader.readAsDataURL(file);
            } else {
                $('#preview_gambar').attr('src', '').addClass('d-none');
            }
        });

        $('#modalProduk').on('hidden.bs.modal', function() {
            $('#formProduk')[0].reset();
            $('#preview_gambar').attr('src', '').addClass('d-none');
            $('#gambar').val('');
        });
    </script>

    <script>
        $('.btnHapusProduk').click(function() {
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
