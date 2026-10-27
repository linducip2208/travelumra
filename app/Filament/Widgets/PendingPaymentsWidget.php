<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingPaymentsWidget extends BaseWidget
{
    public function getHeading(): string|null { return 'Pembayaran Menunggu Konfirmasi'; }
    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::where('status', 'pending')
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('payment_number')
                    ->label('No. Pembayaran')
                    ->searchable(),
                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label('No. Invoice'),
                Tables\Columns\TextColumn::make('booking.pilgrim.full_name')
                    ->label('Jemaah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Tanggal Bayar')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->actions([
                Actions\Action::make('konfirmasi')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (Payment $record) => $record->update([
                        'status' => 'confirmed',
                        'confirmed_by' => auth()->id(),
                        'confirmed_at' => now(),
                    ]))
                    ->requiresConfirmation(),
                Actions\Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(fn (Payment $record) => $record->update(['status' => 'rejected']))
                    ->requiresConfirmation(),
            ]);
    }
}
