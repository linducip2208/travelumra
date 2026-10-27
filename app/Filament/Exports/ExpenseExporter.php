<?php
namespace App\Filament\Exports;
use App\Models\Expense;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ExpenseExporter extends Exporter
{
    protected static ?string $model = Expense::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('expense_number')->label('No. EXP'),
            ExportColumn::make('category.name')->label('Kategori'),
            ExportColumn::make('description')->label('Deskripsi'),
            ExportColumn::make('amount')->label('Jumlah'),
            ExportColumn::make('expense_date')->label('Tanggal'),
            ExportColumn::make('payment_method')->label('Metode'),
            ExportColumn::make('recipient')->label('Penerima'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('branch.name')->label('Cabang'),
            ExportColumn::make('creator.name')->label('Dibuat Oleh'),
            ExportColumn::make('created_at')->label('Tgl Dibuat'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Export pengeluaran selesai. ' . number_format($export->successful_rows) . ' baris.';
    }
}
