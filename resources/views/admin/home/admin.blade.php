@extends('template.admin')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Dashboard Operator</h1>
        <div class="row">
            <!-- Produk Tersedia -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Produk Tersedia</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlah_produk_tersedia }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-boxes fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Transaksi Hari Ini -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Transaksi Hari Ini
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_transaksi }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-receipt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Pendapatan Hari Ini -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pendapatan Hari Ini
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp
                                    {{ number_format($pendapatan_hari_ini, 0, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Produk Hampir Habis -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total pendapatan
                                    Seluruh Transaksi
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp
                                    {{ number_format($pendapatan_keseluruhan_penjualan, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-coins fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ROW 2: Grafik -->
        <div class="row">
            <!-- Grafik Transaksi Live -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Grafik Live Transaksi Harian</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="liveTransactionChart"></canvas>

                    </div>
                </div>
            </div>

            <!-- Produk Terlaris -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Produk Terlaris</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="produkTerlarisChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Grafik Penjualan -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Grafik Penjualan Bulanan</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="penjualanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctxPenjualan = document.getElementById('penjualanChart').getContext('2d');
        const ctxProdukTerlaris = document.getElementById('produkTerlarisChart').getContext('2d');

        let gradient = ctxPenjualan.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(54, 162, 235, 0.5)');
        gradient.addColorStop(1, 'rgba(54, 162, 235, 0)');

        let bulan = @json($bulan);
        let totalPenjualan = @json($total_penjualan);
        let namaProduk = @json($nama_produk);
        let jumlahTerjual = @json($jumlah_terjual);

        if (bulan.length === 1) {
            bulan.unshift("Bulan Sebelumnya");
            totalPenjualan.unshift(0);
            bulan.push("Bulan Berikutnya");
            totalPenjualan.push(totalPenjualan[1] / 2);
        }

        const penjualanChart = new Chart(ctxPenjualan, {
            type: 'line',
            data: {
                labels: bulan,
                datasets: [{
                    label: 'Total Penjualan',
                    data: totalPenjualan,
                    backgroundColor: gradient,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: 'white',
                    pointBorderColor: 'rgba(54, 162, 235, 1)'
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'nearest',
                    intersect: false
                },
                plugins: {
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                let value = context.raw || 0;
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 10000,
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        const produkTerlarisChart = new Chart(ctxProdukTerlaris, {
            type: 'doughnut',
            data: {
                labels: namaProduk,
                datasets: [{
                    label: 'Total Terjual',
                    data: jumlahTerjual,
                    backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                return label + ': ' + value + ' pcs';
                            }
                        }
                    }
                }
            }
        });
    </script>

    <script>
        const ctxLive = document.getElementById('liveTransactionChart').getContext('2d');

        let liveTransactionChart = new Chart(ctxLive, {
            type: 'line',
            data: {
                labels: [], // Tanggal
                datasets: [{
                        label: 'Jumlah Transaksi',
                        data: [],
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: 'white',
                        pointBorderColor: 'rgba(255, 99, 132, 1)'
                    },
                    {
                        label: 'Total Pendapatan',
                        data: [],
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: 'white',
                        pointBorderColor: 'rgba(54, 162, 235, 1)'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'nearest', // Tooltip lebih responsif terhadap kursor
                    intersect: false
                },
                plugins: {
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                if (context.datasetIndex === 1) {
                                    return 'Rp ' + context.raw.toLocaleString('id-ID');
                                }
                                return context.raw + ' Transaksi';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        // Fungsi untuk mengambil data live dari API
        function updateLiveTransactionChart() {
            fetch('/admin/live-transactions')
                .then(response => response.json())
                .then(data => {
                    console.log('Data Live:', data); // Debugging untuk memastikan data masuk

                    if (!data || data.length === 0) {
                        console.warn('Data live kosong!');
                        return;
                    }

                    let labels = data.map(item => item.tanggal);
                    let jumlahTransaksi = data.map(item => item.jumlah_transaksi);
                    let totalPendapatan = data.map(item => item.total_pendapatan);

                    liveTransactionChart.data.labels = labels;
                    liveTransactionChart.data.datasets[0].data = jumlahTransaksi;
                    liveTransactionChart.data.datasets[1].data = totalPendapatan;
                    liveTransactionChart.update();
                })
                .catch(error => console.error('Error fetching live data:', error));
        }

        // Update grafik setiap 5 detik
        setInterval(updateLiveTransactionChart, 30000);
        updateLiveTransactionChart();
    </script>
@endpush
