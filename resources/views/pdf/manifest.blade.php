<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Manifest {{ $cluster->code }}</title>
<style>body{font-family:sans-serif;font-size:11px;margin:20px}h1{font-size:18px;text-align:center}.info{display:flex;gap:20px;margin-bottom:15px;font-size:12px}.info span{font-weight:bold}table{width:100%;border-collapse:collapse}th{background:#4f46e5;color:#fff;padding:8px;text-align:left;font-size:10px}td{padding:6px 8px;border-bottom:1px solid #eee;font-size:11px}tr:nth-child(even){background:#f9fafb}.footer{text-align:center;margin-top:30px;font-size:10px;color:#999}</style></head>
<body>
<h1>Manifest Kloter</h1>
<div class="info">
    <span>Kloter: {{ $cluster->code }} - {{ $cluster->name }}</span>
    <span>Paket: {{ $cluster->package?->name ?? '-' }}</span>
    <span>Jamaah: {{ $cluster->bookings->count() }} / {{ $cluster->capacity }}</span>
</div>
<table>
    <thead><tr><th>No</th><th>No. Booking</th><th>Nama Jamaah</th><th>No. Paspor</th><th>No. Kursi</th><th>No. Kamar</th></tr></thead>
    <tbody>
    @foreach($cluster->bookings as $i => $booking)
    @php $pivot = $booking->pivot; @endphp
    <tr>
        <td>{{ $i + 1 }}</td>
        <td>{{ $booking->booking_number }}</td>
        <td>{{ $booking->pilgrim?->full_name ?? '-' }}</td>
        <td>{{ $booking->pilgrim?->passport_number ?? '-' }}</td>
        <td>{{ $pivot->seat_number ?? '-' }}</td>
        <td>{{ $pivot->room_number ?? '-' }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
<div class="footer">Manifest dicetak pada {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
