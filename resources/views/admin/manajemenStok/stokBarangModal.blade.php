<div class="modal fade" id="modalStok" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Stok</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formStok" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="method" value="POST">
                    <input type="hidden" id="stok_id" name="stok_id">
                    <input type="hidden" id="mode" name="mode"> 

                    <div class="form-group">
                        <label>Produk</label>
                        <select id="produk_id" name="produk_id" class="form-control" required>
                            <option value="">Pilih Produk</option>
                            @foreach ($produk as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_barang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Stok Gudang</label>
                        <input type="number" id="stok_gudang" name="stok_gudang" class="form-control" min="0"
                            required>
                    </div>
                    <div class="form-group">
                        <label>Stok Toko</label>
                        <input type="number" id="stok_toko" name="stok_toko" class="form-control" min="0"
                            required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
