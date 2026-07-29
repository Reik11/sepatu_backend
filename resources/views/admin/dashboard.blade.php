@extends('layouts.admin')

@section('title', 'Dashboard Overview')
@section('header-title', 'Dashboard Overview')

@section('styles')
<style>
    /* Stats Grid Layout */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .stat-card {
        background-color: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        border-color: var(--accent);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        width: 100px;
        height: 100px;
        background: radial-gradient(circle, var(--accent-light) 0%, transparent 70%);
        top: -50px;
        right: -50px;
        border-radius: 50%;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin-top: 8px;
    }

    .stat-label {
        font-size: 14px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        background-color: var(--accent-light);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent);
        font-size: 20px;
    }

    /* Table styling */
    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    th {
        padding: 16px;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 14px;
        border-bottom: 2px solid var(--border);
    }

    td {
        padding: 16px;
        color: var(--text-primary);
        font-size: 14px;
        border-bottom: 1px solid var(--border);
    }

    tr:hover td {
        background-color: rgba(255, 255, 255, 0.02);
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

    .text-right {
        text-align: right;
    }
</style>
@endsection

@section('content')
<!-- Stats Cards Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div>
            <p class="stat-label">Total Penjualan</p>
            <p class="stat-value">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
        </div>
        <div class="stat-icon" style="color: var(--success); background-color: rgba(16, 185, 129, 0.15);">
            <i class="fa-solid fa-wallet"></i>
        </div>
    </div>
    
    <div class="stat-card">
        <div>
            <p class="stat-label">Total Transaksi</p>
            <p class="stat-value">{{ $totalOrders }}</p>
        </div>
        <div class="stat-icon" style="color: var(--accent); background-color: var(--accent-light);">
            <i class="fa-solid fa-receipt"></i>
        </div>
    </div>

    <div class="stat-card">
        <div>
            <p class="stat-label">Perlu Konfirmasi</p>
            <p class="stat-value">{{ $pendingOrders }}</p>
        </div>
        <div class="stat-icon" style="color: var(--warning); background-color: rgba(245, 158, 11, 0.15);">
            <i class="fa-solid fa-clock"></i>
        </div>
    </div>

    <div class="stat-card">
        <div>
            <p class="stat-label">Jenis Sepatu</p>
            <p class="stat-value">{{ $totalShoes }}</p>
        </div>
        <div class="stat-icon" style="color: #38bdf8; background-color: rgba(56, 189, 248, 0.15);">
            <i class="fa-solid fa-shoe-prints"></i>
        </div>
    </div>
</div>

<!-- Recent Transactions Card -->
<div class="card">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
        <h3 style="font-size: 18px; font-weight: 700;">Transaksi Terbaru</h3>
        <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline" style="padding: 8px 16px; font-size: 13px;">
            Lihat Semua Transaksi
        </a>
    </div>

    <div class="table-container">
        @if($recentTransactions->isEmpty())
            <div style="text-align: center; padding: 32px; color: var(--text-secondary);">
                <i class="fa-solid fa-receipt" style="font-size: 40px; margin-bottom: 12px; color: var(--text-muted);"></i>
                <p>Belum ada transaksi masuk saat ini.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Nama Pelanggan</th>
                        <th>Tanggal Transaksi</th>
                        <th class="text-right">Total Belanja</th>
                        <th style="width: 150px; text-align: center;">Status</th>
                        <th style="width: 100px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTransactions as $tx)
                        <tr>
                            <td style="font-weight: 600; color: #818cf8;">#SS-{{ $tx->id }}</td>
                            <td>{{ $tx->customer_name }}</td>
                            <td>{{ $tx->created_at->format('d M Y, H:i') }} WIB</td>
                            <td class="text-right" style="font-weight: 600; color: var(--success);">
                                Rp {{ number_format($tx->total_price, 0, ',', '.') }}
                            </td>
                            <td style="text-align: center;">
                                <span class="badge badge-{{ $tx->status }}">
                                    {{ $tx->status === 'approved' ? 'Disetujui' : ($tx->status === 'rejected' ? 'Ditolak' : 'Tertunda') }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('admin.transactions.show', $tx->id) }}" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px;">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
