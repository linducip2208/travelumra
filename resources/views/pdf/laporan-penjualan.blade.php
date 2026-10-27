<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Laporan Penjualan</title>
<style>body{font-family:sans-serif;font-size:12px;margin:20px}h1{font-size:18px;text-align:center;margin-bottom:5px}.sub{text-align:center;color:#666;margin-bottom:20px}.summary{display:flex;gap:10px;margin-bottom:20px}.card{flex:1;padding:12px;border:1px solid #ddd;border-radius:8px;text-align:center}.card .label{font-size:10px;color:#888;text-transform:uppercase}.card .value{font-size:18px;font-weight:bold;margin-top:4px}table{width:100%;border-collapse:collapse;margin-top:10px}th{background:#4f46e5;color:#fff;padding:8px;text-align:left;font-size:11px}td{padding:6px 8px;border-bottom:1px solid #eee;font-size:11px}tr:nth-child(even){background:#f9fafb}.footer{text-align:center;margin-top:30px;font-size:10px;color:#999}</style></head>
<body>
<h1>Laporan Penjualan</h1>
<div class="sub">Periode: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</div>
<div class="summary">
    <div class="card"><div class="label">Total Booking</div><div class="value">{{ $totalBooking }}</div></div>
    <div class="card"><div class="label">Total Pendapatan</div><div class="value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div></div>
    <div class="card"><div class="label">Pembayaran Masuk</div><div class="value">Rp {{ number_format($payments, 0, ',', '.') }}</div></div>
    <div class="card"><div class="label">Rata-rata/Booking</div><div class="value">Rp {{ number_format($avgPerBooking, 0, ',', '.') }}</div></div>
</div>
<table>
    <thead><tr><th>No. Booking</th><th>Jamaah</th><th>Paket</th><th>Total</th><th>Status</th><th>Tanggal</th></tr></thead>
    <tbody>
    @foreach($bookings as $b)
    <tr>
        <td>{{ $b->booking_number }}</td>
        <td>{{ $b->pilgrim?->full_name ?? '-' }}</td>
        <td>{{ $b->package?->name ?? '-' }}</td>
        <td>Rp {{ number_format($b->total_price, 0, ',', '.') }}</td>
        <td>{{ $b->status }}</td>
        <td>{{ $b->created_at?->format('d M Y') }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
<div class="footer">Dicetak pada {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
