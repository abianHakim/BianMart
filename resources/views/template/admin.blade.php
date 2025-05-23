<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">


    <title>BianMart</title>

    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="{{ asset('assets') }}/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">


    <link href="{{ asset('assets') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">

    @stack('style')

    <style>
        .sidebar-brand {
            padding: 0.75rem 1rem;
        }

        .sidebar-brand-icon {
            font-size: 1.8rem;
        }

        .sidebar-brand-text {
            font-size: 1rem;
            font-weight: bold;
        }
    </style>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">


            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center text-center"
                href="{{ route('dashboard') }}">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-store fa-lg"></i>
                </div>
                <div class="sidebar-brand-text mx-2">Bian Mart</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            @if (Auth::user()->role == 'admin')
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="fas fa-fw fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            @endif

            @if (Auth::user()->role == 'admin')
                <!-- Manajemen Produk -->
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProduk"
                        aria-expanded="true" aria-controls="collapseProduk">
                        <i class="fas fa-fw fa-box"></i>
                        <span>Manajemen Produk</span>
                    </a>
                    <div id="collapseProduk" class="collapse" aria-labelledby="headingProduk"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item" href="{{ route('produk.index') }}">Daftar Produk</a>
                            <a class="collapse-item" href="{{ route('kategori.index') }}">Kategori Produk</a>
                        </div>
                    </div>
                </li>

                <!-- Manajemen Stok -->
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseStok"
                        aria-expanded="true" aria-controls="collapseStok">
                        <i class="fas fa-fw fa-warehouse"></i>
                        <span>Manajemen Stok</span>
                    </a>
                    <div id="collapseStok" class="collapse" aria-labelledby="headingStok"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item" href="{{ route('supplier.index') }}">Manajemen Supplier</a>
                            <a class="collapse-item" href="{{ route('penerimaan.index') }}">Penerimaan Barang</a>
                            <a class="collapse-item" href="{{ route('stokbarang.index') }}">Stok Produk</a>
                            <a class="collapse-item" href="{{ route('batchstok.index') }}">Batch Stok</a>
                        </div>
                    </div>
                </li>
            @endif

            <!-- Barang di Display -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDisplay"
                    aria-expanded="true" aria-controls="collapseDisplay">
                    <i class="fas fa-fw fa-store"></i>
                    <span>Barang di Display</span>
                </a>
                <div id="collapseDisplay" class="collapse" aria-labelledby="headingDisplay"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('displayBarang.index') }}">Daftar Barang Ready</a>
                        @if (Auth::user()->role == 'admin')
                            <a class="collapse-item" href="{{ route('mutasiStok.index') }}">Mutasi Stok</a>
                        @endif
                    </div>
                </div>
            </li>

            <!-- Transaksi -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTransaksi"
                    aria-expanded="true" aria-controls="collapseTransaksi">
                    <i class="fas fa-fw fa-shopping-cart"></i>
                    <span>Transaksi</span>
                </a>
                <div id="collapseTransaksi" class="collapse" aria-labelledby="headingTransaksi"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('transaksi.index') }}">Kasir</a>
                        <a class="collapse-item" href="{{ route('transaksi.riwayat') }}">Riwayat Transaksi</a>
                    </div>
                </div>
            </li>

            <!-- absensi -->
            <li class="nav-item">
                <a class="nav-link" href="{{route('absensi.index')}}">
                    <i class="fas fa-fw fa-calendar-check"></i>
                    <span>Absensi Karyawan</span>
                </a>
            </li>

            <!-- Pengajuan Barang -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('pengajuan.all') }}">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>Pengajuan Barang</span>
                </a>
            </li>

            @if (Auth::user()->role == 'admin')
                <!-- Logs Sistem -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logs.index') }}">
                        <i class="fas fa-fw fa-clipboard-list"></i>
                        <span>Logs Sistem</span>
                    </a>
                </li>
            @endif

            <!-- Laporan -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan"
                    aria-expanded="true" aria-controls="collapseLaporan">
                    <i class="fas fa-fw fa-chart-bar"></i>
                    <span>Laporan</span>
                </a>
                <div id="collapseLaporan" class="collapse" aria-labelledby="headingLaporan"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('laporan.penjualan') }}">Laporan Penjualan</a>
                        @if (Auth::user()->role == 'admin')
                            <a class="collapse-item" href="{{ route('laporan.pembelian') }}">Laporan Pembelian</a>
                            {{-- <a class="collapse-item" href="#">Laporan Stok</a> --}}
                        @endif
                    </div>
                </div>
            </li>

            @if (Auth::user()->role == 'admin')
                <!-- Daftar Member -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('member.index') }}">
                        <i class="fas fa-fw fa-users"></i>
                        <span>Daftar Member</span>
                    </a>
                </li>

                <!-- Pengaturan -->
                {{-- <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse"
                        data-target="#collapsePengaturan" aria-expanded="true" aria-controls="collapsePengaturan">
                        <i class="fas fa-fw fa-cogs"></i>
                        <span>Pengaturan</span>
                    </a>
                    <div id="collapsePengaturan" class="collapse" aria-labelledby="headingPengaturan"
                        data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item" href="#">Pengaturan Akun</a>
                            <a class="collapse-item" href="#">Pengaturan Sistem</a>
                        </div>
                    </div>
                </li> --}}
            @endif

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Sidebar Toggler -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>

        <!-- End of Sidebar -->
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        {{-- <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small"
                                placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div> --}}
                    </form>
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span
                                    class="mr-2 d-none d-lg-inline text-gray-700 small">{{ ucwords(Auth::user()->name) }}</span>
                                <i class="fas fa-chevron-down fa-sm fa-fw mr-2 text-gray-400"></i>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">

                                <a class="dropdown-item" href="#" data-toggle="modal"
                                    data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->
                <!-- Begin Page Content -->
                <div class="container-fluid">


                    @yield('content')


                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->
            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Abian 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('assets') }}/vendor/jquery/jquery.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="{{ asset('assets') }}/vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="{{ asset('assets') }}/js/sb-admin-2.min.js"></script>
    <!-- Page level plugins -->
    <script src="{{ asset('assets') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('assets') }}/js/demo/datatables-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    @stack('script')

    <script>
        (function() {
            window.history.replaceState(null, null, window.location.href);
        })();
    </script>
</body>

</html>
<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg rounded">
            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="logoutModalLabel">
                    Konfirmasi Logout
                </h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Body -->
            <div class="modal-body text-center">
                <p class="mb-3 font-weight-bold text-dark">
                    Pastikan semua data barang inventaris telah diperbarui
                    sebelum logout.
                </p>
            </div>
            <!-- Footer -->
            <div class="modal-footer justify-content-center">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">
                    Batal
                </button>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
