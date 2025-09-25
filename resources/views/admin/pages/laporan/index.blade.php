@extends('admin.main')

@section('content')
<style>
    table thead th {
        background: linear-gradient(to right, #007bff, #0056b3);
        color: white;
        text-align: center;
    }

    table td, table th {
        vertical-align: middle !important;
        padding: 10px 14px !important;
        font-size: 14px;
    }

    .filter-container {
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .filter-container label {
        font-weight: 600;
        font-size: 16px;
        color: #333;
        margin-bottom: 0;
    }

    .filter-container select,
    .filter-container input {
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #ced4da;
        font-size: 14px;
    }

    .filter-select {
        min-width: 150px;
        background-color: white;
        cursor: pointer;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .btn-export {
        margin-left: 10px;
    }

    .filter-row {
        display: flex;
        align-items: center;
        gap: 15px;
        width: 100%;
        flex-wrap: wrap;
    }

    /* Custom dropdown styling */
    .custom-select {
        appearance: none;
        background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"><path fill="%23666" d="M2 0L0 2h4zm0 5L0 3h4z"/></svg>');
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 12px;
        padding-right: 35px;
    }

    .btn-refresh {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
        border-radius: 6px;
        padding: 8px 15px;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-refresh:hover {
        background-color: #218838;
        border-color: #1e7e34;
        color: white;
    }

    .btn-semua-data {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
        border-radius: 6px;
        padding: 8px 15px;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-semua-data:hover {
        background-color: #138496;
        border-color: #117a8b;
        color: white;
        text-decoration: none;
    }

    .filter-buttons {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-left: auto;
    }

    /* Date range display styling */
    .date-range-display {
        display: flex;
        align-items: center;
        gap: 10px;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 8px 12px;
        min-width: 250px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .date-range-display:hover {
        background-color: #e9ecef;
        border-color: #007bff;
    }

    .date-separator {
        color: #6c757d;
        font-weight: 500;
    }

    /* Modal Styling - Improved */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1050;
        display: none;
        justify-content: center;
        align-items: center;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal-dialog {
        background: white;
        border-radius: 8px;
        max-width: 400px;
        width: 90%;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        position: relative;
        animation: modalFadeIn 0.3s ease;
        pointer-events: auto;
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #dee2e6;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #6c757d;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.3s ease;
    }

    .modal-close:hover {
        color: #333;
    }

    .modal-body {
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        display: block;
        font-weight: 500;
        margin-bottom: 5px;
        color: #333;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        font-size: 14px;
        box-sizing: border-box;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        background-color: white;
    }

    .form-control:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }

    input[type="date"].form-control,
    input[type="month"].form-control {
        cursor: pointer;
        position: relative;
    }

    input[type="date"].form-control::-webkit-calendar-picker-indicator,
    input[type="month"].form-control::-webkit-calendar-picker-indicator {
        cursor: pointer;
        filter: brightness(0) saturate(100%) invert(13%) sepia(94%) saturate(7151%) hue-rotate(244deg) brightness(90%) contrast(143%);
    }

    input[type="number"].form-control {
        -moz-appearance: textfield;
    }

    input[type="number"].form-control::-webkit-outer-spin-button,
    input[type="number"].form-control::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn-modal {
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    /* Active filter display */
    .active-filter {
        background-color: #e3f2fd;
        border: 1px solid #2196f3;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 14px;
        color: #1976d2;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-info {
        font-weight: 500;
    }

    .btn-change-filter {
        background: none;
        border: none;
        color: #1976d2;
        cursor: pointer;
        text-decoration: underline;
        font-size: 12px;
    }

    .btn-clear-filter {
        background: none;
        border: none;
        color: #dc3545;
        cursor: pointer;
        padding: 0;
        margin-left: 5px;
        font-size: 16px;
        line-height: 1;
    }

    .btn-clear-filter:hover {
        color: #c82333;
    }

    /* Dynamic button styling */
    .filter-status-active {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }

    .filter-status-filtered {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
    }

    .btn-refresh.reset-mode {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .btn-refresh.reset-mode:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }

    .btn-refresh.refresh-mode {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-refresh.refresh-mode:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
</style>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">📊 Laporan Transaksi</h5>
    </div>

    <div class="card-body">
        {{-- Filter Form --}}
        <form method="GET" action="{{ route('admin.laporan.index') }}" id="filterForm">
            <div class="filter-row">
                <label>Filter:</label>
                
                {{-- Show current filter or dropdown --}}
                @if($filterType === 'range_tanggal' && ($tanggalAwal || $tanggalAkhir))
                    <div class="active-filter">
                        <span class="filter-info">
                            Range Tanggal: {{ \Carbon\Carbon::parse($tanggalAwal)->format('d/m/Y') }} 
                            @if($tanggalAkhir && $tanggalAkhir !== $tanggalAwal)
                                - {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') }}
                            @endif
                        </span>
                        <button type="button" class="btn-change-filter" onclick="openFilterModal('range_tanggal')">
                            Ubah
                        </button>
                        <button type="button" class="btn-clear-filter" onclick="clearFilter()" title="Hapus filter">
                            ×
                        </button>
                    </div>
                @elseif($filterType === 'harian' && $tanggal)
                    <div class="active-filter">
                        <span class="filter-info">
                            Harian: {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}
                        </span>
                        <button type="button" class="btn-change-filter" onclick="openFilterModal('harian')">
                            Ubah
                        </button>
                        <button type="button" class="btn-clear-filter" onclick="clearFilter()" title="Hapus filter">
                            ×
                        </button>
                    </div>
                @elseif($filterType === 'bulanan' && $bulan)
                    <div class="active-filter">
                        <span class="filter-info">
                            Bulanan: {{ \Carbon\Carbon::parse($bulan.'-01')->format('F Y') }}
                        </span>
                        <button type="button" class="btn-change-filter" onclick="openFilterModal('bulanan')">
                            Ubah
                        </button>
                        <button type="button" class="btn-clear-filter" onclick="clearFilter()" title="Hapus filter">
                            ×
                        </button>
                    </div>
                @elseif($filterType === 'tahunan' && $tahun)
                    <div class="active-filter">
                        <span class="filter-info">Tahunan: {{ $tahun }}</span>
                        <button type="button" class="btn-change-filter" onclick="openFilterModal('tahunan')">
                            Ubah
                        </button>
                        <button type="button" class="btn-clear-filter" onclick="clearFilter()" title="Hapus filter">
                            ×
                        </button>
                    </div>
                @else
                    <select name="filter_type" class="filter-select custom-select" id="filterType" onchange="handleFilterChange()">
                        <option value="semua_data" {{ $filterType === 'semua_data' || !$filterType ? 'selected' : '' }}>Semua Data</option>
                        <option value="range_tanggal" {{ $filterType === 'range_tanggal' ? 'selected' : '' }}>Periode</option>
                        <option value="harian" {{ $filterType === 'harian' ? 'selected' : '' }}>Harian</option>
                        <option value="bulanan" {{ $filterType === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                        <option value="tahunan" {{ $filterType === 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                    </select>
                @endif

                {{-- Hidden inputs for different filters --}}
                <input type="hidden" name="tanggal_awal" id="tanggalAwal" value="{{ $tanggalAwal }}">
                <input type="hidden" name="tanggal_akhir" id="tanggalAkhir" value="{{ $tanggalAkhir }}">
                <input type="hidden" name="harian" id="harianHidden" value="{{ $tanggal }}">
                <input type="hidden" name="bulanan" id="bulananHidden" value="{{ $bulan }}">
                <input type="hidden" name="tahunan" id="tahunanHidden" value="{{ $tahun }}">

                {{-- Filter Buttons --}}
                <div class="filter-buttons">
                    {{-- Dynamic button text based on current filter --}}
                    @if($filterType && $filterType !== 'semua_data' && ($tanggalAwal || $tanggal || $bulan || $tahun))
                        <span class="btn-semua-data" style="background-color: #6c757d; border-color: #6c757d; cursor: default;">
                            <i class="fas fa-filter"></i> 
                            @if($filterType === 'range_tanggal')
                                Range: {{ \Carbon\Carbon::parse($tanggalAwal)->format('d/m/Y') }}
                                @if($tanggalAkhir && $tanggalAkhir !== $tanggalAwal)
                                    - {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') }}
                                @endif
                            @elseif($filterType === 'harian')
                                Harian: {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}
                            @elseif($filterType === 'bulanan')
                                Bulanan: {{ \Carbon\Carbon::parse($bulan.'-01')->format('M Y') }}
                            @elseif($filterType === 'tahunan')
                                Tahunan: {{ $tahun }}
                            @endif
                        </span>
                        <button type="button" class="btn-refresh" onclick="resetToAllData()" title="Kembali ke Semua Data">
                            <i class="fas fa-times-circle"></i> Reset Filter
                        </button>
                    @else
                        <span class="btn-semua-data" style="background-color: #28a745; border-color: #28a745; cursor: default;">
                            <i class="fas fa-check-circle"></i> Semua Data Aktif
                        </span>
                        <button type="button" class="btn-refresh" onclick="refreshCurrentPage()" title="Refresh halaman">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    @endif
                </div>
            </div>
        </form>

        {{-- Table --}}
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-hover table-striped text-center">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Transaksi</th>
                        <th>Jenis Pesanan</th>
                        <th>Menu</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporans as $laporan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($laporan->pesanan->tanggal_pesanan)->format('d-m-Y') }}</td>
                            <td class="text-capitalize">
                                {{ $laporan->pesanan->jenis_pesanan ?? ($laporan->pesanan->meja_id ? 'dine-in' : 'takeaway') }}
                            </td>
                            <td>{{ $laporan->menu->nama_menu }}</td>
                            <td>{{ $laporan->menu->kategori->nama_kategori ?? '-' }}</td>
                            <td>{{ $laporan->jumlah }}</td>
                            <td>Rp {{ number_format($laporan->menu->harga ?? 0, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($laporan->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                @if(!$filterType || $filterType === 'semua_data')
                                    Tidak ada data laporan
                                @else
                                    Tidak ada data laporan untuk filter yang dipilih
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Total Pendapatan --}}
        @if($laporans->count())
            <div class="mt-3 text-end">
                <h5>
                    Total Pendapatan: 
                    <span class="badge bg-success">
                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                    </span>
                </h5>
            </div>
        @endif

        {{-- Export Buttons --}}
        <div class="d-flex justify-content-end mt-3">
            <a href="{{ route('admin.laporan.export-pdf', request()->query()) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Cetak PDF
            </a>
            <a href="{{ route('admin.laporan.export-excel', request()->query()) }}" class="btn btn-success fw-bold shadow-sm btn-export">
                <i class="fas fa-file-excel"></i> Download Excel
            </a>
        </div>
    </div>
</div>

{{-- Modal for Range Tanggal --}}
<div id="rangeTanggalModal" class="modal-overlay" onclick="closeModal('rangeTanggalModal')">
    <div class="modal-dialog" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h5 class="modal-title">Pilih Range Tanggal</h5>
            <button type="button" class="modal-close" onclick="closeModal('rangeTanggalModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Dari:</label>
                <input type="date" id="modalTanggalAwal" value="{{ $tanggalAwal }}" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Sampai:</label>
                <input type="date" id="modalTanggalAkhir" value="{{ $tanggalAkhir }}" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal btn-secondary" onclick="closeModal('rangeTanggalModal')">Batal</button>
            <button type="button" class="btn-modal btn-primary" onclick="applyDateRangeFilter()">Terapkan</button>
        </div>
    </div>
</div>

{{-- Modal for Harian --}}
<div id="harianModal" class="modal-overlay" onclick="closeModal('harianModal')">
    <div class="modal-dialog" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h5 class="modal-title">Pilih Tanggal</h5>
            <button type="button" class="modal-close" onclick="closeModal('harianModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Tanggal:</label>
                <input type="date" id="harianDate" value="{{ $tanggal }}" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal btn-secondary" onclick="closeModal('harianModal')">Batal</button>
            <button type="button" class="btn-modal btn-primary" onclick="applyHarianFilter()">Terapkan</button>
        </div>
    </div>
</div>

{{-- Modal for Bulanan --}}
<div id="bulananModal" class="modal-overlay" onclick="closeModal('bulananModal')">
    <div class="modal-dialog" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h5 class="modal-title">Pilih Bulan</h5>
            <button type="button" class="modal-close" onclick="closeModal('bulananModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Bulan:</label>
                <input type="month" id="bulananDate" value="{{ $bulan }}" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal btn-secondary" onclick="closeModal('bulananModal')">Batal</button>
            <button type="button" class="btn-modal btn-primary" onclick="applyBulananFilter()">Terapkan</button>
        </div>
    </div>
</div>

{{-- Modal for Tahunan --}}
<div id="tahunanModal" class="modal-overlay" onclick="closeModal('tahunanModal')">
    <div class="modal-dialog" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h5 class="modal-title">Pilih Tahun</h5>
            <button type="button" class="modal-close" onclick="closeModal('tahunanModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Tahun:</label>
                <input type="number" id="tahunanDate" value="{{ $tahun }}" min="2000" max="{{ date('Y') }}" class="form-control" placeholder="Masukkan tahun">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal btn-secondary" onclick="closeModal('tahunanModal')">Batal</button>
            <button type="button" class="btn-modal btn-primary" onclick="applyTahunanFilter()">Terapkan</button>
        </div>
    </div>
</div>

<script>
function handleFilterChange() {
    const filterType = document.getElementById('filterType').value;
    
    if (filterType === 'semua_data') {
        // Redirect to show all data
        window.location.href = "{{ route('admin.laporan.index') }}";
    } else if (filterType === 'range_tanggal') {
        openFilterModal('range_tanggal');
    } else if (filterType === 'harian') {
        openFilterModal('harian');
    } else if (filterType === 'bulanan') {
        openFilterModal('bulanan');
    } else if (filterType === 'tahunan') {
        openFilterModal('tahunan');
    }
}

function openFilterModal(type) {
    if (type === 'range_tanggal') {
        document.getElementById('rangeTanggalModal').classList.add('show');
    } else if (type === 'harian') {
        document.getElementById('harianModal').classList.add('show');
    } else if (type === 'bulanan') {
        document.getElementById('bulananModal').classList.add('show');
    } else if (type === 'tahunan') {
        document.getElementById('tahunanModal').classList.add('show');
    }
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
    document.body.style.overflow = 'auto';
    
    // Reset dropdown to current active filter or "Semua Data"
    const currentFilter = "{{ $filterType }}";
    const filterTypeSelect = document.getElementById('filterType');
    
    if (filterTypeSelect) {
        if (currentFilter && currentFilter !== 'semua_data' && 
            (("{{ $tanggalAwal }}" || "{{ $tanggal }}" || "{{ $bulan }}" || "{{ $tahun }}"))) {
            // Keep current selection if there's an active filter with data
            filterTypeSelect.value = currentFilter;
        } else {
            filterTypeSelect.value = 'semua_data';
        }
    }
}

function applyDateRangeFilter() {
    const tanggalAwal = document.getElementById('modalTanggalAwal').value;
    const tanggalAkhir = document.getElementById('modalTanggalAkhir').value;
    
    if (!tanggalAwal) {
        alert('Silakan pilih tanggal awal terlebih dahulu');
        return;
    }
    
    // Build URL with parameters
    const params = new URLSearchParams();
    params.append('filter_type', 'range_tanggal');
    params.append('tanggal_awal', tanggalAwal);
    if (tanggalAkhir) {
        params.append('tanggal_akhir', tanggalAkhir);
    }
    
    window.location.href = "{{ route('admin.laporan.index') }}?" + params.toString();
}

function applyHarianFilter() {
    const tanggal = document.getElementById('harianDate').value;
    
    if (!tanggal) {
        alert('Silakan pilih tanggal terlebih dahulu');
        return;
    }
    
    window.location.href = "{{ route('admin.laporan.index') }}?filter_type=harian&harian=" + tanggal;
}

function applyBulananFilter() {
    const bulan = document.getElementById('bulananDate').value;
    
    if (!bulan) {
        alert('Silakan pilih bulan terlebih dahulu');
        return;
    }
    
    window.location.href = "{{ route('admin.laporan.index') }}?filter_type=bulanan&bulanan=" + bulan;
}

function applyTahunanFilter() {
    const tahun = document.getElementById('tahunanDate').value;
    
    if (!tahun) {
        alert('Silakan masukkan tahun terlebih dahulu');
        return;
    }
    
    if (tahun < 2000 || tahun > {{ date('Y') }}) {
        alert('Tahun harus antara 2000 dan {{ date('Y') }}');
        return;
    }
    
    window.location.href = "{{ route('admin.laporan.index') }}?filter_type=tahunan&tahunan=" + tahun;
}

function clearFilter() {
    window.location.href = "{{ route('admin.laporan.index') }}";
}

function refreshCurrentPage() {
    // Refresh halaman saat ini
    window.location.reload();
}

function resetToAllData() {
    // Kembali ke halaman semua data (menghapus semua filter)
    window.location.href = "{{ route('admin.laporan.index') }}";
}

// Fungsi untuk menentukan status navigasi
function updateNavigationStatus() {
    const filterType = "{{ $filterType }}";
    const hasFilterData = "{{ $tanggalAwal || $tanggal || $bulan || $tahun }}";
    
    // Update button states based on current filter
    if (filterType && filterType !== 'semua_data' && hasFilterData) {
        // Ada filter aktif
        console.log('Filter aktif:', filterType);
    } else {
        // Semua data aktif
        console.log('Menampilkan semua data');
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const openModal = document.querySelector('.modal-overlay.show');
        if (openModal) {
            closeModal(openModal.id);
        }
    }
});

// Prevent date inputs from closing modal
document.addEventListener('DOMContentLoaded', function() {
    const dateInputs = document.querySelectorAll('input[type="date"], input[type="month"], input[type="number"]');
    dateInputs.forEach(input => {
        input.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        input.addEventListener('focus', function(e) {
            e.stopPropagation();
        });
        input.addEventListener('change', function(e) {
            e.stopPropagation();
        });
    });
});

// Initialize display on page load
document.addEventListener('DOMContentLoaded', function() {
    const filterType = "{{ $filterType }}";
    const filterSelect = document.getElementById('filterType');
    
    // Set dropdown value
    if (filterSelect && (filterType === 'semua_data' || !filterType)) {
        filterSelect.value = 'semua_data';
    }
    
    // Update navigation status
    updateNavigationStatus();
    
    // Add dynamic classes to buttons if needed
    const hasActiveFilter = filterType && filterType !== 'semua_data' && 
                          ("{{ $tanggalAwal }}" || "{{ $tanggal }}" || "{{ $bulan }}" || "{{ $tahun }}");
    
    const refreshBtn = document.querySelector('.btn-refresh');
    const statusBtn = document.querySelector('.btn-semua-data, .filter-status-active, .filter-status-filtered');
    
    if (hasActiveFilter) {
        // Ada filter aktif
        if (refreshBtn) {
            refreshBtn.classList.add('reset-mode');
            refreshBtn.classList.remove('refresh-mode');
        }
    } else {
        // Semua data aktif
        if (refreshBtn) {
            refreshBtn.classList.add('refresh-mode');
            refreshBtn.classList.remove('reset-mode');
        }
    }
});
</script>
@endsection