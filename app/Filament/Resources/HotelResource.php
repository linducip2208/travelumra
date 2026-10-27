<?php

namespace App\Filament\Resources;

use App\Enums\HotelCity;
use App\Filament\Resources\HotelResource\Pages;
use App\Filament\Resources\HotelResource\RelationManagers;
use App\Models\Hotel;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class HotelResource extends Resource
{
    protected static ?string $model = Hotel::class;

    public static function getNavigationIcon(): string|null { return 'heroicon-o-building-office-2'; }

    public static function getNavigationGroup(): string|null { return 'Operasional'; }
    public static function getNavigationSort(): int { return 1; }

    protected static ?string $navigationLabel = 'Hotel';

    protected static ?string $label = 'Hotel';

    protected static ?string $pluralLabel = 'Hotel';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make('Informasi Hotel')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Hotel')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\Select::make('city')
                            ->label('Kota')
                            ->options(HotelCity::class)
                            ->required(),
                        Forms\Components\TextInput::make('stars')
                            ->label('Bintang')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(7),
                        Forms\Components\TextInput::make('distance_to_haram')
                            ->label('Jarak ke Masjidil Haram (m)')
                            ->numeric()
                            ->suffix('meter'),
                        Forms\Components\TextInput::make('distance_to_nabawi')
                            ->label('Jarak ke Masjid Nabawi (m)')
                            ->numeric()
                            ->suffix('meter'),
                        Forms\Components\TextInput::make('distance_to_airport')
                            ->label('Jarak ke Bandara (m)')
                            ->numeric()
                            ->suffix('meter'),
                    ])->columns(2),
                Schemas\Components\Section::make('Kontak & Lainnya')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telepon')
                            ->maxLength(30),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->maxLength(100),
                        Forms\Components\Textarea::make('amenities')
                            ->label('Fasilitas')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('images')
                            ->label('Gambar')
                            ->multiple()
                            ->image()
                            ->directory('hotels')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Hotel')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Kota')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stars')
                    ->label('Bintang')
                    ->sortable(),
                Tables\Columns\TextColumn::make('distance_to_haram')
                    ->label('Jarak ke Haram')
                    ->suffix(' m')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('city')
                    ->label('Kota')
                    ->options(HotelCity::class),
                Tables\Filters\SelectFilter::make('stars')
                    ->label('Bintang')
                    ->options([
                        1 => '1 Bintang',
                        2 => '2 Bintang',
                        3 => '3 Bintang',
                        4 => '4 Bintang',
                        5 => '5 Bintang',
                        6 => '6 Bintang',
                        7 => '7 Bintang',
                    ]),
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
        return [
            RelationManagers\HotelRoomRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHotels::route('/'),
            'create' => Pages\CreateHotel::route('/create'),
            'edit' => Pages\EditHotel::route('/{record}/edit'),
        ];
    }
}
