<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquipmentResource\Pages;
use App\Models\Equipment;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;

    public static function getNavigationIcon(): string|null { return 'heroicon-o-shopping-bag'; }

    public static function getNavigationGroup(): string|null { return 'Operasional'; }
    public static function getNavigationSort(): int { return 5; }

    protected static ?string $navigationLabel = 'Perlengkapan';

    protected static ?string $label = 'Perlengkapan';

    protected static ?string $pluralLabel = 'Perlengkapan';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin', 'branch_manager']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make('Informasi Perlengkapan')
                    ->schema([
                        Forms\Components\Select::make('package_id')
                            ->label('Paket')
                            ->relationship('package', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('type')
                            ->label('Tipe')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('stock')
                            ->label('Stok')
                            ->numeric()
                            ->required()
                            ->minValue(0),
                        Forms\Components\TextInput::make('allocated')
                            ->label('Terpakai')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('package.name')
                    ->label('Paket')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable(),
                Tables\Columns\TextColumn::make('allocated')
                    ->label('Terpakai')
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining')
                    ->label('Sisa')
                    ->state(fn (Equipment $record): int => $record->stock - $record->allocated)
                    ->sortable(query: function ($query, $direction) {
                        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';
                        return $query->orderByRaw("(stock - allocated) {$direction}");
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('package_id')
                    ->label('Paket')
                    ->relationship('package', 'name'),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options(fn () => Equipment::distinct()->pluck('type', 'type')->filter()->toArray()),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                \Filament\Actions\EditAction::make(),
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
            'index' => Pages\ListEquipment::route('/'),
            'create' => Pages\CreateEquipment::route('/create'),
            'edit' => Pages\EditEquipment::route('/{record}/edit'),
        ];
    }
}
