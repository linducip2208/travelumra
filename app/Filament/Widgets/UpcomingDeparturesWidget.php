<?php

namespace App\Filament\Widgets;

use App\Models\Package;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingDeparturesWidget extends BaseWidget
{
    public function getHeading(): string|null { return 'Keberangkatan 30 Hari ke Depan'; }
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Package::where('departure_date', '>=', now())
                    ->where('departure_date', '<=', now()->addDays(30))
                    ->where('is_active', true)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Paket')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge(),
                Tables\Columns\TextColumn::make('departure_date')
                    ->label('Keberangkatan')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_days')
                    ->label('Durasi')
                    ->suffix(' hari'),
                Tables\Columns\TextColumn::make('booked_count')
                    ->label('Terisi'),
                Tables\Columns\TextColumn::make('quota')
                    ->label('Kuota'),
            ])
            ->defaultSort('departure_date', 'asc')
            ->paginated(false);
    }
}
