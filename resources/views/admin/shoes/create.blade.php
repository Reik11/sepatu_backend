@extends('layouts.admin')

@section('title', 'Tambah Sepatu Baru')
@section('header-title', 'Tambah Sepatu Baru')

@section('styles')
<style>
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 8px;
    }

    .input-field {
        width: 100%;
        background-color: var(--bg-primary);
        border: 1px solid var(--border);
        color: var(--text-primary);
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: var(--transition);
    }

    .input-field:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 2px var(--accent-light);
    }

    textarea.input-field {
        resize: vertical;
        min-height: 120px;
    }

    /* Size checkbox selector styling */
    .sizes-container {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 8px;
    }

    .size-checkbox-label {
        position: relative;
        cursor: pointer;
    }

    .size-checkbox-label input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    .size-box {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        background-color: var(--bg-primary);
        border: 1px solid var(--border);
        color: var(--text-secondary);
        font-weight: 700;
        font-size: 14px;
        border-radius: 8px;
        transition: var(--transition);
        user-select: none;
    }

    .size-checkbox-label input:checked ~ .size-box {
        background-color: var(--accent);
        border-color: var(--accent);
        color: white;
        box-shadow: 0 0 10px rgba(99, 102, 241, 0.4);
    }

    .size-checkbox-label:hover .size-box {
        border-color: var(--accent);
    }

    .error-text {
        color: var(--danger);
        font-size: 12px;
        margin-top: 4px;
        display: block;
    }
</style>
@endsection

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
        <h3 style="font-size: 18px; font-weight: 700;">Formulir Produk Sepatu</h3>
        <a href="{{ route('admin.shoes.index') }}" class="btn btn-outline" style="padding: 8px 16px; font-size: 13px;">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Validation errors banner -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong style="display: block; margin-bottom: 4px;">Terjadi kesalahan input:</strong>
                <ul style="list-style: inside; font-size: 13px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.shoes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-grid">
            <div class="form-group">
                <label for="name">Nama Sepatu *</label>
                <input type="text" id="name" name="name" class="input-field" value="{{ old('name') }}" placeholder="Contoh: Air Max 90" required>
            </div>
            
            <div class="form-group">
                <label for="brand">Merk / Brand *</label>
                <input type="text" id="brand" name="brand" class="input-field" value="{{ old('brand') }}" placeholder="Contoh: Nike, Adidas" required>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="category">Kategori *</label>
                <select id="category" name="category" class="input-field" style="padding-left: 16px; cursor: pointer;" required>
                    <option value="" disabled selected>Pilih Kategori</option>
                    <option value="Running" {{ old('category') == 'Running' ? 'selected' : '' }}>Running</option>
                    <option value="Casual" {{ old('category') == 'Casual' ? 'selected' : '' }}>Casual</option>
                    <option value="Basketball" {{ old('category') == 'Basketball' ? 'selected' : '' }}>Basketball</option>
                    <option value="Sneakers" {{ old('category') == 'Sneakers' ? 'selected' : '' }}>Sneakers</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="price">Harga (Rupiah) *</label>
                <input type="number" id="price" name="price" class="input-field" value="{{ old('price') }}" placeholder="Contoh: 1450000" min="0" required>
            </div>
        </div>

        <div class="form-group">
            <label>Pilih Ukuran yang Tersedia *</label>
            <div class="sizes-container">
                @foreach([38, 39, 40, 41, 42, 43, 44, 45] as $size)
                    <label class="size-checkbox-label">
                        <input type="checkbox" name="sizes[]" value="{{ $size }}" {{ (is_array(old('sizes')) && in_array($size, old('sizes'))) ? 'checked' : '' }}>
                        <div class="size-box">{{ $size }}</div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="stock">Jumlah Stok Awal *</label>
                <input type="number" id="stock" name="stock" class="input-field" value="{{ old('stock', 10) }}" placeholder="Masukkan kuantitas stok" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="image">Foto Sepatu</label>
                <input type="file" id="image" name="image" class="input-field" style="padding-left: 16px;" accept="image/*">
            </div>
        </div>

        <div class="form-group">
            <label for="description">Deskripsi Produk</label>
            <textarea id="description" name="description" class="input-field" placeholder="Tuliskan spesifikasi bahan, kelebihan, dan detail sepatu lainnya...">{{ old('description') }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 32px;">
            <button type="reset" class="btn btn-outline">Reset Form</button>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-cloud-arrow-up"></i> Simpan Produk
            </button>
        </div>
    </form>
</div>
@endsection
