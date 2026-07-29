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
@endif
