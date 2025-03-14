@extends('template.admin')

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Mutasi Stok</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">History Mutasi Stok</h6>

            <div class="d-flex">
                <input type="date" id="start_date" class="form-control mr-2" value="{{ request('start_date') }}">
                <input type="date" id="end_date" class="form-control mr-2" value="{{ request('end_date') }}">
                <button class="btn btn-secondary btn-sm" id="resetFilter">
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historyMutasi as $index => $mutasi)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $mutasi->produk->nama_barang }}</td>
                                <td>
                                    {{ $mutasi->tipe_mutasi == 'gudang_ke_toko' ? 'Gudang → Toko' : 'Toko → Gudang' }}
                                </td>
                                <td>{{ $mutasi->jumlah }}</td>
                                <td>{{ $mutasi->tgl_mutasi }}</td>
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
    <div class="modal-dialog modal-lg"> <!-- Tambahkan class modal-lg agar lebih lebar -->
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
                    <div class="form-group">
                        <label for="barang_id">Pilih Barang</label>
                        <select class="form-control select2" id="barang_id" name="barang_id" required>
                            <option value="" selected disabled> Pilih Barang </option>
                            @foreach ($stokBarang as $barang)
                                <option value="{{ $barang->id }}" data-stok-gudang="{{ $barang->stok_gudang }}"
                                    data-stok-toko="{{ $barang->stok_toko }}">
                                    {{ $barang->produk->nama_barang }} (Gudang: {{ $barang->stok_gudang }}, Toko:
                                    {{ $barang->stok_toko }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tipe_mutasi">Arah Mutasi</label>
                        <select class="form-control" id="tipe_mutasi" name="tipe_mutasi" required>
                            <option value="" selected disabled> Pilih Arah Mutasi </option>
                            <option value="gudang_ke_toko">Dari Gudang ke Toko</option>
                            <option value="toko_ke_gudang">Dari Toko ke Gudang</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="jumlah">Jumlah Mutasi</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" min="1"
                            required>
                        <small class="text-muted">Maksimal: <span id="stok-max">0</span></small>
                    </div>

                    <button type="submit" class="btn btn-success">Simpan Mutasi</button>
                </form>
            </div>
        </div>
    </div>
</div>


@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });

            $('#jumlah').prop('disabled', true);

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
                let maxStok = parseInt($('#stok-max').text());
                let jumlahInput = parseInt($(this).val());

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
            function applyFilter() {
                let startDate = $('#start_date').val();
                let endDate = $('#end_date').val();

                if (startDate && endDate) {
                    let url = "{{ route('mutasiStok.index') }}?start_date=" + startDate + "&end_date=" + endDate;
                    window.location.href = url;
                }
            }

            // Filter otomatis ketika tanggal diubah
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
@endpush
