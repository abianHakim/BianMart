@extends('template.admin')

@push('style')
    <style>
        .scanner-box {
            position: relative;
            width: 100%;
            height: 250px;
            border: 2px dashed #007bff;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            background-color: #f8f9fc;
            overflow: hidden;
        }

        #camera-stream {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        .table td,
        .table th {
            vertical-align: middle;
            text-align: center;
        }

        #cart-container {
            max-height: 300px;
            overflow-y: auto;
        }

        /* Flexbox untuk input nomor HP dan nama */
        .member-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .member-info input {
            flex: 1;
        }

        .member-info span {
            flex: 1;
            font-size: 16px;
        }

        @media (max-width: 768px) {

            .col-md-4,
            .col-md-8 {
                width: 100%;
            }

            .member-info {
                flex-direction: column;
                align-items: flex-start;
            }

            .member-info span {
                width: 100%;
            }
        }

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
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Transaksi Kasir</h1>
        </div>

        <div class="row">
            <!-- Kiri: Scan Barcode & Input Kode -->
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="scanner-box mb-3" id="scanner-box">
                            <video id="camera-stream" width="100%" height="100%" style="display: none;"></video>
                            <span id="scan-text">SCAN BARCODE</span>
                        </div>

                        <div class="input-group mb-3">
                            <input type="text" id="kode_barang" class="form-control"
                                placeholder="Masukkan kode barang & Enter">
                            <input type="number" id="jumlah_barang" class="form-control" style="max-width: 80px;"
                                placeholder="qty">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kanan: Transaksi -->
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Transaksi</h6>
                    </div>
                    <div class="card-body">

                        <!-- Input Nomor HP Member -->
                        <div class="member-info mb-3">
                            <input type="text" id="no_telp" class="form-control" placeholder="Masukkan Nomor HP"
                                autocomplete="off">
                            <input type="hidden" id="id_member" name="id_member"> <!-- Menyimpan ID Member -->
                            <span>Nama: <span id="nama_member">-</span></span>
                        </div>

                        <!-- Daftar Barang -->
                        <div id="cart-container">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-body">
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada barang</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <h5>Total: <span id="total-harga">Rp 0</span></h5>
                            <button class="btn btn-success" id="btnBayar">Bayar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <button style="width: 327px" onclick="cetakInvoice()" class="btn btn-primary mt-2">
            <i class="fas fa-print"></i> Cetak Invoice Sebelumnya
        </button> --}}

    </div>
    @include('admin.transaksi.modal-struk')
@endsection

<!-- Modal Pembayaran -->
<div class="modal fade" id="modalPembayaran" tabindex="-1" aria-labelledby="modalPembayaranLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPembayaranLabel">Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('transaksi.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Total Belanja</label>
                        <input type="text" id="modalTotal" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label>Uang Pelanggan</label>
                        <input type="number" id="uangPelanggan" class="form-control" autofocus min="0"
                            step="0.01">

                    </div>
                    <div class="form-group">
                        <label>Kembalian</label>
                        <input type="text" id="modalKembalian" class="form-control" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btnProsesBayar" disabled>Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Selesai</button>
            </div>
        </div>
    </div>
</div>


@push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/@zxing/library@latest"></script>

    <script>
        let cart = [];

        // Tambah barang ke daftar dengan validasi stok
        function tambahBarang(barang, jumlah = 1) {
            let index = cart.findIndex(item => item.id === barang.id);
            let stokTersedia = barang.stok_toko;

            if (index !== -1) {
                let totalQty = cart[index].qty + jumlah;
                if (totalQty > stokTersedia) {
                    Swal.fire({
                        icon: "warning",
                        title: "Stok Tidak Cukup",
                        text: `Maksimal stok tersedia: ${stokTersedia}`,
                        timer: 1700,
                        timerProgressBar: true,
                    });
                    return;
                }
                cart[index].qty += jumlah;
            } else {
                if (jumlah > stokTersedia) {
                    Swal.fire({
                        icon: "warning",
                        title: "Stok Tidak Cukup",
                        text: `Maksimal stok tersedia: ${stokTersedia}`,
                        timer: 1700,
                        timerProgressBar: true,
                    });
                    return;
                }
                cart.push({
                    id: barang.id,
                    nama: barang.nama_barang,
                    harga: barang.harga_jual,
                    qty: jumlah,
                    stok: barang.stok_toko
                });
            }
            renderCart();
        }

        // Render daftar barang
        function renderCart() {
            let tbody = $("#cart-body");
            tbody.empty();
            let totalHarga = 0;

            if (cart.length === 0) {
                tbody.html('<tr><td colspan="5" class="text-center">Belum ada barang</td></tr>');
            } else {
                cart.forEach((item, index) => {
                    let total = item.qty * item.harga;
                    totalHarga += total;
                    tbody.append(`
                        <tr>
                            <td>${item.nama}</td>
                            <td>Rp ${item.harga}</td>
                            <td>
                                <input type="number" min="1" max="${item.stok}" value="${item.qty}" class="form-control form-control-sm" onchange="ubahQty(${index}, this.value)" >
                            </td>
                            <td>Rp ${total}</td>
                            <td><button class="btn btn-danger btn-sm" onclick="hapusBarang(${index})">Hapus</button></td>
                        </tr>
                    `);
                });
            }
            $("#total-harga").text(`Rp ${totalHarga}`);
        }

        // Validasi perubahan jumlah barang
        function ubahQty(index, qty) {
            qty = parseInt(qty);
            let stokMaks = cart[index].stok;

            if (qty > stokMaks) {
                Swal.fire({
                    icon: "warning",
                    title: "Stok Tidak Cukup",
                    text: `Maksimal stok tersedia: ${stokMaks}`,
                    timer: 1700,
                    timerProgressBar: true,
                });
                cart[index].qty = stokMaks;
            } else {
                cart[index].qty = qty;
            }
            renderCart();
        }

        function hapusBarang(index) {
            cart.splice(index, 1);
            renderCart();
        }

        // Proses Bayar
        $("#btnProsesBayar").click(function() {
            let total = parseFloat($("#modalTotal").val().replace(/[^\d]/g, "")) || 0;
            let uang = parseFloat($("#uangPelanggan").val()) || 0;
            let kembalian = uang - total;
            let idMember = $("#id_member").val() || null;

            if (!Array.isArray(cart) || cart.length === 0) {
                Swal.fire({
                    icon: "error",
                    title: "Keranjang Kosong",
                    text: "Tambahkan barang sebelum memproses transaksi!",
                    timer: 1700,
                    timerProgressBar: true,
                });
                return;
            }

            // Kirim transaksi ke backend
            $.ajax({
                url: "{{ route('transaksi.store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    member_id: idMember,
                    total_harga: total,
                    uang_pelanggan: uang,
                    cart: cart
                },
                success: function(response) {
                    $('#print-area').html(response.invoice_html);

                    Swal.fire({
                        icon: 'success',
                        title: 'Pembayaran Berhasil!',
                        text: 'Apakah Anda ingin mencetak struk?',
                        showCancelButton: true,
                        confirmButtonText: 'Cetak Struk',
                        cancelButtonText: 'Tidak',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // 1. Ambil ID Transaksi dari response
                            const transaksiId = response
                                .id_transaksi; // Pastikan ini dikirim dari backend!

                            // 2. Ambil data transaksi terbaru via AJAX
                            $.ajax({
                                url: `/transaksi/${transaksiId}`,
                                type: 'GET',
                                success: function(detail) {
                                    $('#no_faktur').text(detail.no_faktur);
                                    $('#tgl_faktur').text(detail.tgl_faktur);
                                    $('#kasir').text(detail.user.name);
                                    $('#member').text(detail.member ? detail.member
                                        .nama : '-');
                                    $('#total_bayar').text("Rp " + new Intl
                                        .NumberFormat('id-ID').format(detail
                                            .total_bayar));
                                    $('#uang_pelanggan').text("Rp " + new Intl
                                        .NumberFormat('id-ID').format(response
                                            .uang_pelanggan));
                                    $('#kembalian').text("Rp " + new Intl
                                        .NumberFormat('id-ID').format(response
                                            .kembalian));


                                    let totalQty = 0;
                                    let html = "";
                                    detail.detail_penjualan.forEach(item => {
                                        totalQty += item.jumlah;
                                        html += `
                            <tr>
                                <td class="text-left">${item.nama_produk}</td>
                                <td class="text-center">${item.jumlah}</td>
                                <td class="text-right"><span class="currency">Rp</span> ${new Intl.NumberFormat('id-ID').format(item.sub_total)}</td>
                            </tr>`;
                                    });

                                    $('#detail-produk').html(html);
                                    $('#total_qty').text(totalQty);

                                    // Tutup modal pembayaran dan tampilkan struk setelahnya
                                    $('#modalPembayaran').modal('hide').one(
                                        'hidden.bs.modal',
                                        function() {
                                            $('#detailModal').modal('show');
                                        });

                                    // Saat modal struk ditutup, bersihkan semua data
                                    $('#detailModal').on('hidden.bs.modal',
                                        function() {
                                            // Kosongkan cart
                                            cart = [];
                                            localStorage.removeItem(
                                                "invoiceTerakhir");

                                            // Kosongkan input
                                            $("#uangPelanggan").val('');
                                            $("#id_member").val('');
                                            $("#memberInfo").text('-');
                                            $("#modalTotal").val('Rp 0');
                                            $("#cartTable tbody").html('');

                                            // Refresh halaman biar semua bersih
                                            location.reload();
                                        });
                                },
                                error: function(err) {
                                    console.log(err);
                                    Swal.fire("Error",
                                        "Gagal mengambil detail transaksi",
                                        "error");
                                }
                            });

                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: 'Terima kasih!',
                                text: 'Transaksi telah selesai',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false,
                            }).then(() => location.reload());
                        }
                    });

                    localStorage.setItem("invoiceTerakhir", response.invoice_html);
                },

                error: function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: "error",
                        title: "Gagal Melakukan Transaksi",
                        text: xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan",
                    });
                }
            });
        });

        $('#printBtn').on('click', function() {
            window.print();
        });


        // Ambil data barang berdasarkan kode
        $("#kode_barang").keypress(function(e) {
            if (e.which === 13) {
                let kode = $(this).val();
                let jumlah = parseInt($("#jumlah_barang").val()) || 1;

                $.get(`/api/barang/${kode}`)
                    .done(function(response) {
                        if (response.status === "out_of_stock") {
                            Swal.fire({
                                icon: "error",
                                title: "Stok Habis",
                                text: response.message,
                                timer: 1700,
                                timerProgressBar: true,
                            }).then(() => {
                                $("#kode_barang").val('');
                                $("#jumlah_barang").val('');
                            });
                            return;
                        }

                        let stokTersedia = response.stok_toko;
                        if (jumlah > stokTersedia) {
                            Swal.fire({
                                icon: "warning",
                                title: "Stok Tidak Cukup",
                                text: `Maksimal stok tersedia: ${stokTersedia}`,
                                timer: 1700,
                                timerProgressBar: true,
                            });
                            jumlah = stokTersedia;
                        }

                        if (stokTersedia > 0) {
                            tambahBarang(response, jumlah);
                        }

                        $("#kode_barang").val('');
                        $("#jumlah_barang").val('');
                    })
                    .fail(function(xhr) {
                        if (xhr.status === 404) {
                            Swal.fire({
                                icon: "error",
                                title: "Barang Tidak Ditemukan",
                                text: "Barang dengan kode ini tidak ada!",
                                timer: 1700,
                                timerProgressBar: true,
                            }).then(() => {
                                $("#kode_barang").val('');
                                $("#jumlah_barang").val('');
                            });
                        }
                    });
            }

        });


        //megnambil nama member berdasarkan no_telp
        $(document).ready(function() {
            $("#no_telp").keypress(function(e) {
                if (e.which === 13) { // Jika tombol Enter ditekan
                    let noTelp = $(this).val().trim();

                    if (noTelp === "") {
                        $("#nama_member").text("-");
                        $("#id_member").val("");
                        return;
                    }

                    $.get("{{ route('api.cari-member') }}", {
                        no_telp: noTelp
                    }, function(response) {
                        if (response.status === "success") {
                            $("#nama_member").text(` ${response.nama}`);
                            $("#id_member").val(response.id_member); // Simpan ID Member di form

                            // Notifikasi sukses
                            Swal.fire({
                                icon: "success",
                                title: "Member Ditemukan",
                                text: `Nama: ${response.nama}`,
                                timer: 1700,
                                timerProgressBar: true,
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Member Tidak Ditemukan",
                                text: "Pastikan nomor HP sudah terdaftar",
                                timer: 1700,
                                timerProgressBar: true,
                            });

                            $("#nama_member").text("-");
                            $("#id_member").val(""); // Reset ID jika tidak ditemukan
                        }
                    });
                }
            });
        });


        $("#btnBayar").click(function() {
            let total = $("#total-harga").text().replace("Rp ", "");
            $("#modalTotal").val(`Rp ${total}`);
            $("#modalKembalian").val("Rp 0");
            $("#modalPembayaran").modal('show');
        });



        function hitungKembalian() {
            let total = parseFloat($("#total-harga").text().replace(/[^\d]/g, '')) || 0;
            let uang = parseFloat($("#uangPelanggan").val().replace(/[^\d]/g, '')) || 0;

            if (uang < total || uang === 0) {
                $("#modalKembalian").val("Rp 0");
                $("#btnProsesBayar").prop("disabled", true);
                return;
            }

            let kembalian = uang - total;

            $("#modalKembalian").val("Rp " + kembalian.toLocaleString("id-ID"));
            $("#btnProsesBayar").prop("disabled", false);
        }


        // Jalankan saat input berubah
        $("#uangPelanggan").on("input", function() {
            this.value = this.value.replace(/[^\d]/g, '');
            hitungKembalian();
        });


        // Kamera Scanner menggunakan ZXing
        document.addEventListener("DOMContentLoaded", function() {
            let scanner = new ZXing.BrowserBarcodeReader();
            let videoElement = document.getElementById("camera-stream");
            let lastScannedCode = ""; // Simpan kode terakhir
            let scanCooldown = false; // Cegah scan terlalu cepat

            // Tambahkan suara beep dari file lokal
            const beepSound = new Audio("/sounds/beep.mp3");

            // Debugging: Pastikan suara bisa diputar
            beepSound.addEventListener("canplaythrough", () => {
                console.log("✅ Beep sound ready to play!");
            });

            function startScanner() {
                scanner.decodeFromVideoDevice(null, videoElement, (result, err) => {
                    if (result) {
                        let scannedCode = result.text;

                        if (!scanCooldown && scannedCode !== lastScannedCode) {
                            scanCooldown = true;
                            lastScannedCode = scannedCode;

                            console.log("📸 Barcode Terdeteksi:", scannedCode);
                            $("#kode_barang").val(scannedCode);

                            // Simulasikan tekan ENTER untuk mencari barang
                            let e = jQuery.Event("keypress");
                            e.which = 13;
                            $("#kode_barang").trigger(e);

                            // Mainkan suara beep
                            beepSound.currentTime = 0; // Reset waktu agar bisa diputar ulang
                            beepSound.play().catch(error => console.error("🔴 Gagal memutar suara:",
                                error));

                            // cooldown scan
                            setTimeout(() => {
                                scanCooldown = false;
                                lastScannedCode = "";
                            }, 1500);
                        }
                    }
                });
            }

            function stopScanner() {
                scanner.reset();
            }

            $("#scanner-box").click(function() {
                if (videoElement.style.display === "none") {
                    videoElement.style.display = "block";
                    startScanner();
                } else {
                    videoElement.style.display = "none";
                    stopScanner();
                }
            });
        });

        async function cetakInvoice() {
            try {
                // Tampilkan loading
                const loading = Swal.fire({
                    title: 'Memuat struk terakhir...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                // 1. Gunakan endpoint yang benar
                const response = await fetch("/api/transaksi/terakhir", {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                // 2. Handle error response
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Gagal mengambil data');
                }

                // 3. Parse data
                const {
                    data
                } = await response.json();
                loading.close();

                // 4. Tampilkan di modal (pastikan ID element sesuai)
                $('#no_faktur').text(data.no_faktur || '-');
                $('#tgl_faktur').text(new Date(data.tanggal).toLocaleString('id-ID') || '-');
                $('#kasir').text(data.kasir || '-');

                // Isi tabel produk
                const itemsHtml = data.items.map(item => `
      <tr>
        <td>${item.produk}</td>
        <td class="text-center">${item.qty}</td>
        <td class="text-right">Rp ${item.subtotal.toLocaleString('id-ID')}</td>
      </tr>
    `).join('');
                $('#detail-produk').html(itemsHtml);

                // Isi total
                $('#total_qty').text(data.total.qty);
                $('#total_bayar').text(`Rp ${data.total.amount.toLocaleString('id-ID')}`);
                $('#uang_pelanggan').text(`Rp ${data.total.paid.toLocaleString('id-ID')}`);
                $('#kembalian').text(`Rp ${data.total.change.toLocaleString('id-ID')}`);

                // Tampilkan modal
                $('#detailModal').modal('show');

            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.message || 'Terjadi kesalahan',
                    confirmButtonColor: '#d33'
                });
                console.error('Error:', error);
            }
        }

        // $(document).ready(function() {
        //     // Cetak struk ketika tombol print diklik
        //     $('#printBtn').on('click', function() {
        //         const receipt = document.getElementById('receipt');
        //         const printWindow = window.open('', '', 'width=300,height=600');
        //         printWindow.document.write(receipt.innerHTML);
        //         printWindow.document.close();
        //         printWindow.print();
        //         setTimeout(function() {
        //             printWindow.close();
        //         }, 100);
        //     });
        // });
    </script>
@endpush
