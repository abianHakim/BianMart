@extends('template.admin')

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <style>
        .mutasi-row .form-control {
            width: 100%;
        }

        .select2-container {
            width: 100% !important;
        }

        .jumlah-mutasi {
            text-align: center;
        }

        .input-group .input-group-text {
            background: #f8f9fa;
            border-left: none;
        }

        .input-group input {
            border-right: none;
        }
    </style>
@endpush


@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Mutasi Stok</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">History Mutasi Stok</h6>

            <div class="d-flex align-items-center">
                <input type="date" id="start_date" class="form-control mr-2" value="{{ request('start_date') }}">
                <input type="date" id="end_date" class="form-control mr-2" value="{{ request('end_date') }}">
                <button class="btn btn-secondary btn-sm" id="resetFilter" style="width: 100px;">
                    <i class="fas fa-sync"></i> Reset
                </button>
            </div>

            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalMutasi">
                <i class="fas fa-plus"></i> Tambah Mutasi
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Barang</th>
                            <th>Arah Mutasi</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                            <th>Detal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historyMutasi as $mutasi)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $mutasi->produk->nama_barang }}</td>
                                <td>
                                    {{ $mutasi->dari_lokasi == 'gudang' ? 'Gudang → Toko' : 'Toko → Gudang' }}
                                </td>
                                <td>{{ $mutasi->jumlah }}</td>
                                <td>{{ $mutasi->tgl_mutasi }}</td>
                                <td>
                                    <button class="btn btn-info btn-sm btn-detail" data-toggle="modal"
                                        data-target="#modalDetail" data-produk="{{ $mutasi->produk->nama_barang }}"
                                        data-jumlah="{{ $mutasi->jumlah }}" data-dari="{{ $mutasi->dari_lokasi }}"
                                        data-ke="{{ $mutasi->ke_lokasi }}" data-tanggal="{{ $mutasi->tgl_mutasi }}"
                                        data-keterangan="{{ $mutasi->keterangan ?? 'Tidak ada keterangan' }}">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

<!-- Modal Mutasi Stok -->
<div class="modal fade" id="modalMutasi" tabindex="-1" aria-labelledby="modalMutasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Form Mutasi Stok</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formMutasi" method="POST" action="{{ route('mutasiStok.proses') }}">
                    @csrf

                    <div id="mutasi-container">
                        <div class="mutasi-row row align-items-center mb-2">
                            <!-- Tombol Trash di Kiri -->
                            <div class="col-md-1 text-left">
                                <button type="button" class="btn btn-danger btn-sm remove-row">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <!-- Pilihan Barang -->
                            <div class="col-md-4">
                                <select class="form-control select2 barang-select" name="barang_id[]" required>
                                    <option value="" selected disabled>Pilih Barang</option>
                                    @foreach ($stokBarang as $barang)
                                        <option value="{{ $barang->id }}"
                                            data-stok-gudang="{{ $barang->stok_gudang }}"
                                            data-stok-toko="{{ $barang->stok_toko }}">
                                            {{ $barang->produk->nama_barang }}
                                            (Gudang: {{ $barang->stok_gudang }},
                                            Toko: {{ $barang->stok_toko }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Arah Mutasi -->
                            <div class="col-md-3">
                                <select class="form-control tipe-mutasi" name="tipe_mutasi[]" required>
                                    <option value="" selected disabled>Pilih Arah Mutasi</option>
                                    <option value="gudang_ke_toko">Gudang → Toko</option>
                                    <option value="toko_ke_gudang">Toko → Gudang</option>
                                </select>
                            </div>

                            <!-- Jumlah Mutasi -->
                            <div class="col-md-2">
                                <input type="number" class="form-control jumlah-mutasi" name="jumlah[]" min="1"
                                    required>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Tambah dan Simpan -->
                    <div class="d-flex justify-content-between mt-3">
                        <button type="button" class="btn btn-secondary btn-sm" id="addRow">
                            <i class="fas fa-plus"></i> Tambah Produk
                        </button>
                        <button type="submit" class="btn btn-success btn-sm">Simpan Mutasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- modal detail --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDetailLabel">Detail Mutasi Stok</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nama Barang:</strong></p>
                        <p><strong>Arah Mutasi:</strong></p>
                        <p><strong>Jumlah:</strong></p>
                        <p><strong>Tanggal Mutasi:</strong></p>
                    </div>
                    <div class="col-md-6">
                        <p id="detailProduk"></p>
                        <p id="detailArah"></p>
                        <p id="detailJumlah"></p>
                        <p id="detailTanggal"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


@push('script')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            function initSelect2() {
                $('.select2').select2({
                    width: '100%',
                    placeholder: "Pilih Barang",
                    allowClear: true
                });
            }
            initSelect2();

            // Fungsi untuk memperbarui stok maksimal berdasarkan arah mutasi
            function updateMaxStok(row) {
                let selectedBarang = row.find('.barang-select option:selected');
                let tipeMutasi = row.find('.tipe-mutasi').val();
                let stokGudang = selectedBarang.data('stok-gudang') || 0;
                let stokToko = selectedBarang.data('stok-toko') || 0;
                let stokMax = tipeMutasi === 'gudang_ke_toko' ? stokGudang : stokToko;

                row.find('.jumlah-mutasi').attr('max', stokMax);
                row.find('.jumlah-mutasi').attr('placeholder', `Maks: ${stokMax}`);
                row.find('.stok-max').text(stokMax);
            }

            // Event listener untuk perubahan barang atau tipe mutasi
            $('#mutasi-container').on('change', '.barang-select, .tipe-mutasi', function() {
                let row = $(this).closest('.mutasi-row');
                updateMaxStok(row);
            });

            // Validasi input jumlah mutasi agar tidak melebihi stok maksimal
            $('#mutasi-container').on('input', '.jumlah-mutasi', function() {
                let row = $(this).closest('.mutasi-row');
                let maxStok = parseInt(row.find('.jumlah-mutasi').attr('max')) || 0;
                let jumlahInput = parseInt($(this).val()) || 0;

                if (jumlahInput > maxStok) {
                    Swal.fire({
                        icon: "warning",
                        title: "Jumlah Melebihi Stok",
                        text: `Stok maksimal yang tersedia hanya ${maxStok}`,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $(this).val(maxStok);
                }
            });

            // Menambah baris mutasi stok baru
            $('#addRow').click(function() {
                let newRow = `
            <div class="mutasi-row row align-items-center mb-2">
                <div class="col-md-1 text-left">
                    <button type="button" class="btn btn-danger btn-sm remove-row">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

                <div class="col-md-4">
                    <select class="form-control select2 barang-select" name="barang_id[]" required>
                        <option value="" selected disabled>Pilih Barang</option>
                        @foreach ($stokBarang as $barang)
                            <option value="{{ $barang->id }}" 
                                data-stok-gudang="{{ $barang->stok_gudang }}" 
                                data-stok-toko="{{ $barang->stok_toko }}">
                                {{ $barang->produk->nama_barang }} 
                                (Gudang: {{ $barang->stok_gudang }}, 
                                Toko: {{ $barang->stok_toko }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select class="form-control tipe-mutasi" name="tipe_mutasi[]" required>
                        <option value="" selected disabled>Pilih Arah Mutasi</option>
                        <option value="gudang_ke_toko">Gudang → Toko</option>
                        <option value="toko_ke_gudang">Toko → Gudang</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="number" class="form-control jumlah-mutasi" name="jumlah[]" min="1" required>
                </div>
            </div>
            `;
                $('#mutasi-container').append(newRow);
                initSelect2();
            });


            // Hapus baris mutasi stok
            $('#mutasi-container').on('click', '.remove-row', function() {
                $(this).closest('.mutasi-row').remove();
            });

            // Konfirmasi sebelum submit form mutasi stok
            $('#formMutasi').submit(function(event) {
                event.preventDefault();
                Swal.fire({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin memproses mutasi ini?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Lanjutkan"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: "Berhasil!",
                            text: "Mutasi stok telah diproses.",
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#formMutasi').off('submit').submit();
                        });
                    }
                });
            });

            // Fitur filter berdasarkan tanggal
            function applyFilter() {
                let startDate = $('#start_date').val();
                let endDate = $('#end_date').val();

                if (startDate && endDate) {
                    let url = "{{ route('mutasiStok.index') }}?start_date=" + startDate + "&end_date=" + endDate;
                    window.location.href = url;
                }
            }

            // Event listener untuk filter tanggal
            $('#start_date, #end_date').on('change', function() {
                applyFilter();
            });

            // Tombol Reset Filter
            $('#resetFilter').on('click', function() {
                $('#start_date').val('');
                $('#end_date').val('');
                window.location.href = "{{ route('mutasiStok.index') }}";
            });

        });
    </script>


    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: "Pilih Barang",
                allowClear: true
            });

            $('#barang_id').on('change', function() {
                let selectedItems = $(this).val();
                if (selectedItems.length > 0) {
                    $('#jumlah').prop('disabled', false);
                } else {
                    $('#jumlah').prop('disabled', true);
                }
            });

            function cekKelengkapan() {
                let barangDipilih = $('#barang_id').val() !== null;
                let tipeDipilih = $('#tipe_mutasi').val() !== null;

                if (barangDipilih && tipeDipilih) {
                    $('#jumlah').prop('disabled', false);
                } else {
                    $('#jumlah').prop('disabled', true).val('');
                    $('#stok-max').text('0');
                }
            }

            $('#barang_id, #tipe_mutasi').change(function() {
                let stokGudang = $('#barang_id option:selected').data('stok-gudang') || 0;
                let stokToko = $('#barang_id option:selected').data('stok-toko') || 0;
                let tipe = $('#tipe_mutasi').val();

                let stokMax = tipe === 'gudang_ke_toko' ? stokGudang : stokToko;
                $('#stok-max').text(stokMax);
                $('#jumlah').attr('max', stokMax);

                cekKelengkapan();
            });

            $('#jumlah').on('input', function() {
                let maxStok = parseInt($('#stok-max').text()) || 0;
                let jumlahInput = parseInt($(this).val()) || 0;

                if (jumlahInput > maxStok) {
                    Swal.fire({
                        icon: "warning",
                        title: "Jumlah Melebihi Stok",
                        text: `Stok maksimal yang tersedia hanya ${maxStok}`,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $(this).val(maxStok);
                }
            });

            $('#formMutasi').submit(function(event) {
                event.preventDefault();

                Swal.fire({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin memproses mutasi ini?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, Lanjutkan"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: "Berhasil!",
                            text: "Mutasi stok telah diproses.",
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#formMutasi').off('submit').submit(); // Kirim form ke server
                        });
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.btn-detail').click(function() {
                let produk = $(this).data('produk');
                let jumlah = $(this).data('jumlah');
                let dari = $(this).data('dari');
                let ke = $(this).data('ke');
                let tanggal = $(this).data('tanggal');
                let keterangan = $(this).data('keterangan');

                $('#detailProduk').text(produk);
                $('#detailArah').text(dari.charAt(0).toUpperCase() + dari.slice(1) + ' → ' + ke.charAt(0)
                    .toUpperCase() + ke.slice(1));
                $('#detailJumlah').text(jumlah);
                $('#detailTanggal').text(tanggal);
                // $('#detailKeterangan').text(keterangan);
            });
        });
    </script>
@endpush
