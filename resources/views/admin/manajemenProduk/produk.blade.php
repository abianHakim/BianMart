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
        @include('admin.manajemenProduk.produkModal')

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
            $('#gambar').val('');

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
