@extends('template.admin')

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            display: flex;
            align-items: center;
        }

        .form-control,
        .select2-container {
            height: 38px !important;
        }

        .table {
            table-layout: fixed;
            width: 100%;
        }

        .table th,
        .table td {
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }

        .table td select,
        .table td input {
            width: 100%;
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Penerimaan Barang</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('penerimaanBarang.store') }}" enctype="multipart/form-data"
                id="formPenerimaan">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="supplierSelect">Supplier</label>
                            <select id="supplierSelect" name="supplier_id" class="form-control select2" required>
                                <option value="" disabled selected>Pilih Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    @if ($supplier->produk->count() > 0)
                                        <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label>Produk</label>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Produk</th>
                                    <th>Jumlah</th>
                                    <th>Harga Beli</th>
                                    <th>Subtotal</th>
                                    <th>Tanggal Kedaluwarsa</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="produkList"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-success btn-sm mt-2" id="btnTambahProduk">+ Tambah
                        Produk</button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="fas fa-shopping-cart"></i> Beli
                        </button>
                    </div>
                    <div class="col-md-6 text-right">
                        <h4>Total: <span id="totalHarga">Rp 0</span></h4>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('#supplierSelect').change(function() {
                $('#produkList').empty();
                hitungTotal();
            });

            $('#btnTambahProduk').click(function() {
                let supplier_id = $('#supplierSelect').val();

                if (!supplier_id) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Supplier!',
                        text: 'Silakan pilih supplier terlebih dahulu.',
                        timer: 15000,
                        showConfirmButton: false
                    });
                    return;
                }

                $.ajax({
                    url: '{{ route('getProdukBySupplier') }}',
                    type: 'GET',
                    data: {
                        supplier_id: supplier_id
                    },
                    beforeSend: function() {
                        $('#btnTambahProduk').prop('disabled', true).text('Loading...');
                    },
                    success: function(response) {
                        if (response.length === 0) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Produk Tidak Tersedia!',
                                text: 'Tidak ada produk untuk supplier ini.',
                                timer: 15000,
                                showConfirmButton: false
                            });
                            $('#btnTambahProduk').prop('disabled', false).text(
                                '+ Tambah Produk');
                            return;
                        }

                        let existingProducts = [];
                        $('#produkList select[name="produk_id[]"]').each(function() {
                            existingProducts.push($(this).val());
                        });

                        let options =
                            '<option value="" disabled selected>Pilih Produk</option>';
                        response.forEach(product => {
                            if (!existingProducts.includes(product.id.toString())) {
                                options +=
                                    `<option value="${product.id}" data-harga="${product.harga_beli}">${product.nama_barang}</option>`;
                            }
                        });

                        if (options ===
                            '<option value="" disabled selected>Pilih Produk</option>') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Produk Sudah Dipilih!',
                                text: 'Semua produk sudah dipilih.',
                                timer: 15000,
                                showConfirmButton: false
                            });
                            $('#btnTambahProduk').prop('disabled', false).text(
                                '+ Tambah Produk');
                            return;
                        }

                        let newRow = `
                                <tr>
                                    <td>
                                        <select name="produk_id[]" class="form-control select2 produkSelect" required>${options}</select>
                                    </td>
                                    <td>
                                        <input type="number" name="jumlah[]" class="form-control jumlah" min="1" value="1" required>
                                    </td>
                                    <td>
                                        <input type="number" name="harga_beli[]" class="form-control harga_beli" required data-raw-value="0">
                                    </td>
                                    <td>
                                        <input type="number" name="sub_total[]" class="form-control sub_total" readonly data-raw-value="0">
                                    </td>
                                    <td>
                                        <input type="date" name="expired_date[]" class="form-control" required>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm btnHapusProduk">X</button>
                                    </td>
                                </tr>`;

                        $('#produkList').append(newRow);
                        $('.select2').select2();
                    },
                    complete: function() {
                        $('#btnTambahProduk').prop('disabled', false).text('+ Tambah Produk');
                    }
                });
            });

            // Atur harga beli otomatis dari database
            $(document).on('change', '.produkSelect', function() {
                let produk_id = $(this).val();
                let row = $(this).closest('tr');

                if (!produk_id) return;

                $.ajax({
                    url: "{{ route('getHargaBeli') }}",
                    type: 'GET',
                    data: {
                        produk_id: produk_id
                    },
                    success: function(response) {
                        row.find('.harga_beli').val(response.harga_beli).trigger('input');
                        row.find('.harga_beli').attr('data-raw-value', response.harga_beli);
                        hitungTotal();
                    }
                });
            });

            // Pastikan harga beli tetap angka, tetapi tetap terlihat format Rupiah
            $(document).on('input', '.harga_beli', function() {
                let harga = $(this).val().replace(/\D/g, '');
                if (harga === "") harga = "0";
                harga = parseInt(harga);
                $(this).val(harga);
                $(this).attr('data-raw-value', harga);
                hitungSubtotal($(this).closest('tr'));
            });

            // Menghitung subtotal berdasarkan harga beli dan jumlah
            $(document).on('input', '.jumlah, .harga_beli', function() {
                let row = $(this).closest('tr');
                hitungSubtotal(row);
            });

            function hitungSubtotal(row) {
                let jumlah = parseInt(row.find('.jumlah').val()) || 0;
                let hargaBeli = parseInt(row.find('.harga_beli').attr('data-raw-value')) || 0;
                let subTotal = jumlah * hargaBeli;
                row.find('.sub_total').val(subTotal);
                row.find('.sub_total').attr('data-raw-value', subTotal);

                hitungTotal();
            }

            function hitungTotal() {
                let total = 0;
                $('.sub_total').each(function() {
                    let subTotal = parseInt($(this).attr('data-raw-value')) || 0;
                    total += subTotal;
                });

                $('#totalHarga').text('Rp ' + total.toLocaleString('id-ID'));
            }

            // Hapus produk dari tabel
            $(document).on('click', '.btnHapusProduk', function() {
                $(this).closest('tr').remove();


                hitungTotal();
            });
        });

        $(document).ready(function() {
            $('#formPenerimaan').submit(function(event) {
                event.preventDefault(); // Mencegah form langsung dikirim

                Swal.fire({
                    title: "Konfirmasi Transaksi",
                    text: "Apakah Anda yakin ingin melakukan transaksi ini?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, Lanjutkan",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: "Sedang Memproses",
                            text: "Harap tunggu...",
                            icon: "info",
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Kirim form setelah konfirmasi
                        event.target.submit();
                    }
                });
            });
        });
    </script>
@endpush
