<!-- Modal Batch Stok -->
<div class="modal fade" id="modalBatch" tabindex="-1" role="dialog" aria-labelledby="modalBatchLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBatchLabel">Tambah Batch Stok</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="batchForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="method" value="POST">
                <input type="hidden" name="id" id="batchId">

                <div class="modal-body">
                    <div class="form-group">
                        <label for="produk_id">Produk</label>
                        <select class="form-control" name="produk_id" id="produk_id" required>
                            <option value="">Pilih Produk</option>
                            @foreach ($produk as $p)
                                <option value="{{ $p->id }}">{{ $p->kode_barang }} - {{ $p->nama_barang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="kode_batch">Kode Batch</label>
                        <input type="text" class="form-control" name="kode_batch" id="kode_batch" required>
                    </div>

                    <div class="form-group">
                        <label for="expired_date">Expired Date</label>
                        <input type="date" class="form-control" name="expired_date" id="expired_date" required>
                    </div>

                    <div class="form-group">
                        <label for="stok_gudang">Stok Gudang</label>
                        <input type="number" class="form-control" name="stok_gudang" id="stok_gudang" required>
                    </div>

                    <div class="form-group">
                        <label for="stok_toko">Stok Toko</label>
                        <input type="number" class="form-control" name="stok_toko" id="stok_toko" required>
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
