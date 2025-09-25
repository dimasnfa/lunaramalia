@extends('cart.dinein.master')

@section('title', 'Pesanan Berhasil')

@section('content')
    <div class="text-center mt-5">
        <h1 class="text-success"><i class="fa fa-check-circle"></i> Pesanan Anda Berhasil!</h1>
        <p class="lead">Terima kasih telah memesan. Silakan tunggu pesanan Anda di meja.</p>
        
        <div class="mt-4">
            <a href="{{ route('home') }}" class="btn btn-primary me-3">
                <i class="fa fa-home"></i> Kembali ke Beranda
            </a>
            
            <!-- Button baru untuk melihat pesanan -->
            <a href="{{ route('customer.orders') }}" class="btn btn-info">
                <i class="fa fa-list"></i> Lihat Pesanan Saya
            </a>
        </div>
        
        <!-- Info tambahan -->
        <div class="mt-4 text-muted">
            <small>
                <i class="fa fa-info-circle"></i> 
                Anda dapat melihat status pesanan dan melakukan pembatalan jika diperlukan melalui menu "Lihat Pesanan Saya"
            </small>
        </div>
    </div>
@endsection