@extends('layouts.admin')

@section('title', 'Detail Transaksi #SS-' . $transaction->id)
@section('header-title', 'Detail Transaksi #SS-' . $transaction->id)

@section('styles')
<!-- Leaflet.js Map Library -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .details-layout {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 24px;
    }

    @media (max-width: 992px) {
        .details-layout {
            grid-template-columns: 1fr;
        }
    }

    /* Badges */
    .badge {
        display: inline-flex;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-pending {
        background-color: rgba(245, 158, 11, 0.15);
        color: var(--warning);
        border: 1px solid var(--warning);
    }

    .badge-approved {
        background-color: rgba(16, 185, 129, 0.15);
        color: var(--success);
        border: 1px solid var(--success);
    }

    .badge-rejected {
        background-color: rgba(239, 68, 68, 0.15);
        color: var(--danger);
        border: 1px solid var(--danger);
    }

    /* Info Group */
    .info-section-title {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 16px;
        color: var(--text-primary);
        border-left: 4px solid var(--accent);
        padding-left: 10px;
    }

    .info-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 24px;
    }

    .info-item {
        display: flex;
        font-size: 14px;
    }

    .info-label {
        width: 140px;
        color: var(--text-secondary);
        font-weight: 500;
        flex-shrink: 0;
    }

    .info-val {
        color: var(--text-primary);
        font-weight: 600;
    }

    /* Items table */
    .item-thumb {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid var(--border);
        background-color: var(--bg-primary);
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 12px;
    }

    .items-table th {
        padding: 12px;
        font-size: 13px;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border);
    }

    .items-table td {
        padding: 12px;
        font-size: 13px;
        border-bottom: 1px solid var(--border);
    }

    /* Map & Receipt Styles */
    .map-container {
        height: 300px;
        border-radius: 8px;
        border: 1px solid var(--border);
        margin-top: 8px;
        z-index: 10;
    }

    .receipt-preview {
        width: 100%;
        max-height: 250px;
        object-fit: contain;
        border-radius: 8px;
        border: 1px solid var(--border);
        background-color: var(--bg-primary);
        cursor: zoom-in;
        transition: var(--transition);
    }

    .receipt-preview:hover {
        opacity: 0.9;
        border-color: var(--accent);
    }

    /* Zoom Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(15, 23, 42, 0.9);
        z-index: 999;
        align-items: center;
        justify-content: center;
        padding: 24px;
        cursor: zoom-out;
    }

    .modal-content {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        border-radius: 8px;
        border: 2px solid var(--border);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        animation: zoomIn 0.3s ease;
    }

    @keyframes zoomIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .actions-panel {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
    }
</style>
@endsection

@section('content')
<div style="margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <span class="badge badge-{{ $transaction->status }}">
            {{ $transaction->status === 'approved' ? 'Disetujui' : ($transaction->status === 'rejected' ? 'Ditolak' : 'Tertunda') }}
        </span>
        <span style="color: var(--text-muted); font-size: 14px;">Dibuat: {{ $transaction->created_at->format('d M Y, H:i') }} WIB</span>
    </div>
    <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline" style="padding: 8px 16px; font-size: 13px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
    </a>
</div>

<div class="details-layout">
    
    <!-- LEFT PANEL: Customer Info & Order Items -->
    <div>
        <!-- Customer Info Card -->
        <div class="card">
            <h4 class="info-section-title">Informasi Pelanggan</h4>
            <div class="info-list">
                <div class="info-item">
                    <div class="info-label">Nama Pelanggan</div>
                    <div class="info-val">{{ $transaction->customer_name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">No. Telepon</div>
                    <div class="info-val" style="color: #818cf8;">{{ $transaction->customer_phone }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Alamat Kirim</div>
                    <div class="info-val" style="font-weight: 400; line-height: 1.5;">{{ $transaction->shipping_address }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Kurir Pengiriman</div>
                    <div class="info-val">
                        <span style="background-color: var(--bg-primary); padding: 4px 8px; border-radius: 6px; font-size: 12px;">
                            {{ strtoupper($transaction->shipping_courier) }}
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tipe Pembeli</div>
                    <div class="info-val">
                        {{ $transaction->user_id ? 'Akun Terdaftar (ID: ' . $transaction->user_id . ')' : 'Guest (Tamu)' }}
                    </div>
                </div>
            </div>

            <h4 class="info-section-title">Rincian Belanja</h4>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center;">Gambar</th>
                        <th style="text-align: left;">Nama Sepatu</th>
                        <th style="text-align: center; width: 80px;">Ukuran</th>
                        <th style="text-align: right; width: 120px;">Harga Satuan</th>
                        <th style="text-align: center; width: 80px;">Kuantitas</th>
                        <th style="text-align: right; width: 140px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->items as $item)
                        <tr>
                            <td style="text-align: center;">
                                @if($item->shoe && $item->shoe->image_url)
                                    <img src="{{ strpos($item->shoe->image_url, 'http') === 0 ? $item->shoe->image_url : asset($item->shoe->image_url) }}" class="item-thumb" alt="Shoe">
                                @else
                                    <div class="item-thumb" style="display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                                        <i class="fa-solid fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $item->shoe ? $item->shoe->name : 'Produk Dihapus' }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $item->shoe ? $item->shoe->brand : '-' }}</div>
                            </td>
                            <td style="text-align: center; font-weight: 600; color: #818cf8;">{{ $item->shoe_size }}</td>
                            <td style="text-align: right;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td style="text-align: center; font-weight: 500;">{{ $item->quantity }}</td>
                            <td style="text-align: right; font-weight: 600; color: var(--success);">
                                Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                    <tr style="background-color: rgba(255,255,255,0.01);">
                        <td colspan="4"></td>
                        <td style="text-align: center; font-weight: 700; border-top: 2px solid var(--border);">Total:</td>
                        <td style="text-align: right; font-weight: 700; color: var(--success); border-top: 2px solid var(--border); font-size: 16px;">
                            Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Confirmation Action Form -->
            <div class="actions-panel">
                @if($transaction->status === 'pending')
                    <form action="{{ route('admin.transactions.update-status', $transaction->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-circle-check"></i> Setujui & Potong Stok
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.transactions.update-status', $transaction->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menolak pembayaran ini?')">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-circle-xmark"></i> Tolak Pembayaran
                        </button>
                    </form>
                @else
                    <div style="display: flex; align-items: center; gap: 8px; color: var(--text-secondary); font-size: 14px;">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Transaksi telah selesai diproses dengan status: <strong>{{ strtoupper($transaction->status) }}</strong>.</span>
                        
                        <form action="{{ route('admin.transactions.update-status', $transaction->id) }}" method="POST" style="display: inline-block; margin-left: 12px;">
                            @csrf
                            <input type="hidden" name="status" value="pending">
                            <button type="submit" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px;">
                                Kembalikan ke Pending
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- RIGHT PANEL: Receipt Image & GPS Map Coordinates -->
    <div>
        <!-- Payment Proof Card -->
        <div class="card">
            <h4 class="info-section-title">Bukti Pembayaran</h4>
            <div style="text-align: center; margin-top: 12px;">
                @if($transaction->payment_proof)
                    <img src="{{ strpos($transaction->payment_proof, 'http') === 0 ? $transaction->payment_proof : asset($transaction->payment_proof) }}" class="receipt-preview" alt="Bukti Transfer" onclick="openZoomModal(this.src)">
                    <small style="color: var(--text-muted); display: block; margin-top: 8px; font-style: italic;">
                        <i class="fa-solid fa-magnifying-glass-plus"></i> Klik gambar untuk memperbesar
                    </small>
                @else
                    <div style="padding: 40px 20px; background-color: var(--bg-primary); border-radius: 8px; border: 1px dashed var(--border); color: var(--text-muted);">
                        <i class="fa-solid fa-circle-xmark" style="font-size: 32px; margin-bottom: 8px;"></i>
                        <p>Bukti pembayaran belum diunggah</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- GPS Coordinates Map Card -->
        <div class="card">
            <h4 class="info-section-title">Koordinat Lokasi GPS</h4>
            <div style="margin-top: 12px;">
                @if($transaction->latitude && $transaction->longitude)
                    <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; display: flex; justify-content: space-between;">
                        <span>Latitude: <strong>{{ $transaction->latitude }}</strong></span>
                        <span>Longitude: <strong>{{ $transaction->longitude }}</strong></span>
                    </div>
                    <div id="map" class="map-container"></div>
                @else
                    <div style="padding: 40px 20px; background-color: var(--bg-primary); border-radius: 8px; border: 1px dashed var(--border); color: var(--text-muted); text-align: center;">
                        <i class="fa-solid fa-location-dot" style="font-size: 32px; margin-bottom: 8px;"></i>
                        <p>Koordinat lokasi GPS tidak terlampir</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

<!-- Zoom Receipt Modal Container -->
<div id="zoom-modal" class="modal" onclick="closeZoomModal()">
    <img id="zoomed-image" class="modal-content" alt="Bukti Transfer Perbesar">
</div>

@endsection

@section('scripts')
@if($transaction->latitude && $transaction->longitude)
<!-- Leaflet.js Map script files -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const lat = {{ $transaction->latitude }};
        const lng = {{ $transaction->longitude }};
        const customer = "{{ $transaction->customer_name }}";
        
        // Initialize map on the "map" div with coordinates
        const map = L.map('map').setView([lat, lng], 15);
        
        // Load OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Place marker at customer location coordinate
        L.marker([lat, lng]).addTo(map)
            .bindPopup(`<b>Lokasi Pelanggan:</b><br>${customer}`)
            .openPopup();
            
        // Trigger resize layout fix for Leaflet
        setTimeout(function() {
            map.invalidateSize();
        }, 300);
    });
</script>
@endif
<script>
    function openZoomModal(src) {
        const modal = document.getElementById('zoom-modal');
        const img = document.getElementById('zoomed-image');
        img.src = src;
        modal.style.display = 'flex';
    }

    function closeZoomModal() {
        document.getElementById('zoom-modal').style.display = 'none';
    }
</script>
@endsection
