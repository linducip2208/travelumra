<?php

namespace App\Filament\Resources;

use App\Enums\TransactionType;
use App\Filament\Resources\LoyaltyPointResource\Pages;
use App\Models\LoyaltyPoint;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class LoyaltyPointResource extends Resource
{
    protected static ?string $model = LoyaltyPoint::class;

    public static function getNavigationIcon(): string|null { return 'heroicon-o-star'; }

    public static function getNavigationGroup(): string|null { return 'CRM'; }
    public static function getNavigationSort(): int { return 3; }

    protected static ?string $navigationLabel = 'Poin Loyalitas';

    protected static ?string $label = 'Poin Loyalitas';

    protected static ?string $pluralLabel = 'Poin Loyalitas';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make('Informasi Poin')
                    ->schema([
                        Forms\Components\Select::make('pilgrim_id')
                            ->label('Jemaah')
                            ->relationship('pilgrim', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabledOn('edit'),
                        Forms\Components\Select::make('booking_id')
                            ->label('Booking')
                            ->relationship('booking', 'booking_number')
                            ->searchable()
                            ->disabledOn('edit'),
                        Forms\Components\TextInput::make('points')
                            ->label('Poin')
                            ->numeric()
                            ->required(),
                        Forms\Components\Select::make('transaction_type')
                            ->label('Tipe Transaksi')
                            ->options(TransactionType::class)
                            ->required(),
                        Forms\Components\TextInput::make('value_rupiah')
                            ->label('Nilai (Rp)')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pilgrim.full_name')
                    ->label('Jemaah')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('points')
                    ->label('Poin')
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'earned' => 'success',
                        'redeemed' => 'warning',
                        'expired' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('value_rupiah')
                    ->label('Nilai (Rp)')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('transaction_type')
                    ->label('Tipe Transaksi')
                    ->options(TransactionType::class),
                Tables\Filters\SelectFilter::make('pilgrim_id')
                    ->label('Jemaah')
                    ->relationship('pilgrim', 'full_name'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoyaltyPoints::route('/'),
            'create' => Pages\CreateLoyaltyPoint::route('/create'),
            'view' => Pages\ViewLoyaltyPoint::route('/{record}'),
        ];
    }
}
