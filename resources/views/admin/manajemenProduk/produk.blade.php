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
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahProduk">
                Tambah Produk +
            </button>

        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            {{-- <th>No</th> --}}
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Buy</th>
                            <th>Sell</th>
                            <th>Gambar</th>
                            {{-- <th>Desripsi</th> --}}
                            {{-- <th>Satuan</th> --}}
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
                                    <div class="aksi-btns">
                                        <!-- Tombol Edit -->
                                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                            data-target="#modalEditProduk" data-id="{{ $p->id }}"
                                            data-nama="{{ $p->nama_barang }}" data-kategori="{{ $p->kategori_id }}"
                                            data-harga_beli="{{ $p->harga_beli }}" data-harga_jual="{{ $p->harga_jual }}"
                                            data-deskripsi="{{ $p->deskripsi }}" data-satuan="{{ $p->satuan }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <!-- Tombol Delete -->
                                        <form id="formHapus{{ $p->id }}"
                                            action="{{ route('produk.destroy', $p->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="konfirmasiHapus({{ $p->id }})">
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


<!-- Modal Tambah Produk -->
<div class="modal fade" id="modalTambahProduk" tabindex="-1" aria-labelledby="modalTambahProdukLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Lebar lebih besar -->
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTambahProdukLabel">
                    <i class="fas fa-box"></i> Tambah Produk
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Nama Barang -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-tag"></i> Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" required>
                            </div>
                        </div>
                        <!-- Kategori -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-list"></i> Kategori</label>
                                <select name="kategori_id" class="form-control" required>
                                    <option value="" disabled selected>Pilih Kategori</option>
                                    @foreach ($kategori as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Harga Beli -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-money-bill-wave"></i> Harga Beli</label>
                                <input type="number" name="harga_beli" class="form-control" required>
                            </div>
                        </div>
                        <!-- Harga Jual -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-dollar-sign"></i> Harga Jual</label>
                                <input type="number" name="harga_jual" class="form-control" required>
                            </div>
                        </div>
                        <!-- Satuan -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-cubes"></i> Satuan</label>
                                <input type="text" name="satuan" class="form-control">
                            </div>
                        </div>
                        <!-- Gambar Produk -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-image"></i> Gambar Produk</label>
                                <input type="file" name="gambar" class="form-control" accept="image/*" required>
                            </div>
                        </div>
                        <!-- Deskripsi -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><i class="fas fa-align-left"></i> Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="2"></textarea>
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
@endpush
