@props(['status', 'type' => null])

@php
    $statusLower = strtolower($status);
    $badgeClass = 'badge-muted';

    if ($type) {
        $badgeClass = 'badge-' . $type;
    } else {
        if (in_array($statusLower, ['aman', 'disetujui', 'selesai', 'diterima', 'aktif'])) {
            $badgeClass = 'badge-ok';
        } elseif (in_array($statusLower, ['menipis', 'menunggu', 'dikirim', 'diperiksa', 'perbaikan', 'kirim'])) {
            $badgeClass = 'badge-warn';
        } elseif (in_array($statusLower, ['kritis', 'ditolak', 'rusak', 'selisih'])) {
            $badgeClass = 'badge-danger';
        } elseif (in_array($statusLower, ['diproses', 'diperbaiki', 'masuk', 'terima', 'penyesuaian'])) {
            $badgeClass = 'badge-accent';
        }
    }
@endphp

<span class="badge {{ $badgeClass }}">
    {{ $status }}
</span>
