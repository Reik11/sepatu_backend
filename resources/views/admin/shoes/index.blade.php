@extends('layouts.admin')

@section('title', 'Kelola Produk Sepatu')
@section('header-title', 'Kelola Produk Sepatu')

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
        min-width: 150px;
        transition: var(--transition);
    }

    .select-control:focus {
        border-color: var(--accent);
    }

    /* Shoe Grid / Table Styles */
    .shoe-thumb {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        background-color: var(--bg-primary);
        border: 1px solid var(--border);
    }

    .size-badge {
        display: inline-block;
        background-color: var(--bg-tertiary);
        color: var(--text-secondary);
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 4px;
        margin: 2px;
        font-weight: 600;
    }

    .btn-actions {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    /* Table elements styling */
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
</style>
@endsection

@section('content')
<div class="card">
    <div class="filter-bar">
        <!-- Live Search Bar -->
        <div class="search-input-group">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="search-input" class="form-control" placeholder="Cari nama sepatu atau merk..." onkeyup="filterShoesTable()">
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
            <!-- Category Filter Dropdown -->
            <select id="category-select" class="select-control" onchange="filterShoesTable()">
                <option value="All">Semua Kategori</option>
                <option value="Running">Running</option>
                <option value="Casual">Casual</option>
                <option value="Basketball">Basketball</option>
                <option value="Sneakers">Sneakers</option>
            </select>

            <!-- Create Product Button -->
            <a href="{{ route('admin.shoes.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Tambah Sepatu
            </a>
        </div>
    </div>

    <!-- Shoes Table -->
    <div class="table-container">
        <table id="shoes-table">
            <thead>
                <tr>
                    <th style="width: 80px; text-align: center;">Gambar</th>
                    <th>Nama Produk</th>
                    <th>Merk</th>
                    <th>Kategori</th>
                    <th style="width: 250px;">Ukuran Tersedia</th>
                    <th style="text-align: right;">Harga</th>
                    <th style="text-align: center; width: 100px;">Stok</th>
                    <th style="text-align: center; width: 160px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="shoes-tbody">
                @if($shoes->isEmpty())
                    <tr id="no-results-row">
                        <td colspan="8" style="text-align: center; padding: 48px; color: var(--text-secondary);">
                            <i class="fa-solid fa-shoe-prints" style="font-size: 48px; color: var(--text-muted); margin-bottom: 12px;"></i>
                            <p>Katalog sepatu kosong. Silakan tambahkan sepatu baru.</p>
                        </td>
                    </tr>
                @else
                    @foreach($shoes as $shoe)
                        <tr class="shoe-row" data-name="{{ strtolower($shoe->name) }}" data-brand="{{ strtolower($shoe->brand) }}" data-category="{{ $shoe->category }}">
                            <td style="text-align: center;">
                                @if($shoe->image_url)
                                    <img src="{{ strpos($shoe->image_url, 'http') === 0 ? $shoe->image_url : asset($shoe->image_url) }}" class="shoe-thumb" alt="{{ $shoe->name }}">
                                @else
                                    <div class="shoe-thumb" style="display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                                        <i class="fa-solid fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td style="font-weight: 600;">{{ $shoe->name }}</td>
                            <td>
                                <span style="background-color: rgba(255,255,255,0.05); padding: 4px 8px; border-radius: 6px; font-size: 13px;">
                                    {{ $shoe->brand }}
                                </span>
                            </td>
                            <td>{{ $shoe->category }}</td>
                            <td>
                                @if(is_array($shoe->sizes))
                                    @foreach($shoe->sizes as $size)
                                        <span class="size-badge">{{ $size }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td style="text-align: right; font-weight: 600; color: var(--success);">
                                Rp {{ number_format($shoe->price, 0, ',', '.') }}
                            </td>
                            <td style="text-align: center; font-weight: 500; color: {{ $shoe->stock < 5 ? 'var(--danger)' : 'var(--text-primary)' }}">
                                {{ $shoe->stock }}
                            </td>
                            <td>
                                <div class="btn-actions">
                                    <a href="{{ route('admin.shoes.edit', $shoe->id) }}" class="btn btn-outline" style="padding: 6px 12px; font-size: 13px; color: #38bdf8; border-color: rgba(56, 189, 248, 0.2);">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    
                                    <!-- Delete form -->
                                    <form action="{{ route('admin.shoes.destroy', $shoe->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sepatu ini dari katalog?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline" style="padding: 6px 12px; font-size: 13px; color: var(--danger); border-color: rgba(239, 68, 68, 0.2);">
                                            <i class="fa-solid fa-trash-can"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    
                    <!-- Dynamically shown when no elements match JavaScript search filters -->
                    <tr id="js-no-results" style="display: none;">
                        <td colspan="8" style="text-align: center; padding: 48px; color: var(--text-secondary);">
                            <i class="fa-solid fa-magnifying-glass" style="font-size: 40px; color: var(--text-muted); margin-bottom: 12px;"></i>
                            <p>Tidak ada sepatu yang cocok dengan pencarian Anda.</p>
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
    function filterShoesTable() {
        const searchQuery = document.getElementById('search-input').value.toLowerCase().trim();
        const selectedCategory = document.getElementById('category-select').value;
        const rows = document.getElementsByClassName('shoe-row');
        const jsNoResults = document.getElementById('js-no-results');
        
        let visibleCount = 0;
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const name = row.getAttribute('data-name');
            const brand = row.getAttribute('data-brand');
            const category = row.getAttribute('data-category');
            
            const matchesSearch = name.includes(searchQuery) || brand.includes(searchQuery);
            const matchesCategory = selectedCategory === 'All' || category === selectedCategory;
            
            if (matchesSearch && matchesCategory) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        }
        
        // Show/hide no results row
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
