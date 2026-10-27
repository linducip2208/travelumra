<?php

namespace App\Filament\Resources;

use App\Enums\RoomType;
use App\Filament\Resources\HotelRoomResource\Pages;
use App\Models\HotelRoom;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class HotelRoomResource extends Resource
{
    protected static ?string $model = HotelRoom::class;

    public static function getNavigationIcon(): string|null { return 'heroicon-o-home-modern'; }

    public static function getNavigationGroup(): string|null { return 'Operasional'; }
    public static function getNavigationSort(): int { return 2; }

    protected static ?string $navigationLabel = 'Kamar Hotel';

    protected static ?string $label = 'Kamar Hotel';

    protected static ?string $pluralLabel = 'Kamar Hotel';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make('Informasi Kamar')
                    ->schema([
                        Forms\Components\Select::make('hotel_id')
                            ->label('Hotel')
                            ->relationship('hotel', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('room_number')
                            ->label('Nomor Kamar')
                            ->required()
                            ->maxLength(20),
                        Forms\Components\Select::make('type')
                            ->label('Tipe Kamar')
                            ->options(RoomType::class)
                            ->required(),
                        Forms\Components\TextInput::make('floor')
                            ->label('Lantai')
                            ->numeric(),
                        Forms\Components\TextInput::make('block')
                            ->label('Blok')
                            ->maxLength(20),
                        Forms\Components\Toggle::make('is_available')
                            ->label('Tersedia')
                            ->default(true),
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
                Tables\Columns\TextColumn::make('hotel.name')
                    ->label('Hotel')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('room_number')
                    ->label('Nomor Kamar')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('floor')
                    ->label('Lantai')
                    ->sortable(),
                Tables\Columns\TextColumn::make('block')
                    ->label('Blok')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_available')
                    ->label('Tersedia')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListHotelRooms::route('/'),
            'create' => Pages\CreateHotelRoom::route('/create'),
            'edit' => Pages\EditHotelRoom::route('/{record}/edit'),
        ];
    }
}
