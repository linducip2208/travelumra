<?php
namespace App\Filament\Exports;
use App\Models\Invoice;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InvoiceExporter extends Exporter
{
    protected static ?string $model = Invoice::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('invoice_number')->label('No. Invoice'),
            ExportColumn::make('booking.pilgrim.full_name')->label('Jamaah'),
            ExportColumn::make('booking.package.name')->label('Paket'),
            ExportColumn::make('total_amount')->label('Total'),
            ExportColumn::make('paid_amount')->label('Terbayar'),
            ExportColumn::make('remaining_amount')->label('Sisa'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('due_date')->label('Jatuh Tempo'),
            ExportColumn::make('created_at')->label('Tgl Dibuat'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Export invoice selesai. ' . number_format($export->successful_rows) . ' baris.';
    }
}
