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
@endif
