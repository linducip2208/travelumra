<?php
namespace App\Filament\Exports;
use App\Models\Booking;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class BookingExporter extends Exporter
{
    protected static ?string $model = Booking::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('booking_number')->label('No. Booking'),
            ExportColumn::make('pilgrim.full_name')->label('Jamaah'),
            ExportColumn::make('package.name')->label('Paket'),
            ExportColumn::make('package.departure_date')->label('Tgl Berangkat'),
            ExportColumn::make('room_type')->label('Tipe Kamar'),
            ExportColumn::make('total_price')->label('Total Harga'),
            ExportColumn::make('paid_amount')->label('Terbayar'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('branch.name')->label('Cabang'),
            ExportColumn::make('agent.name')->label('Agent'),
            ExportColumn::make('created_at')->label('Tgl Dibuat'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Export booking selesai. ' . number_format($export->successful_rows) . ' baris berhasil.';
    }
}
