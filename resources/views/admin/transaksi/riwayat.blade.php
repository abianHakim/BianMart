@extends('template.admin')

@push('style')
    <style>
        .receipt {
            font-family: "Courier New", Courier, monospace;
            font-size: 13px;
            max-width: 250px;
            margin: auto;
        }

        .receipt-header {
            font-weight: bold;
            font-size: 16px;
            text-align: center;
        }

        .dashed-line {
            border-top: 2px dashed #000;
            margin: 5px 0;
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
        }

        .receipt-table th,
        .receipt-table td {
            padding: 3px 5px;
            font-size: 13px;
        }

        .receipt-table th {
            border-bottom: 1px solid #000;
        }

        .receipt-table td.text-right {
            white-space: nowrap;
            min-width: 100px;
        }

        .receipt-table td.text-right .currency {
            margin-right: 3px;
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
            align-items: center;
        }

        .total-bayar {
            text-align: right;
            min-width: 120px;
            white-space: nowrap;
        }

        .info-row span {
            display: block;
        }


        @media print {
            body * {
                visibility: hidden;
            }

            #receipt,
            #receipt * {
                visibility: visible;
            }

            #receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Transaksi</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data Transaksi</h6>

        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>No Faktur</th>
                            <th>Tanggal</th>
                            <th>Total Bayar</th>
                            <th>Kasir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($riwayat as $data)
                            <tr>
                                <td>{{ $data->no_faktur }}</td>
                                <td>{{ Carbon\Carbon::parse($data->tgl_faktur)->format('d-m-Y') }}</td>
                                <td>Rp {{ number_format($data->total_bayar, 0, ',', '.') }}</td>
                                <td>{{ $data->user->name }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm btn-detail" data-id="{{ $data->id }}"
                                        data-toggle="modal" data-target="#detailModal">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- {{ $riwayat->links() }} --}}
            </div>
        </div>
    @endsection

    <!-- Modal Detail Transaksi -->
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
                        <p class="receipt-header">Struk Pembelian</p>

                        <!-- Layout kiri-kanan -->
                        <div class="info-row">
                            <span>No Faktur:</span>
                            <span id="no_faktur"></span>
                        </div>
                        <div class="info-row">
                            <span>Tanggal:</span>
                            <span id="tgl_faktur"></span>
                        </div>
                        <div class="info-row">
                            <span>Kasir:</span>
                            <span id="kasir"></span>
                        </div>
                        <div class="info-row">
                            <span>Member:</span>
                            <span id="member"></span>
                        </div>

                        <p class="dashed-line"></p>

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

                        <p class="dashed-line"></p>

                        <div class="info-row">
                            <span><strong>Total Qty:</strong></span>
                            <span id="total_qty"></span>
                        </div>

                        <div class="info-row">
                            <span><strong>Total Bayar:</strong></span>
                            <span class="total-bayar text-right" id="total_bayar"></span>
                        </div>
                        <div class="info-row">
                            <span><strong>Uang Pelanggan:</strong></span>
                            <span class="text-right" id="uang_pelanggan"></span>
                        </div>

                        <div class="info-row">
                            <span><strong>Kembalian:</strong></span>
                            <span class="text-right" id="kembalian"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button id="printBtn" class="btn btn-success btn-sm">Cetak Struk</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            $(document).ready(function() {
                // Gunakan event delegation untuk .btn-detail
                $(document).on('click', '.btn-detail', function() {
                    let transaksiId = $(this).data('id');

                    $.ajax({
                        url: "{{ route('transaksi.show', '') }}/" + transaksiId,
                        type: "GET",
                        success: function(response) {
                            // Update isi modal seperti biasa
                            $('#no_faktur').text(response.no_faktur);
                            $('#tgl_faktur').text(response.tgl_faktur);
                            $('#kasir').text(response.user.name);
                            $('#member').text(response.member ? response.member.nama : '-');

                            let uangPelanggan = response.uang_pelanggan || 0;
                            let totalBayar = response.total_bayar || 0;
                            let kembalian = uangPelanggan - totalBayar;

                            $('#total_bayar').text("Rp " + new Intl.NumberFormat('id-ID').format(
                                totalBayar));
                            $('#uang_pelanggan').text("Rp " + new Intl.NumberFormat('id-ID').format(
                                uangPelanggan));
                            $('#kembalian').text("Rp " + new Intl.NumberFormat('id-ID').format(
                                kembalian));

                            let totalQty = 0;
                            let produkHtml = "";
                            if (response.detail_penjualan.length > 0) {
                                response.detail_penjualan.forEach(item => {
                                    totalQty += item.jumlah;
                                    produkHtml += `
                            <tr>
                                <td class="text-left">${item.nama_produk}</td>
                                <td class="text-center">${item.jumlah}</td>
                                <td class="text-right"><span class="currency">Rp</span> ${new Intl.NumberFormat('id-ID').format(item.sub_total)}</td>
                            </tr>`;
                                });
                            } else {
                                produkHtml =
                                    `<tr><td colspan="3" class="text-center">Tidak ada produk</td></tr>`;
                            }

                            $('#detail-produk').html(produkHtml);
                            $('#total_qty').text(totalQty);
                        },
                        error: function() {
                            alert("Gagal mengambil data!");
                        }
                    });
                });

                // Cetak struk
                $('#printBtn').on('click', function() {
                    window.print();
                });
            });
        </script>
    @endpush
