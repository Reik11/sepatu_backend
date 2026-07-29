@extends('layouts.admin')

@section('title', 'Edit Produk Sepatu')
@section('header-title', 'Edit Produk Sepatu')

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

    .image-preview-container {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 8px;
    }

    .img-preview {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid var(--border);
        background-color: var(--bg-primary);
    }
</style>
@endsection

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
        <h3 style="font-size: 18px; font-weight: 700;">Edit Detail Sepatu</h3>
        <a href="{{ route('admin.shoes.index') }}" class="btn btn-outline" style="padding: 8px 16px; font-size: 13px;">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

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

    <form action="{{ route('admin.shoes.update', $shoe->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-grid">
            <div class="form-group">
                <label for="name">Nama Sepatu *</label>
                <input type="text" id="name" name="name" class="input-field" value="{{ old('name', $shoe->name) }}" required>
            </div>
            
            <div class="form-group">
                <label for="brand">Merk / Brand *</label>
                <input type="text" id="brand" name="brand" class="input-field" value="{{ old('brand', $shoe->brand) }}" required>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="category">Kategori *</label>
                <select id="category" name="category" class="input-field" style="padding-left: 16px; cursor: pointer;" required>
                    <option value="Running" {{ old('category', $shoe->category) == 'Running' ? 'selected' : '' }}>Running</option>
                    <option value="Casual" {{ old('category', $shoe->category) == 'Casual' ? 'selected' : '' }}>Casual</option>
                    <option value="Basketball" {{ old('category', $shoe->category) == 'Basketball' ? 'selected' : '' }}>Basketball</option>
                    <option value="Sneakers" {{ old('category', $shoe->category) == 'Sneakers' ? 'selected' : '' }}>Sneakers</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="price">Harga (Rupiah) *</label>
                <input type="number" id="price" name="price" class="input-field" value="{{ old('price', intval($shoe->price)) }}" min="0" required>
            </div>
        </div>

        <div class="form-group">
            <label>Pilih Ukuran yang Tersedia *</label>
            <div class="sizes-container">
                @php
                    $activeSizes = is_array($shoe->sizes) ? $shoe->sizes : [];
                @endphp
                @foreach([38, 39, 40, 41, 42, 43, 44, 45] as $size)
                    <label class="size-checkbox-label">
                        <input type="checkbox" name="sizes[]" value="{{ $size }}" 
                            {{ (is_array(old('sizes', $activeSizes)) && in_array($size, old('sizes', $activeSizes))) ? 'checked' : '' }}>
                        <div class="size-box">{{ $size }}</div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="stock">Jumlah Stok *</label>
                <input type="number" id="stock" name="stock" class="input-field" value="{{ old('stock', $shoe->stock) }}" min="0" required>
            </div>
            
            <div class="form-group">
                <label>Foto Sepatu</label>
                <div class="image-preview-container">
                    @if($shoe->image_url)
                        <img src="{{ strpos($shoe->image_url, 'http') === 0 ? $shoe->image_url : asset($shoe->image_url) }}" class="img-preview" alt="Preview">
                    @endif
                    <div style="flex-grow: 1;">
                        <input type="file" id="image" name="image" class="input-field" style="padding-left: 16px;" accept="image/*">
                        <small style="color: var(--text-muted); display: block; margin-top: 4px;">Pilih file baru jika ingin mengganti foto produk.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Deskripsi Produk</label>
            <textarea id="description" name="description" class="input-field">{{ old('description', $shoe->description) }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 32px;">
            <a href="{{ route('admin.shoes.index') }}" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
