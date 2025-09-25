@extends('admin.main')

@section('header')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
    <div>
        <h1 class="mb-0">Edit Pesanan</h1>
        <ol class="breadcrumb mt-1 mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.pesanan.index') }}">Pesanan</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<style>
/* Custom styles matching the index page */
.edit-section {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 30px;
}

.section-header {
    padding: 15px 20px;
    font-weight: bold;
    color: white;
    display: flex;
    align-items: center;
    gap: 10px;
}

.dinein-header {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    color: #000;
}

.takeaway-header {
    background: linear-gradient(135deg, #17a2b8, #138496);
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.order-details {
    background: #f8f9fa;
    border-left: 4px solid #007bff;
    padding: 15px;
    margin: 15px 0;
}

.detail-item {
    margin-bottom: 8px;
}

.detail-item strong {
    color: #495057;
    min-width: 120px;
    display: inline-block;
}

.menu-item {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 8px;
    display: flex;
    justify-content-between;
    align-items: center;
}

.menu-item:last-child {
    margin-bottom: 0;
}

.quantity-badge {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
}

.total-amount {
    background: linear-gradient(135deg, #28a745, #1e7e34);
    color: white;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    margin: 20px 0;
}

.btn-action {
    padding: 10px 25px;
    font-weight: 600;
    border-radius: 8px;
    margin: 5px;
    transition: all 0.3s ease;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.status-indicator {
    font-size: 14px;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: 600;
    margin-bottom: 10px;
    display: inline-block;
}

@media (max-width: 768px) {
    .detail-item strong {
        min-width: 100px;
        font-size: 14px;
    }
    
    .menu-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
}
</style>

<div class="container-fluid">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Order Info Header --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="mb-1">Pesanan ID: #{{ $pesanan->id }}</h4>
                    <small class="text-muted">
                        <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($pesanan->created_at)->format('d-m-Y H:i') }}
                        | <i class="fas fa-tag"></i> {{ ucfirst($pesanan->jenis_pesanan) }}
                    </small>
                </div>
                
                {{-- Current Status Indicator --}}
                <div>
                    @php
                        $statusClass = 'badge-secondary';
                        $statusIcon = '🔄';
                        
                        switch($pesanan->status_pesanan) {
                            case 'pending':
                                $statusClass = 'badge-warning';
                                $statusIcon = '⏳';
                                break;
                            case 'dibayar':
                                $statusClass = 'badge-primary';
                                $statusIcon = '💰';
                                break;
                            // case 'selesai':
                            //     $statusClass = 'badge-success';
                            //     $statusIcon = '✅';
                            //     break;
                            case 'dibatalkan':
                                $statusClass = 'badge-danger';
                                $statusIcon = '❌';
                                break;
                        }
                    @endphp
                    
                    <span class="status-indicator badge {{ $statusClass }}">
                        {{ $statusIcon }} Status: {{ ucfirst($pesanan->status_pesanan) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Edit Form Section --}}
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="edit-section">
                <div class="section-header {{ strtolower(str_replace('-', '', $pesanan->jenis_pesanan)) === 'dinein' ? 'dinein-header' : 'takeaway-header' }}">
                    @if(strtolower(str_replace('-', '', $pesanan->jenis_pesanan)) === 'dinein')
                        🍽️ Edit Pesanan Dine-In
                    @else
                        🥡 Edit Pesanan Takeaway
                    @endif
                </div>
                
                <div class="p-4">
                    <form action="{{ route('admin.pesanan.update', $pesanan->id) }}" method="POST" id="editPesananForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="status_pesanan" class="form-label">
                                <i class="fas fa-flag"></i> Status Pesanan
                            </label>
                            <select name="status_pesanan" id="status_pesanan" class="form-control form-control-lg" required>
                                <option value="pending" {{ $pesanan->status_pesanan == 'pending' ? 'selected' : '' }}>
                                    ⏳ Pending
                                </option>
                                <option value="dibayar" {{ $pesanan->status_pesanan == 'dibayar' ? 'selected' : '' }}>
                                    💰 Dibayar
                                </option>
                                {{-- <option value="selesai" {{ $pesanan->status_pesanan == 'selesai' ? 'selected' : '' }}>
                                    ✅ Selesai
                                </option> --}}
                                <option value="dibatalkan" {{ $pesanan->status_pesanan == 'dibatalkan' ? 'selected' : '' }}>
                                    ❌ Dibatalkan
                                </option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Pilih status yang sesuai dengan kondisi pesanan saat ini
                            </small>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex flex-wrap gap-2 justify-content-between">
                            <button type="submit" class="btn btn-success btn-action" id="saveBtn">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.pesanan.index') }}" class="btn btn-secondary btn-action">
                                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Order Details Section --}}
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="edit-section">
                <div class="section-header {{ strtolower(str_replace('-', '', $pesanan->jenis_pesanan)) === 'dinein' ? 'dinein-header' : 'takeaway-header' }}">
                    📋 Detail Pesanan
                </div>
                
                <div class="p-4">
                    {{-- Customer/Table Information --}}
                    <div class="order-details">
                        @if($pesanan->meja)
                            {{-- Dine-in Information --}}
                            <div class="detail-item">
                                <strong><i class="fas fa-chair"></i> Nomor Meja:</strong>
                                <span>{{ $pesanan->meja->nomor_meja }}</span>
                            </div>
                            <div class="detail-item">
                                <strong><i class="fas fa-couch"></i> Tipe Meja:</strong>
                                <span>{{ $pesanan->meja->tipe_meja ?? 'Regular' }}</span>
                            </div>
                            <div class="detail-item">
                                <strong><i class="fas fa-building"></i> Lantai:</strong>
                                <span>Lantai {{ $pesanan->meja->lantai ?? '1' }}</span>
                            </div>
                        @else
                            {{-- Takeaway Information --}}
                            <div class="detail-item">
                                <strong><i class="fas fa-user"></i> Nama Pelanggan:</strong>
                                <span>{{ $pesanan->nama_pelanggan ?? '-' }}</span>
                            </div>
                            <div class="detail-item">
                                <strong><i class="fab fa-whatsapp"></i> Nomor WA:</strong>
                                <span>{{ $pesanan->nomor_wa ?? '-' }}</span>
                            </div>
                        @endif
                        
                        <div class="detail-item">
                            <strong><i class="fas fa-credit-card"></i> Metode Pembayaran:</strong>
                            @if($pesanan->metode_pembayaran === 'qris')
                                <span class="badge badge-info">💳 QRIS</span>
                            @elseif($pesanan->metode_pembayaran === 'cash')
                                <span class="badge badge-success">💵 Cash</span>
                            @else
                                <span class="badge badge-secondary">-</span>
                            @endif
                        </div>
                    </div>

                    {{-- Menu Items --}}
                    <h5 class="mt-4 mb-3">
                        <i class="fas fa-utensils"></i> Item Pesanan
                    </h5>
                    
                    <div class="menu-items">
                        @forelse ($pesanan->detailPesanan as $item)
                            @if ($item->menu)
                                <div class="menu-item">
                                    <div>
                                        <strong>{{ $item->menu->nama_menu }}</strong>
                                        <div class="text-muted small">
                                            @if($item->menu->harga)
                                                Rp {{ number_format($item->menu->harga, 0, ',', '.') }} x {{ $item->jumlah }}
                                            @else
                                                Rp {{ number_format($item->harga, 0, ',', '.') }} x {{ $item->jumlah }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="quantity-badge">
                                            {{ $item->jumlah }}x
                                        </div>
                                        <div class="small mt-1">
                                            @if($item->menu->harga)
                                                <strong>Rp {{ number_format($item->menu->harga * $item->jumlah, 0, ',', '.') }}</strong>
                                            @else
                                                <strong>Rp {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}</strong>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                Tidak ada item dalam pesanan ini
                            </div>
                        @endforelse
                    </div>

                    {{-- Total Amount --}}
                    <div class="total-amount">
                        <i class="fas fa-calculator"></i> Total Harga: 
                        <span>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Loading Overlay --}}
<div id="loadingOverlay" class="d-none" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;">
    <div class="text-center text-white">
        <div class="spinner-border" role="status" style="width: 3rem; height: 3rem;">
            <span class="sr-only">Loading...</span>
        </div>
        <div class="mt-2">Menyimpan perubahan...</div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Edit Pesanan page initialized');
    
    const form = document.getElementById('editPesananForm');
    const saveBtn = document.getElementById('saveBtn');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const statusSelect = document.getElementById('status_pesanan');
    
    // Enhanced form validation
    if (form && saveBtn) {
        // Status change confirmation for critical statuses
        statusSelect.addEventListener('change', function() {
            const newStatus = this.value;
            const currentStatus = '{{ $pesanan->status_pesanan }}';
            
            if (currentStatus !== newStatus) {
                let confirmMessage = '';
                let confirmIcon = '';
                
                switch(newStatus) {
                    case 'dibayar':
                        confirmMessage = 'Tandai pesanan sebagai DIBAYAR?';
                        confirmIcon = '💰';
                        break;
                    case 'selesai':
                        confirmMessage = 'Tandai pesanan sebagai SELESAI?';
                        confirmIcon = '✅';
                        break;
                    case 'dibatalkan':
                        confirmMessage = 'Batalkan pesanan ini?\n\nPerhatian: Tindakan ini akan membatalkan pesanan!';
                        confirmIcon = '❌';
                        break;
                }
                
                if (confirmMessage) {
                    // Update save button to show what will happen
                    saveBtn.innerHTML = `${confirmIcon} <i class="fas fa-save"></i> Simpan: ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}`;
                    saveBtn.className = `btn btn-action ${newStatus === 'dibatalkan' ? 'btn-danger' : (newStatus === 'selesai' ? 'btn-success' : 'btn-primary')}`;
                } else {
                    // Reset button to default
                    saveBtn.innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan';
                    saveBtn.className = 'btn btn-success btn-action';
                }
            }
        });
        
        form.addEventListener('submit', function(e) {
            const newStatus = statusSelect.value;
            const currentStatus = '{{ $pesanan->status_pesanan }}';
            
            // Show confirmation for critical status changes
            if (newStatus === 'dibatalkan' && currentStatus !== 'dibatalkan') {
                if (!confirm('❌ Yakin ingin MEMBATALKAN pesanan ini?\n\nTindakan ini tidak dapat dibatalkan!')) {
                    e.preventDefault();
                    return false;
                }
            } else if (newStatus === 'selesai' && currentStatus !== 'selesai') {
                if (!confirm('✅ Yakin pesanan ini sudah SELESAI?\n\nPelanggan sudah menerima semua item pesanan?')) {
                    e.preventDefault();
                    return false;
                }
            }
            
            // Show loading overlay
            if (loadingOverlay) {
                loadingOverlay.classList.remove('d-none');
                loadingOverlay.style.display = 'flex';
            }
            
            // Disable form elements to prevent double submission
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            statusSelect.disabled = true;
            
            console.log('📝 Form submitted with status:', newStatus);
        });
    }
    
    // Auto-focus on status select
    if (statusSelect) {
        setTimeout(() => {
            statusSelect.focus();
        }, 500);
    }
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + S to save
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            if (form) {
                form.requestSubmit();
            }
        }
        
        // Escape to go back
        if (e.key === 'Escape') {
            window.location.href = '{{ route("admin.pesanan.index") }}';
        }
    });
    
    // Show keyboard shortcuts hint
    console.log('⌨️ Keyboard shortcuts:');
    console.log('   Ctrl/Cmd + S = Save');
    console.log('   Escape = Go back');
    
    console.log('✅ Edit form enhanced and ready');
});

// Prevent form resubmission on page refresh
if (window.performance.navigation.type === window.performance.navigation.TYPE_RELOAD) {
    console.log('🔄 Page refreshed - clearing any form data');
}
</script>
@endsection