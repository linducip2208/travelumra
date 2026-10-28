<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>Laporan Keuangan</title>
<style>body{font-family:sans-serif;font-size:12px;margin:20px}h1{font-size:18px;text-align:center}.sub{text-align:center;color:#666;margin-bottom:20px}.card{padding:12px;border:1px solid #ddd;text-align:center}.card .label{font-size:10px;color:#888}.card .value{font-size:18px;font-weight:bold;margin-top:4px}table{width:100%;border-collapse:collapse}th{background:#4f46e5;color:#fff;padding:8px;text-align:left;font-size:11px}td{padding:6px 8px;border-bottom:1px solid #eee;font-size:11px}.footer{text-align:center;margin-top:30px;font-size:10px;color:#999}</style></head>
<body>
<h1>Laporan Keuangan</h1>
<div class="sub">{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</div>
<table width="100%" style="margin-bottom:20px"><tr>
    <td class="card"><div class="label">Pendapatan</div><div class="value">Rp {{ number_format($payments, 0, ',', '.') }}</div></td>
    <td class="card"><div class="label">Pengeluaran</div><div class="value">Rp {{ number_format($expenses, 0, ',', '.') }}</div></td>
    <td class="card"><div class="label">Selisih</div><div class="value">Rp {{ number_format($payments - $expenses, 0, ',', '.') }}</div></td>
    <td class="card"><div class="label">Piutang</div><div class="value">Rp {{ number_format($outstanding, 0, ',', '.') }}</div></td>
</tr></table>
<div class="footer">Dicetak pada {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
