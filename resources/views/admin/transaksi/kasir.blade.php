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
    </div>
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
            <div class="modal-body">
                <div class="form-group">
                    <label>Total Belanja</label>
                    <input type="text" id="modalTotal" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Uang Pelanggan</label>
                    <input type="number" id="uangPelanggan" class="form-control">
                </div>
                <div class="form-group">
                    <label>Kembalian</label>
                    <input type="text" id="modalKembalian" class="form-control" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnProsesBayar" disabled>Proses</button>
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

        //proeses Bayar
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

            if (uang < total) {
                Swal.fire({
                    icon: "error",
                    title: "Pembayaran Kurang",
                    text: "Uang pelanggan kurang!",
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
                    Swal.fire({
                        icon: "success",
                        title: "Transaksi Berhasil",
                        text: `Pembayaran telah diproses! Kembalian: Rp ${kembalian.toLocaleString()}`,
                        timer: 2500,
                        timerProgressBar: true,
                    }).then(() => {
                        location.reload();
                    });
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

        $(document).ready(function() {
            $("#uangPelanggan").on("input", function() {
                let total = parseFloat($("#modalTotal").val().replace(/[^\d]/g, "")) || 0;
                let uang = parseFloat($(this).val()) || 0;
                let kembalian = uang - total;

                if (uang >= total) {
                    $("#modalKembalian").val(`Rp ${kembalian.toLocaleString()}`);
                    $("#btnProsesBayar").prop("disabled", false);
                } else {
                    $("#modalKembalian").val("Uang kurang!");
                    $("#btnProsesBayar").prop("disabled", true);
                }
            });
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
    </script>
@endpush
