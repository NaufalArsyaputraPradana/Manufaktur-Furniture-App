<div class="hdr">
    <strong>UD Bisa Furniture</strong><br>
    <span class="muted">Invoice / Nota — {{ $order->order_number }}</span>
</div>

<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td width="50%" valign="top">
            <strong>Pelanggan</strong><br>
            {{ $order->user?->name }}<br>
            {{ $order->user?->email }}<br>
            @if($order->user?->phone)
                {{ $order->user->phone }}<br>
            @endif
        </td>
        <td width="50%" valign="top" style="text-align: right;">
            <strong>Tanggal</strong>
            {{ $order->order_date?->format('d M Y') ?? $order->created_at->format('d M Y') }}<br>
            <strong>Status pembayaran</strong>
            {{ $order->payment?->payment_status === \App\Models\Payment::STATUS_PAID ? 'Lunas' : 'DP terbayar' }}<br>
        </td>
    </tr>
</table>

<p class="muted"><strong>Alamat pengiriman</strong><br>{{ $order->shipping_address ?? '-' }}</p>

<table class="items">
    <thead>
        <tr>
            <th>Item</th>
            <th class="right">Qty</th>
            <th class="right">Harga</th>
            <th class="right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($order->orderDetails as $row)
            <tr>
                <td>{{ $row->product_name }}</td>
                <td class="right">{{ $row->quantity }}</td>
                <td class="right">Rp {{ number_format((float) $row->unit_price, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format((float) $row->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p class="total right">
    Total: Rp {{ number_format((float) $order->total, 0, ',', '.') }}
</p>
@if ($order->payment)
    <p class="muted right">
        Terbayar: Rp {{ number_format((float) ($order->payment->amount_paid ?? 0), 0, ',', '.') }}
    </p>
@endif

<p class="muted" style="margin-top: 32px;">Dokumen ini dibuat secara elektronik dan sah tanpa tanda tangan basah.</p>
