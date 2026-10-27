<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Invoice {{ $invoice->invoice_number }}</title>
<style>body{font-family:sans-serif;font-size:12px;margin:30px}.header{display:flex;justify-content:space-between;margin-bottom:30px}.logo{font-size:20px;font-weight:bold;color:#4f46e5}.invoice-title{font-size:24px;font-weight:bold;color:#333}.info{display:flex;gap:40px;margin-bottom:30px}.info-block h4{font-size:11px;color:#888;margin-bottom:4px}.info-block p{font-size:12px;margin:2px 0}table{width:100%;border-collapse:collapse;margin:20px 0}th{background:#4f46e5;color:#fff;padding:10px;text-align:left}td{padding:8px 10px;border-bottom:1px solid #eee}.total{text-align:right;font-size:16px;font-weight:bold;margin-top:20px}.footer{text-align:center;margin-top:40px;font-size:10px;color:#999}</style></head>
<body>
<div class="header">
    <div class="logo">{{ \App\Models\Setting::getValue('site_brand_name', config('app.name', 'Travel Umrah')) }}</div>
    <div class="invoice-title">INVOICE</div>
</div>
<div class="info">
    <div class="info-block">
        <h4>No. Invoice</h4><p>{{ $invoice->invoice_number }}</p>
        <h4>Tanggal</h4><p>{{ $invoice->created_at?->format('d M Y') }}</p>
    </div>
    <div class="info-block">
        <h4>Jamaah</h4><p>{{ $invoice->booking?->pilgrim?->full_name ?? '-' }}</p>
        <h4>Paket</h4><p>{{ $invoice->booking?->package?->name ?? '-' }}</p>
    </div>
    <div class="info-block">
        <h4>Status</h4><p>{{ $invoice->status }}</p>
        <h4>Jatuh Tempo</h4><p>{{ $invoice->due_date?->format('d M Y') ?? '-' }}</p>
    </div>
</div>
<table>
    <thead><tr><th>Deskripsi</th><th>Jumlah</th></tr></thead>
    <tbody>
    <tr><td>Paket {{ $invoice->booking?->package?->name ?? 'Umrah' }}</td><td>Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td></tr>
    </tbody>
</table>
<div class="total">Total: Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</div>
<div class="total">Terbayar: Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</div>
<div class="total">Sisa: Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</div>
<div class="footer">Terima kasih telah mempercayakan perjalanan ibadah Anda bersama kami.</div>
</body>
</html>
