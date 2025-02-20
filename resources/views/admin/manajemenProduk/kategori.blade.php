@extends('template.admin')

@push('style')
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Produk</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data Produk</h6>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalProduk" onclick="resetModal()">
                Tambah Produk +
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
                            <th>Buy</th>
                            <th>Sell</th>
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
                                        <img src="{{ asset('storage/' . $p->gambar) }}" width="80">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm btn-edit" data-toggle="modal"
                                        data-target="#modalProduk" data-id="{{ $p->id }}"
                                        data-nama="{{ $p->nama_barang }}" data-kategori="{{ $p->kategori_id }}"
                                        data-harga_beli="{{ $p->harga_beli }}" data-harga_jual="{{ $p->harga_jual }}"
                                        data-deskripsi="{{ $p->deskripsi }}" data-satuan="{{ $p->satuan }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>

                                    <form id="formHapus{{ $p->id }}" action="{{ route('produk.destroy', $p->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="konfirmasiHapus({{ $p->id }})">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
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

<!-- Modal Tambah/Edit Produk -->
<div class="modal fade" id="modalProduk" tabindex="-1" aria-labelledby="modalProdukLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalProdukLabel">
                    <i class="fas fa-box"></i> Tambah Produk
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formProduk" action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="produk_id" name="id"> <!-- ID untuk Edit -->
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-tag"></i> Nama Barang</label>
                                <input type="text" name="nama_barang" id="nama_barang" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-list"></i> Kategori</label>
                                <select name="kategori_id" id="kategori_id" class="form-control" required>
                                    <option value="" disabled selected>Pilih Kategori</option>
                                    @foreach ($kategori as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-money-bill-wave"></i> Harga Beli</label>
                                <input type="number" name="harga_beli" id="harga_beli" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-dollar-sign"></i> Harga Jual</label>
                                <input type="number" name="harga_jual" id="harga_jual" class="form-control" required>
                            </div>
                        </div>
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
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 1500
            });
        @endif

        // Konfirmasi Hapus
        function konfirmasiHapus(id) {
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data produk akan dihapus secara permanen!",
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
            let nama = $(this).data('nama');
            let kategori = $(this).data('kategori');
            let harga_beli = $(this).data('harga_beli');
            let harga_jual = $(this).data('harga_jual');

            console.log({
                id,
                nama,
                kategori,
                harga_beli,
                harga_jual
            });

            $('#produk_id').val(id);
            $('#nama_barang').val(nama);
            $('#kategori_id').val(kategori);
            $('#harga_beli').val(harga_beli);
            $('#harga_jual').val(harga_jual);

            $('#formProduk').attr('action', '{{ route('produk.update') }}');

            $('#modalProdukLabel').text('Edit Produk');

            $('#modalProduk').modal('show');
        });

        function resetModal() {
            $('#formProduk')[0].reset();
            $('#modalProdukLabel').text('Tambah Produk');
            $('#formProduk').attr('action', '{{ route('produk.store') }}');
        }
    </script>
@endpush
