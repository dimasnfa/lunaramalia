@extends('landing.components.layout')

@section('title', 'Takeaway - Gemilang Cafe')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<input type="hidden" id="session-jenis" value="{{ session('jenis_pesanan') }}">
<input type="hidden" id="session-meja" value="{{ session('meja_id') }}">
<input type="hidden" id="session-wa" value="{{ session('takeaway.nomor_wa') }}">
<input type="hidden" id="recommendation-route" value="/takeaway/rekomendasi/get">

<!-- Hero Section Start -->
<div class="container-fluid py-6 my-6 mt-0" style="background-image: url('{{ asset('assets/img/banner/banner-gemilang.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="container text-center animated bounceInDown" style="background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px;">
        <h1 class="display-1 mb-4">Takeaway</h1>
        <ol class="breadcrumb justify-content-center mb-0 animated bounceInDown">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item text-dark" aria-current="page">Takeaway</li>
        </ol>
    </div>
</div>
<!-- Hero Section End -->

<!-- Pemanggilan CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/booking.css') }}">

<!-- CSS untuk Tab System dan Kategori - Updated with recommendation styles -->
<style>
/* Tab Navigation Styling */
.menu-tabs {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.nav-tabs {
    border: none;
    justify-content: center;
    flex-wrap: wrap;
}

.nav-tabs .nav-item {
    margin: 5px;
}

.nav-tabs .nav-link {
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    color: white;
    border-radius: 50px;
    padding: 12px 25px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.nav-tabs .nav-link:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.nav-tabs .nav-link.active {
    background: linear-gradient(45deg, #28a745, #20c997);
    border-color: #28a745;
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

/* Tab Content */
.tab-content {
    min-height: 500px;
}

.tab-pane {
    display: none;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.4s ease;
}

.tab-pane.active.show {
    display: block;
    opacity: 1;
    transform: translateY(0);
}

/* Menu Cards Enhancement */
.menu-card {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    height: 100%;
}

.menu-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.menu-card .card-img-top {
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.menu-card:hover .card-img-top {
    transform: scale(1.05);
}

.menu-card .card-body {
    padding: 20px;
    text-align: center;
}

.menu-title {
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
    font-size: 16px;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.menu-text {
    color: #666;
    margin-bottom: 8px;
}

.price-tag {
    background: linear-gradient(45deg, #ff6b6b, #ee5a52);
    color: white;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: bold;
    margin-bottom: 15px;
    display: inline-block;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
}

.stock-info {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 13px;
    margin-bottom: 15px;
    display: inline-block;
    font-weight: 600;
}

.stock-info.out-of-stock {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.add-to-cart-btn {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
    border-radius: 25px;
    padding: 12px 20px;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 13px;
}

.add-to-cart-btn:hover {
    background: linear-gradient(45deg, #20c997, #17a2b8);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
    color: white;
}

/* Category Header */
.category-header {
    text-align: center;
    margin: 40px 0 30px 0;
    position: relative;
}

.category-header h3 {
    background: linear-gradient(45deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: bold;
    font-size: 2rem;
    margin: 0;
    position: relative;
}

.category-header::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 3px;
    background: linear-gradient(45deg, #667eea, #764ba2);
    border-radius: 2px;
}

/* Empty State */
.empty-category {
    text-align: center;
    padding: 80px 20px;
    color: #6c757d;
}

.empty-category i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.3;
}

.empty-category h4 {
    margin-bottom: 10px;
    color: #495057;
}

.empty-category p {
    font-size: 14px;
    opacity: 0.8;
}

/* Loading Animation */
.loading-skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Recommendation Popup Enhancement - Added from dinein styles */
.recommendation-popup-custom {
    border-radius: 20px !important;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15) !important;
    overflow: hidden !important;
}

.recommendation-close-btn {
    background: rgba(255, 255, 255, 0.9) !important;
    color: #333 !important;
    border-radius: 50% !important;
    width: 35px !important;
    height: 35px !important;
    font-size: 18px !important;
    font-weight: bold !important;
    transition: all 0.3s ease !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.recommendation-close-btn:hover {
    background: rgba(220, 53, 69, 0.1) !important;
    color: #dc3545 !important;
    transform: scale(1.1) !important;
}

/* Recommendation header styling */
.recommendation-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    margin: -2rem -2rem 2rem -2rem;
    padding: 2rem;
    color: white;
    border-radius: 20px 20px 0 0;
}

.recommendation-header h3 {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 0.5rem;
}

.recommendation-header p {
    background: rgba(255, 255, 255, 0.1);
    padding: 0.75rem 1rem;
    border-radius: 25px;
    margin: 0;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Recommendation card enhancements */
.recommendation-card {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    position: relative;
    overflow: hidden;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 15px;
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
}

.recommendation-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transition: left 0.6s;
    z-index: 1;
}

.recommendation-card:hover::before {
    left: 100%;
}

.recommendation-card:hover {
    transform: translateY(-8px) !important;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    border-color: rgba(102, 126, 234, 0.3) !important;
}

.recommendation-card .card-img-top {
    transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
    position: relative;
    z-index: 0;
}

.recommendation-card:hover .card-img-top {
    transform: scale(1.08) !important;
}

/* Recommendation button styling */
.recommendation-add-btn {
    background: linear-gradient(45deg, #28a745, #20c997) !important;
    border: none !important;
    position: relative;
    overflow: hidden;
    z-index: 1;
    transition: all 0.3s ease !important;
    border-radius: 25px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.recommendation-add-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, #20c997, #17a2b8);
    transition: left 0.4s ease;
    z-index: -1;
}

.recommendation-add-btn:hover::before {
    left: 0;
}

.recommendation-add-btn:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4) !important;
    color: white !important;
}

/* Takeaway Info Alert */
.takeaway-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.takeaway-info .alert-heading {
    color: white;
    font-weight: bold;
}

/* Cart icon animations */
.cart-badge-animate {
    animation: cartBounce 0.6s ease-in-out;
}

@keyframes cartBounce {
    0%, 20%, 53%, 80%, 100% {
        transform: scale(1);
    }
    40%, 43% {
        transform: scale(1.2);
    }
    70% {
        transform: scale(1.1);
    }
    90% {
        transform: scale(1.05);
    }
}

.cart-icon-bounce {
    animation: iconBounce 0.8s ease-in-out;
}

@keyframes iconBounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-5px);
    }
}

.cart-icon-highlight {
    color: #28a745 !important;
    transition: color 0.8s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
    .nav-tabs .nav-link {
        padding: 10px 15px;
        font-size: 12px;
        margin: 3px;
    }
    
    .menu-card .card-img-top {
        height: 150px;
    }
    
    .menu-title {
        font-size: 14px;
        min-height: 35px;
    }
    
    .category-header h3 {
        font-size: 1.5rem;
    }
    
    .recommendation-popup-custom {
        width: 95% !important;
        margin: 1rem !important;
    }
}

@media (max-width: 480px) {
    .nav-tabs .nav-link {
        padding: 8px 12px;
        font-size: 11px;
    }
    
    .menu-card .card-img-top {
        height: 120px;
    }
    
    .menu-title {
        font-size: 13px;
        min-height: 30px;
    }
    
    .category-header h3 {
        font-size: 1.3rem;
    }
}

/* Smooth scroll for navigation links */
html {
    scroll-behavior: smooth;
}
</style>

{{-- Info Takeaway Status --}}
@if(session('jenis_pesanan') === 'takeaway')
<div class="container mb-4">
    <div class="alert takeaway-info text-center" role="alert">
        <h5 class="alert-heading mb-2">
            <i class="fas fa-shopping-bag me-2"></i>
            Data Takeaway Tersimpan
        </h5>
        <div class="row justify-content-center">
            <div class="col-md-6">
                  @if(session('takeaway.nama_pelanggan'))
                    <p class="mb-1"><strong>Nama Pelanggan :</strong> {{ session('takeaway.nama_pelanggan') }}</p>
                @endif
                <p class="mb-1"><strong>Jenis Pesanan :</strong> Takeaway </p>
                @if(session('takeaway.nomor_wa'))
                    <p class="mb-0"><strong>WhatsApp :</strong> {{ session('takeaway.nomor_wa') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Booking Section Start -->
<div class="booking-section">
    <div class="container text-center">
        <h1 class="section-title display-4">Gemilang Cafe & Saung</h1>
        <p class="section-subtitle">Pesan menu favorit Anda dengan mudah dan cepat!</p>
    </div>

    <div class="container">
        <h2 class="text-center text-dark fw-bold mb-4">Daftar Menu</h2>

        <!-- Tab Navigation -->
        <div class="menu-tabs">
            <ul class="nav nav-tabs" id="menuTabs" role="tablist">
                @foreach ($kategoris as $index => $kategori)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                id="{{ Str::slug($kategori->nama_kategori) }}-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#{{ Str::slug($kategori->nama_kategori) }}" 
                                type="button" 
                                role="tab" 
                                aria-controls="{{ Str::slug($kategori->nama_kategori) }}" 
                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            <i class="fas fa-{{ 
                                $kategori->nama_kategori === 'Makanan' ? 'utensils' : 
                                ($kategori->nama_kategori === 'Minuman' ? 'coffee' : 
                                ($kategori->nama_kategori === 'Nasi dan Mie' ? 'bowl-rice' : 
                                ($kategori->nama_kategori === 'Aneka Snack' ? 'cookie-bite' : 'list'))) 
                            }} me-2"></i>
                            {{ $kategori->nama_kategori }}
                            <span class="badge bg-light text-dark ms-2">{{ $kategori->menus->count() }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Tab Content -->
        <div class="tab-content" id="menuTabsContent">
            @foreach ($kategoris as $index => $kategori)
                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                     id="{{ Str::slug($kategori->nama_kategori) }}" 
                     role="tabpanel" 
                     aria-labelledby="{{ Str::slug($kategori->nama_kategori) }}-tab">
                    
                    <div class="category-header">
                        <h3>{{ $kategori->nama_kategori }}</h3>
                    </div>

                    @if($kategori->menus->count() > 0)
                        <div class="row" data-kategori="{{ $kategori->nama_kategori }}">
                            @foreach ($kategori->menus as $menu)
                                @php
                                    $folder = strtolower(str_replace([' ', '&'], '-', $kategori->nama_kategori));
                                    $defaultGambar = 'default.png';
                                    
                                    $customImages = [
                                        'Makanan' => [
                                            'Nasi' => 'nasi.png',
                                            'Ayam Goreng' => 'ayamgoreng.jpg',
                                            'Ayam Bakar' => 'ayambakar.jpg',
                                            'Ayam Mentega' => 'ayamentega.jpg',
                                            'Ayam Lada Hitam' => 'ayamladahitam.jpg',
                                            'Ayam Lombok Ijo' => 'ayamlombokijo.jpg',
                                            'Ayam Asam Manis' => 'ayamasammanis.jpg',
                                            'Ayam Saos Padang' => 'ayamsaospadang.jpg',
                                            'Ayam Rica Rica' => 'ayamricarica.jpg',
                                            'Sop Ayam' => 'sopayam.jpg',
                                            'Garang Asem Ayam' => 'garangasem.jpg',
                                            'Sambal Mentah' => 'sambalmentah.jpg',
                                            'Sambal Pecak' => 'sambalpecak.jpg',
                                            'Sambal Terasi' => 'sambalterasi.jpg',
                                            'Sambal Geprek' => 'sambalgeprek.jpg',
                                            'Sambal Bawang' => 'sambalbawang.jpg',
                                            'Sambal Ijo' => 'sambalijo.jpg',
                                            'Sambal Dabu Dabu' => 'sambaldabu.jpg',
                                        ],
                                        'Minuman' => [
                                            'Jus Alpukat' => 'alpukat.jpg',
                                            'Jus Apel' => 'apel.jpg',
                                            'Jus Strawberry' => 'strawberry.jpg',
                                            'Jus Jeruk' => 'jeruk.jpg',
                                            'Jus Tomat' => 'tomat.jpg',
                                            'Jus Mangga' => 'mangga.jpg',
                                            'Jus Melon' => 'melon.jpg',
                                            'Jus Fibar' => 'fibar.jpg',
                                            'Jus Wortel' => 'wortel.jpg',
                                            'Jeruk Panas' => 'jerukpanas.jpg',
                                            'Jeruk Dingin' => 'jerukdingin.jpg',
                                            'Teh Manis Panas' => 'tehmanis.jpg',
                                            'Teh Manis Dingin' => 'tehmanis.jpg',
                                            'Coffe Ekspresso' => 'coffekspresso.jpg',
                                            'Cappucino Ice' => 'cappucinoice.jpg',
                                            'Cappucino Hot' => 'cappucinohot.jpg',
                                            'Cofe Susu Gula Aren' => 'coffesusugularen.jpg',
                                            'Best Latte Ice' => 'bestlattehot.jpg',
                                            'Cofe Latte Ice' => 'coffelatteice.jpg',
                                            'Cofe Latte Hot' => 'coffelatehot.jpg',
                                            'Best Latte Hot' => 'bestlattehot.jpg',
                                            'Matcha Ice' => 'macthaice.jpg',
                                            'Matcha Hot' => 'matchahot.jpg',
                                            'Coklat Ice' => 'coklatice.jpg',
                                            'Coklat Hot' => 'coklathot.jpg',
                                            'Red Valvet Ice' => 'redvlvt.jpg',
                                            'Red Valvet Hot' => 'redvlvt.jpg',
                                            'Vakal Peach' => 'vakalpeach.jpg',
                                            'Beauty Peach' => 'beautypeach.jpg',
                                            'Teh Tubruk' => 'tehtubruk.jpg',
                                            'Teh Tubruk Susu' => 'tehtubruk2.jpg',
                                        ],
                                        'Nasi dan Mie' => [
                                            'Mie Goreng' => 'miegoreng.png',
                                            'Indomie Rebus' => 'indomierebus.png',
                                            'Indomie Goreng toping' => 'indomiegoreng.png',
                                            'Indomie Goreng+toping' => 'indomiegoreng.png',
                                            'Nasi Goreng Gemilang' => 'nasigorenggemilang.png',
                                            'Nasi Goreng Seafood' => 'nasigorengseafood.png',
                                            'Nasi Goreng Ayam' => 'nasigorengayam.png',
                                            'Kwetiau Goreng' => 'kwetiau.png',
                                            'Kwetiau Rebus' => 'kwetiaurebus.png',
                                        ],
                                       'Aneka Snack' => [
                                            'French Fries' => 'frenchfries.jpg',
                                            'Keong Racun' => 'keongracun.jpg',
                                            'Kongkou Snack' => 'KongkouSnack.jpg',
                                            'Nugget' => 'nugget.jpg',
                                            'Pisang Bakar' => 'pisangbakar.jpg',
                                            'Roti Bakar' => 'rotibakar.png',
                                            'Roti Bakar Keju Coklat' => 'rotibakarkejucoklat.jpg',
                                            'Sosis Goreng' => 'sosisgoreng.jpg',
                                            'Tahu Tepung' => 'tahutepung.jpg',
                                        ],
                                    ];
                                    $gambar = $menu->gambar ?? ($customImages[$kategori->nama_kategori][$menu->nama_menu] ?? $defaultGambar);
                                @endphp

                                <div class="col-lg-3 col-md-4 col-sm-6 mb-4" data-menu-id="{{ $menu->id }}" data-kategori="{{ $kategori->nama_kategori }}">
                                    <div class="card menu-card text-center">
                                        <img src="{{ asset("assets/img/{$folder}/{$gambar}") }}"
                                             class="card-img-top"
                                             alt="{{ $menu->nama_menu }}"
                                             onerror="this.onerror=null; this.src='{{ asset('assets/img/default.png') }}';">

                                        <div class="card-body">
                                            <h6 class="menu-title">{{ $menu->nama_menu }}</h6>
                                            
                                            <div class="price-tag">
                                                Rp. {{ number_format($menu->harga, 0, ',', '.') }}
                                            </div>
                                            
                                            <div class="stock-info {{ $menu->stok <= 0 ? 'out-of-stock' : '' }}">
                                                <i class="fas fa-{{ $menu->stok > 0 ? 'check-circle' : 'times-circle' }} me-1"></i>
                                                {{ $menu->stok > 0 ? 'Tersedia ('.$menu->stok.')' : 'Habis' }}
                                            </div>

                                            @if ($menu->stok > 0)
                                                <button class="btn add-to-cart-btn w-100"
                                                        data-id="{{ $menu->id }}"
                                                        data-name="{{ $menu->nama_menu }}"
                                                        data-price="{{ $menu->harga }}"
                                                        data-kategori="{{ $kategori->nama_kategori }}">
                                                    <i class="fas fa-cart-plus me-2"></i>
                                                    Tambah ke Keranjang
                                                </button>
                                            @else
                                                <button class="btn btn-secondary w-100" disabled>
                                                    <i class="fas fa-ban me-2"></i>
                                                    Tidak Tersedia
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-category">
                            <i class="fas fa-utensils"></i>
                            <h4>Menu Tidak Tersedia</h4>
                            <p>Maaf, kategori ini belum memiliki menu yang tersedia saat ini.</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
<!-- Booking Section End -->

<!-- Include Font Awesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Success/Error Messages from Session -->
@if(session('success'))
<script>
document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#4caf50',
        timer: 3000,
        timerProgressBar: true
    });
});
</script>
@endif

@if(session('error'))
<script>
document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
        icon: 'error',
        title: 'Oops!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#f44336',
        timer: 5000,
        timerProgressBar: true
    });
});
</script>
@endif

@if(session('jenis_pesanan') === 'takeaway')
<script>
document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
        icon: 'info',
        title: 'Data Takeaway Tersimpan',
        html: `
            <div style="text-align: center; padding: 10px;">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                           color: white; padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                    <h4 style="margin: 0; color: white;">
                        <i class="fas fa-shopping-bag" style="margin-right: 8px;"></i>
                        Takeaway 
                    </h4>
                </div>
                <div style="text-align: left; max-width: 250px; margin: 0 auto;">
                  @if(session('takeaway.nama_pelanggan'))
                    <p style="margin: 8px 0; font-size: 16px;">
                        <strong>Nama Pelanggan :</strong> 
                        <span style="color: #1976d2; font-weight: bold;">{{ session('takeaway.nama_pelanggan') }}</span>
                    </p>
                    @endif
                    <p style="margin: 8px 0; font-size: 16px;">
                        <strong>Jenis Pesanan  :</strong> 
                        <span style="color: #2e7d32; font-weight: bold;">Takeaway</span>
                    </p>
                    @if(session('takeaway.nomor_wa'))
                    <p style="margin: 8px 0; font-size: 16px;">
                        <strong>WhatsApp:</strong> 
                        <span style="color: #1976d2; font-weight: bold;">{{ session('takeaway.nomor_wa') }}</span>
                    </p>
                    @endif
                </div>
                <div style="margin-top: 15px; padding: 10px; background-color: #e8f5e8; 
                           border-radius: 8px; border-left: 4px solid #4caf50;">
                    <p style="margin: 0; color: #2e7d32; font-weight: 500;">
                        🍽 Silakan pilih menu favorit Anda!
                    </p>
                </div>
            </div>
        `,
        confirmButtonText: '🛒 Mulai Pesan',
        confirmButtonColor: '#4caf50',
        width: '450px',
        timer: 8000,
        timerProgressBar: true,
        allowOutsideClick: true,
        allowEscapeKey: true
    });
});
</script>
@endif

<!-- Enhanced Takeaway Script with Server-based Apriori Recommendations -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Function untuk update cart count di navbar
    function updateCartCountInNavbar(cartCount) {
        const possibleSelectors = [
            '#cart-count-takeaway',
            '#cart-count', 
            '.cart-count',
            '[data-cart-count]'
        ];
        
        let cartCountElement = null;
        
        for (const selector of possibleSelectors) {
            cartCountElement = document.querySelector(selector);
            if (cartCountElement) break;
        }
        
        if (cartCountElement) {
            const currentCount = parseInt(cartCountElement.textContent) || 0;
            
            if (currentCount !== cartCount) {
                cartCountElement.textContent = cartCount;
                
                if (cartCount > 0) {
                    cartCountElement.classList.remove('d-none');
                    cartCountElement.style.display = 'inline-block';
                } else {
                    cartCountElement.classList.add('d-none');
                    cartCountElement.style.display = 'none';
                }
                
                // Enhanced animation
                cartCountElement.classList.add('cart-badge-animate');
                
                const cartIconSelectors = [
                    '.cart-icon',
                    '[data-cart-icon]',
                    'i[class*="cart"]',
                    '.fa-shopping-cart',
                    '.fa-shopping-bag'
                ];
                
                let cartIcon = null;
                for (const iconSelector of cartIconSelectors) {
                    cartIcon = document.querySelector(iconSelector);
                    if (cartIcon) break;
                }
                
                if (cartIcon) {
                    cartIcon.classList.add('cart-icon-bounce', 'cart-icon-highlight');
                }
                
                setTimeout(() => {
                    cartCountElement.classList.remove('cart-badge-animate');
                    if (cartIcon) {
                        cartIcon.classList.remove('cart-icon-bounce', 'cart-icon-highlight');
                    }
                }, 1000);
                
                console.log('Cart count updated to:', cartCount);
            }
        }
    }

    // Function untuk refresh cart count dari server
    function refreshCartCount() {
        fetch('/takeaway/cart-count', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.cart_count !== undefined) {
                updateCartCountInNavbar(data.cart_count);
            }
        })
        .catch(error => {
            console.log('Error refreshing cart count:', error);
        });
    }

    // Function untuk menampilkan popup rekomendasi menggunakan server-based Apriori
    function showServerBasedRecommendationPopup(selectedMenuId, addedMenuName) {
        console.log('Showing server-based Apriori recommendations for menu:', selectedMenuId);
        
        // Show loading dengan SweetAlert2
        Swal.fire({
            title: 'Mencari Rekomendasi Menu Terbaik',
            html: `
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mb-2"><i class="fas fa-brain me-2"></i>Menganalisis pola pembelian pelanggan...</p>
                    <p class="text-muted small">Menggunakan algoritma Apriori untuk rekomendasi cross-category</p>
                </div>
            `,
            allowOutsideClick: false,
            showConfirmButton: false,
            customClass: {
                popup: 'recommendation-loading-popup'
            },
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // AJAX request untuk mendapatkan rekomendasi dari server
        fetch('/takeaway/rekomendasi/get', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                selected_menus: [parseInt(selectedMenuId)],
                current_menu_id: parseInt(selectedMenuId)
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Server Apriori recommendations response:', data);
            
            if (data.status && data.recommendations && data.recommendations.length > 0) {
                displayServerRecommendations(data, addedMenuName);
            } else {
                displayNoRecommendations(addedMenuName);
            }
        })
        .catch(error => {
            console.error('Error fetching Apriori recommendations:', error);
            displayErrorRecommendations(addedMenuName);
        });
    }

    // Function untuk menampilkan rekomendasi dari server
    function displayServerRecommendations(data, addedMenuName) {
        const algorithmUsed = data.algorithm_used || 'unknown';
        
        let algorithmInfo = '';
        let algorithmColor = '#28a745';
        let algorithmIcon = 'fas fa-lightbulb';
        
        switch (algorithmUsed) {
            case 'apriori':
                algorithmInfo = 'Rekomendasi berdasarkan Algoritma Apriori - Analisis pola pembelian pelanggan';
                algorithmColor = '#007bff';
                algorithmIcon = 'fas fa-brain';
                break;
            case 'intelligent_fallback':
                algorithmInfo = 'Rekomendasi cerdas berdasarkan cross-category dan popularitas menu';
                algorithmColor = '#17a2b8';
                algorithmIcon = 'fas fa-star';
                break;
            default:
                algorithmInfo = 'Rekomendasi berdasarkan menu populer cross-category';
                algorithmColor = '#28a745';
                algorithmIcon = 'fas fa-thumbs-up';
        }
        
        let recommendationsHtml = `
            <div class="recommendation-header text-center mb-4">
                <h3 class="mb-2" style="color: ${algorithmColor};">
                    <i class="${algorithmIcon} me-2"></i>
                    Menu Yang Mungkin Anda Suka
                </h3>
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    ${algorithmInfo}
                </p>
                <div class="mt-2 p-2" style="background: rgba(${algorithmColor === '#007bff' ? '0, 123, 255' : algorithmColor === '#17a2b8' ? '23, 162, 184' : '40, 167, 69'}, 0.1); border-radius: 10px;">
                    <small><strong>${addedMenuName}</strong> telah ditambahkan ke keranjang</small>
                </div>
            </div>
            <div class="row justify-content-center">
        `;
        
        data.recommendations.forEach(function(item, index) {
            const folder = getFolderFromCategory(item.kategori);
            const imagePath = `/assets/img/${folder}/${item.gambar || 'default.png'}`;
            const formattedPrice = new Intl.NumberFormat('id-ID').format(item.harga);
            
            // Confidence badge color
            let confidenceClass = 'bg-success';
            let confidenceText = 'Tinggi';
            if (item.confidence < 60) {
                confidenceClass = 'bg-warning';
                confidenceText = 'Sedang';
            } else if (item.confidence < 80) {
                confidenceClass = 'bg-info';
                confidenceText = 'Baik';
            } else {
                confidenceText = 'Sangat Cocok';
            }
            
            recommendationsHtml += `
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="recommendation-card card h-100 shadow-sm" data-id="${item.id}" 
                         style="border-radius: 15px; overflow: hidden; transition: all 0.3s ease;">
                        <div class="position-relative">
                            <img src="${imagePath}" 
                                 class="card-img-top" 
                                 alt="${item.nama_menu}" 
                                 style="height: 150px; object-fit: cover;"
                                 onerror="this.src='/assets/img/default.png';">
                            ${item.confidence ? `
                                <span class="position-absolute top-0 end-0 badge ${confidenceClass} m-2" 
                                      style="font-size: 0.7rem;">
                                    ${Math.round(item.confidence)}% ${confidenceText}
                                </span>
                            ` : ''}
                        </div>
                        <div class="card-body text-center d-flex flex-column">
                            <h6 class="card-title fw-bold mb-2" style="min-height: 40px; display: flex; align-items: center; justify-content: center;">
                                ${item.nama_menu}
                            </h6>
                            <div class="badge bg-secondary text-white mb-2" style="font-size: 0.75rem;">
                                <i class="fas fa-tag me-1"></i>${item.kategori}
                            </div>
                            <p class="card-text fw-bold text-success mb-2" style="font-size: 1.1rem;">
                                Rp. ${formattedPrice}
                            </p>
                            <p class="text-muted small mb-3" style="font-style: italic; min-height: 36px;">
                                <i class="fas fa-magic me-1"></i>${item.rule_text || 'Rekomendasi spesial untuk Anda'}
                            </p>
                            <button class="btn btn-primary recommendation-add-btn mt-auto w-100" 
                                    data-id="${item.id}" 
                                    data-name="${item.nama_menu}" 
                                    data-price="${item.harga}"
                                    style="border-radius: 25px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-cart-plus me-2"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        recommendationsHtml += `</div>`;
        
        // Show recommendations dengan SweetAlert2
        Swal.fire({
            title: false,
            html: recommendationsHtml,
            width: '900px',
            padding: '2rem',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: {
                popup: 'recommendation-popup-custom',
                closeButton: 'recommendation-close-btn'
            },
            didOpen: () => {
                // Bind recommendation button events
                bindRecommendationEvents();
                
                // Add hover effects dengan jQuery jika tersedia
                if (typeof $ !== 'undefined') {
                    $('.recommendation-card').hover(
                        function() {
                            $(this).css('transform', 'translateY(-5px)');
                            $(this).find('.card-img-top').css('transform', 'scale(1.05)');
                        },
                        function() {
                            $(this).css('transform', 'translateY(0)');
                            $(this).find('.card-img-top').css('transform', 'scale(1)');
                        }
                    );
                }
            }
        });
    }

    // Function untuk menampilkan pesan jika tidak ada rekomendasi
    function displayNoRecommendations(addedMenuName) {
        Swal.fire({
            title: 'Menu Berhasil Ditambahkan!',
            html: `
                <div class="text-center p-4">
                    <div class="mb-4 p-3" style="background: linear-gradient(135deg, #28a745, #20c997); border-radius: 15px; color: white;">
                        <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 10px;"></i>
                        <h5 style="color: white; margin: 0;">${addedMenuName}</h5>
                        <small>telah ditambahkan ke keranjang</small>
                    </div>
                    <i class="fas fa-utensils" style="font-size: 48px; color: #bdc3c7; margin-bottom: 20px;"></i>
                    <h4 class="text-muted mb-2">Rekomendasi Sedang Disiapkan</h4>
                    <p class="text-secondary">Sistem rekomendasi masih mempelajari preferensi Anda.</p>
                    <p class="text-muted small">Silakan lanjutkan memilih menu lainnya atau proceed ke checkout.</p>
                </div>
            `,
            icon: 'success',
            confirmButtonText: 'Lanjutkan Berbelanja',
            confirmButtonColor: '#28a745',
            timer: 5000,
            timerProgressBar: true
        });
    }

    // Function untuk menampilkan pesan error
    function displayErrorRecommendations(addedMenuName) {
        Swal.fire({
            title: 'Menu Berhasil Ditambahkan!',
            html: `
                <div class="text-center p-4">
                    <div class="mb-4 p-3" style="background: linear-gradient(135deg, #28a745, #20c997); border-radius: 15px; color: white;">
                        <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 10px;"></i>
                        <h5 style="color: white; margin: 0;">${addedMenuName || 'Item'}</h5>
                        <small>berhasil ditambahkan ke keranjang</small>
                    </div>
                    <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #ffc107; margin-bottom: 20px;"></i>
                    <h4 class="text-warning mb-2">Rekomendasi Tidak Tersedia</h4>
                    <p class="text-secondary">Terjadi masalah saat memuat rekomendasi menu.</p>
                    <p class="text-muted small">Anda masih dapat melanjutkan pemesanan dengan menu lainnya.</p>
                </div>
            `,
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#28a745'
        });
    }

    // Function untuk mendapatkan folder dari kategori
    function getFolderFromCategory(kategori) {
        const folderMap = {
            'Makanan': 'makanan',
            'Minuman': 'minuman',
            'Nasi dan Mie': 'nasi-dan-mie',
            'Aneka Snack': 'aneka-snack'
        };
        return folderMap[kategori] || 'default';
    }

    // Function untuk menambahkan item rekomendasi ke keranjang
    function addRecommendedToCart(menuId, menuName, menuPrice) {
        const jenisPesanan = document.getElementById('session-jenis').value;
        const nomorWA = document.getElementById('session-wa').value;
        
        if (!jenisPesanan || jenisPesanan !== 'takeaway') {
            Swal.fire({
                icon: 'warning',
                title: 'Mode Tidak Sesuai',
                text: 'Silakan aktifkan mode takeaway terlebih dahulu',
                confirmButtonColor: '#f44336'
            });
            return;
        }
        
        // Update button state
        const recBtn = document.querySelector(`.recommendation-add-btn[data-id="${menuId}"]`);
        if (!recBtn) return;
        
        const originalHTML = recBtn.innerHTML;
        recBtn.disabled = true;
        recBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Menambahkan...';
        
        fetch('/takeaway/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                menu_id: parseInt(menuId),
                qty: 1,
                jenis_pesanan: 'takeaway',
                nomor_wa: nomorWA
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count
                if (data.cart_count !== undefined) {
                    updateCartCountInNavbar(data.cart_count);
                }
                
                // Show success feedback on button
                recBtn.innerHTML = '<i class="fas fa-check me-2"></i>Ditambahkan!';
                recBtn.classList.remove('btn-primary');
                recBtn.classList.add('btn-success');
                
                // Show toast notification
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                
                Toast.fire({
                    icon: 'success',
                    title: `${menuName} berhasil ditambahkan!`
                });
                
                // Close recommendation popup after 2 seconds
                setTimeout(() => {
                    Swal.close();
                }, 2000);
                
            } else {
                recBtn.disabled = false;
                recBtn.innerHTML = originalHTML;
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Gagal Menambahkan',
                    text: data.message || 'Menu tidak dapat ditambahkan ke keranjang',
                    confirmButtonColor: '#f44336'
                });
            }
        })
        .catch(error => {
            console.error('Error adding recommended item:', error);
            recBtn.disabled = false;
            recBtn.innerHTML = originalHTML;
            
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Gagal menambahkan menu ke keranjang',
                confirmButtonColor: '#f44336'
            });
        });
    }

    // Function untuk bind events recommendation buttons
    function bindRecommendationEvents() {
        document.querySelectorAll('.recommendation-add-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const menuId = this.getAttribute('data-id');
                const menuName = this.getAttribute('data-name');
                const menuPrice = this.getAttribute('data-price');
                addRecommendedToCart(menuId, menuName, menuPrice);
            });
        });
        
        // jQuery fallback jika tersedia
        if (typeof $ !== 'undefined') {
            $('.recommendation-add-btn').off('click').on('click', function(e) {
                e.preventDefault();
                const menuId = $(this).data('id');
                const menuName = $(this).data('name');
                const menuPrice = $(this).data('price');
                addRecommendedToCart(menuId, menuName, menuPrice);
            });
        }
    }

    // Enhanced add to cart dengan popup rekomendasi Apriori untuk takeaway
    document.querySelectorAll('.add-to-cart-btn').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const menuId = this.getAttribute('data-id');
            const menuName = this.getAttribute('data-name');
            const menuPrice = parseFloat(this.getAttribute('data-price'));
            const menuKategori = this.getAttribute('data-kategori');
            
            // Validasi session data untuk takeaway
            const jenisPesanan = document.getElementById('session-jenis').value;
            const nomorWA = document.getElementById('session-wa').value;
            
            if (!jenisPesanan || jenisPesanan !== 'takeaway') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Mode Tidak Sesuai',
                    text: 'Silakan aktifkan mode takeaway terlebih dahulu',
                    confirmButtonColor: '#f44336'
                });
                return;
            }
            
            // Disable button dan show loading
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Menambahkan...';
            
            // AJAX request ke endpoint takeaway
            fetch('/takeaway/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    menu_id: parseInt(menuId),
                    qty: 1,
                    jenis_pesanan: 'takeaway',
                    nomor_wa: nomorWA
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Add to cart response:', data);
                
                if (data.success) {
                    // Update cart count di navbar langsung dari response
                    if (data.cart_count !== undefined) {
                        updateCartCountInNavbar(data.cart_count);
                    }
                    
                    // Show success toast
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                    
                    Toast.fire({
                        icon: 'success',
                        title: `${menuName} ditambahkan ke keranjang!`
                    }).then(() => {
                        // Show server-based Apriori recommendation popup
                        setTimeout(() => {
                            showServerBasedRecommendationPopup(menuId, menuName);
                        }, 300);
                    });
                    
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Gagal menambahkan item ke keranjang',
                        confirmButtonColor: '#f44336'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem',
                    confirmButtonColor: '#f44336'
                });
            })
            .finally(() => {
                // Re-enable button
                this.disabled = false;
                this.innerHTML = originalText;
            });
        });
    });
    
    // Auto-refresh cart count setiap 30 detik untuk memastikan sinkronisasi
    setInterval(() => {
        refreshCartCount();
    }, 30000);
    
    // Refresh cart count saat page load
    refreshCartCount();
    
    // Global functions untuk compatibility
    window.showServerBasedRecommendationPopup = showServerBasedRecommendationPopup;
    window.addRecommendedToCart = addRecommendedToCart;
    window.updateCartCountInNavbar = updateCartCountInNavbar;
    window.refreshCartCount = refreshCartCount;
});

// Backward compatibility functions untuk addtocart.js
window.addToCartTakeaway = function(menuId, menuName, menuPrice) {
    // Trigger click event pada tombol yang sesuai
    const button = document.querySelector(`[data-id="${menuId}"]`);
    if (button) {
        button.click();
    }
};

// Fallback function untuk update cart count (compatibility dengan kode lama)
if (typeof window.updateCartCount === 'undefined') {
    window.updateCartCount = function(count) {
        if (typeof window.updateCartCountInNavbar === 'function') {
            window.updateCartCountInNavbar(count);
        }
    };
}
</script>

@endsection

@section('scripts')
<!-- Load jQuery before addtocart.js -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Modified addtocart.js untuk compatibility -->
<script src="{{ asset('assets/js/addtocart.js') }}"></script>
@endsection