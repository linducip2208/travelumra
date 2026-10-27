<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AgentCommissionWidget extends BaseWidget
{
    protected static ?int $sort = 10;

    public function getHeading(): string|null { return 'Booking & Komisi Saya'; }

    public static function canView(): bool
    {
        return auth()->user()?->role === 'agent';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::where('agent_id', auth()->id())->latest()->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('booking_number')->label('No. Booking')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('pilgrim.full_name')->label('Jamaah')->searchable(),
                Tables\Columns\TextColumn::make('package.name')->label('Paket')->limit(30),
                Tables\Columns\TextColumn::make('total_price')->label('Total')->money('IDR'),
                Tables\Columns\TextColumn::make('referral_commission')->label('Komisi')->money('IDR'),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal')->date('d M Y'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
