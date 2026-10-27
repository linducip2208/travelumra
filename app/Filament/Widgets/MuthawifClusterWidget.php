<?php

namespace App\Filament\Widgets;

use App\Models\Cluster;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MuthawifClusterWidget extends BaseWidget
{
    protected static ?int $sort = 8;

    public function getHeading(): string|null { return 'Kloter & Jamaah Bimbingan Saya'; }

    public static function canView(): bool
    {
        return auth()->user()?->role === 'muthawif';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Cluster::withCount('bookings')
                    ->where('muthawif_id', auth()->id())
                    ->whereNotIn('status', ['completed', 'cancelled'])
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Kode Kloter')->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Nama Kloter')->searchable(),
                Tables\Columns\TextColumn::make('bookings_count')->label('Jamaah')->sortable(),
                Tables\Columns\TextColumn::make('capacity')->label('Kapasitas'),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
