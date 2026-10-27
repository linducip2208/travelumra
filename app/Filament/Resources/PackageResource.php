<?php

namespace App\Filament\Resources;

use App\Enums\PackageCategory;
use App\Enums\PackageType;
use App\Filament\Resources\PackageResource\Pages;
use App\Models\Package;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;
    public static function getNavigationIcon(): string|null { return 'heroicon-o-gift'; }
    public static function getNavigationGroup(): string|null { return 'Penjualan'; }
    public static function getNavigationSort(): int { return 2; }
    protected static ?string $navigationLabel = 'Paket';
    protected static ?string $label = 'Paket';
    protected static ?string $pluralLabel = 'Paket';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin', 'branch_manager', 'agent']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make('Informasi Paket')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Paket')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'PKG-' . Str::upper(Str::random(6))),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Paket')
                            ->required()
                            ->maxLength(100)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options(PackageType::class)
                            ->required(),
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options(PackageCategory::class)
                            ->required(),
                    ])->columns(2),
                Schemas\Components\Section::make('Penerbangan & Jadwal')
                    ->schema([
                        Forms\Components\TextInput::make('airline')->label('Maskapai')->maxLength(50),
                        Forms\Components\TextInput::make('departure_airport')->label('Bandara Keberangkatan')->maxLength(50),
                        Forms\Components\DatePicker::make('departure_date')->label('Tgl Keberangkatan')->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $days = (int) $get('duration_days');
                                if ($state && $days > 0) {
                                    $set('return_date', \Carbon\Carbon::parse($state)->addDays($days)->format('Y-m-d'));
                                }
                            }),
                        Forms\Components\DatePicker::make('return_date')->label('Tgl Kepulangan')->required(),
                        Forms\Components\TextInput::make('duration_days')->label('Durasi (Hari)')->numeric()->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $departure = $get('departure_date');
                                $days = (int) $state;
                                if ($departure && $days > 0) {
                                    $set('return_date', \Carbon\Carbon::parse($departure)->addDays($days)->format('Y-m-d'));
                                }
                            }),
                    ])->columns(2),
                Schemas\Components\Section::make('Hotel Makkah')
                    ->schema([
                        Forms\Components\TextInput::make('makkah_hotel_name')->label('Nama Hotel')->maxLength(100),
                        Forms\Components\TextInput::make('makkah_hotel_stars')->label('Bintang')->numeric()->minValue(1)->maxValue(7),
                        Forms\Components\TextInput::make('makkah_hotel_distance_meters')->label('Jarak (meter)')->numeric(),
                        Forms\Components\TextInput::make('makkah_hotel_room_type')->label('Tipe Kamar')->maxLength(30),
                    ])->columns(2),
                Schemas\Components\Section::make('Hotel Madinah')
                    ->schema([
                        Forms\Components\TextInput::make('madinah_hotel_name')->label('Nama Hotel')->maxLength(100),
                        Forms\Components\TextInput::make('madinah_hotel_stars')->label('Bintang')->numeric()->minValue(1)->maxValue(7),
                        Forms\Components\TextInput::make('madinah_hotel_distance_meters')->label('Jarak (meter)')->numeric(),
                    ])->columns(2),
                Schemas\Components\Section::make('Harga')
                    ->schema([
                        Forms\Components\TextInput::make('price_single')->label('Single (Rp)')->numeric()->prefix('Rp')->default(0),
                        Forms\Components\TextInput::make('price_double')->label('Double (Rp)')->numeric()->prefix('Rp')->default(0),
                        Forms\Components\TextInput::make('price_triple')->label('Triple (Rp)')->numeric()->prefix('Rp')->default(0),
                        Forms\Components\TextInput::make('price_quad')->label('Quad (Rp)')->numeric()->prefix('Rp')->default(0),
                    ])->columns(4),
                Schemas\Components\Section::make('Kuota & Konten')
                    ->schema([
                        Forms\Components\TextInput::make('quota')->label('Kuota')->numeric()->required()->default(0),
                        Forms\Components\TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
                        Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
                        Forms\Components\Toggle::make('is_featured')->label('Featured')->default(false),
                        Forms\Components\FileUpload::make('banner_image')->label('Banner')->image()->directory('packages'),
                        Forms\Components\FileUpload::make('gallery_images')->label('Galeri')->multiple()->image()->directory('packages/gallery'),
                        Forms\Components\Textarea::make('itinerary')->label('Itinerary')->columnSpanFull(),
                        Forms\Components\Textarea::make('includes')->label('Termasuk')->columnSpanFull(),
                        Forms\Components\Textarea::make('excludes')->label('Tidak Termasuk')->columnSpanFull(),
                        Forms\Components\Textarea::make('terms')->label('Syarat & Ketentuan')->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Kode')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Nama Paket')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Tipe')->badge()->sortable(),
                Tables\Columns\TextColumn::make('category')->label('Kategori')->badge()->sortable(),
                Tables\Columns\TextColumn::make('departure_date')->label('Keberangkatan')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('price_quad')->label('Harga Quad')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('quota')->label('Kuota')->sortable(),
                Tables\Columns\TextColumn::make('booked_count')->label('Terisi')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->label('Tipe')->options(PackageType::class),
                Tables\Filters\SelectFilter::make('category')->label('Kategori')->options(PackageCategory::class),
                Tables\Filters\SelectFilter::make('is_active')->label('Status')->options(['1' => 'Aktif', '0' => 'Nonaktif']),
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
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}
