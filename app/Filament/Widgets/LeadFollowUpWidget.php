<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LeadFollowUpWidget extends BaseWidget
{
    public function getHeading(): string|null { return 'Follow Up Hari Ini'; }
    protected static ?int $sort = 6;

    public static function canView(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin', 'branch_manager', 'agent']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Lead::whereDate('follow_up_date', today())
                    ->when(
                        auth()->user()->role !== 'super_admin',
                        fn ($q) => $q->where('assigned_to', auth()->id())
                    )
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'contacted' => 'warning',
                        'interested' => 'success',
                        'follow_up' => 'primary',
                        'converted' => 'success',
                        'closed_lost' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('follow_up_date')
                    ->label('Follow Up')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->badge(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->actions([
                Actions\Action::make('update_status')
                    ->label('Update')
                    ->icon('heroicon-o-pencil-square')
                    ->form([
                        \Filament\Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(LeadStatus::class)
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ])
                    ->action(function (Lead $record, array $data): void {
                        $record->update([
                            'status' => $data['status'],
                            'notes' => ($data['notes'] ?? '') ?: $record->notes,
                        ]);

                        \App\Models\LeadActivity::create([
                            'lead_id' => $record->id,
                            'staff_id' => auth()->id(),
                            'type' => 'status_change',
                            'content' => 'Status diubah menjadi ' . $data['status'],
                        ]);
                    }),
            ]);
    }
}
