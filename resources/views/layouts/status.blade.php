@php
    $colors = ['pending' => 'secondary', 'processing' => 'info', 'shipped' => 'primary', 'completed' => 'success', 'cancelled' => 'danger'];
@endphp
<span class="badge bg-{{ $colors[$order->order_status] ?? 'secondary' }}">{{ $order->status_label }}</span>
<span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'danger' : 'warning text-dark') }}">
    {{ ['pending' => 'Belum bayar', 'paid' => 'Lunas', 'failed' => 'Gagal'][$order->payment_status] }}</span>
