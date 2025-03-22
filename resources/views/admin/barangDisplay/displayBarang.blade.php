@extends('template.admin')

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <style>
        /* Container utama */
        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: flex-start;
        }

        /* Kartu produk */
        .product-card {
            flex: 1 1 calc(25% - 15px);
            /* 4 kolom di layar besar */
            max-width: calc(25% - 15px);
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #ddd;
            transition: transform 0.2s, box-shadow 0.3s;
            position: relative;
        }

        /* Responsif untuk ukuran layar kecil */
        @media (max-width: 992px) {
            .product-card {
                flex: 1 1 calc(33.333% - 15px);
                /* 3 kolom */
                max-width: calc(33.333% - 15px);
            }
        }

        @media (max-width: 768px) {
            .product-card {
                flex: 1 1 calc(50% - 15px);
                /* 2 kolom */
                max-width: calc(50% - 15px);
            }
        }

        @media (max-width: 576px) {
            .product-card {
                flex: 1 1 calc(100% - 15px);
                /* 1 kolom */
                max-width: calc(100% - 15px);
            }
        }

        /* Efek hover */
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        /* Gambar produk */
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: contain;
            background: #f9f9f9;
        }

        /* Info produk */
        .product-info {
            padding: 10px;
        }

        .product-name {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .product-price {
            font-size: 18px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 5px;
        }

        .product-stock {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        /* Tombol */
        .btn-add-to-cart {
            width: 100%;
            font-size: 14px;
            font-weight: bold;
            padding: 8px;
            border-radius: 5px;
            background-color: #ff5a5f;
            color: white;
            border: none;
            transition: background 0.3s;
            display: block;
            text-align: center;
        }

        .btn-add-to-cart:hover {
            background-color: #e0484f;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <h1 class="h3 mb-4 text-gray-800">Display Barang</h1>
        <div class="product-container">
            @foreach ($stokBarang as $barang)
                <div class="product-card">
                    <img src="{{ asset('storage/' . $barang->produk->gambar) }}" alt="{{ $barang->produk->nama_barang }}"
                        class="product-image">
                    <div class="product-info">
                        <div class="product-name">{{ $barang->produk->nama_barang }}</div>
                        <div class="product-price">Rp {{ number_format($barang->produk->harga_jual, 0, ',', '.') }}</div>
                        <div class="product-stock">Stok: {{ $barang->stok_toko }}</div>
                        <a href="#" class="btn-add-to-cart">Tambah ke Keranjang</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('script')
@endpush
