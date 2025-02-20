<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">


    <title>BianMart-Admin</title>

    <link href="{{ asset('assets') }}/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="{{ asset('assets') }}/css/sb-admin-2.min.css" rel="stylesheet">


    <link href="{{ asset('assets') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">

    @stack('style')
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center"
                href="{{ route('admin.dashboard') }}">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-store"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Bian Mart</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="#">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
            </li>

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
                        <h6 class="collapse-header">Halaman</h6>
                        <a class="collapse-item" href="#">Daftar Produk</a>
                        <a class="collapse-item" href="{{ route('kategori.index') }}">Kategori Produk</a>
                        <a class="collapse-item" href="#">Barang Ready (Display)</a>
                        <a class="collapse-item" href="#">Stok Produk</a>
                        {{-- <a class="collapse-item" href="{{ route('produk.index') }}">Daftar Produk</a>
                        <a class="collapse-item" href="{{ route('produk.ready') }}">Barang Ready (Display)</a>
                        <a class="collapse-item" href="{{ route('stok.index') }}">Stok Produk</a> --}}
                    </div>
                </div>
            </li>

            <!-- Transaksi -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTransaksi"
                    aria-expanded="true" aria-controls="collapseTransaksi">
                    <i class="fas fa-fw fa-cash-register"></i>
                    <span>Transaksi</span>
                </a>
                <div id="collapseTransaksi" class="collapse" aria-labelledby="headingTransaksi"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Halaman</h6>
                        <a class="collapse-item" href="W">Kasir</a>
                        <a class="collapse-item" href="W">Riwayat Transaksi</a>
                        <a class="collapse-item" href="#">Refund / Retur Barang</a>
                        {{-- <a class="collapse-item" href="{{ route('transaksi.kasir') }}">Kasir</a>
                        <a class="collapse-item" href="{{ route('transaksi.riwayat') }}">Riwayat Transaksi</a>
                        <a class="collapse-item" href="{{ route('transaksi.retur') }}">Refund / Retur Barang</a> --}}
                    </div>
                </div>
            </li>

            <!-- Mutasi Stok -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMutasi"
                    aria-expanded="true" aria-controls="collapseMutasi">
                    <i class="fas fa-fw fa-exchange-alt"></i>
                    <span>Mutasi Stok</span>
                </a>
                <div id="collapseMutasi" class="collapse" aria-labelledby="headingMutasi"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Halaman</h6>
                        <a class="collapse-item" href="#">Pemindahan Barang</a>
                        <a class="collapse-item" href="#">Riwayat Mutasi</a>
                        {{-- <a class="collapse-item" href="{{ route('mutasi.create') }}">Pemindahan Barang</a>
                        <a class="collapse-item" href="{{ route('mutasi.riwayat') }}">Riwayat Mutasi</a> --}}
                    </div>
                </div>
            </li>

            <!-- Laporan -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan"
                    aria-expanded="true" aria-controls="collapseLaporan">
                    <i class="fas fa-fw fa-chart-line"></i>
                    <span>Laporan</span>
                </a>
                <div id="collapseLaporan" class="collapse" aria-labelledby="headingLaporan"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Halaman</h6>
                        <a class="collapse-item" href="#">Laporan Penjualan</a>
                        <a class="collapse-item" href="#">Laporan Stok</a>
                        <a class="collapse-item" href="#">Laporan Keuangan</a>
                        {{-- <a class="collapse-item" href="{{ route('laporan.penjualan') }}">Laporan Penjualan</a>
                        <a class="collapse-item" href="{{ route('laporan.stok') }}">Laporan Stok</a>
                        <a class="collapse-item" href="{{ route('laporan.keuangan') }}">Laporan Keuangan</a> --}}
                    </div>
                </div>
            </li>

            <!-- Pengelolaan Pengguna -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePengguna"
                    aria-expanded="true" aria-controls="collapsePengguna">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Pengelolaan Pengguna</span>
                </a>
                <div id="collapsePengguna" class="collapse" aria-labelledby="headingPengguna"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Halaman</h6>
                        <a class="collapse-item" href="#">Daftar Pengguna</a>
                        <a class="collapse-item" href="#">Hak Akses & Role</a>
                        {{-- <a class="collapse-item" href="{{ route('pengguna.index') }}">Daftar Pengguna</a>
                        <a class="collapse-item" href="{{ route('pengguna.role') }}">Hak Akses & Role</a> --}}
                    </div>
                </div>
            </li>

            <!-- Pengaturan -->
            <li class="nav-item">
                <a class="nav-link" href="#">
                    {{-- <a class="nav-link" href="{{ route('pengaturan.index') }}"> --}}
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Pengaturan</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Sidebar Toggler (Sidebar) -->
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
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small"
                                placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
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
