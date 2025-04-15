<style>
    .receipt {
        font-family: 'Courier New', monospace;
        font-size: 13px;
        max-width: 250px;
        margin: 0 auto;
    }

    .receipt-header {
        font-weight: bold;
        font-size: 16px;
        text-align: center;
        margin-bottom: 10px;
    }

    .dashed-line {
        border-top: 1px dashed #000;
        margin: 8px 0;
    }

    .receipt-table {
        width: 100%;
        border-collapse: collapse;
        margin: 8px 0;
    }

    .receipt-table th,
    .receipt-table td {
        padding: 3px 0;
        font-size: 12px;
    }

    .receipt-table th {
        border-bottom: 1px solid #000;
    }

    .text-left {
        text-align: left;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        margin: 4px 0;
    }
</style>



<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h5 class="modal-title w-100">
                    <i class="fas fa-store"></i>
                    <br>
                    <strong>Bian Mart</strong>
                </h5>
            </div>
            <div class="modal-body">
                <div class="receipt" id="receipt">
                    <p class="receipt-header text-center"><strong>Struk Pembelian</strong></p>

                    <div class="info-row">
                        <span>No Faktur:</span>
                        <span id="no_faktur" class="text-right"></span>
                    </div>
                    <div class="info-row">
                        <span>Tanggal:</span>
                        <span id="tgl_faktur" class="text-right"></span>
                    </div>
                    <div class="info-row">
                        <span>Kasir:</span>
                        <span id="kasir" class="text-right"></span>
                    </div>
                    <div class="info-row">
                        <span>Member:</span>
                        <span id="member" class="text-right">-</span>
                    </div>

                    <div class="dashed-line"></div>

                    <table class="receipt-table">
                        <thead>
                            <tr>
                                <th class="text-left">Produk</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detail-produk"></tbody>
                    </table>

                    <div class="dashed-line"></div>

                    <div class="info-row">
                        <span><strong>Total Qty:</strong></span>
                        <span id="total_qty" class="text-right"></span>
                    </div>
                    <div class="info-row">
                        <span><strong>Total Bayar:</strong></span>
                        <span id="total_bayar" class="text-right"></span>
                    </div>
                    <div class="info-row">
                        <span><strong>Uang Pelanggan:</strong></span>
                        <span id="uang_pelanggan" class="text-right"></span>
                    </div>
                    <div class="info-row">
                        <span><strong>Kembalian:</strong></span>
                        <span id="kembalian" class="text-right"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button id="printBtn" class="btn btn-success btn-sm">
                    <i class="fas fa-print"></i> Cetak Struk
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
