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
                                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="2" placeholder="Tambahkan deskripsi singkat"></textarea>
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
