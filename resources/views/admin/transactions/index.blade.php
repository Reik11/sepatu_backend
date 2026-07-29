@extends('layouts.admin')

@section('title', 'Manajemen Transaksi')
@section('header-title', 'Manajemen Transaksi')

@section('styles')
<style>
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
    }

    .search-input-group {
        position: relative;
        flex-grow: 1;
        max-width: 400px;
    }

    .search-input-group i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }

    .form-control {
        width: 100%;
        background-color: var(--bg-primary);
        border: 1px solid var(--border);
        color: var(--text-primary);
        padding: 12px 16px;
        padding-left: 44px;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: var(--transition);
    }

    .form-control:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 2px var(--accent-light);
    }

    .select-control {
        background-color: var(--bg-primary);
        border: 1px solid var(--border);
        color: var(--text-primary);
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        cursor: pointer;
        min-width: 180px;
        transition: var(--transition);
    }

    .select-control:focus {
        border-color: var(--accent);
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
        vertical-align: middle;
    }

    tr:hover td {
        background-color: rgba(255, 255, 255, 0.02);
    }

    .text-right {
        text-align: right;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="filter-bar">
        <!-- Live Search -->
        <div class="search-input-group">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="tx-search-input" class="form-control" placeholder="Cari nama pembeli atau ID..." onkeyup="filterTransactions()">
        </div>

        <div>
            <!-- Status Filter -->
            <select id="tx-status-select" class="select-control" onchange="filterTransactions()">
                <option value="All">Semua Status</option>
                <option value="pending">Menunggu Konfirmasi (Pending)</option>
                <option value="approved">Disetujui (Approved)</option>
                <option value="rejected">Ditolak (Rejected)</option>
            </select>
        </div>
    </div>

    <div class="table-container">
        <table id="transactions-table">
            <thead>
                <tr>
                    <th style="width: 120px;">ID Pesanan</th>
                    <th>Nama Pembeli</th>
                    <th>No. Telepon</th>
                    <th>Tanggal & Waktu</th>
                    <th>Kurir</th>
                    <th class="text-right">Total Transaksi</th>
                    <th style="text-align: center; width: 160px;">Status</th>
                    <th style="text-align: center; width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="transactions-tbody">
                @if($transactions->isEmpty())
                    <tr id="no-records-row">
                        <td colspan="8" style="text-align: center; padding: 48px; color: var(--text-secondary);">
                            <i class="fa-solid fa-receipt" style="font-size: 48px; color: var(--text-muted); margin-bottom: 12px;"></i>
                            <p>Belum ada transaksi masuk.</p>
                        </td>
                    </tr>
                @else
                    @foreach($transactions as $tx)
                        <tr class="tx-row" data-id="{{ $tx->id }}" data-name="{{ strtolower($tx->customer_name) }}" data-status="{{ $tx->status }}">
                            <td style="font-weight: 600; color: #818cf8;">#SS-{{ $tx->id }}</td>
                            <td style="font-weight: 600;">{{ $tx->customer_name }}</td>
                            <td>{{ $tx->customer_phone }}</td>
                            <td>{{ $tx->created_at->format('d M Y, H:i') }} WIB</td>
                            <td>
                                <span style="background-color: var(--bg-tertiary); padding: 4px 8px; border-radius: 6px; font-size: 13px; font-weight: 500;">
                                    {{ strtoupper($tx->shipping_courier) }}
                                </span>
                            </td>
                            <td class="text-right" style="font-weight: 600; color: var(--success);">
                                Rp {{ number_format($tx->total_price, 0, ',', '.') }}
                            </td>
                            <td style="text-align: center;">
                                <span class="badge badge-{{ $tx->status }}">
                                    {{ $tx->status === 'approved' ? 'Disetujui' : ($tx->status === 'rejected' ? 'Ditolak' : 'Tertunda') }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('admin.transactions.show', $tx->id) }}" class="btn btn-outline" style="padding: 6px 12px; font-size: 13px;">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    
                    <tr id="js-no-tx-results" style="display: none;">
                        <td colspan="8" style="text-align: center; padding: 48px; color: var(--text-secondary);">
                            <i class="fa-solid fa-magnifying-glass" style="font-size: 40px; color: var(--text-muted); margin-bottom: 12px;"></i>
                            <p>Tidak ada transaksi yang sesuai.</p>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function filterTransactions() {
        const searchQuery = document.getElementById('tx-search-input').value.toLowerCase().trim();
        const selectedStatus = document.getElementById('tx-status-select').value;
        const rows = document.getElementsByClassName('tx-row');
        const jsNoResults = document.getElementById('js-no-tx-results');
        
        let visibleCount = 0;
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const name = row.getAttribute('data-name');
            const id = row.getAttribute('data-id');
            const status = row.getAttribute('data-status');
            
            const matchesSearch = name.includes(searchQuery) || id.includes(searchQuery);
            const matchesStatus = selectedStatus === 'All' || status === selectedStatus;
            
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        }
        
        if (rows.length > 0) {
            if (visibleCount === 0) {
                jsNoResults.style.display = '';
            } else {
                jsNoResults.style.display = 'none';
            }
        }
    }
</script>
@endsection
