<?php

namespace App\Filament\Widgets;

use App\Services\ApprovalService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingApprovalsWidget extends BaseWidget
{
    protected static ?int $sort = 9;

    public function getHeading(): string|null { return 'Booking Menunggu Approval'; }

    public static function canView(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin', 'finance']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\Booking::where('status', 'pending_approval')
                    ->with(['package', 'pilgrim'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('booking_number')->label('No. Booking')->searchable(),
                Tables\Columns\TextColumn::make('pilgrim.full_name')->label('Jamaah')->searchable(),
                Tables\Columns\TextColumn::make('package.name')->label('Paket')->limit(30),
                Tables\Columns\TextColumn::make('total_price')->label('Total')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal')->date('d M Y'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
